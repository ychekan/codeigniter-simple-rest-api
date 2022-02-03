<?php

namespace App\Notifications;

use Config\Services;

class ForgotPasswordNotification
{
    /**
     * @var
     */
    private $email;

    /**
     * @var
     */
    private $hash;

    /**
     * @var
     */
    private $name;

    /**
     * @param string $email
     * @param string $name
     * @param string $hash
     */
    public function __construct(string $email = '', string $name = '', string $hash = '')
    {
        $this->email = $email;
        $this->name = $name;
        $this->hash = $hash;
    }

    /**
     * @return bool
     */
    public function send(): bool
    {
        $email = Services::email();

        $email->setFrom(env('INFO_EMAIL'), env('APP_NAME'));
        $email->setTo($this->email);

        $email->setSubject('Reset password');
        $email->setMessage(view('emails/forgot-password', [
            'name' => $this->name,
            'hash' => $this->hash,
        ]));

        return $email->send();
    }
}