import './bootstrap';
import '../css/app.css';
import 'floating-vue/dist/style.css'

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import FloatingVue from 'floating-vue'
import { usePage } from '@inertiajs/vue3';
// import { formatDate ,number_format ,monthName } from './global.ts'

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

// window.formatDate = formatDate;
// window.number_format = number_format;
// window.monthName = monthName;


createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(FloatingVue)
            .mixin({
                methods: {
                    can: function (permissions) {
                        var allPermissions = this.$page.props.auth.can;
                        var hasPermission = false;
                        permissions.forEach(function (item) {
                            if (allPermissions[item]) hasPermission = true;
                        });
                        return hasPermission;
                    },
                    lang: function () {
                        return usePage().props.language.original;
                    }
                },
            })
            .mount(el);
    },
    progress: {
        showSpinner: true,
        color: '#990f02',
        // color: '#0284c7',
        scale: 3,
    },
});
