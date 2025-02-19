import './bootstrap';
import '../css/app.css';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';
import { MotionPlugin } from '@vueuse/motion'
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import axios from 'axios';
import HandleAuthChanges from './Middleware/HandleAuthChanges';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const pinia = createPinia();
pinia.use(piniaPluginPersistedstate);

// Set up Axios defaults
axios.defaults.withCredentials = true;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(MotionPlugin)
            .use(pinia);

        HandleAuthChanges();

        app.mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
