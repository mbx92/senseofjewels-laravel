<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
    logs: { type: Object, required: true },
    products: { type: Array, required: true },
    inventoryEnabled: { type: Boolean, required: true },
});

const form = useForm({
    product_id: '',
    type: 'in',
    quantity: 1,
    note: '',
});

function submitAdjust() {
    form.transform((d) => ({
        ...d,
        product_id: Number(d.product_id),
        quantity: Number(d.quantity),
    })).post(route('admin.inventory.adjust'), { preserveScroll: true });
}
</script>

<template>
    <Head title="Inventory — Admin" />
    <AdminLayout>
        <div class="space-y-8">
            <div>
                <h1 class="text-2xl font-bold">Inventory</h1>
                <p class="text-sm text-base-content/60">Riwayat penyesuaian stok.</p>
            </div>
            <div v-if="!inventoryEnabled" class="alert alert-warning">
                <span>Inventory dinonaktifkan di pengaturan — penyesuaian diblokir.</span>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-base">Penyesuaian cepat</h2>
                    <form class="mt-4 grid gap-4 md:grid-cols-2 lg:grid-cols-4" @submit.prevent="submitAdjust">
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Produk *</legend>
                            <select v-model="form.product_id" class="select select-bordered w-full" required :disabled="!inventoryEnabled">
                                <option value="" disabled>Pilih…</option>
                                <option v-for="p in products" :key="p.id" :value="String(p.id)">{{ p.name }} ({{ p.sku }} — stok {{ p.stock }})</option>
                            </select>
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Tipe *</legend>
                            <select v-model="form.type" class="select select-bordered w-full" :disabled="!inventoryEnabled">
                                <option value="in">Masuk (+)</option>
                                <option value="out">Keluar (−)</option>
                                <option value="adjustment">Set ke nilai</option>
                            </select>
                        </fieldset>
                        <fieldset class="fieldset">
                            <legend class="fieldset-legend">Qty / nilai target *</legend>
                            <input v-model.number="form.quantity" type="number" min="1" class="input input-bordered w-full" :disabled="!inventoryEnabled" />
                        </fieldset>
                        <fieldset class="fieldset md:col-span-2 lg:col-span-4">
                            <legend class="fieldset-legend">Catatan *</legend>
                            <input v-model="form.note" type="text" class="input input-bordered w-full" required :disabled="!inventoryEnabled" />
                        </fieldset>
                        <div class="md:col-span-2 lg:col-span-4">
                            <button type="submit" class="btn btn-primary btn-sm" :disabled="!inventoryEnabled || form.processing">Terapkan</button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-sm">
                        <thead>
                            <tr>
                                <th>Waktu</th>
                                <th>Produk</th>
                                <th>User</th>
                                <th>Tipe</th>
                                <th class="text-right">Δ</th>
                                <th class="text-right">Sebelum</th>
                                <th class="text-right">Sesudah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="log in logs.data" :key="log.id">
                                <td class="whitespace-nowrap text-xs">{{ log.created_at }}</td>
                                <td>{{ log.product_name }}</td>
                                <td>{{ log.user_name ?? '—' }}</td>
                                <td>{{ log.type }}</td>
                                <td class="text-right">{{ log.quantity }}</td>
                                <td class="text-right">{{ log.stock_before }}</td>
                                <td class="text-right">{{ log.stock_after }}</td>
                            </tr>
                            <tr v-if="!logs.data?.length">
                                <td colspan="7" class="py-8 text-center text-base-content/50">Belum ada log.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="logs.last_page > 1" class="card-body flex flex-wrap justify-center gap-1 pt-0">
                    <template v-for="(link, idx) in logs.links" :key="idx">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="btn btn-xs join-item"
                            :class="link.active ? 'btn-primary' : 'btn-ghost'"
                            preserve-scroll
                        >
                            <span v-html="link.label"></span>
                        </Link>
                    </template>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
