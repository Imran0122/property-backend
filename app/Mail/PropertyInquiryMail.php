<?php
namespace App\Mail;
class PropertyInquiryMail extends Mailable
{
    public function __construct(public $data) {}

    public function build()
    {
        return $this->subject('New Property Inquiry')
            ->view('emails.property-inquiry');
    }
}
