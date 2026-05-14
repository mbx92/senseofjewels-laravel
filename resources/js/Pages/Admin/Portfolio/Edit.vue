<script setup>
import SingleMediaPicker from '@/Components/Admin/SingleMediaPicker.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({ portfolio: { type: Object, required: true } });

const form = useForm({
    title: props.portfolio.title,
    category: props.portfolio.category ?? '',
    client_name: props.portfolio.client_name ?? '',
    project_url: props.portfolio.project_url ?? '',
    description: props.portfolio.description ?? '',
    image_url: props.portfolio.image_url ?? '',
    completed_at: props.portfolio.completed_at ?? '',
    sort_order: props.portfolio.sort_order ?? 0,
    is_featured: !!props.portfolio.is_featured,
    is_active: !!props.portfolio.is_active,
});

function submit() {
    form
        .transform((d) => ({
            ...d,
            sort_order: Number(d.sort_order),
            is_featured: !!d.is_featured,
            is_active: !!d.is_active,
        }))
        .put(route('admin.portfolio.update', props.portfolio.id), { preserveScroll: true });
}
</script>

<template>
    <Head :title="`Edit Portfolio — ${portfolio.title}`" />
    <AdminLayout>
        <div class="mx-auto max-w-lg space-y-6">
            <Link :href="route('admin.portfolio.index')" class="btn btn-ghost btn-sm">← Kembali</Link>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <form class="space-y-4" @submit.prevent="submit">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Judul *</legend>
                            <input v-model="form.title" type="text" class="input w-full" required />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Kategori</legend>
                            <input v-model="form.category" type="text" class="input w-full" />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Klien</legend>
                            <input v-model="form.client_name" type="text" class="input w-full" />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">URL Proyek</legend>
                            <input v-model="form.project_url" type="url" class="input w-full" />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Deskripsi</legend>
                            <textarea v-model="form.description" rows="4" class="textarea w-full" />
                        </fieldset>
                        <SingleMediaPicker v-model="form.image_url" label="Gambar" />
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Selesai</legend>
                            <input v-model="form.completed_at" type="date" class="input w-full" />
                        </fieldset>
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
                            <Link :href="route('admin.portfolio.index')" class="btn btn-ghost">Batal</Link>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
