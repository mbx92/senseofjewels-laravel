<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLog;
use App\Services\MidtransService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(
        protected MidtransService $midtrans,
        protected OrderService    $orderService,
    ) {}

    /** Apakah Midtrans sudah dikonfigurasi? */
    private function midtransConfigured(): bool
    {
        return ! empty(config('midtrans.server_key'));
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

        // Jika Midtrans belum dikonfigurasi → tampilkan simulator
        if (! $this->midtransConfigured()) {
            return view('payment.mock', compact('order'));
        }

        return view('payment.show', compact('order'));
    }

    /**
     * POST /payment/mock-simulate
     * Simulator lokal: proses hasil pembayaran tanpa Midtrans.
     * Hanya aktif ketika MIDTRANS_SERVER_KEY kosong.
     */
    public function mockSimulate(Request $request): RedirectResponse
    {
        abort_if($this->midtransConfigured(), 403, 'Mock simulator tidak aktif saat Midtrans sudah dikonfigurasi.');

        $request->validate([
            'order_number' => ['required', 'string'],
            'result'       => ['required', 'in:success,pending,failed'],
        ]);

        $order = Order::query()
            ->where('order_number', $request->order_number)
            ->with('payment')
            ->firstOrFail();

        if ($order->user_id && $order->user_id !== $request->user()?->id) {
            abort(403);
        }

        $payment = $order->payment ?? Payment::query()->firstOrCreate(
            ['order_id' => $order->id],
            ['provider' => 'mock', 'amount' => $order->total, 'currency' => $order->currency ?? 'IDR', 'status' => 'pending', 'payload' => []],
        );

        $now = now();

        match ($request->result) {
            'success' => [
                $payment->update(['status' => 'paid', 'payment_type' => 'mock_transfer', 'paid_at' => $now, 'payload' => ['simulated' => true]]),
                $order->update(['payment_status' => 'paid', 'status' => 'processing', 'paid_at' => $now]),
                $this->orderService->deductStock($order),
            ],
            'pending' => [
                $payment->update(['status' => 'pending', 'payload' => ['simulated' => true]]),
                $order->update(['payment_status' => 'pending']),
            ],
            'failed' => [
                $payment->update(['status' => 'failed', 'payload' => ['simulated' => true]]),
                $order->update(['payment_status' => 'failed', 'status' => 'cancelled']),
            ],
        };

        PaymentLog::query()->create([
            'payment_id' => $payment->id,
            'event'      => 'mock_simulate_' . $request->result,
            'status'     => $payment->status,
            'payload'    => ['result' => $request->result, 'simulated_at' => $now->toIso8601String()],
            'logged_at'  => $now,
        ]);

        $redirectRoute = match ($request->result) {
            'success' => route('payment.success', ['order_id' => $order->order_number]),
            'pending' => route('payment.pending', ['order_id' => $order->order_number]),
            default   => route('payment.failed',  ['order_id' => $order->order_number]),
        };

        return redirect($redirectRoute);
    }

    /**
     * POST /payment/token
     * Return a Snap token for the given order (JSON).
     */
    public function token(Request $request): JsonResponse
    {
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
