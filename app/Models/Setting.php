<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'group',
        'key',
        'value',
        'type',
    ];

    public static function valueOf(string $key, ?string $default = null): ?string
    {
        return static::query()
            ->where('key', $key)
            ->value('value') ?? $default;
    }

    public static function boolOf(string $key, bool $default = false): bool
    {
        $value = static::valueOf($key);
        if ($value === null) {
            return $default;
        }

        return in_array(strtolower((string) $value), ['1', 'true', 'on', 'yes'], true);
    }

    public static function cartEnabled(): bool
    {
        return static::boolOf('cart_enabled', true);
    }

    public static function whatsappNumber(): ?string
    {
        $number = static::valueOf('contact_whatsapp')
            ?: static::valueOf('whatsapp_number');

        $digits = preg_replace('/\D+/', '', (string) $number);

        return $digits !== '' ? $digits : null;
    }

    public static function whatsappUrl(?string $message = null): ?string
    {
        $number = static::whatsappNumber();

        if (! $number) {
            return null;
        }

        return 'https://wa.me/' . $number . ($message ? '?text=' . rawurlencode($message) : '');
    }
}
