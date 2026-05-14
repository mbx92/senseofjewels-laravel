<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    orders: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
});

const search = ref(props.filters.search ?? '');
let t = null;
watch(search, () => {
    clearTimeout(t);
    t = setTimeout(() => {
        router.get(
            route('admin.orders.index'),
            {
                search: search.value || undefined,
                status: props.filters.status || undefined,
                payment_status: props.filters.payment_status || undefined,
            },
            { preserveState: true, replace: true },
        );
    }, 350);
});

function filterStatus(s) {
    router.get(
        route('admin.orders.index'),
        {
            search: props.filters.search || undefined,
            status: s || undefined,
            payment_status: props.filters.payment_status || undefined,
        },
        { preserveState: true, replace: true },
    );
}

function filterPayment(p) {
    router.get(
        route('admin.orders.index'),
        {
            search: props.filters.search || undefined,
            status: props.filters.status || undefined,
            payment_status: p || undefined,
        },
        { preserveState: true, replace: true },
    );
}
</script>

<template>
    <Head title="Pesanan — Admin" />
    <AdminLayout>
        <div class="space-y-6">
            <h1 class="text-2xl font-bold">Pesanan</h1>
            <div class="flex flex-wrap gap-3">
                <input v-model="search" type="search" placeholder="Cari nomor / nama / email…" class="input input-bordered input-sm w-full max-w-xs" />
                <select class="select select-bordered select-sm" :value="filters.status || ''" @change="filterStatus(($event.target).value)">
                    <option value="">Semua status</option>
                    <option v-for="s in ['pending', 'processing', 'shipped', 'delivered', 'completed', 'cancelled']" :key="s" :value="s">{{ s }}</option>
                </select>
                <select class="select select-bordered select-sm" :value="filters.payment_status || ''" @change="filterPayment(($event.target).value)">
                    <option value="">Semua pembayaran</option>
                    <option v-for="p in ['pending', 'paid', 'failed', 'refunded']" :key="p" :value="p">{{ p }}</option>
                </select>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Pelanggan</th>
                                <th>Status</th>
                                <th>Bayar</th>
                                <th class="text-right">Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="o in orders.data" :key="o.id">
                                <td class="font-mono text-xs">{{ o.order_number }}</td>
                                <td>{{ o.customer_name }}</td>
                                <td>{{ o.status }}</td>
                                <td>{{ o.payment_status }}</td>
                                <td class="text-right">{{ o.currency }} {{ o.total }}</td>
                                <td>
                                    <Link :href="route('admin.orders.show', o.id)" class="btn btn-ghost btn-xs">Detail</Link>
                                </td>
                            </tr>
                            <tr v-if="!orders.data?.length">
                                <td colspan="6" class="py-8 text-center text-base-content/50">Tidak ada pesanan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="orders.last_page > 1" class="card-body flex flex-wrap justify-center gap-1 pt-0">
                    <template v-for="(link, idx) in orders.links" :key="idx">
                        <Link v-if="link.url" :href="link.url" class="btn btn-xs" :class="link.active ? 'btn-primary' : 'btn-ghost'" preserve-scroll>
                            <span v-html="link.label"></span>
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
