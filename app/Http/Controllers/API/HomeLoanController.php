<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeLoanController extends Controller
{
 public function calculate(Request $request)
{
    $propertyPrice = (float) $request->input('property_price');
    $downPaymentPercent = (float) $request->input('down_payment_percentage');
    $loanYears = (int) $request->input('loan_years');
    $interestRate = (float) $request->input('interest_rate');

    if (!$propertyPrice || !$loanYears || !$interestRate) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid input values',
            'received_data' => $request->all()
        ], 400);
    }

    $downPaymentAmount = ($propertyPrice * $downPaymentPercent) / 100;
    $loanAmount = $propertyPrice - $downPaymentAmount;
    $monthlyRate = $interestRate / 100 / 12;
    $months = $loanYears * 12;

    $monthlyInstallment = ($loanAmount * $monthlyRate * pow(1 + $monthlyRate, $months)) /
        (pow(1 + $monthlyRate, $months) - 1);

    return response()->json([
        'status' => true,
        'loan_amount' => round($loanAmount),
        'monthly_installment' => round($monthlyInstallment),
        'total_payment' => round($monthlyInstallment * $months)
    ]);
}


}

