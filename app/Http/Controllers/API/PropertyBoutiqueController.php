<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\BoutiqueCartItem;
use App\Models\BoutiqueOrder;
use App\Models\BoutiqueOrderItem;
use App\Models\BoutiqueProduct;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PropertyBoutiqueController extends Controller
{
    public function products(Request $request)
    {
        $user = $request->user();

        $products = BoutiqueProduct::where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();

        $announcements = $products->where('category', 'announcements')->values();
        $credits = $products->where('category', 'credits')->values();

        $cartItems = BoutiqueCartItem::where('user_id', $user->id)->get();

        $cartTotal = $cartItems->sum(function ($item) {
            return $item->quantity * $item->unit_price;
        });

        return response()->json([
            'status' => true,
            'data' => [
                'announcements' => $announcements,
                'credits' => $credits,
                'cart_summary' => [
                    'items_count' => (int) $cartItems->sum('quantity'),
                    'total' => (float) $cartTotal,
                    'currency' => 'MAD',
                ]
            ]
        ]);
    }

    public function cart(Request $request)
    {
        $items = BoutiqueCartItem::with('product')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        $formatted = $items->map(function ($item) {
            return [
                'id' => $item->id,
                'product_id' => $item->boutique_product_id,
                'product_name' => optional($item->product)->name,
                'property_id' => $item->property_id,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'total_price' => (float) ($item->quantity * $item->unit_price),
                'currency' => $item->currency,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'items' => $formatted,
                'summary' => [
                    'items_count' => (int) $items->sum('quantity'),
                    'subtotal' => (float) $formatted->sum('total_price'),
                    'currency' => 'MAD',
                ]
            ]
        ]);
    }

    public function addToCart(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'product_id' => ['required', 'exists:boutique_products,id'],
            'property_id' => ['nullable', 'exists:properties,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = BoutiqueProduct::findOrFail($data['product_id']);

        if ($product->requires_property && empty($data['property_id'])) {
            return response()->json([
                'status' => false,
                'message' => 'This product requires a property selection.'
            ], 422);
        }

        if (!empty($data['property_id'])) {
            $property = Property::where('id', $data['property_id'])
                ->where('user_id', $user->id)
                ->first();

            if (!$property) {
                return response()->json([
                    'status' => false,
                    'message' => 'Selected property does not belong to the current user.'
                ], 422);
            }
        }

        $quantity = $data['quantity'] ?? 1;

        $query = BoutiqueCartItem::where('user_id', $user->id)
            ->where('boutique_product_id', $product->id);

        if (!empty($data['property_id'])) {
            $query->where('property_id', $data['property_id']);
        } else {
            $query->whereNull('property_id');
        }

        $existing = $query->first();

        if ($existing) {
            $existing->quantity += $quantity;
            $existing->save();
            $item = $existing->load('product');
        } else {
            $item = BoutiqueCartItem::create([
                'user_id' => $user->id,
                'boutique_product_id' => $product->id,
                'property_id' => $data['property_id'] ?? null,
                'quantity' => $quantity,
                'unit_price' => $product->price,
                'currency' => $product->currency,
            ])->load('product');
        }

        return response()->json([
            'status' => true,
            'message' => 'Item added to cart successfully',
            'data' => $item
        ]);
    }

    public function updateCartItem(Request $request, $id)
    {
        $user = $request->user();

        $item = BoutiqueCartItem::with('product')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $data = $request->validate([
            'quantity' => ['nullable', 'integer', 'min:1'],
            'property_id' => ['nullable', 'exists:properties,id'],
        ]);

        if (array_key_exists('property_id', $data) && !empty($data['property_id'])) {
            $property = Property::where('id', $data['property_id'])
                ->where('user_id', $user->id)
                ->first();

            if (!$property) {
                return response()->json([
                    'status' => false,
                    'message' => 'Selected property does not belong to the current user.'
                ], 422);
            }
        }

        if ($item->product && $item->product->requires_property && empty($data['property_id']) && !$item->property_id) {
            return response()->json([
                'status' => false,
                'message' => 'This product requires a property selection.'
            ], 422);
        }

        if (isset($data['quantity'])) {
            $item->quantity = $data['quantity'];
        }

        if (array_key_exists('property_id', $data)) {
            $item->property_id = $data['property_id'];
        }

        $item->save();

        return response()->json([
            'status' => true,
            'message' => 'Cart item updated successfully',
            'data' => $item->fresh()->load('product')
        ]);
    }

    public function removeFromCart(Request $request, $id)
    {
        $item = BoutiqueCartItem::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $item->delete();

        return response()->json([
            'status' => true,
            'message' => 'Cart item removed successfully'
        ]);
    }

    public function clearCart(Request $request)
    {
        BoutiqueCartItem::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'status' => true,
            'message' => 'Cart cleared successfully'
        ]);
    }

    public function checkout(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'payment_method' => ['required', 'in:wallet,stripe,paypal'],
            'notes' => ['nullable', 'string'],
        ]);

        $cartItems = BoutiqueCartItem::with('product')
            ->where('user_id', $user->id)
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'status' => false,
                'message' => 'Cart is empty'
            ], 422);
        }

        foreach ($cartItems as $item) {
            if ($item->product && $item->product->requires_property && empty($item->property_id)) {
                return response()->json([
                    'status' => false,
                    'message' => 'One or more cart items need a property before checkout.'
                ], 422);
            }
        }

        DB::beginTransaction();

        try {
            $subtotal = $cartItems->sum(function ($item) {
                return $item->quantity * $item->unit_price;
            });

            $order = BoutiqueOrder::create([
                'user_id' => $user->id,
                'order_number' => 'PB-' . now()->format('YmdHis') . '-' . strtoupper(Str::random(4)),
                'subtotal' => $subtotal,
                'total' => $subtotal,
                'currency' => 'MAD',
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => $data['payment_method'],
                'notes' => $data['notes'] ?? null,
            ]);

            foreach ($cartItems as $item) {
                BoutiqueOrderItem::create([
                    'boutique_order_id' => $order->id,
                    'boutique_product_id' => $item->boutique_product_id,
                    'property_id' => $item->property_id,
                    'title' => $item->product->name ?? 'Product',
                    'description' => $item->product->description ?? null,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->quantity * $item->unit_price,
                    'currency' => $item->currency,
                ]);
            }

            BoutiqueCartItem::where('user_id', $user->id)->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Order created successfully',
                'data' => $order->load('items')
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Checkout failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function orders(Request $request)
    {
        $orders = BoutiqueOrder::with('items')
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return response()->json([
            'status' => true,
            'data' => $orders
        ]);
    }

    public function orderShow(Request $request, $id)
    {
        $order = BoutiqueOrder::with('items.product')
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $order
        ]);
    }
}