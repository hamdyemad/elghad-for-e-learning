<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $code;
    public $name;

    /**
     * Create a new message instance.
     */
    public function __construct(string $code, string $name = null)
    {
        $this->code = $code;
        $this->name = $name;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Verify Your Email Address')
                    ->view('emails.verify-email')
                    ->with([
                        'code' => $this->code,
                        'name' => $this->name
                    ]);
    }
}
