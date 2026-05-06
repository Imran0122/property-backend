<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\BoutiqueOrder;
use App\Models\Property;
use App\Models\UserCredit;
use Carbon\Carbon;
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
            'data'    => $orders->through(fn($order) => $this->formatOrder($order)),
        ]);
    }

    // GET /api/admin/boutique-orders/{id}
    public function show($id)
    {
        $order = BoutiqueOrder::with(['user:id,name,email', 'items'])
            ->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $this->formatOrder($order),
        ]);
    }

    // POST /api/admin/boutique-orders/{id}/approve
    public function approve($id)
    {
        $order = BoutiqueOrder::with(['items', 'user'])->findOrFail($id);

        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending orders can be approved.',
            ], 422);
        }

        DB::beginTransaction();

        try {
            // 1. Order status update karo
            $order->update([
                'status'         => 'completed',
                'payment_status' => 'paid',
            ]);

            // 2. User credits fetch/create karo
            $credits = UserCredit::firstOrCreate(['user_id' => $order->user_id]);
            $creditsUpdated = false;

            foreach ($order->items as $item) {
                $type   = strtolower($item->type ?? $item->title ?? '');
                $qty    = (int) $item->quantity;
                $column = UserCredit::creditColumnForType($type);

                // Credits add karo
                if ($column) {
                    $credits->$column += $qty;
                    $creditsUpdated = true;
                }

                // Agar announcement type hai aur property_id attached hai
                // to property ko active/featured kar do
                if (
                    str_contains($type, 'announcement') &&
                    ! empty($item->property_id)
                ) {
                    $this->activateProperty($item->property_id, 30);
                }

                // Agar hot-ad hai aur property attached hai
                if (
                    str_contains($type, 'hot') &&
                    ! str_contains($type, 'super') &&
                    ! empty($item->property_id)
                ) {
                    $this->featureProperty($item->property_id, 30, 'hot');
                }

                // Agar super-hot-ad hai aur property attached hai
                if (
                    str_contains($type, 'super') &&
                    str_contains($type, 'hot') &&
                    ! empty($item->property_id)
                ) {
                    $this->featureProperty($item->property_id, 30, 'super_hot');
                }
            }

            if ($creditsUpdated) {
                $credits->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order approved. Credits added to user wallet.',
                'data'    => [
                    'order'   => $this->formatOrder($order->fresh(['items', 'user'])),
                    'credits' => [
                        'announcement_credits' => $credits->announcement_credits,
                        'refresh_credits'      => $credits->refresh_credits,
                        'hot_credits'          => $credits->hot_credits,
                        'super_hot_credits'    => $credits->super_hot_credits,
                        'story_credits'        => $credits->story_credits,
                        'photo_credits'        => $credits->photo_credits,
                        'video_credits'        => $credits->video_credits,
                    ],
                ],
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
            'message' => 'Order rejected successfully.',
            'data'    => $this->formatOrder($order->fresh()),
        ]);
    }

    // ─── Private Helpers ───────────────────────────────────────────────────────

    private function formatOrder($order): array
    {
        return [
            'id'             => $order->id,
            'order_number'   => $order->order_number,
            'user'           => $order->user
                ? [
                    'id'    => $order->user->id,
                    'name'  => $order->user->name,
                    'email' => $order->user->email,
                  ]
                : null,
            'subtotal'       => $order->subtotal,
            'total'          => $order->total,
            'currency'       => $order->currency,
            'status'         => $order->status,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
            'notes'          => $order->notes,
            'created_at'     => $order->created_at,
            'items'          => ($order->items ?? collect())->map(fn($item) => [
                'id'          => $item->id,
                'title'       => $item->title,
                'description' => $item->description,
                'quantity'    => $item->quantity,
                'unit_price'  => $item->unit_price,
                'total_price' => $item->total_price,
                'currency'    => $item->currency,
                'type'        => $item->type,
                'property_id' => $item->property_id ?? null,
            ])->values(),
        ];
    }

    /**
     * Announcement approve hone pe property active karo
     */
    private function activateProperty(int $propertyId, int $days = 30): void
    {
        Property::where('id', $propertyId)->update([
            'status'        => 'active',
            'featured_until' => Carbon::now()->addDays($days),
        ]);
    }

    /**
     * Hot / Super Hot Ad approve hone pe property featured karo
     */
    private function featureProperty(int $propertyId, int $days = 30, string $level = 'hot'): void
    {
        Property::where('id', $propertyId)->update([
            'is_featured'    => 1,
            'featured_level' => $level,        // hot | super_hot
            'featured_until' => Carbon::now()->addDays($days),
        ]);
    }
}