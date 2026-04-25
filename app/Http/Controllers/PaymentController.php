<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected MidtransService $midtrans,
    ) {}

    private function midtransConfigured(): bool
    {
        return ! empty(config('midtrans.server_key')) && ! empty(config('midtrans.client_key'));
    }

    /** Midtrans active jika toggle ON dan env key tersedia. */
    private function midtransActive(): bool
    {
        $enabled = Setting::boolOf('midtrans_enabled', true);
        return $enabled && $this->midtransConfigured();
    }

    /**
     * GET /payment/{orderNumber}
     * Tampilkan halaman pembayaran Snap atau simulator lokal.
     */
    public function show(Request $request, string $orderNumber): View|RedirectResponse
    {
        $order = Order::query()
            ->where('order_number', $orderNumber)
            ->with(['items', 'payment'])
            ->firstOrFail();

        if ($order->user_id && $order->user_id !== $request->user()?->id) {
            abort(403);
        }

        // Jika sudah lunas, langsung ke halaman sukses
        if (in_array($order->payment?->status, ['paid', 'settlement'])) {
            return redirect()->route('payment.success', ['order_id' => $orderNumber]);
        }

        // Manual payment fallback (integration OFF / config missing)
        if (! $this->midtransActive()) {
            return view('payment.manual', compact('order'));
        }

        return view('payment.show', compact('order'));
    }

    /**
     * POST /payment/token
     * Return a Snap token for the given order (JSON).
     */
    public function token(Request $request): JsonResponse
    {
        if (! $this->midtransActive()) {
            return response()->json(['error' => 'Midtrans tidak aktif.'], 422);
        }

        $request->validate(['order_id' => ['required', 'exists:orders,id']]);

        $order = Order::query()
            ->where('id', $request->order_id)
            ->with(['items', 'payment'])
            ->firstOrFail();

        // Authorise: only the owner or a guest whose order is in session
        if ($order->user_id && $order->user_id !== $request->user()?->id) {
            abort(403);
        }

        // Reuse existing snap token if not yet paid
        if ($order->payment?->snap_token && $order->payment?->status === 'pending') {
            return response()->json(['token' => $order->payment->snap_token]);
        }

        try {
            $token = $this->midtrans->createSnapToken($order);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Gagal membuat token pembayaran.'], 500);
        }

        return response()->json(['token' => $token]);
    }

    /**
     * POST /payment/notification
     * Midtrans webhook — excluded from CSRF.
     */
    public function notification(Request $request): JsonResponse
    {
        if (! $this->midtransActive()) {
            return response()->json(['message' => 'Midtrans disabled.'], 202);
        }

        try {
            $notification = $this->midtrans->verifyNotification();
            $payload = (array) $notification;
            $this->midtrans->handleWebhook($payload);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Error processing notification.'], 500);
        }

        return response()->json(['message' => 'OK']);
    }

    /**
     * GET /payment/success
     */
    public function success(Request $request): View
    {
        $order = $this->resolveOrderFromRequest($request);
        return view('payment.success', compact('order'));
    }

    /**
     * GET /payment/pending
     */
    public function pending(Request $request): View
    {
        $order = $this->resolveOrderFromRequest($request);
        return view('payment.pending', compact('order'));
    }

    /**
     * GET /payment/failed
     */
    public function failed(Request $request): View
    {
        $order = $this->resolveOrderFromRequest($request);
        return view('payment.failed', compact('order'));
    }

    // ---------------------------------------------------------------

    private function resolveOrderFromRequest(Request $request): ?Order
    {
        $orderNumber = $request->query('order_id') ?? session('last_order_number');

        if (! $orderNumber) {
            return null;
        }

        return Order::query()
            ->where('order_number', $orderNumber)
            ->with('payment')
            ->first();
    }
}
