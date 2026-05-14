<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';
import ProductForm from './Partials/ProductForm.vue';

defineProps({
    categories: { type: Array, required: true },
});

const gallery = ref([]);

const form = useForm({
    name: '',
    sku: '',
    category_id: '',
    short_description: '',
    description: '',
    specifications_text: '',
    price: '',
    cost_price: '',
    stock: 0,
    min_stock_alert: 5,
    weight: '',
    is_active: true,
    is_featured: false,
});

function submit() {
    form
        .transform((data) => ({
            ...data,
            category_id: data.category_id === '' || data.category_id === null ? null : Number(data.category_id),
            cost_price: data.cost_price === '' || data.cost_price === null ? null : data.cost_price,
            weight: data.weight === '' || data.weight === null ? null : data.weight,
            stock: Number(data.stock),
            min_stock_alert: Number(data.min_stock_alert),
            is_active: !!data.is_active,
            is_featured: !!data.is_featured,
            media_image_urls_json: JSON.stringify(gallery.value),
        }))
        .post(route('admin.products.store'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Tambah Produk — Admin" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <Link :href="route('admin.products.index')" class="btn btn-ghost btn-sm">← Kembali</Link>
                    <div class="space-y-1">
                        <h1 class="text-2xl font-bold">Tambah Produk</h1>
                        <p class="text-sm text-base-content/60">Susun informasi produk dengan ritme form yang sama di seluruh modul commerce.</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <ProductForm v-model:gallery="gallery" mode="create" :form="form" :categories="categories" />
            </form>
        </div>
    </AdminLayout>
</template>
