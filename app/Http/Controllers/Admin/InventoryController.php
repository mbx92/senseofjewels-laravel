<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\InventoryAdjustmentRequest;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class InventoryController extends Controller
{
    public function index(): View
    {
        $logs = InventoryLog::query()
            ->with(['product', 'user'])
            ->latest()
            ->paginate(30);

        $products = Product::query()->where('is_active', true)->orderBy('name')->get();
        $inventoryEnabled = Setting::boolOf('inventory_enabled', true);

        return view('admin.inventory.index', compact('logs', 'products', 'inventoryEnabled'));
    }

    public function adjust(InventoryAdjustmentRequest $request): RedirectResponse
    {
        if (! Setting::boolOf('inventory_enabled', true)) {
            return redirect()->route('admin.inventory.index')
                ->with('error', 'Inventory nonaktif. Penyesuaian stok tidak dapat dilakukan.');
        }

        $product = Product::findOrFail($request->product_id);
        $before  = $product->stock;

        $change = match ($request->type) {
            'in'         => $request->quantity,
            'out'        => -$request->quantity,
            'adjustment' => $request->quantity - $before, // set to exact value
            default      => 0,
        };

        $after = max(0, $before + $change);

        $product->update(['stock' => $after]);

        InventoryLog::query()->create([
            'product_id'   => $product->id,
            'user_id'      => auth()->id(),
            'type'         => $request->type,
            'quantity'     => abs($change),
            'stock_before' => $before,
            'stock_after'  => $after,
            'note'         => $request->note,
        ]);

        return redirect()->route('admin.inventory.index')
            ->with('success', "Stok {$product->name} berhasil diperbarui ({$before} → {$after}).");
    }
}
