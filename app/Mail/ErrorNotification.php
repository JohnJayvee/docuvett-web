<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ErrorNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $params;
    public $recipients;

    public function __construct(array $recipients, array $params)
    {
        $this->recipients = $recipients;
        $this->params = $params;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
         return $this->to($this->recipients)
            ->subject('Error notification')
            ->view('email.error_notification', [
                'title' => $this->params['title'],
                'error' => $this->params['error'] ?? [],
            ]);
    }
}