@extends('layouts.admin')

@section('content')
<div class="max-w-5xl space-y-8" x-data="{ tab: 'general' }">

    {{-- Heading --}}
    <div class="space-y-1">
        <p class="text-[10px] uppercase tracking-[0.25em] text-primary">System</p>
        <h1 class="display-font text-4xl text-base-content font-normal">Settings</h1>
    </div>

    <div class="rounded-box border border-base-300 bg-base-100 p-5 shadow-sm md:p-6">
    {{-- Tab bar --}}
    <div class="mb-8 flex flex-wrap items-center gap-0 border-b border-base-300">
        @foreach(['general' => 'General', 'contact' => 'Contact', 'social' => 'Social', 'seo' => 'SEO', 'commerce' => 'Commerce', 'colors' => 'Colors'] as $key => $label)
            <button @click="tab = '{{ $key }}'" type="button"
                    class="px-5 py-3 text-[10px] uppercase tracking-widest border-b-2 -mb-px transition-colors"
                    :class="tab === '{{ $key }}' ? 'border-primary text-primary' : 'border-transparent text-base-content/50 hover:text-base-content'">
                {{ $label }}
            </button>
        @endforeach
    </div>

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-2">
        @csrf @method('PUT')

        {{-- GENERAL --}}
        <div x-show="tab === 'general'" class="space-y-6">
            @include('admin.components.media-picker', [
                'inputName'    => 'site_logo',
                'inputId'      => 'site_logo',
                'currentValue' => old('site_logo', $settings['site_logo'] ?? ''),
                'label'        => 'Site Logo',
            ])

            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Site Name</label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? '') }}"
                           class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Site Tagline</label>
                    <input type="text" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline'] ?? '') }}"
                           class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Currency</label>
                    <input type="text" name="currency" value="{{ old('currency', $settings['currency'] ?? 'IDR') }}"
                           class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Weight Unit</label>
                    <input type="text" name="weight_unit" value="{{ old('weight_unit', $settings['weight_unit'] ?? 'gram') }}"
                           class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>
            <div class="flex items-center gap-4 pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="maintenance_mode" value="0">
                    <input type="checkbox" name="maintenance_mode" value="1"
                           class="toggle toggle-sm toggle-primary"
                           {{ ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' }}>
                    <span class="text-sm text-base-content/70">Maintenance Mode</span>
                </label>
                <span class="text-[11px] text-base-content/40">Site will show a maintenance page to visitors</span>
            </div>
        </div>

        {{-- CONTACT --}}
        <div x-show="tab === 'contact'" class="space-y-6">
            <div>
                <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Address</label>
                <textarea name="contact_address" rows="3"
                          class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors resize-none">{{ old('contact_address', $settings['contact_address'] ?? '') }}</textarea>
            </div>
            <div class="grid gap-6 sm:grid-cols-2">
                <div>
                    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Phone</label>
                    <input type="text" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone'] ?? '') }}"
                           class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Email</label>
                    <input type="email" name="contact_email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}"
                           class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">WhatsApp Number</label>
                    <input type="text" name="contact_whatsapp" value="{{ old('contact_whatsapp', $settings['contact_whatsapp'] ?? '') }}"
                           placeholder="+62 812 3456 7890"
                           class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>
            <div>
                <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Google Maps Embed URL</label>
                <input type="text" name="contact_maps_embed" value="{{ old('contact_maps_embed', $settings['contact_maps_embed'] ?? '') }}"
                       class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors">
            </div>
        </div>

        {{-- SOCIAL --}}
        <div x-show="tab === 'social'" class="space-y-6">
            <div class="grid gap-6 sm:grid-cols-2">
                @foreach(['social_instagram' => 'Instagram URL', 'social_facebook' => 'Facebook URL', 'social_twitter' => 'Twitter / X URL', 'social_youtube' => 'YouTube URL', 'whatsapp_number' => 'WhatsApp Number (intl format)'] as $key => $label)
                <div class="{{ $key === 'whatsapp_number' ? 'sm:col-span-2' : '' }}">
                    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">{{ $label }}</label>
                    <input type="text" name="{{ $key }}" value="{{ old($key, $settings[$key] ?? '') }}"
                           class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors">
                </div>
                @endforeach
            </div>
            <div class="flex items-center gap-4 pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="instagram_feed_enabled" value="0">
                    <input type="checkbox" name="instagram_feed_enabled" value="1"
                           class="toggle toggle-sm toggle-primary"
                           {{ ($settings['instagram_feed_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                    <span class="text-sm text-base-content/70">Tampilkan Instagram Feed di Landing</span>
                </label>
                <span class="text-[11px] text-base-content/40">Matikan jika ingin menyembunyikan blok feed Instagram.</span>
            </div>
        </div>

        {{-- SEO --}}
        <div x-show="tab === 'seo'" class="space-y-6">
            <div>
                <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Default SEO Title</label>
                <input type="text" name="seo_title" value="{{ old('seo_title', $settings['seo_title'] ?? '') }}"
                       class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors">
                <p class="text-[10px] text-base-content/40 mt-1">Recommended: 50–60 characters</p>
            </div>
            <div>
                <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Meta Description</label>
                <textarea name="seo_description" rows="4"
                          class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors resize-none">{{ old('seo_description', $settings['seo_description'] ?? '') }}</textarea>
                <p class="text-[10px] text-base-content/40 mt-1">Recommended: 150–160 characters</p>
            </div>
        </div>

        {{-- COMMERCE --}}
        <div x-show="tab === 'commerce'" class="space-y-6">
            <div class="grid gap-6 sm:grid-cols-3">
                <div>
                    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Currency Symbol</label>
                    <input type="text" name="shop_currency_symbol" value="{{ old('shop_currency_symbol', $settings['shop_currency_symbol'] ?? 'Rp') }}"
                           class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors">
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Free Shipping Threshold</label>
                    <input type="number" name="free_shipping_threshold" value="{{ old('free_shipping_threshold', $settings['free_shipping_threshold'] ?? '') }}"
                           min="0" step="1000"
                           class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors">
                    <p class="text-[10px] text-base-content/40 mt-1">0 = disabled</p>
                </div>
                <div>
                    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">Tax Rate (%)</label>
                    <input type="number" name="tax_rate" value="{{ old('tax_rate', $settings['tax_rate'] ?? '0') }}"
                           min="0" max="100" step="0.01"
                           class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors">
                </div>
            </div>
            <div class="flex items-center gap-4 pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="inventory_enabled" value="0">
                    <input type="checkbox" name="inventory_enabled" value="1"
                           class="toggle toggle-sm toggle-primary"
                           {{ ($settings['inventory_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                    <span class="text-sm text-base-content/70">Inventory Active</span>
                </label>
                <span class="text-[11px] text-base-content/40">Jika nonaktif, stok tidak membatasi pembelian dan tidak berkurang otomatis.</span>
            </div>
            <div class="flex items-center gap-4 pt-2">
                <label class="flex items-center gap-3 cursor-pointer">
                    <input type="hidden" name="cart_enabled" value="0">
                    <input type="checkbox" name="cart_enabled" value="1"
                           class="toggle toggle-sm toggle-primary"
                           {{ ($settings['cart_enabled'] ?? '1') === '1' ? 'checked' : '' }}>
                    <span class="text-sm text-base-content/70">Cart & Checkout Active</span>
                </label>
                <span class="text-[11px] text-base-content/40">Jika nonaktif, shop menjadi katalog dan tombol produk diarahkan ke WhatsApp.</span>
            </div>
        </div>

        {{-- COLORS --}}
        @php
            $colorDefaults = [
                'theme_primary'         => ['label' => 'Primary (Gold)',        'default' => '#bfa054'],
                'theme_secondary'       => ['label' => 'Secondary (Sage)',       'default' => '#8faf9f'],
                'theme_accent'          => ['label' => 'Accent (Terracotta)',    'default' => '#c4956a'],
                'theme_neutral'         => ['label' => 'Neutral (Dark Brown)',   'default' => '#3d2b1f'],
                'theme_neutral_content' => ['label' => 'Neutral Content',        'default' => '#ebdfd5'],
                'theme_base_100'        => ['label' => 'Base 100 (Background)',  'default' => '#faf6ef'],
                'theme_base_200'        => ['label' => 'Base 200',               'default' => '#f0e8da'],
                'theme_base_300'        => ['label' => 'Base 300',               'default' => '#e8d5b7'],
                'theme_base_content'    => ['label' => 'Base Content (Text)',    'default' => '#2c1a0e'],
            ];
        @endphp
        <div x-show="tab === 'colors'" class="space-y-6">
            <p class="text-xs text-base-content/50">Ubah palet warna tema <em>bali-craft</em>. Perubahan langsung tampil di seluruh situs tanpa perlu rebuild CSS.</p>
            <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($colorDefaults as $key => $meta)
                @php $val = old($key, $settings[$key] ?? $meta['default']); @endphp
                <div x-data="{ color: @js($val) }">
                    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2">{{ $meta['label'] }}</label>
                    <div class="flex items-center gap-3">
                        <input type="color" name="{{ $key }}" x-model="color"
                               class="w-10 h-10 cursor-pointer border border-base-300 bg-transparent p-0.5 rounded-none">
                        <input type="text" :value="color" @input="color = $event.target.value"
                               class="flex-1 border-b border-base-content/20 bg-transparent py-2 text-xs font-mono focus:outline-none focus:border-primary transition-colors"
                               placeholder="{{ $meta['default'] }}"
                               pattern="^#[0-9a-fA-F]{6}$">
                        {{-- Sync color picker with text input --}}
                        <input type="hidden" :name="'{{ $key }}'" :value="color">
                    </div>
                    <p class="text-[9px] text-base-content/30 mt-1">Default: {{ $meta['default'] }}</p>
                </div>
                @endforeach
            </div>
            <div class="bg-base-200 border border-base-300 p-4">
                <p class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-3">Preview</p>
                <div class="flex flex-wrap gap-2">
                    @foreach($colorDefaults as $key => $meta)
                    @php $previewColor = $settings[$key] ?? $meta['default']; @endphp
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded-sm border border-base-content/10" style="background: {{ $previewColor }}"></div>
                        <span class="text-[9px] text-base-content/50">{{ $meta['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Submit --}}
        <div class="pt-4">
            <button type="submit" class="text-[11px] uppercase tracking-widest bg-neutral text-neutral-content px-8 py-3 hover:bg-neutral/80 transition-colors">
                Save Settings
            </button>
        </div>

    </form>

</div>
</div>
@endsection
