<script setup>
import { computed, onMounted, onUnmounted, ref, watch } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    hero: {
        type: Object,
        required: true,
    },
});

const slides = computed(() => props.hero.slides ?? []);
const cur = ref(0);
let timer = null;

const textPosMap = {
    'top-left': 'justify-content:flex-start;align-items:flex-start;text-align:left',
    'top-center': 'justify-content:flex-start;align-items:center;text-align:center',
    'top-right': 'justify-content:flex-start;align-items:flex-end;text-align:right',
    'middle-left': 'justify-content:center;align-items:flex-start;text-align:left',
    'middle-center': 'justify-content:center;align-items:center;text-align:center',
    'middle-right': 'justify-content:center;align-items:flex-end;text-align:right',
    'bottom-left': 'justify-content:flex-end;align-items:flex-start;text-align:left',
    'bottom-center': 'justify-content:flex-end;align-items:center;text-align:center',
    'bottom-right': 'justify-content:flex-end;align-items:flex-end;text-align:right',
};

const overlayFlexStyle = computed(() => {
    const slide = slides.value[cur.value];
    const pos = (slide && slide.text_position) || 'top-left';

    return `z-index:2;${textPosMap[pos] || textPosMap['top-left']}`;
});

function restartCarousel() {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
    if (slides.value.length > 1) {
        timer = setInterval(() => {
            cur.value = (cur.value + 1) % slides.value.length;
        }, 5000);
    }
}

watch(
    () => slides.value.length,
    () => {
        cur.value = 0;
        restartCarousel();
    },
);

onMounted(() => {
    restartCarousel();
});

onUnmounted(() => {
    if (timer) {
        clearInterval(timer);
    }
});

const b1 = computed(() => props.hero.banner1 ?? {});
const b2 = computed(() => props.hero.banner2 ?? {});
</script>

