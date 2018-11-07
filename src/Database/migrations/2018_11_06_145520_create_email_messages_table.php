<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEmailMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('uid')->unique(); // уникальный номер письма в почтовом ящике
            $table->string('message_id')->unique(); // уникальный номер сообщения
            $table->string('subject')->nullable(); // тема письма
            $table->longText('text_body')->nullable(); // письмо храниться на сервере в виде обычного текста
            $table->longText('html_body')->nullable(); // письмо храниться на сервере в виде html-версии
            $table->text('header')->nullable(); // технический заголовок письма
            $table->text('folder')->nullable(); // папка письма
            $table->unsignedInteger('email_id')->default(0); // ID Email
            $table->unsignedInteger('client_id')->default(0); // ID Клиента
            $table->tinyInteger('is_ready')->default(0); // письмо полностью загружено
            $table->timestampTz('received_at')->nullable(); // дата письма
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_messages');
    }
}
