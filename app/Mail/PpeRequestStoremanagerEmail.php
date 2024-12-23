<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PpeRequestStoremanagerEmail extends Mailable
{
    use Queueable, SerializesModels;
    protected $Storedetails;
    /**
     * Create a new message instance.
     */
    public function __construct($Storedetails)
    {
        $this->Storedetails = $Storedetails;
    }


    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject:('PPE Shoe Request Approved'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.pperequest.storemanageremail',
            with: ['Storedetails' => $this->Storedetails]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
