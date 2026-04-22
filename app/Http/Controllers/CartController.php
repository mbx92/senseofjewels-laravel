<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function index(Request $request): View
    {
        $cart = $this->currentCart($request)->load('items.product.images');

        return view('cart.index', compact('cart'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'product_id' => ['required', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::query()
            ->where('is_active', true)
            ->findOrFail($validated['product_id']);

        $cart = $this->currentCart($request);

        if ($request->user() && ! $cart->user_id) {
            $cart->update(['user_id' => $request->user()->id]);
        }

        $quantity = $validated['quantity'] ?? 1;

        $item = $cart->items()->firstOrNew([
            'product_id' => $product->id,
        ]);

        $newQuantity = ($item->exists ? $item->quantity : 0) + $quantity;

        $item->fill([
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => $newQuantity,
            'unit_price' => $product->price,
            'line_total' => $newQuantity * $product->price,
        ]);
        $item->save();

        $this->syncCartTotals($cart);

        return back()->with('status', "{$product->name} added to cart.");
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->currentCart($request);

        abort_unless($cartItem->cart_id === $cart->id, 404);

        $cartItem->update([
            'quantity' => $validated['quantity'],
            'line_total' => $validated['quantity'] * $cartItem->unit_price,
        ]);

        $this->syncCartTotals($cart);

        return back()->with('status', 'Cart updated successfully.');
    }

    public function destroy(Request $request, CartItem $cartItem): RedirectResponse
    {
        $cart = $this->currentCart($request);

        abort_unless($cartItem->cart_id === $cart->id, 404);

        $cartItem->delete();

        $this->syncCartTotals($cart);

        return back()->with('status', 'Item removed from cart.');
    }

    protected function currentCart(Request $request): Cart
    {
        return Cart::query()->firstOrCreate(
            ['session_id' => $request->session()->getId()],
            [
                'user_id' => $request->user()?->id,
                'currency' => 'IDR',
            ],
        );
    }

    protected function syncCartTotals(Cart $cart): void
    {
        $subtotal = $cart->items()->sum('line_total');

        $cart->update([
            'subtotal' => $subtotal,
            'discount_total' => 0,
            'total' => $subtotal,
        ]);
    }
}
