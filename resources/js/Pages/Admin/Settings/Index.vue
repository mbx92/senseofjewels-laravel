<script setup>
import SingleMediaPicker from '@/Components/Admin/SingleMediaPicker.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    settings: { type: Object, required: true },
});

const tab = ref('general');

function s(key, fallback = '') {
    return props.settings[key] ?? fallback;
}

function b(key) {
    return s(key) === '1' || s(key) === true;
}

const colorDefaults = {
    theme_primary: { label: 'Primary (Gold)', default: '#bfa054' },
    theme_secondary: { label: 'Secondary (Sage)', default: '#8faf9f' },
    theme_accent: { label: 'Accent (Terracotta)', default: '#c4956a' },
    theme_neutral: { label: 'Neutral (Dark Brown)', default: '#3d2b1f' },
    theme_neutral_content: { label: 'Neutral Content', default: '#ebdfd5' },
    theme_base_100: { label: 'Base 100 (Background)', default: '#faf6ef' },
    theme_base_200: { label: 'Base 200', default: '#f0e8da' },
    theme_base_300: { label: 'Base 300', default: '#e8d5b7' },
    theme_base_content: { label: 'Base Content (Text)', default: '#2c1a0e' },
};

const form = useForm({
    site_name: s('site_name'),
    site_tagline: s('site_tagline'),
    site_logo: s('site_logo'),
    currency: s('currency', 'IDR'),
    weight_unit: s('weight_unit', 'gram'),
    maintenance_mode: b('maintenance_mode'),
    contact_address: s('contact_address'),
    contact_phone: s('contact_phone'),
    contact_email: s('contact_email'),
    contact_maps_embed: s('contact_maps_embed'),
    contact_whatsapp: s('contact_whatsapp'),
    social_instagram: s('social_instagram'),
    social_facebook: s('social_facebook'),
    social_twitter: s('social_twitter'),
    social_youtube: s('social_youtube'),
    whatsapp_number: s('whatsapp_number'),
    instagram_feed_enabled: b('instagram_feed_enabled'),
    seo_title: s('seo_title'),
    seo_description: s('seo_description'),
    shop_currency_symbol: s('shop_currency_symbol', 'Rp'),
    free_shipping_threshold: s('free_shipping_threshold'),
    tax_rate: s('tax_rate', '0'),
    inventory_enabled: b('inventory_enabled'),
    cart_enabled: b('cart_enabled'),
    theme_primary: s('theme_primary', colorDefaults.theme_primary.default),
    theme_secondary: s('theme_secondary', colorDefaults.theme_secondary.default),
    theme_accent: s('theme_accent', colorDefaults.theme_accent.default),
    theme_neutral: s('theme_neutral', colorDefaults.theme_neutral.default),
    theme_neutral_content: s('theme_neutral_content', colorDefaults.theme_neutral_content.default),
    theme_base_100: s('theme_base_100', colorDefaults.theme_base_100.default),
    theme_base_200: s('theme_base_200', colorDefaults.theme_base_200.default),
    theme_base_300: s('theme_base_300', colorDefaults.theme_base_300.default),
    theme_base_content: s('theme_base_content', colorDefaults.theme_base_content.default),
});

const tabs = [
    { id: 'general', label: 'General' },
    { id: 'contact', label: 'Contact' },
    { id: 'social', label: 'Social' },
    { id: 'seo', label: 'SEO' },
    { id: 'commerce', label: 'Commerce' },
    { id: 'colors', label: 'Colors' },
];

