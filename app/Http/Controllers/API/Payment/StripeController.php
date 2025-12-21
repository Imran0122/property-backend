<?php

namespace App\Http\Controllers\API\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class StripeController extends Controller
{
    public function createIntent(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1'
        ]);

        Stripe::setApiKey(config('services.stripe.secret'));

        $intent = PaymentIntent::create([
            'amount' => $request->amount * 100,
            'currency' => 'usd'
        ]);

        return response()->json([
            'client_secret' => $intent->client_secret
        ]);
    }
}
