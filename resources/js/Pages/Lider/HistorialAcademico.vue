<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
            font-family: 'Segoe UI', system-ui, sans-serif;
        "
    >
        <div
            class="sb-overlay"
            :class="{ show: sidebarOpen }"
            @click="sidebarOpen = false"
        ></div>

        <!-- SIDEBAR -->
        <div class="sb-lid" :class="{ show: sidebarOpen }">
            <div class="sb-brand">
                <div
                    style="
                        width: 34px;
                        height: 34px;
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
                    <div class="sb-title">UPTP — Proyectos</div>
                    <div class="sb-sub">PNF Informática</div>
                </div>
                <button
                    class="btn btn-sm text-white ms-auto fs-5 p-0 d-lg-none"
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
                    <div class="sb-role">
                        <i
                            class="bi bi-star-fill me-1"
                            style="color: #ffd700; font-size: 0.65rem"
                        ></i
                        >Líder de Equipo
                    </div>
                </div>
            </div>
            <div class="sb-nav">
                <div class="sb-sec">Mi Proyecto</div>
                <Link href="/lider" class="sb-link">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </Link>
                <Link href="/lider/equipo" class="sb-link">
                    <i class="bi bi-people"></i> Mi Equipo
                </Link>
                <Link href="/lider/proyecto" class="sb-link">
                    <i class="bi bi-building"></i> Mi Comunidad
                </Link>
                <div class="sb-sec">Acciones</div>
                <Link href="/lider/entregables" class="sb-link">
                    <i class="bi bi-cloud-upload"></i> Entregables
                </Link>
                <Link href="/lider/carta" class="sb-link">
                    <i class="bi bi-send"></i> Carta de Presentación
                </Link>
                <Link href="/lider/historial-academico" class="sb-link active">
                    <i class="bi bi-mortarboard"></i> Historial Académico
                </Link>
                <div style="padding: 0.75rem 1.4rem; margin-top: auto">
                    <form @submit.prevent="logout">
                        <button
                            type="submit"
                            class="sb-link w-100 text-start border-0 p-0"
                            style="
                                color: #ff8591;
                                font-weight: 700;
                                background: none;
                                font-size: 0.88rem;
                            "
                        >
                            <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- MAIN -->
        <div class="main-lid">
            <div class="topbar-lid">
                <div class="d-flex align-items-center gap-3">
                    <button
                        class="btn btn-outline-danger btn-sm toggle-btn px-2"
                        @click="sidebarOpen = true"
                    >
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div class="topbar-title">
                        <i class="bi bi-mortarboard-fill me-2 text-danger"></i>Historial Académico
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="tb-btn" @click="toggleTema" title="Cambiar tema">
                        <i
                            class="bi"
                            :class="tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'"
                        ></i>
                    </button>
                </div>
            </div>

            <!-- CONTENIDO -->
            <div class="content-lid">
                <div v-if="!equipo" class="text-center py-5">
                    <i class="bi bi-exclamation-triangle text-danger fs-1 d-block mb-3"></i>
                    <h5 class="fw-bold">No tienes un equipo asignado</h5>
                    <p class="text-muted">Comunícate con tu profesor de proyecto para formar parte de un equipo.</p>
                </div>

                <template v-else>
                    <!-- BANNER HERO (Rojo, Negro y Blanco) -->
                    <div class="banner-lid mb-4">
                        <div class="d-flex align-items-center gap-3 flex-wrap flex-md-nowrap">
                            <div style="width: 46px; height: 46px; background: #dc3545; border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 1.35rem; flex-shrink: 0; box-shadow: 0 4px 10px rgba(220, 53, 69, 0.35);">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                    <span class="badge bg-danger fs-6 px-2.5 py-1">{{ equipo.equ_codigo }}</span>
                                    <span class="badge bg-secondary">{{ equipo.seccion?.sec_codigo }}</span>
                                    <span class="badge" style="background: rgba(255, 255, 255, 0.15); color: #fff;">{{ equipo.trayecto?.tra_nombre }}</span>
                                </div>
                                <div class="b-title fs-5 fw-bold text-white mb-1">{{ equipo.equ_titulo }}</div>
                                <div class="b-sub d-flex flex-wrap gap-3 text-white-50 small">
                                    <span>
                                        <i class="bi bi-geo-alt me-1 text-danger"></i>
                                        <strong>Comunidad:</strong> {{ equipo.proyecto_comunidad?.comunidad?.com_nombre || 'No asignada' }}
                                    </span>
                                    <span>
                                        <i class="bi bi-tag me-1 text-danger"></i>
                                        <strong>Tipo:</strong> {{ equipo.proyecto_comunidad?.tipo_proyecto?.tpr_nombre || 'No definido' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="text-md-end mt-2 mt-md-0">
                            <span class="text-white-50 small d-block mb-1">Estado del Proyecto</span>
                            <span class="bs" :class="equipo.resultado_proyecto?.estado_proyecto?.epr_nombre === 'Aprobado' ? 'ba' : 'bd'">
                                {{ equipo.resultado_proyecto?.estado_proyecto?.epr_nombre || 'En Desarrollo' }}
                            </span>
                        </div>
                    </div>

                    <!-- KPI CARDS (Rojo, Negro y Blanco) -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-4 col-sm-6">
                            <div class="sc p-3 h-100 mb-0" style="border-left: 4px solid #dc3545 !important;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted small fw-semibold d-block">Puntos de Control</span>
                                        <h3 class="fw-bold mb-0 text-danger">
                                            {{ puntosCumplidos }} / {{ totalPuntos }}
                                        </h3>
                                        <small class="text-muted">Cumplidos</small>
                                    </div>
                                    <div class="p-3 rounded-circle" style="background: rgba(220, 53, 69, 0.1);">
                                        <i class="bi bi-flag-fill fs-3 text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="sc p-3 h-100 mb-0" style="border-left: 4px solid #dc3545 !important;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted small fw-semibold d-block">Entregables</span>
                                        <h3 class="fw-bold mb-0 text-danger">
                                            {{ entregablesAprobados }} / {{ totalEntregables }}
                                        </h3>
                                        <small class="text-muted">Aprobados</small>
                                    </div>
                                    <div class="p-3 rounded-circle" style="background: rgba(220, 53, 69, 0.1);">
                                        <i class="bi bi-file-earmark-check-fill fs-3 text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-12">
                            <div class="sc p-3 h-100 mb-0" style="border-left: 4px solid #dc3545 !important;">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div>
                                        <span class="text-muted small fw-semibold d-block">Socializaciones</span>
                                        <h3 class="fw-bold mb-0 text-danger">
                                            {{ socializacionesAptas }} / {{ totalSocializaciones }}
                                        </h3>
                                        <small class="text-muted">Aptas / Aprobadas</small>
                                    </div>
                                    <div class="p-3 rounded-circle" style="background: rgba(220, 53, 69, 0.1);">
                                        <i class="bi bi-people-fill fs-3 text-danger"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TABS ACADÉMICOS (Rojo, Negro y Blanco) -->
                    <div class="d-flex gap-2 mb-3 pb-2 overflow-x-auto" style="border-bottom: 1px solid var(--border);">
                        <button
                            type="button"
                            class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                            :style="tabActivo === 'puntos' ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;' : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'"
                            @click="tabActivo = 'puntos'"
                        >
                            <i class="bi bi-flag me-1"></i> Puntos de Control ({{ totalPuntos }})
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                            :style="tabActivo === 'entregables' ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;' : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'"
                            @click="tabActivo = 'entregables'"
                        >
                            <i class="bi bi-file-earmark-check me-1"></i> Entregables ({{ totalEntregables }})
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                            :style="tabActivo === 'socializaciones' ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;' : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'"
                            @click="tabActivo = 'socializaciones'"
                        >
                            <i class="bi bi-people me-1"></i> Socializaciones ({{ totalSocializaciones }})
                        </button>
                        <button
                            type="button"
                            class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                            :style="tabActivo === 'integrantes' ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;' : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'"
                            @click="tabActivo = 'integrantes'"
                        >
                            <i class="bi bi-person-lines-fill me-1"></i> Integrantes ({{ equipo.integrantes?.length || 0 }})
                        </button>
                    </div>

                    <!-- TAB 1: PUNTOS DE CONTROL -->
                    <div v-if="tabActivo === 'puntos'">
                        <div v-if="!equipo.puntos_control || equipo.puntos_control.length === 0" class="sc text-center py-5 text-muted">
                            <i class="bi bi-info-circle fs-2 d-block mb-2 text-danger"></i>
                            No hay puntos de control registrados por el profesor aún.
                        </div>
                        <div v-else class="sc">
                            <div class="sh">
                                <h6><i class="bi bi-flag-fill me-2 text-danger"></i>Puntos de Control Asignados</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="ct">
                                    <thead>
                                        <tr>
                                            <th>N°</th>
                                            <th>Nombre del Punto de Control</th>
                                            <th>Fecha Límite</th>
                                            <th>Estado</th>
                                            <th>Observaciones del Profesor</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(puc, idx) in equipo.puntos_control" :key="puc.puc_id">
                                            <td><span class="bs bsec">{{ puc.puc_orden || idx + 1 }}</span></td>
                                            <td>
                                                <div class="fw-bold">{{ puc.puc_nombre }}</div>
                                                <small class="text-muted" v-if="puc.puc_descripcion">{{ puc.puc_descripcion }}</small>
                                            </td>
                                            <td>
                                                <span>{{ puc.puc_fecha_reprogramada || puc.puc_fecha_limite }}</span>
                                                <span v-if="puc.puc_fecha_reprogramada" class="bs bw ms-1">Reprogramada</span>
                                            </td>
                                            <td>
                                                <span v-if="puc.seguimientos && puc.seguimientos.length > 0 && puc.seguimientos[0].seq_cumplido" class="bs ba">
                                                    <i class="bi bi-check-circle me-1"></i>Cumplido
                                                </span>
                                                <span v-else-if="puc.seguimientos && puc.seguimientos.length > 0 && puc.seguimientos[0].seq_cumplido === false" class="bs bd">
                                                    <i class="bi bi-x-circle me-1"></i>No Cumplido
                                                </span>
                                                <span v-else class="bs bw">
                                                    <i class="bi bi-clock me-1"></i>Pendiente
                                                </span>
                                            </td>
                                            <td>
                                                <small v-if="puc.seguimientos && puc.seguimientos.length > 0 && puc.seguimientos[0].seq_observaciones">
                                                    {{ puc.seguimientos[0].seq_observaciones }}
                                                </small>
                                                <small v-else class="text-muted">—</small>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 2: ENTREGABLES -->
                    <div v-if="tabActivo === 'entregables'">
                        <div v-if="!equipo.entregables || equipo.entregables.length === 0" class="sc text-center py-5 text-muted">
                            <i class="bi bi-cloud-arrow-up fs-2 d-block mb-2 text-danger"></i>
                            Tu equipo no ha subido ningún entregable aún.
                            <div class="mt-3">
                                <Link href="/lider/entregables" class="btn btn-sm btn-danger fw-bold rounded-pill px-3">
                                    <i class="bi bi-cloud-upload me-1"></i>Subir Entregable
                                </Link>
                            </div>
                        </div>
                        <div v-else class="sc">
                            <div class="sh">
                                <h6><i class="bi bi-file-earmark-check-fill me-2 text-danger"></i>Entregables Subidos por el Equipo</h6>
                                <Link href="/lider/entregables" class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3">
                                    <i class="bi bi-cloud-upload me-1"></i>Gestionar Entregables
                                </Link>
                            </div>
                            <div class="table-responsive">
                                <table class="ct">
                                    <thead>
                                        <tr>
                                            <th>Tipo / Nombre del Archivo</th>
                                            <th>Punto de Control</th>
                                            <th>Versión</th>
                                            <th>Estado</th>
                                            <th>Observaciones</th>
                                            <th class="text-end pe-3">Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="ent in equipo.entregables" :key="ent.entr_id">
                                            <td>
                                                <div class="fw-bold">{{ ent.tipo_entregable?.tet_nombre || 'Entregable' }}</div>
                                                <small class="text-muted"><i class="bi bi-file-earmark me-1"></i>{{ ent.entr_nombre_archivo }}</small>
                                            </td>
                                            <td>
                                                <span v-if="ent.punto_control" class="bs bsec">{{ ent.punto_control.puc_nombre }}</span>
                                                <span v-else class="text-muted">—</span>
                                            </td>
                                            <td><span class="bs bsec">v{{ ent.entr_version || 1 }}</span></td>
                                            <td>
                                                <span v-if="ent.entr_aprobado === true" class="bs ba">Aprobado</span>
                                                <span v-else-if="ent.entr_aprobado === false" class="bs bd">Rechazado</span>
                                                <span v-else class="bs bw">En Revisión</span>
                                            </td>
                                            <td>
                                                <small v-if="ent.entr_observacion_rechazo" class="text-danger">
                                                    {{ ent.entr_observacion_rechazo }}
                                                </small>
                                                <small v-else class="text-muted">—</small>
                                            </td>
                                            <td class="text-end pe-3">
                                                <a v-if="ent.entr_ruta" :href="`/storage/${ent.entr_ruta}`" target="_blank" class="btn btn-sm btn-outline-danger">
                                                    <i class="bi bi-download me-1"></i>Descargar
                                                </a>
                                                <span v-else class="text-muted">—</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 3: SOCIALIZACIONES -->
                    <div v-if="tabActivo === 'socializaciones'">
                        <div v-if="!equipo.socializaciones || equipo.socializaciones.length === 0" class="sc text-center py-5 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2 text-danger"></i>
                            No hay socializaciones programadas para tu equipo.
                        </div>
                        <div v-else class="row g-3">
                            <div v-for="soc in equipo.socializaciones" :key="soc.soc_id" class="col-md-6">
                                <div class="sc h-100 p-3" style="border-left: 3px solid #dc3545 !important;">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="fw-bold mb-0" style="color: var(--text-main);">{{ soc.tipo_socializacion?.tso_nombre || 'Socialización' }}</h6>
                                        <span v-if="soc.soc_apto === true" class="bs ba">Apto</span>
                                        <span v-else-if="soc.soc_apto === false" class="bs bd">No Apto</span>
                                        <span v-else class="bs bw">Pendiente</span>
                                    </div>
                                    <div class="small text-muted mb-2">
                                        <div><i class="bi bi-calendar-event me-1 text-danger"></i>{{ soc.soc_fecha }} a las {{ soc.soc_hora }}</div>
                                        <div><i class="bi bi-geo-alt me-1 text-danger"></i>{{ soc.soc_lugar }}</div>
                                    </div>
                                    <div v-if="soc.soc_observaciones" class="p-2 rounded small mb-2" style="background: var(--bg-th); border: 1px solid var(--border);">
                                        <strong>Observaciones:</strong> {{ soc.soc_observaciones }}
                                    </div>
                                    <div v-if="soc.jurados && soc.jurados.length > 0">
                                        <small class="fw-semibold text-muted d-block mb-1">Jurados Evaluadores:</small>
                                        <div class="d-flex flex-wrap gap-1">
                                            <span v-for="j in soc.jurados" :key="j.jso_id" class="bs bsec">
                                                {{ j.usuario?.usu_primer_nombre }} {{ j.usuario?.usu_primer_apellido }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TAB 4: INTEGRANTES -->
                    <div v-if="tabActivo === 'integrantes'">
                        <div class="sc">
                            <div class="sh">
                                <h6><i class="bi bi-person-lines-fill me-2 text-danger"></i>Integrantes del Equipo</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="ct">
                                    <thead>
                                        <tr>
                                            <th>Cédula</th>
                                            <th>Nombre y Apellido</th>
                                            <th>Rol en el Equipo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="integ in equipo.integrantes" :key="integ.ein_id">
                                            <td>{{ integ.usuario?.usu_cedula }}</td>
                                            <td class="fw-bold">{{ integ.usuario?.usu_primer_nombre }} {{ integ.usuario?.usu_primer_apellido }}</td>
                                            <td>
                                                <span v-if="integ.ein_es_lider" class="bs bd">
                                                    <i class="bi bi-star-fill me-1 text-warning"></i>Líder
                                                </span>
                                                <span v-else class="bs bsec">Integrante</span>
                                            </td>
                                            <td>
                                                <span v-if="integ.ein_status" class="bs ba">Activo</span>
                                                <span v-else class="bs bsec">Retirado</span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Modal Confirmación Cerrar Sesión -->
        <ModalConfirmar
            ref="modalLogout"
            titulo="Cerrar Sesión"
            mensaje="¿Estás seguro de que deseas cerrar sesión en el sistema?"
            icono="🚪"
            textoConfirmar="Sí, cerrar sesión"
            textoCancelar="Cancelar"
            colorConfirmar="#dc3545"
            @confirmar="ejecutarLogout"
        />
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import ModalConfirmar from "@/Components/ModalConfirmar.vue";

const props = defineProps({
    equipo: { type: Object, default: null },
});

const page = usePage();
const sidebarOpen = ref(false);
const tabActivo = ref("puntos");
const tema = ref(localStorage.getItem("lider-theme") || "light");

const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("lider-theme", tema.value);
};

const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "");
});

