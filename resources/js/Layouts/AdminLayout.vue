<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';
import { route } from 'ziggy-js';

const page = usePage();
const user = computed(() => page.props.auth?.user);
const canManageUsers = computed(() => page.props.auth?.can?.manage_users ?? false);
const flash = computed(() => page.props.flash ?? {});

const url = computed(() => page.url ?? '');

const mobileSidebarOpen = ref(false);

defineExpose({ mobileSidebarOpen });

const isDashboard = computed(() => {
    const u = url.value.split('?')[0];
    return u === '/admin' || u === '/admin/';
});

function activePrefix(prefix) {
    const u = url.value.split('?')[0];
    return u === prefix || u.startsWith(`${prefix}/`);
}

function closeSidebar() {
    mobileSidebarOpen.value = false;
}

let progressTimer = null;
function startBar() {
    const bar = document.getElementById('admin-nav-progress');
    if (!bar) return;
    let progress = 8;
    bar.style.opacity = '1';
    bar.style.width = `${progress}%`;
    clearInterval(progressTimer);
    progressTimer = setInterval(() => {
        progress = Math.min(progress + (100 - progress) * 0.08, 92);
        bar.style.width = `${progress}%`;
    }, 120);
}
function finishBar() {
    const bar = document.getElementById('admin-nav-progress');
    if (!bar) return;
    clearInterval(progressTimer);
    bar.style.width = '100%';
    setTimeout(() => {
        bar.style.opacity = '0';
        bar.style.width = '0%';
    }, 180);
}

let offStart = () => {};
let offFinish = () => {};

onMounted(() => {
    offStart = router.on('start', startBar);
    offFinish = router.on('finish', finishBar);
});

onUnmounted(() => {
    offStart();
    offFinish();
    clearInterval(progressTimer);
});
</script>

