<?php

namespace App\Mail\Ohc;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PatientEmail extends Mailable
{
    use Queueable,
    SerializesModels;


        private $emailDetails;
        private $medicineDetails;
        private $hospitaldetails;

        public function __construct($emailDetails, $medicineDetails,$hospitaldetails)
        {
            $this->emailDetails = $emailDetails;
            $this->medicineDetails = $medicineDetails;
            $this->hospitaldetails = $hospitaldetails;

        }

        public function build()
        {
            return $this->view('emails.ohc.patient-list')
                ->subject(config('app.name') . " - " . $this->emailDetails['mail_subject'])
                ->with([
                    "emailDetails" => $this->emailDetails,
                    "medicineDetails" => $this->medicineDetails,
                    "hospitaldetails" => $this->hospitaldetails

                ]);
        }
}
