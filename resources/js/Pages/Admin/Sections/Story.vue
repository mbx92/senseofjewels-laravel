<script setup>
import SingleMediaPicker from '@/Components/Admin/SingleMediaPicker.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
    section: { type: Object, required: true },
});

const form = useForm({
    eyebrow: props.section.eyebrow ?? '',
    title: props.section.title ?? '',
    subtitle: props.section.subtitle ?? '',
    content: props.section.content ?? '',
    cta_text: props.section.cta_text ?? '',
    cta_url: props.section.cta_url ?? '',
    image_path: props.section.image_path ?? '',
    secondary_image: props.section.secondary_image ?? '',
    is_active: props.section.is_active ?? true,
});

function submit() {
    form.transform((d) => ({ ...d, is_active: !!d.is_active })).put(route('admin.story.update'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Story — Admin" />
    <AdminLayout>
        <div class="mx-auto max-w-2xl space-y-6">
            <div class="flex items-center gap-3">
                <Link :href="route('admin.dashboard')" class="btn btn-ghost btn-sm">← Dashboard</Link>
                <h1 class="text-2xl font-bold">Story section</h1>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <form class="space-y-4" @submit.prevent="submit">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Eyebrow</legend>
                            <input v-model="form.eyebrow" type="text" class="input input-bordered w-full" />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Judul</legend>
                            <input v-model="form.title" type="text" class="input input-bordered w-full" />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Subjudul</legend>
                            <input v-model="form.subtitle" type="text" class="input input-bordered w-full" />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Konten</legend>
                            <textarea v-model="form.content" rows="8" class="textarea textarea-bordered w-full" />
                        </fieldset>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">CTA teks</legend>
                                <input v-model="form.cta_text" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">CTA URL</legend>
                                <input v-model="form.cta_url" type="text" class="input input-bordered w-full" />
                            </fieldset>
                        </div>
                        <SingleMediaPicker v-model="form.image_path" label="Gambar utama" />
                        <SingleMediaPicker v-model="form.secondary_image" label="Gambar sekunder" />
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
