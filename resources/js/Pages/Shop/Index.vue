<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();

const t = computed(() => page.props.translations ?? {});
const categories = computed(() => page.props.categories ?? []);
const products = computed(() => page.props.products ?? { data: [], links: [] });
const filters = computed(() => page.props.filters ?? { search: null, category: null });
const cartEnabled = computed(() => page.props.cartEnabled ?? true);

const searchInput = ref((filters.value.search ?? '') || '');
let searchTimer = null;

watch(
    filters,
    (f) => {
        searchInput.value = (f.search ?? '') || '';
    },
    { deep: true },
);

function scheduleSearch() {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => {
        router.get(
            route('shop.index'),
            {
                search: searchInput.value.trim() || undefined,
                category: filters.value.category || undefined,
                page: 1,
            },
            { replace: true, preserveScroll: true },
        );
    }, 350);
}

function categoryHref(slug) {
    return route('shop.index', {
        category: slug || undefined,
        search: filters.value.search || undefined,
    });
}

function allHref() {
    return route('shop.index', {
        search: filters.value.search || undefined,
    });
}
</script>

<template>
    <Head :title="`${t.ready_to_wear} — Sense of Jewels`" />

    <AppLayout>
        <div class="container mx-auto px-4 sm:px-6 lg:px-12 max-w-[1400px] pb-40 md:pb-56">
            <div class="py-12 md:py-20 text-center border-b border-base-300 mb-12">
                <h1 class="display-font text-5xl md:text-6xl text-base-content mb-4 tracking-wide">{{ t.ready_to_wear }}</h1>
                <p class="text-base-content/60 max-w-lg mx-auto text-lg font-light">{{ t.subtitle }}</p>
            </div>

            <div class="flex flex-col md:flex-row justify-between items-center mb-12 gap-6 text-sm uppercase tracking-widest">
                <div class="flex items-center gap-8 overflow-x-auto w-full md:w-auto pb-4 md:pb-0 no-scrollbar whitespace-nowrap text-xs font-semibold">
                    <Link
                        :href="allHref()"
                        class="pb-1 transition-colors"
                        :class="!filters.category ? 'text-base-content border-b border-base-content' : 'text-base-content/50 hover:text-base-content'"
                        >{{ t.all }}</Link
                    >
                    <Link
                        v-for="cat in categories"
                        :key="cat.slug"
                        :href="categoryHref(cat.slug)"
                        class="pb-1 transition-colors"
                        :class="filters.category === cat.slug ? 'text-base-content border-b border-base-content' : 'text-base-content/50 hover:text-base-content'"
                        >{{ cat.name }}</Link
                    >
                </div>

                <div class="w-full md:w-64">
                    <div class="relative">
                        <input
                            v-model="searchInput"
                            type="text"
                            class="w-full border-b border-base-content/20 bg-transparent py-2 text-xs placeholder:text-base-content/40 focus:outline-none focus:border-base-content transition-colors"
                            :placeholder="t.search_placeholder"
                            @input="scheduleSearch()"
                        />
                        <span class="pointer-events-none absolute right-0 top-1/2 -translate-y-1/2 text-base-content/40">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <circle cx="11" cy="11" r="8" />
                                <path d="m21 21-4.3-4.3" />
                            </svg>
                        </span>
                    </div>
                </div>
            </div>

            <div v-if="!products.data.length" class="py-32 text-center">
                <span class="block text-4xl mb-4 opacity-20">❍</span>
                <p class="text-base-content/50 text-lg font-light mb-6">{{ t.empty_title }}</p>
                <Link :href="route('shop.index')" class="uppercase tracking-widest text-xs font-semibold text-base-content border-b border-base-content pb-1">{{ t.reset_filters }}</Link>
            </div>

            <template v-else>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-6 gap-y-16">
                    <div v-for="product in products.data" :key="product.id" class="group flex flex-col relative text-center">
                        <div class="aspect-[3/4] bg-base-200 w-full mb-6 block overflow-hidden relative border border-base-300 group-hover:border-primary/40 transition-colors">
                            <Link :href="route('shop.show', product.slug)" class="absolute inset-0 z-10 block" :aria-label="product.name">
                                <span class="sr-only">{{ product.name }}</span>
                            </Link>
                            <img
                                v-if="product.image_url"
                                :src="product.image_url"
                                :alt="product.name"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-1000 group-hover:scale-105"
                            />
                            <div
                                v-else
                                class="absolute inset-0 bg-gradient-to-tr from-base-200 to-base-300 flex items-center justify-center transition-transform duration-1000 group-hover:scale-105"
                            >
                                <span class="text-base-content/30 tracking-[0.2em] text-[10px] uppercase">{{ t.view_details }}</span>
                            </div>
                            <div v-if="product.is_featured" class="absolute top-4 right-4 z-10">
                                <span class="bg-base-100 px-3 py-1 text-[9px] uppercase tracking-widest">{{ t.bestseller }}</span>
                            </div>

                            <div class="absolute bottom-0 left-0 right-0 p-4 opacity-0 group-hover:opacity-100 group-hover:translate-y-0 translate-y-4 transition-all duration-500 z-20">
                                <form v-if="cartEnabled" method="POST" :action="route('cart.store')" class="js-add-to-cart-form">
                                    <input type="hidden" name="_token" :value="page.props.csrf" />
                                    <input type="hidden" name="product_id" :value="product.id" />
                                    <button
                                        type="submit"
                                        class="js-add-to-cart-btn inline-flex w-full items-center justify-center gap-2 bg-base-100 py-3 text-[10px] font-semibold uppercase tracking-widest text-base-content transition-colors hover:bg-neutral hover:text-white"
                                    >
                                        <svg class="js-add-to-cart-spinner hidden h-3.5 w-3.5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                            <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.25" stroke-width="3"></circle>
                                            <path d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                                        </svg>
                                        <span class="js-add-to-cart-label">{{ t.add_to_cart }}</span>
                                    </button>
                                    <div class="js-add-to-cart-error mt-2 hidden rounded-md bg-red-50 px-2 py-1 text-[10px] font-semibold text-red-700"></div>
                                </form>
                                <a
                                    v-else-if="product.whatsapp_url"
                                    :href="product.whatsapp_url"
                                    target="_blank"
                                    class="block w-full bg-base-100 text-base-content py-3 uppercase tracking-widest text-[10px] font-semibold hover:bg-neutral hover:text-white transition-colors"
                                    >{{ t.order_via_whatsapp }}</a
                                >
                                <span v-else class="block w-full bg-base-100 text-base-content/40 py-3 uppercase tracking-widest text-[10px] font-semibold">
                                    {{ t.whatsapp_not_configured }}
                                </span>
                            </div>
                        </div>

                        <div class="flex flex-col grow pt-1 pb-4">
                            <div class="text-[10px] text-base-content/50 uppercase tracking-widest mb-2">{{ product.category_name || t.uncategorized }}</div>
                            <Link :href="route('shop.show', product.slug)" class="display-font text-xl text-base-content group-hover:text-primary transition-colors mb-3">
                                {{ product.name }}
                            </Link>
                            <div class="font-light text-base-content/80 mt-1">
                                <template v-if="product.discounted_price_formatted">
                                    <span class="text-error font-medium">{{ product.discounted_price_formatted }}</span>
                                    <span class="text-xs text-base-content/40 line-through ml-1">{{ product.price_formatted }}</span>
                                    <span v-if="product.discount_percent" class="ml-1 bg-error text-error-content text-[9px] font-bold px-1.5 py-0.5">-{{ product.discount_percent }}%</span>
                                </template>
                                <template v-else>
                                    {{ product.price_formatted }}
                                </template>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="products.last_page > 1" class="mt-24 pt-10 border-t border-base-200 flex justify-center flex-wrap gap-1">
                    <template v-for="(link, idx) in products.links" :key="idx">
                        <Link v-if="link.url" :href="link.url" class="btn btn-sm btn-ghost min-h-8 h-8 px-3" :class="{ 'btn-active btn-primary': link.active }" preserve-scroll>
                            <span v-html="link.label"></span>
                        </Link>
                        <span v-else class="btn btn-sm btn-ghost btn-disabled pointer-events-none min-h-8 h-8 px-3 opacity-50">
                            <span v-html="link.label"></span>
                        </span>
                    </template>
                </div>
            </template>
        </div>
    </AppLayout>
</template>
