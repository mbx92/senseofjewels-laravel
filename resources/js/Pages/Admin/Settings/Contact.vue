<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
    settings: { type: Object, required: true },
});

const form = useForm({
    contact_address: props.settings.contact_address ?? '',
    contact_phone: props.settings.contact_phone ?? '',
    contact_email: props.settings.contact_email ?? '',
    contact_maps_embed: props.settings.contact_maps_embed ?? '',
    contact_whatsapp: props.settings.contact_whatsapp ?? '',
});

function submit() {
    form.put(route('admin.contact-settings.update'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Contact — Admin" />
    <AdminLayout>
        <div class="mx-auto max-w-2xl space-y-6">
            <div class="flex items-center gap-3">
                <Link :href="route('admin.settings.index')" class="btn btn-ghost btn-sm">← Settings</Link>
                <h1 class="text-2xl font-bold">Informasi kontak</h1>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <form class="space-y-4" @submit.prevent="submit">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Alamat</legend>
                            <textarea v-model="form.contact_address" rows="3" class="textarea textarea-bordered w-full" />
                        </fieldset>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Telepon</legend>
                                <input v-model="form.contact_phone" type="text" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Email</legend>
                                <input v-model="form.contact_email" type="email" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset sm:col-span-2">
                                <legend class="fieldset-legend">WhatsApp</legend>
                                <input v-model="form.contact_whatsapp" type="text" class="input input-bordered w-full" />
                            </fieldset>
                        </div>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Google Maps embed URL</legend>
                            <input v-model="form.contact_maps_embed" type="text" class="input input-bordered w-full" />
                        </fieldset>
                        <div class="flex justify-end gap-2">
                            <Link :href="route('admin.settings.index')" class="btn btn-ghost">Batal</Link>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
