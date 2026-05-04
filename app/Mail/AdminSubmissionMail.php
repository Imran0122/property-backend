<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminSubmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $type;
    public string $submitterName;
    public string $submitterEmail;
    public string $itemTitle;
    public array  $details;

    public function __construct(string $type, string $submitterName, string $submitterEmail, string $itemTitle, array $details = [])
    {
        $this->type          = $type;
        $this->submitterName  = $submitterName;
        $this->submitterEmail = $submitterEmail;
        $this->itemTitle      = $itemTitle;
        $this->details        = $details;
    }

    public function build(): self
    {
        $subject = $this->type === 'property'
            ? 'New Property Submitted — Waiting for Approval'
            : 'New Agency Submitted — Waiting for Approval';

        return $this->subject($subject)
                    ->view('emails.admin-submission');
    }
}