<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();

const t = computed(() => page.props.layoutStrings ?? {});
const cartEnabled = computed(() => page.props.cartEnabled ?? true);
const whatsappUrl = computed(() => page.props.whatsappUrl ?? '');
const navCartCount = computed(() => Number(page.props.navCartCount ?? 0));
const siteName = computed(() => page.props.siteName ?? 'Sense of Jewels');
const siteLogo = computed(() => page.props.siteLogo ?? null);
const currency = computed(() => page.props.currency ?? 'IDR');
const currencyOptions = computed(() => page.props.currencyOptions ?? {});
const csrf = computed(() => page.props.csrf ?? '');
const flashCartAdded = computed(() => page.props.flash?.cart_added ?? null);

const url = computed(() => page.url ?? '');
const isHome = computed(() => url.value === '/' || url.value === '');
const isShop = computed(() => url.value.startsWith('/shop'));

const localeOpen = ref(false);
const currencyOpen = ref(false);
const localeRoot = ref(null);
const currencyRoot = ref(null);

const toasts = ref([]);

function pushToast(detail) {
    if (!detail?.message) return;
    const id = Date.now() + Math.random();
    toasts.value.push({
        id,
        message: detail.message,
        type: detail.type || 'info',
    });
    setTimeout(() => {
        toasts.value = toasts.value.filter((x) => x.id !== id);
    }, 1800);
}

function onAppToastWindow(e) {
    pushToast(e.detail);
}

function onDocClick(e) {
    if (localeOpen.value && localeRoot.value && !localeRoot.value.contains(e.target)) {
        localeOpen.value = false;
    }
    if (currencyOpen.value && currencyRoot.value && !currencyRoot.value.contains(e.target)) {
        currencyOpen.value = false;
    }
}

const cartAddedVisible = ref(false);
watch(
    flashCartAdded,
    (msg) => {
        if (msg) {
            cartAddedVisible.value = true;
            setTimeout(() => {
                cartAddedVisible.value = false;
            }, 1800);
        }
    },
    { immediate: true },
);

onMounted(() => {
    window.addEventListener('app-toast', onAppToastWindow);
    document.addEventListener('click', onDocClick, true);
});

onUnmounted(() => {
    window.removeEventListener('app-toast', onAppToastWindow);
    document.removeEventListener('click', onDocClick, true);
});
</script>

