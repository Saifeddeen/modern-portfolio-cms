import '../css/app.css';
import './bootstrap.ts';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, DefineComponent, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import PrimeVue from 'primevue/config';
import AuraTheme from '@primeuix/themes/aura';
import ToastService from 'primevue/toastservice'; // Add this
import ConfirmationService from 'primevue/confirmationservice'; // Add this

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./pages/${name}.vue`,
            import.meta.glob<DefineComponent>('./pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);
        app.use(ZiggyVue);

        app.use(PrimeVue, {
            theme: {
                preset: AuraTheme,
                options: {
                    darkModeSelector: '.app-dark',
                }
            }
        });

        // Register PrimeVue Services
        app.use(ToastService);
        app.use(ConfirmationService);

        app.mount(el);
    },
    progress: {
        color: '#6366f1', // Iris 500
    },
});