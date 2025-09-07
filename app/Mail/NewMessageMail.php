<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $senderName;
    public $messageContent;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($senderName, $messageContent)
    {
        
        $this->senderName = $senderName;
        $this->messageContent = $messageContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('You have a new message from ' . $this->senderName)
                    ->view('emails.new_message'); // <-- We will create this view next
    }
}