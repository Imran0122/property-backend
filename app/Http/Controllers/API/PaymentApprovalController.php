<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PaymentApprovalController extends Controller
{
    public function approve($id)
    {
        return response()->json(['message' => 'Payment approved']);
    }
}
