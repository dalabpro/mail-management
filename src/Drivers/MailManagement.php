<?php

namespace Kgregorywd\MailManagement\Drivers;

use App\Models\Client;
use Carbon\Carbon;
use Kgregorywd\MailManagement\Models\EmailMessage;
use Kgregorywd\MailManagement\Models\MailBox;
use View;

class MailManagement
{

    public function getBackendButton($model, $prefix, $type)
    {
        $link = url("parser/mail/{$model->id}");

        return (string) View::make('MailManagement::backend.ajax.button', compact(
            'link',
            'prefix',
            'type'
        ))->render();
    }

    public function parse($clientId)
    {
        try{

            $client = Client::find($clientId);
            $clientEmails = $client->emails->pluck('email', 'id')->toArray();

            // Получаем все почтовые ящики
            $adminEmails = MailBox::all();

            foreach ($adminEmails as $adminEmail) {

                $host = $adminEmail['imap_host'];
                $port = $adminEmail['imap_port'];
                $login = $adminEmail['imap_username'];
                $pass = $adminEmail['imap_password'];
                $param = "/imap/{$adminEmail['imap_encryption']}/novalidate-cert";
                $server = "{" . "{$host}:{$port}{$param}" . "}";

                $mbox = imap_open($server, $login, $pass);

                $foldersOld = [
                    'INBOX',
                    imap_utf8_to_mutf7('Отправленные'),
                ];
                $foldersNew = [];

                $mailboxes = imap_list($mbox, $server, '*');
                foreach ($mailboxes as $item) {
                    $foldersNew[] = str_replace($server, '', $item);
                }

                $folders = array_intersect($foldersOld, $foldersNew);

                foreach ($folders as $folder) {
                    $mbox = imap_open($server.$folder, $login, $pass);

                    if ($mbox) {
                        $criteria = "SINCE " . (new Carbon())->subDays(60)->format('d-M-Y');
                        $uids   = imap_search($mbox, $criteria, SE_UID);

                        $fetchResults = imap_fetch_overview($mbox, implode(',', $uids), FT_UID);

                        foreach ($fetchResults as $fetchResult) {
                            /*Парсим EMail из заголовка*/
                            $pattern = "/[-a-z0-9!#$%&'*_`{|}~]+[-a-z0-9!#$%&'*_`{|}~\.=?]*@[a-zA-Z0-9_-]+[a-zA-Z0-9\._-]+/i";
                            preg_match_all($pattern, $fetchResult->from, $resultsFromData);
                            preg_match_all($pattern, $fetchResult->to, $resultsToData);

                            $emailFromFrom = array_unique(array_map(function ($i) {
                                return $i[0];
                            }, $resultsFromData));

                            $emailToFrom = array_unique(array_map(function ($i) {
                                return $i[0];
                            }, $resultsToData));

                            $emailFromResult = array_search($emailFromFrom[0], $clientEmails);
                            $emailToResult = array_search($emailToFrom[0], $clientEmails);

                            if ($emailFromResult || $emailToResult) {

                                $mailBox = EmailMessage::firstOrCreate([
                                    'uid' => $fetchResult->uid,
                                    'message_id' => $fetchResult->message_id,
                                ]);

                                /*
                                 * Если запись создаётся, то тогда идём дальше. Если нет, то останавливаем цикл
                                 */
                                if ($mailBox->wasRecentlyCreated) {

                                    /*
                                     * Решение с кодировкой заголовков от mail.ru
                                     */
                                    if (isset($fetchResult->subject) && (!is_null($fetchResult->subject))) {
                                        mb_internal_encoding('UTF-8');
                                        $subject = mb_decode_mimeheader($fetchResult->subject);
                                    } else {
                                        $subject = 'default';
                                    }

                                    $mailBox->subject = $subject;
                                    $mailBox->text_body = $this->getTextBody($mbox, $fetchResult->uid);
                                    $mailBox->html_body = $this->getHtmlBody($mbox, $fetchResult->uid);
                                    $mailBox->header = $this->getHeaderRaw($mbox, $fetchResult->uid);
                                    $mailBox->client_id = $client->id;
                                    $mailBox->email_id = $adminEmail->id;
                                    $mailBox->folder = $folder;
                                    $mailBox->is_ready = 1;
                                    $mailBox->received_at =  Carbon::parse($fetchResult->date)->format('Y-m-d H:i:s');

                                    $mailBox->save();
                                }

                            }
                        }
                    }
                }
            }

            return "Выполнено";
        } catch (\Exception $e) {

            return $e->getMessage();
        }
    }

