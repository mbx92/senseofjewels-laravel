<script setup>
import AccountLayout from '@/Layouts/AccountLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();
const order = computed(() => page.props.order ?? {});
const items = computed(() => page.props.items ?? []);
const isGuest = computed(() => page.props.is_guest ?? false);
const emailQuery = computed(() => page.props.email_query ?? '');
const effectivePaymentStatus = computed(() => page.props.effective_payment_status ?? '');
const stepsDone = computed(() => page.props.steps_done ?? {});

const email = ref(emailQuery.value || '');

function submitFilter() {
    router.get(route('orders.track', order.value.order_number), { email: email.value || undefined });
}
</script>

<template>
    <Head :title="`Tracking ${order.order_number} — Sense of Jewels`" />

    <AccountLayout active="tracking">
        <div class="space-y-6">
            <div class="flex flex-col gap-3 lg:flex-row lg:items-end lg:justify-between">
                <div class="space-y-1">
                    <h1 class="text-3xl font-semibold">Order Tracking</h1>
                    <p class="text-base-content/70">Track order, fulfillment, and payment status using your order number.</p>
                </div>
                <div class="badge badge-outline badge-sm w-fit">{{ order.order_number }}</div>
            </div>

            <div v-if="isGuest" class="card border border-base-300 bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <h2 class="card-title">Filter by email</h2>
                    <form class="flex flex-col gap-3 sm:flex-row" @submit.prevent="submitFilter">
                        <input v-model="email" type="email" class="input input-bordered flex-1" placeholder="customer@example.com" />
                        <button type="submit" class="btn btn-primary">Validate</button>
                    </form>
                </div>
            </div>

            <div class="steps steps-vertical w-full rounded-box border border-base-300 bg-base-100 p-5 sm:p-6 lg:steps-horizontal">
                <div class="step" :class="{ 'step-primary': stepsDone.placed }">Placed</div>
                <div class="step" :class="{ 'step-primary': stepsDone.paid }">Paid</div>
                <div class="step" :class="{ 'step-primary': stepsDone.fulfilled }">Fulfilled</div>
                <div class="step" :class="{ 'step-primary': stepsDone.completed }">Completed</div>
            </div>

            <div class="grid gap-6 lg:grid-cols-[1fr,340px]">
                <div class="card border border-base-300 bg-base-100 shadow-sm">
                    <div class="card-body gap-4">
                        <h2 class="card-title">Order Items</h2>
                        <div class="overflow-x-auto">
                            <table class="table table-zebra">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Qty</th>
                                        <th>Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, idx) in items" :key="idx">
                                        <td>
                                            <div class="font-medium">{{ item.product_name }}</div>
                                            <div class="text-xs text-base-content/60">{{ item.product_sku }}</div>
                                        </td>
                                        <td>{{ item.quantity }}</td>
                                        <td>{{ item.total_formatted }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="card border border-base-300 bg-base-100 shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title">Statuses</h2>
                            <div class="space-y-3 text-sm">
                                <div class="flex items-center justify-between">
                                    <span>Order</span>
                                    <span class="badge badge-outline">{{ order.status }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Payment</span>
                                    <span class="badge badge-outline">{{ effectivePaymentStatus }}</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span>Fulfillment</span>
                                    <span class="badge badge-outline">{{ order.fulfillment_status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border border-base-300 bg-base-100 shadow-sm">
                        <div class="card-body">
                            <h2 class="card-title">Payment</h2>
                            <p class="text-sm text-base-content/70">Provider: {{ order.payment_provider ?? 'midtrans' }}</p>
                            <p class="text-sm text-base-content/70">Status: {{ effectivePaymentStatus }}</p>
                            <div class="divider my-0"></div>
                            <div class="flex items-center justify-between text-base font-semibold">
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
