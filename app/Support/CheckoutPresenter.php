<?php

namespace App\Support;

use App\Models\Cart;
use App\Models\CartItem;
use App\Services\CurrencyService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

final class CheckoutPresenter
{
    /**
     * @return array<string, mixed>
     */
    public static function index(
        Request $request,
        Cart $cart,
        CurrencyService $currency,
        ?Authenticatable $user,
        bool $midtransActive,
    ): array {
        $cart->loadMissing('items.product');

        $items = $cart->items->map(function (CartItem $item) use ($currency) {
            return [
                'product_name' => $item->product_name,
                'quantity' => (int) $item->quantity,
                'line_total_formatted' => $currency->format((int) $item->line_total),
            ];
        })->values()->all();

        $productDiscountTotal = (int) $cart->items->sum(function (CartItem $item) {
            $basePrice = $item->product?->price ?? $item->unit_price;

            return max(0, ((float) $basePrice - (float) $item->unit_price) * (int) $item->quantity);
        });

        $discountTotal = (int) $cart->discount_total;
        $totalSaved = $productDiscountTotal + $discountTotal;

        return [
            'items' => $items,
            'subtotal_formatted' => $currency->format((int) $cart->subtotal),
            'product_discount_total' => $productDiscountTotal,
            'product_discount_total_formatted' => $currency->format($productDiscountTotal),
            'discount_total' => $discountTotal,
            'discount_total_formatted' => $currency->format($discountTotal),
            'total_saved_formatted' => $currency->format($totalSaved),
            'total_formatted' => $currency->format((int) $cart->total),
            'show_product_discount' => $productDiscountTotal > 0,
            'show_voucher_discount' => $discountTotal > 0,
            'show_total_saved' => $totalSaved > 0,
            'midtrans_active' => $midtransActive,
            'defaults' => [
                'customer_name' => old('customer_name', $user?->name ?? ''),
                'customer_email' => old('customer_email', $user?->email ?? ''),
                'customer_phone' => old('customer_phone', $user?->phone ?? ''),
                'shipping_address' => [
                    'line_1' => old('shipping_address.line_1', $user?->address_line_1 ?? ''),
                    'city' => old('shipping_address.city', $user?->city ?? ''),
                    'province' => old('shipping_address.province', $user?->province ?? ''),
                    'postal_code' => old('shipping_address.postal_code', $user?->postal_code ?? ''),
                    'country' => old('shipping_address.country', $user?->country ?? 'Indonesia'),
                ],
                'notes' => old('notes', ''),
            ],
            'translations' => self::translations(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function translations(): array
    {
        return [
            'title' => __('Checkout'),
            'subtitle' => __('Complete your shipping details, then finish payment with Midtrans.'),
            'empty_cart' => __('Your cart is empty.'),
            'back_to_shop' => __('Back to Shop'),
            'step_data' => __('Data'),
            'step_confirm' => __('Confirmation'),
            'step_payment' => __('Payment'),
            'customer_heading' => __('Customer details'),
            'full_name' => __('Full name'),
            'email' => __('Email'),
            'phone' => __('Phone'),
            'phone_placeholder' => __('+62 8xx xxxx xxxx'),
            'shipping_heading' => __('Shipping address'),
            'address_line_1' => __('Street address'),
            'address_placeholder' => __('Street, building, unit'),
            'city' => __('City'),
            'province' => __('Province'),
            'postal_code' => __('Postal code'),
            'country' => __('Country'),
            'notes_heading' => __('Order notes'),
            'notes_placeholder' => __('Delivery instructions (optional)'),
            'summary_heading' => __('Order summary'),
            'qty_label' => __('Qty'),
            'subtotal' => __('Subtotal'),
            'product_discount' => __('Product discount'),
            'voucher_discount' => __('Voucher discount'),
            'total_saved' => __('Total saved'),
            'total' => __('Total'),
            'voucher_code' => __('Voucher code'),
            'voucher_placeholder' => __('Enter voucher code'),
            'apply' => __('Apply'),
            'voucher_payable' => __('Amount due'),
            'payment_secured' => __('Payments are processed securely by Midtrans.'),
            'payment_redirect' => __('You will continue to the payment step after confirmation.'),
            'payment_manual' => __('You will see payment instructions after confirming your order.'),
            'submit' => __('Confirm & Continue to Payment'),
            'back_to_cart' => __('Back to cart'),
        ];
    }
}
