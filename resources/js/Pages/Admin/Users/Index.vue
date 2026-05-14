<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    users: { type: Object, required: true },
    roles: { type: Array, required: true },
    filters: { type: Object, default: () => ({}) },
});

const search = ref(props.filters.search ?? '');
const role = ref(props.filters.role ?? '');
let t = null;
watch([search, role], () => {
    clearTimeout(t);
    t = setTimeout(() => {
        router.get(
            route('admin.users.index'),
            {
                search: search.value || undefined,
                role: role.value || undefined,
            },
            { preserveState: true, replace: true },
        );
    }, 350);
});

const del = ref(null);
function submitDelete() {
    if (!del.value) return;
    router.delete(route('admin.users.destroy', del.value.id), {
        preserveScroll: true,
        onFinish: () => (del.value = null),
    });
}
</script>

<template>
    <Head title="Users — Admin" />
    <AdminLayout>
        <div class="space-y-6">
            <div>
                <h1 class="text-2xl font-bold">Users</h1>
                <p class="text-sm text-base-content/60">Kelola akun staf.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <input v-model="search" type="search" placeholder="Cari nama / email…" class="input input-bordered input-sm w-full max-w-xs" />
                <select v-model="role" class="select select-bordered select-sm">
                    <option value="">Semua role</option>
                    <option v-for="r in roles" :key="r.id" :value="r.name">{{ r.name }}</option>
                </select>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-sm">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Roles</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="u in users.data" :key="u.id">
                                <td class="font-medium">{{ u.name }}</td>
                                <td>{{ u.email }}</td>
                                <td>{{ u.phone || '—' }}</td>
                                <td>
                                    <span v-for="rn in u.roles" :key="rn" class="badge badge-ghost badge-sm mr-1">{{ rn }}</span>
                                    <span v-if="!u.roles?.length">—</span>
                                </td>
                                <td class="flex flex-wrap gap-1">
                                    <Link :href="route('admin.users.edit', u.id)" class="btn btn-ghost btn-xs">Edit</Link>
                                    <button type="button" class="btn btn-outline btn-error btn-xs" @click="del = u">Hapus</button>
                                </td>
                            </tr>
                            <tr v-if="!users.data?.length">
                                <td colspan="5" class="py-8 text-center text-base-content/50">Tidak ada user.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="users.last_page > 1" class="card-body flex flex-wrap justify-center gap-1 pt-0">
                    <template v-for="(link, idx) in users.links" :key="idx">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="btn btn-xs join-item"
                            :class="link.active ? 'btn-primary' : 'btn-ghost'"
                            preserve-scroll
                        >
                            <span v-html="link.label" />
                        </Link>
                    </template>
                </div>
            </div>
        </div>

        <dialog class="modal" :class="{ 'modal-open': !!del }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Hapus user?</h3>
                <div class="modal-action">
                    <button type="button" class="btn" @click="del = null">Batal</button>
                    <button type="button" class="btn btn-error" @click="submitDelete">Hapus</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop" @click="del = null"><button>close</button></form>
        </dialog>
    </AdminLayout>
</template>
