<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // Admin View All Transactions
    public function adminIndex(Request $request)
    {
        $transactions = Invoice::with(['user', 'property'])
            ->when($request->status, fn($q) => $q->where('status', $request->status))
            ->when($request->from, fn($q) => $q->whereDate('created_at', '>=', $request->from))
            ->when($request->to, fn($q) => $q->whereDate('created_at', '<=', $request->to))
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.transactions.index', compact('transactions'));
    }

    // Agent View Own Transactions
    public function agentIndex(Request $request)
    {
        $transactions = Invoice::with(['property'])
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('agent.transactions.index', compact('transactions'));
    }
}
