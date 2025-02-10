import { createApp, h} from "vue";
import type { DefineComponent } from "vue";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createInertiaApp } from "@inertiajs/vue3";
import { Quasar, Notify, Dialog, Loading } from "quasar";
import "quasar/src/css/index.sass";
import "@quasar/extras/material-icons/material-icons.css";
import "@quasar/extras/fontawesome-v6/fontawesome-v6.css";
import Vue3Toastify from "vue3-toastify";
import "vue3-toastify/dist/index.css";
import { ZiggyVue } from "ziggy-js";

createInertiaApp({
    resolve: (name) =>
		resolvePageComponent(
			`./Pages/${name}.vue`,
			import.meta.glob<DefineComponent>("./Pages/**/*.vue")
		),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });

        app.use(plugin);
        app.use(ZiggyVue, (window as any).Ziggy);

        app.use(Quasar, {
			plugins: {
				Notify,
				Dialog,
				Loading
			}
			// lang: quasarLang
		});
        
        app.use(Vue3Toastify, {
            autoClose: 3000,
            position: "top-right",
        });

        app.mount(el);
    },
});
