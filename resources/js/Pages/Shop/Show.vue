<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed, ref, watch, watchEffect } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();

const t = computed(() => page.props.translations ?? {});
const p = computed(() => page.props.product ?? {});
const relatedProducts = computed(() => page.props.relatedProducts ?? []);
const cartEnabled = computed(() => page.props.cartEnabled ?? true);
const productWhatsappUrl = computed(() => page.props.productWhatsappUrl ?? null);

const mainSrc = ref('');
const quantity = ref(1);

const maxQty = computed(() => {
    if (!p.value.inventory_enabled) return 999;
    return Math.max(1, p.value.stock || 0);
});

watchEffect(() => {
    mainSrc.value = p.value.main_image_url || (p.value.gallery?.[0]?.url ?? '');
});

watch(
    () => p.value.slug,
    () => {
        quantity.value = 1;
    },
);

function selectThumb(url) {
    mainSrc.value = url;
}

function decQty() {
    if (quantity.value > 1) quantity.value -= 1;
}

function incQty() {
    if (quantity.value < maxQty.value) quantity.value += 1;
}

watch(quantity, (q) => {
    const n = Number(q);
    if (Number.isNaN(n) || n < 1) {
        quantity.value = 1;
        return;
    }
    if (n > maxQty.value) quantity.value = maxQty.value;
});
</script>

