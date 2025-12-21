<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $filePath;

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice, $filePath = null)
    {
        // Load required relations
        $this->invoice = $invoice->load(['user', 'property']);
        $this->filePath = $filePath ?? storage_path('app/'.$invoice->pdf_path);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $subject = 'Invoice #'.$this->invoice->id;

        $email = $this->subject($subject)
                      ->view('emails.invoice')
                      ->with([
                          'invoice' => $this->invoice
                      ]);

        // Agar PDF path exist kare to attach karo
        if (file_exists($this->filePath)) {
            $email->attach($this->filePath, [
                'as' => 'Invoice_'.$this->invoice->id.'.pdf',
                'mime' => 'application/pdf',
            ]);
        }

        return $email;
    }
}
