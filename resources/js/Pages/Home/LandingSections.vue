<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed, onMounted, reactive, ref, watch } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();

const t = computed(() => page.props.translations ?? {});
const about = computed(() => page.props.about);
const services = computed(() => page.props.services ?? []);
const portfolioItems = computed(() => page.props.portfolioItems ?? []);
const promos = computed(() => page.props.promos ?? []);
const newArrivals = computed(() => page.props.newArrivals ?? []);
const featuredProducts = computed(() => page.props.featuredProducts ?? []);
const categories = computed(() => page.props.categories ?? []);
const story = computed(() => page.props.story);
const contact = computed(() => page.props.contact ?? { enabled: false });
const instagramFeedEnabled = computed(() => page.props.instagramFeedEnabled);
const instagramUrl = computed(() => page.props.instagramUrl ?? '#');
const csrf = computed(() => page.props.csrf ?? '');
const errors = computed(() => page.props.errors ?? {});
const old = computed(() => page.props.old ?? {});

const contactForm = reactive({
    name: '',
    email: '',
    subject: '',
    message: '',
});

watch(
    () => page.props.old,
    (o) => {
        contactForm.name = (o && o.name) || '';
        contactForm.email = (o && o.email) || '';
        contactForm.subject = (o && o.subject) || '';
        contactForm.message = (o && o.message) || '';
    },
    { deep: true, immediate: true },
);

const promoModalOpen = ref(false);
const promoStorageKey = computed(() => page.props.promoStorageKey ?? '');

const promoModal = computed(() => promos.value[0] ?? null);

const promoGridClass = computed(() => {
    const n = promos.value.length;
    if (n === 1) return 'grid gap-4 grid-cols-1 max-w-2xl mx-auto';
    if (n === 2) return 'grid gap-4 grid-cols-1 md:grid-cols-2';

    return 'grid gap-4 grid-cols-1 md:grid-cols-3';
});

onMounted(() => {
    if (!promoStorageKey.value) return;
    try {
        if (!window.localStorage.getItem(promoStorageKey.value)) {
            promoModalOpen.value = true;
        }
    } catch {
        promoModalOpen.value = true;
    }
});

function closePromoModal() {
    promoModalOpen.value = false;
    try {
        if (promoStorageKey.value) {
            window.localStorage.setItem(promoStorageKey.value, '1');
        }
    } catch {
        //
    }
}
</script>

