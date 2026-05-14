<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    active: {
        type: String,
        default: 'profile',
    },
});

const page = usePage();
const user = computed(() => page.props.auth?.user ?? null);
</script>

<template>
    <AppLayout>
        <div class="mx-auto w-full max-w-[1480px] px-4 sm:px-5 lg:px-6 xl:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-12 lg:gap-8 xl:gap-10">
                <aside class="lg:col-span-4 lg:sticky lg:top-28 lg:self-start">
                    <div class="space-y-7 rounded-[24px] border border-base-300 bg-base-100/90 p-5 lg:p-6">
                        <div v-if="user" class="pb-7 border-b border-base-300">
                            <p class="text-[11px] uppercase tracking-[0.3em] text-base-content/40 mb-3">My Account</p>
                            <p class="text-xl font-medium text-base-content leading-snug">{{ user.name }}</p>
                            <p class="text-sm text-base-content/45 mt-1.5 truncate">{{ user.email }}</p>
                        </div>

                        <nav
                            class="flex flex-row gap-0 overflow-x-auto no-scrollbar -mx-4 px-4 border-b border-base-300 sm:mx-0 sm:px-0 lg:flex-col lg:border-b-0"
                        >
                            <Link
                                :href="route('profile.edit')"
                                class="shrink-0 flex items-center gap-3 py-3.5 pr-8 text-sm uppercase tracking-[0.2em] font-semibold lg:border-b lg:border-base-200 lg:pr-0 lg:py-4 transition-colors"
                                :class="active === 'profile' ? 'text-primary' : 'text-base-content/50 hover:text-base-content'"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="shrink-0">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                Profile
                            </Link>
                            <Link
                                :href="route('orders.index')"
                                class="shrink-0 flex items-center gap-3 py-3.5 pr-8 text-sm uppercase tracking-[0.2em] font-semibold lg:border-b lg:border-base-200 lg:pr-0 lg:py-4 transition-colors"
                                :class="active === 'orders' ? 'text-primary' : 'text-base-content/50 hover:text-base-content'"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="shrink-0">
                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                    <rect x="8" y="2" width="8" height="4" rx="1" />
                                    <path d="M9 12h6M9 16h4" />
                                </svg>
                                Orders
                            </Link>
                            <Link
                                :href="route('account.tracking')"
                                class="shrink-0 flex items-center gap-3 py-3.5 pr-8 text-sm uppercase tracking-[0.2em] font-semibold lg:border-b lg:border-base-200 lg:pr-0 lg:py-4 transition-colors"
                                :class="active === 'tracking' ? 'text-primary' : 'text-base-content/50 hover:text-base-content'"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="shrink-0">
                                    <circle cx="12" cy="12" r="10" />
                                    <polyline points="12 6 12 12 16 14" />
                                </svg>
                                Tracking
                            </Link>
                            <Link
                                :href="route('account.reviews')"
                                class="shrink-0 flex items-center gap-3 py-3.5 pr-8 text-sm uppercase tracking-[0.2em] font-semibold lg:border-b lg:border-base-200 lg:pr-0 lg:py-4 transition-colors"
                                :class="active === 'reviews' ? 'text-primary' : 'text-base-content/50 hover:text-base-content'"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" class="shrink-0">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                </svg>
                                Reviews
                            </Link>
                        </nav>

                        <form v-if="user" method="POST" :action="route('logout')" class="hidden lg:block pt-2">
                            <input type="hidden" name="_token" :value="page.props.csrf" />
                            <button type="submit" class="text-xs uppercase tracking-[0.22em] text-base-content/30 hover:text-error/70 transition-colors">
                                Sign Out
                            </button>
                        </form>
                    </div>
                </aside>

                <section class="min-w-0 rounded-[24px] border border-base-300 bg-base-100/90 p-5 shadow-sm sm:p-6 lg:col-span-8 lg:p-7 xl:p-8">
                    <slot />
                </section>
            </div>
        </div>
    </AppLayout>
</template>
