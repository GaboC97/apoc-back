<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactoMailable extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Datos del formulario de contacto.
     */
    public $nombre;
    public $email;
    public $mensaje;

    /**
     * Crea una nueva instancia del mensaje.
     */
    public function __construct($nombre, $email, $mensaje)
    {
        $this->nombre = $nombre;
        $this->email = $email;
        $this->mensaje = $mensaje;
    }

    /**
     * Obtiene el sobre del mensaje.
     */
    public function envelope(): Envelope
    {
        // El asunto del correo que recibirás.
        return new Envelope(
            subject: 'Nuevo Mensaje de Contacto: ' . $this->nombre,
        );
    }

    /**
     * Obtiene la definición del contenido del mensaje.
     */
    public function content(): Content
    {
        // Usa una plantilla de Blade simple para el cuerpo del correo.
        return new Content(
            view: 'emails.contacto',
            with: [
                'nombre' => $this->nombre,
                'email' => $this->email,
                'mensaje' => $this->mensaje,
            ],
        );
    }

    /**
     * Obtiene los attachments para el mensaje.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
