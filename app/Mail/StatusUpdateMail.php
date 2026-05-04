<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $type;
    public string $status;
    public string $userName;
    public string $itemTitle;
    public string $adminNote;

    public function __construct(string $type, string $status, string $userName, string $itemTitle, string $adminNote = '')
    {
        $this->type      = $type;
        $this->status    = $status;
        $this->userName  = $userName;
        $this->itemTitle = $itemTitle;
        $this->adminNote = $adminNote;
    }

    public function build(): self
    {
        $statusLabel = $this->status === 'approved' ? 'Approved' : 'Rejected';
        $typeLabel   = $this->type === 'property' ? 'Property' : 'Agency';

        return $this->subject("{$typeLabel} {$statusLabel} — Hectare Property")
                    ->view('emails.status-update');
    }
}