<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    media: { type: Object, required: true },
    collections: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
});

const search = ref(props.filters.search ?? '');
const collection = ref(props.filters.collection ?? '');
let t = null;
watch([search, collection], () => {
    clearTimeout(t);
    t = setTimeout(() => {
        router.get(
            route('admin.media.index'),
            {
                search: search.value || undefined,
                collection: collection.value || undefined,
            },
            { preserveState: true, replace: true },
        );
    }, 350);
});

const uploadCollection = ref('general');
const fileInput = ref(null);
const dropzoneRef = ref(null);
const isDragging = ref(false);
const uploadCount = ref(0);

function onPickFiles() {
    fileInput.value?.click();
}

function onFilesChange(e) {
    const files = e.target.files;
    if (!files?.length) return;
    uploadFiles(files);
}

function onDrop(e) {
    isDragging.value = false;
    const files = e.dataTransfer?.files;
    if (!files?.length) return;
    uploadFiles(files);
}

function onDragOver(e) {
    e.preventDefault();
    isDragging.value = true;
}

function onDragLeave(e) {
    if (dropzoneRef.value && !dropzoneRef.value.contains(e.relatedTarget)) {
        isDragging.value = false;
    }
}

function uploadFiles(files) {
    const fd = new FormData();
    fd.append('collection', uploadCollection.value || 'general');
    for (let i = 0; i < files.length; i++) {
        fd.append('files[]', files[i]);
    }
    uploadCount.value = files.length;
    router.post(route('admin.media.store'), fd, {
        preserveScroll: true,
        forceFormData: true,
        onFinish: () => {
            if (fileInput.value) fileInput.value.value = '';
            uploadCount.value = 0;
        },
    });
}

const editing = ref(null);
const metaForm = useForm({ alt: '', title: '' });

function openEdit(m) {
    editing.value = m;
    metaForm.alt = m.alt ?? '';
    metaForm.title = m.title ?? '';
    metaForm.clearErrors();
}

function saveMeta() {
    if (!editing.value) return;
    metaForm.put(route('admin.media.update', editing.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            editing.value = null;
        },
    });
}

const del = ref(null);
function destroyItem() {
    if (!del.value) return;
    router.delete(route('admin.media.destroy', del.value.id), {
        preserveScroll: true,
        onFinish: () => (del.value = null),
    });
}
</script>

