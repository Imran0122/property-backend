<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\UserCredit;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    public function index(Request $request)
    {
        $user    = $request->user();
        $credits = UserCredit::firstOrCreate(['user_id' => $user->id]);

        return response()->json([
            'success' => true,
            'data'    => [
                'announcement_credits' => $credits->announcement_credits ?? 0,
                'refresh_credits'      => $credits->refresh_credits ?? 0,
                'hot_credits'          => $credits->hot_credits ?? 0,
                'super_hot_credits'    => $credits->super_hot_credits ?? 0,
                'story_credits'        => $credits->story_credits ?? 0,
                'photo_credits'        => $credits->photo_credits ?? 0,
                'video_credits'        => $credits->video_credits ?? 0,
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