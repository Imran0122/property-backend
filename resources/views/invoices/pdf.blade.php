<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 14px;
            margin: 40px;
            color: #333;
        }
        .header {
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 25px;
        }
        .header h2 {
            margin: 0;
            color: #222;
        }
        .header small {
            color: #777;
        }
        .details, .payment, .summary {
            margin-bottom: 25px;
        }
        .details table, .payment table {
            width: 100%;
            border-collapse: collapse;
        }
        .details td, .payment td {
            padding: 6px 0;
        }
        .summary {
            border-top: 2px solid #333;
            padding-top: 10px;
        }
        .summary td {
            padding: 5px 0;
        }
        .footer {
            border-top: 1px solid #ccc;
            margin-top: 40px;
            padding-top: 10px;
            font-size: 12px;
            color: #888;
            text-align: center;
        }
        .status {
            font-weight: bold;
            color: {{ $invoice->status === 'paid' ? '#008000' : ($invoice->status === 'refunded' ? '#d9534f' : '#f0ad4e') }};
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>Property Website - Invoice</h2>
        <small>Invoice #: {{ $invoice->id }}</small><br>
        <small>Date: {{ $invoice->created_at->format('d M, Y') }}</small>
    </div>

    <div class="details">
        <table>
            <tr>
                <td><strong>Billed To:</strong></td>
                <td>{{ $invoice->user->name }}<br>{{ $invoice->user->email }}</td>
            </tr>
            <tr>
                <td><strong>Property:</strong></td>
                <td>{{ $invoice->property->title ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td><strong>Location:</strong></td>
                <td>{{ $invoice->property->location ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    <div class="payment">
        <h3>Payment Details</h3>
        <table>
            <tr>
                <td><strong>Amount:</strong></td>
                <td>PKR {{ number_format($invoice->amount) }}</td>
            </tr>
            <tr>
                <td><strong>Status:</strong></td>
                <td class="status">{{ ucfirst($invoice->status) }}</td>
            </tr>
            <tr>
                <td><strong>Payment Method:</strong></td>
                <td>{{ ucfirst($invoice->method) }}</td>
            </tr>
            <tr>
                <td><strong>Transaction ID:</strong></td>
                <td>{{ $invoice->payment_id }}</td>
            </tr>
            @if($invoice->refunded_at)
            <tr>
                <td><strong>Refunded At:</strong></td>
                <td>{{ $invoice->refunded_at->format('d M, Y h:i A') }}</td>
            </tr>
            <tr>
                <td><strong>Refund ID:</strong></td>
                <td>{{ $invoice->refund_id }}</td>
            </tr>
            @endif
        </table>
    </div>

    <div class="summary">
        <table width="100%">
            <tr>
                <td><strong>Subtotal:</strong></td>
                <td align="right">PKR {{ number_format($invoice->amount) }}</td>
            </tr>
            <tr>
                <td><strong>Tax (0%):</strong></td>
                <td align="right">PKR 0</td>
            </tr>
            <tr>
                <td><strong>Total:</strong></td>
                <td align="right"><strong>PKR {{ number_format($invoice->amount) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Thank you for your business with <strong>Property Website</strong>.  
        <br>This is a system-generated invoice — no signature required.
    </div>

</body>
</html>
