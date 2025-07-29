<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingInvite extends Mailable
{
    use Queueable, SerializesModels;

    public $url;

    public $expiration;

    public function __construct($url, $expiration)
    {
        $this->url = $url;
        $this->expiration = $expiration;
    }

    public function build()
    {
        return $this->subject('Приглашение на бронирование таймслота')
            ->view('emails.booking_invite')
            ->with([
                'url' => $this->url,
                'expiration' => $this->expiration
            ]);
    }
}