<template>
    <div class="flex min-h-screen">
        <div id="admin-nav-progress" class="pointer-events-none fixed left-0 top-0 z-[100] h-[2px] w-0 bg-primary opacity-0 transition-opacity duration-200" />

        <aside class="hidden w-72 flex-shrink-0 bg-neutral text-neutral-content lg:block">
            <div class="sticky top-0 flex h-screen flex-col overflow-y-auto">
                <div class="px-6 py-6 border-b border-white/10">
                    <Link :href="route('admin.dashboard')" class="block">
                        <p class="display-font text-2xl text-primary tracking-wide leading-none">Sense of Jewels</p>
                        <p class="text-[9px] uppercase tracking-[0.25em] text-neutral-content/40 mt-1.5">Admin Dashboard</p>
                    </Link>
                </div>

                <nav class="flex-1 px-4 py-5 space-y-0.5">
                    <Link
                        :href="route('admin.dashboard')"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-none text-[11px] uppercase tracking-[0.18em] transition-colors"
                        :class="isDashboard ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                        </svg>
                        Dashboard
                    </Link>

                    <p class="px-3 pt-5 pb-2 text-[9px] uppercase tracking-[0.25em] text-neutral-content/30">Landing Page</p>
                    <Link
                        v-for="item in [
                            { label: 'Hero Section', r: 'admin.hero', m: '/admin/hero' },
                            { label: 'About', r: 'admin.about', m: '/admin/about' },
                            { label: 'Story', r: 'admin.story', m: '/admin/story' },
                            { label: 'Services', r: 'admin.services.index', m: '/admin/services' },
                            { label: 'Portfolio', r: 'admin.portfolio.index', m: '/admin/portfolio' },
                            { label: 'Testimonials', r: 'admin.testimonials.index', m: '/admin/testimonials' },
                            { label: 'Contact Info', r: 'admin.contact-settings', m: '/admin/contact-settings' },
                        ]"
                        :key="item.r"
                        :href="route(item.r)"
                        class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors"
                        :class="activePrefix(item.m) ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'"
                    >
                        {{ item.label }}
                    </Link>

                    <p class="px-3 pt-5 pb-2 text-[9px] uppercase tracking-[0.25em] text-neutral-content/30">Commerce</p>
                    <Link
                        v-for="item in [
                            { label: 'Products', r: 'admin.products.index', m: '/admin/products' },
                            { label: 'Categories', r: 'admin.categories.index', m: '/admin/categories' },
                            { label: 'Inventory', r: 'admin.inventory.index', m: '/admin/inventory' },
                            { label: 'Orders', r: 'admin.orders.index', m: '/admin/orders' },
                            { label: 'Discounts', r: 'admin.discounts.index', m: '/admin/discounts' },
                            { label: 'Vouchers', r: 'admin.vouchers.index', m: '/admin/vouchers' },
                        ]"
                        :key="item.r"
                        :href="route(item.r)"
                        class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors"
                        :class="activePrefix(item.m) ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'"
                    >
                        {{ item.label }}
                    </Link>

                    <p class="px-3 pt-5 pb-2 text-[9px] uppercase tracking-[0.25em] text-neutral-content/30">System</p>
                    <template v-if="canManageUsers">
                        <Link
                            :href="route('admin.users.index')"
                            class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors"
                            :class="activePrefix('/admin/users') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'"
                        >
                            Users
                        </Link>
                        <Link
                            :href="route('admin.roles.index')"
                            class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors"
                            :class="activePrefix('/admin/roles') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'"
                        >
                            Roles
                        </Link>
                    </template>
                    <Link
                        :href="route('admin.media.index')"
                        class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors"
                        :class="activePrefix('/admin/media') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'"
                    >
                        Media Library
                    </Link>
                    <Link
                        :href="route('admin.settings.index')"
                        class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors"
                        :class="activePrefix('/admin/settings') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'"
                    >
                        Settings
                    </Link>
                    <Link
                        :href="route('admin.integrations.index')"
                        class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors"
                        :class="activePrefix('/admin/integrations') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'"
                    >
                        Integrations
                    </Link>
                </nav>

                <div class="px-6 py-4 border-t border-white/10">
                    <a :href="route('home')" target="_blank" class="text-[10px] uppercase tracking-widest text-neutral-content/40 hover:text-primary transition-colors">← View Live Site</a>
                </div>
            </div>
        </aside>

        <aside v-show="mobileSidebarOpen" class="fixed inset-0 z-50 flex lg:hidden">
            <div class="fixed inset-0 bg-black/50" @click="closeSidebar" />
            <div class="relative w-72 flex-shrink-0 flex flex-col bg-neutral text-neutral-content overflow-y-auto">
                <div class="flex items-center justify-between px-6 py-6 border-b border-white/10">
                    <Link :href="route('admin.dashboard')" class="block" @click="closeSidebar">
                        <p class="display-font text-2xl text-primary tracking-wide leading-none">Sense of Jewels</p>
                        <p class="text-[9px] uppercase tracking-[0.25em] text-neutral-content/40 mt-1.5">Admin Dashboard</p>
                    </Link>
                    <button type="button" class="text-neutral-content/60 hover:text-neutral-content" @click="closeSidebar">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <nav class="flex-1 px-4 py-5 space-y-0.5">
                    <Link
                        :href="route('admin.dashboard')"
                        class="flex items-center gap-3 px-3 py-2.5 rounded-none text-[11px] uppercase tracking-[0.18em] transition-colors"
                        :class="isDashboard ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'"
                        @click="closeSidebar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="7" height="7" rx="1" />
                            <rect x="14" y="3" width="7" height="7" rx="1" />
                            <rect x="3" y="14" width="7" height="7" rx="1" />
                            <rect x="14" y="14" width="7" height="7" rx="1" />
                        </svg>
                        Dashboard
                    </Link>

                    <p class="px-3 pt-5 pb-2 text-[9px] uppercase tracking-[0.25em] text-neutral-content/30">Landing Page</p>
                    <Link
                        v-for="item in [
                            { label: 'Hero Section', r: 'admin.hero', m: '/admin/hero' },
                            { label: 'About', r: 'admin.about', m: '/admin/about' },
                            { label: 'Story', r: 'admin.story', m: '/admin/story' },
                            { label: 'Services', r: 'admin.services.index', m: '/admin/services' },
                            { label: 'Portfolio', r: 'admin.portfolio.index', m: '/admin/portfolio' },
                            { label: 'Testimonials', r: 'admin.testimonials.index', m: '/admin/testimonials' },
                            { label: 'Contact Info', r: 'admin.contact-settings', m: '/admin/contact-settings' },
                        ]"
                        :key="item.r"
                        :href="route(item.r)"
                        class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors"
                        :class="activePrefix(item.m) ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'"
                        @click="closeSidebar"
                    >
                        {{ item.label }}
                    </Link>

                    <p class="px-3 pt-5 pb-2 text-[9px] uppercase tracking-[0.25em] text-neutral-content/30">Commerce</p>
                    <Link
                        v-for="item in [
                            { label: 'Products', r: 'admin.products.index', m: '/admin/products' },
                            { label: 'Categories', r: 'admin.categories.index', m: '/admin/categories' },
                            { label: 'Inventory', r: 'admin.inventory.index', m: '/admin/inventory' },
                            { label: 'Orders', r: 'admin.orders.index', m: '/admin/orders' },
                            { label: 'Discounts', r: 'admin.discounts.index', m: '/admin/discounts' },
                            { label: 'Vouchers', r: 'admin.vouchers.index', m: '/admin/vouchers' },
                        ]"
                        :key="item.r"
                        :href="route(item.r)"
                        class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors"
                        :class="activePrefix(item.m) ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'"
                        @click="closeSidebar"
                    >
                        {{ item.label }}
                    </Link>

                    <p class="px-3 pt-5 pb-2 text-[9px] uppercase tracking-[0.25em] text-neutral-content/30">System</p>
                    <template v-if="canManageUsers">
                        <Link :href="route('admin.users.index')" class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors" :class="activePrefix('/admin/users') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'" @click="closeSidebar"> Users </Link>
                        <Link :href="route('admin.roles.index')" class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors" :class="activePrefix('/admin/roles') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'" @click="closeSidebar"> Roles </Link>
                    </template>
                    <Link :href="route('admin.media.index')" class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors" :class="activePrefix('/admin/media') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'" @click="closeSidebar"> Media Library </Link>
                    <Link :href="route('admin.settings.index')" class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors" :class="activePrefix('/admin/settings') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'" @click="closeSidebar"> Settings </Link>
                    <Link :href="route('admin.integrations.index')" class="flex items-center gap-3 px-3 py-2.5 text-[11px] uppercase tracking-[0.18em] transition-colors" :class="activePrefix('/admin/integrations') ? 'bg-primary/20 text-primary' : 'text-neutral-content/60 hover:text-neutral-content hover:bg-white/5'" @click="closeSidebar"> Integrations </Link>
                </nav>

                <div class="px-6 py-4 border-t border-white/10">
                    <a :href="route('home')" target="_blank" class="text-[10px] uppercase tracking-widest text-neutral-content/40 hover:text-primary transition-colors">← View Live Site</a>
                </div>
            </div>
        </aside>

        <div class="flex-1 flex flex-col min-h-screen bg-base-200 min-w-0">
            <div class="flex items-center justify-between h-14 px-5 border-b border-base-300 bg-base-100">
                <button type="button" class="lg:hidden p-2 -ml-2 text-base-content/60 hover:text-base-content cursor-pointer" @click="mobileSidebarOpen = true">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <span class="text-[11px] uppercase tracking-[0.2em] text-base-content/50 hidden lg:block">Sense of Jewels · Admin</span>
                <div class="flex items-center gap-4">
                    <a :href="route('home')" target="_blank" class="text-[10px] uppercase tracking-widest text-base-content/50 hover:text-base-content transition-colors hidden sm:block">View Site ↗</a>
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="flex items-center gap-2 cursor-pointer group">
                            <div class="w-7 h-7 rounded-full bg-primary/20 flex items-center justify-center text-primary text-xs font-semibold">
                                {{ String(user?.name ?? 'A').slice(0, 1).toUpperCase() }}
                            </div>
                            <span class="text-[11px] uppercase tracking-widest text-base-content/70 group-hover:text-base-content transition-colors hidden sm:block">{{ user?.name ?? 'Admin' }}</span>
                        </label>
                        <ul tabindex="0" class="dropdown-content z-50 mt-3 w-44 bg-base-100 border border-base-300 shadow-lg py-1">
                            <li>
                                <Link :href="route('profile.edit')" class="block px-4 py-2.5 text-[11px] uppercase tracking-widest text-base-content/70 hover:text-base-content hover:bg-base-200 transition-colors">Profile</Link>
                            </li>
                            <li>
                                <form method="POST" :action="route('logout')">
                                    <input type="hidden" name="_token" :value="page.props.csrf" />
                                    <button type="submit" class="w-full text-left px-4 py-2.5 text-[11px] uppercase tracking-widest text-base-content/70 hover:text-base-content hover:bg-base-200 transition-colors">Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <main class="flex-1 p-5 lg:p-8">
                <div v-if="flash.success || flash.status" class="mb-6 flex items-center gap-3 bg-success/10 border border-success/30 text-success px-4 py-3 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                    {{ flash.success ?? flash.status }}
                </div>
                <div v-if="flash.error" class="mb-6 flex items-center gap-3 bg-error/10 border border-error/30 text-error px-4 py-3 text-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                    {{ flash.error }}
                </div>

                <slot />
            </main>
        </div>
    </div>
</template>
