<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;

     /**
     * @var
     */
    private $user;
    private $token;

    /**
     * The subject of the message.
     *
     * @var string
     */
    public $subject = '[Advoguez] Recuperação de senha';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->subject = '[Advoguez] - Recuperação de senha';
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mail.reset-password', ['user' => $this->user]);
    }
}
