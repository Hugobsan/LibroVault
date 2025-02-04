import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { Quasar } from 'quasar';
import quasarLang from 'quasar/lang/pt-BR';
import 'vue3-toastify/dist/index.css';
import Vue3Toastify from 'vue3-toastify';

createInertiaApp({
    resolve: name => require(`./Pages/${name}.vue`),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Quasar, {
                lang: quasarLang,
            })
            .use(Vue3Toastify, {
                autoClose: 3000,
                position: 'top-right',
            })
            .mount(el);
    },
});