function submit() {
    form
        .transform((d) => ({
            ...d,
            maintenance_mode: !!d.maintenance_mode,
            instagram_feed_enabled: !!d.instagram_feed_enabled,
            inventory_enabled: !!d.inventory_enabled,
            cart_enabled: !!d.cart_enabled,
            free_shipping_threshold: d.free_shipping_threshold === '' || d.free_shipping_threshold === null ? null : Number(d.free_shipping_threshold),
            tax_rate: d.tax_rate === '' ? null : Number(d.tax_rate),
        }))
        .put(route('admin.settings.update'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Settings — Admin" />
    <AdminLayout>
        <div class="mx-auto max-w-5xl space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-[10px] uppercase tracking-[0.25em] text-primary">System</p>
                    <h1 class="text-3xl font-normal">Settings</h1>
                </div>
                <div class="flex flex-wrap gap-2 text-xs">
                    <Link :href="route('admin.contact-settings')" class="link link-primary">Halaman kontak terpisah</Link>
                    <Link :href="route('admin.integrations.index')" class="link link-primary">Integrations</Link>
                </div>
            </div>

            <div class="rounded-box border border-base-300 bg-base-100 p-5 shadow-sm md:p-6">
                <div class="mb-6 flex flex-wrap gap-0 border-b border-base-300">
                    <button
                        v-for="t in tabs"
                        :key="t.id"
                        type="button"
                        class="-mb-px border-b-2 px-4 py-3 text-[10px] uppercase tracking-widest transition-colors"
                        :class="tab === t.id ? 'border-primary text-primary' : 'border-transparent text-base-content/50 hover:text-base-content'"
                        @click="tab = t.id"
                    >
                        {{ t.label }}
                    </button>
                </div>

                <form class="space-y-6" @submit.prevent="submit">
                    <div v-show="tab === 'general'" class="space-y-6">
                        <SingleMediaPicker v-model="form.site_logo" label="Site logo" />
                        <div class="grid gap-4 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Site name</legend>
                                <input v-model="form.site_name" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Site tagline</legend>
                                <input v-model="form.site_tagline" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Currency</legend>
                                <input v-model="form.currency" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Weight unit</legend>
                                <input v-model="form.weight_unit" type="text" class="input input-bordered w-full" />
                            </fieldset>
                        </div>
                        <label class="flex cursor-pointer items-center gap-3">
                            <input v-model="form.maintenance_mode" type="checkbox" class="toggle toggle-primary toggle-sm" />
                            <span class="text-sm">Maintenance mode</span>
                        </label>
                    </div>

                    <div v-show="tab === 'contact'" class="space-y-4">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Address</legend>
                            <textarea v-model="form.contact_address" rows="3" class="textarea textarea-bordered w-full" />
                        </fieldset>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Phone</legend>
                                <input v-model="form.contact_phone" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Email</legend>
                                <input v-model="form.contact_email" type="email" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset sm:col-span-2">
                                <legend class="fieldset-legend">WhatsApp</legend>
                                <input v-model="form.contact_whatsapp" type="text" class="input input-bordered w-full" placeholder="+62 …" />
                            </fieldset>
                        </div>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Google Maps embed URL</legend>
                            <input v-model="form.contact_maps_embed" type="text" class="input input-bordered w-full" />
                        </fieldset>
                    </div>

                    <div v-show="tab === 'social'" class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Instagram URL</legend>
                                <input v-model="form.social_instagram" type="url" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Facebook URL</legend>
                                <input v-model="form.social_facebook" type="url" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Twitter / X URL</legend>
                                <input v-model="form.social_twitter" type="url" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">YouTube URL</legend>
                                <input v-model="form.social_youtube" type="url" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset sm:col-span-2">
                                <legend class="fieldset-legend">WhatsApp number (intl)</legend>
                                <input v-model="form.whatsapp_number" type="text" class="input input-bordered w-full" />
                            </fieldset>
                        </div>
                        <label class="flex cursor-pointer items-center gap-3">
                            <input v-model="form.instagram_feed_enabled" type="checkbox" class="toggle toggle-primary toggle-sm" />
                            <span class="text-sm">Tampilkan Instagram feed di landing</span>
                        </label>
                    </div>

                    <div v-show="tab === 'seo'" class="space-y-4">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Default SEO title</legend>
                            <input v-model="form.seo_title" type="text" class="input input-bordered w-full" />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Meta description</legend>
                            <textarea v-model="form.seo_description" rows="4" class="textarea textarea-bordered w-full" />
                        </fieldset>
                    </div>

                    <div v-show="tab === 'commerce'" class="space-y-4">
                        <div class="grid gap-4 sm:grid-cols-3">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Currency symbol</legend>
                                <input v-model="form.shop_currency_symbol" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Free shipping threshold</legend>
                                <input v-model="form.free_shipping_threshold" type="number" min="0" step="1000" class="input input-bordered w-full" />
                                <p class="text-[10px] text-base-content/40">0 = disabled</p>
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Tax rate (%)</legend>
                                <input v-model="form.tax_rate" type="number" min="0" max="100" step="0.01" class="input input-bordered w-full" />
                            </fieldset>
                        </div>
                        <label class="flex cursor-pointer items-center gap-3">
                            <input v-model="form.inventory_enabled" type="checkbox" class="toggle toggle-primary toggle-sm" />
                            <span class="text-sm">Inventory active</span>
                        </label>
                        <label class="flex cursor-pointer items-center gap-3">
                            <input v-model="form.cart_enabled" type="checkbox" class="toggle toggle-primary toggle-sm" />
                            <span class="text-sm">Cart & checkout active</span>
                        </label>
                    </div>

                    <div v-show="tab === 'colors'" class="space-y-4">
                        <p class="text-xs text-base-content/50">Palet tema bali-craft (hex 6 digit).</p>
                        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            <fieldset v-for="(meta, key) in colorDefaults" :key="key" class="fieldset">
                                <legend class="fieldset-legend">{{ meta.label }}</legend>
                                <div class="flex items-center gap-2">
                                    <input v-model="form[key]" type="color" class="h-10 w-10 cursor-pointer border border-base-300 bg-transparent p-0.5" />
                                    <input v-model="form[key]" type="text" pattern="^#[0-9a-fA-F]{6}$" class="input input-bordered input-sm flex-1 font-mono text-xs" :placeholder="meta.default" />
                                </div>
                                <p class="text-[9px] text-base-content/40">Default: {{ meta.default }}</p>
                            </fieldset>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" class="btn btn-neutral" :disabled="form.processing">Save settings</button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>
