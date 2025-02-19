<?php

namespace App\Mail\Ohc;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MedicineStockEmail extends Mailable
{

    use Queueable,
        SerializesModels;

    protected $details;
    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details,$data)
    {
        $this->details = $details;
        $this->data = $data;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.ohc.medicine-stock-request')
            ->subject(config('app.name') . " - " . $this->details['mail_subject'])
            ->with([
                'details' => $this->details,
                'data' => $this->data
            ]);
    }

}
