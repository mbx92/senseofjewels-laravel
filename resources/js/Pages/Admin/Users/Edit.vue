<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
    user: { type: Object, required: true },
    roles: { type: Array, required: true },
});

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    phone: props.user.phone ?? '',
    password: '',
    roles: [...(props.user.role_names ?? [])],
});

function submit() {
    form
        .transform((d) => {
            const o = { ...d };
            if (!o.password) {
                delete o.password;
            }
            return o;
        })
        .put(route('admin.users.update', props.user.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Edit User — Admin" />
    <AdminLayout>
        <div class="mx-auto max-w-lg space-y-6">
            <div class="flex items-center gap-3">
                <Link :href="route('admin.users.index')" class="btn btn-ghost btn-sm">← Kembali</Link>
                <h1 class="text-2xl font-bold">Edit User</h1>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <form class="space-y-4" @submit.prevent="submit">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Nama *</legend>
                            <input v-model="form.name" type="text" class="input input-bordered w-full" required />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Email *</legend>
                            <input v-model="form.email" type="email" class="input input-bordered w-full" required />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Phone</legend>
                            <input v-model="form.phone" type="text" class="input input-bordered w-full" />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Password baru</legend>
                            <input v-model="form.password" type="password" class="input input-bordered w-full" autocomplete="new-password" />
                            <p class="text-xs text-base-content/50">Kosongkan jika tidak diubah.</p>
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Roles *</legend>
                            <div class="space-y-2">
                                <label v-for="r in roles" :key="r.id" class="flex cursor-pointer items-center gap-2 text-sm">
                                    <input v-model="form.roles" type="checkbox" class="checkbox checkbox-sm" :value="r.name" />
                                    {{ r.name }}
                                </label>
                            </div>
                            <p v-if="form.errors.roles" class="text-error text-sm">{{ form.errors.roles }}</p>
                        </fieldset>
                        <div class="flex justify-end gap-2">
                            <Link :href="route('admin.users.index')" class="btn btn-ghost">Batal</Link>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
