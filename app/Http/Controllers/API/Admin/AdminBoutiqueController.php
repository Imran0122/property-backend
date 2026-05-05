<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Mail\StatusUpdateMail;
use App\Models\BoutiqueOrder;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AdminBoutiqueController extends Controller
{
    public function index()
    {
        $orders = BoutiqueOrder::with(['user', 'items'])
            ->latest()
            ->paginate(20);

        return response()->json(['status' => true, 'data' => $orders]);
    }

    public function show($id)
    {
        $order = BoutiqueOrder::with(['user', 'items'])->findOrFail($id);
        return response()->json(['status' => true, 'data' => $order]);
    }

    public function approve($id)
    {
        DB::beginTransaction();
        try {
            $order = BoutiqueOrder::with(['user', 'items'])->findOrFail($id);

            $order->update([
                'status'         => 'completed',
                'payment_status' => 'paid',
            ]);

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $order->user_id],
                ['balance' => 0]
            );
            $wallet->credit(
                $order->total,
                'credit',
                "Order #{$order->order_number} approved by admin"
            );

            if ($order->user?->email) {
                try {
                    Mail::to($order->user->email)->send(new StatusUpdateMail(
                        'order',
                        'approved',
                        $order->user->name,
                        "Order #{$order->order_number}"
                    ));
                } catch (\Exception $e) {}
            }

            DB::commit();
            return response()->json(['status' => true, 'message' => 'Order approved and credits added to wallet']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function reject($id)
    {
        $order = BoutiqueOrder::with('user')->findOrFail($id);

        $order->update([
            'status'         => 'cancelled',
            'payment_status' => 'failed',
        ]);

        if ($order->user?->email) {
            try {
                Mail::to($order->user->email)->send(new StatusUpdateMail(
                    'order',
                    'rejected',
                    $order->user->name,
                    "Order #{$order->order_number}"
                ));
            } catch (\Exception $e) {}
        }

        return response()->json(['status' => true, 'message' => 'Order rejected']);
    }
}