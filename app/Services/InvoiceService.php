<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Invoice;

class InvoiceService
{
    public static function generate(Invoice $invoice)
    {
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));
        $fileName = 'invoice_' . $invoice->id . '.pdf';
        $filePath = storage_path('app/public/invoices/' . $fileName);

        $pdf->save($filePath);
        return $filePath;
    }
}
