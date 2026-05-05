<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoutiqueOrder;
use App\Models\UserCredit;
use Illuminate\Support\Facades\DB;

class AdminBoutiqueController extends Controller
{
    // GET /api/admin/boutique-orders
    public function index()
    {
        $orders = BoutiqueOrder::with(['user:id,name,email', 'items'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => $orders->through(fn($order) => [
                'id'             => $order->id,
                'order_number'   => $order->order_number,
                'user'           => $order->user,
                'subtotal'       => $order->subtotal,
                'total'          => $order->total,
                'currency'       => $order->currency,
                'status'         => $order->status,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'notes'          => $order->notes,
                'created_at'     => $order->created_at,
                'items'          => $order->items->map(fn($item) => [
                    'id'          => $item->id,
                    'title'       => $item->title,
                    'description' => $item->description,
                    'quantity'    => $item->quantity,
                    'unit_price'  => $item->unit_price,
                    'total_price' => $item->total_price,
                    'currency'    => $item->currency,
                    'type'        => $item->type,
                ])->values(),
            ]),
        ]);
    }

    // GET /api/admin/boutique-orders/{id}
    public function show($id)
    {
        $order = BoutiqueOrder::with(['user:id,name,email', 'items'])->findOrFail($id);

        return response()->json(['success' => true, 'data' => $order]);
    }

    // POST /api/admin/boutique-orders/{id}/approve
    public function approve($id)
    {
        $order = BoutiqueOrder::with('items')->findOrFail($id);

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be approved.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status'         => 'completed',
                'payment_status' => 'paid',
            ]);

            $credits = UserCredit::firstOrCreate(['user_id' => $order->user_id]);

            foreach ($order->items as $item) {
                $type = strtolower($item->type ?? '');
                $qty  = (int) $item->quantity;

                if (str_contains($type, 'super') && str_contains($type, 'hot')) {
                    $credits->super_hot_credits += $qty;
                } elseif (str_contains($type, 'hot')) {
                    $credits->hot_credits += $qty;
                } elseif (str_contains($type, 'refresh')) {
                    $credits->refresh_credits += $qty;
                } elseif (str_contains($type, 'story')) {
                    $credits->story_credits += $qty;
                } elseif (str_contains($type, 'photo')) {
                    $credits->photo_credits += $qty;
                } elseif (str_contains($type, 'video')) {
                    $credits->video_credits += $qty;
                }
            }

            $credits->save();
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order approved. Credits added to user wallet.',
                'data'    => $order->fresh(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Approval failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    // POST /api/admin/boutique-orders/{id}/reject
    public function reject($id)
    {
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
            'message' => 'Order rejected.',
            'data'    => $order->fresh(),
        ]);
    }
}