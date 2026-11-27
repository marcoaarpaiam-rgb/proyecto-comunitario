<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
        "
    >
                <SidebarCoordinador
            :sidebar-open="sidebarOpen"
            @close="sidebarOpen = false"
        />

<div class="main">
            <div class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <button
                        class="btn btn-outline-danger btn-sm toggle-btn px-2"
                        @click="sidebarOpen = true"
                    >
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div class="tb-title">
                        <i
                            class="bi bi-mic me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Socializaciones
                    </div>
                </div>
                <button class="tb-btn" @click="toggleTema">
                    <i
                        class="bi"
                        :class="tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'"
                    ></i>
                </button>
            </div>
            <div class="content">
                <div
                    style="
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        margin-bottom: 1.25rem;
                        flex-wrap: wrap;
                        gap: 10px;
                    "
                >
                    <div>
                        <h4
                            style="
                                font-weight: 800;
                                font-size: 1.25rem;
                                color: var(--text-main);
                                margin: 0;
                            "
                        >
                            Socializaciones
                        </h4>
                        <p
                            style="
                                color: var(--text-muted);
                                font-size: 0.85rem;
                                margin-top: 2px;
                            "
                        >
                            Vista general de todas las socializaciones
                            registradas
                        </p>
                    </div>
                    <div class="srch">
                        <i class="bi bi-search"></i
                        ><input
                            v-model="busqueda"
                            type="text"
                            placeholder="Buscar equipo o tipo..."
                        />
                    </div>
                </div>

                <!-- MÓDULOS SEPARADOS: 1. PRE-SOCIALIZACIÓN VS 2. SOCIALIZACIÓN FINAL -->
                <div class="card border-0 shadow-sm p-2 mb-3 rounded-4" style="background: var(--bg-card); border: 1px solid var(--border) !important;">
                    <ul class="nav nav-pills gap-2 flex-wrap" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-bold d-flex align-items-center gap-2 rounded-pill px-4 py-2"
                                :class="moduloActivo === 'pre_socializacion' ? 'active bg-danger text-white shadow-sm' : 'text-body'"
                                type="button"
                                @click="moduloActivo = 'pre_socializacion'"
                            >
                                <i class="bi bi-person-check-fill fs-5"></i>
                                <span>1. Módulo Pre-Socialización</span>
                                <span
                                    class="badge rounded-pill"
                                    :class="moduloActivo === 'pre_socializacion' ? 'bg-white text-danger' : 'bg-secondary text-white'"
                                >
                                    {{ totalPreSoc }}
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-bold d-flex align-items-center gap-2 rounded-pill px-4 py-2"
                                :class="moduloActivo === 'socializacion_final' ? 'active bg-danger text-white shadow-sm' : 'text-body'"
                                type="button"
                                @click="moduloActivo = 'socializacion_final'"
                            >
                                <i class="bi bi-mortarboard-fill fs-5"></i>
                                <span>2. Módulo Socialización Final</span>
                                <span
                                    class="badge rounded-pill"
                                    :class="moduloActivo === 'socializacion_final' ? 'bg-white text-danger' : 'bg-secondary text-white'"
                                >
                                    {{ totalSocFinal }}
                                </span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="sc">
                    <div class="table-responsive">
                        <table class="ct">
                            <thead>
                                <tr>
                                    <th class="ps-4">Equipo</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Lugar</th>
                                    <th>Trayecto</th>
                                    <th>Resultado</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="filtradas.length === 0">
                                    <td
                                        colspan="7"
                                        class="text-center py-5"
                                        style="color: var(--text-muted)"
                                    >
                                        Sin socializaciones registradas
                                    </td>
                                </tr>
                                <tr v-for="soc in filtradas" :key="soc.soc_id">
                                    <td class="ps-4">
                                        <button
                                            type="button"
                                            class="btn btn-link p-0 text-danger fw-bold text-decoration-none text-start d-flex align-items-center gap-1"
                                            @click="abrirDetalleEquipo(soc)"
                                            title="Ver todas las opciones del equipo"
                                        >
                                            <i class="bi bi-box-arrow-up-right small"></i>
                                            <span>{{ soc.equipo?.equ_codigo }}</span>
                                        </button>
                                        <div class="small text-muted text-truncate" style="max-width: 170px;" :title="soc.equipo?.equ_titulo">
                                            {{ soc.equipo?.equ_titulo || 'Proyecto PST' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="bs"
                                            :class="
                                                soc.tipo_socializacion
                                                    ?.tso_orden === 1
                                                    ? 'bw'
                                                    : 'ba'
                                            "
                                            >{{
                                                soc.tipo_socializacion
                                                    ?.tso_nombre
                                            }}</span
                                        >
                                    </td>
                                    <td style="font-size: 0.88rem">
                                        {{ soc.soc_fecha }}
                                    </td>
                                    <td
                                        style="
                                            font-size: 0.85rem;
                                            color: var(--text-muted);
                                        "
                                    >
                                        {{ soc.soc_lugar }}
                                    </td>
                                    <td style="font-size: 0.85rem">
                                        {{ soc.trayecto?.tra_nombre }}
                                    </td>
                                    <td>
                                        <span
                                            v-if="soc.soc_apto === true"
                                            class="bs ba"
                                            >Apto</span
                                        >
                                        <span
                                            v-else-if="soc.soc_apto === false"
                                            class="bs bd"
                                            >No Apto</span
                                        >
                                        <span
                                            v-if="soc.soc_aprobado === true"
                                            class="bs ba ms-1"
                                            >Aprobado</span
                                        >
                                        <span
                                            v-else-if="
                                                soc.soc_aprobado === false
                                            "
                                            class="bs bd ms-1"
                                            >No Aprobado</span
                                        >
                                        <span
                                            v-if="
                                                soc.soc_apto === null &&
                                                soc.soc_aprobado === null
                                            "
                                            class="bs bsec"
                                            >Pendiente</span
                                        >
                                    </td>
                                    <td class="text-end pe-4">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-dark fw-bold me-1"
                                            @click="abrirDetalleEquipo(soc)"
                                            title="Opciones completas del equipo, expediente y descargas"
                                        >
                                            <i class="bi bi-folder2-open me-1"></i>Opciones
                                        </button>
                                        <a
                                            v-if="soc.equipo?.equ_id"
                                            :href="'/coordinador/equipos/' + soc.equipo.equ_id + '/expediente'"
                                            class="btn btn-sm btn-outline-primary me-1"
                                            title="Ver Expediente del Equipo"
                                        >
                                            <i class="bi bi-file-earmark-person"></i>
                                        </a>
                                        <a
                                            v-if="soc.tipo_socializacion?.tso_orden === 2 && soc.equipo?.equ_id"
                                            :href="'/evaluaciones/planilla-pdf/' + soc.equipo.equ_id"
                                            target="_blank"
                                            class="btn btn-sm btn-outline-primary"
                                            title="Exportar Planilla de Evaluación (PDF)"
                                        >
                                            <i class="bi bi-file-earmark-pdf"></i>
                                        </a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL DETALLE DEL EQUIPO DENTRO DE SOCIALIZACIÓN (COORDINADOR) -->
        <div class="modal fade" id="mDetalleEquipoSoc" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white p-3 border-bottom border-danger border-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger fs-6 font-monospace">{{ socSeleccionadaDetalle?.equipo?.equ_codigo }}</span>
                            <h5 class="modal-title fw-bold text-white m-0">Detalle y Opciones del Equipo</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4 text-dark" v-if="socSeleccionadaDetalle">
                        <!-- Identidad del Proyecto -->
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                <div>
                                    <span class="badge bg-dark me-1">Sección {{ socSeleccionadaDetalle.equipo?.seccion?.sec_codigo }}</span>
                                    <span class="badge bg-secondary me-1">{{ socSeleccionadaDetalle.trayecto?.tra_nombre || socSeleccionadaDetalle.equipo?.seccion?.trayecto?.tra_nombre || 'Trayecto' }}</span>
                                    <span class="badge bg-light text-dark border">{{ socSeleccionadaDetalle.tipo_socializacion?.tso_nombre }}</span>
                                </div>
                                <span v-if="socSeleccionadaDetalle.soc_entrega_cd_informe" class="badge bg-success">
                                    <i class="bi bi-disc-fill me-1"></i>CD / Informe Entregado
                                </span>
                                <span v-else class="badge bg-warning text-dark">
                                    <i class="bi bi-disc me-1"></i>CD / Informe Pendiente
                                </span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">
                                {{ socSeleccionadaDetalle.equipo?.equ_titulo || 'Proyecto Sociotecnológico' }}
                            </h5>
                            <div class="small text-muted mt-2 d-flex flex-wrap gap-3">
                                <div>
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    <strong>Comunidad:</strong> {{ socSeleccionadaDetalle.equipo?.proyectoComunidad?.comunidad?.com_nombre || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.comunidad?.com_nombre || 'No asignada' }}
                                </div>
                                <div>
                                    <i class="bi bi-pin-map text-danger me-1"></i>
                                    {{ socSeleccionadaDetalle.equipo?.proyectoComunidad?.comunidad?.com_direccion || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.comunidad?.com_sector || 'Dirección en proceso' }}
                                </div>
                            </div>
                        </div>

                        <!-- Convocatoria y Estado de Socialización -->
                        <div class="card border mb-3">
                            <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-calendar-event text-danger me-1"></i> Estado del Acto de Socialización</span>
                                <span v-if="socSeleccionadaDetalle.tipo_socializacion?.tso_orden === 1" class="badge" :class="socSeleccionadaDetalle.soc_apto ? 'bg-success' : (socSeleccionadaDetalle.soc_apto === false ? 'bg-danger' : 'bg-secondary')">
                                    {{ socSeleccionadaDetalle.soc_apto ? 'Apto' : (socSeleccionadaDetalle.soc_apto === false ? 'No Apto' : 'Pendiente') }}
                                </span>
                                <span v-else class="badge" :class="socSeleccionadaDetalle.soc_aprobado ? 'bg-success' : (socSeleccionadaDetalle.soc_aprobado === false ? 'bg-danger' : 'bg-secondary')">
                                    {{ socSeleccionadaDetalle.soc_aprobado ? 'Aprobado' : (socSeleccionadaDetalle.soc_aprobado === false ? 'No Aprobado' : 'Pendiente') }}
                                </span>
                            </div>
                            <div class="card-body p-3 small">
                                <div class="row g-2">
                                    <div class="col-sm-4">
                                        <div class="text-muted">Fecha del Acto:</div>
                                        <div class="fw-bold" :class="socSeleccionadaDetalle.soc_fecha ? 'text-dark' : 'text-danger'">
                                            {{ socSeleccionadaDetalle.soc_fecha || 'Sin fecha programada' }}
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="text-muted">Hora:</div>
                                        <div class="fw-bold text-dark">{{ socSeleccionadaDetalle.soc_hora || 'Por definir' }}</div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="text-muted">Lugar:</div>
                                        <div class="fw-bold text-dark">{{ socSeleccionadaDetalle.soc_lugar || 'Por definir' }}</div>
                                    </div>
                                </div>
                                <div v-if="socSeleccionadaDetalle.soc_observaciones" class="mt-2 p-2 bg-light rounded border">
                                    <strong>Observaciones registradas:</strong> {{ socSeleccionadaDetalle.soc_observaciones }}
                                </div>
                            </div>
                        </div>

                        <!-- Link Temporal de 12 Horas para Jurados y Responsable de la Comunidad -->
                        <div class="card border mb-3">
                            <div class="card-header bg-dark text-white p-2 px-3 d-flex justify-content-between align-items-center">
                                <span class="fw-bold small text-uppercase">
                                    <i class="bi bi-link-45deg text-warning me-1"></i> Link Temporal para Jurado (Vigencia 12 Horas)
                                </span>
                                <span class="badge bg-warning text-dark small">Exclusivo Jurado / Resp. Comunidad</span>
                            </div>
                            <div class="card-body p-3">
                                <!-- Si es pre-socialización -->
                                <div v-if="socSeleccionadaDetalle.tipo_socializacion?.tso_orden === 1" class="text-muted small">
                                    <i class="bi bi-info-circle text-primary me-1"></i>
                                    Este equipo cursa Semestre Impar (Pre-Socialización cualitativa). Los enlaces temporales de 12 horas con rúbricas cuantitativas se generan durante la Socialización Final (Semestre Par).
                                </div>

                                <!-- Si es socialización final pero no tiene fecha programada -->
                                <div v-else-if="!socSeleccionadaDetalle.soc_fecha" class="alert alert-warning py-2 px-3 mb-0 small">
                                    <i class="bi bi-calendar-x-fill me-1"></i>
                                    <strong>Atención:</strong> Para generar y compartir el enlace de evaluación con los jurados, primero debe registrarse la fecha y lugar de la defensa final.
                                </div>

                                <!-- Si es socialización final y tiene fecha -->
                                <div v-else>
                                    <template v-if="tokenJuradoActivo(socSeleccionadaDetalle)">
                                        <label class="form-label fw-bold small text-muted mb-1">Enlace de Acceso Directo:</label>
                                        <div class="input-group input-group-sm mb-2">
                                            <input
                                                type="text"
                                                class="form-control font-monospace bg-light"
                                                readonly
                                                :value="obtenerUrlJurado(socSeleccionadaDetalle)"
                                            />
                                            <button
                                                class="btn fw-bold text-white"
                                                :class="copiadoExitoso ? 'btn-success' : 'btn-danger'"
                                                type="button"
                                                @click="copiarLinkModal(tokenJuradoActivo(socSeleccionadaDetalle))"
                                            >
                                                <i class="bi" :class="copiadoExitoso ? 'bi-check-lg' : 'bi-clipboard'"></i>
                                                {{ copiadoExitoso ? '¡Copiado!' : 'Copiar Link' }}
                                            </button>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <a
                                                :href="obtenerUrlJurado(socSeleccionadaDetalle)"
                                                target="_blank"
                                                class="btn btn-sm btn-dark text-white fw-bold"
                                            >
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Abrir Panel de Jurado
                                            </a>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                @click="generarLinkDesdeModal(socSeleccionadaDetalle.equipo.equ_id)"
                                                :disabled="generandoLinkId === socSeleccionadaDetalle.equipo.equ_id"
                                            >
                                                <span v-if="generandoLinkId === socSeleccionadaDetalle.equipo.equ_id" class="spinner-border spinner-border-sm me-1"></span>
                                                <i v-else class="bi bi-arrow-repeat me-1"></i>Regenerar Link (Nuevas 12h)
                                            </button>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <span class="small text-muted">Aún no se ha emitido un enlace de 12 horas para este proyecto.</span>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger fw-bold"
                                                @click="generarLinkDesdeModal(socSeleccionadaDetalle.equipo.equ_id)"
                                                :disabled="generandoLinkId === socSeleccionadaDetalle.equipo.equ_id"
                                            >
                                                <span v-if="generandoLinkId === socSeleccionadaDetalle.equipo.equ_id" class="spinner-border spinner-border-sm me-1"></span>
                                                <i v-else class="bi bi-magic me-1"></i>Generar Enlace de 12 Horas
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Integrantes del Equipo -->
                        <div class="card border mb-3">
                            <div class="card-header bg-white fw-bold small text-muted text-uppercase">
                                <i class="bi bi-people-fill text-danger me-1"></i> Integrantes del Equipo ({{ socSeleccionadaDetalle.equipo?.integrantes?.length || 0 }})
                            </div>
                            <ul class="list-group list-group-flush small">
                                <li
                                    v-for="integ in socSeleccionadaDetalle.equipo?.integrantes"
                                    :key="integ.ein_id || integ.ein_id_usu"
                                    class="list-group-item d-flex justify-content-between align-items-center py-2"
                                >
                                    <div>
                                        <strong class="text-dark">{{ integ.usuario?.usu_primer_nombre }} {{ integ.usuario?.usu_primer_apellido }}</strong>
                                        <span class="text-muted font-monospace ms-2">C.I. {{ integ.usuario?.usu_cedula }}</span>
                                    </div>
                                    <span v-if="integ.ein_es_lider" class="badge bg-danger rounded-pill">Líder</span>
                                    <span v-else class="badge bg-light text-muted border rounded-pill">Integrante</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Accesos y Descargas Oficiales -->
                        <div class="card border bg-light">
                            <div class="card-header bg-white fw-bold small text-dark">
                                <i class="bi bi-download text-danger me-1"></i> Navegación Directa y Descargas Oficiales UPTP
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-2">
                                    <!-- Expediente -->
                                    <div class="col-sm-6">
                                        <a
                                            :href="'/coordinador/equipos/' + socSeleccionadaDetalle.equipo?.equ_id + '/expediente'"
                                            class="btn btn-outline-dark btn-sm w-100 fw-bold text-start d-flex align-items-center justify-content-between p-2"
                                        >
                                            <span><i class="bi bi-file-earmark-person-fill text-danger me-1"></i>Expediente de Calificaciones</span>
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </div>

                                    <!-- 4 Actores Rúbricas -->
                                    <div class="col-sm-6" v-if="socSeleccionadaDetalle.equipo?.proyectoComunidad?.pco_id || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.pco_id">
                                        <a
                                            :href="'/evaluaciones/recorrido/' + (socSeleccionadaDetalle.equipo?.proyectoComunidad?.pco_id || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.pco_id)"
                                            class="btn btn-outline-danger btn-sm w-100 fw-bold text-start d-flex align-items-center justify-content-between p-2"
                                        >
                                            <span><i class="bi bi-award-fill me-1"></i>Evaluación 4 Actores (Rúbricas)</span>
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </div>

                                    <!-- Acta Final PDF -->
                                    <div class="col-sm-4" v-if="socSeleccionadaDetalle.equipo?.proyectoComunidad?.pco_id || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.pco_id">
                                        <a
                                            :href="'/evaluaciones/pdf/' + (socSeleccionadaDetalle.equipo?.proyectoComunidad?.pco_id || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.pco_id)"
                                            target="_blank"
                                            class="btn btn-danger btn-sm w-100 fw-bold p-2 text-truncate"
                                            title="Descargar Acta Final Oficial PDF con Hash SHA-256"
                                        >
                                            <i class="bi bi-file-earmark-pdf-fill me-1"></i>Acta Final PDF
                                        </a>
                                    </div>

                                    <!-- Planilla Vinculación Excel -->
                                    <div class="col-sm-6" v-if="socSeleccionadaDetalle.equipo?.proyectoComunidad?.pco_id || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.pco_id">
                                        <a
                                            :href="'/excel/vinculacion/' + (socSeleccionadaDetalle.equipo?.proyectoComunidad?.pco_id || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.pco_id)"
                                            target="_blank"
                                            class="btn btn-success btn-sm w-100 fw-bold p-2 text-truncate"
                                            title="Descargar Planilla de Vinculación Excel"
                                        >
                                            <i class="bi bi-file-earmark-excel-fill me-1"></i>Planilla Vinc. Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light p-3 border-top">
                        <button type="button" class="btn btn-secondary fw-bold rounded-pill px-4" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import SidebarCoordinador from "@/Components/SidebarCoordinador.vue";
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import { Modal } from "bootstrap";
const menuMaestras = ref(false);
const menuAcademico = ref(false);
const menuSistema = ref(false);
const props = defineProps({ socializaciones: Array });
const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("coord-theme") || "light");
const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("coord-theme", tema.value);
};
const logout = () => router.post("/logout");
const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});
const moduloActivo = ref("pre_socializacion"); // "pre_socializacion" | "socializacion_final"

