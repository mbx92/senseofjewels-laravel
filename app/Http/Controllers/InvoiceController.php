<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class InvoiceController extends Controller
{
    public function download(string $orderNumber): Response
    {
        $order = Order::with(['items', 'payment', 'user', 'voucher'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        // Authorise: owner OR admin/editor/super-admin
        $user = auth()->user();
        if (! $user) {
            abort(403);
        }

        $isAdmin = $user->hasAnyRole(['super-admin', 'admin', 'editor']);
        if (! $isAdmin && $order->user_id !== $user->id) {
            abort(403);
        }

        $siteName   = Setting::valueOf('site_name', config('app.name'));
        $logoPath   = Setting::valueOf('site_logo');
        $logoBase64 = null;

        if ($logoPath) {
            // Strip leading slash so storage_path() works correctly
            $logoPath = ltrim($logoPath, '/');
            // Resolve: could be storage/... or public/storage/...
            $candidates = [
                public_path($logoPath),
                storage_path('app/public/' . ltrim(str_replace('storage/', '', $logoPath), '/')),
                base_path($logoPath),
            ];
            foreach ($candidates as $candidate) {
                if (file_exists($candidate)) {
                    $mime = mime_content_type($candidate);
                    $data = base64_encode(file_get_contents($candidate));
                    $logoBase64 = "data:{$mime};base64,{$data}";
                    break;
                }
            }
        }

        $primaryColor = Setting::valueOf('theme_primary', '#c8a96e');

        $pdf = Pdf::loadView('invoices.invoice', [
            'order'        => $order,
            'siteName'     => $siteName,
            'logoBase64'   => $logoBase64,
            'primaryColor' => $primaryColor,
        ])->setPaper('a4', 'portrait');

        $filename = 'invoice-' . $order->order_number . '-' . now()->format('YmdHis') . '.pdf';

        return $pdf->download($filename, [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ]);
    }
}
