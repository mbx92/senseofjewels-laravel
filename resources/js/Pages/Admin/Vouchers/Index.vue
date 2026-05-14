<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';

defineProps({
    vouchers: { type: Object, required: true },
});

const del = ref(null);
function submitDelete() {
    if (!del.value) return;
    router.delete(route('admin.vouchers.destroy', del.value.id), {
        preserveScroll: true,
        onFinish: () => (del.value = null),
    });
}
</script>

<template>
    <Head title="Voucher — Admin" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold">Voucher</h1>
                    <p class="text-sm text-base-content/60">Kode promo terhubung ke diskon.</p>
                </div>
                <Link :href="route('admin.vouchers.create')" class="btn btn-primary btn-sm">+ Tambah</Link>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <div class="overflow-x-auto">
                    <table class="table table-zebra table-sm">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Diskon</th>
                                <th>Limit</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="v in vouchers.data" :key="v.id">
                                <td class="font-mono font-medium">{{ v.code }}</td>
                                <td>
                                    <span v-if="v.discount_type">{{ v.discount_type }} · {{ v.discount_value }}</span>
                                    <span v-else>—</span>
                                </td>
                                <td>{{ v.usage_limit ?? '∞' }}</td>
                                <td>
                                    <span v-if="v.is_active" class="badge badge-success badge-sm">Aktif</span>
                                    <span v-else class="badge badge-ghost badge-sm">Off</span>
                                </td>
                                <td class="flex flex-wrap gap-1">
                                    <Link :href="route('admin.vouchers.edit', v.id)" class="btn btn-ghost btn-xs">Edit</Link>
                                    <button type="button" class="btn btn-outline btn-error btn-xs" @click="del = v">Hapus</button>
                                </td>
                            </tr>
                            <tr v-if="!vouchers.data?.length">
                                <td colspan="5" class="py-8 text-center text-base-content/50">Belum ada voucher.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div v-if="vouchers.last_page > 1" class="card-body flex flex-wrap justify-center gap-1 pt-0">
                    <template v-for="(link, idx) in vouchers.links" :key="idx">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            class="btn btn-xs join-item"
                            :class="link.active ? 'btn-primary' : 'btn-ghost'"
                            preserve-scroll
                        >
                            <span v-html="link.label" />
                        </Link>
                    </template>
                </div>
            </div>
        </div>

        <dialog class="modal" :class="{ 'modal-open': !!del }">
            <div class="modal-box">
                <h3 class="font-bold text-lg">Hapus voucher?</h3>
                <p class="py-2 text-sm">Diskon terkait juga akan dihapus.</p>
                <div class="modal-action">
                    <button type="button" class="btn" @click="del = null">Batal</button>
                    <button type="button" class="btn btn-error" @click="submitDelete">Hapus</button>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop" @click="del = null"><button>close</button></form>
        </dialog>
    </AdminLayout>
</template>
