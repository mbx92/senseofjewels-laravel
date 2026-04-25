<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'voucher_id',
        'order_number',
        'status',
        'fulfillment_status',
        'payment_status',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'billing_address',
        'notes',
        'subtotal',
        'discount_total',
        'shipping_total',
        'tax_total',
        'total',
        'currency',
        'placed_at',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'billing_address' => 'array',
            'placed_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function getFulfillmentStatusAttribute($value): string
    {
        return match ($this->status) {
            'pending' => 'unfulfilled',
            'processing', 'shipped' => 'processing',
            'delivered', 'completed' => 'fulfilled',
            'cancelled' => 'cancelled',
            default => (string) ($value ?? 'unfulfilled'),
        };
    }

    protected static function booted(): void
    {
        static::saving(function (Order $order): void {
            if (! $order->isDirty('status')) {
                return;
            }

            $order->fulfillment_status = match ($order->status) {
                'pending' => 'unfulfilled',
                'processing', 'shipped' => 'processing',
                'delivered', 'completed' => 'fulfilled',
                'cancelled' => 'cancelled',
                default => $order->fulfillment_status,
            };
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function voucher(): BelongsTo
    {
        return $this->belongsTo(Voucher::class);
    }

    public function voucherUsages(): HasMany
    {
        return $this->hasMany(VoucherUsage::class);
    }
}
