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

function onPickFiles() {
    fileInput.value?.click();
}

function onFilesChange(e) {
    const files = e.target.files;
    if (!files?.length) return;
    const fd = new FormData();
    fd.append('collection', uploadCollection.value || 'general');
    for (let i = 0; i < files.length; i++) {
        fd.append('files[]', files[i]);
    }
    router.post(route('admin.media.store'), fd, {
        preserveScroll: true,
        forceFormData: true,
        onFinish: () => {
            if (fileInput.value) fileInput.value.value = '';
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
                    <input ref="fileInput" type="file" class="hidden" multiple accept="image/*" @change="onFilesChange" />
                    <select v-model="uploadCollection" class="select select-bordered select-sm">
                        <option value="general">general</option>
                        <option v-for="c in collections" :key="c" :value="c">{{ c }}</option>
                    </select>
                    <button type="button" class="btn btn-primary btn-sm" @click="onPickFiles">Upload</button>
                </div>
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

        <dialog class="modal" :class="{ 'modal-open': !!editing }">
            <div class="modal-box max-w-md">
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
                    <div class="modal-action">
                        <button type="button" class="btn" @click="editing = null">Tutup</button>
                        <button type="submit" class="btn btn-primary" :disabled="metaForm.processing">Simpan</button>
                    </div>
                </form>
            </div>
            <form method="dialog" class="modal-backdrop" @click="editing = null"><button>close</button></form>
        </dialog>

        <dialog class="modal" :class="{ 'modal-open': !!del }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Hapus media?</h3>
                <p class="py-2 text-sm">File di storage akan dihapus.</p>
                <div class="modal-action">
                    <button type="button" class="btn" @click="del = null">Batal</button>
                    <button type="button" class="btn btn-error" @click="destroyItem">Hapus</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop" @click="del = null"><button>close</button></form>
        </dialog>
    </AdminLayout>
</template>
