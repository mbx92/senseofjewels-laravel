<?php

namespace App\Support;

use App\Models\Cart;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

final class AppLayoutShared
{
    /**
     * Theme color overrides from settings (same keys as View::composer on layouts).
     *
     * @return array<string, string|null>
     */
    public static function themeColors(): array
    {
        static $themeColors = null;

        if ($themeColors === null) {
            try {
                if (Schema::hasTable('settings')) {
                    $themeColors = Setting::query()
                        ->where('group', 'colors')
                        ->pluck('value', 'key')
                        ->toArray();
                }
            } catch (\Throwable) {
                //
            }
            $themeColors = $themeColors ?? [];
        }

        return $themeColors;
    }

    /**
     * @return array{cartEnabled: bool, navCartCount: int, whatsappUrl: ?string, siteName: string, siteLogo: ?string}
     */
    public static function navigation(Request $request): array
    {
        try {
            $cartEnabled = Setting::cartEnabled();
            $whatsappUrl = Setting::whatsappUrl();
            $sessionId = $request->session()->getId();
            $count = 0;
            if ($cartEnabled && $sessionId && Schema::hasTable('carts')) {
                $cartQuery = Cart::query()->withCount('items');
                if (Auth::check()) {
                    $cartQuery->where('user_id', Auth::id());
                } else {
                    $cartQuery->where('session_id', $sessionId)->whereNull('user_id');
                }
                $cart = $cartQuery->first();
                $count = $cart?->items_count ?? 0;
            }
        } catch (\Throwable) {
            $cartEnabled = true;
            $whatsappUrl = null;
            $count = 0;
        }

        $siteName = Setting::valueOf('site_name', config('app.name', 'Sense of Jewels')) ?: config('app.name', 'Sense of Jewels');
        $siteLogo = Setting::valueOf('site_logo');

        return [
            'cartEnabled' => $cartEnabled,
            'navCartCount' => $count,
            'whatsappUrl' => $whatsappUrl,
            'siteName' => $siteName,
            'siteLogo' => $siteLogo,
        ];
    }

    /**
     * Translated strings for the public site shell (matches layouts/app.blade.php).
     *
     * @return array<string, string>
     */
    public static function publicLayoutStrings(): array
    {
        return [
            'announcement_left' => __('Sense of Jewels Studio · Seminyak, Bali'),
            'announcement_right_shipping' => __('Free shipping to all of Indonesia'),
            'announcement_right_whatsapp' => __('Order via WhatsApp'),
            'nav_home' => __('Home'),
            'nav_shop' => __('Shop'),
            'nav_cart' => __('Cart'),
            'nav_order_wa' => __('Order WA'),
            'nav_story' => __('Story'),
            'nav_contact' => __('Contact'),
            'footer_nav_title' => __('Navigation'),
            'footer_collection' => __('Collection'),
            'footer_brand_story' => __('Brand Story'),
            'footer_contact_us' => __('Contact Us'),
            'footer_social_title' => __('Social'),
            'footer_instagram' => __('Instagram'),
            'footer_whatsapp' => __('WhatsApp'),
            'footer_email' => __('Email'),
            'footer_rights' => __('All rights reserved by Sense of Jewels'),
            'locale_id' => __('Bahasa ID'),
            'locale_en' => 'English',
        ];
    }
}
