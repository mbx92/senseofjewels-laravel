<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InventoryAdjustmentRequest;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InventoryController extends Controller
{
    public function index(): Response
    {
        $logs = InventoryLog::query()
            ->with(['product', 'user'])
            ->latest()
            ->paginate(30)
            ->through(function (InventoryLog $log) {
                return [
                    'id' => $log->id,
                    'product_name' => $log->product?->name,
                    'user_name' => $log->user?->name,
                    'type' => $log->type,
                    'quantity' => $log->quantity,
                    'stock_before' => $log->stock_before,
                    'stock_after' => $log->stock_after,
                    'note' => $log->note,
                    'created_at' => $log->created_at?->format('Y-m-d H:i'),
                ];
            });

        $products = Product::query()->where('is_active', true)->orderBy('name')->get(['id', 'name', 'sku', 'stock']);

        return Inertia::render('Admin/Inventory/Index', [
            'logs' => $logs,
            'products' => $products,
            'inventoryEnabled' => Setting::boolOf('inventory_enabled', true),
        ]);
    }

    public function adjust(InventoryAdjustmentRequest $request): RedirectResponse
    {
        if (! Setting::boolOf('inventory_enabled', true)) {
            return redirect()->route('admin.inventory.index')
                ->with('error', 'Inventory nonaktif. Penyesuaian stok tidak dapat dilakukan.');
        }

        $product = Product::findOrFail($request->product_id);
        $before = $product->stock;

        $change = match ($request->type) {
            'in' => $request->quantity,
            'out' => -$request->quantity,
            'adjustment' => $request->quantity - $before,
            default => 0,
        };

        $after = max(0, $before + $change);

        $product->update(['stock' => $after]);

        InventoryLog::query()->create([
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'type' => $request->type,
            'quantity' => abs($change),
            'stock_before' => $before,
            'stock_after' => $after,
            'note' => $request->note,
        ]);

        return redirect()->route('admin.inventory.index')
            ->with('success', "Stok {$product->name} berhasil diperbarui ({$before} → {$after}).");
    }
}