<template>
    <Head :title="`${p.name} — Sense of Jewels`" />

    <AppLayout>
        <div class="container mx-auto px-4 sm:px-6 lg:px-12 max-w-[1400px] pb-32">
            <div class="py-6 border-b border-base-200 mb-10">
                <nav class="text-[11px] uppercase tracking-widest text-base-content/50 flex items-center gap-2">
                    <Link :href="route('home')" class="hover:text-base-content transition-colors">{{ t.home }}</Link>
                    <span>/</span>
                    <Link :href="route('shop.index')" class="hover:text-base-content transition-colors">{{ t.shop }}</Link>
                    <span>/</span>
                    <span class="text-base-content">{{ p.name }}</span>
                </nav>
            </div>

            <div class="grid grid-cols-1 gap-8 lg:grid-cols-12 lg:gap-6 xl:gap-8 mb-20">
                <div class="lg:col-span-8 space-y-4">
                    <div class="aspect-[4/5] min-h-[420px] md:min-h-[560px] bg-base-100 border border-base-300 overflow-hidden relative w-full">
                        <img v-if="mainSrc" :src="mainSrc" :alt="p.name" class="block h-full w-full object-cover object-center" />
                        <div v-else class="absolute inset-0 flex flex-col items-center justify-center gap-3 bg-base-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="44" height="44" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1" class="text-base-content/20">
                                <rect width="18" height="18" x="3" y="3" rx="2" />
                                <circle cx="9" cy="9" r="2" />
                                <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
                            </svg>
                            <span class="text-base-content/30 text-[10px] uppercase tracking-[0.3em]">{{ t.no_image }}</span>
                        </div>
                        <div v-if="p.is_featured" class="absolute top-4 left-4 z-10">
                            <span class="bg-base-100/90 px-3 py-1 text-[9px] uppercase tracking-widest text-base-content">{{ t.featured }}</span>
                        </div>
                    </div>

                    <div v-if="p.gallery?.length" class="flex gap-3 overflow-x-auto pb-1">
                        <button
                            v-for="(img, idx) in p.gallery"
                            :key="idx"
                            type="button"
                            class="thumb-btn shrink-0 w-20 h-20 md:w-24 md:h-24 border overflow-hidden bg-base-100 transition-colors"
                            :class="
                                mainSrc === img.url
                                    ? 'border-base-content shadow-[inset_0_0_0_1px_rgba(44,26,14,0.25)]'
                                    : 'border-base-300 hover:border-base-content/60'
                            "
                            @click="selectThumb(img.url)"
                        >
                            <img :src="img.url" :alt="p.name" class="block w-full h-full object-cover" />
                        </button>
                    </div>
                </div>

                <div class="lg:col-span-4 flex flex-col gap-6 py-2">
                    <p v-if="p.category_name" class="text-[10px] uppercase tracking-[0.3em] text-base-content/50">{{ p.category_name }}</p>
                    <h1 class="display-font text-4xl md:text-5xl text-base-content leading-tight">{{ p.name }}</h1>

                    <div class="flex items-baseline gap-3 border-y border-base-200 py-5">
                        <template v-if="p.has_discount">
                            <span class="display-font text-3xl text-error">{{ p.discounted_price_formatted }}</span>
                            <span class="display-font text-xl text-base-content/40 line-through">{{ p.price_formatted }}</span>
                            <span class="bg-error text-error-content text-[9px] font-bold uppercase tracking-widest px-2 py-1">-{{ p.discount_percent }}%</span>
                        </template>
                        <template v-else>
                            <span class="display-font text-3xl text-base-content">{{ p.price_formatted }}</span>
                        </template>
                        <span v-if="p.weight" class="text-[11px] text-base-content/40 uppercase tracking-widest">{{ p.weight }} g</span>
                    </div>
                    <p v-if="p.has_discount && p.save_formatted" class="text-[11px] text-success uppercase tracking-widest -mt-3">{{ t.save }} {{ p.save_formatted }}</p>

                    <p v-if="p.short_description" class="text-base-content/65 font-light leading-relaxed text-sm md:text-base">{{ p.short_description }}</p>

                    <div class="border border-base-200 bg-base-100 p-4 space-y-4">
                        <div class="flex items-center gap-2">
                            <template v-if="!p.inventory_enabled">
                                <span class="inline-block w-2 h-2 rounded-full bg-success"></span>
                                <span class="text-[11px] uppercase tracking-widest text-base-content/60">{{ t.inventory_off }} · {{ t.ready_to_order }}</span>
                            </template>
                            <template v-else-if="p.stock > 0">
                                <span class="inline-block w-2 h-2 rounded-full bg-success"></span>
                                <span class="text-[11px] uppercase tracking-widest text-base-content/60">{{ t.in_stock }} ({{ p.stock }})</span>
                            </template>
                            <template v-else>
                                <span class="inline-block w-2 h-2 rounded-full bg-error"></span>
                                <span class="text-[11px] uppercase tracking-widest text-error/70">{{ t.out_of_stock }}</span>
                            </template>
                        </div>

                        <form v-if="p.can_purchase && cartEnabled" method="POST" :action="route('cart.store')" class="space-y-3 js-add-to-cart-form">
                            <input type="hidden" name="_token" :value="page.props.csrf" />
                            <input type="hidden" name="product_id" :value="p.id" />
                            <div class="flex flex-col gap-3 sm:flex-row">
                                <div class="flex border border-base-300 w-full sm:w-32 bg-base-100">
                                    <button type="button" class="px-4 py-3 text-base-content/60 hover:text-base-content text-lg leading-none transition-colors" @click="decQty">−</button>
                                    <input
                                        v-model.number="quantity"
                                        type="number"
                                        name="quantity"
                                        min="1"
                                        :max="p.inventory_enabled ? p.stock : undefined"
                                        class="w-full text-center bg-transparent text-sm border-x border-base-300 focus:outline-none"
                                    />
                                    <button type="button" class="px-4 py-3 text-base-content/60 hover:text-base-content text-lg leading-none transition-colors" @click="incQty">+</button>
                                </div>
                                <button
                                    type="submit"
                                    class="js-add-to-cart-btn inline-flex w-full items-center justify-center gap-2 bg-primary px-6 py-3 text-[11px] font-semibold uppercase tracking-widest text-white transition-colors hover:bg-base-content hover:text-base-100 sm:flex-1"
                                >
                                    <svg class="js-add-to-cart-spinner hidden h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-opacity="0.25" stroke-width="3"></circle>
                                        <path d="M22 12a10 10 0 0 0-10-10" stroke="currentColor" stroke-width="3" stroke-linecap="round"></path>
                                    </svg>
                                    <span class="js-add-to-cart-label">{{ t.add_to_cart }}</span>
                                </button>
                            </div>
                            <div class="js-add-to-cart-error hidden rounded-md bg-red-50 px-3 py-2 text-[11px] font-semibold text-red-700"></div>
                        </form>
                        <a
                            v-else-if="p.can_purchase && productWhatsappUrl"
                            :href="productWhatsappUrl"
                            target="_blank"
                            class="block w-full bg-primary text-center text-white px-6 py-3 uppercase tracking-widest text-[11px] font-semibold hover:bg-base-content hover:text-base-100 transition-colors"
                            >{{ t.order_via_whatsapp }}</a
                        >
                        <button v-else-if="p.can_purchase" disabled type="button" class="w-full border border-base-300 py-3 uppercase tracking-widest text-[11px] text-base-content/40 cursor-not-allowed">
                            {{ t.whatsapp_not_configured }}
                        </button>
                        <button v-else disabled type="button" class="w-full border border-base-300 py-3 uppercase tracking-widest text-[11px] text-base-content/40 cursor-not-allowed">
                            {{ t.out_of_stock }}
                        </button>

                        <p class="text-[10px] text-base-content/35 uppercase tracking-widest">{{ t.sku }}: {{ p.sku }}</p>
                    </div>
                </div>
            </div>

            <div class="mb-24 border-t border-base-200 pt-12 space-y-12">
                <section class="grid gap-6 lg:grid-cols-[180px,minmax(0,1fr)] lg:gap-10">
                    <div>
                        <h2 class="text-[11px] uppercase tracking-widest text-base-content/50">{{ t.description }}</h2>
                    </div>
                    <div>
                        <div
                            v-if="p.has_description"
                            class="max-w-3xl text-base-content/72 font-light text-sm md:text-base leading-8 [&_p]:mb-4 [&_p:last-child]:mb-0 [&_ul]:mb-4 [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:mb-4 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mb-2 [&_strong]:font-semibold [&_em]:italic"
                            v-html="p.description_html"
                        ></div>
                        <p v-else class="text-base-content/40 text-sm font-light">{{ t.no_description }}</p>
                    </div>
                </section>

                <section v-if="p.specifications?.length" class="grid gap-6 border-t border-base-200 pt-10 lg:grid-cols-[180px,minmax(0,1fr)] lg:gap-10">
                    <div>
                        <h2 class="text-[11px] uppercase tracking-widest text-base-content/50">{{ t.specifications }}</h2>
                    </div>
                    <div class="max-w-3xl border border-base-200 bg-base-100">
                        <div
                            v-for="(row, idx) in p.specifications"
                            :key="idx"
                            class="grid grid-cols-[minmax(0,180px),1fr] gap-4 border-b border-base-200 px-5 py-4 last:border-b-0 max-sm:grid-cols-1 max-sm:gap-2"
                        >
                            <span class="text-[11px] uppercase tracking-widest text-base-content/45">{{ row.label }}</span>
                            <span class="text-sm leading-7 text-base-content/80">{{ row.value }}</span>
                        </div>
                    </div>
                </section>
            </div>

            <div v-if="relatedProducts.length" class="border-t border-base-200 pt-16">
                <div class="flex justify-between items-end mb-12">
                    <div>
                        <span class="block text-primary uppercase tracking-[0.25em] text-[10px] mb-3">{{ t.you_may_also_like }}</span>
                        <h2 class="display-font text-3xl md:text-4xl text-base-content">{{ t.related_products }}</h2>
                    </div>
                    <Link :href="route('shop.index')" class="uppercase tracking-widest text-xs font-semibold text-base-content hover:text-primary border-b border-base-content hover:border-primary transition-all pb-1 hidden md:block">{{
                        t.view_all
                    }}</Link>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-5 md:gap-8">
                    <Link v-for="rel in relatedProducts" :key="rel.slug" :href="route('shop.show', rel.slug)" class="group cursor-pointer">
                        <div class="aspect-[3/4] bg-base-200 mb-4 relative overflow-hidden border border-base-300 group-hover:border-primary/40 transition-colors">
                            <img v-if="rel.image_url" :src="rel.image_url" :alt="rel.name" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                            <div v-else class="absolute inset-0 bg-gradient-to-t from-base-300/50 to-base-200"></div>
                            <div class="absolute bottom-0 left-0 right-0 p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-400 z-20">
                                <span class="block w-full bg-base-100/95 text-base-content py-2.5 uppercase tracking-widest text-[9px] font-semibold text-center">{{ t.view_product }}</span>
                            </div>
                        </div>
                        <div class="text-center px-1">
                            <h3 class="display-font text-lg text-base-content mb-1 group-hover:text-primary transition-colors">{{ rel.name }}</h3>
                            <p v-if="rel.discounted_price_formatted" class="text-[11px] tracking-widest">
                                <span class="text-error">{{ rel.discounted_price_formatted }}</span>
                                <span class="text-base-content/40 line-through ml-1">{{ rel.price_formatted }}</span>
                                <span v-if="rel.discount_percent" class="ml-1 bg-error text-error-content text-[9px] font-bold px-1.5 py-0.5">-{{ rel.discount_percent }}%</span>
                            </p>
                            <p v-else class="text-[11px] text-base-content/60 tracking-widest">{{ rel.price_formatted }}</p>
                        </div>
                    </Link>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
