<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

defineProps({
    today_revenue_formatted: { type: String, required: true },
    today_orders: { type: Number, required: true },
    total_orders: { type: Number, required: true },
    pending_orders: { type: Number, required: true },
    total_products: { type: Number, required: true },
    low_stock_count: { type: Number, required: true },
    total_customers: { type: Number, required: true },
    recent_orders: { type: Array, default: () => [] },
});

const steps = [
    { done: true, label: 'Foundation routes & layouts' },
    { done: true, label: 'Database models & migrations' },
    { done: true, label: 'CMS CRUD: categories (admin)' },
    { done: true, label: 'Admin panel: Inertia + Vue (shop, CMS, system)' },
    { done: false, label: 'Midtrans Snap checkout' },
];
</script>

<template>
    <Head title="Dashboard — Admin" />

    <AdminLayout>
        <div class="space-y-8">
            <div class="flex flex-col gap-1">
                <p class="text-[10px] uppercase tracking-[0.25em] text-primary">Admin Panel</p>
                <h1 class="display-font text-4xl text-base-content font-normal">Dashboard</h1>
                <p class="text-sm text-base-content/50 font-light">Snapshot of your store's content, catalog, and orders.</p>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <div class="bg-base-100 border border-base-300 px-6 py-5">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-3">Today's revenue</p>
                    <p class="display-font text-4xl text-primary leading-none mb-2">{{ today_revenue_formatted }}</p>
                    <p class="text-[11px] text-base-content/40">{{ today_orders }} orders today</p>
                </div>
                <div class="bg-base-100 border border-base-300 px-6 py-5">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-3">Total orders</p>
                    <p class="display-font text-5xl text-base-content leading-none mb-2">{{ total_orders }}</p>
                    <p class="text-[11px] text-warning">{{ pending_orders }} pending payment</p>
                </div>
                <div class="bg-base-100 border border-base-300 px-6 py-5">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-3">Total products</p>
                    <p class="display-font text-5xl text-secondary leading-none mb-2">{{ total_products }}</p>
                    <p v-if="low_stock_count > 0" class="text-[11px] text-error">⚠ {{ low_stock_count }} low stock</p>
                    <p v-else class="text-[11px] text-base-content/40">Stock OK</p>
                </div>
                <div class="bg-base-100 border border-base-300 px-6 py-5">
                    <p class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-3">Total customers</p>
                    <p class="display-font text-5xl text-base-content leading-none mb-2">{{ total_customers }}</p>
                    <p class="text-[11px] text-base-content/40">Registered accounts</p>
                </div>
            </div>

            <div class="grid gap-6 xl:grid-cols-[2fr,1fr]">
                <div class="bg-base-100 border border-base-300">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-base-200">
                        <p class="text-[10px] uppercase tracking-[0.2em] text-base-content/60">Recent orders</p>
                        <a :href="route('admin.orders.index')" class="text-[10px] uppercase tracking-widest text-primary hover:underline">View all</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="border-b border-base-200">
                                    <th class="text-left px-6 py-3 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Order</th>
                                    <th class="text-left px-6 py-3 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Customer</th>
                                    <th class="text-left px-6 py-3 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Status</th>
                                    <th class="text-right px-6 py-3 text-[10px] uppercase tracking-widest text-base-content/40 font-normal">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="o in recent_orders" :key="o.order_number" class="border-b border-base-200 last:border-0 hover:bg-base-200/50 transition-colors">
                                    <td class="px-6 py-3.5 text-xs font-mono text-base-content/70">{{ o.order_number }}</td>
                                    <td class="px-6 py-3.5 text-xs">{{ o.customer_name }}</td>
                                    <td class="px-6 py-3.5">
                                        <span class="text-[9px] uppercase tracking-widest border border-base-content/20 px-2.5 py-1 text-base-content/60">{{ o.status }}</span>
                                    </td>
                                    <td class="px-6 py-3.5 text-xs text-right">{{ o.total_formatted }}</td>
                                </tr>
                                <tr v-if="!recent_orders.length">
                                    <td colspan="4" class="px-6 py-10 text-center text-sm text-base-content/40">No orders yet.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-base-100 border border-base-300">
                    <div class="px-6 py-4 border-b border-base-200">
                        <p class="text-[10px] uppercase tracking-[0.2em] text-base-content/60">Build progress</p>
                    </div>
                    <ul class="px-6 py-5 space-y-4">
                        <li v-for="(step, i) in steps" :key="i" class="flex items-center gap-3">
                            <span
                                class="w-4 h-4 shrink-0 flex items-center justify-center rounded-full"
                                :class="step.done ? 'bg-primary' : 'border border-base-content/20'"
                            >
                                <svg
                                    v-if="step.done"
                                    xmlns="http://www.w3.org/2000/svg"
                                    class="w-2.5 h-2.5 text-primary-content"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="3"
                                >
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                            <span class="text-xs" :class="step.done ? 'text-base-content/70' : 'text-base-content/40'">{{ step.label }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
