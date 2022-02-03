<?php

namespace Config;

use App\Notifications\ForgotPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use App\Providers\AuthServiceProvider;
use CodeIgniter\Events\Events;
use CodeIgniter\Exceptions\FrameworkException;
use Fluent\Auth\Contracts\ResetPasswordInterface;
use Fluent\Auth\Contracts\VerifyEmailInterface;

/*
 * --------------------------------------------------------------------
 * Application Events
 * --------------------------------------------------------------------
 * Events allow you to tap into the execution of the program without
 * modifying or extending core files. This file provides a central
 * location to define your events, though they can always be added
 * at run-time, also, if needed.
 *
 * You create code that can execute by subscribing to events with
 * the 'on()' method. This accepts any form of callable, including
 * Closures, that will be executed when the event is triggered.
 *
 * Example:
 *      Events::on('create', [$myInstance, 'myMethod']);
 */

Events::on('pre_system', static function () {
    if (ENVIRONMENT !== 'testing') {
        if (ini_get('zlib.output_compression')) {
            throw FrameworkException::forEnabledZlibOutputCompression();
        }

        while (ob_get_level() > 0) {
            ob_end_flush();
        }

        ob_start(static function ($buffer) {
            return $buffer;
        });
    }

    /*
     * --------------------------------------------------------------------
     * Debug Toolbar Listeners.
     * --------------------------------------------------------------------
     * If you delete, they will no longer be collected.
     */
    if (CI_DEBUG && ! is_cli()) {
        Events::on('DBQuery', 'CodeIgniter\Debug\Toolbar\Collectors\Database::collect');
        Services::toolbar()->respond();
    }
});

/**
 * For use auth guard
 */
Events::on('pre_system', [AuthServiceProvider::class, 'register']);

/**
 * --------------------------------------------------------------------
 * CodeIgniter4 Authentication Listeners.
 * --------------------------------------------------------------------
 * This event will be dispatch to send reset password and verify email,
 * you are free to implement this dispatcher, example using
 * twilio service to send sms.
 */
Events::on(ResetPasswordInterface::class, function ($email, $name, $hash) {
    (new ForgotPasswordNotification($email, $name, $hash))->send();
});

Events::on(VerifyEmailInterface::class, function ($email, $name, $hash) {
    (new VerifyEmailNotification($email, $name, $hash))->send();
});
