<?php

namespace Kgregorywd\MailManagement\Http\Controllers\Backend;

use App\Http\Controllers\BackendController;
use App\Models\Client;
use App\Models\SettingEmails;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Kgregorywd\Currencies\Models\Currency;
use Kgregorywd\MailManagement\Models\EmailMessage;
use Kgregorywd\MailManagement\Models\MailBox;
use Kgregorywd\MailManagement\Models\MailsInbox;

class MailManagementController extends BackendController
{

    public function __construct(Request $request, Currency $model)
    {
        parent::__construct($request, $model);

        $this->middleware(function ($request, $next) {

            $this->setCollect([
                'titleIndex' => trans("currency::{$this->prefix}.{$this->getCollect('type')}.title_index"),
                'viewPath' => $this->getCollect('viewPath'),
            ])->setCollect([
                'breadcrumbs' => array_merge($this->getCollect('breadcrumbs'), [
                    [
                        'name' => $this->getCollect('titleIndex'),
                        'url' => route("backend.{$this->getCollect('type')}.index")
                    ],
                ]),
            ]);

            return $next($request);
        });
    }

    public function index(Currency $model)
    {
        $this
            ->setCollect('models', $model->paginate(25))
            ->setCollect('breadcrumbs', (String)view()->make('backend.ajax.breadcrumb', $this->getCollect())->render());

        return view('currency::backend.currencies.index', $this->getCollect());
    }
    public function parse($clientId)
    {
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
            $folder = 'INBOX';
            $lastUid = $adminEmail['last_message_uid'];//uid последнего считанного сообщения

            $mbox = imap_open("{" . "{$host}:{$port}{$param}" . "}$folder", $login, $pass);

            if ($mbox) {
                $mcount = imap_check($mbox);

                $countMessages = $mcount->Nmsgs;
                $uidFrom = $lastUid + 1;

                $fetchResults = imap_fetch_overview($mbox, "{$uidFrom}:{$countMessages}", FT_UID);

//                dd(count($fetchResults), $fetchResults);
                foreach ($fetchResults as $fetchResult) {
//                    dump(count($fetchResults), $fetchResult);
                    /*Парсим EMail из заголовка*/
                    $pattern = "/[-a-z0-9!#$%&'*_`{|}~]+[-a-z0-9!#$%&'*_`{|}~\.=?]*@[a-zA-Z0-9_-]+[a-zA-Z0-9\._-]+/i";
                    preg_match_all($pattern, $fetchResult->from, $resultsFromData);

                    $emailFrom = array_unique(array_map(function ($i) {
                        return $i[0];
                    }, $resultsFromData));

                    $emailResult = array_search($emailFrom[0], $clientEmails);

                    if ($emailResult) {


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
                            $this->getHtmlBody($mbox, $fetchResult->uid);
//                            dd($mbox, $fetchResult->uid, $this->getHtmlBody($mbox, $fetchResult->uid));

                            $mailBox->subject = $subject;
                            $mailBox->text_body = $this->getTextBody($mbox, $fetchResult->uid);
                            $mailBox->html_body = $this->getHtmlBody($mbox, $fetchResult->uid);
                            $mailBox->header = $this->getHeaderRaw($mbox, $fetchResult->uid);
                            $mailBox->client_id = $client->id;
                            $mailBox->email_id = $emailResult;
                            $mailBox->is_ready = 1;
                            $mailBox->received_at =  Carbon::parse($fetchResult->date)->format('Y-m-d H:i:s');

                            $mailBox->save();
                        }

                    }
                }
            }
        }

        dd('Работа окончена!');
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
            dd($structure, $mimetype, $this->getMimeType($structure));
            if ($mimetype == $this->getMimeType($structure)) {
                $partNumber = 1;
                //imap_fetchbody - извлекает определённый раздел тела сообщения
                $text = imap_fetchbody($imap, $uid, $partNumber, FT_UID);


                $bodymsg = imap_qprint(imap_fetchbody($imap, $uid, 1.2));

                if (empty($bodymsg)) {
                    $bodymsg = imap_qprint(imap_fetchbody($imap, $uid, 1));
                }

                dd($text);
                $charset = $structure->parameters[0]->value;//кодировка символов

                //0 - 7BIT; 1 - 8BIT; 2 - BINARY; 3 - BASE64; 4 - QUOTED-PRINTABLE; 5 - OTHER
                switch ($structure->encoding) {
                    case 3:
                        //imap_base64 - декодирует BASE64-кодированный текст
                        $text = imap_base64($text);
                        break;
                    case 4:
                        //imap_qprint - конвертирует закавыченную строку в 8-битную строку
                        $text = imap_qprint($text);
                        break;
                }

                if($mimetype == 'TEXT/PLAIN'){
                    $text = iconv($charset,"UTF-8", $text);
                }

                if($mimetype == 'TEXT/HTML'){
                    $text = iconv($charset,"UTF-8", $text);
                }

                return $text;
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
}
