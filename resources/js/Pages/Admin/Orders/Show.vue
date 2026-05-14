<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({ order: { type: Object, required: true } });

const statusForm = useForm({
    status: props.order.status,
    payment_status: props.order.payment_status,
    tracking_number: props.order.shipping_address?.tracking ?? '',
});

function saveStatus() {
    statusForm.patch(route('admin.orders.updateStatus', props.order.id), { preserveScroll: true });
}
</script>

<template>
    <Head :title="`Pesanan ${order.order_number} — Admin`" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <Link :href="route('admin.orders.index')" class="btn btn-ghost btn-sm">← Kembali</Link>
                    <div>
                        <h1 class="text-2xl font-bold">#{{ order.order_number }}</h1>
                        <p class="text-sm text-base-content/60">{{ order.placed_at }}</p>
                    </div>
                </div>
                <a :href="route('admin.orders.invoice', order.order_number)" target="_blank" class="btn btn-outline btn-sm">Invoice</a>
            </div>
            <div class="grid gap-6 xl:grid-cols-3">
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body space-y-4">
                        <h2 class="card-title text-base">Perbarui status</h2>
                        <form class="space-y-3" @submit.prevent="saveStatus">
                            <label class="form-control">
                                <span class="label-text text-sm">Status pesanan</span>
                                <select v-model="statusForm.status" class="select select-bordered select-sm w-full" required>
                                    <option v-for="s in ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled']" :key="s" :value="s">{{ s }}</option>
                                </select>
                            </label>
                            <label class="form-control">
                                <span class="label-text text-sm">Status pembayaran</span>
                                <select v-model="statusForm.payment_status" class="select select-bordered select-sm w-full">
                                    <option v-for="p in ['pending', 'paid', 'failed', 'refunded']" :key="p" :value="p">{{ p }}</option>
                                </select>
                            </label>
                            <p v-if="order.payment" class="text-xs text-base-content/50">Provider: {{ order.payment.provider }}</p>
                            <label class="form-control">
                                <span class="label-text text-sm">No. resi</span>
                                <input v-model="statusForm.tracking_number" type="text" class="input input-bordered input-sm w-full" />
                            </label>
                            <button type="submit" class="btn btn-primary btn-sm w-full" :disabled="statusForm.processing">Simpan</button>
                        </form>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-base">Pelanggan</h2>
                        <p class="font-medium">{{ order.customer_name }}</p>
                        <p class="text-sm text-base-content/60">{{ order.customer_email }}</p>
                        <p v-if="order.customer_phone" class="text-sm text-base-content/60">{{ order.customer_phone }}</p>
                    </div>
                </div>
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h2 class="card-title text-base">Alamat kirim</h2>
                        <div v-if="order.shipping_address" class="space-y-1 text-sm">
                            <p>{{ order.shipping_address.line_1 }}</p>
                            <p>{{ order.shipping_address.city }}, {{ order.shipping_address.province }}</p>
                            <p>{{ order.shipping_address.postal_code }}, {{ order.shipping_address.country }}</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base">Item</h2>
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Produk</th>
                                <th class="text-right">Qty</th>
                                <th class="text-right">Harga</th>
                                <th class="text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="it in order.items" :key="it.id">
                                <td>
                                    <div class="flex items-center gap-2">
                                        <img v-if="it.image_url" :src="it.image_url" class="h-10 w-10 rounded object-cover" alt="" />
                                        <span>{{ it.product_name }}</span>
                                    </div>
                                </td>
                                <td class="text-right">{{ it.quantity }}</td>
                                <td class="text-right">{{ it.unit_price }}</td>
                                <td class="text-right">{{ it.total }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <div class="mt-4 flex justify-end text-sm">
                        <div class="space-y-1 text-right">
                            <p>Subtotal: {{ order.subtotal }}</p>
                            <p>Diskon: {{ order.discount_total }}</p>
                            <p>Ongkir: {{ order.shipping_total }}</p>
                            <p class="font-bold">Total: {{ order.currency }} {{ order.total }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
