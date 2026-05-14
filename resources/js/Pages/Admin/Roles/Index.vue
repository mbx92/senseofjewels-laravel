<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { reactive, ref } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    roles: { type: Array, required: true },
    allPermissions: { type: Array, required: true },
});

const permByRole = reactive({});
for (const r of props.roles) {
    permByRole[r.id] = [...(r.permission_names ?? [])];
}

const newRole = useForm({ name: '', permissions: [] });
function storeRole() {
    newRole.post(route('admin.roles.store'), {
        preserveScroll: true,
        onSuccess: () => {
            newRole.reset('name', 'permissions');
        },
    });
}

function updateRole(roleId) {
    router.put(
        route('admin.roles.update', roleId),
        { permissions: permByRole[roleId] ?? [] },
        { preserveScroll: true },
    );
}

const del = ref(null);
function destroyRole() {
    if (!del.value) return;
    router.delete(route('admin.roles.destroy', del.value.id), {
        preserveScroll: true,
        onFinish: () => (del.value = null),
    });
}
</script>

<template>
    <Head title="Roles — Admin" />
    <AdminLayout>
        <div class="space-y-8">
            <div>
                <h1 class="text-2xl font-bold">Roles & permissions</h1>
                <p class="text-sm text-base-content/60">Role sistem (super-admin, admin, customer) tidak dapat dihapus.</p>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <h2 class="card-title text-base">Role baru</h2>
                    <form class="flex flex-wrap items-end gap-3" @submit.prevent="storeRole">
                        <fieldset class="fieldset flex-1 min-w-[12rem]">
                            <legend class="fieldset-legend">Nama role *</legend>
                            <input v-model="newRole.name" type="text" class="input input-bordered w-full" required />
                        </fieldset>
                        <button type="submit" class="btn btn-primary btn-sm" :disabled="newRole.processing">Buat</button>
                    </form>
                    <p v-if="newRole.errors.name" class="text-error text-sm">{{ newRole.errors.name }}</p>
                </div>
            </div>

            <div v-for="r in roles" :key="r.id" class="card bg-base-100 shadow-sm">
                <div class="card-body gap-3">
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div>
                            <h3 class="font-semibold">{{ r.name }}</h3>
                            <p class="text-xs text-base-content/50">{{ r.users_count }} user</p>
                        </div>
                        <div class="flex gap-1">
                            <button type="button" class="btn btn-primary btn-xs" @click="updateRole(r.id)">Simpan izin</button>
                            <button
                                v-if="!r.is_system"
                                type="button"
                                class="btn btn-outline btn-error btn-xs"
                                @click="del = r"
                            >
                                Hapus role
                            </button>
                        </div>
                    </div>
                    <div class="max-h-48 space-y-1 overflow-y-auto rounded border border-base-300 p-2">
                        <label v-for="p in allPermissions" :key="p.id" class="flex cursor-pointer items-center gap-2 text-xs">
                            <input v-model="permByRole[r.id]" type="checkbox" class="checkbox checkbox-xs" :value="p.name" />
                            {{ p.name }}
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <dialog class="modal" :class="{ 'modal-open': !!del }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Hapus role?</h3>
                <div class="modal-action">
                    <button type="button" class="btn" @click="del = null">Batal</button>
                    <button type="button" class="btn btn-error" @click="destroyRole">Hapus</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop" @click="del = null"><button>close</button></form>
        </dialog>
    </AdminLayout>
</template>
