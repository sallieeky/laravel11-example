import 'element-plus/dist/index.css';
import './bootstrap';
import './Core/Config/dxDefaultOptions';
import '../css/dx.material.pkt-scheme.css';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createPinia } from "pinia";
import { dd } from './Core/Helpers/dump';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
window.dd = dd;

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    progress: {
        delay: 250,
        color: '#0a4f86',
        showSpinner: false,
    },
    setup({ el, App, props, plugin }) {
        delete el.dataset.page;
        document.body.classList.remove('page-loading');
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(createPinia())
            .mount(el);
    },
});
