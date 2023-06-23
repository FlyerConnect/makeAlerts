<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class Email extends Mailable
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
        $address = 'benjamin@reachwellapp.com';
        $subject = 'This is a demo!';
        $name = 'Jane Doe';
        return $this->view('emails.makeAlert')
                    ->from($address, $name)
                    ->subject($subject)
                    ->with([ 'message' => $this->data['message'] ]);
    }
}
