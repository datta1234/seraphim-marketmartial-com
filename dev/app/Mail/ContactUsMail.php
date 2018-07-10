<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContactUsMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($contact_details)
    {
        $this->contact_details = $contact_details;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = $this->contact_details['name'];
        $email = $this->contact_details['contact_email'];
        $message = $this->contact_details['contact_email'];

        return $this->view('emails.contact')->with(['name' => $name, 'email' => $email, 'content' => $message]);
    }
}
