<script setup>
import { route } from 'ziggy-js';
import { ref, watch } from 'vue';
import { xsrfToken } from '@/utils/xsrf';

const props = defineProps({
    modelValue: { type: Array, default: () => [] },
    /** Label for first thumbnail slot (e.g. "Utama" vs "Utama Baru") */
    primaryLabel: { type: String, default: 'Utama' },
    error: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const open = ref(false);
const loading = ref(false);
const mediaItems = ref([]);
const query = ref('');
let queryTimer = null;

function syncQuery() {
    clearTimeout(queryTimer);
    queryTimer = setTimeout(() => {
        load();
    }, 300);
}

watch(query, () => {
    if (open.value) {
        syncQuery();
    }
});

watch(open, (v) => {
    if (v) {
        load();
    }
});

async function load() {
    loading.value = true;
    try {
        const url = `${route('admin.media.json')}?q=${encodeURIComponent(query.value)}`;
        const res = await fetch(url, {
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-XSRF-TOKEN': xsrfToken(),
            },
        });
        mediaItems.value = await res.json();
    } catch {
        mediaItems.value = [];
    } finally {
        loading.value = false;
    }
}

function pick(url) {
    if (props.modelValue.includes(url)) {
        return;
    }
    if (props.modelValue.length >= 8) {
        return;
    }
    emit('update:modelValue', [...props.modelValue, url]);
}

function remove(index) {
    const next = [...props.modelValue];
    next.splice(index, 1);
    emit('update:modelValue', next);
}

function galleryCaption(index) {
    if (index === 0) {
        return props.primaryLabel;
    }

    return `Galeri ${index}`;
}
</script>

<template>
    <div class="space-y-3">
        <div class="flex flex-wrap gap-2">
            <div v-for="(url, index) in modelValue" :key="url" class="relative h-20 w-20 overflow-hidden rounded border border-base-300">
                <img :src="url" class="h-full w-full object-cover" alt="" />
                <button
                    type="button"
                    class="absolute right-1 top-1 flex h-5 w-5 items-center justify-center rounded-full bg-error text-xs leading-none text-white"
                    @click="remove(index)"
                >
                    &times;
                </button>
                <div class="absolute bottom-0 left-0 right-0 bg-black/55 py-0.5 text-center text-[9px] text-white">
                    {{ galleryCaption(index) }}
                </div>
            </div>
            <div
                v-if="!modelValue.length"
                class="flex h-20 w-20 items-center justify-center rounded border border-dashed border-base-300 text-[9px] uppercase tracking-widest text-base-content/40"
            >
                No Img
            </div>
        </div>

        <button type="button" class="btn btn-outline btn-sm" @click="open = true">Pilih dari Media Library</button>
        <p v-if="error" class="text-sm text-error">{{ error }}</p>

        <div v-show="open" class="fixed inset-0 z-[60] flex items-center justify-center bg-black/70">
            <div class="flex max-h-[90vh] w-full max-w-4xl flex-col bg-base-100 shadow-2xl">
                <div class="flex items-center justify-between border-b border-base-300 px-6 py-4">
                    <h3 class="display-font text-2xl">Media Library</h3>
                    <div class="flex items-center gap-3">
                        <input
                            v-model="query"
                            type="text"
                            placeholder="Search..."
                            class="w-44 border-b border-base-content/20 bg-transparent py-1.5 text-xs focus:border-primary focus:outline-none"
                        />
                        <button type="button" class="text-xl text-base-content/50 hover:text-base-content" @click="open = false">&times;</button>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-4">
                    <div v-if="loading" class="py-12 text-center text-sm text-base-content/50">Loading...</div>
                    <div class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-6">
                        <button
                            v-for="item in mediaItems"
                            :key="item.id"
                            type="button"
                            class="relative aspect-square overflow-hidden bg-base-200 transition-all hover:opacity-80"
                            :class="modelValue.includes(item.url) ? 'ring-2 ring-primary ring-offset-1' : ''"
                            @click="pick(item.url)"
                        >
                            <img v-show="item.is_image" :src="item.url" :alt="item.alt" class="h-full w-full object-cover" />
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
