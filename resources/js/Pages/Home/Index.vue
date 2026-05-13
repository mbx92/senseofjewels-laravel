<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { computed, onMounted, watch } from 'vue';
import HeroSection from './HeroSection.vue';
import LandingSections from './LandingSections.vue';

const page = usePage();
const hero = computed(() => page.props.hero ?? { enabled: false });

function notifyStatus(message) {
    if (!message || typeof window.showAppToast !== 'function') return;
    window.showAppToast(message, 'success');
}

onMounted(() => {
    notifyStatus(page.props.flash?.status);
});

watch(
    () => page.props.flash?.status,
    (msg) => {
        if (msg) notifyStatus(msg);
    },
);
</script>

<template>
    <Head title="Sense of Jewels" />

    <AppLayout>
        <HeroSection v-if="hero.enabled" :hero="hero" />
        <LandingSections />
    </AppLayout>
</template>
