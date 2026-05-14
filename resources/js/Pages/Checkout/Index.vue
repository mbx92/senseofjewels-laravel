<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();

const props = defineProps({
    items: { type: Array, default: () => [] },
    subtotal_formatted: { type: String, default: '' },
    product_discount_total: { type: Number, default: 0 },
    product_discount_total_formatted: { type: String, default: '' },
    discount_total: { type: Number, default: 0 },
    discount_total_formatted: { type: String, default: '' },
    total_saved_formatted: { type: String, default: '' },
    total_formatted: { type: String, default: '' },
    show_product_discount: { type: Boolean, default: false },
    show_voucher_discount: { type: Boolean, default: false },
    show_total_saved: { type: Boolean, default: false },
    midtrans_active: { type: Boolean, default: true },
    defaults: {
        type: Object,
        default: () => ({
            customer_name: '',
            customer_email: '',
            customer_phone: '',
            shipping_address: {
                line_1: '',
                city: '',
                province: '',
                postal_code: '',
                country: 'Indonesia',
            },
            notes: '',
        }),
    },
    translations: { type: Object, default: () => ({}) },
});

const t = computed(() => props.translations ?? {});
const flash = computed(() => page.props.flash ?? {});

const form = useForm({
    customer_name: props.defaults.customer_name ?? '',
    customer_email: props.defaults.customer_email ?? '',
    customer_phone: props.defaults.customer_phone ?? '',
    shipping_address: {
        line_1: props.defaults.shipping_address?.line_1 ?? '',
        city: props.defaults.shipping_address?.city ?? '',
        province: props.defaults.shipping_address?.province ?? '',
        postal_code: props.defaults.shipping_address?.postal_code ?? '',
        country: props.defaults.shipping_address?.country ?? 'Indonesia',
    },
    notes: props.defaults.notes ?? '',
});

const voucherCode = ref('');
const voucherLoading = ref(false);
const voucherMessage = ref('');
const voucherValid = ref(false);
const voucherDiscountFmt = ref('');
const voucherTotalFmt = ref('');

function fieldError(key) {
    return form.errors[key] ?? '';
}

function submitCheckout() {
    form.post(route('checkout.store'));
}

async function applyVoucher() {
    voucherLoading.value = true;
    voucherMessage.value = '';
    voucherValid.value = false;
    try {
        const res = await fetch(route('checkout.apply-voucher'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': page.props.csrf,
            },
            body: JSON.stringify({ code: voucherCode.value.toUpperCase() }),
        });
        const data = await res.json();
        voucherLoading.value = false;
        voucherMessage.value = data.message ?? '';
        voucherValid.value = Boolean(data.valid);
        if (data.valid) {
            voucherDiscountFmt.value = data.discount_fmt ?? '';
            voucherTotalFmt.value = data.total_fmt ?? '';
        }
    } catch {
        voucherLoading.value = false;
        voucherMessage.value = 'Terjadi kesalahan.';
        voucherValid.value = false;
    }
}

const isEmpty = computed(() => !props.items?.length);

const paymentFootnote = computed(() =>
    props.midtrans_active ? t.value.payment_redirect : t.value.payment_manual,
);
</script>