<template>
    <Head title="Media — Admin" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold">Media</h1>
                    <p class="text-sm text-base-content/60">Upload dan kelola aset publik.</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <select v-model="uploadCollection" class="select select-bordered select-sm">
                        <option value="general">general</option>
                        <option v-for="c in collections" :key="c" :value="c">{{ c }}</option>
                    </select>
                </div>
            </div>

            <div
                ref="dropzoneRef"
                class="relative flex flex-col items-center justify-center rounded-box border-2 border-dashed px-6 py-10 transition-colors"
                :class="isDragging ? 'border-primary bg-primary/5' : 'border-base-300 bg-base-100 hover:border-base-content/30'"
                @dragover.prevent="onDragOver"
                @dragleave="onDragLeave"
                @drop.prevent="onDrop"
            >
                <input ref="fileInput" type="file" class="hidden" multiple accept="image/*" @change="onFilesChange" />

                <template v-if="isDragging">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-primary mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                    </svg>
                    <p class="text-sm font-medium text-primary">Lepaskan file di sini</p>
                </template>
                <template v-else-if="uploadCount > 0">
                    <span class="loading loading-spinner loading-md text-primary mb-3" />
                    <p class="text-sm text-base-content/70">Mengupload {{ uploadCount }} file…</p>
                </template>
                <template v-else>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-base-content/30 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-base-content/50">
                        Seret &amp; lepas file ke sini, atau
                        <button type="button" class="link link-primary" @click="onPickFiles">pilih file</button>
                    </p>
                    <p class="text-[10px] text-base-content/30 mt-1">Gambar JPEG, PNG, WebP, GIF, SVG — maks 4 MB</p>
                </template>
            </div>

            <div class="flex flex-wrap gap-2">
                <input v-model="search" type="search" placeholder="Cari nama / alt…" class="input input-bordered input-sm w-full max-w-xs" />
                <select v-model="collection" class="select select-bordered select-sm">
                    <option value="">Semua koleksi</option>
                    <option v-for="c in collections" :key="c" :value="c">{{ c }}</option>
                </select>
            </div>
            <div class="grid gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6">
                <div v-for="m in media.data" :key="m.id" class="card border border-base-300 bg-base-100 shadow-sm">
                    <figure class="aspect-square bg-base-200">
                        <img v-if="m.is_image" :src="m.url" :alt="m.alt || ''" class="h-full w-full object-cover" />
                        <div v-else class="flex h-full items-center justify-center p-2 text-center text-xs text-base-content/50">
                            {{ m.original_name }}
                        </div>
                    </figure>
                    <div class="card-body gap-1 p-3">
                        <p class="line-clamp-2 text-xs font-medium" :title="m.original_name">{{ m.original_name }}</p>
                        <p class="text-[10px] text-base-content/50">{{ m.human_size }} · {{ m.collection }}</p>
                        <div class="card-actions mt-1 justify-end gap-1">
                            <button type="button" class="btn btn-ghost btn-xs" @click="openEdit(m)">Meta</button>
                            <button type="button" class="btn btn-outline btn-error btn-xs" @click="del = m">Hapus</button>
                        </div>
                    </div>
                </div>
            </div>
            <div v-if="media.last_page > 1" class="flex flex-wrap justify-center gap-1">
                <template v-for="(link, idx) in media.links" :key="idx">
                    <Link
                        v-if="link.url"
                        :href="link.url"
                        class="btn btn-xs join-item"
                        :class="link.active ? 'btn-primary' : 'btn-ghost'"
                        preserve-scroll
                    >
                        <span v-html="link.label" />
                    </Link>
                </template>
            </div>
        </div>

        <Teleport to="body">
            <div
                v-if="editing"
                class="fixed inset-0 z-[2147483647] flex items-center justify-center bg-black/45 p-4"
                @click.self="editing = null"
                @keydown.escape.window="editing = null"
            >
                <div class="w-full max-w-md rounded-box border border-base-300 bg-base-100 p-6 shadow-2xl">
                    <h3 class="font-bold text-lg">Edit meta</h3>
                    <form class="mt-4 space-y-3" @submit.prevent="saveMeta">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Alt</legend>
                            <input v-model="metaForm.alt" type="text" class="input input-bordered w-full" />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Title</legend>
                            <input v-model="metaForm.title" type="text" class="input input-bordered w-full" />
                        </fieldset>
                        <div class="mt-6 flex items-center justify-end gap-3">
                            <button type="button" class="btn btn-ghost" @click="editing = null">Tutup</button>
                            <button type="submit" class="btn btn-primary" :disabled="metaForm.processing">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>

            <div
                v-if="del"
                class="fixed inset-0 z-[2147483647] flex items-center justify-center bg-black/45 p-4"
                @click.self="del = null"
                @keydown.escape.window="del = null"
            >
                <div class="w-full max-w-md rounded-box border border-base-300 bg-base-100 p-6 shadow-2xl">
                    <h3 class="font-bold text-lg">Hapus media?</h3>
                    <p class="mt-2 text-sm">File di storage akan dihapus.</p>
                    <div class="mt-6 flex items-center justify-end gap-3">
                        <button type="button" class="btn btn-ghost" @click="del = null">Batal</button>
                        <button type="button" class="btn btn-error" @click="destroyItem">Hapus</button>
                    </div>
                </div>
            </div>
        </Teleport>
    </AdminLayout>
</template>
