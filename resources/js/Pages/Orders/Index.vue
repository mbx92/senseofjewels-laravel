<script setup>
import AccountLayout from '@/Layouts/AccountLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();
const orders = computed(() => page.props.orders ?? { data: [], links: [] });
const flash = computed(() => page.props.flash ?? {});
</script>

<template>
    <Head title="My Orders — Sense of Jewels" />

    <AccountLayout active="orders">
        <div class="space-y-8 w-full">
            <div>
                <h1 class="display-font text-4xl text-base-content font-normal lg:text-5xl">My Orders</h1>
            </div>

            <div v-if="flash.status" class="alert alert-success">
                <span>{{ flash.status }}</span>
            </div>

            <div v-if="!orders.data?.length" class="card w-full border border-base-300 bg-base-200/70 shadow-sm">
                <div class="card-body min-h-72 items-center justify-center py-10 text-center sm:py-12">
                    <p class="mb-4 text-base-content/60">You have no orders yet.</p>
                    <Link :href="route('shop.index')" class="btn btn-primary">Start Shopping</Link>
                </div>
            </div>
            <div v-else class="card w-full border border-base-300 bg-base-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Order No.</th>
                                <th>Date</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="o in orders.data" :key="o.order_number">
                                <td class="font-mono font-semibold text-sm">{{ o.order_number }}</td>
                                <td class="text-sm">{{ o.placed_at }}</td>
                                <td class="font-semibold">{{ o.total_formatted }}</td>
                                <td>
                                    <span class="badge badge-sm capitalize" :class="o.status_badge">{{ o.status }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-sm capitalize" :class="o.payment_badge">{{ o.payment_status }}</span>
                                </td>
                                <td>
                                    <Link :href="route('orders.show', o.order_number)" class="btn btn-ghost btn-xs">Details</Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div v-if="orders.links?.length > 3" class="join flex flex-wrap justify-center gap-1">
                <template v-for="link in orders.links" :key="link.label">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="join-item btn btn-sm"
                        :class="{ 'btn-active': link.active }"
                        preserve-scroll
                        v-html="link.label"
                    />
                    <span v-else class="join-item btn btn-sm btn-disabled" v-html="link.label" />
                </template>
            </div>
        </div>
    </AccountLayout>
</template>
