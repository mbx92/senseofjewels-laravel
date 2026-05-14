<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const form = useForm({
    password: '',
});
</script>

<template>
    <Head title="Confirm password — Sense of Jewels" />

    <GuestLayout>
        <p class="text-sm text-base-content/70 mb-4">This is a secure area. Please confirm your password before continuing.</p>
        <form @submit.prevent="form.post(route('password.confirm'))">
            <label class="form-control w-full">
                <div class="label"><span class="label-text">Password</span></div>
                <input v-model="form.password" type="password" required autocomplete="current-password" class="input input-bordered w-full" />
                <div v-if="form.errors.password" class="label"><span class="label-text-alt text-error">{{ form.errors.password }}</span></div>
            </label>
            <button type="submit" class="btn btn-primary mt-4 w-full" :disabled="form.processing">Confirm</button>
        </form>
    </GuestLayout>
</template>
