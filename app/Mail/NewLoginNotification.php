<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue; // <--- Tama ito

// CHANGE: Idinagdag natin ang "implements ShouldQueue" dito
class NewLoginNotification extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $ipAddress;

    public function __construct($ipAddress)
    {
        $this->ipAddress = $ipAddress;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[Security Alert] New Login to Your Account',
        );
    }

    public function content(): Content
    {
        return new Content(
            // Siguraduhin na meron kang file sa resources/views/emails/new-login.blade.php
            view: 'emails.new-login', 
        );
    }
}