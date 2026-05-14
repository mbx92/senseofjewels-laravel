<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
    settings: { type: Object, required: true },
    midtransConfigured: { type: Boolean, required: true },
});

const form = useForm({
    midtrans_enabled: props.settings.midtrans_enabled === '1' || props.settings.midtrans_enabled === true,
});

function submit() {
    form.transform((d) => ({ midtrans_enabled: !!d.midtrans_enabled })).put(route('admin.integrations.update'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Integrations — Admin" />
    <AdminLayout>
        <div class="mx-auto max-w-2xl space-y-6">
            <div class="flex items-center gap-3">
                <Link :href="route('admin.settings.index')" class="btn btn-ghost btn-sm">← Settings</Link>
                <h1 class="text-2xl font-bold">Integrations</h1>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <div v-if="!midtransConfigured" class="alert alert-warning text-sm">
                        <span>Midtrans belum dikonfigurasi di <code class="text-xs">.env</code> (server key & client key).</span>
                    </div>
                    <form class="space-y-4" @submit.prevent="submit">
                        <label class="flex cursor-pointer items-center gap-3">
                            <input v-model="form.midtrans_enabled" type="checkbox" class="toggle toggle-primary" :disabled="!midtransConfigured" />
                            <span class="text-sm">Aktifkan Midtrans di checkout</span>
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
