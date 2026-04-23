<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContactSettingRequest;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    private array $contactKeys = [
        'contact_address',
        'contact_phone',
        'contact_email',
        'contact_maps_embed',
        'contact_whatsapp',
    ];

    private array $allSettingKeys = [
        // General
        'site_name', 'site_tagline', 'currency', 'weight_unit', 'maintenance_mode',
        // Contact
        'contact_address', 'contact_phone', 'contact_email', 'contact_maps_embed', 'contact_whatsapp',
        // Social
        'social_instagram', 'social_facebook', 'social_twitter', 'social_youtube', 'whatsapp_number',
        // SEO
        'seo_title', 'seo_description',
        // Commerce
        'shop_currency_symbol', 'free_shipping_threshold', 'tax_rate',
        // Theme Colors
        'theme_primary', 'theme_secondary', 'theme_accent', 'theme_neutral',
        'theme_base_100', 'theme_base_200', 'theme_base_300', 'theme_base_content',
        'theme_neutral_content',
    ];

    public function index(): View
    {
        $settings = Setting::query()
            ->whereIn('key', $this->allSettingKeys)
            ->pluck('value', 'key');

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'site_name'              => ['nullable', 'string', 'max:255'],
            'site_tagline'           => ['nullable', 'string', 'max:255'],
            'currency'               => ['nullable', 'string', 'max:10'],
            'weight_unit'            => ['nullable', 'string', 'max:10'],
            'maintenance_mode'       => ['boolean'],
            'contact_address'        => ['nullable', 'string'],
            'contact_phone'          => ['nullable', 'string', 'max:30'],
            'contact_email'          => ['nullable', 'email', 'max:255'],
            'contact_maps_embed'     => ['nullable', 'string'],
            'contact_whatsapp'       => ['nullable', 'string', 'max:30'],
            'social_instagram'       => ['nullable', 'url', 'max:255'],
            'social_facebook'        => ['nullable', 'url', 'max:255'],
            'social_twitter'         => ['nullable', 'url', 'max:255'],
            'social_youtube'         => ['nullable', 'url', 'max:255'],
            'whatsapp_number'        => ['nullable', 'string', 'max:30'],
            'seo_title'              => ['nullable', 'string', 'max:255'],
            'seo_description'        => ['nullable', 'string', 'max:500'],
            'shop_currency_symbol'   => ['nullable', 'string', 'max:10'],
            'free_shipping_threshold'=> ['nullable', 'numeric', 'min:0'],
            'tax_rate'               => ['nullable', 'numeric', 'min:0', 'max:100'],
            // Theme colors — must be valid hex
            'theme_primary'          => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme_secondary'        => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme_accent'           => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme_neutral'          => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme_base_100'         => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme_base_200'         => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme_base_300'         => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme_base_content'     => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'theme_neutral_content'  => ['nullable', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $groups = [
            'site_name' => 'general', 'site_tagline' => 'general',
            'currency' => 'general', 'weight_unit' => 'general', 'maintenance_mode' => 'general',
            'contact_address' => 'contact', 'contact_phone' => 'contact',
            'contact_email' => 'contact', 'contact_maps_embed' => 'contact', 'contact_whatsapp' => 'contact',
            'social_instagram' => 'social', 'social_facebook' => 'social',
            'social_twitter' => 'social', 'social_youtube' => 'social', 'whatsapp_number' => 'social',
            'seo_title' => 'seo', 'seo_description' => 'seo',
            'shop_currency_symbol' => 'commerce', 'free_shipping_threshold' => 'commerce', 'tax_rate' => 'commerce',
            'theme_primary' => 'colors', 'theme_secondary' => 'colors', 'theme_accent' => 'colors',
            'theme_neutral' => 'colors', 'theme_base_100' => 'colors', 'theme_base_200' => 'colors',
            'theme_base_300' => 'colors', 'theme_base_content' => 'colors', 'theme_neutral_content' => 'colors',
        ];

        foreach ($this->allSettingKeys as $key) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                [
                    'value' => $request->boolean('maintenance_mode') && $key === 'maintenance_mode'
                        ? '1'
                        : ($validated[$key] ?? ''),
                    'group' => $groups[$key] ?? 'general',
                    'type'  => 'text',
                ],
            );
        }

        // Sync Laravel's maintenance-mode file with the setting value
        $downFile = storage_path('framework/down');
        if ($request->boolean('maintenance_mode')) {
            if (! file_exists($downFile)) {
                file_put_contents($downFile, json_encode([
                    'secret'   => null,
                    'status'   => 503,
                    'template' => null,
                    'retry'    => null,
                    'refresh'  => null,
                    'redirect' => null,
                    'message'  => '',
                ]));
            }
        } else {
            if (file_exists($downFile)) {
                unlink($downFile);
            }
        }

        return redirect()->route('admin.settings.index')
            ->with('success', 'Settings berhasil disimpan.');
    }

    // Legacy contact settings route kept for backward-compat
    public function contact(): View
    {
        $settings = Setting::query()
            ->whereIn('key', $this->contactKeys)
            ->pluck('value', 'key');

        return view('admin.settings.contact', compact('settings'));
    }

    public function updateContact(ContactSettingRequest $request): RedirectResponse
    {
        foreach ($this->contactKeys as $key) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $request->input($key, ''), 'group' => 'contact', 'type' => 'text'],
            );
        }

        return redirect()->route('admin.contact-settings')
            ->with('success', 'Informasi kontak berhasil diperbarui.');
    }
}