<template>
    <Head :title="`${t.title} — Sense of Jewels`" />

    <AppLayout>
        <div class="container mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12 space-y-8">
            <div class="space-y-1">
                <h1 class="display-font text-4xl text-base-content">{{ t.title }}</h1>
                <p class="text-sm text-base-content/55">{{ t.subtitle }}</p>
            </div>

            <div v-if="flash.status" class="alert alert-success text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ flash.status }}</span>
            </div>

            <div v-if="flash.error" class="alert alert-error text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <span>{{ flash.error }}</span>
            </div>

            <div v-if="isEmpty" class="alert alert-warning">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20A10 10 0 0112 2z" />
                </svg>
                <span>
                    {{ t.empty_cart }}
                    <Link :href="route('shop.index')" class="underline">{{ t.back_to_shop }}</Link>
                </span>
            </div>

            <form v-else class="space-y-6" @submit.prevent="submitCheckout">
                <div class="grid gap-8 lg:grid-cols-[1fr,360px] items-start">
                    <div class="space-y-6">
                        <ul class="steps steps-horizontal w-full text-xs mb-2">
                            <li class="step step-primary">{{ t.step_data }}</li>
                            <li class="step step-primary">{{ t.step_confirm }}</li>
                            <li class="step">{{ t.step_payment }}</li>
                        </ul>

                        <div class="card border border-base-300 bg-base-100">
                            <div class="card-body gap-4">
                                <h2 class="card-title text-base font-semibold">{{ t.customer_heading }}</h2>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="form-control sm:col-span-2">
                                        <label class="label"><span class="label-text">{{ t.full_name }} <span class="text-error">*</span></span></label>
                                        <input
                                            v-model="form.customer_name"
                                            type="text"
                                            class="input input-bordered w-full"
                                            :class="{ 'input-error': fieldError('customer_name') }"
                                            required
                                        />
                                        <p v-if="fieldError('customer_name')" class="label-text-alt text-error mt-1">{{ fieldError('customer_name') }}</p>
                                    </div>
                                    <div class="form-control">
                                        <label class="label"><span class="label-text">{{ t.email }} <span class="text-error">*</span></span></label>
                                        <input
                                            v-model="form.customer_email"
                                            type="email"
                                            class="input input-bordered w-full"
                                            :class="{ 'input-error': fieldError('customer_email') }"
                                            required
                                        />
                                        <p v-if="fieldError('customer_email')" class="label-text-alt text-error mt-1">{{ fieldError('customer_email') }}</p>
                                    </div>
                                    <div class="form-control">
                                        <label class="label"><span class="label-text">{{ t.phone }}</span></label>
                                        <input
                                            v-model="form.customer_phone"
                                            type="text"
                                            :placeholder="t.phone_placeholder"
                                            class="input input-bordered w-full"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border border-base-300 bg-base-100">
                            <div class="card-body gap-4">
                                <h2 class="card-title text-base font-semibold">{{ t.shipping_heading }}</h2>
                                <div class="grid gap-4 sm:grid-cols-2">
                                    <div class="form-control sm:col-span-2">
                                        <label class="label"><span class="label-text">{{ t.address_line_1 }} <span class="text-error">*</span></span></label>
                                        <input
                                            v-model="form.shipping_address.line_1"
                                            type="text"
                                            :placeholder="t.address_placeholder"
                                            class="input input-bordered w-full"
                                            :class="{ 'input-error': fieldError('shipping_address.line_1') }"
                                            required
                                        />
                                        <p v-if="fieldError('shipping_address.line_1')" class="label-text-alt text-error mt-1">
                                            {{ fieldError('shipping_address.line_1') }}
                                        </p>
                                    </div>
                                    <div class="form-control">
                                        <label class="label"><span class="label-text">{{ t.city }} <span class="text-error">*</span></span></label>
                                        <input
                                            v-model="form.shipping_address.city"
                                            type="text"
                                            class="input input-bordered w-full"
                                            :class="{ 'input-error': fieldError('shipping_address.city') }"
                                            required
                                        />
                                        <p v-if="fieldError('shipping_address.city')" class="label-text-alt text-error mt-1">{{ fieldError('shipping_address.city') }}</p>
                                    </div>
                                    <div class="form-control">
                                        <label class="label"><span class="label-text">{{ t.province }} <span class="text-error">*</span></span></label>
                                        <input
                                            v-model="form.shipping_address.province"
                                            type="text"
                                            class="input input-bordered w-full"
                                            :class="{ 'input-error': fieldError('shipping_address.province') }"
                                            required
                                        />
                                        <p v-if="fieldError('shipping_address.province')" class="label-text-alt text-error mt-1">
                                            {{ fieldError('shipping_address.province') }}
                                        </p>
                                    </div>
                                    <div class="form-control">
                                        <label class="label"><span class="label-text">{{ t.postal_code }} <span class="text-error">*</span></span></label>
                                        <input
                                            v-model="form.shipping_address.postal_code"
                                            type="text"
                                            class="input input-bordered w-full"
                                            :class="{ 'input-error': fieldError('shipping_address.postal_code') }"
                                            required
                                        />
                                        <p v-if="fieldError('shipping_address.postal_code')" class="label-text-alt text-error mt-1">
                                            {{ fieldError('shipping_address.postal_code') }}
                                        </p>
                                    </div>
                                    <div class="form-control">
                                        <label class="label"><span class="label-text">{{ t.country }}</span></label>
                                        <input v-model="form.shipping_address.country" type="text" class="input input-bordered w-full" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card border border-base-300 bg-base-100">
                            <div class="card-body gap-3">
                                <h2 class="card-title text-base font-semibold">{{ t.notes_heading }}</h2>
                                <div class="form-control">
                                    <textarea
                                        v-model="form.notes"
                                        rows="3"
                                        :placeholder="t.notes_placeholder"
                                        class="textarea textarea-bordered min-h-20 w-full"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-4 lg:sticky lg:top-24">
                        <div class="card border border-base-300 bg-base-100">
                            <div class="card-body gap-4">
                                <h2 class="card-title text-base font-semibold">{{ t.summary_heading }}</h2>

                                <ul class="space-y-3 divide-y divide-base-200">
                                    <li v-for="(item, idx) in items" :key="idx" class="flex items-start justify-between gap-3 pt-3 first:pt-0 text-sm">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-medium text-base-content truncate">{{ item.product_name }}</p>
                                            <p class="text-base-content/50 text-xs mt-0.5">{{ t.qty_label }} {{ item.quantity }}</p>
                                        </div>
                                        <span class="font-light text-base-content/80 whitespace-nowrap">{{ item.line_total_formatted }}</span>
                                    </li>
                                </ul>

                                <div class="pt-3 space-y-2 text-sm border-t border-base-200">
                                    <div class="flex justify-between text-base-content/60">
                                        <span>{{ t.subtotal }}</span>
                                        <span>{{ subtotal_formatted }}</span>
                                    </div>
                                    <div v-if="show_product_discount" class="flex justify-between text-success">
                                        <span>{{ t.product_discount }}</span>
                                        <span>- {{ product_discount_total_formatted }}</span>
                                    </div>
                                    <div v-if="show_voucher_discount" class="flex justify-between text-success">
                                        <span>{{ t.voucher_discount }}</span>
                                        <span>- {{ discount_total_formatted }}</span>
                                    </div>
                                    <div v-if="show_total_saved" class="flex justify-between text-base-content/70 text-xs pt-1">
                                        <span>{{ t.total_saved }}</span>
                                        <span>{{ total_saved_formatted }}</span>
                                    </div>
                                    <div class="flex justify-between font-semibold text-base pt-1 border-t border-base-200">
                                        <span>{{ t.total }}</span>
                                        <span>{{ total_formatted }}</span>
                                    </div>
                                </div>

                                <div class="pt-1">
                                    <div class="form-control">
                                        <label class="label">
                                            <span class="label-text text-xs font-medium uppercase tracking-widest text-base-content/50">{{ t.voucher_code }}</span>
                                        </label>
                                        <div class="flex gap-2">
                                            <input
                                                v-model="voucherCode"
                                                type="text"
                                                :placeholder="t.voucher_placeholder"
                                                class="input input-bordered input-sm flex-1 uppercase"
                                                @keydown.enter.prevent="applyVoucher"
                                            />
                                            <button
                                                type="button"
                                                class="btn btn-outline btn-sm"
                                                :disabled="voucherLoading || !voucherCode"
                                                @click="applyVoucher"
                                            >
                                                <span v-show="!voucherLoading">{{ t.apply }}</span>
                                                <span v-show="voucherLoading" class="loading loading-spinner loading-xs" />
                                            </button>
                                        </div>
                                        <p v-if="voucherMessage" class="mt-1 text-xs" :class="voucherValid ? 'text-success' : 'text-error'">
                                            {{ voucherMessage }}
                                        </p>
                                    </div>
                                    <div v-if="voucherValid" class="mt-3 space-y-1 text-sm bg-success/10 rounded-lg p-3">
                                        <div class="flex justify-between text-success font-medium">
                                            <span>{{ t.voucher_discount }}</span>
                                            <span>- {{ voucherDiscountFmt }}</span>
                                        </div>
                                        <div class="flex justify-between font-bold text-base-content">
                                            <span>{{ t.voucher_payable }}</span>
                                            <span>{{ voucherTotalFmt }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-xs text-base-content/40 text-center leading-relaxed px-2">
                            <template v-if="midtrans_active">
                                {{ t.payment_secured }}<br />
                                {{ paymentFootnote }}
                            </template>
                            <template v-else>
                                {{ paymentFootnote }}
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <Link :href="route('cart.index')" class="btn btn-ghost btn-sm self-start">{{ t.back_to_cart }}</Link>
                    <button type="submit" class="btn btn-primary w-full sm:w-auto btn-lg sm:min-w-[280px]" :disabled="form.processing">
                        <span v-if="!form.processing">{{ t.submit }} →</span>
                        <span v-else class="loading loading-spinner" />
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
