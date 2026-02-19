<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeLoanController extends Controller
{
  public function calculate(Request $request)
{
    $propertyPrice = (float) $request->query('property_price', 0);
    $downPaymentPercent = (float) $request->query('down_payment_percentage', 0);
    $loanYears = (int) $request->query('loan_years', 0);
    $interestRate = (float) $request->query('interest_rate', 0);

    if ($propertyPrice <= 0 || $loanYears <= 0 || $interestRate <= 0) {
        return response()->json([
            'status' => false,
            'message' => 'Invalid input values'
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

