<script setup>
import AccountLayout from '@/Layouts/AccountLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';

defineProps({
    orders: { type: Array, default: () => [] },
});

const orderNum = ref('');

function trackSubmit() {
    const n = orderNum.value.trim();
    if (!n) return;
    router.visit(`/orders/${encodeURIComponent(n)}/tracking`);
}
</script>

<template>
    <Head title="Tracking — Sense of Jewels" />

    <AccountLayout active="tracking">
        <div class="space-y-10">
            <div>
                <h1 class="display-font text-4xl text-base-content font-normal lg:text-5xl">Tracking</h1>
            </div>

            <div class="border-b border-base-200 pb-10">
                <p class="text-xs text-base-content/50 uppercase tracking-[0.18em] mb-4">Track by order number</p>
                <form class="flex flex-col sm:flex-row gap-3 max-w-sm" @submit.prevent="trackSubmit">
                    <input
                        v-model="orderNum"
                        type="text"
                        placeholder="SOJ-XXXXXXXX"
                        class="flex-1 border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors placeholder:text-base-content/30"
                    />
                    <button type="submit" class="btn btn-primary btn-sm px-8 self-end">Track</button>
                </form>
            </div>

            <div v-if="orders.length" class="space-y-5">
                <p class="text-[10px] uppercase tracking-[0.22em] text-base-content/40">Recent Orders</p>
                <div class="divide-y divide-base-200">
                    <div v-for="o in orders" :key="o.order_number" class="flex items-center justify-between py-4 gap-4">
                        <div class="space-y-0.5 min-w-0">
                            <p class="font-mono text-sm font-medium text-base-content truncate">{{ o.order_number }}</p>
                            <p class="text-[11px] text-base-content/50">{{ o.placed_at }}</p>
                        </div>
                        <div class="flex items-center gap-4 shrink-0">
                            <span class="badge badge-sm capitalize" :class="o.fulfillment_badge">{{ o.fulfillment_status }}</span>
                            <Link
                                :href="route('orders.track', o.order_number)"
                                class="text-[10px] uppercase tracking-[0.18em] font-semibold text-primary hover:text-primary/70 transition-colors whitespace-nowrap"
                            >
                                Track →
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
            <div v-else class="py-20 text-center">
                <p class="text-sm text-base-content/40">No orders to track yet.</p>
                <Link :href="route('shop.index')" class="mt-5 inline-block text-[10px] uppercase tracking-[0.2em] font-semibold text-primary hover:text-primary/70 transition-colors">
                    Browse Products →
                </Link>
            </div>
        </div>
    </AccountLayout>
</template>
