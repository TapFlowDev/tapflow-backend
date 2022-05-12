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
        return $this->markdown('mail.testPDF')->with([
            'status' => $this->data['status'],
            'projectName' => $this->data['projectName'],
            'clientEmail' => $this->data['clientEmail'],
        ])->attach($this->data['pdf_file']);
    }
}
