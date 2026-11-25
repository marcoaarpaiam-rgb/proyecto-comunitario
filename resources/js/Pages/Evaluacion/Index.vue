<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body, #f8f9fa);
            color: var(--text-main, #212529);
        "
    >
        <!-- Sidebar Dinámico según Rol -->
        <SidebarProfesor
            v-if="esProfesor"
            :sidebar-open="sidebarOpen"
            @close="sidebarOpen = false"
        />
        <SidebarLider
            v-else-if="esLider"
            :sidebar-open="sidebarOpen"
            @close="sidebarOpen = false"
        />
        <SidebarCoordinador
            v-else
            :sidebar-open="sidebarOpen"
            @close="sidebarOpen = false"
        />

        <!-- CONTENIDO PRINCIPAL -->
        <div class="main" :class="esCoordinador ? 'main-coord-layout' : 'main-prof-layout'">
            <!-- Topbar -->
            <div class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <div class="tb-title d-flex align-items-center gap-2">
                        <i class="bi bi-journal-check text-danger fs-5 d-none d-sm-inline"></i>
                        <span>Rúbricas Oficiales — Evaluación por Trayecto</span>
                        <span v-if="esProfesor" class="badge bg-danger-subtle text-danger border border-danger-subtle small ms-2">
                            Módulo Docente
                        </span>
                        <span v-else class="badge bg-dark-subtle text-dark border small ms-2">
                            Coordinación PNF
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <Link
                        :href="esProfesor ? '/profesor' : '/coordinador'"
                        class="btn btn-outline-secondary btn-sm rounded-pill px-3 d-none d-sm-inline-flex align-items-center gap-1"
                    >
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </Link>
                    <button class="tb-btn" @click="toggleTema" title="Cambiar tema">
                        <i class="bi" :class="tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'"></i>
                    </button>
                </div>
            </div>

            <div class="content p-3 p-md-4">
                <!-- Alertas Flash -->
                <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show shadow-sm rounded-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ $page.props.flash.success }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $page.props.flash.error }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <!-- Cabecera del Motor de Evaluación -->
                <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                    <div>
                        <h4 class="fw-bold m-0 d-flex align-items-center gap-2" style="color: var(--text-main);">
                            <i class="bi bi-award-fill text-danger fs-4"></i>
                            Motor de Evaluación de Proyectos (Semestres 1 y 2)
                        </h4>
                        <p class="text-muted small m-0 mt-1">
                            Gestión de rúbricas ponderadas, calificaciones cuantitativas sobre 20 pts y veredictos cualitativos oficiales del PNFI.
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge border p-2 d-flex align-items-center gap-2 shadow-sm rounded-pill px-3" style="background: var(--bg-card); color: var(--text-main); border-color: var(--border) !important;">
                            <i class="bi bi-info-circle-fill text-danger"></i>
                            <span class="small">Semestre 1: <strong>40%</strong> &bull; Semestre 2: <strong>60%</strong></span>
                        </span>
                    </div>
                </div>

                <!-- Tabla de Proyectos Activos Optimizada -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden" style="background: var(--bg-card); border: 1px solid var(--border) !important;">
                    <div class="card-header border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-3" style="background: var(--bg-card); border-color: var(--border) !important;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-kanban-fill text-danger fs-5"></i>
                            <h6 class="m-0 fw-bold" style="color: var(--text-main);">Proyectos Sociotecnológicos Activos</h6>
                            <span class="badge bg-danger text-white rounded-pill ms-2 px-2 py-1 small">
                                {{ proyectosFiltrados.length }} {{ proyectosFiltrados.length === 1 ? 'proyecto' : 'proyectos' }}
                            </span>
                        </div>

                        <!-- Barra de Búsqueda -->
                        <div class="input-group" style="max-width: 350px;">
                            <span class="input-group-text border-end-0 text-muted" style="background: var(--bg-body); border-color: var(--border);">
                                <i class="bi bi-search"></i>
                            </span>
                            <input
                                v-model="busqueda"
                                type="text"
                                class="form-control border-start-0 ps-0"
                                style="background: var(--bg-body); color: var(--text-main); border-color: var(--border);"
                                placeholder="Buscar por código, título, comunidad, sección..."
                            />
                            <button
                                v-if="busqueda"
                                class="btn border"
                                style="background: var(--bg-body); color: var(--text-muted); border-color: var(--border);"
                                type="button"
                                @click="busqueda = ''"
                            >
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 w-100 table-borderless" style="color: var(--text-main);">
                                <thead class="border-bottom small text-uppercase fw-bold" style="background: var(--bg-th); color: var(--text-muted); border-color: var(--border) !important;">
                                    <tr>
                                        <th class="ps-4 py-3" style="min-width: 170px;">Equipo / Código</th>
                                        <th class="py-3" style="min-width: 320px;">Proyecto Sociotecnológico</th>
                                        <th class="py-3" style="min-width: 170px;">Trayecto / Sección</th>
                                        <th class="text-center py-3" style="min-width: 140px;">Semestre 1 (40%)</th>
                                        <th class="text-center py-3" style="min-width: 140px;">Semestre 2 (60%)</th>
                                        <th class="text-center py-3" style="min-width: 150px;">Resultado Final</th>
                                        <th class="text-center pe-4 py-3" style="min-width: 160px;">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="p in proyectosFiltrados"
                                        :key="p.id_proyecto"
                                        class="border-bottom"
                                        style="border-color: var(--border) !important;"
                                    >
                                        <!-- Código / Equipo -->
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="p-2 rounded bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                                                    <i class="bi bi-people-fill fs-5"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold font-monospace text-danger fs-6 lh-1">
                                                        {{ p.codigo_equipo }}
                                                    </div>
                                                    <span class="badge border small mt-1 font-monospace" style="background: var(--badge-bg); color: var(--text-muted); border-color: var(--border) !important;">
                                                        #{{ p.id_proyecto }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Proyecto Sociotecnológico & Comunidad -->
                                        <td class="py-3">
                                            <div class="fw-bold fs-6 mb-1 text-wrap" style="color: var(--text-main); line-height: 1.35;" :title="p.titulo_proyecto">
                                                {{ p.titulo_proyecto }}
                                            </div>
                                            <div class="d-flex align-items-center gap-1 text-muted small">
                                                <i class="bi bi-geo-alt-fill text-danger flex-shrink-0"></i>
                                                <span class="text-truncate" style="max-width: 290px;" :title="p.comunidad_nombre">
                                                    {{ p.comunidad_nombre || 'Sin comunidad asignada' }}
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Trayecto / Sección -->
                                        <td class="py-3">
                                            <div class="d-flex flex-column gap-1">
                                                <span class="badge bg-secondary-subtle border-0 fw-semibold text-start px-2 py-1" style="width: fit-content; color: var(--text-main);">
                                                    {{ p.trayecto_nombre || 'PNFI' }}
                                                </span>
                                                <span class="badge border text-start px-2 py-1" style="width: fit-content; background: var(--badge-bg); color: var(--text-muted); border-color: var(--border) !important;">
                                                    Sección: <strong>{{ p.seccion_codigo || '—' }}</strong>
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Semestre 1 (40%) -->
                                        <td class="text-center py-3">
                                            <div v-if="p.semestre1 && p.semestre1.estado_semestre === 'Culminado'">
                                                <span class="badge bg-success-subtle text-success border border-success px-3 py-1 rounded-pill fw-bold fs-6 shadow-sm">
                                                    {{ formatearNota(p.semestre1.nota_cuantitativa_semestre) }} pts
                                                </span>
                                                <div class="small text-success fw-bold mt-1" style="font-size: 0.72rem;">Culminado</div>
                                            </div>
                                            <div v-else-if="p.semestre1 && p.semestre1.estado_semestre === 'En Evaluacion'">
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-3 py-1 rounded-pill fw-bold">
                                                    En Evaluación
                                                </span>
                                            </div>
                                            <div v-else>
                                                <span class="badge border px-3 py-1 rounded-pill" style="background: var(--badge-bg); color: var(--text-muted); border-color: var(--border) !important;">
                                                    En Curso
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Semestre 2 (60%) -->
                                        <td class="text-center py-3">
                                            <div v-if="p.semestre2 && p.semestre2.estado_semestre === 'Culminado'">
                                                <span class="badge bg-success-subtle text-success border border-success px-3 py-1 rounded-pill fw-bold fs-6 shadow-sm">
                                                    {{ formatearNota(p.semestre2.nota_cuantitativa_semestre) }} pts
                                                </span>
                                                <div class="small text-success fw-bold mt-1" style="font-size: 0.72rem;">Culminado</div>
                                            </div>
                                            <div v-else-if="p.semestre2 && p.semestre2.estado_semestre === 'En Evaluacion'">
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning px-3 py-1 rounded-pill fw-bold">
                                                    En Evaluación
                                                </span>
                                            </div>
                                            <div v-else>
                                                <span class="badge border px-3 py-1 rounded-pill" style="background: var(--badge-bg); color: var(--text-muted); border-color: var(--border) !important;">
                                                    En Curso
                                                </span>
                                            </div>
                                        </td>

                                        <!-- Resultado Final -->
                                        <td class="text-center py-3">
                                            <div v-if="p.acta_cerrada">
                                                <div class="fw-bold text-danger fs-5">
                                                    {{ formatearNota(p.calificacion_definitiva_cuantitativa) }} pts
                                                </div>
                                                <span
                                                    class="badge rounded-pill small mt-1 shadow-sm px-2 py-1"
                                                    :class="Number(p.calificacion_definitiva_cuantitativa) >= 16 ? 'bg-success text-white' : 'bg-danger text-white'"
                                                    style="font-size: 0.72rem;"
                                                >
                                                    {{ p.calificacion_definitiva_cualitativa }}
                                                </span>
                                            </div>
                                            <span v-else class="badge border px-3 py-1 rounded-pill" style="background: var(--badge-bg); color: var(--text-muted); border-color: var(--border) !important;">
                                                Pendiente
                                            </span>
                                        </td>

                                        <!-- Acciones -->
                                        <td class="text-center text-nowrap pe-4 py-3">
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <Link
                                                    :href="`/evaluaciones/recorrido/${p.id_proyecto}`"
                                                    class="btn btn-sm btn-danger fw-bold rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1"
                                                >
                                                    <i class="bi bi-clipboard2-check"></i>
                                                    <span>Ver Recorrido</span>
                                                </Link>
                                                <a
                                                    v-if="p.acta_cerrada"
                                                    :href="`/evaluaciones/pdf/${p.id_proyecto}`"
                                                    target="_blank"
                                                    class="btn btn-sm btn-outline-danger rounded-circle"
                                                    title="Descargar Acta Final Oficial PDF"
                                                    style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"
                                                >
                                                    <i class="bi bi-file-earmark-pdf"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>

                                    <tr v-if="proyectosFiltrados.length === 0">
                                        <td colspan="7" class="text-center py-5 text-muted">
                                            <div class="p-4">
                                                <i class="bi bi-inbox fs-1 d-block mb-2 text-secondary"></i>
                                                <p class="mb-1 fw-bold fs-6" style="color: var(--text-main);">No se encontraron proyectos</p>
                                                <small class="text-muted">
                                                    {{ busqueda ? 'Prueba cambiando los términos de búsqueda.' : (esProfesor ? 'Aún no tienes proyectos asignados en tus secciones.' : 'No hay proyectos sociotecnológicos registrados.') }}
                                                </small>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import SidebarProfesor from "@/Components/SidebarProfesor.vue";
