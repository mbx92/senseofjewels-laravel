<script setup>
import SingleMediaPicker from '@/Components/Admin/SingleMediaPicker.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const form = useForm({
    title: '',
    icon: '',
    summary: '',
    description: '',
    image_url: '',
    sort_order: 0,
    is_featured: false,
    is_active: true,
});

function submit() {
    form
        .transform((d) => ({
            ...d,
            sort_order: Number(d.sort_order),
            is_featured: !!d.is_featured,
            is_active: !!d.is_active,
        }))
        .post(route('admin.services.store'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Tambah Service — Admin" />
    <AdminLayout>
        <div class="mx-auto max-w-lg space-y-6">
            <div class="flex items-center gap-3">
                <Link :href="route('admin.services.index')" class="btn btn-ghost btn-sm">← Kembali</Link>
                <h1 class="text-2xl font-bold">Tambah Service</h1>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <form class="space-y-4" @submit.prevent="submit">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Judul *</legend>
                            <input v-model="form.title" type="text" class="input w-full" required />
                            <p v-if="form.errors.title" class="text-error text-sm">{{ form.errors.title }}</p>
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Icon</legend>
                            <input v-model="form.icon" type="text" class="input w-full" />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Ringkasan</legend>
                            <textarea v-model="form.summary" rows="2" class="textarea w-full" />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Deskripsi</legend>
                            <textarea v-model="form.description" rows="4" class="textarea w-full" />
                        </fieldset>
                        <SingleMediaPicker v-model="form.image_url" label="Gambar" />
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Urutan</legend>
                            <input v-model.number="form.sort_order" type="number" min="0" class="input w-28" />
                        </fieldset>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2">
                                <input v-model="form.is_featured" type="checkbox" class="toggle toggle-accent" :true-value="true" :false-value="false" />
                                <span class="text-sm">Featured</span>
                            </label>
                            <label class="flex items-center gap-2">
                                <input v-model="form.is_active" type="checkbox" class="toggle toggle-success" :true-value="true" :false-value="false" />
                                <span class="text-sm">Aktif</span>
                            </label>
                        </div>
                        <div class="flex justify-end gap-2">
                            <Link :href="route('admin.services.index')" class="btn btn-ghost">Batal</Link>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
