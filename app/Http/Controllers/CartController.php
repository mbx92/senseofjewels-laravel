<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\Setting;
use App\Services\DiscountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(protected DiscountService $discountService) {}

    public function index(Request $request): View
    {
        $cart = $this->currentCart($request)->load('items.product.images');

        return view('cart.index', compact('cart'));
    }

    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $wantsJson = $request->expectsJson() || $request->ajax();

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
        if ($this->inventoryEnabled() && $newQuantity > $product->stock) {
            if ($wantsJson) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Stok tidak mencukupi untuk jumlah yang diminta.',
                ], 422);
            }
            return back()->with('error', 'Stok tidak mencukupi untuk jumlah yang diminta.');
        }

        $unitPrice = $this->discountService->applyProductDiscount($product) ?? $product->price;

        $item->fill([
            'product_name' => $product->name,
            'product_sku' => $product->sku,
            'quantity' => $newQuantity,
            'unit_price' => $unitPrice,
            'line_total' => $newQuantity * $unitPrice,
        ]);
        $item->save();

        $this->syncCartTotals($cart);

        if ($wantsJson) {
            return response()->json([
                'ok' => true,
                'message' => "{$product->name} added to cart.",
                'added_text' => 'Added ✓',
                'cart_count' => $cart->items()->count(),
            ]);
        }

        return back()
            ->with('status', "{$product->name} added to cart.")
            ->with('cart_added', 'Added ✓');
    }

    public function update(Request $request, CartItem $cartItem): RedirectResponse
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cart = $this->currentCart($request);

        abort_unless($cartItem->cart_id === $cart->id, 404);

        $product = $cartItem->product;
        if ($this->inventoryEnabled() && $product && $validated['quantity'] > $product->stock) {
            return back()->with('error', 'Stok tidak mencukupi untuk jumlah yang diminta.');
        }
        $unitPrice = $product
            ? ($this->discountService->applyProductDiscount($product) ?? $product->price)
            : $cartItem->unit_price;

        $cartItem->update([
            'quantity' => $validated['quantity'],
            'unit_price' => $unitPrice,
            'line_total' => $validated['quantity'] * $unitPrice,
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
        $sessionId = $request->session()->getId();

        if ($request->user()) {
            $sessionCart = Cart::query()
                ->where('session_id', $sessionId)
                ->where(function ($q) use ($request) {
                    $q->whereNull('user_id')
                        ->orWhere('user_id', $request->user()->id);
                })
                ->first();

            if ($sessionCart) {
                if (! $sessionCart->user_id) {
                    $sessionCart->update(['user_id' => $request->user()->id]);
                }

                return $sessionCart;
            }

            $cart = Cart::query()->firstOrCreate(
                ['user_id' => $request->user()->id],
                ['session_id' => $sessionId, 'currency' => 'IDR'],
            );

            if ($cart->session_id !== $sessionId) {
                $cart->update(['session_id' => $sessionId]);
            }

            return $cart;
        }

        return Cart::query()->firstOrCreate(
            ['session_id' => $sessionId, 'user_id' => null],
            ['currency' => 'IDR'],
        );
    }

    protected function syncCartTotals(Cart $cart): void
    {
        $cart->loadMissing('items.product');

        foreach ($cart->items as $item) {
            if (! $item->product) {
                continue;
            }

            $unitPrice = $this->discountService->applyProductDiscount($item->product) ?? $item->product->price;

            if ((float) $item->unit_price !== (float) $unitPrice) {
                $item->update([
                    'unit_price' => $unitPrice,
                    'line_total' => $item->quantity * $unitPrice,
                ]);
            } else {
                // Keep line_total consistent even if quantity changed elsewhere
                $expectedLineTotal = $item->quantity * $unitPrice;
                if ((float) $item->line_total !== (float) $expectedLineTotal) {
                    $item->update(['line_total' => $expectedLineTotal]);
                }
            }
        }

        $subtotal = $cart->items()->sum('line_total');

        $cart->update([
            'subtotal' => $subtotal,
            'discount_total' => 0,
            'total' => $subtotal,
        ]);
    }

    private function inventoryEnabled(): bool
    {
        return Setting::boolOf('inventory_enabled', true);
    }
}
