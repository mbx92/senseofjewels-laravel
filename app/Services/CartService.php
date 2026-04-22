<?php

namespace App\Services;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CartService
{
    protected function resolveCart(): Cart
    {
        if (Auth::check()) {
            // Prefer user-bound cart
            $cart = Cart::query()->firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => Session::getId(), 'currency' => 'IDR'],
            );
        } else {
            $cart = Cart::query()->firstOrCreate(
                ['session_id' => Session::getId()],
                ['currency' => 'IDR'],
            );
        }

        return $cart;
    }

    public function add(int $productId, int $qty = 1): CartItem
    {
        $product = Product::query()->where('is_active', true)->findOrFail($productId);

        $cart = $this->resolveCart();

        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);

        $newQty = ($item->exists ? $item->quantity : 0) + $qty;

        $item->fill([
            'product_name' => $product->name,
            'product_sku'  => $product->sku,
            'quantity'     => $newQty,
            'unit_price'   => $product->price,
            'line_total'   => $newQty * $product->price,
        ])->save();

        $this->syncTotals($cart);

        return $item;
    }

    public function update(int $cartItemId, int $qty): CartItem
    {
        $item = CartItem::findOrFail($cartItemId);

        abort_unless($item->cart->user_id === Auth::id() || $item->cart->session_id === Session::getId(), 403);

        $item->update([
            'quantity'   => $qty,
            'line_total' => $qty * $item->unit_price,
        ]);

        $this->syncTotals($item->cart);

        return $item->fresh();
    }

    public function remove(int $cartItemId): void
    {
        $item = CartItem::findOrFail($cartItemId);

        abort_unless($item->cart->user_id === Auth::id() || $item->cart->session_id === Session::getId(), 403);

        $cart = $item->cart;
        $item->delete();

        $this->syncTotals($cart);
    }

    public function clear(): void
    {
        $cart = $this->resolveCart();
        $cart->items()->delete();
        $cart->update(['subtotal' => 0, 'discount_total' => 0, 'total' => 0]);
    }

    /**
     * Merge anonymous session cart into the authenticated user's cart after login.
     */
    public function merge(int $userId): void
    {
        $sessionCart = Cart::query()->where('session_id', Session::getId())->first();

        if (! $sessionCart || $sessionCart->items()->doesntExist()) {
            return;
        }

        $userCart = Cart::query()->firstOrCreate(
            ['user_id' => $userId],
            ['session_id' => Session::getId(), 'currency' => 'IDR'],
        );

        foreach ($sessionCart->items as $sessionItem) {
            $existing = $userCart->items()->where('product_id', $sessionItem->product_id)->first();

            if ($existing) {
                $newQty = $existing->quantity + $sessionItem->quantity;
                $existing->update([
                    'quantity'   => $newQty,
                    'line_total' => $newQty * $existing->unit_price,
                ]);
            } else {
                $userCart->items()->create($sessionItem->only([
                    'product_id', 'product_name', 'product_sku',
                    'quantity', 'unit_price', 'line_total', 'options',
                ]));
            }
        }

        $sessionCart->items()->delete();
        $sessionCart->delete();

        $this->syncTotals($userCart);
    }

    public function getItems(): Collection
    {
        return $this->resolveCart()->load('items.product.images')->items;
    }

    public function getCart(): Cart
    {
        return $this->resolveCart()->load('items.product.images');
    }

    protected function syncTotals(Cart $cart): void
    {
        $subtotal = $cart->items()->sum('line_total');

        $cart->update([
            'subtotal'       => $subtotal,
            'discount_total' => 0,
            'total'          => $subtotal,
        ]);
    }
}
