<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

defineProps({
    order: { type: Object, required: true },
    items: { type: Array, default: () => [] },
});
</script>

<template>
    <Head :title="`Manual payment ${order.order_number} — Sense of Jewels`" />

    <AppLayout>
        <div class="container mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-12 space-y-8">
            <div class="space-y-1">
                <p class="text-xs uppercase tracking-[0.25em] text-primary">Order #{{ order.order_number }}</p>
                <h1 class="display-font text-4xl text-base-content">Manual payment</h1>
                <p class="text-sm text-base-content/55">Midtrans is currently off. Your order is saved with payment status pending.</p>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1fr,340px] items-start">
                <div class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-5">
                        <div class="alert alert-info text-sm">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 110 20A10 10 0 0112 2z" />
                            </svg>
                            <span>Our team will verify payment manually. You can send proof via the store WhatsApp.</span>
                        </div>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-base-content/60">Customer</span>
                                <span class="text-base-content">{{ order.customer_name }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/60">Email</span>
                                <span class="text-base-content">{{ order.customer_email }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/60">Status</span>
                                <span class="badge badge-warning badge-sm">pending</span>
                            </div>
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3 pt-2">
                            <Link :href="route('orders.show', order.order_number)" class="btn btn-primary">View order details</Link>
                            <Link :href="route('orders.index')" class="btn btn-outline">Back to orders</Link>
                        </div>
                    </div>
                </div>

                <div class="card border border-base-300 bg-base-100">
                    <div class="card-body gap-4">
                        <h3 class="font-semibold text-sm uppercase tracking-widest text-base-content/50">Summary</h3>
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