    //получение содержимого письма в виде простого текста
    public function getTextBody($mbox, $uid)
    {
        return $this->getPart($mbox, $uid, "TEXT/PLAIN");
    }

    //получение содержимого письма в виде формате html
    public function getHtmlBody($mbox, $uid)
    {
        return $this->getPart($mbox, $uid, "TEXT/HTML");
    }

    //получение части письма
    public function getPart($imap, $uid, $mimetype)
    {
        //получение структуры письма
        $structure = imap_fetchstructure($imap, $uid, FT_UID);
        if ($structure) {
            foreach ($structure->parts as $part) {
                if ($mimetype === $this->getMimeType($part)) {
                    $text = imap_qprint(imap_fetchbody($imap, $uid, 1.1, FT_UID));

                    if (empty($text)) {
                        $text = imap_qprint(imap_fetchbody($imap, $uid, 1, FT_UID));
                    }

                    $text = $this->decodeText($text, $part->encoding);
                    $charset = $part->parameters[0]->value;//кодировка символов

                    if($mimetype == 'TEXT/PLAIN'){
                        $text = iconv($charset, "UTF-8", $text);
                    }

                    if($mimetype == 'TEXT/HTML'){
                        $text = iconv($charset, "UTF-8", $text);
                    }

                    return $text;
                }
            }
        }
        return false;
    }

    //MIME-тип передается числом, а подтип - текстом.
    //Функция приводит все в текстовый вид.
    //Например: если type = 0 и subtype = "PLAIN",
    //то функция вернет "TEXT/PLAIN".
    //TEXT - 0, MULTIPART - 1, .. , APPLICATION - 3 и т.д.
    public function getMimeType($structure)
    {
        $primaryMimetype = [
            "TEXT",
            "MULTIPART",
            "MESSAGE",
            "APPLICATION",
            "AUDIO",
            "IMAGE",
            "VIDEO",
            "OTHER"
        ];

        if ($structure->subtype) {

            return "{$primaryMimetype[(int)$structure->type]}/{$structure->subtype}";
        }

        return "TEXT/PLAIN";
    }

    //получение заголовка письма в виде объекта
    public function getHeader($mbox, $uid)
    {
        return imap_rfc822_parse_headers($this->getHeaderRaw($mbox, $uid));
    }

    //получение заголовка письма
    public function getHeaderRaw($mbox, $uid)
    {

        return imap_fetchbody($mbox, $uid, '0', FT_UID);
    }

    public function decodeText($message, $encoding)
    {
        //0 - 7BIT; 1 - 8BIT; 2 - BINARY; 3 - BASE64; 4 - QUOTED-PRINTABLE; 5 - OTHER
        switch ($encoding) {

            // 7BIT
            case 0: break;
            // 8BIT
            case 1: $message = imap_8bit($message); break;

            // BINARY
            case 2: $message = imap_binary($message); break;

            // imap_base64 - декодирует BASE64-кодированный текст
            case 3: $message = imap_base64($message); break;

            // imap_qprint - конвертирует закавыченную строку в 8-битную строку
            case 4: $message = imap_qprint($message); break;

            // OTHER
            case 5: break;

            // UNKNOWN
            default: break;
        }

        return $message;
    }

}
