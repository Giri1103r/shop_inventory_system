<?php

namespace App\Mail\Ohc;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MedicineRequisitionEmail extends Mailable
{

    use Queueable,
        SerializesModels;


            private $emailDetails;
            private $medicineDetails;

            public function __construct($emailDetails, $medicineDetails)
            {
                $this->emailDetails = $emailDetails;
                $this->medicineDetails = $medicineDetails;

            }

            public function build()
            {
                return $this->view('emails.ohc.medicine-requisition')
                    ->subject(config('app.name') . " - " . $this->emailDetails['mail_subject'])
                    ->with([
                        "emailDetails" => $this->emailDetails,
                        "medicineDetails" => $this->medicineDetails
                    ]);
            }

}