const totalPreSoc = computed(() =>
    (props.socializaciones || []).filter(
        (s) => s.tipo_socializacion?.tso_orden === 1 || s.soc_id_tso == 1
    ).length
);

const totalSocFinal = computed(() =>
    (props.socializaciones || []).filter(
        (s) => s.tipo_socializacion?.tso_orden === 2 || s.soc_id_tso == 2
    ).length
);

const busqueda = ref("");
const filtradas = computed(() => {
    let list = props.socializaciones || [];
    if (moduloActivo.value === "pre_socializacion") {
        list = list.filter((s) => s.tipo_socializacion?.tso_orden === 1 || s.soc_id_tso == 1);
    } else {
        list = list.filter((s) => s.tipo_socializacion?.tso_orden === 2 || s.soc_id_tso == 2);
    }
    if (!busqueda.value.trim()) return list;
    const q = busqueda.value.toLowerCase();
    return list.filter(
        (s) =>
            s.equipo?.equ_codigo?.toLowerCase().includes(q) ||
            s.tipo_socializacion?.tso_nombre?.toLowerCase().includes(q) ||
            s.equipo?.equ_titulo?.toLowerCase().includes(q),
    );
});

let mDetalle = null;
const socSeleccionadaDetalle = ref(null);
const copiadoExitoso = ref(false);
const generandoLinkId = ref(null);

