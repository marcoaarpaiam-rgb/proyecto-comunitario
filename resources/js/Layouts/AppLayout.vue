<template>
    <div
        class="d-flex vh-100 overflow-hidden"
        :class="{ 'sidebar-collapsed': collapsed }"
        :data-bs-theme="modoOscuro ? 'dark' : 'light'"
    >
        <!-- SIDEBAR -->
        <div
            class="sidebar d-flex flex-column"
            :class="{
                collapsed: collapsed && !esMobil,
                'mobile-open': sidebarMobileOpen && esMobil,
            }"
        >
            <!-- Logo -->
            <div
                class="sidebar-brand d-flex align-items-center px-3 py-3 border-bottom border-white border-opacity-25"
            >
                <img
                    :src="'/images/images.png'"
                    alt="Logo"
                    class="sidebar-logo me-2"
                />
                <div class="sidebar-brand-text">
                    <div
                        class="fw-bold text-white lh-1"
                        style="font-size: 0.9rem"
                    >
                        UPTP
                    </div>
                    <div class="text-white-50" style="font-size: 0.7rem">
                        Proyectos
                    </div>
                </div>
            </div>

            <!-- Usuario -->
            <div
                class="sidebar-user px-3 py-3 border-bottom border-white border-opacity-25"
            >
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar-circle">
                        {{ iniciales }}
                    </div>
                    <div class="sidebar-brand-text overflow-hidden">
                        <div
                            class="text-white fw-semibold text-truncate"
                            style="font-size: 0.85rem"
                        >
                            {{ $page.props.auth.user.usu_primer_nombre }}
                            {{ $page.props.auth.user.usu_primer_apellido }}
                        </div>
                        <div class="text-white-50" style="font-size: 0.72rem">
                            {{ rolLabel }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navegación -->
            <nav class="sidebar-nav flex-grow-1 overflow-auto py-2">
                <template v-for="item in menuItems" :key="item.label">
                    <!-- Item con subitems -->
                    <div v-if="item.children">
                        <button
                            class="sidebar-item w-100 text-start border-0"
                            :class="{ active: openMenu === item.label }"
                            @click="toggleMenu(item.label)"
                        >
                            <span v-if="item.svg" class="sidebar-icon d-inline-flex align-items-center" v-html="item.svg"></span>
                            <i v-else :class="item.icon + ' sidebar-icon'"></i>
                            <span class="sidebar-label">{{ item.label }}</span>
                            <i
                                class="bi ms-auto sidebar-label"
                                :class="
                                    openMenu === item.label
                                        ? 'bi-chevron-up'
                                        : 'bi-chevron-down'
                                "
                                style="font-size: 0.75rem"
                            ></i>
                        </button>
                        <div
                            v-show="openMenu === item.label"
                            class="sidebar-submenu"
                        >
                            <a
                                v-for="child in item.children"
                                :key="child.label"
                                :href="child.href"
                                class="sidebar-subitem d-flex align-items-center gap-2"
                            >
                                <span v-if="child.svg" class="d-inline-flex align-items-center" style="width: 16px; height: 16px" v-html="child.svg"></span>
                                <i
                                    v-else
                                    :class="child.icon"
                                    style="font-size: 0.8rem; width: 16px"
                                ></i>
                                <span class="sidebar-label">{{
                                    child.label
                                }}</span>
                            </a>
                        </div>
                    </div>
                    <!-- Item simple -->
                    <a
                        v-else
                        :href="item.href"
                        class="sidebar-item d-flex align-items-center"
                        :class="{ active: isActive(item.href) }"
                    >
                        <span v-if="item.svg" class="sidebar-icon d-inline-flex align-items-center" v-html="item.svg"></span>
                        <i v-else :class="item.icon + ' sidebar-icon'"></i>
                        <span class="sidebar-label">{{ item.label }}</span>
                    </a>
                </template>
            </nav>

            <!-- Cerrar sesión -->
            <div
                class="sidebar-footer border-top border-white border-opacity-25 p-3"
            >
                <form @submit.prevent="logout">
                    <button
                        type="submit"
                        class="sidebar-item w-100 text-start border-0 text-danger-emphasis"
                    >
                        <i
                            class="bi bi-box-arrow-left sidebar-icon text-danger"
                        ></i>
                        <span class="sidebar-label text-danger"
                            >Cerrar Sesión</span
                        >
                    </button>
                </form>
            </div>
        </div>

        <!-- CONTENIDO PRINCIPAL -->
        <div
            class="main-content d-flex flex-column flex-grow-1 overflow-hidden"
        >
            <!-- TOPBAR -->
            <div
                class="topbar d-flex align-items-center px-4 border-bottom bg-white shadow-sm"
            >
                <!-- Botón colapsar sidebar -->
                <button
                    class="btn btn-link text-dark p-0 me-3"
                    @click="toggleSidebar"
                >
                    <i
                        class="bi fs-4"
                        :class="
                            collapsed
                                ? 'bi-layout-sidebar-inset'
                                : 'bi-layout-sidebar'
                        "
                    ></i>
                </button>

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="me-auto">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item text-muted">
                            {{ rolLabel }}
                        </li>
                        <li class="breadcrumb-item active fw-semibold">
                            {{ pageTitle }}
                        </li>
                    </ol>
                </nav>

                <button
                    class="btn btn-link text-dark p-1"
                    @click="toggleModo"
                    title="Modo oscuro"
                >
                    <i
                        class="bi fs-5"
                        :class="
                            modoOscuro
                                ? 'bi-sun-fill text-warning'
                                : 'bi-moon-stars'
                        "
                    ></i>
                </button>

                <!-- Panel de notificaciones -->
                <div class="position-relative" ref="notiRef">
                    <button
                        class="btn btn-link text-dark position-relative p-1"
                        @click="toggleNotificaciones"
                        title="Notificaciones"
                    >
                        <i class="bi bi-bell fs-5"></i>
                        <span
                            v-if="sinLeer > 0"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.6rem"
                        >
                            {{ sinLeer }}
                        </span>
                    </button>

                    <div
                        v-if="verNotificaciones"
                        class="position-absolute end-0 mt-2 shadow-lg rounded-3 bg-white border"
                        style="width: 340px; z-index: 1050; top: 100%"
                    >
                        <div
                            class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center"
                        >
                            <span class="fw-bold small">
                                Notificaciones
                                <span
                                    v-if="sinLeer > 0"
                                    class="badge bg-danger ms-1"
                                    >{{ sinLeer }}</span
                                >
                            </span>
                            <div class="d-flex gap-2">
                                <button
                                    v-if="sinLeer > 0"
                                    class="btn btn-sm btn-link text-muted p-0 small"
                                    @click="leerTodas"
                                >
                                    Marcar todas leídas
                                </button>
                                <button
                                    class="btn btn-sm btn-link text-muted p-0"
                                    @click="verNotificaciones = false"
                                >
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        <div
                            v-if="listaNotificaciones.length === 0"
                            class="text-center text-muted py-4 small"
                        >
                            <i class="bi bi-bell-slash fs-2 d-block mb-2"></i>
                            No hay notificaciones
                        </div>

                        <ul
                            v-else
                            class="list-group list-group-flush"
                            style="max-height: 350px; overflow-y: auto"
                        >
                            <li
                                v-for="not in listaNotificaciones"
                                :key="not.not_id"
                                class="list-group-item px-3 py-2 cursor-pointer"
                                :class="!not.not_leida ? 'bg-light' : ''"
                                @click="marcarLeida(not)"
                            >
                                <div class="d-flex gap-2 align-items-start">
                                    <div class="mt-1">
                                        <span
                                            v-if="!not.not_leida"
                                            class="d-inline-block rounded-circle bg-danger"
                                            style="width: 8px; height: 8px"
                                        ></span>
                                        <span
                                            v-else
                                            class="d-inline-block rounded-circle bg-transparent"
                                            style="width: 8px; height: 8px"
                                        ></span>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div
                                            class="small"
                                            :class="
                                                !not.not_leida
                                                    ? 'fw-semibold'
                                                    : ''
                                            "
                                        >
                                            {{ not.not_mensaje }}
                                        </div>
                                        <div
                                            class="text-muted"
                                            style="font-size: 0.72rem"
                                        >
                                            {{
                                                formatFecha(
                                                    not.not_fecha_generacion,
                                                )
                                            }}
                                        </div>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- Overlay móvil -->
            <div
                class="sidebar-overlay"
                :class="{ visible: sidebarMobileOpen }"
                @click="sidebarMobileOpen = false"
            ></div>
            <!-- CONTENIDO DE LA PÁGINA -->
            <div class="page-content flex-grow-1 overflow-auto p-4">
                <slot />
            </div>
        </div>
        <Toast ref="toastRef" />
    </div>
</template>

<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { ref, computed, watch, onMounted, onUnmounted } from "vue";
import Toast from "@/Components/Toast.vue";

const verNotificaciones = ref(false);
const listaNotificaciones = ref([]);
const notiRef = ref(null);

const sinLeer = computed(
    () => listaNotificaciones.value.filter((n) => !n.not_leida).length,
);

const toggleNotificaciones = () => {
    verNotificaciones.value = !verNotificaciones.value;
    if (verNotificaciones.value) cargarNotificaciones();
};

const cargarNotificaciones = () => {
    if (!page.props.auth?.user) return;
    // Primero genera notificaciones automáticas (escritura en POST),
    // luego carga la lista actualizada (solo lectura en GET)
    const csrf = document.querySelector("meta[name=csrf-token]")?.content;
    fetch("/notificaciones/generar", {
        method: "POST",
        headers: { "X-CSRF-TOKEN": csrf, "Content-Type": "application/json" },
    })
        .catch(() => {})
        .finally(() => {
            fetch("/notificaciones/lista")
                .then((r) => r.json())
                .then((data) => {
                    listaNotificaciones.value = data;
                })
                .catch(() => {});
        });
};

const marcarLeida = (not) => {
    if (not.not_leida) return;
    fetch(`/notificaciones/${not.not_id}/leer`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]")
                ?.content,
        },
    }).then(() => {
        not.not_leida = true;
        not.not_fecha_lectura = new Date().toISOString();
    });
};

