<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();
const flash = computed(() => page.props.flash ?? {});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});
</script>

<template>
    <Head title="Log in — Sense of Jewels" />

    <GuestLayout>
        <div class="space-y-2">
            <h1 class="text-2xl font-semibold">Customer Login</h1>
            <p class="text-sm text-base-content/70">Access your profile, checkout, and order tracking.</p>
        </div>

        <div v-if="flash.status" role="alert" class="alert alert-success mt-4">
            <span>{{ flash.status }}</span>
        </div>

        <form class="mt-6 space-y-4" @submit.prevent="form.post(route('login'))">
            <label class="form-control w-full">
                <div class="label"><span class="label-text">Email</span></div>
                <input v-model="form.email" type="email" required autofocus autocomplete="username" class="input input-bordered w-full" />
                <div v-if="form.errors.email" class="label">
                    <span class="label-text-alt text-error">{{ form.errors.email }}</span>
                </div>
            </label>

            <label class="form-control w-full">
                <div class="label"><span class="label-text">Password</span></div>
                <input v-model="form.password" type="password" required autocomplete="current-password" class="input input-bordered w-full" />
                <div v-if="form.errors.password" class="label">
                    <span class="label-text-alt text-error">{{ form.errors.password }}</span>
                </div>
            </label>

            <label class="label cursor-pointer justify-start gap-3">
                <input v-model="form.remember" type="checkbox" class="checkbox checkbox-sm" />
                <span class="label-text">Remember me</span>
            </label>

            <div class="flex flex-col gap-3 pt-2 sm:flex-row sm:items-center sm:justify-between">
                <Link :href="route('password.request')" class="link link-hover text-sm">Forgot your password?</Link>
                <button type="submit" class="btn btn-primary" :disabled="form.processing">Log in</button>
            </div>
        </form>

        <p class="mt-4 text-sm text-base-content/70">
            New customer?
            <Link :href="route('register')" class="link link-primary">Create an account</Link>
        </p>
    </GuestLayout>
</template>
