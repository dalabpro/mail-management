<?php

Route::group(['prefix' => '', 'namespace' => 'Backend', 'as' => 'backend.'], function ($routes){

    $routes->get('parser/mail/{client_id}', 'MailManagementController@parse')
        ->name('mail.parse');

});