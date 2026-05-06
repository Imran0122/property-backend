<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\UserCredit;
use App\Models\BoutiqueOrder;

class WalletController extends Controller
{
    // GET /api/wallet
    public function index(Request $request)
    {
        $user    = $request->user();
        $credits = UserCredit::firstOrCreate(['user_id' => $user->id]);

        return response()->json([
            'success' => true,
            'data'    => [
                'refresh_credits'   => $credits->refresh_credits,
                'hot_credits'       => $credits->hot_credits,
                'super_hot_credits' => $credits->super_hot_credits,
                'story_credits'     => $credits->story_credits,
                'photo_credits'     => $credits->photo_credits,
                'video_credits'     => $credits->video_credits,
            ],
        ]);
    }

    // GET /api/wallet/orders — recent orders summary
    public function orders(Request $request)
    {
        $orders = BoutiqueOrder::where('user_id', $request->user()->id)
            ->latest()
            ->take(5)
            ->get(['id', 'order_number', 'total', 'currency', 'status', 'payment_status', 'created_at']);

        return response()->json([
            'success' => true,
            'data'    => $orders,
        ]);
    }
}