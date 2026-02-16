<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bank;

class BankController extends Controller
{
    public function index()
    {
        $banks = Bank::where('is_active', true)
            ->select('id', 'name', 'logo', 'interest_rate')
            ->get();

        return response()->json([
            'status' => true,
            'data' => $banks
        ]);
    }
}