// Logout
const modalLogout = ref(null);
const logout = () => {
    modalLogout.value?.abrir();
};
const ejecutarLogout = () => {
    router.post("/logout");
};

// KPIs y estadísticas
const totalPuntos = computed(() => props.equipo?.puntos_control?.length || 0);
const puntosCumplidos = computed(() => {
    return (props.equipo?.puntos_control || []).filter(
        (p) => p.seguimientos && p.seguimientos.length > 0 && p.seguimientos[0].seq_cumplido
    ).length;
});

const totalEntregables = computed(() => props.equipo?.entregables?.length || 0);
const entregablesAprobados = computed(() => {
    return (props.equipo?.entregables || []).filter((e) => e.entr_aprobado === true).length;
});

const totalSocializaciones = computed(() => props.equipo?.socializaciones?.length || 0);
const socializacionesAptas = computed(() => {
    return (props.equipo?.socializaciones || []).filter((s) => s.soc_apto === true).length;
});
</script>

<style scoped>
:root {
    --bg-body: #f4f6f9;
    --bg-card: #ffffff;
    --bg-sidebar: #0d0e11;
    --bg-topbar: #ffffff;
    --border: #dee2e6;
    --text-main: #1a1a1a;
    --text-muted: #6c757d;
    --bg-th: #f8f9fa;
    --bg-hover: #fff8f8;
    --border-side: rgba(220, 53, 69, 0.25);
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
    --border-side: rgba(220, 53, 69, 0.5);
}

