<script setup>
import AccountLayout from '@/Layouts/AccountLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
</script>

<template>
    <Head title="Reviews — Sense of Jewels" />

    <AccountLayout active="reviews">
        <div class="space-y-10">
            <div>
                <h1 class="display-font text-4xl text-base-content font-normal lg:text-5xl">Reviews</h1>
            </div>

            <div class="flex flex-col items-center justify-center py-24 text-center gap-5">
                <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.8" class="text-base-content/20">
                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                </svg>
                <div class="space-y-1">
                    <p class="text-sm text-base-content/50">You have not written any reviews yet.</p>
                    <p class="text-xs text-base-content/30">Reviews will appear here once your orders are completed.</p>
                </div>
                <Link :href="route('shop.index')" class="mt-2 text-[10px] uppercase tracking-[0.2em] font-semibold text-primary hover:text-primary/70 transition-colors">
                    Browse Products →
                </Link>
            </div>
        </div>
    </AccountLayout>
</template>
