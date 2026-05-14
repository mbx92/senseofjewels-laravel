<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { onMounted, ref } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();

const props = defineProps({
    order: { type: Object, required: true },
    items: { type: Array, default: () => [] },
    routes: { type: Object, required: true },
    midtrans: { type: Object, required: true },
});

const loading = ref(false);
const errorMsg = ref('');
let cachedToken = props.order.snap_token || '';

function loadSnapScript() {
    return new Promise((resolve, reject) => {
        if (window.snap) {
            resolve();
            return;
        }
        const s = document.createElement('script');
        s.src = props.midtrans.snap_url;
        s.setAttribute('data-client-key', props.midtrans.client_key);
        s.onload = () => resolve();
        s.onerror = () => reject(new Error('Failed to load Midtrans'));
        document.body.appendChild(s);
    });
}

async function pay() {
    loading.value = true;
    errorMsg.value = '';
    try {
        await loadSnapScript();
        let token = cachedToken;
        if (!token) {
            const res = await fetch(props.routes.token, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': page.props.csrf,
                    Accept: 'application/json',
                },
                body: JSON.stringify({ order_id: props.order.id }),
            });
            const data = await res.json();
            if (!res.ok) {
                errorMsg.value = data.error ?? 'Could not load payment.';
                loading.value = false;
                return;
            }
            token = data.token;
            cachedToken = token;
        }
        loading.value = false;
        window.snap.pay(token, {
            onSuccess: () => {
                window.location.href = props.routes.success;
            },
            onPending: () => {
                window.location.href = props.routes.pending;
            },
            onError: () => {
                window.location.href = props.routes.failed;
            },
            onClose: () => {},
        });
    } catch {
        errorMsg.value = 'Something went wrong. Please try again.';
        loading.value = false;
    }
}

onMounted(() => {
    cachedToken = props.order.snap_token || '';
});
</script>

<template>
    <Head :title="`Pay ${order.order_number} — Sense of Jewels`" />

    <AppLayout>
        <div class="container mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-12 space-y-8">
            <div class="space-y-1">
                <p class="text-xs uppercase tracking-[0.25em] text-primary">Order #{{ order.order_number }}</p>
                <h1 class="display-font text-4xl text-base-content">Complete payment</h1>
                <p class="text-sm text-base-content/55">Tap the button below to open Midtrans Snap.</p>
            </div>

            <ul class="steps steps-horizontal w-full text-xs">
                <li class="step step-primary">Data</li>
                <li class="step step-primary">Confirmation</li>
                <li class="step step-primary">Payment</li>
            </ul>

            <div class="grid gap-6 lg:grid-cols-[1fr,340px] items-start">
                <div class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="font-semibold text-base">Amount due</h2>
                                <p class="display-font text-3xl text-base-content">{{ order.total_formatted }}</p>
                            </div>
                        </div>

                        <div class="text-sm text-base-content/60 space-y-1">
                            <div class="flex justify-between">
                                <span>Customer</span>
                                <span class="text-base-content">{{ order.customer_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Email</span>
                                <span class="text-base-content">{{ order.customer_email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Method</span>
                                <span class="text-base-content">Midtrans</span>
                            </div>
                        </div>

                        <div class="alert alert-info text-sm gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20A10 10 0 0112 2z" />
                            </svg>
                            <span>After tapping Pay now, the Midtrans window opens. Do not close this page.</span>
                        </div>

                        <button type="button" class="btn btn-primary btn-block btn-lg" :disabled="loading" @click="pay">
                            <span v-show="loading" class="loading loading-spinner loading-sm" />
                            <span>{{ loading ? 'Processing…' : 'Pay now' }}</span>
                        </button>

                        <div v-if="errorMsg" class="alert alert-error mt-2 text-sm">
                            {{ errorMsg }}
                        </div>
                    </div>
                </div>

                <div class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-4">
                        <h3 class="font-semibold text-sm uppercase tracking-widest text-base-content/50">Order items</h3>
                        <ul class="space-y-3 divide-y divide-base-200">
                            <li v-for="(item, idx) in items" :key="idx" class="flex items-start justify-between gap-3 pt-3 first:pt-0 text-sm">
                                <div class="flex-1 min-w-0">
                                    <p class="font-medium text-base-content truncate">{{ item.product_name }}</p>
                                    <p class="text-xs text-base-content/45 mt-0.5">Qty {{ item.quantity }}</p>
                                </div>
                                <span class="text-base-content/80 whitespace-nowrap">{{ item.total_formatted }}</span>
                            </li>
                        </ul>
                        <div class="pt-3 border-t border-base-200 text-sm space-y-1.5">
                            <div v-if="order.discount_total > 0" class="flex justify-between text-success">
                                <span>Discount</span>
                                <span>- {{ order.discount_total_formatted }}</span>
                            </div>
                            <div class="flex justify-between font-semibold">
                                <span>Total</span>
                                <span>{{ order.total_formatted }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
