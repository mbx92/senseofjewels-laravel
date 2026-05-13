<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();

const t = computed(() => page.props.translations ?? {});
const items = computed(() => page.props.items ?? []);
const isEmpty = computed(() => page.props.is_empty ?? true);
const subtotalFormatted = computed(() => page.props.subtotal_formatted ?? '');
const discountTotal = computed(() => Number(page.props.discount_total ?? 0));
const discountTotalFormatted = computed(() => page.props.discount_total_formatted ?? '');
const totalFormatted = computed(() => page.props.total_formatted ?? '');
const productDiscountTotal = computed(() => Number(page.props.product_discount_total ?? 0));
const productDiscountTotalFormatted = computed(() => page.props.product_discount_total_formatted ?? '');
const totalSavedFormatted = computed(() => page.props.total_saved_formatted ?? '');

const flash = computed(() => page.props.flash ?? {});

const showSavingsBreakdown = computed(() => productDiscountTotal.value + discountTotal.value > 0);
</script>

<template>
    <Head :title="`${t.title} — Sense of Jewels`" />

    <AppLayout>
        <div class="container mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-12 space-y-8">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-1">
                    <h1 class="display-font text-4xl text-base-content">{{ t.title }}</h1>
                    <p class="text-sm text-base-content/55">{{ t.subtitle }}</p>
                </div>
                <Link :href="route('shop.index')" class="btn btn-outline btn-sm self-start sm:self-auto">← {{ t.continue_shopping }}</Link>
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

            <div class="grid gap-6 lg:grid-cols-[1fr,300px] items-start">
                <div class="card border border-base-300 bg-base-100 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="table">
                            <thead class="bg-base-200/50">
                                <tr class="text-xs uppercase tracking-widest text-base-content/50">
                                    <th class="w-full">{{ t.col_product }}</th>
                                    <th class="text-center whitespace-nowrap">{{ t.col_qty }}</th>
                                    <th class="text-right whitespace-nowrap">{{ t.col_unit }}</th>
                                    <th class="text-right whitespace-nowrap">{{ t.col_subtotal }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template v-if="!isEmpty">
                                    <tr v-for="item in items" :key="item.id" class="align-middle">
                                        <td>
                                            <p class="font-medium text-base-content">{{ item.product_name }}</p>
                                            <p class="text-xs text-base-content/45 mt-0.5">SKU: {{ item.product_sku }}</p>
                                        </td>
                                        <td class="text-center">
                                            <form method="POST" :action="route('cart.update', item.id)" class="inline-flex items-center gap-1">
                                                <input type="hidden" name="_token" :value="page.props.csrf" />
                                                <input type="hidden" name="_method" value="PATCH" />
                                                <input
                                                    type="number"
                                                    name="quantity"
                                                    min="1"
                                                    :max="item.max_stock != null ? item.max_stock : undefined"
                                                    :value="item.quantity"
                                                    class="input input-bordered input-sm w-16 text-center"
                                                />
                                                <button type="submit" class="btn btn-ghost btn-xs text-base-content/50 hover:text-base-content" :title="t.update">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                        <td class="text-right whitespace-nowrap text-sm text-base-content/70">{{ item.unit_price_formatted }}</td>
                                        <td class="text-right whitespace-nowrap font-medium">{{ item.line_total_formatted }}</td>
                                        <td class="text-right">
                                            <form method="POST" :action="route('cart.destroy', item.id)">
                                                <input type="hidden" name="_token" :value="page.props.csrf" />
                                                <input type="hidden" name="_method" value="DELETE" />
                                                <button type="submit" class="btn btn-ghost btn-xs text-error hover:bg-error/10" :title="t.delete">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                </template>
                                <tr v-else>
                                    <td colspan="5">
                                        <div class="py-16 text-center space-y-3">
                                            <span class="block text-5xl opacity-20">🛒</span>
                                            <p class="text-base-content/50">{{ t.empty }}</p>
                                            <Link :href="route('shop.index')" class="btn btn-primary btn-sm">{{ t.start_shopping }}</Link>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card border border-base-300 bg-base-100 lg:sticky lg:top-24">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base font-semibold">{{ t.summary }}</h2>

                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between text-base-content/60">
                                <span>{{ t.subtotal }}</span>
                                <span>{{ subtotalFormatted }}</span>
                            </div>
                            <div v-if="productDiscountTotal > 0" class="flex justify-between text-success">
                                <span>{{ t.product_discount }}</span>
                                <span>- {{ productDiscountTotalFormatted }}</span>
                            </div>
                            <div v-if="discountTotal > 0" class="flex justify-between text-success">
                                <span>{{ t.voucher_discount }}</span>
                                <span>- {{ discountTotalFormatted }}</span>
                            </div>
                            <div v-if="showSavingsBreakdown" class="flex justify-between text-base-content/70 text-xs pt-1">
                                <span>{{ t.total_saved }}</span>
                                <span>{{ totalSavedFormatted }}</span>
                            </div>
                            <div class="divider my-1"></div>
                            <div class="flex justify-between font-semibold text-base">
                                <span>{{ t.total }}</span>
                                <span>{{ totalFormatted }}</span>
                            </div>
                        </div>

                        <div class="card-actions pt-2">
                            <Link v-if="!isEmpty" :href="route('checkout.index')" class="btn btn-primary btn-block">{{ t.proceed_checkout }} →</Link>
                            <button v-else type="button" class="btn btn-primary btn-block" disabled>{{ t.checkout }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