<template>
    <div>
        <section v-if="about" id="about" class="border-t border-base-200 bg-base-100 py-16 md:py-20">
            <div class="container mx-auto max-w-6xl px-6 lg:px-12">
                <div class="mx-auto max-w-4xl text-center">
                    <span class="mb-3 block text-[10px] uppercase tracking-[0.25em] text-primary">{{ t.about_us }}</span>
                    <h2 class="display-font text-4xl text-base-content md:text-5xl">{{ about.title }}</h2>
                    <div v-if="about.content" class="prose prose-neutral mx-auto mt-6 max-w-3xl text-base-content/70 prose-p:leading-relaxed" v-html="about.content"></div>
                </div>
            </div>
        </section>

        <section v-if="services.length" id="services" class="border-t border-base-200 bg-base-200 py-20 md:py-24">
            <div class="container mx-auto max-w-7xl px-6 lg:px-12">
                <div class="mb-12 text-center">
                    <span class="mb-3 block text-[10px] uppercase tracking-[0.25em] text-primary">{{ t.our_services }}</span>
                    <h2 class="display-font text-4xl text-base-content md:text-5xl">{{ t.what_we_offer }}</h2>
                </div>
                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <article
                        v-for="(service, idx) in services"
                        :key="idx"
                        class="overflow-hidden border border-base-300 bg-base-100 transition-colors hover:border-primary/40"
                    >
                        <div class="relative aspect-[16/10] bg-base-200">
                            <img v-if="service.image_url" :src="service.image_url" :alt="service.title" class="absolute inset-0 h-full w-full object-cover" />
                            <div v-else class="absolute inset-0 bg-gradient-to-br from-base-200 via-base-300 to-base-200"></div>
                        </div>
                        <div class="space-y-3 p-5">
                            <h3 class="display-font text-2xl text-base-content">{{ service.title }}</h3>
                            <div
                                v-if="service.summary"
                                class="prose prose-sm max-w-none text-base-content/70 prose-p:my-0 prose-p:leading-relaxed"
                                v-html="service.summary"
                            ></div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <section v-if="portfolioItems.length" id="portfolio" class="border-t border-base-200 bg-base-100 py-20 md:py-24">
            <div class="container mx-auto max-w-7xl px-6 lg:px-12">
                <div class="mb-12 text-center">
                    <span class="mb-3 block text-[10px] uppercase tracking-[0.25em] text-primary">{{ t.portfolio }}</span>
                    <h2 class="display-font text-4xl text-base-content md:text-5xl">{{ t.selected_works }}</h2>
                </div>
                <div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
                    <article
                        v-for="(item, idx) in portfolioItems"
                        :key="idx"
                        class="group overflow-hidden border border-base-300 bg-base-100 transition-colors hover:border-primary/40"
                    >
                        <div class="relative aspect-[4/3] bg-base-200">
                            <img
                                v-if="item.image_url"
                                :src="item.image_url"
                                :alt="item.title"
                                class="absolute inset-0 h-full w-full object-cover transition-transform duration-500 group-hover:scale-105"
                            />
                            <div v-else class="absolute inset-0 bg-gradient-to-br from-base-200 via-base-300 to-base-200"></div>
                        </div>
                        <div class="space-y-2 p-5">
                            <h3 class="display-font text-2xl text-base-content">{{ item.title }}</h3>
                            <p v-if="item.category" class="text-[11px] uppercase tracking-[0.2em] text-base-content/55">{{ item.category }}</p>
                            <div
                                v-if="item.description"
                                class="prose prose-sm max-w-none text-base-content/70 prose-p:my-0 prose-p:leading-relaxed"
                                v-html="item.description"
                            ></div>
                        </div>
                    </article>
                </div>
            </div>
        </section>

        <template v-if="promos.length">
            <Teleport to="body">
                <div
                    v-show="promoModalOpen && promoModal"
                    class="fixed inset-0 z-[2147483647] flex items-center justify-center bg-black/70 p-4 isolation-isolate"
                    style="z-index: 2147483647"
                    @click.self="closePromoModal()"
                    @keydown.escape.window="closePromoModal()"
                >
                    <div
                        class="relative z-[2147483647] w-full max-w-2xl overflow-hidden border border-base-300 bg-base-100 shadow-2xl"
                        style="z-index: 2147483647"
                        @click.stop
                    >
                        <div class="grid md:grid-cols-2">
                            <div class="relative min-h-64 bg-neutral">
                                <img
                                    v-if="promoModal.image_url"
                                    :src="promoModal.image_url"
                                    :alt="promoModal.code"
                                    class="absolute inset-0 h-full w-full object-cover"
                                />
                                <div v-else class="absolute inset-0 bg-gradient-to-br from-neutral via-neutral/85 to-base-300"></div>
                                <div v-if="promoModal.image_url" class="absolute inset-0 bg-black/35"></div>
                                <div class="relative z-10 flex h-full items-end p-6">
                                    <span class="bg-primary/90 px-3 py-1 text-[10px] uppercase tracking-[0.2em] text-primary-content">{{ t.special_offer }}</span>
                                </div>
                            </div>
                            <div class="flex flex-col justify-center p-6 md:p-8">
                                <p class="text-[10px] uppercase tracking-[0.25em] text-primary">{{ t.promo_code }}</p>
                                <h3 class="mt-2 display-font text-4xl text-base-content">{{ promoModal.code }}</h3>
                                <p v-if="promoModal.discount_label" class="mt-2 text-sm text-base-content/80">{{ promoModal.discount_label }}</p>
                                <p v-if="promoModal.description" class="mt-3 text-sm leading-relaxed text-base-content/65">{{ promoModal.description }}</p>
                                <div class="mt-5 space-y-1 text-[11px] text-base-content/55">
                                    <p v-if="promoModal.ends_at">{{ t.valid_until }} {{ promoModal.ends_at }}</p>
                                    <p v-if="promoModal.minimum_order_amount > 0">{{ t.minimum_order }} {{ promoModal.minimum_order_formatted }}</p>
                                </div>
                                <div class="mt-6 flex items-center gap-3">
                                    <Link :href="route('shop.index')" class="inline-flex items-center border border-base-content/25 px-5 py-2.5 text-[11px] uppercase tracking-[0.18em] text-base-content hover:border-primary hover:text-primary transition-colors" @click="closePromoModal">{{
                                        t.shop_now
                                    }}</Link>
                                    <button type="button" class="text-[11px] uppercase tracking-[0.18em] text-base-content/50 hover:text-base-content" @click="closePromoModal">
                                        {{ t.maybe_later }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </Teleport>

            <section class="py-10 md:py-14 bg-base-100 border-t border-base-200">
                <div class="container mx-auto px-6 lg:px-12 max-w-7xl">
                    <div class="text-center mb-8">
                        <span class="block text-primary uppercase tracking-[0.25em] text-[10px] mb-2">{{ t.special_offers }}</span>
                        <h2 class="display-font text-3xl md:text-4xl text-base-content">{{ t.active_promotions }}</h2>
                    </div>
                    <div :class="promoGridClass">
                        <Link
                            v-for="promo in promos"
                            :key="promo.id"
                            :href="route('shop.index')"
                            class="group relative overflow-hidden block min-h-48 md:min-h-56 border border-base-300 hover:border-primary/40 transition-colors duration-300"
                            style="min-height: 190px"
                        >
                            <img
                                v-if="promo.image_url"
                                :src="promo.image_url"
                                :alt="promo.code"
                                class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105"
                            />
                            <template v-if="promo.image_url">
                                <div class="absolute inset-0 bg-gradient-to-r from-black/70 via-black/40 to-transparent"></div>
                            </template>
                            <template v-else>
                                <div class="absolute inset-0 bg-gradient-to-br from-neutral via-neutral/80 to-base-300 transition-transform duration-700 group-hover:scale-105"></div>
                                <div class="absolute inset-0 bg-[radial-gradient(circle_at_20%_60%,rgba(191,160,84,0.25),transparent_55%)]"></div>
                                <div
                                    class="absolute inset-0 opacity-5"
                                    style="background-image: repeating-linear-gradient(45deg, #bfa054 0, #bfa054 1px, transparent 0, transparent 50%); background-size: 20px 20px"
                                ></div>
                                <div class="absolute right-8 top-1/2 -translate-y-1/2 opacity-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" class="w-28 h-28 text-primary fill-current">
                                        <path d="M32 6 L8 22 L14 42 H50 L56 22 Z M32 6 L20 22 H44 Z M14 42 L18 56 H46 L50 42 Z" />
                                    </svg>
                                </div>
                            </template>
                            <div class="absolute inset-0 flex flex-col justify-center px-4 py-4 md:px-8 md:py-6" style="z-index: 10">
                                <span v-if="promo.discount_label" class="mb-1.5 md:mb-2 w-fit bg-primary/90 text-primary-content text-[8px] md:text-[9px] uppercase tracking-[0.15em] md:tracking-[0.2em] px-2 py-1 md:px-3">
                                    {{ promo.discount_label }}
                                </span>
                                <h3 class="display-font text-white text-xl md:text-3xl leading-tight mb-1">{{ promo.code }}</h3>
                                <p v-if="promo.description" class="text-white/70 text-[11px] md:text-xs font-light mt-0.5 md:mt-1 line-clamp-2">{{ promo.description }}</p>
                                <div class="mt-2.5 md:mt-4 flex flex-wrap items-center gap-2 md:gap-3 text-[9px] md:text-[10px] text-white/60 uppercase tracking-[0.12em] md:tracking-widest">
                                    <span class="border border-white/30 px-2 py-1 md:px-3 font-mono tracking-wider text-white/90">{{ promo.code }}</span>
                                    <span v-if="promo.ends_at">{{ t.valid_until }} {{ promo.ends_at }}</span>
                                    <span v-if="promo.minimum_order_amount > 0">{{ t.min_order }} {{ promo.minimum_order_formatted }}</span>
                                </div>
                                <span
                                    class="mt-3 md:mt-5 w-fit border border-white/50 text-white text-[9px] md:text-[10px] uppercase tracking-[0.15em] md:tracking-[0.2em] px-4 py-2 md:px-5 md:py-2.5 group-hover:bg-white group-hover:text-neutral transition-all duration-300"
                                    >{{ t.shop_now }}</span
                                >
                            </div>
                        </Link>
                    </div>
                </div>
            </section>
        </template>

        <section id="collection" class="py-20 md:py-28 bg-base-100 border-t border-base-200">
            <div class="container mx-auto px-6 lg:px-12 max-w-7xl">
                <div class="flex flex-col md:flex-row justify-between items-center md:items-end gap-6 mb-14 text-center md:text-left">
                    <div>
                        <span class="block text-primary uppercase tracking-[0.25em] text-[10px] mb-3">Just Arrived</span>
                        <h2 class="display-font text-4xl md:text-5xl text-base-content">{{ t.new_arrivals }}</h2>
                    </div>
                    <Link :href="route('shop.index')" class="uppercase tracking-widest text-xs font-semibold text-base-content hover:text-primary border-b border-base-content hover:border-primary transition-all pb-1 mx-auto md:mx-0">{{
                        t.view_all
                    }}</Link>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-5 md:gap-8">
                    <template v-if="newArrivals.length">
                        <Link v-for="p in newArrivals" :key="p.slug" :href="route('shop.show', p.slug)" class="group cursor-pointer">
                            <div class="aspect-[3/4] bg-base-200 mb-5 relative overflow-hidden border border-base-300 group-hover:border-primary/40 transition-colors">
                                <img v-if="p.image_url" :src="p.image_url" :alt="p.name" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                                <div v-else class="absolute inset-0 bg-gradient-to-t from-base-300/50 to-base-200 transition-transform duration-700 group-hover:scale-105"></div>
                                <div v-if="p.is_featured" class="absolute top-3 right-3 z-10">
                                    <span class="bg-base-100/90 px-3 py-1 text-[9px] uppercase tracking-widest text-base-content">{{ t.featured_badge }}</span>
                                </div>
                                <div class="absolute bottom-0 left-0 right-0 p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-400 z-20">
                                    <span class="block w-full bg-base-100/95 text-base-content py-2.5 uppercase tracking-widest text-[9px] font-semibold text-center">{{ t.view_product }}</span>
                                </div>
                            </div>
                            <div class="text-center px-1">
                                <h3 class="display-font text-lg text-base-content mb-1 group-hover:text-primary transition-colors">{{ p.name }}</h3>
                                <p v-if="p.discounted_price_formatted" class="text-[11px] tracking-widest">
                                    <span class="text-error">{{ p.discounted_price_formatted }}</span>
                                    <span class="text-base-content/40 line-through">{{ p.price_formatted }}</span>
                                    <span v-if="p.discount_percent" class="bg-error text-error-content text-[9px] font-bold px-1.5 py-0.5">-{{ p.discount_percent }}%</span>
                                </p>
                                <p v-else class="text-[11px] text-base-content/60 tracking-widest">{{ p.price_formatted }}</p>
                            </div>
                        </Link>
                    </template>
                    <p v-else class="col-span-4 text-center text-base-content/40 py-8">{{ t.no_products_yet }}</p>
                </div>
            </div>
        </section>

        <section class="py-20 md:py-28 bg-base-200 border-y border-base-300">
            <div class="container mx-auto px-6 lg:px-12 max-w-7xl">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-end gap-6 mb-12">
                    <div>
                        <span class="block text-primary uppercase tracking-[0.25em] text-[10px] mb-3">Editor's Pick</span>
                        <h2 class="display-font text-4xl md:text-5xl text-base-content">{{ t.featured_products }}</h2>
                        <p class="mt-4 max-w-2xl text-sm md:text-base text-base-content/65 font-light leading-relaxed">{{ t.featured_editor_pick }}</p>
                    </div>
                    <Link :href="route('shop.index')" class="uppercase tracking-widest text-xs font-semibold text-base-content hover:text-primary border-b border-base-content hover:border-primary transition-all pb-1">{{
                        t.explore_collection
                    }}</Link>
                </div>
                <div v-if="featuredProducts.length" class="grid grid-cols-2 md:grid-cols-4 gap-5 md:gap-8">
                    <Link v-for="p in featuredProducts" :key="p.slug" :href="route('shop.show', p.slug)" class="group cursor-pointer">
                        <div class="aspect-[3/4] bg-base-100 mb-5 relative overflow-hidden border border-base-300 group-hover:border-primary/40 transition-colors">
                            <img v-if="p.image_url" :src="p.image_url" :alt="p.name" class="absolute inset-0 w-full h-full object-cover transition-transform duration-700 group-hover:scale-105" />
                            <div v-else class="absolute inset-0 bg-base-100"></div>
                            <div class="absolute top-3 right-3 z-10">
                                <span class="bg-base-100/90 px-3 py-1 text-[9px] uppercase tracking-widest text-base-content">{{ t.featured_badge }}</span>
                            </div>
                            <div class="absolute bottom-0 left-0 right-0 p-3 translate-y-full group-hover:translate-y-0 transition-transform duration-400 z-20">
                                <span class="block w-full bg-base-100/95 text-base-content py-2.5 uppercase tracking-widest text-[9px] font-semibold text-center">{{ t.view_product }}</span>
                            </div>
                        </div>
                        <div class="text-center px-1">
                            <h3 class="display-font text-lg text-base-content mb-1 group-hover:text-primary transition-colors">{{ p.name }}</h3>
                            <p v-if="p.discounted_price_formatted" class="text-[11px] tracking-widest">
                                <span class="text-error">{{ p.discounted_price_formatted }}</span>
                                <span class="text-base-content/40 line-through">{{ p.price_formatted }}</span>
                                <span v-if="p.discount_percent" class="bg-error text-error-content text-[9px] font-bold px-1.5 py-0.5">-{{ p.discount_percent }}%</span>
                            </p>
                            <p v-else class="text-[11px] text-base-content/60 tracking-widest">{{ p.price_formatted }}</p>
                        </div>
                    </Link>
                </div>
                <div v-else class="border border-dashed border-base-300 bg-base-100 px-6 py-16 text-center text-base-content/50">{{ t.no_featured_products }}</div>
            </div>
        </section>

        <section class="py-20 md:py-28 bg-base-100">
            <div class="container mx-auto px-6 lg:px-12 max-w-7xl">
                <div class="text-center mb-16">
                    <span class="block text-accent uppercase tracking-[0.25em] text-[10px] mb-4">{{ t.attractive_jewellery }}</span>
                    <h2 class="display-font text-4xl md:text-5xl text-base-content">{{ t.gorgeous_collections }}</h2>
                </div>
                <div class="grid grid-cols-3 md:grid-cols-6 gap-6 md:gap-8">
                    <template v-if="categories.length">
                        <Link
                            v-for="cat in categories"
                            :key="cat.slug"
                            :href="route('shop.index', { category: cat.slug })"
                            class="group flex flex-col items-center gap-5"
                        >
                            <div
                                class="w-full aspect-square rounded-full overflow-hidden bg-base-200 relative ring-1 ring-base-300 group-hover:ring-primary/50 transition-all duration-300 group-hover:shadow-lg group-hover:shadow-primary/10"
                            >
                                <img
                                    v-if="cat.image_url"
                                    :src="cat.image_url"
                                    :alt="cat.name"
                                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                />
                                <div v-else class="absolute inset-0 bg-gradient-to-br from-base-200 via-base-300 to-[#e0cba8] group-hover:scale-110 transition-transform duration-500"></div>
                                <div v-if="!cat.image_url" class="absolute inset-0 flex items-center justify-center">
                                    <span class="text-base-content/20 tracking-widest text-[9px] uppercase">Photo</span>
                                </div>
                            </div>
                            <span class="uppercase tracking-[0.2em] text-[10px] font-semibold text-base-content/70 group-hover:text-base-content transition-colors">{{ cat.name }}</span>
                        </Link>
                    </template>
                    <p v-else class="col-span-6 text-center text-base-content/40 py-8">{{ t.no_categories_yet }}</p>
                </div>
            </div>
        </section>

        <section v-if="story" id="story" class="py-20 md:py-32 bg-base-200">
            <div class="container mx-auto px-6 lg:px-12 max-w-6xl">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-12 md:gap-16 items-center">
                    <div class="md:col-span-5 space-y-8">
                        <span v-if="story.eyebrow" class="block text-primary uppercase tracking-[0.25em] text-[10px]">{{ story.eyebrow }}</span>
                        <h2 v-if="story.title || story.subtitle" class="display-font text-4xl md:text-5xl text-base-content leading-tight">
                            <template v-if="story.title">{{ story.title }}</template>
                            <template v-if="story.subtitle"><br /><span class="italic font-light">{{ story.subtitle }}</span></template>
                        </h2>
                        <div class="w-12 h-px bg-base-content/20"></div>
                        <p v-if="story.content" class="text-base-content/70 font-light leading-relaxed">{{ story.content }}</p>
                        <a v-if="story.cta_text" :href="story.cta_url" class="inline-block uppercase tracking-widest text-xs font-semibold text-base-content hover:text-primary border-b border-base-content hover:border-primary transition-all pb-1">{{
                            story.cta_text
                        }}</a>
                    </div>
                    <div class="md:col-span-7 relative">
                        <div class="aspect-[4/3] w-full bg-base-300 overflow-hidden flex items-center justify-center">
                            <img v-if="story.main_image" :src="story.main_image" :alt="story.title" class="w-full h-full object-cover" />
                            <span v-else class="text-base-content/30 tracking-[0.3em] uppercase text-sm">Artisan Working</span>
                        </div>
                        <div class="absolute -bottom-6 -left-6 w-2/5 aspect-square bg-base-100 border border-base-200 hidden md:flex items-center justify-center shadow-xl overflow-hidden">
                            <img v-if="story.secondary_image" :src="story.secondary_image" alt="" class="w-full h-full object-cover" />
                            <span v-else class="text-base-content/30 tracking-[0.2em] uppercase text-[10px] text-center px-4">Detail Shot</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section v-if="contact.enabled" id="contact" class="py-20 md:py-28 bg-base-100 border-t border-base-200">
            <div class="container mx-auto px-6 lg:px-12 max-w-7xl">
                <div class="grid gap-8 lg:grid-cols-[1fr,1.15fr]">
                    <div class="space-y-6">
                        <div>
                            <span class="block text-primary uppercase tracking-[0.25em] text-[10px] mb-3">{{ t.get_in_touch }}</span>
                            <h2 class="display-font text-4xl md:text-5xl text-base-content">{{ contact.title }}</h2>
                            <p class="mt-4 text-sm md:text-base text-base-content/65 font-light leading-relaxed">{{ contact.intro }}</p>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <div class="rounded-box border border-base-300 bg-base-200/60 p-4">
                                <div class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-1">{{ t.email }}</div>
                                <a :href="`mailto:${contact.email}`" class="text-sm font-medium break-all hover:text-primary transition-colors">{{ contact.email }}</a>
                            </div>
                            <div class="rounded-box border border-base-300 bg-base-200/60 p-4">
                                <div class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-1">{{ t.phone }}</div>
                                <a :href="`tel:${contact.phone_href}`" class="text-sm font-medium hover:text-primary transition-colors">{{ contact.phone }}</a>
                            </div>
                            <div class="rounded-box border border-base-300 bg-base-200/60 p-4 sm:col-span-2">
                                <div class="text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-1">{{ t.address }}</div>
                                <div class="text-sm font-medium whitespace-pre-line">{{ contact.address }}</div>
                            </div>
                        </div>
                        <a
                            v-if="contact.wa_link"
                            :href="contact.wa_link"
                            target="_blank"
                            rel="noreferrer"
                            class="inline-flex w-fit items-center gap-2 border border-base-content/20 px-4 py-2 text-[10px] uppercase tracking-[0.2em] font-semibold text-base-content hover:border-primary hover:text-primary transition-colors"
                            >{{ t.chat_whatsapp }}</a
                        >
                        <div v-if="contact.maps_src" class="overflow-hidden rounded-box border border-base-300">
                            <iframe :src="contact.maps_src" width="100%" height="250" style="border: 0" allowfullscreen loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>
                    <div class="card border border-base-300 bg-base-100 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title text-xl">{{ t.send_message }}</h3>
                            <form :action="route('contact.store')" method="POST" class="grid gap-3">
                                <input type="hidden" name="_token" :value="csrf" />
                                <div class="form-control">
                                    <input
                                        v-model="contactForm.name"
                                        type="text"
                                        name="name"
                                        :placeholder="t.your_name"
                                        class="input input-bordered w-full"
                                        :class="{ 'input-error': errors.name }"
                                        required
                                    />
                                    <label v-if="errors.name" class="label"><span class="label-text-alt text-error">{{ errors.name }}</span></label>
                                </div>
                                <div class="form-control">
                                    <input
                                        v-model="contactForm.email"
                                        type="email"
                                        name="email"
                                        :placeholder="t.your_email"
                                        class="input input-bordered w-full"
                                        :class="{ 'input-error': errors.email }"
                                        required
                                    />
                                    <label v-if="errors.email" class="label"><span class="label-text-alt text-error">{{ errors.email }}</span></label>
                                </div>
                                <div class="form-control">
                                    <input
                                        v-model="contactForm.subject"
                                        type="text"
                                        name="subject"
                                        :placeholder="t.subject"
                                        class="input input-bordered w-full"
                                        :class="{ 'input-error': errors.subject }"
                                        required
                                    />
                                    <label v-if="errors.subject" class="label"><span class="label-text-alt text-error">{{ errors.subject }}</span></label>
                                </div>
                                <div class="form-control">
                                    <textarea
                                        v-model="contactForm.message"
                                        name="message"
                                        rows="5"
                                        :placeholder="t.your_message"
                                        class="textarea textarea-bordered min-h-32 w-full"
                                        :class="{ 'textarea-error': errors.message }"
                                        required
                                    ></textarea>
                                    <label v-if="errors.message" class="label"><span class="label-text-alt text-error">{{ errors.message }}</span></label>
                                </div>
                                <button type="submit" class="btn btn-primary">{{ t.send_message }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section v-if="instagramFeedEnabled" class="py-20 bg-base-200 border-t border-base-300">
            <div class="text-center mb-12">
                <a :href="instagramUrl" target="_blank" rel="noopener noreferrer" class="display-font text-3xl text-base-content hover:text-primary transition-colors">@senseofjewels</a>
            </div>
            <div class="grid grid-cols-3 md:grid-cols-6 gap-0">
                <a
                    v-for="i in 6"
                    :key="i"
                    :href="instagramUrl"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="aspect-square bg-base-300 relative group flex items-center justify-center border-[0.5px] border-base-100"
                >
                    <span class="text-base-content/25 tracking-widest text-[9px] uppercase">Post {{ i }}</span>
                    <div class="absolute inset-0 bg-primary/80 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="text-white">
                            <rect width="20" height="20" x="2" y="2" rx="5" ry="5" />
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                            <line x1="17.5" x2="17.51" y1="6.5" y2="6.5" />
                        </svg>
                    </div>
                </a>
            </div>
        </section>
    </div>
</template>
