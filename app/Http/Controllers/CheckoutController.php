<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Payment;
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

    public function index(Request $request): View
    {
        $cart = $this->cartService->getCart();

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Keranjang Anda kosong.');
        }

        return view('checkout.index', compact('cart'));
    }

    public function applyVoucher(Request $request): JsonResponse
    {
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

        // Create payment record for Midtrans
        Payment::query()->firstOrCreate(
            ['order_id' => $order->id],
            [
                'provider' => 'midtrans',
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
            ->with('status', 'Order berhasil dibuat! Silakan selesaikan pembayaran.');
    }
}
