<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BoutiqueProduct;
use App\Models\BoutiqueCartItem;
use App\Models\BoutiqueOrder;
use App\Models\BoutiqueOrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertyBoutiqueController extends Controller
{
    // GET /api/property-boutique/products
    public function products(Request $request)
    {
        $announcements = BoutiqueProduct::where('status', 'active')
            ->where('category', 'announcements')
            ->orderBy('sort_order')
            ->get();

        $credits = BoutiqueProduct::where('status', 'active')
            ->where('category', 'credits')
            ->orderBy('sort_order')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'announcements' => $announcements,
                'credits' => $credits,
            ],
        ]);
    }

    // GET /api/property-boutique/cart
    public function cart(Request $request)
    {
        $user = $request->user();

        $items = BoutiqueCartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        $subtotal = $items->sum('total_price');
        $currency = $items->first()?->currency ?? 'MAD';

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $items->map(fn($item) => [
                    'id'                  => $item->id,
                    'boutique_product_id' => $item->boutique_product_id,
                    'title'               => $item->product?->name,
                    'description'         => $item->product?->description,
                    'quantity'            => $item->quantity,
                    'unit_price'          => $item->unit_price,
                    'total_price'         => $item->total_price,
                    'currency'            => $item->currency,
                    'type'                => $item->product?->type,
                    'category'            => $item->product?->category,
                ])->values(),
                'summary' => [
                    'items_count' => $items->sum('quantity'),
                    'subtotal'    => $subtotal,
                    'currency'    => $currency ?: 'MAD',
                ],
            ],
        ]);
    }

    // POST /api/property-boutique/cart
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:boutique_products,id',
            'quantity'   => 'nullable|integer|min:1',
        ]);

        $user     = $request->user();
        $product  = BoutiqueProduct::findOrFail($request->product_id);
        $quantity = (int) ($request->quantity ?? 1);

        $existing = BoutiqueCartItem::where('user_id', $user->id)
            ->where('boutique_product_id', $product->id)
            ->first();

        if ($existing) {
            $existing->quantity   += $quantity;
            $existing->total_price = $existing->quantity * $existing->unit_price;
            $existing->save();
            $cartItem = $existing;
        } else {
            $cartItem = BoutiqueCartItem::create([
                'user_id'             => $user->id,
                'boutique_product_id' => $product->id,
                'quantity'            => $quantity,
                'unit_price'          => $product->price,
                'total_price'         => $product->price * $quantity,
                'currency'            => $product->currency,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart.',
            'data'    => $cartItem,
        ]);
    }

    // PATCH /api/property-boutique/cart/{id}
    public function updateCartItem(Request $request, $id)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);

        $user = $request->user();
        $item = BoutiqueCartItem::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $item->quantity    = $request->quantity;
        $item->total_price = $item->unit_price * $item->quantity;
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'Cart item updated.',
            'data'    => $item,
        ]);
    }

    // DELETE /api/property-boutique/cart/{id}
    public function removeCartItem(Request $request, $id)
    {
        $user = $request->user();
        BoutiqueCartItem::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail()
            ->delete();

        return response()->json(['success' => true, 'message' => 'Item removed.']);
    }

    // DELETE /api/property-boutique/cart
    public function clearCart(Request $request)
    {
        BoutiqueCartItem::where('user_id', $request->user()->id)->delete();

        return response()->json(['success' => true, 'message' => 'Cart cleared.']);
    }

    // POST /api/property-boutique/checkout
    public function checkout(Request $request)
    {
        $request->validate([
            'payment_method' => 'required|in:wallet,stripe,paypal,cash',
        ]);

        $user      = $request->user();
        $cartItems = BoutiqueCartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Cart is empty.',
            ], 422);
        }

        $subtotal = $cartItems->sum('total_price');
        $currency = $cartItems->first()?->currency ?? 'MAD';

        DB::beginTransaction();
        try {
            $order = BoutiqueOrder::create([
                'user_id'        => $user->id,
                'order_number'   => 'PB-' . now()->format('Ymd') . now()->format('His') . '-' . strtoupper(Str::random(4)),
                'subtotal'       => $subtotal,
                'total'          => $subtotal,
                'currency'       => $currency,
                'status'         => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $request->payment_method,
            ]);

            foreach ($cartItems as $cartItem) {
                BoutiqueOrderItem::create([
                    'boutique_order_id'   => $order->id,
                    'boutique_product_id' => $cartItem->boutique_product_id,
                    'title'               => $cartItem->product?->name,
                    'description'         => $cartItem->product?->description,
                    'quantity'            => $cartItem->quantity,
                    'unit_price'          => $cartItem->unit_price,
                    'total_price'         => $cartItem->total_price,
                    'currency'            => $cartItem->currency,
                    'type'                => $cartItem->product?->type,
                    'category'            => $cartItem->product?->category,
                ]);
            }

            BoutiqueCartItem::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed. Awaiting admin approval.',
                'data'    => [
                    'order_number' => $order->order_number,
                    'total'        => $order->total,
                    'currency'     => $order->currency,
                    'status'       => $order->status,
                ],
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Checkout failed: ' . $e->getMessage(),
            ], 500);
        }
    }

    // GET /api/property-boutique/orders
    public function orders(Request $request)
    {
        $orders = BoutiqueOrder::where('user_id', $request->user()->id)
            ->orderByDesc('created_at')
            ->paginate(10);

        return response()->json(['success' => true, 'data' => $orders]);
    }

    // GET /api/property-boutique/orders/{id}
    public function orderShow(Request $request, $id)
    {
        $order = BoutiqueOrder::with('items')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json(['success' => true, 'data' => $order]);
    }
}