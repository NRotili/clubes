<?php

namespace App\Mail;

use App\Models\ClubConfig;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ComunicacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $clubNombre;

    public function __construct(
        public string $asunto,
        public string $cuerpo,
        public string $nombreDestinatario,
    ) {
        $this->clubNombre = ClubConfig::nombre();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: $this->asunto);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.comunicacion');
    }
}
