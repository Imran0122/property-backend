<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StripeController extends Controller
{
    public function createIntent(Request $request)
    {
        return response()->json(['message' => 'Stripe intent created']);
    }
}
