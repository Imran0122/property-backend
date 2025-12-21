<?php

namespace App\Http\Controllers;

use App\Models\Package;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\Invoice;
use App\Mail\InvoiceMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;

class PaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // ensure only logged in users can buy
    }

    // Show packages page
    public function index()
    {
        $packages = Package::all();
        return view('subscriptions.index', compact('packages'));
    }

    /** STRIPE CHECKOUT **/
    public function stripeCheckout(Request $request, Package $package)
    {
        $user = Auth::user();
        Stripe::setApiKey(config('services.stripe.secret') ?? env('STRIPE_SECRET'));

        $currency = env('PAYMENT_CURRENCY', 'PKR');
        $amount = round($package->price, 2);

        // Create a local pending transaction
        $tx = Transaction::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'gateway' => 'stripe',
            'amount' => $amount,
            'currency' => $currency,
            'status' => 'pending',
        ]);

        $session = StripeSession::create([
            'payment_method_types' => ['card'],
            'mode' => 'payment',
            'line_items' => [[
                'price_data' => [
                    'currency' => strtolower($currency),
                    'product_data' => ['name' => $package->name . " Package"],
                    'unit_amount' => (int) round($amount * 100),
                ],
                'quantity' => 1,
            ]],
            'metadata' => [
                'transaction_id' => $tx->id,
                'user_id' => $user->id,
                'package_id' => $package->id,
            ],
            'success_url' => route('payments.stripe.success') . '?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url' => route('subscriptions.index'),
        ]);

        $tx->update(['gateway_id' => $session->id, 'payload' => ['session' => $session]]);

        return redirect($session->url);
    }

    public function stripeSuccess(Request $request)
    {
        $sessionId = $request->get('session_id');
        return view('subscriptions.success', compact('sessionId'));
    }

    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $secret = env('STRIPE_WEBHOOK_SECRET');

        try {
            $event = \Stripe\Webhook::constructEvent($payload, $sigHeader, $secret);
        } catch (\Exception $e) {
            Log::error("Stripe webhook error: ".$e->getMessage());
            return response()->json(['error' => 'Invalid payload/signature'], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            $txId = $session->metadata->transaction_id ?? null;

            if ($txId) {
                $tx = Transaction::find($txId);
                if ($tx && $tx->status !== 'succeeded') {
                    $tx->update([
                        'status' => 'succeeded',
                        'payload' => array_merge($tx->payload ?? [], ['stripe_session' => $session]),
                        'gateway_id' => $session->id,
                    ]);

                    $pkg = Package::find($tx->package_id);
                    if ($pkg) {
                        $sub = Subscription::create([
                            'user_id' => $tx->user_id,
                            'package_id' => $pkg->id,
                            'starts_at' => now(),
                            'ends_at' => now()->addDays($pkg->duration_days),
                            'status' => 'active',
                        ]);

                        // Generate invoice
                        $invoice = Invoice::create([
                            'user_id' => $tx->user_id,
                            'transaction_id' => $tx->id,
                            'amount' => $tx->amount,
                            'status' => 'paid',
                            'pdf_path' => $this->generateInvoicePdf($tx, $pkg),
                        ]);

                        // Send email
                        Mail::to($tx->user->email)->send(new InvoiceMail($invoice));
                    }
                }
            }
        }

        return response()->json(['received' => true]);
    }

    /** PAYPAL **/
    public function paypalCreate(Request $request, Package $package)
    {
        $user = Auth::user();
        $clientId = env('PAYPAL_CLIENT_ID');
        $clientSecret = env('PAYPAL_CLIENT_SECRET');
        $mode = env('PAYPAL_MODE', 'sandbox');

        $environment = $mode === 'live'
            ? new \PayPalCheckoutSdk\Core\LiveEnvironment($clientId, $clientSecret)
            : new SandboxEnvironment($clientId, $clientSecret);
        $client = new PayPalHttpClient($environment);

        $orderRequest = new OrdersCreateRequest();
        $orderRequest->prefer('return=representation');
        $orderRequest->body = [
            'intent' => 'CAPTURE',
            'purchase_units' => [[
                'amount' => [
                    'currency_code' => env('PAYMENT_CURRENCY','PKR'),
                    'value' => number_format($package->price, 2, '.', ''),
                ],
                'description' => $package->name . ' Package',
            ]],
            'application_context' => [
                'return_url' => route('payments.paypal.success'),
                'cancel_url' => route('subscriptions.index'),
            ],
        ];

        $tx = Transaction::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'gateway' => 'paypal',
            'amount' => $package->price,
            'currency' => env('PAYMENT_CURRENCY','PKR'),
            'status' => 'pending',
        ]);

        try {
            $response = $client->execute($orderRequest);
            $order = $response->result;
            $approveUrl = collect($order->links)->firstWhere('rel', 'approve')->href ?? null;

            $tx->update(['gateway_id' => $order->id, 'payload' => ['order' => $order]]);
            if ($approveUrl) return redirect($approveUrl);

            return redirect()->route('subscriptions.index')->with('error', 'Could not create PayPal order.');
        } catch (\Exception $e) {
            Log::error('PayPal create error: '.$e->getMessage());
            return redirect()->route('subscriptions.index')->with('error', 'PayPal error.');
        }
    }

    public function paypalSuccess(Request $request)
    {
        $token = $request->get('token');
        $clientId = env('PAYPAL_CLIENT_ID');
        $clientSecret = env('PAYPAL_CLIENT_SECRET');
        $mode = env('PAYPAL_MODE', 'sandbox');

        $environment = $mode === 'live'
            ? new \PayPalCheckoutSdk\Core\LiveEnvironment($clientId, $clientSecret)
            : new SandboxEnvironment($clientId, $clientSecret);
        $client = new PayPalHttpClient($environment);

        $captureRequest = new OrdersCaptureRequest($token);
        $captureRequest->prefer('return=representation');

        try {
            $response = $client->execute($captureRequest);
            $result = $response->result;

            $tx = Transaction::where('gateway_id', $token)->first();
            if ($tx && $tx->status !== 'succeeded') {
                $tx->update([
                    'status' => 'succeeded',
                    'payload' => array_merge($tx->payload ?? [], ['paypal_capture' => $result]),
                ]);

                $pkg = Package::find($tx->package_id);
                if ($pkg) {
                    $sub = Subscription::create([
                        'user_id' => $tx->user_id,
                        'package_id' => $pkg->id,
                        'starts_at' => now(),
                        'ends_at' => now()->addDays($pkg->duration_days),
                        'status' => 'active',
                    ]);

                    // Generate invoice
                    $invoice = Invoice::create([
                        'user_id' => $tx->user_id,
                        'transaction_id' => $tx->id,
                        'amount' => $tx->amount,
                        'status' => 'paid',
                        'pdf_path' => $this->generateInvoicePdf($tx, $pkg),
                    ]);

                    Mail::to($tx->user->email)->send(new InvoiceMail($invoice));
                }

                return view('subscriptions.success', ['order' => $result]);
            }
        } catch (\Exception $e) {
            Log::error('PayPal capture error: '.$e->getMessage());
            return redirect()->route('subscriptions.index')->with('error', 'PayPal capture failed.');
        }
    }

    /** Helper: generate invoice PDF and return file path **/
    protected function generateInvoicePdf(Transaction $tx, Package $pkg)
    {
        $fileName = 'invoices/invoice_'.$tx->id.'.pdf';
        $filePath = storage_path('app/'.$fileName);

        $pdf = \PDF::loadView('pdf.invoice', ['transaction' => $tx, 'package' => $pkg]);
        $pdf->save($filePath);

        return $fileName;
    }
}
