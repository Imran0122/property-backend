<!doctype html>
<html>
  <body>
    <p>Hi {{ $invoice->transaction->user->name ?? '' }},</p>
    <p>Please find attached invoice <strong>{{ $invoice->invoice_number }}</strong> for your transaction.</p>
    <p>Amount: {{ $invoice->currency }} {{ number_format($invoice->amount,2) }}</p>
    <p>Status: {{ ucfirst($invoice->status) }}</p>
    <p>Thank you,<br>{{ config('app.name') }}</p>


    <p>Dear {{ $invoice->user->name }},</p>
<p>Thank you for your payment. Please find attached your invoice.</p>
<p>Regards,<br>Property Website Team</p>

  </body>
</html>
