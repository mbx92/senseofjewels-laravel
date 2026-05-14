<script setup>
import SingleMediaPicker from '@/Components/Admin/SingleMediaPicker.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({ testimonial: { type: Object, required: true } });

const form = useForm({
    name: props.testimonial.name,
    position: props.testimonial.position ?? '',
    company: props.testimonial.company ?? '',
    rating: props.testimonial.rating,
    message: props.testimonial.message,
    photo_url: props.testimonial.photo_url ?? '',
    sort_order: props.testimonial.sort_order ?? 0,
    is_featured: !!props.testimonial.is_featured,
    is_active: !!props.testimonial.is_active,
});

function submit() {
    form
        .transform((d) => ({
            ...d,
            rating: Number(d.rating),
            sort_order: Number(d.sort_order),
            is_featured: !!d.is_featured,
            is_active: !!d.is_active,
        }))
        .put(route('admin.testimonials.update', props.testimonial.id), { preserveScroll: true });
}
</script>

<template>
    <Head :title="`Edit Testimonial — ${testimonial.name}`" />
    <AdminLayout>
        <div class="mx-auto max-w-lg space-y-6">
            <Link :href="route('admin.testimonials.index')" class="btn btn-ghost btn-sm">← Kembali</Link>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <form class="space-y-4" @submit.prevent="submit">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Nama *</legend>
                            <input v-model="form.name" type="text" class="input w-full" required />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Jabatan</legend>
                            <input v-model="form.position" type="text" class="input w-full" />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Perusahaan</legend>
                            <input v-model="form.company" type="text" class="input w-full" />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Rating *</legend>
                            <input v-model.number="form.rating" type="number" min="1" max="5" class="input w-28" required />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Pesan *</legend>
                            <textarea v-model="form.message" rows="4" class="textarea w-full" required maxlength="1000" />
                        </fieldset>
                        <SingleMediaPicker v-model="form.photo_url" label="Foto" />
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
                            <Link :href="route('admin.testimonials.index')" class="btn btn-ghost">Batal</Link>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">Perbarui</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
