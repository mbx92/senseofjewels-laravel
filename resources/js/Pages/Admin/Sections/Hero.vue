<script setup>
import SingleMediaPicker from '@/Components/Admin/SingleMediaPicker.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    initial: { type: Object, required: true },
    textPositions: { type: Object, required: true },
});

function emptySlide() {
    return {
        image: '',
        title: '',
        subtitle: '',
        description: '',
        cta_text: '',
        cta_url: '',
        text_position: 'top-left',
        focus_x: 50,
        focus_y: 50,
        zoom: 100,
    };
}

const slides = ref(
    props.initial.heroSlides?.length ? JSON.parse(JSON.stringify(props.initial.heroSlides)) : [emptySlide()],
);

const { heroSlides: _hs, ...rest } = props.initial;
const form = useForm({ ...rest });

function addSlide() {
    slides.value.push(emptySlide());
}

function removeSlide(i) {
    if (slides.value.length <= 1) return;
    slides.value.splice(i, 1);
}

function submit() {
    form
        .transform((d) => ({
            ...d,
            is_active: !!d.is_active,
            hero_slides: JSON.stringify(slides.value),
        }))
        .put(route('admin.hero.update'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Hero — Admin" />
    <AdminLayout>
        <div class="mx-auto max-w-4xl space-y-6">
            <div class="flex items-center gap-3">
                <Link :href="route('admin.dashboard')" class="btn btn-ghost btn-sm">← Dashboard</Link>
                <h1 class="text-2xl font-bold">Hero section</h1>
            </div>
            <form class="space-y-8" @submit.prevent="submit">
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base">Status</h2>
                        <label class="flex cursor-pointer items-center gap-3">
                            <input v-model="form.is_active" type="checkbox" class="toggle toggle-success" :true-value="true" :false-value="false" />
                            <span class="text-sm">Section aktif</span>
                        </label>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <h2 class="text-lg font-semibold">Slides utama</h2>
                        <button type="button" class="btn btn-outline btn-sm" @click="addSlide">+ Slide</button>
                    </div>
                    <div v-for="(slide, idx) in slides" :key="idx" class="card border border-base-300 bg-base-100 shadow-sm">
                        <div class="card-body gap-3">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-semibold uppercase tracking-wider text-base-content/50">Slide {{ idx + 1 }}</span>
                                <button v-if="slides.length > 1" type="button" class="btn btn-ghost btn-xs text-error" @click="removeSlide(idx)">Hapus</button>
                            </div>
                            <SingleMediaPicker v-model="slide.image" :label="`Gambar slide ${idx + 1}`" />
                            <div class="grid gap-3 sm:grid-cols-2">
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">Judul</legend>
                                    <input v-model="slide.title" type="text" class="input input-bordered w-full" />
                                </fieldset>
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">Subjudul</legend>
                                    <input v-model="slide.subtitle" type="text" class="input input-bordered w-full" />
                                </fieldset>
                            </div>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Deskripsi</legend>
                                <textarea v-model="slide.description" rows="2" class="textarea textarea-bordered w-full" />
                            </fieldset>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">CTA teks</legend>
                                    <input v-model="slide.cta_text" type="text" class="input input-bordered w-full" />
                                </fieldset>
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">CTA URL</legend>
                                    <input v-model="slide.cta_url" type="text" class="input input-bordered w-full" />
                                </fieldset>
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">Posisi teks</legend>
                                    <select v-model="slide.text_position" class="select select-bordered w-full">
                                        <option v-for="(label, pos) in textPositions" :key="pos" :value="pos">{{ label }}</option>
                                    </select>
                                </fieldset>
                            </div>
                            <div class="grid gap-3 sm:grid-cols-3">
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">Focus X %</legend>
                                    <input v-model.number="slide.focus_x" type="number" min="0" max="100" class="input input-bordered w-full" />
                                </fieldset>
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">Focus Y %</legend>
                                    <input v-model.number="slide.focus_y" type="number" min="0" max="100" class="input input-bordered w-full" />
                                </fieldset>
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">Zoom %</legend>
                                    <input v-model.number="slide.zoom" type="number" min="80" max="160" class="input input-bordered w-full" />
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body gap-4">
                        <h2 class="card-title text-base">Fallback (tanpa slide)</h2>
                        <p class="text-xs text-base-content/50">Digunakan jika tidak ada slide valid.</p>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Judul</legend>
                                <input v-model="form.title" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Subjudul</legend>
                                <input v-model="form.subtitle" type="text" class="input input-bordered w-full" />
                            </fieldset>
                        </div>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Deskripsi</legend>
                            <textarea v-model="form.description" rows="2" class="textarea textarea-bordered w-full" />
                        </fieldset>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">CTA teks</legend>
                                <input v-model="form.cta_text" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">CTA URL</legend>
                                <input v-model="form.cta_url" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Posisi teks</legend>
                                <select v-model="form.text_position" class="select select-bordered w-full">
                                    <option v-for="(label, pos) in textPositions" :key="`fb-${pos}`" :value="pos">{{ label }}</option>
                                </select>
                            </fieldset>
                        </div>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Season badge</legend>
                                <input v-model="form.season_badge" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Eyebrow</legend>
                                <input v-model="form.eyebrow" type="text" class="input input-bordered w-full" />
                            </fieldset>
                        </div>
                    </div>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    <div class="card bg-base-100 shadow-sm">
                        <div class="card-body gap-3">
                            <h2 class="card-title text-base">Banner 1</h2>
                            <SingleMediaPicker v-model="form.banner1_image" label="Gambar" />
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Label</legend>
                                <input v-model="form.banner1_label" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Judul</legend>
                                <input v-model="form.banner1_title" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Subjudul</legend>
                                <input v-model="form.banner1_subtitle" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">CTA teks</legend>
                                    <input v-model="form.banner1_cta_text" type="text" class="input input-bordered w-full" />
                                </fieldset>
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">CTA URL</legend>
                                    <input v-model="form.banner1_cta_url" type="text" class="input input-bordered w-full" />
                                </fieldset>
                            </div>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Posisi teks</legend>
                                <select v-model="form.banner1_text_position" class="select select-bordered w-full">
                                    <option v-for="(label, pos) in textPositions" :key="`b1-${pos}`" :value="pos">{{ label }}</option>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                    <div class="card bg-base-100 shadow-sm">
                        <div class="card-body gap-3">
                            <h2 class="card-title text-base">Banner 2</h2>
                            <SingleMediaPicker v-model="form.banner2_image" label="Gambar" />
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Label</legend>
                                <input v-model="form.banner2_label" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Judul</legend>
                                <input v-model="form.banner2_title" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Subjudul</legend>
                                <input v-model="form.banner2_subtitle" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">CTA teks</legend>
                                    <input v-model="form.banner2_cta_text" type="text" class="input input-bordered w-full" />
                                </fieldset>
                                <fieldset class="fieldset">
                                    <legend class="fieldset-legend">CTA URL</legend>
                                    <input v-model="form.banner2_cta_url" type="text" class="input input-bordered w-full" />
                                </fieldset>
                            </div>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Posisi teks</legend>
                                <select v-model="form.banner2_text_position" class="select select-bordered w-full">
                                    <option v-for="(label, pos) in textPositions" :key="`b2-${pos}`" :value="pos">{{ label }}</option>
                                </select>
                            </fieldset>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="btn btn-primary" :disabled="form.processing">Simpan hero</button>
                </div>
            </form>
        </div>
    </AdminLayout>
</template>
