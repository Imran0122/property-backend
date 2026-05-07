<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoutiqueOrder;
use App\Models\UserCredit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminBoutiqueController extends Controller
{
    // GET /api/admin/boutique-orders
    public function index(Request $request)
    {
        try {
            $orders = BoutiqueOrder::with(['user', 'items.product'])
                ->orderBy('created_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data'    => $orders,
            ]);
        } catch (\Throwable $e) {
            Log::error('AdminBoutiqueController@index: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // GET /api/admin/boutique-orders/{id}
    public function show($id)
    {
        try {
            $order = BoutiqueOrder::with(['user', 'items.product'])->findOrFail($id);
            return response()->json(['success' => true, 'data' => $order]);
        } catch (\Throwable $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 404);
        }
    }

    // POST /api/admin/boutique-orders/{id}/approve
    public function approve($id)
    {
        try {
            DB::beginTransaction();

            $order = BoutiqueOrder::with('items.product')->findOrFail($id);

            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending orders can be approved.',
                ], 422);
            }

            // Add credits to user wallet
            $credits = UserCredit::firstOrCreate(['user_id' => $order->user_id]);

            foreach ($order->items as $item) {
                $product = $item->product;
                if (!$product) continue;

                $col = $this->creditColumn($product->type);
                if ($col) {
                    $credits->increment($col, $item->quantity);
                }
            }

            $order->update([
                'status'         => 'completed',
                'payment_status' => 'paid',
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order approved. Credits added to user wallet.',
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('AdminBoutiqueController@approve: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // POST /api/admin/boutique-orders/{id}/reject
    public function reject($id)
    {
        try {
            $order = BoutiqueOrder::findOrFail($id);

            if ($order->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Only pending orders can be rejected.',
                ], 422);
            }

            $order->update([
                'status'         => 'cancelled',
                'payment_status' => 'failed',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Order rejected successfully.',
            ]);
        } catch (\Throwable $e) {
            Log::error('AdminBoutiqueController@reject: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function creditColumn(string $type): ?string
    {
        return match (true) {
            str_contains($type, 'hot-ad') && !str_contains($type, 'super') => 'hot_credits',
            str_contains($type, 'super-hot')                               => 'super_hot_credits',
            str_contains($type, 'refresh')                                 => 'refresh_credits',
            str_contains($type, 'announcement')                            => 'announcement_credits',
            str_contains($type, 'story')                                   => 'story_credits',
            str_contains($type, 'photo')                                   => 'photo_credits',
            str_contains($type, 'video')                                   => 'video_credits',
            default                                                        => null,
        };
    }
}