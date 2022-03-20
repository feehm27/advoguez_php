<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CanceledMettingMail extends Mailable
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
    public $subject = '[Advoguez] Cancelamento de Reunião';

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->subject = '[Advoguez] - Cancelamento de Reunião';
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view('mail.canceled-meeting', ['data' => $this->data]);
    }
}
