<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';

defineProps({ items: { type: Array, required: true } });
const del = ref(null);
function submitDelete() {
    if (!del.value) return;
    router.delete(route('admin.portfolio.destroy', del.value.id), { preserveScroll: true, onFinish: () => (del.value = null) });
}
</script>

<template>
    <Head title="Portfolio — Admin" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold">Portfolio</h1>
                    <p class="text-sm text-base-content/60">Karya landing page.</p>
                </div>
                <Link :href="route('admin.portfolio.create')" class="btn btn-primary btn-sm">+ Tambah</Link>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Kategori</th>
                                <th>Urutan</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="p in items" :key="p.id">
                                <td class="font-medium">{{ p.title }}</td>
                                <td>{{ p.category || '—' }}</td>
                                <td>{{ p.sort_order }}</td>
                                <td>
                                    <span v-if="p.is_active" class="badge badge-success badge-sm">Aktif</span>
                                    <span v-else class="badge badge-ghost badge-sm">Off</span>
                                </td>
                                <td class="flex gap-1">
                                    <Link :href="route('admin.portfolio.edit', p.id)" class="btn btn-ghost btn-xs">Edit</Link>
                                    <button type="button" class="btn btn-outline btn-error btn-xs" @click="del = p">Hapus</button>
                                </td>
                            </tr>
                            <tr v-if="!items.length">
                                <td colspan="5" class="py-8 text-center text-base-content/50">Belum ada data.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div v-if="del" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.self="del = null">
            <div class="card w-80 bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-base">Hapus?</h3>
                    <p class="text-sm">{{ del.title }}</p>
                    <div class="card-actions justify-end">
                        <button type="button" class="btn btn-ghost btn-sm" @click="del = null">Batal</button>
                        <button type="button" class="btn btn-error btn-sm" @click="submitDelete">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
