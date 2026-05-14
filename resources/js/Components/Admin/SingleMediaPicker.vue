<script setup>
import { route } from 'ziggy-js';
import { ref, watch } from 'vue';
import { xsrfToken } from '@/utils/xsrf';

defineProps({
    label: { type: String, default: 'Gambar' },
    error: { type: String, default: '' },
});

const selected = defineModel({ type: String, default: '' });

const open = ref(false);
const loading = ref(false);
const items = ref([]);
const query = ref('');
let queryTimer = null;

function scheduleLoad() {
    clearTimeout(queryTimer);
    queryTimer = setTimeout(() => load(), 400);
}

watch(open, (v) => {
    if (v) {
        load();
    }
});

watch(query, () => {
    if (open.value) {
        scheduleLoad();
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
        items.value = await res.json();
    } catch {
        items.value = [];
    } finally {
        loading.value = false;
    }
}

function pick(item) {
    selected.value = item.url;
    open.value = false;
}

function clearSelection() {
    selected.value = '';
}
</script>

<template>
    <fieldset class="fieldset">
        <legend class="fieldset-legend">{{ label }}</legend>

        <div class="flex items-start gap-4">
            <div class="flex h-24 w-24 shrink-0 items-center justify-center overflow-hidden border border-base-300 bg-base-200">
                <img v-if="selected" :src="selected" class="h-full w-full object-cover" alt="" />
                <span v-else class="px-1 text-center text-[9px] uppercase tracking-widest text-base-content/30">No Image</span>
            </div>
            <div class="space-y-2 pt-1">
                <button type="button" class="border border-base-content/30 px-3 py-1.5 text-[10px] uppercase tracking-widest transition-colors hover:bg-base-content hover:text-base-100" @click="open = true">
                    Choose from Library
                </button>
                <button v-if="selected" type="button" class="block text-[10px] uppercase tracking-widest text-error hover:underline" @click="clearSelection">Remove</button>
                <p class="max-w-[220px] truncate text-[9px] text-base-content/40">{{ selected || 'No image selected' }}</p>
            </div>
        </div>
        <p v-if="error" class="fieldset-label text-error">{{ error }}</p>

        <div v-show="open" class="fixed inset-0 z-60 flex items-center justify-center bg-black/70">
            <div class="flex max-h-[90vh] w-full max-w-4xl flex-col bg-base-100 shadow-2xl">
                <div class="flex shrink-0 items-center justify-between border-b border-base-300 px-6 py-4">
                    <h3 class="display-font text-2xl">Media Library</h3>
                    <div class="flex items-center gap-3">
                        <input
                            v-model="query"
                            type="text"
                            placeholder="Search..."
                            class="w-40 border-b border-base-content/20 bg-transparent py-1.5 text-xs focus:border-primary focus:outline-none"
                        />
                        <button type="button" class="text-xl leading-none text-base-content/40 hover:text-base-content" @click.prevent.stop="open = false">&times;</button>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-4">
                    <div v-if="loading" class="py-12 text-center text-sm text-base-content/40">Loading…</div>
                    <div v-else-if="!items.length" class="py-12 text-center text-sm text-base-content/40">
                        No media found.
                        <a :href="route('admin.media.index')" target="_blank" class="text-primary underline">Upload media ↗</a>
                    </div>
                    <div v-else class="grid grid-cols-3 gap-2 sm:grid-cols-4 md:grid-cols-6">
                        <button
                            v-for="item in items"
                            :key="item.id"
                            type="button"
                            class="relative aspect-square overflow-hidden bg-base-200 transition-all hover:opacity-80"
                            :class="selected === item.url ? 'ring-2 ring-primary ring-offset-1' : ''"
                            @click="pick(item)"
                        >
                            <img v-if="item.is_image" :src="item.url" :alt="item.alt" class="h-full w-full object-cover" />
                            <span v-else class="flex h-full items-center justify-center p-1 text-[9px] uppercase tracking-widest text-base-content/40">{{ item.original_name }}</span>
                        </button>
                    </div>
                </div>
                <div class="flex shrink-0 items-center justify-between border-t border-base-300 px-6 py-3">
                    <a :href="route('admin.media.index')" target="_blank" class="text-[10px] uppercase tracking-widest text-primary hover:underline">Manage Library ↗</a>
                    <button type="button" class="text-[11px] uppercase tracking-widest text-base-content/50 hover:text-base-content" @click.prevent.stop="open = false">Close</button>
                </div>
            </div>
        </div>
    </fieldset>
</template>
