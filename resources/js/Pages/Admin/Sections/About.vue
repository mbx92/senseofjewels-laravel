<script setup>
import SingleMediaPicker from '@/Components/Admin/SingleMediaPicker.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
    section: { type: Object, required: true },
});

const form = useForm({
    title: props.section.title ?? '',
    content: props.section.content ?? '',
    image_url: props.section.image_url ?? '',
    is_active: props.section.is_active ?? true,
});

function submit() {
    form.transform((d) => ({ ...d, is_active: !!d.is_active })).put(route('admin.about.update'), { preserveScroll: true });
}
</script>

<template>
    <Head title="About — Admin" />
    <AdminLayout>
        <div class="mx-auto max-w-2xl space-y-6">
            <div class="flex items-center gap-3">
                <Link :href="route('admin.dashboard')" class="btn btn-ghost btn-sm">← Dashboard</Link>
                <h1 class="text-2xl font-bold">About section</h1>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <form class="space-y-4" @submit.prevent="submit">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Judul *</legend>
                            <input v-model="form.title" type="text" class="input input-bordered w-full" required />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Konten</legend>
                            <textarea v-model="form.content" rows="6" class="textarea textarea-bordered w-full" />
                        </fieldset>
                        <SingleMediaPicker v-model="form.image_url" label="Gambar" />
                        <label class="flex cursor-pointer items-center gap-3">
                            <input v-model="form.is_active" type="checkbox" class="toggle toggle-success" :true-value="true" :false-value="false" />
                            <span class="text-sm">Aktif</span>
                        </label>
                        <div class="flex justify-end">
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
