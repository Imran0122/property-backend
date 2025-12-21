<?php

namespace App\Http\Controllers\API\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaypalController extends Controller
{
    public function create()
    {
        return response()->json([
            'status' => true,
            'message' => 'Paypal order created (frontend SDK use karega)'
        ]);
    }

    public function success()
    {
        return response()->json([
            'status' => true,
            'message' => 'Payment successful'
        ]);
    }

    public function cancel()
    {
        return response()->json([
            'status' => false,
            'message' => 'Payment cancelled'
        ]);
    }
}
