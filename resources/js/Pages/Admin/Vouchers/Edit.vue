<script setup>
import SingleMediaPicker from '@/Components/Admin/SingleMediaPicker.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
    voucher: { type: Object, required: true },
});

const form = useForm({
    code: props.voucher.code,
    description: props.voucher.description ?? '',
    discount_type: props.voucher.discount_type,
    discount_value: Number(props.voucher.discount_value),
    maximum_discount_amount: props.voucher.maximum_discount_amount,
    minimum_order_amount: props.voucher.minimum_order_amount,
    usage_limit: props.voucher.usage_limit,
    per_user_limit: props.voucher.per_user_limit,
    starts_at: props.voucher.starts_at ?? '',
    ends_at: props.voucher.ends_at ?? '',
    is_active: props.voucher.is_active,
    image_url: props.voucher.image_url ?? '',
});

function submit() {
    form
        .transform((d) => ({
            ...d,
            code: String(d.code).trim().toUpperCase(),
            discount_value: Number(d.discount_value),
            maximum_discount_amount:
                d.maximum_discount_amount === '' || d.maximum_discount_amount === null ? null : Number(d.maximum_discount_amount),
            minimum_order_amount: d.minimum_order_amount === '' || d.minimum_order_amount === null ? null : Number(d.minimum_order_amount),
            usage_limit: d.usage_limit === '' || d.usage_limit === null ? null : Number(d.usage_limit),
            per_user_limit: d.per_user_limit === '' || d.per_user_limit === null ? null : Number(d.per_user_limit),
            is_active: !!d.is_active,
        }))
        .put(route('admin.vouchers.update', props.voucher.id), { preserveScroll: true });
}
</script>

<template>
    <Head title="Edit Voucher — Admin" />
    <AdminLayout>
        <div class="mx-auto max-w-lg space-y-6">
            <div class="flex items-center gap-3">
                <Link :href="route('admin.vouchers.index')" class="btn btn-ghost btn-sm">← Kembali</Link>
                <h1 class="text-2xl font-bold">Edit Voucher</h1>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body gap-4">
                    <form class="space-y-4" @submit.prevent="submit">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Kode *</legend>
                            <input v-model="form.code" type="text" class="input input-bordered w-full font-mono uppercase" required />
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Deskripsi</legend>
                            <textarea v-model="form.description" rows="2" class="textarea textarea-bordered w-full" />
                        </fieldset>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Tipe diskon *</legend>
                                <select v-model="form.discount_type" class="select select-bordered w-full">
                                    <option value="percent">Persen</option>
                                    <option value="fixed">Nominal</option>
                                </select>
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Nilai *</legend>
                                <input v-model.number="form.discount_value" type="number" min="0" step="0.01" class="input input-bordered w-full" required />
                            </fieldset>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Maks. diskon</legend>
                                <input v-model.number="form.maximum_discount_amount" type="number" min="0" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Min. order</legend>
                                <input v-model.number="form.minimum_order_amount" type="number" min="0" class="input input-bordered w-full" />
                            </fieldset>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Limit total</legend>
                                <input v-model.number="form.usage_limit" type="number" min="1" class="input input-bordered w-full" />
                            </fieldset>
                            <fieldset class="fieldset">
                                <legend class="fieldset-legend">Limit per user</legend>
                                <input v-model.number="form.per_user_limit" type="number" min="1" class="input input-bordered w-full" />
                            </fieldset>
                        </div>
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
                            <Link :href="route('admin.vouchers.index')" class="btn btn-ghost">Batal</Link>
                            <button type="submit" class="btn btn-primary" :disabled="form.processing">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
