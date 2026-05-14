<script setup>
import AccountLayout from '@/Layouts/AccountLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();

const props = defineProps({
    user: { type: Object, required: true },
    must_verify_email: { type: Boolean, default: false },
});

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const deleteForm = useForm({
    password: '',
});

const flash = computed(() => page.props.flash ?? {});
const errors = computed(() => page.props.errors ?? {});

const showSavedProfile = ref(false);
const showSavedPassword = ref(false);
const showDeleteModal = ref(false);

watch(
    () => flash.value.status,
    (s) => {
        if (s === 'profile-updated') {
            showSavedProfile.value = true;
            setTimeout(() => {
                showSavedProfile.value = false;
            }, 2000);
        }
        if (s === 'password-updated') {
            showSavedPassword.value = true;
            setTimeout(() => {
                showSavedPassword.value = false;
            }, 2000);
        }
    },
    { immediate: true },
);

function submitProfile() {
    profileForm.patch(route('profile.update'));
}

function submitPassword() {
    passwordForm.put(route('password.update'));
}

function sendVerification() {
    router.post(route('verification.send'));
}

function submitDelete() {
    deleteForm.delete(route('profile.destroy'), {
        preserveScroll: true,
        onFinish: () => {
            if (!deleteForm.hasErrors) {
                showDeleteModal.value = false;
            }
        },
    });
}

function errUpdatePassword(field) {
    return errors.value[`updatePassword.${field}`] ?? '';
}

function errUserDeletion(field) {
    return errors.value[`userDeletion.${field}`] ?? '';
}
</script>

<template>
    <Head title="Profile — Sense of Jewels" />

    <AccountLayout active="profile">
        <div class="space-y-6">
            <div>
                <h1 class="display-font text-4xl text-base-content font-normal lg:text-5xl">Profile</h1>
                <p class="mt-2 text-sm text-base-content/55">Manage your account details and security.</p>
            </div>

            <div class="rounded-[20px] border border-base-300 bg-base-100 p-5 sm:p-6">
                <header>
                    <h2 class="text-lg font-medium text-base-content">Profile information</h2>
                    <p class="mt-1 text-sm text-base-content/60">Update your name and email.</p>
                </header>

                <form class="mt-6 space-y-6" @submit.prevent="submitProfile">
                    <div>
                        <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2" for="name">Name</label>
                        <input
                            id="name"
                            v-model="profileForm.name"
                            type="text"
                            required
                            autocomplete="name"
                            class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors"
                        />
                        <p v-if="profileForm.errors.name" class="mt-2 text-sm text-error">{{ profileForm.errors.name }}</p>
                    </div>

                    <div>
                        <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2" for="email">Email</label>
                        <input
                            id="email"
                            v-model="profileForm.email"
                            type="email"
                            required
                            autocomplete="username"
                            class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors"
                        />
                        <p v-if="profileForm.errors.email" class="mt-2 text-sm text-error">{{ profileForm.errors.email }}</p>

                        <div v-if="must_verify_email" class="mt-2 text-sm text-base-content/70">
                            <p>Your email is unverified.</p>
                            <button type="button" class="underline text-primary" @click="sendVerification">Resend verification email</button>
                            <p v-if="flash.status === 'verification-link-sent'" class="mt-2 text-success font-medium text-sm">
                                A new verification link has been sent.
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="text-[11px] uppercase tracking-widest bg-neutral text-neutral-content px-6 py-2.5 hover:bg-neutral/80 transition-colors" :disabled="profileForm.processing">
                            Save
                        </button>
                        <p v-show="showSavedProfile" class="text-sm text-base-content/55">Saved.</p>
                    </div>
                </form>
            </div>

            <div class="rounded-[20px] border border-base-300 bg-base-100 p-5 sm:p-6">
                <header>
                    <h2 class="text-lg font-medium text-base-content">Update password</h2>
                    <p class="mt-1 text-sm text-base-content/60">Use a long, random password.</p>
                </header>

                <form class="mt-6 space-y-6" @submit.prevent="submitPassword">
                    <div>
                        <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2" for="cur">Current password</label>
                        <input
                            id="cur"
                            v-model="passwordForm.current_password"
                            type="password"
                            autocomplete="current-password"
                            class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors"
                        />
                        <p v-if="errUpdatePassword('current_password')" class="mt-2 text-sm text-error">{{ errUpdatePassword('current_password') }}</p>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2" for="pw">New password</label>
                        <input
                            id="pw"
                            v-model="passwordForm.password"
                            type="password"
                            autocomplete="new-password"
                            class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors"
                        />
                        <p v-if="errUpdatePassword('password')" class="mt-2 text-sm text-error">{{ errUpdatePassword('password') }}</p>
                    </div>
                    <div>
                        <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2" for="pwc">Confirm password</label>
                        <input
                            id="pwc"
                            v-model="passwordForm.password_confirmation"
                            type="password"
                            autocomplete="new-password"
                            class="w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors"
                        />
                        <p v-if="errUpdatePassword('password_confirmation')" class="mt-2 text-sm text-error">{{ errUpdatePassword('password_confirmation') }}</p>
                    </div>
                    <div class="flex items-center gap-4">
                        <button type="submit" class="text-[11px] uppercase tracking-widest bg-neutral text-neutral-content px-6 py-2.5 hover:bg-neutral/80 transition-colors" :disabled="passwordForm.processing">
                            Save
                        </button>
                        <p v-show="showSavedPassword" class="text-sm text-base-content/55">Saved.</p>
                    </div>
                </form>
            </div>

            <div class="rounded-[20px] border border-error/30 bg-base-100 p-5 sm:p-6">
                <header>
                    <h2 class="text-lg font-medium text-base-content">Delete account</h2>
                    <p class="mt-1 text-sm text-base-content/60">This permanently deletes your account and data.</p>
                </header>
                <button type="button" class="mt-6 text-[11px] uppercase tracking-widest bg-error text-error-content px-6 py-2.5 hover:bg-error/90 transition-colors" @click="showDeleteModal = true">
                    Delete account
                </button>
            </div>
        </div>

        <div v-if="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-black/40 p-4" @click.self="showDeleteModal = false">
            <div class="bg-base-100 max-w-md w-full p-6 shadow-xl border border-base-300">
                <h2 class="text-lg font-medium text-base-content">Are you sure?</h2>
                <p class="mt-1 text-sm text-base-content/60">Enter your password to confirm permanent deletion.</p>
                <form class="mt-6" @submit.prevent="submitDelete">
                    <label class="block text-[10px] uppercase tracking-[0.2em] text-base-content/50 mb-2" for="delpw">Password</label>
                    <input
                        id="delpw"
                        v-model="deleteForm.password"
                        type="password"
                        class="block w-full border-b border-base-content/20 bg-transparent py-2.5 text-sm focus:outline-none focus:border-primary transition-colors"
                    />
                    <p v-if="errUserDeletion('password')" class="mt-2 text-sm text-error">{{ errUserDeletion('password') }}</p>
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" class="text-[10px] uppercase tracking-[0.2em] text-base-content/60" @click="showDeleteModal = false">Cancel</button>
                        <button type="submit" class="text-[11px] uppercase tracking-widest bg-error text-error-content px-5 py-2.5" :disabled="deleteForm.processing">Delete</button>
                    </div>
                </form>
            </div>
        </div>
    </AccountLayout>
</template>
