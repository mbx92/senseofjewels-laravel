<script setup>
import SingleMediaPicker from '@/Components/Admin/SingleMediaPicker.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
    discount: { type: Object, required: true },
    categories: { type: Array, required: true },
    products: { type: Array, required: true },
});

const form = useForm({
    name: props.discount.name,
    code: props.discount.code ?? '',
    description: props.discount.description ?? '',
    type: props.discount.type,
    value: Number(props.discount.value),
    applies_to: props.discount.applies_to,
    category_ids: (props.discount.category_ids ?? []).map((id) => Number(id)),
    product_ids: (props.discount.product_ids ?? []).map((id) => Number(id)),
    minimum_order_amount: props.discount.minimum_order_amount,
    maximum_discount_amount: props.discount.maximum_discount_amount,
    usage_limit: props.discount.usage_limit,
    starts_at: props.discount.starts_at ?? '',
    ends_at: props.discount.ends_at ?? '',
    is_active: props.discount.is_active,
    image_url: props.discount.image_url ?? '',
});

function submit() {
    form
        .transform((d) => ({
            ...d,
            value: Number(d.value),
            minimum_order_amount: d.minimum_order_amount === '' || d.minimum_order_amount === null ? null : Number(d.minimum_order_amount),
            maximum_discount_amount: d.maximum_discount_amount === '' || d.maximum_discount_amount === null ? null : Number(d.maximum_discount_amount),
            usage_limit: d.usage_limit === '' || d.usage_limit === null ? null : Number(d.usage_limit),
            is_active: !!d.is_active,
            category_ids: d.applies_to === 'category' ? d.category_ids : [],
            product_ids: d.applies_to === 'product' ? d.product_ids : [],
        }))
        .put(route('admin.discounts.update', props.discount.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Edit Diskon — Admin" />
    <AdminLayout>
        <div class="mx-auto max-w-2xl space-y-6">
            <div class="flex items-center gap-3">
                <Link :href="route('admin.discounts.index')" class="btn btn-ghost btn-sm">← Kembali</Link>
                <h1 class="text-2xl font-bold">Edit Diskon</h1>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <form class="space-y-4" @submit.prevent="submit">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Nama *</legend>
                            <input v-model="form.name" type="text" class="input input-bordered w-full" required />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Kode</legend>
                            <input v-model="form.code" type="text" class="input input-bordered w-full uppercase" />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Deskripsi</legend>
                            <textarea v-model="form.description" rows="2" class="textarea textarea-bordered w-full" />
                        </fieldset>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Tipe *</legend>
                                <select v-model="form.type" class="select select-bordered w-full">
                                    <option value="percent">Persen</option>
                                    <option value="fixed">Nominal</option>
                                </select>
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Nilai *</legend>
                                <input v-model.number="form.value" type="number" min="0" step="0.01" class="input input-bordered w-full" required />
                            </fieldset>
                        </div>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Berlaku untuk *</legend>
                            <select v-model="form.applies_to" class="select select-bordered w-full">
                                <option value="all">Semua</option>
                                <option value="category">Kategori tertentu</option>
                                <option value="product">Produk tertentu</option>
                            </select>
                        </fieldset>
                        <fieldset v-if="form.applies_to === 'category'" class="fieldset">
                            <legend class="fieldset-legend">Kategori</legend>
                            <div class="max-h-40 space-y-1 overflow-y-auto rounded border border-base-300 p-2">
                                <label v-for="c in categories" :key="c.id" class="flex cursor-pointer items-center gap-2 text-sm">
                                    <input v-model="form.category_ids" type="checkbox" class="checkbox checkbox-sm" :value="c.id" />
                                    {{ c.name }}
                                </label>
                            </div>
                        </fieldset>
                        <fieldset v-if="form.applies_to === 'product'" class="fieldset">
                            <legend class="fieldset-legend">Produk</legend>
                            <div class="max-h-48 space-y-1 overflow-y-auto rounded border border-base-300 p-2">
                                <label v-for="p in products" :key="p.id" class="flex cursor-pointer items-center gap-2 text-sm">
                                    <input v-model="form.product_ids" type="checkbox" class="checkbox checkbox-sm" :value="p.id" />
                                    {{ p.name }}
                                </label>
                            </div>
                        </fieldset>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Min. order</legend>
                                <input v-model.number="form.minimum_order_amount" type="number" min="0" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Maks. diskon</legend>
                                <input v-model.number="form.maximum_discount_amount" type="number" min="0" class="input input-bordered w-full" />
                            </fieldset>
                        </div>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Batas pemakaian</legend>
                            <input v-model.number="form.usage_limit" type="number" min="1" class="input input-bordered w-full max-w-xs" />
                        </fieldset>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Mulai</legend>
                                <input v-model="form.starts_at" type="date" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Berakhir</legend>
                                <input v-model="form.ends_at" type="date" class="input input-bordered w-full" />
                            </fieldset>
                        </div>
                        <SingleMediaPicker v-model="form.image_url" label="Gambar" />
                        <label class="flex items-center gap-2">
                            <input v-model="form.is_active" type="checkbox" class="toggle toggle-success" :true-value="true" :false-value="false" />
                            <span class="text-sm">Aktif</span>
                        </label>
                        <div class="flex justify-end gap-2">
                            <Link :href="route('admin.discounts.index')" class="btn btn-ghost">Batal</Link>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
