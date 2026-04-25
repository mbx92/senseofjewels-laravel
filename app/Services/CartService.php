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
    public function __construct(protected DiscountService $discountService) {}

    protected function resolveCart(): Cart
    {
        $sessionId = Session::getId();

        if (Auth::check()) {
            $sessionCart = Cart::query()->where('session_id', $sessionId)->first();

            if ($sessionCart) {
                if (! $sessionCart->user_id) {
                    $sessionCart->update(['user_id' => Auth::id()]);
                }

                return $sessionCart;
            }

            $cart = Cart::query()->firstOrCreate(
                ['user_id' => Auth::id()],
                ['session_id' => $sessionId, 'currency' => 'IDR'],
            );

            if ($cart->session_id !== $sessionId) {
                $cart->update(['session_id' => $sessionId]);
            }

            return $cart;
        }

        return Cart::query()->firstOrCreate(
            ['session_id' => $sessionId],
            ['currency' => 'IDR'],
        );
    }

    public function add(int $productId, int $qty = 1): CartItem
    {
        $product = Product::query()->where('is_active', true)->findOrFail($productId);

        $cart = $this->resolveCart();

        $item = $cart->items()->firstOrNew(['product_id' => $product->id]);

        $newQty = ($item->exists ? $item->quantity : 0) + $qty;

        $unitPrice = $this->discountService->applyProductDiscount($product) ?? $product->price;

        $item->fill([
            'product_name' => $product->name,
            'product_sku'  => $product->sku,
            'quantity'     => $newQty,
            'unit_price'   => $unitPrice,
            'line_total'   => $newQty * $unitPrice,
        ])->save();

        $this->syncTotals($cart);

        return $item;
    }

    public function update(int $cartItemId, int $qty): CartItem
    {
        $item = CartItem::findOrFail($cartItemId);

        abort_unless($item->cart->user_id === Auth::id() || $item->cart->session_id === Session::getId(), 403);

        $product = $item->product;
        $unitPrice = $product
            ? ($this->discountService->applyProductDiscount($product) ?? $product->price)
            : $item->unit_price;

        $item->update([
            'quantity'   => $qty,
            'unit_price' => $unitPrice,
            'line_total' => $qty * $unitPrice,
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
        $cart = $this->resolveCart()->load('items.product.images');
        $this->syncTotals($cart);

        return $cart->fresh('items.product.images')->items;
    }

    public function getCart(): Cart
    {
        $cart = $this->resolveCart()->load('items.product.images');
        $this->syncTotals($cart);

        return $cart->fresh('items.product.images');
    }

    protected function syncTotals(Cart $cart): void
    {
        $cart->loadMissing('items.product');

        foreach ($cart->items as $item) {
            if (! $item->product) {
                continue;
            }

            $unitPrice = $this->discountService->applyProductDiscount($item->product) ?? $item->product->price;
            $expectedLineTotal = $item->quantity * $unitPrice;

            if ((float) $item->unit_price !== (float) $unitPrice || (float) $item->line_total !== (float) $expectedLineTotal) {
                $item->update([
                    'unit_price' => $unitPrice,
                    'line_total' => $expectedLineTotal,
                ]);
            }
        }

        $subtotal = $cart->items()->sum('line_total');

        $cart->update([
            'subtotal'       => $subtotal,
            'discount_total' => 0,
            'total'          => $subtotal,
        ]);
    }
}
