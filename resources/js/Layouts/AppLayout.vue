<template>
    <div
        class="d-flex vh-100 overflow-hidden"
        :class="{ 'sidebar-collapsed': collapsed }"
    >
        <!-- SIDEBAR -->
        <div class="sidebar d-flex flex-column">
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
                            <i :class="item.icon + ' sidebar-icon'"></i>
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
                                <i
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
                        <i :class="item.icon + ' sidebar-icon'"></i>
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
                    @click="collapsed = !collapsed"
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

                <!-- Notificaciones -->
                <div class="position-relative" ref="notiRef">
                    <button
                        class="btn btn-link text-dark position-relative p-1"
                        @click="verNotificaciones = !verNotificaciones"
                        title="Notificaciones"
                    >
                        <i class="bi bi-bell fs-5"></i>
                        <span
                            v-if="notificaciones > 0"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            style="font-size: 0.6rem"
                        >
                            {{ notificaciones }}
                        </span>
                    </button>

                    <!-- Panel de notificaciones -->
                    <div
                        v-if="verNotificaciones"
                        class="position-absolute end-0 mt-2 shadow-lg rounded-3 bg-white border"
                        style="width: 320px; z-index: 1050; top: 100%"
                    >
                        <div
                            class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center"
                        >
                            <span class="fw-bold small">Notificaciones</span>
                            <button
                                class="btn btn-sm btn-link text-muted p-0"
                                @click="verNotificaciones = false"
                            >
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                        <div
                            v-if="listaNotificaciones.length === 0"
                            class="text-center text-muted py-4 small"
                        >
                            No hay notificaciones
                        </div>
                        <ul
                            v-else
                            class="list-group list-group-flush"
                            style="max-height: 300px; overflow-y: auto"
                        >
                            <li
                                v-for="not in listaNotificaciones"
                                :key="not.not_id"
                                class="list-group-item px-3 py-2"
                                :class="{ 'bg-light': !not.not_leida }"
                            >
                                <div class="small fw-semibold">
                                    {{ not.not_mensaje }}
                                </div>
                                <div
                                    class="text-muted"
                                    style="font-size: 0.72rem"
                                >
                                    {{
                                        new Date(
                                            not.not_fecha_generacion,
                                        ).toLocaleString("es-VE")
                                    }}
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- CONTENIDO DE LA PÁGINA -->
            <div class="page-content flex-grow-1 overflow-auto p-4">
                <slot />
            </div>
        </div>
    </div>
</template>

<script setup>
import { router, usePage } from "@inertiajs/vue3";
import { ref, computed, onMounted, onUnmounted } from "vue";

const verNotificaciones = ref(false);
const notiRef = ref(null);
const listaNotificaciones = ref([]);

// Cerrar panel al hacer click fuera
const cerrarPanel = (e) => {
    if (notiRef.value && !notiRef.value.contains(e.target)) {
        verNotificaciones.value = false;
    }
};

onMounted(() => {
    document.addEventListener("click", cerrarPanel);
    // Cargar notificaciones si el usuario tiene acceso
    if (page.props.auth?.user) {
        fetch("/notificaciones/lista")
            .then((r) => r.json())
            .then((data) => {
                listaNotificaciones.value = data;
            })
            .catch(() => {});
    }
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

const menuCoordinador = [
    { label: "Dashboard", icon: "bi bi-grid-fill", href: "/coordinador" },
    {
        label: "Tablas Maestras",
        icon: "bi bi-table",
        children: [
            {
                label: "Tipos de Proyecto",
                icon: "bi bi-tag",
                href: "/coordinador/tipos-proyecto",
            },
            {
                label: "Modalidades",
                icon: "bi bi-layers",
                href: "/coordinador/maestras/modalidades",
            },
            {
                label: "Tipos de Jurado",
                icon: "bi bi-person-badge",
                href: "/coordinador/maestras/tipos-jurado",
            },
            {
                label: "Tipos de Beneficiario",
                icon: "bi bi-people",
                href: "/coordinador/maestras/tipos-beneficiario",
            },
            {
                label: "Tipos de Entregable",
                icon: "bi bi-file-earmark",
                href: "/coordinador/maestras/tipos-entregable",
            },
            {
                label: "Tipos de Evento Equipo",
                icon: "bi bi-calendar-event",
                href: "/coordinador/maestras/tipos-evento-equipo",
            },
            {
                label: "Turnos",
                icon: "bi bi-clock",
                href: "/coordinador/maestras/turnos",
            },
            {
                label: "Estados de Proyecto",
                icon: "bi bi-award",
                href: "/coordinador/maestras/estados-proyecto",
            },
        ],
    },
    {
        label: "Secciones",
        icon: "bi bi-collection-fill",
        href: "/coordinador/secciones",
    },
    {
        label: "Comunidades",
        icon: "bi bi-building",
        href: "/coordinador/comunidades",
    },
    {
        label: "Equipos",
        icon: "bi bi-people-fill",
        href: "/coordinador/equipos",
    },
    {
        label: "Proyectos",
        icon: "bi bi-folder-fill",
        href: "/coordinador/proyectos",
    },
    {
        label: "Socializaciones",
        icon: "bi bi-mic-fill",
        href: "/coordinador/socializaciones",
    },
    {
        label: "Resultados",
        icon: "bi bi-award-fill",
        href: "/coordinador/resultados",
    },
    {
        label: "Reportes",
        icon: "bi bi-bar-chart-fill",
        href: "/coordinador/reportes",
    },
    {
        label: "Cartas de Presentación",
        icon: "bi bi-file-earmark-text-fill",
        href: "/coordinador/cartas",
    },
    {
        label: "Bitácora",
        icon: "bi bi-journal-text",
        href: "/coordinador/bitacora",
    },
];
const menuProfesor = [
    { label: "Dashboard", icon: "bi bi-grid-fill", href: "/profesor" },
    {
        label: "Mis Equipos",
        icon: "bi bi-people-fill",
        href: "/profesor/equipos",
    },
    {
        label: "Puntos de Control",
        icon: "bi bi-flag-fill",
        href: "/profesor/puntos-control",
    },
    {
        label: "Socializaciones",
        icon: "bi bi-mic-fill",
        href: "/profesor/socializaciones",
    },
    {
        label: "Entregables",
        icon: "bi bi-file-earmark-check-fill",
        href: "/profesor/entregables",
    },
];
const menuLider = [
    { label: "Dashboard", icon: "bi bi-grid-fill", href: "/lider" },
    { label: "Mi Equipo", icon: "bi bi-people-fill", href: "/lider/equipo" },
    {
        label: "Mi Proyecto",
        icon: "bi bi-folder-fill",
        href: "/lider/proyecto",
    },
    {
        label: "Entregables",
        icon: "bi bi-cloud-upload-fill",
        href: "/lider/entregables",
    },
    {
        label: "Carta de Presentación",
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
</script>

<style>
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
</style>
