import "./bootstrap";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/inertia-vue3";
import { createPinia } from "pinia";

createInertiaApp({
    // Função para resolver os componentes das páginas
    resolve: (name) => import(`./Pages/${name}.vue`),
    setup({ el, app, props, plugin }) {
        const pinia = createPinia();
        createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(pinia)
            .mount(el);
    },
});