/* SIDEBAR */
.sb-lid {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    background: var(--bg-sidebar);
    display: flex;
    flex-direction: column;
    z-index: 1040;
    box-shadow: 3px 0 15px rgba(0, 0, 0, 0.15);
    border-right: 2px solid var(--border-side);
    transition: transform 0.3s;
}
.sb-brand {
    padding: 1.3rem 1.4rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    gap: 10px;
}
.sb-title {
    font-weight: 800;
    font-size: 1rem;
    color: #fff;
    line-height: 1.1;
}
.sb-sub {
    font-size: 0.75rem;
    font-weight: 700;
    color: #ff4d5e;
}
.sb-user {
    padding: 1rem 1.4rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    gap: 10px;
}
.sb-avatar {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #dc3545;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 800;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.sb-name {
    color: #fff;
    font-size: 0.88rem;
    font-weight: 700;
}
.sb-role {
    color: #94a3b8;
    font-size: 0.73rem;
    font-weight: 600;
    margin-top: 1px;
}
.sb-nav {
    flex: 1;
    overflow-y: auto;
    padding: 0.4rem 0;
    scrollbar-width: none;
}
.sb-nav::-webkit-scrollbar {
    display: none;
}
.sb-sec {
    padding: 0.8rem 1.4rem 0.25rem;
    color: #64748b;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    font-weight: 700;
}
.sb-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.65rem 1.4rem;
    color: #cbd5e1;
    text-decoration: none;
    font-size: 0.88rem;
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
    border-left-color: #dc3545;
    font-weight: 700;
}
.sb-link i {
    font-size: 1rem;
    width: 20px;
    text-align: center;
}

