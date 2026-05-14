<script setup>
import SingleMediaPicker from '@/Components/Admin/SingleMediaPicker.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
    parents: { type: Array, required: true },
    category: { type: Object, required: true },
});

const form = useForm({
    name: props.category.name,
    parent_id: props.category.parent_id != null ? String(props.category.parent_id) : '',
    description: props.category.description ?? '',
    image_url: props.category.image_url ?? '',
    sort_order: props.category.sort_order ?? 0,
    is_active: !!props.category.is_active,
});

function submit() {
    form
        .transform((data) => ({
            ...data,
            parent_id: data.parent_id === '' || data.parent_id === null ? null : Number(data.parent_id),
            sort_order: Number(data.sort_order ?? 0),
            is_active: !!data.is_active,
        }))
        .put(route('admin.categories.update', props.category.id), { preserveScroll: true });
}
</script>

<template>
    <Head :title="`Edit Kategori — ${category.name}`" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex items-center gap-3">
                <Link :href="route('admin.categories.index')" class="btn btn-ghost btn-sm">← Kembali</Link>
                <div class="space-y-1">
                    <h1 class="text-2xl font-bold">Edit Kategori</h1>
                    <p class="text-sm text-base-content/60">Perubahan nama, gambar, dan hierarki kategori kini punya ritme yang sama seperti form admin lainnya.</p>
                </div>
            </div>

            <div class="card max-w-lg bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <form class="space-y-4" @submit.prevent="submit">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Nama Kategori <span class="text-error">*</span></legend>
                            <input v-model="form.name" type="text" class="input w-full" :class="{ 'input-error': form.errors.name }" required />
                            <p v-if="form.errors.name" class="fieldset-label text-error">{{ form.errors.name }}</p>
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Kategori Induk</legend>
                            <select v-model="form.parent_id" class="select w-full">
                                <option value="">— Tidak Ada —</option>
                                <option v-for="p in parents" :key="p.id" :value="String(p.id)">{{ p.name }}</option>
                            </select>
                        </fieldset>

                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Deskripsi</legend>
                            <textarea v-model="form.description" rows="3" class="textarea w-full" />
                        </fieldset>

                        <SingleMediaPicker v-model="form.image_url" label="Gambar" :error="form.errors.image_url" />

                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Urutan Tampil</legend>
                            <input v-model.number="form.sort_order" type="number" min="0" class="input w-28" />
                        </fieldset>

                        <div class="flex items-center gap-3">
                            <input id="is_active" v-model="form.is_active" type="checkbox" class="toggle toggle-success" :true-value="true" :false-value="false" />
                            <label for="is_active" class="text-sm">Aktif</label>
                        </div>

                        <div class="card-actions justify-end pt-2">
                            <Link :href="route('admin.categories.index')" class="btn btn-ghost">Batal</Link>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
