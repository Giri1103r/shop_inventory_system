<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RestEmployeePasswordEmail extends Mailable
{

    use Queueable,
        SerializesModels;

    protected $newpass;
    protected $details;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($details,$newpass)
    {

        $this->details = $details;
        $this->newpass = $newpass;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {

        return $this->view('emails.employee.resetpassbyadmin')
            ->subject(config('app.name') . " - Password Reset")
            ->with([
                'details' => $this->details,
                'newpass' => $this->newpass 
            ]);
    }
}
