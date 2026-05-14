<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';

defineProps({
    products: {
        type: Object,
        required: true,
    },
});

const deleteTarget = ref(null);

function confirmDelete(p) {
    deleteTarget.value = p;
}

function cancelDelete() {
    deleteTarget.value = null;
}

function submitDelete() {
    if (!deleteTarget.value) return;
    router.delete(route('admin.products.destroy', deleteTarget.value.id), {
        preserveScroll: true,
        onFinish: () => {
            deleteTarget.value = null;
        },
    });
}
</script>

<template>
    <Head title="Produk — Admin" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <div class="space-y-1">
                    <h1 class="text-2xl font-bold">Produk</h1>
                    <p class="text-sm text-base-content/60">Grid aksi dan tabel produk; tambah dan ubah memakai form Inertia + Vue.</p>
                </div>
                <Link :href="route('admin.products.create')" class="btn btn-primary btn-sm">+ Tambah Produk</Link>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th>SKU</th>
                                <th>Kategori</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Stok</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products.data" :key="product.id">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="avatar">
                                            <div class="w-10 rounded bg-base-200">
                                                <img v-if="product.primary_image_url" :src="product.primary_image_url" :alt="product.name" />
                                                <div v-else class="flex h-10 w-10 items-center justify-center text-xs text-base-content/30">N/A</div>
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-sm font-medium">{{ product.name }}</div>
                                            <span v-if="product.is_featured" class="badge badge-accent badge-xs">Featured</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="font-mono text-xs">{{ product.sku }}</td>
                                <td class="text-sm">{{ product.category_name ?? '-' }}</td>
                                <td class="text-right text-sm">{{ product.price_formatted }}</td>
                                <td class="text-right">
                                    <span :class="product.stock <= product.min_stock_alert ? 'font-bold text-error' : ''">{{ product.stock }}</span>
                                </td>
                                <td>
                                    <span v-if="product.is_active" class="badge badge-success badge-sm">Aktif</span>
                                    <span v-else class="badge badge-ghost badge-sm">Nonaktif</span>
                                </td>
                                <td>
                                    <div class="flex gap-1">
                                        <Link :href="route('admin.products.edit', product.id)" class="btn btn-ghost btn-xs">Edit</Link>
                                        <button type="button" class="btn btn-outline btn-error btn-xs" @click="confirmDelete(product)">Hapus</button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!products.data.length">
                                <td colspan="7" class="py-8 text-center text-base-content/50">Belum ada produk.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="products.last_page > 1" class="card-body join flex flex-wrap justify-center gap-1 pt-0">
                    <template v-for="(link, idx) in products.links" :key="idx">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="btn btn-sm join-item"
                            :class="link.active ? 'btn-active btn-primary' : 'btn-ghost'"
                            preserve-scroll
                        >
                            <span v-html="link.label"></span>
                        </Link>
                        <span v-else class="btn btn-ghost join-item btn-disabled btn-sm pointer-events-none opacity-50">
                            <span v-html="link.label"></span>
                        </span>
                    </template>
                </div>
            </div>
        </div>

        <div
            v-if="deleteTarget"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
            @click.self="cancelDelete"
            role="dialog"
            aria-modal="true"
            aria-labelledby="delete-product-title"
        >
            <div class="card w-80 bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 id="delete-product-title" class="card-title text-base">Hapus Produk?</h3>
                    <p class="text-sm">Produk <strong>{{ deleteTarget.name }}</strong> akan dihapus beserta semua gambarnya.</p>
                    <div class="card-actions mt-2 justify-end">
                        <button type="button" class="btn btn-ghost btn-sm" @click="cancelDelete">Batal</button>
                        <button type="button" class="btn btn-error btn-sm" @click="submitDelete">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
