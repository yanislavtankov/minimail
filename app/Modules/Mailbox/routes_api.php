<?php

use Illuminate\Support\Facades\Route;

Route::group(['middleware' => 'jwt'], function () {
    Route::apiResource('mailboxes', 'MailboxController');
});
