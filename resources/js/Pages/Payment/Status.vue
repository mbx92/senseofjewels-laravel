<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    variant: { type: String, required: true },
    order: { type: Object, default: null },
});

const title = computed(() => {
    if (props.variant === 'success') return 'Payment successful!';
    if (props.variant === 'pending') return 'Waiting for payment';
    return 'Payment failed';
});
</script>

<template>
    <Head :title="`${title} — Sense of Jewels`" />

    <AppLayout>
        <div class="min-h-[70vh] flex items-center justify-center px-4">
            <div class="max-w-md w-full text-center space-y-6">
                <div
                    v-if="variant === 'success'"
                    class="flex items-center justify-center w-24 h-24 rounded-full bg-success/10 mx-auto"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-success" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <div
                    v-else-if="variant === 'pending'"
                    class="flex items-center justify-center w-24 h-24 rounded-full bg-warning/10 mx-auto"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-warning" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div v-else class="flex items-center justify-center w-24 h-24 rounded-full bg-error/10 mx-auto">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 text-error" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </div>

                <div class="space-y-2">
                    <h1 class="display-font text-4xl text-base-content">{{ title }}</h1>
                    <p v-if="order" class="text-base-content/60">
                        Order <span class="font-medium text-base-content">#{{ order.order_number }}</span>
                        <span v-if="variant === 'success'"> is being processed.</span>
                        <span v-else-if="variant === 'pending'"> is not confirmed yet.</span>
                        <span v-else> could not be completed.</span>
                    </p>
                    <p v-else class="text-base-content/60">
                        <span v-if="variant === 'success'">Your payment has been received.</span>
                        <span v-else-if="variant === 'pending'">Your payment is being verified.</span>
                        <span v-else>Payment could not be processed.</span>
                    </p>
                </div>

                <div v-if="variant === 'success'" class="alert alert-success text-sm text-left">
                    <span>Confirmation has been sent to your email. Your order will be processed shortly.</span>
                </div>
                <div v-else-if="variant === 'pending'" class="alert alert-warning text-sm text-left">
                    <span>Your payment is being processed. We will email you after confirmation.</span>
                </div>
                <div v-else class="alert alert-error text-sm text-left">
                    <span>Payment was denied or cancelled. Try again or use another method.</span>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <template v-if="variant === 'success'">
                        <Link v-if="order" :href="route('orders.show', order.order_number)" class="btn btn-primary">View order</Link>
                        <Link :href="route('shop.index')" class="btn btn-outline">Continue shopping</Link>
                    </template>
                    <template v-else-if="variant === 'pending'">
                        <Link v-if="order" :href="route('orders.show', order.order_number)" class="btn btn-warning btn-outline">Check order</Link>
                        <Link :href="route('home')" class="btn btn-outline">Home</Link>
                    </template>
                    <template v-else>
                        <Link v-if="order" :href="route('payment.show', order.order_number)" class="btn btn-primary">Try again</Link>
                        <Link v-if="order" :href="route('orders.show', order.order_number)" class="btn btn-outline">View order</Link>
                        <Link v-if="!order" :href="route('shop.index')" class="btn btn-primary">Back to shop</Link>
                    </template>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
