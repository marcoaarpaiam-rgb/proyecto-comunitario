<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
            font-family: &quot;Segoe UI&quot;, system-ui, sans-serif;
        "
    >
        <div
            class="sidebar-overlay"
            :class="{ show: sidebarOpen }"
            @click="sidebarOpen = false"
        ></div>

        <!-- SIDEBAR -->
        <div class="sidebar-coord" :class="{ show: sidebarOpen }">
            <div class="sb-brand">
                <div
                    style="
                        width: 36px;
                        height: 36px;
                        background: #dc3545;
                        border-radius: 8px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #fff;
                        font-weight: 800;
                    "
                >
                    💻
                </div>
                <div>
                    <div class="sb-brand-title">UPTP — Proyectos</div>
                    <div class="sb-brand-sub">PNF Informática</div>
                </div>
                <button
                    class="btn btn-sm text-white ms-auto fs-4 p-0 d-lg-none"
                    @click="sidebarOpen = false"
                >
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="sb-user">
                <div class="sb-avatar">{{ iniciales }}</div>
                <div>
                    <div class="sb-name">
                        {{ $page.props.auth.user.usu_primer_nombre }}
                        {{ $page.props.auth.user.usu_primer_apellido }}
                    </div>
                    <div class="sb-role">Coordinador PNF</div>
                </div>
            </div>
            <div class="sb-nav">
                <div class="sb-section">Principal</div>
                <a href="/coordinador" class="sb-link active"
                    ><i class="bi bi-speedometer2"></i> Dashboard</a
                >
                <a href="/coordinador/secciones" class="sb-link"
                    ><i class="bi bi-collection"></i> Secciones</a
                >
                <a href="/coordinador/comunidades" class="sb-link"
                    ><i class="bi bi-building"></i> Comunidades</a
                >
                <a href="/coordinador/equipos" class="sb-link"
                    ><i class="bi bi-people"></i> Equipos</a
                >
                <a href="/coordinador/proyectos" class="sb-link"
                    ><i class="bi bi-folder"></i> Proyectos</a
                >
                <div class="sb-section">Académico</div>
                <a href="/coordinador/socializaciones" class="sb-link"
                    ><i class="bi bi-mic"></i> Socializaciones</a
                >
                <a href="/coordinador/resultados" class="sb-link"
                    ><i class="bi bi-trophy"></i> Resultados</a
                >
                <div class="sb-section">Reportes</div>
                <a href="/coordinador/reportes" class="sb-link"
                    ><i class="bi bi-bar-chart"></i> Reportes</a
                >
                <a href="/coordinador/cartas" class="sb-link"
                    ><i class="bi bi-file-earmark-text"></i> Cartas</a
                >
                <div class="sb-section">Sistema</div>
                <a href="/coordinador/tipos-proyecto" class="sb-link"
                    ><i class="bi bi-sliders"></i> Tablas Maestras</a
                >
                <a href="/coordinador/bitacora" class="sb-link"
                    ><i class="bi bi-journal-text"></i> Bitácora</a
                >
                <a href="/coordinador/configuracion" class="sb-link"
                    ><i class="bi bi-gear"></i> Configuración</a
                >
                <div style="padding: 1rem 1.5rem">
                    <form @submit.prevent="logout">
                        <button
                            type="submit"
                            class="sb-link w-100 text-start border-0 p-0"
                            style="
                                color: #ff8591;
                                font-weight: 700;
                                background: none;
                            "
                        >
                            <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- MAIN -->
        <div class="main-coord">
            <div class="topbar-coord">
                <div class="d-flex align-items-center gap-3">
                    <button
                        class="btn btn-outline-danger btn-sm toggle-btn px-2"
                        @click="sidebarOpen = true"
                    >
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div class="topbar-title">
                        <i
                            class="bi bi-speedometer2 me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Dashboard
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span
                        class="badge rounded-pill px-3 py-2 d-none d-md-inline-block"
                        style="
                            background: #6f42c1;
                            color: #fff;
                            font-weight: 700;
                            cursor: pointer;
                        "
                    >
                        <i class="bi bi-arrow-repeat me-1"></i>Sincronizar SOGAC
                    </span>
                    <button class="topbar-btn" @click="toggleTema">
                        <i
                            class="bi"
                            :class="
                                tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'
                            "
                        ></i>
                    </button>
                    <button class="topbar-btn" style="position: relative">
                        <i class="bi bi-bell"></i>
                        <span
                            v-if="stats.notificaciones > 0"
                            class="notif-dot"
                        ></span>
                    </button>
                </div>
            </div>

            <div class="content-coord">
                <!-- STATS -->
                <div class="row g-3 mb-4">
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="stat-card">
                            <div class="stat-icon blue">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <div class="stat-num">
                                    {{ stats.estudiantes }}
                                </div>
                                <div class="stat-label">Estudiantes</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="stat-card">
                            <div class="stat-icon red">
                                <i class="bi bi-diagram-3-fill"></i>
                            </div>
                            <div>
                                <div class="stat-num">{{ stats.equipos }}</div>
                                <div class="stat-label">Equipos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="stat-card">
                            <div class="stat-icon green">
                                <i class="bi bi-building-check"></i>
                            </div>
                            <div>
                                <div class="stat-num">
                                    {{ stats.comunidades }}
                                </div>
                                <div class="stat-label">Comunidades</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="stat-card">
                            <div class="stat-icon orange">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <div class="stat-num">
                                    {{ stats.porVencer }}
                                </div>
                                <div class="stat-label">Por Vencer</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="stat-card">
                            <div class="stat-icon green">
                                <i class="bi bi-trophy-fill"></i>
                            </div>
                            <div>
                                <div class="stat-num">
                                    {{ stats.aprobados }}
                                </div>
                                <div class="stat-label">Aprobados</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-2 col-md-4 col-6">
                        <div class="stat-card">
                            <div class="stat-icon purple">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                            <div>
                                <div class="stat-num">
                                    {{ stats.profesores }}
                                </div>
                                <div class="stat-label">Profesores</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ACCIONES RÁPIDAS -->
                <div class="section-card mb-4">
                    <div class="section-header">
                        <h6>
                            <i
                                class="bi bi-lightning-charge me-2 text-danger"
                            ></i
                            >Acciones Rápidas
                        </h6>
                    </div>
                    <div class="p-3">
                        <div class="row g-2">
                            <div
                                class="col-6 col-sm-4 col-md-3 col-lg-2"
                                v-for="acc in acciones"
                                :key="acc.label"
                            >
                                <a :href="acc.href" class="quick-action">
                                    <i :class="acc.icon + ' text-danger'"></i
                                    >{{ acc.label }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- ESTADO POR SECCIÓN -->
                    <div class="col-lg-8">
                        <div class="section-card">
                            <div class="section-header">
                                <h6>
                                    <i class="bi bi-grid me-2 text-danger"></i
                                    >Estado por Sección
                                </h6>
                            </div>
                            <div class="table-responsive">
                                <table class="custom-table mb-0">
                                    <thead>
                                        <tr>
                                            <th>Sección</th>
                                            <th>Turno</th>
                                            <th>Equipos</th>
                                            <th>Avance</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="secciones.length === 0">
                                            <td
                                                colspan="5"
                                                class="text-center text-muted py-4"
                                            >
                                                Sin secciones registradas
                                            </td>
                                        </tr>
                                        <tr
                                            v-for="sec in secciones"
                                            :key="sec.sec_id"
                                        >
                                            <td>
                                                <strong>{{
                                                    sec.sec_codigo
                                                }}</strong>
                                            </td>
                                            <td>
                                                <span
                                                    :class="
                                                        'badge badge-' +
                                                        badgeTurno(
                                                            sec.turno
                                                                ?.tur_nombre,
                                                        )
                                                    "
                                                    >{{
                                                        sec.turno?.tur_nombre
                                                    }}</span
                                                >
                                            </td>
                                            <td class="fw-semibold">
                                                {{ sec.total_equipos }}
                                            </td>
                                            <td>
                                                <div
                                                    class="d-flex align-items-center gap-2"
                                                    style="min-width: 90px"
                                                >
                                                    <div
                                                        class="progress flex-grow-1"
                                                        style="height: 7px"
                                                    >
                                                        <div
                                                            class="progress-bar"
                                                            :class="
                                                                colorBarra(
                                                                    sec.avance,
                                                                )
                                                            "
                                                            :style="{
                                                                width:
                                                                    sec.avance +
                                                                    '%',
                                                            }"
                                                        ></div>
                                                    </div>
                                                    <span class="fw-bold small"
                                                        >{{ sec.avance }}%</span
                                                    >
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    class="badge-status"
                                                    :class="
                                                        badgeSeccion(sec.avance)
                                                    "
                                                    >{{
                                                        labelSeccion(sec.avance)
                                                    }}</span
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- ALERTAS -->
                    <div class="col-lg-4">
                        <div class="section-card">
                            <div class="section-header">
                                <h6>
                                    <i class="bi bi-bell me-2 text-danger"></i
                                    >Alertas
                                </h6>
                                <span
                                    v-if="notificaciones.length > 0"
                                    class="badge bg-danger rounded-pill px-2 py-1 fw-bold"
                                >
                                    {{
                                        notificaciones.filter(
                                            (n) => !n.not_leida,
                                        ).length
                                    }}
                                    nuevas
                                </span>
                            </div>
                            <div
                                v-if="notificaciones.length === 0"
                                class="p-3 text-center text-muted small py-4"
                            >
                                Sin alertas
                            </div>
                            <div
                                v-for="not in notificaciones.slice(0, 5)"
                                :key="not.not_id"
                                class="notif-item"
                            >
                                <div
                                    class="notif-icon"
                                    :class="tipoIcon(not.not_mensaje)"
                                >
                                    <i
                                        class="bi"
                                        :class="tipoIconClass(not.not_mensaje)"
                                    ></i>
                                </div>
                                <div>
                                    <div class="notif-text">
                                        {{ not.not_mensaje }}
                                    </div>
                                    <div class="notif-time">
                                        {{
                                            formatFecha(
                                                not.not_fecha_generacion,
                                            )
                                        }}
                                    </div>
                                </div>
                            </div>
                            <div
                                v-if="notificaciones.length > 0"
                                class="p-3 text-center"
                            >
                                <a
                                    href="/coordinador/bitacora"
                                    class="btn btn-sm btn-outline-danger w-100 rounded-pill fw-bold"
                                    >Ver bitácora</a
                                >
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";

