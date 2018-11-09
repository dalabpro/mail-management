<?php

Route::group(['prefix' => '', 'namespace' => 'Backend', 'as' => 'backend.'], function ($routes){

    $routes->get('mailboxes', 'MailManagementController@index')
        ->name('mailboxes.index');


    $routes->delete('mailboxes', 'MailManagementController@destroy')->name('mailboxes.destroy');
    $routes->resource('mailboxes', 'MailManagementController', [
        'except' => ['destroy'],
        'names' => [
            'index'   => 'mailboxes.index',
            'create'  => 'mailboxes.create',
            'store'  => 'mailboxes.store',
            'show'  => 'mailboxes.show',
            'edit'  => 'mailboxes.edit',
            'update'  => 'mailboxes.update',
        ],
    ]);

    $routes->get('parser/mail/{client_id}', 'MailManagementController@parse')
        ->name('mail.parse');
});