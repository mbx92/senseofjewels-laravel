<script setup>
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
</script>

<template>
    <div class="min-h-screen flex flex-col lg:flex-row">
        <div class="hidden lg:flex lg:w-1/2 xl:w-[55%] relative bg-neutral overflow-hidden flex-col justify-between p-16">
            <div class="absolute inset-0 bg-gradient-to-br from-neutral via-neutral/90 to-[#2a1a10]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_80%,rgba(191,160,84,0.18),transparent_55%)]"></div>
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_80%_20%,rgba(143,175,159,0.1),transparent_50%)]"></div>

            <div class="relative z-10">
                <Link :href="route('home')" class="display-font text-3xl text-white/90 tracking-wide hover:text-primary transition-colors">Sense of Jewels</Link>
            </div>

            <div class="relative z-10">
                <p class="text-primary text-[10px] uppercase tracking-[0.3em] mb-6">Admin Portal</p>
                <h2 class="display-font text-5xl xl:text-6xl text-white leading-[0.95] mb-8">
                    Crafted with<br />
                    <span class="italic font-light text-white/70">intention.</span>
                </h2>
                <div class="w-10 h-px bg-primary/50 mb-8"></div>
                <p class="text-white/50 text-sm font-light leading-relaxed max-w-xs">
                    Manage your jewelry collections, orders, and content from one elegant dashboard.
                </p>
            </div>

            <div class="relative z-10 flex items-center gap-6">
                <Link :href="route('home')" class="text-white/50 hover:text-white text-[10px] uppercase tracking-widest transition-colors">← Back to Site</Link>
                <Link :href="route('shop.index')" class="text-white/50 hover:text-white text-[10px] uppercase tracking-widest transition-colors">Shop</Link>
            </div>
        </div>

        <div class="flex-1 flex flex-col items-center justify-center px-6 py-16 bg-base-100">
            <div class="lg:hidden mb-10 text-center">
                <Link :href="route('home')" class="display-font text-3xl text-base-content">Sense of Jewels</Link>
            </div>

            <div class="w-full max-w-sm">
                <slot />
            </div>

            <p class="mt-10 text-[10px] text-base-content/40 uppercase tracking-widest">&copy; {{ new Date().getFullYear() }} Sense of Jewels · Bali</p>
        </div>
    </div>
</template>
