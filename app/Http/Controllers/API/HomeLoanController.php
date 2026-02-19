<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeLoanController extends Controller
{
    public function calculate(Request $request)
    {
        // ✅ Validation
        $validated = $request->validate([
            'property_price' => 'required|numeric|min:1',
            'down_payment_percentage' => 'required|numeric|min:0|max:100',
            'loan_years' => 'required|integer|min:1|max:35',
            'interest_rate' => 'required|numeric|min:0.1|max:50'
        ]);

        $propertyPrice = (float) $validated['property_price'];
        $downPaymentPercent = (float) $validated['down_payment_percentage'];
        $loanYears = (int) $validated['loan_years'];
        $interestRate = (float) $validated['interest_rate'];

        // ✅ Calculations
        $downPaymentAmount = ($propertyPrice * $downPaymentPercent) / 100;
        $loanAmount = $propertyPrice - $downPaymentAmount;

        $monthlyRate = $interestRate / 100 / 12;
        $months = $loanYears * 12;

        $monthlyInstallment = ($loanAmount * $monthlyRate * pow(1 + $monthlyRate, $months)) /
            (pow(1 + $monthlyRate, $months) - 1);

        $totalPayment = $monthlyInstallment * $months;
        $totalInterest = $totalPayment - $loanAmount;

        // ✅ Clean Response (Design Friendly)
        return response()->json([
            'success' => true,
            'data' => [
                'property_price' => round($propertyPrice),
                'down_payment_percentage' => $downPaymentPercent,
                'down_payment_amount' => round($downPaymentAmount),
                'loan_years' => $loanYears,
                'interest_rate' => $interestRate,
                'loan_amount' => round($loanAmount),
                'monthly_installment' => round($monthlyInstallment),
                'total_payment' => round($totalPayment),
                'total_interest' => round($totalInterest),
            ]
        ]);
    }


}

