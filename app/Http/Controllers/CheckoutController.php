<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Voucher;
use App\Services\CartService;
use App\Services\DiscountService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        protected CartService     $cartService,
        protected OrderService    $orderService,
        protected DiscountService $discountService,
    ) {}

    private function midtransActive(): bool
    {
        $enabled = Setting::boolOf('midtrans_enabled', true);
        $configured = ! empty(config('midtrans.server_key')) && ! empty(config('midtrans.client_key'));

        return $enabled && $configured;
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (! Setting::cartEnabled()) {
            return redirect()->route('shop.index')
                ->with('error', 'Checkout sedang nonaktif. Silakan order produk via WhatsApp.');
        }

        $cart = $this->cartService->getCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang Anda kosong.');
        }

        return view('checkout.index', compact('cart'));
    }

    public function applyVoucher(Request $request): JsonResponse
    {
        if (! Setting::cartEnabled()) {
            return response()->json(['message' => 'Checkout sedang nonaktif.'], 403);
        }

        $request->validate(['code' => ['required', 'string']]);

        $cart   = $this->cartService->getCart();
        $result = $this->discountService->validateVoucher(
            $request->code,
            $request->user()?->id,
            $cart->subtotal,
        );

        if (! $result['valid']) {
            return response()->json(['valid' => false, 'message' => $result['message']], 422);
        }

        session(['checkout_voucher_id' => $result['voucher']->id]);

        return response()->json([
            'valid'           => true,
            'message'         => $result['message'],
            'discount_amount' => $result['discount_amount'],
            'discount_fmt'    => 'Rp ' . number_format($result['discount_amount'], 0, ',', '.'),
            'total'           => $cart->subtotal - $result['discount_amount'],
            'total_fmt'       => 'Rp ' . number_format($cart->subtotal - $result['discount_amount'], 0, ',', '.'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (! Setting::cartEnabled()) {
            return redirect()->route('shop.index')
                ->with('error', 'Checkout sedang nonaktif. Silakan order produk via WhatsApp.');
        }

        $validated = $request->validate([
            'customer_name'                => ['required', 'string', 'max:255'],
            'customer_email'               => ['required', 'email', 'max:255'],
            'customer_phone'               => ['nullable', 'string', 'max:50'],
            'shipping_address.line_1'      => ['required', 'string', 'max:255'],
            'shipping_address.city'        => ['required', 'string', 'max:255'],
            'shipping_address.province'    => ['required', 'string', 'max:255'],
            'shipping_address.postal_code' => ['required', 'string', 'max:20'],
            'shipping_address.country'     => ['nullable', 'string', 'max:100'],
            'voucher_code'                 => ['nullable', 'string', 'max:50'],
            'notes'                        => ['nullable', 'string'],
        ]);

        $cart = $this->cartService->getCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        if (Setting::boolOf('inventory_enabled', true)) {
            $cart->loadMissing('items.product');
            foreach ($cart->items as $item) {
                if (! $item->product) {
                    return redirect()->route('cart.index')->with('error', 'Produk pada keranjang tidak ditemukan.');
                }

                if ($item->quantity > $item->product->stock) {
                    return redirect()->route('cart.index')->with(
                        'error',
                        "Stok {$item->product->name} tersisa {$item->product->stock}. Silakan sesuaikan jumlah."
                    );
                }
            }
        }

        // Resolve voucher
        $voucherId = null;
        if (! empty($validated['voucher_code'])) {
            $voucher = Voucher::query()->where('code', strtoupper($validated['voucher_code']))->first();
            $voucherId = $voucher?->id;
        } elseif (session('checkout_voucher_id')) {
            $voucherId = session('checkout_voucher_id');
        }

        $order = $this->orderService->create(
            userId: $request->user()?->id,
            cart: $cart,
            customerData: [
                'name'  => $validated['customer_name'],
                'email' => $validated['customer_email'],
                'phone' => $validated['customer_phone'] ?? null,
                'notes' => $validated['notes'] ?? null,
            ],
            address: array_merge($validated['shipping_address'], ['country' => $validated['shipping_address']['country'] ?? 'Indonesia']),
            voucherId: $voucherId,
        );

        // Create payment record for active provider
        $paymentProvider = $this->midtransActive() ? 'midtrans' : 'manual';
        Payment::query()->firstOrCreate(
            ['order_id' => $order->id],
            [
                'provider' => $paymentProvider,
                'amount'   => $order->total,
                'currency' => $order->currency ?? 'IDR',
                'status'   => 'pending',
                'payload'  => [],
            ],
        );

        // Clear cart & temp session data
        $this->cartService->clear();
        session()->forget('checkout_voucher_id');
        session(['last_order_number' => $order->order_number]);

        return redirect()->route('payment.show', $order->order_number)
            ->with('status', $this->midtransActive()
                ? 'Order berhasil dibuat! Silakan selesaikan pembayaran.'
                : 'Order berhasil dibuat. Silakan lanjutkan ke instruksi pembayaran manual.');
    }
}