const abrirDetalleEquipo = (soc) => {
    socSeleccionadaDetalle.value = soc;
    mDetalle = mDetalle || new Modal(document.getElementById("mDetalleEquipoSoc"));
    mDetalle.show();
};

const tokenJuradoActivo = (soc) => {
    return (
        soc?.equipo?.proyectoComunidad?.token_jurado ||
        soc?.equipo?.proyecto_comunidad?.token_jurado ||
        null
    );
};

const obtenerUrlJurado = (soc) => {
    const token = tokenJuradoActivo(soc);
    if (!token) return "";
    return window.location.origin + "/evaluacion-jurado/" + token;
};

const copiarLinkModal = (token) => {
    if (!token) return;
    const url = window.location.origin + "/evaluacion-jurado/" + token;
    navigator.clipboard.writeText(url);
    copiadoExitoso.value = true;
    setTimeout(() => {
        copiadoExitoso.value = false;
    }, 3000);
};

const generarLinkDesdeModal = (equId) => {
    generandoLinkId.value = equId;
    router.post(
        `/coordinador/equipos/${equId}/generar-link-jurado`,
        {},
        {
            preserveScroll: true,
            onSuccess: (page) => {
                if (
                    page.props.flash?.link_jurado &&
                    socSeleccionadaDetalle.value?.equipo
                ) {
                    const parts = page.props.flash.link_jurado.split("/");
                    const tok = parts[parts.length - 1];
                    const p =
                        socSeleccionadaDetalle.value.equipo.proyectoComunidad ||
                        socSeleccionadaDetalle.value.equipo.proyecto_comunidad;
                    if (p) {
                        p.token_jurado = tok;
                    }
                }
            },
            onFinish: () => {
                generandoLinkId.value = null;
            },
        },
    );
};
</script>
<style scoped>
:root {
    --rojo: #dc3545;
    --bg-body: #f0f2f5;
    --bg-card: #fff;
    --bg-sidebar: #1a1d23;
    --bg-topbar: #fff;
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
.sb {
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
.sb-av {
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
.sb-lnk {
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
.sb-lnk:hover {
    background: rgba(255, 255, 255, 0.06);
    color: #fff;
}
.sb-lnk.active {
    background: rgba(220, 53, 69, 0.18);
    color: #fff;
    border-left-color: #dc3545;
    font-weight: 700;
}
.sb-lnk i {
    font-size: 1rem;
    width: 20px;
    text-align: center;
}
.main {
    transition: margin-left 0.3s;
}
.topbar {
    background: var(--bg-topbar);
    padding: 0.85rem 1.4rem;
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
.tb-title {
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
.content {
    padding: 1.4rem 1.25rem;
}
.sc {
    background: var(--bg-card);
    border-radius: 13px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--border);
    overflow: hidden;
    transition:
        background 0.3s,
        border-color 0.3s;
    margin-bottom: 1.2rem;
}
.ct {
    width: 100%;
    border-collapse: collapse;
}
.ct thead th {
    padding: 0.7rem 1rem;
    background: var(--bg-th);
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid var(--border);
}
.ct tbody td {
    padding: 0.7rem 1rem;
    border-bottom: 1px solid var(--border);
    color: var(--text-main);
    font-size: 0.9rem;
}
.ct tbody tr:hover td {
    background: var(--bg-hover);
}
.bs {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.76rem;
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
.bp {
    background: #cff4fc;
    color: #055160;
    border: 1px solid #b6effb;
}
.bw {
    background: #fff3cd;
    color: #664d03;
    border: 1px solid #ffecb5;
}
.bsec {
    background: #f8f9fa;
    color: #212529;
    border: 1px solid #dee2e6;
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
[data-theme="dark"] .bp {
    background: rgba(13, 110, 253, 0.25);
    color: #9ec5fe;
    border-color: rgba(13, 110, 253, 0.4);
}
[data-theme="dark"] .bw {
    background: rgba(255, 193, 7, 0.25);
    color: #ffe69c;
    border-color: rgba(255, 193, 7, 0.4);
}
[data-theme="dark"] .bsec {
    background: #252830;
    color: #f1f3f5;
    border-color: #2d3139;
}
.srch {
    position: relative;
    max-width: 260px;
}
.srch input {
    padding: 0.4rem 0.9rem 0.4rem 2rem;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--bg-card);
    color: var(--text-main);
    font-size: 0.88rem;
    width: 100%;
}
.srch input:focus {
    outline: none;
    border-color: #dc3545;
}
.srch i {
    position: absolute;
    left: 0.6rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.85rem;
}
.sb-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1035;
    display: none;
}
@media (min-width: 992px) {
    .sb {
        transform: translateX(0) !important;
    }
    .main {
        margin-left: 250px;
    }
    .toggle-btn {
        display: none !important;
    }
}
@media (max-width: 991.98px) {
    .sb {
        transform: translateX(-100%);
    }
    .main {
        margin-left: 0;
    }
    .sb.show {
        transform: translateX(0);
    }
    .sb-overlay.show {
        display: block !important;
    }
    .content {
        padding: 1rem;
    }
}
</style>
