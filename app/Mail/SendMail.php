<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    public $view;
    public $data;
    public $custom;
    public $id;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($view, $data, $custom = null, $id = null)
    {
        $this->view   = $view;
        $this->data   = $data;
        $this->custom = $custom;
        $this->id = $id;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {


        if (env('MAIL_BCC')) {
            return $this->markdown($this->view)->subject($this->custom->subject)
            ->replyTo(env('MAIL_REPLY'), env('NAME_REPLY'))
            ->with('data', $this->data)->bcc(env('MAIL_BCC'))
            ->withSymfonyMessage(function ($message) {
                    $message->getHeaders()->addTextHeader('X-Custom-ID', $this->id);
                    $message->getHeaders()->addTextHeader('X-Endpoint', config('app.endpoint'));
                });

            
        } else {
            return $this->markdown($this->view)->subject($this->custom->subject)
            ->replyTo(env('MAIL_REPLY'), env('NAME_REPLY'))
            ->with('data', $this->data)
            ->withSymfonyMessage(function ($message) {
                    $message->getHeaders()->addTextHeader('X-Custom-ID', $this->id);
                    $message->getHeaders()->addTextHeader('X-Endpoint', config('app.endpoint'));
                });
        }

    }
}
