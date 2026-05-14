<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';

defineProps({
    categories: { type: Array, required: true },
});

const deleteTarget = ref(null);

function confirmDelete(c) {
    deleteTarget.value = c;
}

function cancelDelete() {
    deleteTarget.value = null;
}

function submitDelete() {
    if (!deleteTarget.value) {
        return;
    }
    router.delete(route('admin.categories.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onFinish: () => {
            deleteTarget.value = null;
        },
    });
}
</script>

<template>
    <Head title="Kategori — Admin" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-1">
                    <h1 class="text-2xl font-bold">Kategori</h1>
                    <p class="text-sm text-base-content/60">Kelompokkan katalog dengan hierarki yang tetap rapi di desktop dan mobile.</p>
                </div>
                <Link :href="route('admin.categories.create')" class="btn btn-primary btn-sm">+ Tambah Kategori</Link>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Induk</th>
                                <th>Produk</th>
                                <th>Urutan</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="category in categories" :key="category.id">
                                <td class="font-medium">{{ category.name }}</td>
                                <td class="text-sm text-base-content/60">{{ category.parent_name ?? '—' }}</td>
                                <td>{{ category.products_count }}</td>
                                <td>{{ category.sort_order }}</td>
                                <td>
                                    <span v-if="category.is_active" class="badge badge-success badge-sm">Aktif</span>
                                    <span v-else class="badge badge-ghost badge-sm">Nonaktif</span>
                                </td>
                                <td>
                                    <div class="flex gap-1">
                                        <Link :href="route('admin.categories.edit', category.id)" class="btn btn-ghost btn-xs">Edit</Link>
                                        <button type="button" class="btn btn-outline btn-error btn-xs" @click="confirmDelete(category)">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!categories.length">
                                <td colspan="6" class="py-8 text-center text-base-content/50">Belum ada kategori.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div
            v-if="deleteTarget"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-cat-title"
            @click.self="cancelDelete"
        >
            <div class="card w-80 bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 id="delete-cat-title" class="card-title text-base">Hapus Kategori?</h3>
                    <p class="text-sm">Kategori <strong>{{ deleteTarget.name }}</strong> akan dihapus. Pastikan tidak ada produk terhubung.</p>
                    <div class="card-actions mt-2 justify-end">
                        <button type="button" class="btn btn-ghost btn-sm" @click="cancelDelete">Batal</button>
                        <button type="button" class="btn btn-error btn-sm" @click="submitDelete">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
