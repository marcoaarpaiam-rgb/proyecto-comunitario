import "./bootstrap";
import "bootstrap/dist/css/bootstrap.min.css";
import "bootstrap-icons/font/bootstrap-icons.css";
import "nprogress/nprogress.css";
import "../css/app.css";
import "bootstrap";

import NProgress from "nprogress";
import { createApp, h } from "vue";
import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { router } from "@inertiajs/vue3";

NProgress.configure({ showSpinner: false, trickleSpeed: 200 });

// Sincronización automática de tema (claro / oscuro) en html y body para modales y componentes globales
const syncGlobalTheme = () => {
    try {
        let themeKey = "coord-theme";
        if (window.location.pathname.startsWith("/coordinador")) {
            themeKey = "coord-theme";
        } else if (window.location.pathname.startsWith("/profesor")) {
            themeKey = "prof-theme";
        } else if (window.location.pathname.startsWith("/lider")) {
            themeKey = "lid-theme";
        } else if (window.location.pathname === "/login") {
            themeKey = "login-theme";
        } else if (window.location.pathname === "/home" || window.location.pathname === "/") {
            themeKey = "home-theme";
        } else {
            themeKey = "theme";
        }
        const currentTheme = localStorage.getItem(themeKey) || (themeKey === "login-theme" || themeKey === "home-theme" ? "light" : (localStorage.getItem("modo") || localStorage.getItem("theme") || "light"));
        document.documentElement.setAttribute("data-theme", currentTheme);
        document.documentElement.setAttribute("data-bs-theme", currentTheme);
        if (document.body) {
            document.body.setAttribute("data-theme", currentTheme);
            document.body.setAttribute("data-bs-theme", currentTheme);
        }
    } catch (e) {}
};
syncGlobalTheme();

const originalSetItem = localStorage.setItem.bind(localStorage);
localStorage.setItem = function(key, val) {
    originalSetItem(key, val);
    if (typeof key === "string" && (key.includes("theme") || key === "modo")) {
        document.documentElement.setAttribute("data-theme", val);
        document.documentElement.setAttribute("data-bs-theme", val);
        if (document.body) {
            document.body.setAttribute("data-theme", val);
            document.body.setAttribute("data-bs-theme", val);
        }
    }
};

window.addEventListener("storage", syncGlobalTheme);
router.on("navigate", syncGlobalTheme);

router.on("start", (event) => {
    if (window.location.pathname === "/login" || event?.detail?.visit?.url?.pathname === "/login") {
        return;
    }
    NProgress.start();
});
router.on("finish", () => NProgress.done());

createInertiaApp({
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        ),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el);
    },
});