<template>
    <div>
        <header class="sticky top-0 z-50 w-full">
            <div class="w-full bg-neutral text-neutral-content">
                <div class="max-w-7xl mx-auto px-4 py-2 flex items-center justify-between gap-4 text-[11px] tracking-wide">
                    <span class="hidden sm:block">✦ &nbsp; {{ t.announcement_left }}</span>
                    <span class="w-full sm:w-auto text-center sm:text-right"
                        >{{ t.announcement_right_shipping }} &nbsp;|&nbsp; {{ t.announcement_right_whatsapp }}</span
                    >
                </div>
            </div>

            <nav id="main-navbar" class="w-full bg-base-100 border-b border-base-200">
                <div class="lg:hidden flex items-center justify-between h-14 px-4">
                    <button id="mobile-menu-btn" type="button" class="p-2 text-base-content/70 hover:text-base-content">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <Link :href="route('home')" class="inline-flex h-10 items-center">
                        <img v-if="siteLogo" :src="siteLogo" :alt="siteName" class="w-auto object-contain" style="height: 26px" />
                        <span v-else class="display-font text-2xl text-base-content">{{ siteName }}</span>
                    </Link>
                    <Link
                        v-if="cartEnabled"
                        :href="route('cart.index')"
                        class="relative inline-flex h-8 w-8 items-center justify-center p-2 text-base-content/70 hover:text-base-content"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        <span
                            data-cart-count-badge
                            class="absolute top-0 right-0 h-4 min-w-4 translate-x-1/3 -translate-y-1/3 items-center justify-center rounded-full bg-primary px-1 text-[9px] font-bold leading-none text-white shadow-sm"
                            :class="navCartCount > 0 ? 'flex' : 'hidden'"
                            >{{ navCartCount > 0 ? navCartCount : '' }}</span
                        >
                    </Link>
                    <a
                        v-else-if="whatsappUrl"
                        :href="whatsappUrl"
                        target="_blank"
                        class="inline-flex h-8 w-8 items-center justify-center p-2 text-base-content/70 hover:text-base-content"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"
                            />
                        </svg>
                    </a>
                </div>

                <div id="mobile-menu" class="hidden lg:hidden border-t border-base-200 bg-base-100 px-6 pb-5 pt-3">
                    <ul class="space-y-1">
                        <li>
                            <Link
                                :href="route('home')"
                                class="block py-2.5 text-[11px] uppercase tracking-[0.2em] font-semibold text-base-content hover:text-primary transition-colors"
                                >{{ t.nav_home }}</Link
                            >
                        </li>
                        <li>
                            <Link
                                :href="route('shop.index')"
                                class="block py-2.5 text-[11px] uppercase tracking-[0.2em] font-semibold text-base-content/70 hover:text-primary transition-colors"
                                >{{ t.nav_shop }}</Link
                            >
                        </li>
                        <li v-if="cartEnabled">
                            <Link
                                :href="route('cart.index')"
                                class="block py-2.5 text-[11px] uppercase tracking-[0.2em] font-semibold text-base-content/70 hover:text-primary transition-colors"
                                >{{ t.nav_cart }}</Link
                            >
                        </li>
                        <li v-else-if="whatsappUrl">
                            <a
                                :href="whatsappUrl"
                                target="_blank"
                                class="block py-2.5 text-[11px] uppercase tracking-[0.2em] font-semibold text-base-content/70 hover:text-primary transition-colors"
                                >{{ t.nav_order_wa }}</a
                            >
                        </li>
                        <li>
                            <a href="/#story" class="block py-2.5 text-[11px] uppercase tracking-[0.2em] font-semibold text-base-content/70 hover:text-primary transition-colors">{{
                                t.nav_story
                            }}</a>
                        </li>
                        <li>
                            <a href="/#contact" class="block py-2.5 text-[11px] uppercase tracking-[0.2em] font-semibold text-base-content/70 hover:text-primary transition-colors">{{
                                t.nav_contact
                            }}</a>
                        </li>
                    </ul>
                </div>

                <div class="hidden lg:grid grid-cols-3 items-center h-[72px] max-w-7xl mx-auto px-6 lg:px-8">
                    <nav class="flex items-center gap-8">
                        <Link
                            :href="route('home')"
                            class="text-[11px] uppercase tracking-[0.18em] font-semibold transition-colors"
                            :class="isHome ? 'text-primary' : 'text-base-content/70 hover:text-primary'"
                            >{{ t.nav_home }}</Link
                        >
                        <Link
                            :href="route('shop.index')"
                            class="text-[11px] uppercase tracking-[0.18em] font-semibold transition-colors"
                            :class="isShop ? 'text-primary' : 'text-base-content/70 hover:text-primary'"
                            >{{ t.nav_shop }}</Link
                        >
                        <a href="/#story" class="text-[11px] uppercase tracking-[0.18em] font-semibold text-base-content/70 hover:text-primary transition-colors">{{ t.nav_story }}</a>
                        <a href="/#contact" class="text-[11px] uppercase tracking-[0.18em] font-semibold text-base-content/70 hover:text-primary transition-colors">{{ t.nav_contact }}</a>
                    </nav>

                    <div class="flex justify-center">
                        <Link
                            :href="route('home')"
                            :class="
                                siteLogo
                                    ? 'inline-flex h-14 items-center'
                                    : 'display-font text-[2.4rem] text-base-content hover:text-primary transition-colors leading-none tracking-wide'
                            "
                        >
                            <img v-if="siteLogo" :src="siteLogo" :alt="siteName" class="w-auto object-contain" style="height: 38px" />
                            <template v-else>{{ siteName }}</template>
                        </Link>
                    </div>

                    <div class="flex items-center justify-end gap-5">
                        <button type="button" class="text-base-content/50 hover:text-base-content transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                        </button>
                        <Link
                            v-if="$page.props.auth?.user"
                            :href="route('profile.edit')"
                            class="text-base-content/50 hover:text-base-content transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </Link>
                        <Link v-else :href="route('login')" class="text-base-content/50 hover:text-base-content transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </Link>
                        <Link
                            v-if="cartEnabled"
                            :href="route('cart.index')"
                            class="relative inline-flex h-8 w-8 items-center justify-center text-base-content/50 hover:text-base-content transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 0 0-8 0v4M5 9h14l1 12H4L5 9z" />
                            </svg>
                            <span
                                data-cart-count-badge
                                class="absolute top-0 right-0 h-4 min-w-4 translate-x-1/3 -translate-y-1/3 items-center justify-center rounded-full bg-primary px-1 text-[9px] font-bold leading-none text-white shadow-sm"
                                :class="navCartCount > 0 ? 'flex' : 'hidden'"
                                >{{ navCartCount > 0 ? navCartCount : '' }}</span
                            >
                        </Link>
                        <a
                            v-if="whatsappUrl"
                            :href="whatsappUrl"
                            target="_blank"
                            class="text-[10px] uppercase tracking-[0.18em] font-bold bg-neutral text-neutral-content px-4 py-2.5 hover:bg-primary hover:text-primary-content transition-colors whitespace-nowrap"
                            >{{ t.nav_order_wa }}</a
                        >

                        <div class="flex items-center gap-1">
                            <div ref="localeRoot" class="relative">
                                <button
                                    type="button"
                                    class="text-[10px] uppercase tracking-[0.14em] text-base-content/50 hover:text-base-content transition-colors flex items-center gap-0.5"
                                    @click.stop="localeOpen = !localeOpen"
                                >
                                    {{ String($page.props.locale || 'en').toUpperCase() }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </button>
                                <div
                                    v-show="localeOpen"
                                    class="absolute right-0 top-full mt-1 z-50 bg-base-100 border border-base-300 shadow-lg min-w-[90px]"
                                >
                                    <form v-for="(label, code) in { id: t.locale_id, en: t.locale_en }" :key="code" method="POST" :action="route('preferences.locale')">
                                        <input type="hidden" name="_token" :value="csrf" />
                                        <input type="hidden" name="locale" :value="code" />
                                        <button
                                            type="submit"
                                            class="w-full text-left px-3 py-2 text-[10px] uppercase tracking-[0.12em] hover:bg-base-200 transition-colors"
                                            :class="$page.props.locale === code ? 'text-primary font-semibold' : 'text-base-content/70'"
                                        >
                                            {{ label }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <span class="text-base-content/20 text-xs">|</span>
                            <div ref="currencyRoot" class="relative">
                                <button
                                    type="button"
                                    class="text-[10px] uppercase tracking-[0.14em] text-base-content/50 hover:text-base-content transition-colors flex items-center gap-0.5"
                                    @click.stop="currencyOpen = !currencyOpen"
                                >
                                    {{ currency }}
                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="m6 9 6 6 6-6" />
                                    </svg>
                                </button>
                                <div
                                    v-show="currencyOpen"
                                    class="absolute right-0 top-full mt-1 z-50 bg-base-100 border border-base-300 shadow-lg min-w-[90px]"
                                >
                                    <form
                                        v-for="(label, code) in currencyOptions"
                                        :key="code"
                                        method="POST"
                                        :action="route('preferences.currency')"
                                    >
                                        <input type="hidden" name="_token" :value="csrf" />
                                        <input type="hidden" name="currency" :value="code" />
                                        <button
                                            type="submit"
                                            class="w-full text-left px-3 py-2 text-[10px] uppercase tracking-[0.12em] hover:bg-base-200 transition-colors"
                                            :class="currency === code ? 'text-primary font-semibold' : 'text-base-content/70'"
                                        >
                                            {{ label }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <div
            class="pointer-events-none fixed right-4 top-24 z-[80] flex w-[min(92vw,360px)] flex-col gap-2"
        >
            <div
                v-for="toast in toasts"
                :key="toast.id"
                class="pointer-events-auto inline-flex items-center gap-2 rounded-lg px-4 py-3 text-xs font-semibold uppercase tracking-widest shadow-lg"
                :class="{
                    'bg-green-600 text-white': toast.type === 'success',
                    'bg-red-600 text-white': toast.type === 'error',
                    'bg-slate-700 text-white': toast.type !== 'success' && toast.type !== 'error',
                }"
            >
                <span>{{ toast.message }}</span>
            </div>
        </div>

        <main class="grow flex w-full flex-col overflow-x-hidden">
            <div
                v-if="flashCartAdded && cartAddedVisible"
                class="pointer-events-none fixed right-4 top-24 z-[70]"
            >
                <div
                    class="inline-flex items-center gap-2 rounded-full bg-success px-4 py-2 text-xs font-semibold uppercase tracking-widest text-success-content shadow-lg"
                >
                    <span>{{ flashCartAdded }}</span>
                </div>
            </div>
            <slot />
        </main>

        <div class="bg-neutral text-neutral-content w-full">
            <footer class="footer p-10 lg:px-16 container mx-auto flex flex-col md:flex-row justify-between gap-8 py-16">
                <aside class="max-w-sm">
                    <div class="display-font text-3xl font-bold text-primary mb-2">Sense of Jewels</div>
                    <p class="text-sm opacity-80 mt-4 leading-relaxed">
                        Handcrafted jewelry with timeless character and artisan detail.<br />
                        Designed for modern heirlooms since 2019.
                    </p>
                </aside>

                <nav class="flex flex-col gap-3">
                    <h6 class="footer-title text-primary opacity-100 mb-2">{{ t.footer_nav_title }}</h6>
                    <a href="#home" class="link link-hover text-neutral-content/80">{{ t.nav_home }}</a>
                    <a href="#collection" class="link link-hover text-neutral-content/80">{{ t.footer_collection }}</a>
                    <a href="#story" class="link link-hover text-neutral-content/80">{{ t.footer_brand_story }}</a>
                    <a href="#contact" class="link link-hover text-neutral-content/80">{{ t.footer_contact_us }}</a>
                </nav>

                <nav class="flex flex-col gap-3">
                    <h6 class="footer-title text-primary opacity-100 mb-2">{{ t.footer_social_title }}</h6>
                    <a href="https://instagram.com/senseofjewels" target="_blank" class="link link-hover flex items-center gap-3 text-neutral-content/80">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                        </svg>
                        {{ t.footer_instagram }}
                    </a>
                    <a v-if="whatsappUrl" :href="whatsappUrl" target="_blank" class="link link-hover flex items-center gap-3 text-neutral-content/80">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"
                            />
                        </svg>
                        {{ t.footer_whatsapp }}
                    </a>
                    <a href="mailto:hello@senseofjewels.id" class="link link-hover flex items-center gap-3 text-neutral-content/80">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="20" height="16" x="2" y="4" rx="2" />
                            <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                        </svg>
                        {{ t.footer_email }}
                    </a>
                </nav>
            </footer>
        </div>

        <div class="bg-neutral text-neutral-content px-10 border-t border-neutral-content/10">
            <div class="footer footer-center p-4 container mx-auto pb-8 pt-8">
                <aside>
                    <p class="text-neutral-content/60">Copyright © {{ new Date().getFullYear() }} — {{ t.footer_rights }}</p>
                </aside>
            </div>
        </div>
    </div>
</template>
