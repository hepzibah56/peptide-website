<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ContactMail extends Mailable
{
    public function __construct(public array $data) {}

    public function build()
    {
        return $this->from($this->data['email'], $this->data['name'])
            ->subject('Kontaktskjema: '.config('shop.name'))
            ->view('emails.contact', ['data' => $this->data]);
    }
}