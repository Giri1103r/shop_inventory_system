<?php

namespace App\Mail\GembaWalk;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GembaWalkMail extends Mailable
{
    use Queueable,
        SerializesModels;

    protected $details;
    /**
     * Create a new message instance.
     *
     * @return void
     */

    public function __construct($details)
    {
        $this->details = $details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view('emails.gembaWalk.gembaWalk')
            ->subject(config('app.name') . " - " . $this->details['mail_subject'])
            ->with("details", $this->details);
    }
}
