<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DocumentExpiredReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $expiredDocument;

    public function __construct($expiredDocument)
    {
        $this->expiredDocument = $expiredDocument;
    }

    public function build()
    {
        return $this->subject('Notifikasi Tanggal Terlewati')
            ->view('emails.DocumentReminder')
            ->with([
                'expiredDocument' => $this->expiredDocument
            ]);
    }
}
