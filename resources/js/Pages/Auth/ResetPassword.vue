<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const props = defineProps({
    token: { type: String, required: true },
    email: { type: String, default: '' },
});

const form = useForm({
    token: props.token,
    email: props.email,
    password: '',
    password_confirmation: '',
});
</script>

<template>
    <Head title="Reset password — Sense of Jewels" />

    <GuestLayout>
        <h1 class="text-xl font-semibold mb-4">Reset password</h1>
        <form class="space-y-4" @submit.prevent="form.post(route('password.store'))">
            <input type="hidden" name="token" :value="form.token" />

            <label class="form-control w-full">
                <div class="label"><span class="label-text">Email</span></div>
                <input v-model="form.email" type="email" required autofocus autocomplete="username" class="input input-bordered w-full" />
                <div v-if="form.errors.email" class="label"><span class="label-text-alt text-error">{{ form.errors.email }}</span></div>
            </label>

            <label class="form-control w-full">
                <div class="label"><span class="label-text">Password</span></div>
                <input v-model="form.password" type="password" required autocomplete="new-password" class="input input-bordered w-full" />
                <div v-if="form.errors.password" class="label"><span class="label-text-alt text-error">{{ form.errors.password }}</span></div>
            </label>

            <label class="form-control w-full">
                <div class="label"><span class="label-text">Confirm password</span></div>
                <input v-model="form.password_confirmation" type="password" required autocomplete="new-password" class="input input-bordered w-full" />
            </label>

            <div class="flex justify-end gap-2">
                <Link :href="route('login')" class="btn btn-ghost btn-sm">Cancel</Link>
                <button type="submit" class="btn btn-primary" :disabled="form.processing">Reset password</button>
            </div>
        </form>
    </GuestLayout>
</template>