const leerTodas = () => {
    fetch("/notificaciones/leer-todas", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector("meta[name=csrf-token]")
                ?.content,
        },
    }).then(() => {
        listaNotificaciones.value.forEach((n) => (n.not_leida = true));
    });
};

const formatFecha = (fecha) => {
    const d = new Date(fecha);
    const ahora = new Date();
    const diff = Math.floor((ahora - d) / 1000);
    if (diff < 60) return "Hace un momento";
    if (diff < 3600) return `Hace ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `Hace ${Math.floor(diff / 3600)} h`;
    return d.toLocaleDateString("es-VE");
};

const cerrarPanel = (e) => {
    if (notiRef.value && !notiRef.value.contains(e.target)) {
        verNotificaciones.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", cerrarPanel);
    cargarNotificaciones();
});

onUnmounted(() => {
    document.removeEventListener("click", cerrarPanel);
});

const props = defineProps({
    pageTitle: { type: String, default: "Dashboard" },
    notificaciones: { type: Number, default: 0 },
});

const page = usePage();
const collapsed = ref(false);
const openMenu = ref(null);

const toastRef = ref(null);

// Escuchar flash messages de Inertia y convertirlos en toasts
watch(
    () => page.props.flash,
    (flash) => {
        if (flash?.success) toastRef.value?.agregar(flash.success, "success");
        if (flash?.error) toastRef.value?.agregar(flash.error, "error");
    },
    { deep: true },
);

const user = computed(() => page.props.auth.user);

const iniciales = computed(() => {
    const n = user.value?.usu_primer_nombre?.[0] || "";
    const a = user.value?.usu_primer_apellido?.[0] || "";
    return (n + a).toUpperCase();
});

const rolLabel = computed(() => {
    const rol = page.props.auth.rol;
    if (rol === "coordinador") return "Coordinador";
    if (rol === "profesor_proyecto") return "Profesor de Proyecto";
    if (rol === "lider") return "Líder de Equipo";
    return "Usuario";
});

const menuItems = computed(() => {
    const rol = page.props.auth.rol;
    if (rol === "coordinador") return menuCoordinador;
    if (rol === "profesor_proyecto") return menuProfesor;
    if (rol === "lider") return menuLider;
    return [];
});

const svgs = {
    dashboard: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>',
    table: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18"/><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M3 15h18"/></svg>',
    tag: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12.586 2.586A2 2 0 0 0 11.172 2H4a2 2 0 0 0-2 2v7.172a2 2 0 0 0 .586 1.414l8.704 8.704a2.426 2.426 0 0 0 3.42 0l6.58-6.58a2.426 2.426 0 0 0 0-3.42z"/><circle cx="7.5" cy="7.5" r=".5" fill="currentColor"/></svg>',
    layers: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>',
    personBadge: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M17 11h5"/><path d="M19 9v4"/></svg>',
    people: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    file: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/></svg>',
    calendar: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>',
    clock: '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
    award: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"/></svg>',
    secciones: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>',
    building: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="16" height="20" x="4" y="2" rx="2" ry="2"/><path d="M9 22v-4h6v4"/><path d="M8 6h.01"/><path d="M16 6h.01"/><path d="M12 6h.01"/><path d="M12 10h.01"/><path d="M12 14h.01"/><path d="M16 10h.01"/><path d="M16 14h.01"/><path d="M8 10h.01"/><path d="M8 14h.01"/></svg>',
    proyectos: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/></svg>',
    puntosControl: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" x2="4" y1="22" y2="15"/></svg>',
    socializaciones: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h20"/><path d="M21 3v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V3"/><path d="m7 21 5-5 5 5"/></svg>',
    entregables: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="m9 15 2 2 4-4"/></svg>',
    reportes: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><line x1="12" x2="12" y1="20" y2="10"/><line x1="18" x2="18" y1="20" y2="4"/><line x1="6" x2="6" y1="20" y2="16"/></svg>',
    cartas: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>',
    expediente: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m16 11 2 2 4-4"/></svg>',
    bitacora: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
    configuracion: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>',
    evaluaciones: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="6"/><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"/></svg>',
    autoevaluacion: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>',
    upload: '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"/><path d="M12 12v9"/><path d="m16 16-4-4-4 4"/></svg>'
};

const menuCoordinador = [
    { label: "Dashboard", svg: svgs.dashboard, icon: "bi bi-grid-fill", href: "/coordinador" },
    {
        label: "Tablas Maestras",
        svg: svgs.table,
        icon: "bi bi-table",
        children: [
            {
                label: "Tipos de Proyecto",
                svg: svgs.tag,
                icon: "bi bi-tag",
                href: "/coordinador/tipos-proyecto",
            },
            {
                label: "Modalidades",
                svg: svgs.layers,
                icon: "bi bi-layers",
                href: "/coordinador/maestras/modalidades",
            },
            {
                label: "Tipos de Jurado",
                svg: svgs.personBadge,
                icon: "bi bi-person-badge",
                href: "/coordinador/maestras/tipos-jurado",
            },
            {
                label: "Tipos de Beneficiario",
                svg: svgs.people,
                icon: "bi bi-people",
                href: "/coordinador/maestras/tipos-beneficiario",
            },
            {
                label: "Tipos de Entregable",
                svg: svgs.file,
                icon: "bi bi-file-earmark",
                href: "/coordinador/maestras/tipos-entregable",
            },
            {
                label: "Tipos Evento Equipo",
                svg: svgs.calendar,
                icon: "bi bi-calendar-event",
                href: "/coordinador/maestras/tipos-evento-equipo",
            },
            {
                label: "Turnos",
                svg: svgs.clock,
                icon: "bi bi-clock",
                href: "/coordinador/maestras/turnos",
            },
            {
                label: "Estados de Proyecto",
                svg: svgs.award,
                icon: "bi bi-award",
                href: "/coordinador/maestras/estados-proyecto",
            },
        ],
    },
    {
        label: "Secciones",
        svg: svgs.secciones,
        icon: "bi bi-collection-fill",
        href: "/coordinador/secciones",
    },
    {
        label: "Comunidades",
        svg: svgs.building,
        icon: "bi bi-building",
        href: "/coordinador/comunidades",
    },
    {
        label: "Equipos",
        svg: svgs.people,
        icon: "bi bi-people-fill",
        href: "/coordinador/equipos",
    },
    {
        label: "Proyectos",
        svg: svgs.proyectos,
        icon: "bi bi-folder-fill",
        href: "/coordinador/proyectos",
    },
    {
        label: "Evaluación Cualitativa/Cuantitativa",
        svg: svgs.evaluaciones,
        icon: "bi bi-journal-check",
        href: "/evaluaciones",
    },
    {
        label: "Socializaciones",
        svg: svgs.socializaciones,
        icon: "bi bi-mic-fill",
        href: "/coordinador/socializaciones",
    },
    {
        label: "Resultados",
        svg: svgs.award,
        icon: "bi bi-award-fill",
        href: "/coordinador/resultados",
    },
    {
        label: "Reportes",
        svg: svgs.reportes,
        icon: "bi bi-bar-chart-fill",
        href: "/coordinador/reportes",
    },
    {
        label: "Cartas de Presentación",
        svg: svgs.cartas,
        icon: "bi bi-file-earmark-text-fill",
        href: "/coordinador/cartas",
    },
    {
        label: "Expediente Estudiantes",
        svg: svgs.expediente,
        icon: "bi bi-person-badge-fill",
        href: "/coordinador/estudiantes/buscar",
    },
    {
        label: "Bitácora",
        svg: svgs.bitacora,
        icon: "bi bi-journal-text",
        href: "/coordinador/bitacora",
    },
    {
        label: "Configuración",
        svg: svgs.configuracion,
        icon: "bi bi-gear-fill",
        href: "/coordinador/configuracion",
    },
];
const menuProfesor = [
    { label: "Dashboard", svg: svgs.dashboard, icon: "bi bi-grid-fill", href: "/profesor" },
    {
        label: "Mis Equipos",
        svg: svgs.people,
        icon: "bi bi-people-fill",
        href: "/profesor/equipos",
    },
    {
        label: "Puntos de Control",
        svg: svgs.puntosControl,
        icon: "bi bi-flag-fill",
        href: "/profesor/puntos-control",
    },
    {
        label: "Evaluación Trayecto (Rúbricas)",
        svg: svgs.evaluaciones,
        icon: "bi bi-journal-check",
        href: "/evaluaciones",
    },
    {
        label: "Socializaciones",
        svg: svgs.socializaciones,
        icon: "bi bi-mic-fill",
        href: "/profesor/socializaciones",
    },
    {
        label: "Entregables",
        svg: svgs.entregables,
        icon: "bi bi-file-earmark-check-fill",
        href: "/profesor/entregables",
    },
    {
        label: "Expediente Estudiantes",
        svg: svgs.expediente,
        icon: "bi bi-person-badge-fill",
        href: "/profesor/estudiantes/buscar",
    },
];
const menuLider = [
    { label: "Dashboard", svg: svgs.dashboard, icon: "bi bi-grid-fill", href: "/lider" },
    { label: "Mi Equipo", svg: svgs.people, icon: "bi bi-people-fill", href: "/lider/equipo" },
    {
        label: "Mi Proyecto",
        svg: svgs.proyectos,
        icon: "bi bi-folder-fill",
        href: "/lider/proyecto",
    },
    {
        label: "Entregables",
        svg: svgs.upload,
        icon: "bi bi-cloud-upload-fill",
        href: "/lider/entregables",
    },
    {
        label: "Autoevaluación PST (10%)",
        svg: svgs.autoevaluacion,
        icon: "bi bi-check-circle",
        href: "/evaluaciones",
    },
    {
        label: "Carta de Presentación",
        svg: svgs.cartas,
        icon: "bi bi-file-earmark-text-fill",
        href: "/lider/carta",
    },
];

const toggleMenu = (label) => {
    openMenu.value = openMenu.value === label ? null : label;
};

const isActive = (href) => window.location.pathname === href;

const logout = () => {
    router.post("/logout");
};

const modoOscuro = ref(localStorage.getItem("modo") === "dark");

const toggleModo = () => {
    modoOscuro.value = !modoOscuro.value;
    localStorage.setItem("modo", modoOscuro.value ? "dark" : "light");
    aplicarModo();
};

const aplicarModo = () => {
    document.documentElement.setAttribute(
        "data-bs-theme",
        modoOscuro.value ? "dark" : "light",
    );
};

onMounted(() => {
    aplicarModo();
});

const sidebarMobileOpen = ref(false);
const esMobil = ref(window.innerWidth <= 768);

const toggleSidebar = () => {
    if (esMobil.value) {
        sidebarMobileOpen.value = !sidebarMobileOpen.value;
    } else {
        collapsed.value = !collapsed.value;
    }
};

onMounted(() => {
    window.addEventListener("resize", () => {
        esMobil.value = window.innerWidth <= 768;
    });
});
</script>

<style>
[data-bs-theme="dark"] .page-content {
  background: #1a1d23 !important;
}

[data-bs-theme="dark"] .topbar {
  background: #212529 !important;
  border-color: #373b3e !important;
}

[data-bs-theme="dark"] .topbar .btn-link {
  color: #adb5bd !important;
}

[data-bs-theme="dark"] .card {
  background: #2b2f33;
  border-color: #373b3e;
}

[data-bs-theme="dark"] .table-light th {
  background: #343a40;
  color: #dee2e6;
}

[data-bs-theme="dark"] .form-control,
[data-bs-theme="dark"] .form-select {
  background: #343a40;
  color: #dee2e6;
  border-color: #495057;
}

[data-bs-theme="dark"] .modal-content {
  background: #2b2f33;
}

[data-bs-theme="dark"] .list-group-item {
  background: #2b2f33;
  border-color: #373b3e;
  color: #dee2e6;
}

[data-bs-theme="dark"] .accordion-button {
  background: #343a40;
  color: #dee2e6;
}

[data-bs-theme="dark"] .bg-light {
  background: #343a40 !important;
}
/* ── Variables ── */
:root {
    --sidebar-width: 240px;
    --sidebar-collapsed-width: 64px;
    --sidebar-bg: #1f3864;
    --sidebar-hover: rgba(255, 255, 255, 0.1);
    --sidebar-active: rgba(220, 53, 69, 0.85);
    --topbar-height: 56px;
}

/* ── Sidebar ── */
.sidebar {
    width: var(--sidebar-width);
    min-width: var(--sidebar-width);
    background: var(--sidebar-bg);
    transition:
        width 0.25s ease,
        min-width 0.25s ease;
    overflow: hidden;
}
.sidebar.collapsed {
    width: var(--sidebar-collapsed-width);
    min-width: var(--sidebar-collapsed-width);
}

/* Logo */
.sidebar-logo {
    height: 36px;
    width: 36px;
    object-fit: contain;
    flex-shrink: 0;
    border-radius: 6px;
    background: white;
    padding: 2px;
}

/* Avatar */
.avatar-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #dc3545;
    color: white;
    font-weight: 700;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.avatar-circle-sm {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #1f3864;
    color: white;
    font-weight: 700;
    font-size: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Nav items */
.sidebar-item {
    display: flex;
    align-items: center;
    padding: 10px 14px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    background: transparent;
    cursor: pointer;
    border-radius: 8px;
    margin: 2px 8px;
    font-size: 0.875rem;
    transition: background 0.15s;
    white-space: nowrap;
}
.sidebar-item:hover {
    background: rgba(255,255,255,0.1));
    color: white;
}
.sidebar-item.active {
    background: rgba(220,53,69,0.85);
    color: white;
    font-weight: 600;
}
.sidebar-icon {
    font-size: 1rem;
    width: 20px;
    flex-shrink: 0;
    margin-right: 10px;
}

/* Submenú */
.sidebar-submenu {
    padding-left: 8px;
}
.sidebar-subitem {
    padding: 7px 14px 7px 34px;
    color: rgba(255, 255, 255, 0.65);
    text-decoration: none;
    font-size: 0.82rem;
    border-radius: 8px;
    margin: 1px 8px;
    display: flex;
    align-items: center;
    white-space: nowrap;
    transition: background 0.15s;
}
.sidebar-subitem:hover {
    background: rgba(255,255,255,0.1);
    color: white;
}

/* Ocultar labels cuando está colapsado */
.collapsed .sidebar-label,
.collapsed .sidebar-brand-text {
    display: none;
}
.collapsed .sidebar-icon {
    margin-right: 0;
}
.collapsed .sidebar-item {
    justify-content: center;
    padding: 10px;
}

/* ── Topbar ── */
.topbar {
    height: 56px;
    min-height: 56px;
}

/* ── Page content ── */
.page-content {
    background: #f4f6f9;
}
/* ── MÓVIL ── */
@media (max-width: 768px) {

  /* Sidebar oculto por defecto en móvil */
  .sidebar {
    position: fixed;
    top: 0; left: 0; bottom: 0;
    z-index: 1040;
    transform: translateX(-100%);
    transition: transform 0.25s ease, width 0.25s ease;
    width: var(--sidebar-width) !important;
    min-width: var(--sidebar-width) !important;
  }

  .sidebar.mobile-open {
    transform: translateX(0);
  }

  /* Overlay cuando el sidebar está abierto en móvil */
  .sidebar-overlay {
    display: none;
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(0,0,0,0.5);
    z-index: 1039;
  }

  .sidebar-overlay.visible {
    display: block;
  }

  /* El contenido ocupa todo el ancho */
  .main-content {
    width: 100% !important;
  }

  /* Labels del sidebar siempre visibles en móvil */
  .sidebar.mobile-open .sidebar-label,
  .sidebar.mobile-open .sidebar-brand-text {
    display: inline !important;
  }

  .sidebar.mobile-open .sidebar-icon {
    margin-right: 10px !important;
  }

  .sidebar.mobile-open .sidebar-item {
    justify-content: flex-start !important;
    padding: 10px 14px !important;
  }

  /* Topbar en móvil */
  .topbar {
    padding-left: 1rem !important;
    padding-right: 1rem !important;
  }

  /* Tablas en móvil con scroll horizontal */
  .table-responsive-mobile {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  /* Cards en móvil */
  .card {
    border-radius: 8px !important;
  }

  /* Page content padding reducido en móvil */
  .page-content {
    padding: 1rem !important;
  }

  /* Modales en móvil ocupan más espacio */
  .modal-dialog {
    margin: 0.5rem !important;
    max-width: calc(100vw - 1rem) !important;
  }

  /* Ocultar columnas menos importantes en móvil */
  .d-mobile-none {
    display: none !important;
  }
}

@media (min-width: 769px) {
  .sidebar-overlay { display: none !important; }
}
</style>
