<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    private function subjectLabel(): string
    {
        return match ($this->data['subject'] ?? 'general') {
            'services' => 'Trámites y Servicios',
            'complaint' => 'Denuncia Ciudadana',
            'info' => 'Información',
            'other' => 'Otro',
            default => 'Consulta General',
        };
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Nuevo mensaje de contacto - ' . $this->subjectLabel(),
            replyTo: [new Address($this->data['email'], $this->data['name'])],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
            with: [
                'data' => $this->data,
            ],
        );
    }
}