/* TOPBAR & MAIN */
.main-lid {
    transition: margin-left 0.3s;
}
.topbar-lid {
    background: var(--bg-topbar);
    padding: 0.85rem 1.4rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    border-bottom: 2px solid rgba(220, 53, 69, 0.1);
    position: sticky;
    top: 0;
    z-index: 1030;
}
.topbar-title {
    font-weight: 800;
    color: var(--text-main);
    font-size: 1.15rem;
}
.tb-btn {
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1.3rem;
    cursor: pointer;
    padding: 4px;
    transition: color 0.2s;
}
.tb-btn:hover {
    color: #dc3545;
}
.content-lid {
    padding: 1.4rem 1.25rem;
    flex: 1;
}

/* BANNER */
.banner-lid {
    background: linear-gradient(135deg, #1e2227, #090a0c);
    border-radius: 13px;
    padding: 1.3rem 1.5rem;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.12);
    border: 1px solid var(--border-side);
    border-left: 4px solid #dc3545;
}
.banner-lid .b-title {
    font-weight: 800;
    font-size: 1.05rem;
}
.banner-lid .b-sub {
    opacity: 0.85;
    font-size: 0.85rem;
    margin-top: 3px;
}

/* CARDS */
.sc {
    background: var(--bg-card);
    border-radius: 13px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--border);
    overflow: hidden;
    transition: background 0.3s, border-color 0.3s;
    margin-bottom: 1.2rem;
}
.sh {
    padding: 1rem 1.3rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.sh h6 {
    margin: 0;
    font-weight: 800;
    color: var(--text-main);
    font-size: 0.97rem;
}

/* TABLES */
.ct {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}
.ct thead th {
    background: var(--bg-th);
    color: var(--text-muted);
    font-size: 0.73rem;
    text-transform: uppercase;
    letter-spacing: 0.6px;
    font-weight: 700;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}
.ct tbody td {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border);
    font-size: 0.85rem;
    color: var(--text-main);
}
.ct tbody tr:last-child td {
    border-bottom: none;
}
.ct tbody tr:hover td {
    background: var(--bg-hover);
}

