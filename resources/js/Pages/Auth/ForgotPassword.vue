<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();
const flash = computed(() => page.props.flash ?? {});

const form = useForm({
    email: '',
});
</script>

<template>
    <Head title="Forgot password — Sense of Jewels" />

    <GuestLayout>
        <div class="mb-4 text-sm text-base-content/70">Forgot your password? Enter your email and we will send a reset link.</div>

        <div v-if="flash.status" class="alert alert-success mb-4 text-sm">{{ flash.status }}</div>

        <form @submit.prevent="form.post(route('password.email'))">
            <label class="form-control w-full">
                <div class="label"><span class="label-text">Email</span></div>
                <input v-model="form.email" type="email" required autofocus class="input input-bordered w-full" />
                <div v-if="form.errors.email" class="label"><span class="label-text-alt text-error">{{ form.errors.email }}</span></div>
            </label>
            <div class="mt-4 flex items-center justify-between">
                <Link :href="route('login')" class="link text-sm">Back to login</Link>
                <button type="submit" class="btn btn-primary btn-sm" :disabled="form.processing">Email reset link</button>
            </div>
        </form>
    </GuestLayout>
</template>