import SidebarCoordinador from "@/Components/SidebarCoordinador.vue";
import SidebarLider from "@/Components/SidebarLider.vue";

const props = defineProps({
    proyectos: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const sidebarOpen = ref(false);

// Detección robusta de rol para profesores, coordinadores y estudiantes
const rolUsuario = computed(() => {
    const r =
        page.props.auth?.rol ||
        page.props.auth?.user?.usu_rol ||
        page.props.auth?.user?.rol ||
        page.props.auth?.user?.roles?.[0] ||
        "";
    return String(r).toLowerCase();
});

const esProfesor = computed(() => {
    return (
        rolUsuario.value.includes("profesor") ||
        rolUsuario.value.includes("docente") ||
        (page.props.auth?.roles && page.props.auth.roles.some((r) => String(r).toLowerCase().includes("profesor"))) ||
        window.location.pathname.startsWith("/profesor")
    );
});

const esLider = computed(() => {
    return (
        rolUsuario.value.includes("lider") ||
        rolUsuario.value.includes("estudiante") ||
        window.location.pathname.startsWith("/lider")
    );
});

const esCoordinador = computed(() => {
    return !esProfesor.value && !esLider.value;
});

const tema = ref(
    localStorage.getItem(esProfesor.value ? "prof-theme" : "coord-theme") || "light"
);

const toggleTema = () => {
    const k = esProfesor.value ? "prof-theme" : "coord-theme";
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem(k, tema.value);
    document.documentElement.setAttribute("data-theme", tema.value);
    document.body.setAttribute("data-theme", tema.value);
};

const busqueda = ref("");

const proyectosFiltrados = computed(() => {
    if (!busqueda.value.trim()) return props.proyectos;
    const term = busqueda.value.toLowerCase().trim();
    return props.proyectos.filter((p) => {
        return (
            (p.codigo_equipo && p.codigo_equipo.toLowerCase().includes(term)) ||
            (p.titulo_proyecto && p.titulo_proyecto.toLowerCase().includes(term)) ||
            (p.comunidad_nombre && p.comunidad_nombre.toLowerCase().includes(term)) ||
            (p.seccion_codigo && String(p.seccion_codigo).toLowerCase().includes(term)) ||
            (p.trayecto_nombre && p.trayecto_nombre.toLowerCase().includes(term))
        );
    });
});

const formatearNota = (val) => {
    if (val === null || val === undefined || isNaN(val)) return "0.00";
    return Number(val).toFixed(2);
};
</script>

<style scoped>
:root {
    --rojo: #dc3545;
    --bg-body: #f0f2f5;
    --bg-card: #ffffff;
    --bg-topbar: #ffffff;
    --border: #dee2e6;
    --text-main: #1a1a1a;
    --text-muted: #6c757d;
    --bg-th: #f8f9fa;
    --bg-hover: #fff8f8;
    --badge-bg: #f8f9fa;
}
[data-theme="dark"] {
    --bg-body: #111317;
    --bg-card: #1e2128;
    --bg-topbar: #1e2128;
    --border: #2d3139;
    --text-main: #f1f3f5;
    --text-muted: #adb5bd;
    --bg-th: #252830;
    --bg-hover: #252830;
    --badge-bg: #252830;
}
.main {
    min-height: 100vh;
    background: var(--bg-body);
    color: var(--text-main);
    transition: margin-left 0.3s, background 0.2s, color 0.2s;
}
.main-coord-layout {
    margin-left: 260px;
}
.main-prof-layout {
    margin-left: 250px;
}
.topbar {
    height: 65px;
    background: var(--bg-topbar);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
    position: sticky;
    top: 0;
    z-index: 1020;
    transition: background 0.2s, border-color 0.2s;
}
.tb-title {
    font-size: 1.12rem;
    font-weight: 700;
    color: var(--text-main);
}
.tb-btn {
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1.2rem;
    cursor: pointer;
    padding: 4px;
    transition: color 0.2s;
}
.tb-btn:hover {
    color: #dc3545;
}
.table td, .table th {
    padding-top: 1rem;
    padding-bottom: 1rem;
}
.table tbody tr:hover td {
    background: var(--bg-hover) !important;
}
@media (max-width: 991.98px) {
    .main,
    .main-coord-layout,
    .main-prof-layout {
        margin-left: 0 !important;
    }
}
</style>