<template>
    <section id="home" class="grid grid-cols-1 md:grid-cols-5 min-h-[92vh] gap-2 p-2 md:p-3">
        <div class="md:col-span-3 relative bg-base-300 min-h-[60vh] md:min-h-0 overflow-hidden group">
            <template v-for="(slide, idx) in slides" :key="`${slide.image}-${idx}`">
                <div class="absolute inset-0 transition-opacity duration-1000" :style="{ opacity: cur === idx ? 1 : 0 }">
                    <img
                        :src="slide.image"
                        class="h-full w-full"
                        :style="{
                            objectFit: 'cover',
                            objectPosition: `${slide.focus_x || 50}% ${slide.focus_y || 50}%`,
                            transform: `scale(${(slide.zoom || 100) / 100})`,
                            transformOrigin: 'center',
                        }"
                        alt=""
                    />
                </div>
            </template>

            <template v-if="slides.length === 0">
                <div class="absolute inset-0 bg-gradient-to-br from-neutral via-neutral/70 to-base-300/60"></div>
                <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_70%,rgba(191,160,84,0.15),transparent_60%)]"></div>
            </template>

            <div class="absolute inset-0 bg-black/40" style="z-index: 1"></div>

            <div
                v-if="slides.length > 1"
                style="position: absolute; right: 20px; bottom: 20px; z-index: 3; display: flex; align-items: center; gap: 6px; padding: 8px 10px; border-radius: 9999px; background: rgba(0, 0, 0, 0.18); backdrop-filter: blur(4px)"
            >
                <span
                    v-for="(_, dotIdx) in slides"
                    :key="dotIdx"
                    role="button"
                    tabindex="0"
                    :style="
                        cur === dotIdx
                            ? 'width:18px;height:8px;background:#ffffff;opacity:1;border:1px solid #ffffff;border-radius:9999px;transition:all 0.3s;cursor:pointer;display:inline-block;flex:0 0 auto;box-shadow:0 1px 4px rgba(0,0,0,0.25)'
                            : 'width:8px;height:8px;background:rgba(255,255,255,0.38);opacity:0.95;border:1px solid rgba(255,255,255,0.72);border-radius:9999px;transition:all 0.3s;cursor:pointer;display:inline-block;flex:0 0 auto;box-shadow:0 1px 4px rgba(0,0,0,0.25)'
                    "
                    @click="cur = dotIdx"
                    @keydown.enter.prevent="cur = dotIdx"
                    @keydown.space.prevent="cur = dotIdx"
                ></span>
            </div>

            <div class="absolute inset-0 flex flex-col p-6 md:p-8 lg:p-12" :style="overlayFlexStyle">
                <div v-if="hero.season_badge" style="display: inline-flex; align-items: center; gap: 6px; margin-bottom: 2rem">
                    <span style="display: block; width: 24px; height: 1px; background: rgba(255, 255, 255, 0.5)"></span>
                    <span class="text-white uppercase tracking-[0.3em]" style="font-size: 10px; opacity: 0.85">{{ hero.season_badge }}</span>
                </div>
                <p v-if="hero.eyebrow" class="text-white/70 text-[11px] uppercase tracking-[0.25em] mb-3">{{ hero.eyebrow }}</p>
                <h1
                    v-show="slides[cur] && slides[cur].title"
                    class="display-font text-white leading-tight mb-4"
                    style="font-size: clamp(2rem, 4vw, 3.5rem)"
                >
                    <span>{{ slides[cur]?.title }}</span
                    ><br />
                    <span v-show="slides[cur] && slides[cur].subtitle" class="italic font-light">{{ slides[cur]?.subtitle }}</span>
                </h1>
                <p
                    v-show="slides[cur] && slides[cur].description"
                    class="text-white/70 text-sm mb-6 font-light leading-relaxed"
                    style="max-width: 22rem"
                >
                    {{ slides[cur]?.description }}
                </p>
                <a
                    v-show="slides[cur] && slides[cur].cta_text"
                    :href="(slides[cur] && slides[cur].cta_url) || route('shop.index')"
                    class="inline-block border border-white/70 text-white text-[10px] uppercase tracking-[0.25em] px-6 py-3 hover:bg-white hover:text-neutral transition-all duration-300"
                >
                    {{ slides[cur]?.cta_text }}
                </a>
            </div>
        </div>

        <div class="md:col-span-2 flex flex-col gap-2">
            <div
                class="relative flex-1 min-h-[40vh] md:min-h-0 bg-base-200 overflow-hidden group cursor-pointer"
                :style="b1.image ? { backgroundImage: `url('${b1.image}')`, backgroundSize: 'cover', backgroundPosition: 'center' } : {}"
            >
                <template v-if="!b1.image">
                    <div class="absolute inset-0 bg-gradient-to-br from-base-200 via-[#e8d5b7] to-base-300 group-hover:scale-105 transition-transform duration-700"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_70%_30%,rgba(191,160,84,0.12),transparent_50%)]"></div>
                </template>
                <template v-else>
                    <div class="absolute inset-0 bg-black/30 group-hover:bg-black/20 transition-colors duration-300"></div>
                </template>

                <div class="absolute inset-0 flex flex-col p-8" :style="`z-index:10;${b1.text_position_css || ''}`">
                    <p v-if="b1.label" :class="b1.image ? 'text-white/60' : 'text-base-content/50'" class="text-[10px] uppercase tracking-[0.25em] mb-2">
                        {{ b1.label }}
                    </p>
                    <h2 v-if="b1.title" :class="b1.image ? 'text-white' : 'text-base-content'" class="display-font text-3xl md:text-4xl leading-tight mb-6">
                        {{ b1.title }}<br />
                        <span :class="b1.image ? 'text-amber-300' : 'text-primary'" class="italic font-light">{{ b1.subtitle }}</span>
                    </h2>
                    <a
                        v-if="b1.cta_text"
                        :href="b1.cta_url"
                        :class="
                            b1.image
                                ? 'border-white/60 text-white hover:bg-white hover:text-neutral'
                                : 'border-base-content/60 text-base-content hover:bg-base-content hover:text-base-100'
                        "
                        class="inline-block border text-[10px] uppercase tracking-[0.2em] px-6 py-2.5 w-fit transition-all duration-300"
                    >
                        {{ b1.cta_text }}
                    </a>
                </div>
            </div>

            <div
                class="relative flex-1 min-h-[30vh] md:min-h-0 bg-base-300 overflow-hidden group cursor-pointer"
                :style="b2.image ? { backgroundImage: `url('${b2.image}')`, backgroundSize: 'cover', backgroundPosition: 'center' } : {}"
            >
                <template v-if="!b2.image">
                    <div class="absolute inset-0 bg-gradient-to-tl from-neutral/40 via-base-300 to-base-200 group-hover:scale-105 transition-transform duration-700"></div>
                    <div class="absolute inset-0 bg-[radial-gradient(circle_at_30%_80%,rgba(143,175,159,0.18),transparent_55%)]"></div>
                </template>
                <template v-else>
                    <div class="absolute inset-0 bg-black/30 group-hover:bg-black/20 transition-colors duration-300"></div>
                </template>

                <div class="absolute inset-0 flex flex-col p-8" :style="`z-index:10;${b2.text_position_css || ''}`">
                    <p v-if="b2.label" :class="b2.image ? 'text-white/60' : 'text-base-content/50'" class="text-[10px] uppercase tracking-[0.25em] mb-2">
                        {{ b2.label }}
                    </p>
                    <h2 v-if="b2.title" :class="b2.image ? 'text-white' : 'text-base-content'" class="display-font text-3xl md:text-4xl leading-tight">
                        {{ b2.title }}<br />
                        <span :class="b2.image ? 'text-amber-300' : 'text-primary'" class="italic font-light">{{ b2.subtitle }}</span>
                    </h2>
                    <a
                        v-if="b2.cta_text"
                        :href="b2.cta_url"
                        :class="
                            b2.image
                                ? 'border-white/60 text-white hover:bg-white hover:text-neutral'
                                : 'border-base-content/60 text-base-content hover:bg-base-content hover:text-base-100'
                        "
                        class="inline-block mt-6 border text-[10px] uppercase tracking-[0.2em] px-6 py-2.5 w-fit transition-all duration-300"
                    >
                        {{ b2.cta_text }}
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>