const props = defineProps({
    stats: {
        type: Object,
        default: () => ({
            estudiantes: 0,
            equipos: 0,
            comunidades: 0,
            porVencer: 0,
            aprobados: 0,
            profesores: 0,
            notificaciones: 0,
        }),
    },
    secciones: { type: Array, default: () => [] },
});

const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("coord-theme") || "light");
const notificaciones = ref([]);

const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});

const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("coord-theme", tema.value);
};

const logout = () => router.post("/logout");

const acciones = [
    {
        label: "Secciones",
        icon: "bi bi-collection",
        href: "/coordinador/secciones",
    },
    { label: "Equipos", icon: "bi bi-people", href: "/coordinador/equipos" },
    {
        label: "Resultados",
        icon: "bi bi-trophy",
        href: "/coordinador/resultados",
    },
    {
        label: "Reportes",
        icon: "bi bi-bar-chart",
        href: "/coordinador/reportes",
    },
    {
        label: "Solvencia",
        icon: "bi bi-person-check",
        href: "/coordinador/reportes",
    },
    {
        label: "Cartas",
        icon: "bi bi-file-earmark-text",
        href: "/coordinador/cartas",
    },
];

const badgeTurno = (t) => {
    if (t === "Mañana") return "bg-warning text-dark";
    if (t === "Tarde") return "bg-info text-dark";
    if (t === "Noche") return "bg-dark text-white";
    return "bg-secondary text-white";
};

