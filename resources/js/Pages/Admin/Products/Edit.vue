<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { route } from 'ziggy-js';
import ProductForm from './Partials/ProductForm.vue';

const props = defineProps({
    categories: { type: Array, required: true },
    product: { type: Object, required: true },
});

const gallery = ref([]);

const existingImages = computed(() => props.product.images ?? []);

const form = useForm({
    name: props.product.name,
    sku: props.product.sku,
    category_id: props.product.category_id != null ? String(props.product.category_id) : '',
    short_description: props.product.short_description ?? '',
    description: props.product.description ?? '',
    specifications_text: props.product.specifications_text ?? '',
    price: props.product.price,
    cost_price: props.product.cost_price ?? '',
    stock: props.product.stock,
    min_stock_alert: props.product.min_stock_alert ?? 5,
    weight: props.product.weight ?? '',
    is_active: !!props.product.is_active,
    is_featured: !!props.product.is_featured,
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
        .put(route('admin.products.update', props.product.id), { preserveScroll: true });
}
</script>

<template>
    <Head :title="`Edit Produk — ${product.name}`" />

    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-3">
                    <Link :href="route('admin.products.index')" class="btn btn-ghost btn-sm">← Kembali</Link>
                    <div class="space-y-1">
                        <h1 class="text-2xl font-bold">Edit Produk</h1>
                        <p class="text-sm text-base-content/60">Rapikan konten, gambar, dan status produk tanpa kehilangan ritme vertikal form.</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit">
                <ProductForm v-model:gallery="gallery" mode="edit" :form="form" :categories="categories" :existing-images="existingImages" />
            </form>
        </div>
    </AdminLayout>
</template>
