<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class draftMail extends Mailable
{
    use Queueable, SerializesModels;
    public $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // ini_set('max_execution_time', 180);
        return $this->subject($this->data['subject'])->markdown('mail.testPDF')->with([
            'agency_name' => $this->data['AgencyName'],
            'client_name' => $this->data['ClientName'],
            'email_body' => $this->data['email_body'],
        ])->attach($this->data['pdf_file']);
    }
}
