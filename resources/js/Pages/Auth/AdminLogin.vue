<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});
</script>

<template>
    <Head title="Admin sign in — Sense of Jewels" />

    <GuestLayout>
        <div class="space-y-2 mb-10">
            <p class="text-primary text-[10px] uppercase tracking-[0.3em] mb-4">CMS Access</p>
            <h1 class="display-font text-4xl text-base-content font-normal">Welcome back.</h1>
            <p class="text-sm text-base-content/50 font-light">Sign in to manage your jewelry collections and orders.</p>
        </div>

        <form class="space-y-6" @submit.prevent="form.post(route('admin.login'))">
            <div class="space-y-1">
                <label class="text-[10px] uppercase tracking-[0.2em] text-base-content/60">Email</label>
                <input
                    v-model="form.email"
                    type="email"
                    required
                    autofocus
                    autocomplete="username"
                    class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm text-base-content placeholder:text-base-content/30 focus:outline-none focus:border-primary transition-colors"
                />
                <p v-if="form.errors.email" class="text-[10px] text-error mt-1">{{ form.errors.email }}</p>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] uppercase tracking-[0.2em] text-base-content/60">Password</label>
                <input
                    v-model="form.password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm text-base-content placeholder:text-base-content/30 focus:outline-none focus:border-primary transition-colors"
                />
            </div>

            <div class="flex items-center gap-3">
                <input id="remember" v-model="form.remember" type="checkbox" class="checkbox checkbox-xs border-base-content/30" />
                <label for="remember" class="text-[11px] text-base-content/50 cursor-pointer">Remember me</label>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full bg-neutral text-neutral-content py-3.5 text-[10px] uppercase tracking-[0.25em] font-semibold hover:bg-primary hover:text-primary-content transition-colors duration-300"
                    :disabled="form.processing"
                >
                    Sign In
                </button>
            </div>
        </form>
    </GuestLayout>
</template>
