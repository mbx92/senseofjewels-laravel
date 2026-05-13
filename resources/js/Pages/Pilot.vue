<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
</script>

<template>
    <Head title="Inertia pilot" />

    <AppLayout>
        <div class="container mx-auto max-w-2xl px-4 py-16">
            <div class="card bg-base-100 border border-base-200 shadow-sm">
                <div class="card-body gap-4">
                    <h1 class="display-font text-3xl text-base-content">Inertia + Vue</h1>
                    <p class="text-sm text-base-content/60">
                        Halaman percobaan stack. Beranda dan toko lainnya masih Blade seperti sebelumnya.
                    </p>
                    <div class="flex flex-wrap gap-2">
                        <Link :href="route('home')" class="btn btn-primary">Ke beranda (Blade)</Link>
                        <Link :href="route('inertia.pilot')" class="btn btn-outline btn-sm">Muat ulang pilot</Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