const colorBarra = (p) => {
    if (p >= 80) return "bg-success";
    if (p >= 50) return "bg-warning";
    if (p >= 20) return "bg-danger";
    return "bg-secondary";
};

const badgeSeccion = (p) => {
    if (p >= 100) return "badge-aprobado";
    if (p >= 50) return "badge-proceso";
    if (p >= 20) return "badge-pendiente";
    return "badge-critico";
};

const labelSeccion = (p) => {
    if (p >= 100) return "Completado";
    if (p >= 50) return "En Proceso";
    if (p >= 20) return "Pendiente";
    return "Crítico";
};

const tipoIcon = (msg) => {
    if (msg?.includes("vencido")) return "danger";
    if (msg?.includes("vence")) return "warn";
    return "info";
};

const tipoIconClass = (msg) => {
    if (msg?.includes("vencido")) return "bi-exclamation-triangle";
    if (msg?.includes("vence")) return "bi-clock";
    return "bi-info-circle";
};

const formatFecha = (f) => {
    const d = new Date(f),
        diff = Math.floor((new Date() - d) / 1000);
    if (diff < 60) return "Hace un momento";
    if (diff < 3600) return `Hace ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `Hace ${Math.floor(diff / 3600)} h`;
    return d.toLocaleDateString("es-VE");
};

onMounted(() => {
    fetch("/notificaciones/lista")
        .then((r) => r.json())
        .then((d) => {
            notificaciones.value = d;
        })
        .catch(() => {});
});
</script>
<style>
:root {
    --rojo: #dc3545;
    --rojo-dark: #b02a37;
    --bg-body: #f0f2f5;
    --bg-card: #ffffff;
    --bg-sidebar: #1a1d23;
    --bg-topbar: #ffffff;
    --border: #dee2e6;
    --text-main: #1a1a1a;
    --text-muted: #6c757d;
    --bg-th: #f8f9fa;
    --bg-hover: #fff8f8;
    --bg-quick: #f8f9fa;
    --border-side: rgba(220, 53, 69, 0.25);
    --notif-border: #e9ecef;
    --badge-bg: #f8f9fa;
    --badge-color: #212529;
}
[data-theme="dark"] {
    --bg-body: #111317;
    --bg-card: #1e2128;
    --bg-sidebar: #0d0e11;
    --bg-topbar: #1e2128;
    --border: #2d3139;
    --text-main: #f1f3f5;
    --text-muted: #adb5bd;
    --bg-th: #252830;
    --bg-hover: #252830;
    --bg-quick: #252830;
    --border-side: rgba(220, 53, 69, 0.5);
    --notif-border: #2d3139;
    --badge-bg: #252830;
    --badge-color: #f1f3f5;
}
.sidebar-coord {
    position: fixed;
    top: 0;
    left: 0;
    width: 260px;
    height: 100vh;
    background: var(--bg-sidebar);
    display: flex;
    flex-direction: column;
    z-index: 1040;
    box-shadow: 3px 0 20px rgba(0, 0, 0, 0.2);
    border-right: 2px solid var(--border-side);
    transition: transform 0.3s;
}
.sb-brand {
    padding: 1.4rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    gap: 12px;
}
.sb-brand-title {
    font-weight: 800;
    font-size: 1.05rem;
    color: #fff;
    line-height: 1.2;
}
.sb-brand-sub {
    font-size: 0.78rem;
    font-weight: 700;
    color: #ff4d5e;
}
.sb-user {
    padding: 1.1rem 1.5rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    gap: 10px;
}
.sb-avatar {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--rojo);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 800;
    font-size: 0.9rem;
    flex-shrink: 0;
}
.sb-name {
    color: #fff;
    font-size: 0.9rem;
    font-weight: 700;
}
.sb-role {
    color: #94a3b8;
    font-size: 0.75rem;
    font-weight: 600;
    margin-top: 2px;
}
.sb-nav {
    flex: 1;
    overflow-y: auto;
    padding: 0.5rem 0;
    scrollbar-width: none;
}
.sb-nav::-webkit-scrollbar {
    display: none;
}
.sb-section {
    padding: 0.9rem 1.5rem 0.3rem;
    color: #64748b;
    font-size: 0.72rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    font-weight: 700;
}
.sb-link {
    display: flex;
    align-items: center;
    gap: 11px;
    padding: 0.68rem 1.5rem;
    color: #cbd5e1;
    text-decoration: none;
    font-size: 0.9rem;
    font-weight: 500;
    border-left: 3px solid transparent;
    transition: all 0.15s;
}
.sb-link:hover {
    background: rgba(255, 255, 255, 0.06);
    color: #fff;
}
.sb-link.active {
    background: rgba(220, 53, 69, 0.18);
    color: #fff;
    border-left-color: var(--rojo);
    font-weight: 700;
}
.sb-link i {
    font-size: 1.05rem;
    width: 20px;
    text-align: center;
}
.sb-badge {
    margin-left: auto;
    background: var(--rojo);
    color: #fff;
    font-size: 0.68rem;
    font-weight: 800;
    padding: 2px 7px;
    border-radius: 20px;
}
.main-coord {
    transition: margin-left 0.3s;
}
.topbar-coord {
    background: var(--bg-topbar);
    padding: 0.9rem 1.5rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 0;
    z-index: 1030;
    border-bottom: 2px solid rgba(220, 53, 69, 0.1);
    transition: background 0.3s;
}
.topbar-title {
    font-weight: 800;
    color: var(--text-main);
    font-size: 1.2rem;
}
.topbar-btn {
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1.35rem;
    cursor: pointer;
    padding: 4px;
    position: relative;
    transition: color 0.2s;
}
.topbar-btn:hover {
    color: var(--rojo);
}
.notif-dot {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 8px;
    height: 8px;
    background: var(--rojo);
    border-radius: 50%;
    border: 2px solid var(--bg-topbar);
}
.content-coord {
    padding: 1.5rem;
}
.stat-card {
    background: var(--bg-card);
    border-radius: 14px;
    padding: 1.2rem;
    display: flex;
    align-items: center;
    gap: 14px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--border);
    transition:
        background 0.3s,
        border-color 0.3s;
}
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
}
.stat-icon.red {
    background: rgba(220, 53, 69, 0.12);
    color: var(--rojo);
}
.stat-icon.blue {
    background: rgba(13, 110, 253, 0.12);
    color: #0d6efd;
}
.stat-icon.green {
    background: rgba(25, 135, 84, 0.12);
    color: #198754;
}
.stat-icon.orange {
    background: rgba(255, 143, 0, 0.12);
    color: #ff8f00;
}
.stat-icon.purple {
    background: rgba(111, 66, 193, 0.12);
    color: #6f42c1;
}
.stat-num {
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1;
}
.stat-label {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-top: 3px;
}
.section-card {
    background: var(--bg-card);
    border-radius: 14px;
    box-shadow: 0 3px 12px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--border);
    overflow: hidden;
    transition:
        background 0.3s,
        border-color 0.3s;
    margin-bottom: 1.25rem;
}
.section-header {
    padding: 1.1rem 1.4rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.section-header h6 {
    margin: 0;
    font-weight: 800;
    color: var(--text-main);
    font-size: 1rem;
}
.quick-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
    padding: 0.9rem 0.5rem;
    background: var(--bg-quick);
    border-radius: 10px;
    text-decoration: none;
    color: var(--text-main);
    font-size: 0.82rem;
    font-weight: 700;
    border: 1px solid var(--border);
    transition: all 0.2s;
}
.quick-action:hover {
    background: rgba(220, 53, 69, 0.08);
    border-color: var(--rojo);
    color: var(--rojo);
}
.quick-action i {
    font-size: 1.4rem;
}
.custom-table {
    width: 100%;
    border-collapse: collapse;
}
.custom-table thead th {
    padding: 0.75rem 1rem;
    background: var(--bg-th);
    color: var(--text-muted);
    font-size: 0.8rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid var(--border);
}
.custom-table tbody td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border);
    color: var(--text-main);
    font-size: 0.9rem;
}
.custom-table tbody tr:hover td {
    background: var(--bg-hover);
}
.badge-status {
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.78rem;
    font-weight: 700;
}
.badge-aprobado {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
}
.badge-proceso {
    background: #cff4fc;
    color: #055160;
    border: 1px solid #b6effb;
}
.badge-pendiente {
    background: #fff3cd;
    color: #664d03;
    border: 1px solid #ffecb5;
}
.badge-critico {
    background: #f8d7da;
    color: #842029;
    border: 1px solid #f5c2c7;
}
[data-theme="dark"] .badge-aprobado {
    background: rgba(25, 135, 84, 0.25);
    color: #a3cfbb;
    border-color: rgba(25, 135, 84, 0.4);
}
[data-theme="dark"] .badge-proceso {
    background: rgba(13, 110, 253, 0.25);
    color: #9ec5fe;
    border-color: rgba(13, 110, 253, 0.4);
}
[data-theme="dark"] .badge-pendiente {
    background: rgba(255, 193, 7, 0.25);
    color: #ffe69c;
    border-color: rgba(255, 193, 7, 0.4);
}
[data-theme="dark"] .badge-critico {
    background: rgba(220, 53, 69, 0.25);
    color: #f8d7da;
    border-color: rgba(220, 53, 69, 0.4);
}
.notif-item {
    display: flex;
    gap: 12px;
    padding: 0.9rem 1.4rem;
    border-bottom: 1px solid var(--notif-border);
    align-items: flex-start;
}
.notif-item:last-child {
    border-bottom: none;
}
.notif-icon {
    width: 34px;
    height: 34px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
    flex-shrink: 0;
}
.notif-icon.warn {
    background: #fff3cd;
    color: #664d03;
}
.notif-icon.info {
    background: #cff4fc;
    color: #055160;
}
.notif-icon.danger {
    background: #f8d7da;
    color: #842029;
}
[data-theme="dark"] .notif-icon.warn {
    background: rgba(255, 193, 7, 0.2);
    color: #ffe69c;
}
[data-theme="dark"] .notif-icon.info {
    background: rgba(13, 110, 253, 0.2);
    color: #9ec5fe;
}
[data-theme="dark"] .notif-icon.danger {
    background: rgba(220, 53, 69, 0.2);
    color: #f8d7da;
}
.notif-text {
    font-size: 0.88rem;
    color: var(--text-main);
    font-weight: 500;
    line-height: 1.5;
}
.notif-text strong {
    color: var(--rojo);
}
.notif-time {
    font-size: 0.75rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-top: 3px;
}
.sidebar-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1035;
    display: none;
}
@media (min-width: 992px) {
    .sidebar-coord {
        transform: translateX(0) !important;
    }
    .main-coord {
        margin-left: 260px;
    }
    .toggle-btn {
        display: none !important;
    }
}
@media (max-width: 991.98px) {
    .sidebar-coord {
        transform: translateX(-100%);
    }
    .main-coord {
        margin-left: 0;
    }
    .sidebar-coord.show {
        transform: translateX(0);
    }
    .sidebar-overlay.show {
        display: block !important;
    }
    .content-coord {
        padding: 1rem;
    }
}
</style>
