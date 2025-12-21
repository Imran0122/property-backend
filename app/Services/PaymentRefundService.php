<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\Invoice;
use Stripe\Stripe;
use Stripe\Refund;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Payments\CapturesRefundRequest;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use PDF; // barryvdh dompdf

class PaymentRefundService
{
    public function __construct()
    {
        // ensure Stripe key available for direct calls
        Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));
    }

    /**
     * Create invoice PDF and store file
     */
    public function createInvoiceFromTransaction(Transaction $tx)
    {
        // invoice number like INV-20251006-0001
        $invoiceNumber = 'INV-'.now()->format('Ymd').'-'.Str::padLeft($tx->id,4,'0');

        $view = view('invoices.template', ['transaction' => $tx, 'invoice_number' => $invoiceNumber])->render();

        $pdf = PDF::loadHTML($view);
        $dir = 'invoices';
        $filename = $invoiceNumber . '.pdf';
        $path = $dir.'/'.$filename;

        Storage::disk(config('filesystems.default'))->put($path, $pdf->output());

        $invoice = Invoice::create([
            'transaction_id' => $tx->id,
            'invoice_number' => $invoiceNumber,
            'pdf_path' => $path,
            'amount' => $tx->amount,
            'currency' => $tx->currency,
            'status' => 'issued'
        ]);

        return $invoice;
    }

    /**
     * Refund a transaction (calls appropriate gateway)
     * Returns array ['success'=>bool,'message'=>string,'refund_id'=>?]
     */
    public function refundTransaction(Transaction $tx, $amount = null)
    {
        $amount = $amount ?? $tx->amount;

        if ($tx->gateway === 'stripe') {
            return $this->refundStripe($tx, $amount);
        } elseif ($tx->gateway === 'paypal') {
            return $this->refundPayPal($tx, $amount);
        } else {
            return ['success' => false, 'message' => 'Unsupported gateway'];
        }
    }

    protected function refundStripe(Transaction $tx, $amount)
    {
        try {
            // try get payment_intent or charge id from transaction payload
            $payload = $tx->payload ?? [];
            $session = $payload['stripe_session'] ?? ($payload['session'] ?? null);
            $paymentIntentId = $session->payment_intent ?? ($payload['payment_intent'] ?? null);
            $chargeId = $session->payment_intent ?? null; // fallback

            if (!$paymentIntentId && isset($tx->gateway_id)) {
                // gateway_id might be session id - try to fetch session
                $sessionObj = \Stripe\Checkout\Session::retrieve($tx->gateway_id);
                $paymentIntentId = $sessionObj->payment_intent ?? null;
            }

            if (!$paymentIntentId) {
                return ['success'=>false,'message'=>'Could not find payment_intent for Stripe transaction.'];
            }

            $refund = Refund::create([
                'payment_intent' => $paymentIntentId,
                'amount' => (int) round($amount * 100), // smallest unit
            ]);

            $tx->update([
                'status' => 'refunded',
                'payload' => array_merge($tx->payload ?? [], ['refund' => $refund]),
            ]);

            // mark invoice refunded if exists
            $tx->invoice && $tx->invoice->update(['status' => 'refunded']);

            return ['success'=>true,'message'=>'Refunded via Stripe','refund_id'=>$refund->id];
        } catch (\Exception $e) {
            \Log::error('Stripe refund error: '.$e->getMessage());
            return ['success'=>false,'message'=>'Stripe refund failed: '.$e->getMessage()];
        }
    }

    protected function refundPayPal(Transaction $tx, $amount)
    {
        try {
            $clientId = env('PAYPAL_CLIENT_ID');
            $clientSecret = env('PAYPAL_CLIENT_SECRET');
            $mode = env('PAYPAL_MODE','sandbox');

            $environment = $mode === 'live'
                ? new \PayPalCheckoutSdk\Core\LiveEnvironment($clientId, $clientSecret)
                : new SandboxEnvironment($clientId, $clientSecret);
            $client = new PayPalHttpClient($environment);

            // need capture id to refund — capture id should be saved in tx payload during success
            $payload = $tx->payload ?? [];
            $captureId = $payload['paypal_capture']->result->id ?? ($payload['order']->purchase_units[0]->payments->captures[0]->id ?? null);

            if (!$captureId) {
                // try find in payload structure
                $captureId = data_get($payload, 'paypal_capture.id') ?? data_get($payload, 'order.purchase_units.0.payments.captures.0.id');
            }

            if (!$captureId) {
                return ['success'=>false,'message'=>'Could not find PayPal capture id.'];
            }

            // Refund the capture
            $request = new CapturesRefundRequest($captureId);
            $request->body = [
                'amount' => [
                    'value' => number_format($amount, 2, '.', ''),
                    'currency_code' => $tx->currency ?? env('PAYMENT_CURRENCY','PKR')
                ]
            ];

            $response = $client->execute($request);
            $result = $response->result;

            $tx->update([
                'status' => 'refunded',
                'payload' => array_merge($tx->payload ?? [], ['refund' => $result]),
            ]);

            $tx->invoice && $tx->invoice->update(['status' => 'refunded']);

            return ['success'=>true,'message'=>'Refunded via PayPal','refund_id'=>($result->id ?? null)];
        } catch (\Exception $e) {
            \Log::error('PayPal refund error: '.$e->getMessage());
            return ['success'=>false,'message'=>'PayPal refund failed: '.$e->getMessage()];
        }
    }
}
