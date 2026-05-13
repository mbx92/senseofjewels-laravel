<?php

namespace App\Support;

use App\Models\Cart;
use App\Models\CartItem;
use App\Services\CurrencyService;

final class CartPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function index(Cart $cart, CurrencyService $currency, bool $inventoryEnabled): array
    {
        $cart->loadMissing('items.product');

        $items = $cart->items->map(function (CartItem $item) use ($currency, $inventoryEnabled) {
            $product = $item->product;
            $maxStock = ($inventoryEnabled && $product) ? (int) $product->stock : null;

            return [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'product_sku' => $item->product_sku,
                'quantity' => (int) $item->quantity,
                'unit_price_formatted' => $currency->format((int) $item->unit_price),
                'line_total_formatted' => $currency->format((int) $item->line_total),
                'max_stock' => $maxStock,
            ];
        })->values()->all();

        $productDiscountTotal = (int) $cart->items->sum(function (CartItem $item) {
            $basePrice = $item->product?->price ?? $item->unit_price;

            return max(0, ((float) $basePrice - (float) $item->unit_price) * (int) $item->quantity);
        });

        return [
            'items' => $items,
            'is_empty' => $cart->items->isEmpty(),
            'subtotal_formatted' => $currency->format((int) $cart->subtotal),
            'discount_total' => (float) $cart->discount_total,
            'discount_total_formatted' => $currency->format((int) $cart->discount_total),
            'total_formatted' => $currency->format((int) $cart->total),
            'product_discount_total' => $productDiscountTotal,
            'product_discount_total_formatted' => $currency->format($productDiscountTotal),
            'total_saved_formatted' => $currency->format($productDiscountTotal + (int) $cart->discount_total),
            'translations' => self::translations(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function translations(): array
    {
        return [
            'title' => __('Shopping Cart'),
            'subtitle' => __('Review your selected products before proceeding to payment.'),
            'continue_shopping' => __('Continue Shopping'),
            'col_product' => __('Product'),
            'col_qty' => __('Quantity'),
            'col_unit' => __('Unit price'),
            'col_subtotal' => __('Subtotal'),
            'update' => __('Update'),
            'delete' => __('Delete'),
            'empty' => __('Your cart is empty.'),
            'start_shopping' => __('Start Shopping'),
            'summary' => __('Summary'),
            'subtotal' => __('Subtotal'),
            'product_discount' => __('Product discount'),
            'voucher_discount' => __('Voucher discount'),
            'total_saved' => __('Total saved'),
            'total' => __('Total'),
            'checkout' => __('Checkout'),
            'proceed_checkout' => __('Proceed to Checkout'),
        ];
    }
}
