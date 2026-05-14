<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();
const flash = computed(() => page.props.flash ?? {});

const form = useForm({});
</script>

<template>
    <Head title="Verify email — Sense of Jewels" />

    <GuestLayout>
        <p class="text-sm text-base-content/70 mb-4">Thanks for signing up! Please verify your email by clicking the link we sent.</p>

        <div v-if="flash.status === 'verification-link-sent'" class="alert alert-success text-sm mb-4">A new verification link has been sent.</div>

        <form @submit.prevent="form.post(route('verification.send'))">
            <button type="submit" class="btn btn-outline btn-sm" :disabled="form.processing">Resend email</button>
        </form>

        <Link :href="route('logout')" method="post" as="button" class="btn btn-ghost btn-sm mt-6">Log out</Link>
    </GuestLayout>
</template>
