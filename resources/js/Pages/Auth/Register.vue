<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});
</script>

<template>
    <Head title="Register — Sense of Jewels" />

    <GuestLayout>
        <div class="space-y-2">
            <h1 class="text-2xl font-semibold">Create Account</h1>
            <p class="text-sm text-base-content/70">Register to manage your orders and save your details.</p>
        </div>

        <form class="mt-6 space-y-4" @submit.prevent="form.post(route('register'))">
            <label class="form-control w-full">
                <div class="label"><span class="label-text">Full name</span></div>
                <input v-model="form.name" type="text" required autofocus autocomplete="name" class="input input-bordered w-full" />
                <div v-if="form.errors.name" class="label"><span class="label-text-alt text-error">{{ form.errors.name }}</span></div>
            </label>

            <label class="form-control w-full">
                <div class="label"><span class="label-text">Email</span></div>
                <input v-model="form.email" type="email" required autocomplete="username" class="input input-bordered w-full" />
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

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <Link :href="route('login')" class="link link-hover text-sm">Already registered?</Link>
                <button type="submit" class="btn btn-primary" :disabled="form.processing">Register</button>
            </div>
        </form>
    </GuestLayout>
</template>
