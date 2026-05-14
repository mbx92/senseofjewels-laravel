<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';

defineProps({ testimonials: { type: Array, required: true } });
const del = ref(null);
function submitDelete() {
    if (!del.value) return;
    router.delete(route('admin.testimonials.destroy', del.value.id), { preserveScroll: true, onFinish: () => (del.value = null) });
}
</script>

<template>
    <Head title="Testimonials — Admin" />
    <AdminLayout>
        <div class="space-y-6">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <h1 class="text-2xl font-bold">Testimonials</h1>
                </div>
                <Link :href="route('admin.testimonials.create')" class="btn btn-primary btn-sm">+ Tambah</Link>
            </div>
            <div class="card bg-base-100 shadow-sm">
                <table class="table table-zebra">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Rating</th>
                            <th>Urutan</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="t in testimonials" :key="t.id">
                            <td>{{ t.name }}</td>
                            <td>{{ t.rating }} ★</td>
                            <td>{{ t.sort_order }}</td>
                            <td class="flex gap-1">
                                <Link :href="route('admin.testimonials.edit', t.id)" class="btn btn-ghost btn-xs">Edit</Link>
                                <button type="button" class="btn btn-outline btn-error btn-xs" @click="del = t">Hapus</button>
                            </td>
                        </tr>
                        <tr v-if="!testimonials.length">
                            <td colspan="4" class="py-8 text-center text-base-content/50">Belum ada data.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div v-if="del" class="fixed inset-0 z-50 flex items-center justify-center bg-black/40" @click.self="del = null">
            <div class="card w-80 bg-base-100 shadow-xl">
                <div class="card-body">
                    <h3 class="card-title text-base">Hapus testimonial?</h3>
                    <p class="text-sm">{{ del.name }}</p>
                    <div class="card-actions justify-end">
                        <button type="button" class="btn btn-ghost btn-sm" @click="del = null">Batal</button>
                        <button type="button" class="btn btn-error btn-sm" @click="submitDelete">Hapus</button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
