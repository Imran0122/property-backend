<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaypalController extends Controller
{
    public function create() {
        return response()->json(['message' => 'Paypal create']);
    }

    public function success() {
        return response()->json(['message' => 'Paypal success']);
    }

    public function cancel() {
        return response()->json(['message' => 'Paypal cancel']);
    }
}
