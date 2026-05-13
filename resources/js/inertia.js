import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from 'ziggy-js';
import { initScrollAnimationsAndMobileMenu } from './scroll-animations';

createInertiaApp({
    title: (title) =>
        title
            ? `${title} — ${import.meta.env.VITE_APP_NAME || 'Sense of Jewels'}`
            : import.meta.env.VITE_APP_NAME || 'Sense of Jewels',
    resolve: (name) =>
        resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#bfa054',
    },
});

router.on('finish', () => {
    initScrollAnimationsAndMobileMenu();
});
