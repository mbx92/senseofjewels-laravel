<script setup>
import AccountLayout from '@/Layouts/AccountLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();
const order = computed(() => page.props.order ?? {});
const items = computed(() => page.props.items ?? []);
const steps = computed(() => page.props.steps ?? []);
const currentStepIndex = computed(() => Number(page.props.current_step_index ?? -1));
const flash = computed(() => page.props.flash ?? {});
</script>

<template>
    <Head :title="`${order.order_number} — Sense of Jewels`" />

    <AccountLayout active="orders">
        <div class="space-y-8">
            <div class="space-y-1">
                <Link :href="route('orders.index')" class="text-[10px] uppercase tracking-[0.18em] font-semibold text-base-content/40 hover:text-primary transition-colors"
                    >← Back to Orders</Link
                >
                <div class="flex items-end justify-between gap-4 flex-wrap">
                    <h1 class="display-font text-3xl text-base-content font-normal">{{ order.order_number }}</h1>
                    <div class="flex items-center gap-3 pb-1">
                        <span class="text-xs text-base-content/50">{{ order.placed_at_display }}</span>
                        <a
                            :href="route('orders.invoice', order.order_number)"
                            target="_blank"
                            class="inline-flex items-center gap-1.5 rounded-lg border border-base-300 bg-base-100 px-3 py-1.5 text-xs font-semibold text-base-content/70 shadow-sm hover:border-primary hover:text-primary transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0-3-3m3 3 3-3M3 17v2a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-2M7 10V7a5 5 0 0 1 10 0v3" />
                            </svg>
                            Download Invoice
                        </a>
                    </div>
                </div>
            </div>

            <div v-if="flash.status" class="alert alert-success">
                <span>{{ flash.status }}</span>
            </div>

            <div v-if="order.status !== 'cancelled'" class="card overflow-x-auto bg-base-100 shadow-sm">
                <div class="card-body">
                    <ul class="steps steps-horizontal w-full">
                        <li v-for="(step, i) in steps" :key="step" class="step capitalize text-sm" :class="{ 'step-primary': i <= currentStepIndex }">
                            {{ step }}
                        </li>
                    </ul>
                </div>
            </div>
            <div v-else class="alert alert-error"><span>This order has been cancelled.</span></div>

            <div class="grid gap-6 md:grid-cols-2">
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-base">Order Information</h2>
                        <div class="text-sm space-y-1">
                            <div class="flex justify-between">
                                <span class="text-base-content/60">Date</span>
                                <span>{{ order.placed_at_long }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/60">Status</span>
                                <span class="badge badge-sm capitalize">{{ order.status }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-base-content/60">Payment</span>
                                <span class="badge badge-sm capitalize">{{ order.payment_status }}</span>
                            </div>
                            <div v-if="order.payment_provider" class="flex justify-between">
                                <span class="text-base-content/60">Method</span>
                                <span class="capitalize">{{ order.payment_provider }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-base">Shipping Address</h2>
                        <div v-if="order.shipping_address" class="space-y-1 text-sm">
                            <p class="font-medium">{{ order.customer_name }}</p>
                            <p>{{ order.shipping_address.line_1 }}</p>
                            <p>
                                {{ order.shipping_address.city }}, {{ order.shipping_address.province }} {{ order.shipping_address.postal_code }}
                            </p>
                            <p>{{ order.shipping_address.country }}</p>
                            <p v-if="order.shipping_address.tracking" class="mt-2 rounded bg-base-200 px-2 py-1 font-mono text-xs">
                                Tracking: {{ order.shipping_address.tracking }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base">Order Items</h2>
                    <div class="overflow-x-auto">
                        <table class="table table-sm table-zebra">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th class="text-right">Qty</th>
                                    <th class="text-right">Price</th>
                                    <th class="text-right">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(item, idx) in items" :key="idx">
                                    <td>
                                        <div class="font-medium">{{ item.product_name }}</div>
                                        <div class="text-xs text-base-content/50">{{ item.product_sku }}</div>
                                    </td>
                                    <td class="text-right">{{ item.quantity }}</td>
                                    <td class="text-right">{{ item.unit_price_formatted }}</td>
                                    <td class="text-right font-semibold">{{ item.total_formatted }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="flex justify-end pt-4">
                        <div class="text-sm space-y-1 min-w-48">
                            <div class="flex justify-between">
                                <span class="text-base-content/60">Subtotal</span>
                                <span>{{ order.subtotal_formatted }}</span>
                            </div>
                            <div v-if="order.show_discount" class="flex justify-between text-success">
                                <span>Discount</span>
                                <span>- {{ order.discount_total_formatted }}</span>
                            </div>
                            <div v-if="order.show_shipping" class="flex justify-between">
                                <span class="text-base-content/60">Shipping</span>
                                <span>{{ order.shipping_total_formatted }}</span>
                            </div>
                            <div class="mt-1 flex justify-between border-t border-base-300 pt-1 text-base font-bold">
                                <span>Total</span>
                                <span>{{ order.total_formatted }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AccountLayout>
</template>
