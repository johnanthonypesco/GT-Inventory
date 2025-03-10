<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CustomPasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetUrl;

    /**
     * Create a new message instance.
     *
     * @param  string  $token
     * @param  string  $userType
     * @param  string  $email
     */
    public function __construct($token, $userType, $email)
    {
        // ✅ Generate the reset URL with userType as a query parameter
        $this->resetUrl = url(route($userType . '.password.reset', [
            'token' => $token,
            'email' => $email,
        ], false));
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Reset Your Password')
                    ->view('emails.password-reset')
                    ->with([
                        'resetUrl' => $this->resetUrl,
                    ]);
    }
}