/* BADGES */
.bs {
    display: inline-flex;
    align-items: center;
    padding: 4px 11px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
}
.ba {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
}
.bd {
    background: #f8d7da;
    color: #842029;
    border: 1px solid #f5c2c7;
}
.bw {
    background: #fff3cd;
    color: #664d03;
    border: 1px solid #ffecb5;
}
.bsec {
    background: var(--bg-th);
    color: var(--text-muted);
    border: 1px solid var(--border);
}
[data-theme="dark"] .ba {
    background: rgba(25, 135, 84, 0.25);
    color: #a3cfbb;
    border-color: rgba(25, 135, 84, 0.4);
}
[data-theme="dark"] .bd {
    background: rgba(220, 53, 69, 0.25);
    color: #f8d7da;
    border-color: rgba(220, 53, 69, 0.4);
}
[data-theme="dark"] .bw {
    background: rgba(255, 193, 7, 0.25);
    color: #ffe69c;
    border-color: rgba(255, 193, 7, 0.4);
}
[data-theme="dark"] .bsec {
    background: #252830;
    color: #adb5bd;
    border-color: #3d424e;
}

/* OVERLAY & RESPONSIVE */
.sb-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1035;
    display: none;
}
@media (min-width: 992px) {
    .sb-lid {
        transform: translateX(0) !important;
    }
    .main-lid {
        margin-left: 250px;
    }
    .toggle-btn {
        display: none !important;
    }
}
@media (max-width: 991.98px) {
    .sb-lid {
        transform: translateX(-100%);
    }
    .main-lid {
        margin-left: 0;
    }
    .sb-lid.show {
        transform: translateX(0);
    }
    .sb-overlay.show {
        display: block !important;
    }
    .content-lid {
        padding: 1rem;
    }
}
</style>
