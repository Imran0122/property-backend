<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeLoanController extends Controller
{
   public function calculate(Request $request)
{
    // Validate request (important)
    $request->validate([
        'property_price' => 'required|numeric|min:1',
        'down_payment_percentage' => 'required|numeric|min:0',
        'loan_years' => 'required|numeric|min:1',
        'interest_rate' => 'required|numeric|min:0',
    ]);

    $price = $request->property_price;
    $downPercentage = $request->down_payment_percentage;
    $years = $request->loan_years;
    $interestRate = $request->interest_rate;

    $downPayment = ($price * $downPercentage) / 100;
    $loanAmount = $price - $downPayment;

    $monthlyRate = ($interestRate / 100) / 12;
    $months = $years * 12;

    // Safe EMI calculation
    if ($monthlyRate == 0) {
        $emi = $loanAmount / $months;
    } else {
        $emi = ($loanAmount * $monthlyRate * pow(1 + $monthlyRate, $months)) 
              / (pow(1 + $monthlyRate, $months) - 1);
    }

    return response()->json([
        'status' => true,
        'property_price' => $price,
        'down_payment' => round($downPayment, 2),
        'loan_amount' => round($loanAmount, 2),
        'monthly_emi' => round($emi, 2),
    ]);
}

}

