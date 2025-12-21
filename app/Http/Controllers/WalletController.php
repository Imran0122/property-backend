<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    // Agent wallet dashboard
    public function index()
    {
        $wallet = Wallet::firstOrCreate(['user_id' => auth()->id()]);
        $transactions = $wallet->transactions()->latest()->paginate(10);
        return view('agent.wallet.index', compact('wallet', 'transactions'));
    }

    // Admin view all wallets
    public function adminIndex()
    {
        $wallets = Wallet::with('transactions', 'user')->paginate(15);
        return view('admin.wallets.index', compact('wallets'));
    }

    // Admin add credit manually
    public function addCredit(Request $request, $id)
    {
        $wallet = Wallet::findOrFail($id);
        $wallet->credit($request->amount, "Admin credit added");
        return back()->with('success', 'Credit added successfully!');
    }
}
