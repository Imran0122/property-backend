<?php

// namespace App\Http\Controllers\API;

// use App\Http\Controllers\Controller;
// use Illuminate\Http\Request;

// class HomeLoanController extends Controller
// {
//     public function calculate(Request $request)
//     {
//         $request->validate([
//             'property_price' => 'required|numeric|min:1',
//             'down_payment_percentage' => 'required|numeric|min:0|max:100',
//             'loan_years' => 'required|numeric|min:1',
//             'interest_rate' => 'required|numeric|min:0'
//         ]);

//         $propertyPrice = $request->property_price;
//         $downPaymentPercentage = $request->down_payment_percentage;
//         $loanYears = $request->loan_years;
//         $annualInterestRate = $request->interest_rate;

//         // Down payment amount
//         $downPaymentAmount = ($propertyPrice * $downPaymentPercentage) / 100;

//         // Loan amount
//         $loanAmount = $propertyPrice - $downPaymentAmount;

//         // Convert yearly interest to monthly
//         $monthlyInterestRate = ($annualInterestRate / 100) / 12;

//         // Total months
//         $totalMonths = $loanYears * 12;

//         // EMI Formula
//         if ($monthlyInterestRate > 0) {
//             $emi = ($loanAmount * $monthlyInterestRate * pow(1 + $monthlyInterestRate, $totalMonths)) /
//                    (pow(1 + $monthlyInterestRate, $totalMonths) - 1);
//         } else {
//             $emi = $loanAmount / $totalMonths;
//         }

//         $totalPayment = $emi * $totalMonths;
//         $totalInterest = $totalPayment - $loanAmount;

//         return response()->json([
//             'status' => true,
//             'data' => [
//                 'property_price' => round($propertyPrice, 2),
//                 'down_payment_amount' => round($downPaymentAmount, 2),
//                 'loan_amount' => round($loanAmount, 2),
//                 'monthly_installment' => round($emi, 2),
//                 'total_payment' => round($totalPayment, 2),
//                 'total_interest' => round($totalInterest, 2),
//                 'loan_years' => $loanYears,
//                 'interest_rate' => $annualInterestRate
//             ]
//         ]);
//     }
// }




namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeLoanController extends Controller
{
    // public function calculate(Request $request)
    // {
    //     $price = $request->property_price;
    //     $downPercentage = $request->down_payment_percentage;
    //     $years = $request->loan_years;
    //     $interestRate = $request->interest_rate;

    //     $downPayment = ($price * $downPercentage) / 100;
    //     $loanAmount = $price - $downPayment;

    //     $monthlyRate = ($interestRate / 100) / 12;
    //     $months = $years * 12;

    //     $emi = ($loanAmount * $monthlyRate * pow(1 + $monthlyRate, $months)) 
    //           / (pow(1 + $monthlyRate, $months) - 1);

    //     return response()->json([
    //         'status' => true,
    //         'loan_amount' => round($loanAmount, 2),
    //         'monthly_emi' => round($emi, 2)
    //     ]);
    // }


    public function calculate()
{
    return response()->json([
        'status' => true,
        'message' => 'Home Loan API Working'
    ]);
}

}
