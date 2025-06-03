<?php

namespace App\Mail\IMS;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvestigationEmail extends Mailable
{
    use Queueable, SerializesModels;

    protected $details;
    protected $investigationarray;
    protected $rcpaarray;

    /**
     * Create a new message instance.
     */
    public function __construct($details, $investigationarray, $rcpaarray)
    {
        $this->details = $details;
        $this->investigationarray = $investigationarray;
        $this->rcpaarray = collect($rcpaarray); // Ensure it's a Collection
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->view('emails.incident.investigation')
            ->subject(config('app.name') . ' - ' . $this->details['mail_subject'])
            ->with([
                'details' => $this->details,
                'investigationarray' => $this->investigationarray,
                'rcpaarray' => $this->rcpaarray
            ]);
    }
}
