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








        <!-- MAIN -->
        <div class="main">
            <!-- TOPBAR -->
            <div class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <div class="tb-title">
                        <i class="bi bi-arrow-left-right me-2 text-danger"></i>
                        Gestión Académica de Secciones: Transición Semestral y Cierre
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button
                        class="btn btn-dark btn-sm fw-bold rounded-pill px-3 shadow-sm border border-secondary"
                        @click="sincronizarSogac"
                        :disabled="sincronizando"
                    >
                        <span v-if="sincronizando" class="spinner-border spinner-border-sm me-1"></span>
                        <i v-else class="bi bi-arrow-repeat me-1 text-danger"></i>
                        Sincronizar Transición SOGAC
                    </button>
                    <button class="tb-btn" @click="toggleTema" title="Cambiar tema">
                        <i
                            class="bi"
                            :class="tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'"
                        ></i>
                    </button>
                </div>
            </div>

            <!-- CONTENIDO PRINCIPAL -->
            <div class="content p-3 p-md-4">
                <!-- Banner de Explicación Institucional UPTP -->
                <div class="card border-0 shadow-sm mb-4" style="background: #1a1d23; color: #ffffff; border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-3 flex-wrap">
                            <div class="p-3 bg-danger rounded-circle text-white fs-3 d-none d-sm-block">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <div style="flex: 1; min-width: 280px;">
                                <span class="badge bg-danger px-3 py-1 mb-2 text-uppercase fw-bold">
                                    Control Académico Institucional UPTP
                                </span>
                                <h4 class="fw-bold mb-1 text-white">
                                    Gestión Diferenciada: Semestre Impar vs. Semestre Par
                                </h4>
                                <p class="text-white-50 mb-0" style="font-size: 1rem; line-height: 1.6;">
                                    En un mismo lapso conviven secciones que <strong>inician proyecto</strong> en Semestre Impar (131, 331, 531, 731) y secciones que <strong>cierran proyecto</strong> en Semestre Par (231, 431, 631, 831).
                                    Utilice esta vista para promover equipos al semestre cuantitativo o realizar el <strong>cierre definitivo de trayecto</strong> y envío con acta firmada al Repositorio Institucional (nota mínima aprobatoria: 16.00 pts).
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SELECTOR DE PESTAÑAS PRINCIPALES: IMPAR VS PAR -->
                <div class="d-flex gap-2 mb-4 border-bottom pb-2">
                    <button
                        type="button"
                        class="btn fw-bold px-4 py-2 rounded-pill fs-6 d-flex align-items-center gap-2"
                        :class="tabActiva === 'impar' ? 'btn-danger text-white shadow-sm' : 'btn-outline-dark bg-white'"
                        @click="tabActiva = 'impar'"
                        style="min-height: 48px;"
                    >
                        <i class="bi bi-rocket-takeoff-fill"></i>
                        <span>1. Semestre Impar: Transición a Par ({{ seccionesImpares.length }} Secciones)</span>
                    </button>
                    <button
                        type="button"
                        class="btn fw-bold px-4 py-2 rounded-pill fs-6 d-flex align-items-center gap-2"
                        :class="tabActiva === 'par' ? 'btn-danger text-white shadow-sm' : 'btn-outline-dark bg-white'"
                        @click="tabActiva = 'par'"
                        style="min-height: 48px;"
                    >
                        <i class="bi bi-cloud-arrow-up-fill"></i>
                        <span>2. Semestre Par: Cierre y Repositorio ({{ seccionesPares.length }} Secciones)</span>
                    </button>
                </div>

                <!-- ════════════════════════════════════════════════════════════ -->
                <!-- PESTAÑA 1: TRANSICIÓN SEMESTRAL (SEMESTRE IMPAR A PAR) -->
                <!-- ════════════════════════════════════════════════════════════ -->
                <div v-if="tabActiva === 'impar'">
                    <!-- Filtros y Búsqueda -->
                    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div class="input-group" style="min-width: 260px;">
                                <span class="input-group-text bg-white border"><i class="bi bi-search"></i></span>
                                <input
                                    v-model="filtroTexto"
                                    type="text"
                                    class="form-control"
                                    placeholder="Buscar por código de sección, equipo o comunidad..."
                                />
                            </div>
                            <div style="min-width: 240px;">
                                <SelectBuscable
                                    v-model="filtroSeccion"
                                    :options="opcionesSeccionesImpares"
                                    placeholder="Todas las Secciones Impares"
                                />
                            </div>
                        </div>
                        <div class="text-muted fw-bold small">
                            Secciones impares activas: <strong>{{ seccionesFiltradas.length }}</strong>
                        </div>
                    </div>

                    <!-- Lista de Secciones Impares -->
                    <div v-if="!seccionesFiltradas.length" class="card p-5 text-center shadow-sm border-0 my-4">
                        <i class="bi bi-check-circle-fill text-success fs-1 mb-2"></i>
                        <h5 class="fw-bold">No se encontraron secciones impares pendientes de transición</h5>
                        <p class="text-muted mb-0">
                            Todas las secciones impares actuales se encuentran promovidas a semestres pares o no coinciden con la búsqueda.
                        </p>
                    </div>

                    <div v-for="sec in seccionesFiltradas" :key="sec.sec_id" class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
                        <!-- Cabecera de la Sección -->
                        <div class="card-header bg-white p-3 p-md-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="badge bg-danger fs-6 px-3 py-2 rounded-pill fw-bold">
                                        Sección Impar: {{ sec.sec_codigo }}
                                    </span>
                                    <i class="bi bi-arrow-right fs-5 text-muted"></i>
                                    <span class="badge bg-dark fs-6 px-3 py-2 rounded-pill fw-bold">
                                        Sección Par Destino: {{ sec.sec_codigo_destino }}
                                    </span>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        {{ sec.trayecto }} &bull; Turno {{ sec.turno }}
                                    </span>
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-person-badge text-danger me-1"></i> Docente de Proyecto: <strong>{{ sec.profesor }}</strong>
                                    &bull; Total Equipos: <strong>{{ sec.total_equipos }}</strong>
                                </div>
                            </div>

                            <!-- Botón para promover toda la sección en bloque -->
                            <div>
                                <button
                                    v-if="sec.total_equipos > 0"
                                    class="btn btn-danger fw-bold px-4 rounded-pill"
                                    style="min-height: 48px;"
                                    @click="confirmarPromoverSeccion(sec)"
                                >
                                    <i class="bi bi-collection-play-fill me-2"></i>
                                    Promover Toda la Sección a {{ sec.sec_codigo_destino }}
                                </button>
                            </div>
                        </div>

                        <!-- Tabla de Equipos de la Sección -->
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small text-uppercase">
                                        <tr>
                                            <th class="ps-4">Código / Proyecto</th>
                                            <th>Comunidad Beneficiada</th>
                                            <th>Integrantes</th>
                                            <th class="text-center">Pre-Socialización</th>
                                            <th class="text-center">Puntos Control</th>
                                            <th class="text-end pe-4">Acción de Promoción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!sec.equipos?.length">
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                No hay equipos registrados en esta sección.
                                            </td>
                                        </tr>
                                        <tr v-for="eq in sec.equipos" :key="eq.equ_id">
                                            <td class="ps-4" style="max-width: 280px;">
                                                <div class="fw-bold text-danger">{{ eq.equ_codigo }}</div>
                                                <div class="fw-semibold text-dark text-truncate" :title="eq.equ_titulo">
                                                    {{ eq.equ_titulo || 'Sin título registrado' }}
                                                </div>
                                                <small v-if="eq.lider" class="text-muted d-block">
                                                    Líder: {{ eq.lider }}
                                                </small>
                                            </td>
                                            <td>
                                                <span class="small text-dark fw-medium">{{ eq.comunidad_nombre }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-2 py-1">
                                                    <i class="bi bi-people me-1"></i>{{ eq.integrantes_count }} estudiantes
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span v-if="eq.pre_soc_apta" class="badge bg-success rounded-pill px-3 py-1">
                                                    ✓ Apto en Pre-Soc
                                                </span>
                                                <span v-else-if="eq.pre_soc_evaluada" class="badge bg-danger rounded-pill px-3 py-1">
                                                    No Apto
                                                </span>
                                                <span v-else class="badge bg-warning text-dark rounded-pill px-3 py-1">
                                                    Pre-Soc Pendiente
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-light text-dark border">
                                                    {{ eq.puntos_cumplidos }} cumplidos
                                                </span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <button
                                                    class="btn btn-outline-danger fw-bold rounded-pill px-3"
                                                    style="min-height: 44px;"
                                                    @click="confirmarPromoverEquipo(eq, sec)"
                                                >
                                                    <i class="bi bi-arrow-right-circle me-1"></i>
                                                    Promover a {{ eq.seccion_destino }}
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ════════════════════════════════════════════════════════════ -->
                <!-- PESTAÑA 2: CIERRE DEFINITIVO Y REPOSITORIO (SEMESTRE PAR) -->
                <!-- ════════════════════════════════════════════════════════════ -->
                <div v-else-if="tabActiva === 'par'">
                    <div class="alert alert-dark p-3 rounded-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <i class="bi bi-info-circle-fill text-danger me-2 fs-5"></i>
                            <strong>Reglamento de Cierre de Trayecto:</strong> Los proyectos que hayan culminado la Socialización Final con calificación definitiva mayor o igual a <strong>16.00 puntos</strong> pueden ser archivados y enviados al Repositorio Institucional con acta digital y hash criptográfico SHA-256.
                        </div>
                    </div>

                    <div v-if="!seccionesPares.length" class="card p-5 text-center shadow-sm border-0 my-4">
                        <i class="bi bi-inbox text-muted fs-1 mb-2"></i>
                        <h5 class="fw-bold">No hay secciones registradas en el semestre par</h5>
                        <p class="text-muted mb-0">
                            Las secciones aparecerán aquí cuando sean promovidas desde el semestre impar o registradas con código par (231, 431, 631, 831).
                        </p>
                    </div>

                    <div v-for="sec in seccionesPares" :key="sec.sec_id" class="card shadow-sm border-0 mb-4" style="border-radius: 12px; overflow: hidden;">
                        <div class="card-header bg-white p-3 p-md-4 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                    <span class="badge bg-dark fs-6 px-3 py-2 rounded-pill fw-bold text-white">
                                        Sección Par: {{ sec.sec_codigo }}
                                    </span>
                                    <span class="badge bg-light text-dark border px-2 py-1">
                                        {{ sec.trayecto }} &bull; Turno {{ sec.turno }}
                                    </span>
                                    <span class="badge bg-success px-2 py-1">
                                        Semestre Par (Cierre de Trayecto)
                                    </span>
                                </div>
                                <div class="small text-muted">
                                    <i class="bi bi-person-badge text-danger me-1"></i> Docente de Proyecto: <strong>{{ sec.profesor }}</strong>
                                    &bull; Total Equipos: <strong>{{ sec.total_equipos }}</strong>
                                </div>
                            </div>

                            <div>
                                <button
                                    v-if="sec.total_equipos > 0"
                                    class="btn btn-dark text-white fw-bold px-4 rounded-pill"
                                    style="min-height: 48px;"
                                    @click="confirmarCierreSeccion(sec)"
                                >
                                    <i class="bi bi-archive-fill text-danger me-2"></i>
                                    Cerrar y Archivar Toda la Sección en Repositorio
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small text-uppercase">
                                        <tr>
                                            <th class="ps-4">Código / Proyecto</th>
                                            <th>Comunidad Beneficiada</th>
                                            <th>Integrantes</th>
                                            <th class="text-center">Acta Final (4 Actores)</th>
                                            <th class="text-center">Calificación Definitiva</th>
                                            <th class="text-end pe-4">Repositorio Institucional</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-if="!sec.equipos?.length">
                                            <td colspan="6" class="text-center py-4 text-muted">
                                                No hay equipos registrados en esta sección.
                                            </td>
                                        </tr>
                                        <tr v-for="eq in sec.equipos" :key="eq.equ_id">
                                            <td class="ps-4" style="max-width: 280px;">
                                                <div class="fw-bold text-dark">{{ eq.equ_codigo }}</div>
                                                <div class="small text-muted text-truncate" :title="eq.equ_titulo">
                                                    {{ eq.equ_titulo || 'Sin título registrado' }}
                                                </div>
                                            </td>
                                            <td>
                                                <span class="small text-dark fw-medium">{{ eq.comunidad_nombre }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-light text-dark border px-2 py-1">
                                                    {{ eq.integrantes_count }} estudiantes
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <span v-if="eq.acta_cerrada" class="badge bg-success rounded-pill px-3 py-1">
                                                    ✓ Acta Cerrada
                                                </span>
                                                <span v-else class="badge bg-secondary rounded-pill px-3 py-1">
                                                    Pendiente de Cierre
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                <div v-if="eq.calificacion !== null">
                                                    <span
                                                        class="fw-bold fs-6"
                                                        :class="eq.calificacion >= 16 ? 'text-success' : 'text-danger'"
                                                    >
                                                        {{ eq.calificacion.toFixed(2) }} / 20 pts
                                                    </span>
                                                    <small class="d-block text-muted" style="font-size: 0.75rem;">
                                                        {{ eq.mencion || (eq.calificacion >= 16 ? 'Aprobado' : 'Reprobado') }}
                                                    </small>
                                                </div>
                                                <span v-else class="text-muted small">Sin calificar</span>
                                            </td>
                                            <td class="text-end pe-4">
                                                <div v-if="eq.en_repositorio" class="d-inline-flex flex-column align-items-end">
                                                    <span class="badge bg-success text-white px-3 py-2 rounded-pill fw-bold">
                                                        <i class="bi bi-check-circle-fill me-1"></i> Archivado en Repositorio
                                                    </span>
                                                    <small class="font-monospace text-muted mt-1" style="font-size: 0.7rem;" v-if="eq.hash_repositorio || eq.hash_verificacion">
                                                        SHA-256: {{ (eq.hash_repositorio || eq.hash_verificacion).substring(0, 16) }}...
                                                    </small>
                                                </div>
                                                <button
                                                    v-else-if="eq.aprobado_para_cierre"
                                                    class="btn btn-outline-dark fw-bold rounded-pill px-3"
                                                    style="min-height: 44px;"
                                                    @click="confirmarCierreEquipo(eq, sec)"
                                                >
                                                    <i class="bi bi-cloud-arrow-up-fill text-danger me-1"></i>
                                                    Cerrar y Enviar a Repositorio
                                                </button>
                                                <span v-else class="badge bg-light text-muted border px-2 py-1">
                                                    Requiere Acta &ge; 16.00 pts
                                                </span>
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

        <!-- Modal de Confirmación -->
        <ModalConfirmar
            ref="modalConfirmacion"
            :titulo="modalTitulo"
            :mensaje="modalMensaje"
            :icono="modalIcono"
            :textoConfirmar="modalBotonConfirmar"
            textoCancelar="Cancelar"
            colorConfirmar="#dc3545"
            @confirmar="ejecutarAccionModal"
        />

        <!-- Modal Cerrar Sesión -->
        <ModalConfirmar
            ref="modalLogout"
            titulo="Cerrar Sesión"
            mensaje="¿Estás seguro de que deseas salir del sistema?"
            icono="🚪"
            textoConfirmar="Sí, cerrar sesión"
            textoCancelar="Cancelar"
            colorConfirmar="#dc3545"
            @confirmar="router.post('/logout')"
        />
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import ModalConfirmar from "@/Components/ModalConfirmar.vue";
import SidebarCoordinador from "@/Components/SidebarCoordinador.vue";
import SelectBuscable from "@/Components/SelectBuscable.vue";

const props = defineProps({
    seccionesImpares: { type: Array, default: () => [] },
    seccionesPares:   { type: Array, default: () => [] },
});

const opcionesSeccionesImpares = computed(() => [
    { value: "", label: "Todas las Secciones Impares" },
    ...(props.seccionesImpares || []).map((sec) => ({
        value: sec.sec_id,
        label: `Sección ${sec.sec_codigo} (${sec.total_equipos} equipos)`,
    })),
]);

const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("coord-theme") || "light");
const menuMaestras = ref(false);
const menuSistema = ref(false);

const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("coord-theme", tema.value);
};

const logout = () => modalLogout.value?.abrir();
const modalLogout = ref(null);

const iniciales = computed(() => {
    const u = page.props.auth?.user;
    return (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "");
});

// Filtros
const filtroTexto = ref("");
const filtroSeccion = ref("");

const seccionesFiltradas = computed(() => {
    return props.seccionesImpares.filter((sec) => {
        if (filtroSeccion.value && sec.sec_id !== filtroSeccion.value) {
            return false;
        }
        if (!filtroTexto.value.trim()) return true;

        const q = filtroTexto.value.toLowerCase();
        const coincideSec = String(sec.sec_codigo).toLowerCase().includes(q);
        const coincideEquipos = sec.equipos.some(
            (e) =>
                String(e.equ_codigo).toLowerCase().includes(q) ||
                String(e.equ_titulo || "").toLowerCase().includes(q) ||
                String(e.comunidad_nombre || "").toLowerCase().includes(q)
        );
        return coincideSec || coincideEquipos;
    });
});

// Pestaña Activa
const tabActiva = ref(new URLSearchParams(window.location.search).get("tab") === "par" ? "par" : "impar");

// Modal de Confirmación
const modalConfirmacion = ref(null);
const modalTitulo = ref("");
const modalMensaje = ref("");
const modalIcono = ref("🚀");
const modalBotonConfirmar = ref("Confirmar");
const elementoSeleccionado = ref(null);
const accionModal = ref(""); // 'promover_equipo', 'promover_seccion', 'cierre_equipo', 'cierre_seccion'

const confirmarPromoverEquipo = (eq, sec) => {
    accionModal.value = "promover_equipo";
    elementoSeleccionado.value = eq;
    modalIcono.value = "🚀";
    modalBotonConfirmar.value = "Sí, Promover a Semestre Par";
    modalTitulo.value = `Promover Equipo ${eq.equ_codigo} a Semestre Par`;
    modalMensaje.value = `¿Confirmas promover el equipo "${eq.equ_codigo}" de la sección impar ${sec.sec_codigo} a la sección par ${eq.seccion_destino}? Se congelará su historial del Semestre 1 y se habilitará el Semestre 2 conservando todas las notas acumuladas.`;
    modalConfirmacion.value?.abrir();
};

const confirmarPromoverSeccion = (sec) => {
    accionModal.value = "promover_seccion";
    elementoSeleccionado.value = sec;
    modalIcono.value = "🚀";
    modalBotonConfirmar.value = `Sí, Promover Toda la Sección a ${sec.sec_codigo_destino}`;
    modalTitulo.value = `Promover Toda la Sección ${sec.sec_codigo} a ${sec.sec_codigo_destino}`;
    modalMensaje.value = `¿Confirmas promover la totalidad de los ${sec.total_equipos} equipos de la sección impar ${sec.sec_codigo} a la nueva sección par ${sec.sec_codigo_destino}?`;
    modalConfirmacion.value?.abrir();
};

const confirmarCierreEquipo = (eq, sec) => {
    accionModal.value = "cierre_equipo";
    elementoSeleccionado.value = eq;
    modalIcono.value = "📦";
    modalBotonConfirmar.value = "Sí, Cerrar y Enviar al Repositorio";
    modalTitulo.value = `Cierre de Trayecto y Envío a Repositorio — Equipo ${eq.equ_codigo}`;
    modalMensaje.value = `¿Confirmas cerrar definitivamente el proyecto del equipo "${eq.equ_codigo}" (Calificación: ${eq.calificacion?.toFixed(2)} pts) y archivarlo en el Repositorio Institucional con Hash SHA-256?`;
    modalConfirmacion.value?.abrir();
};

const confirmarCierreSeccion = (sec) => {
    accionModal.value = "cierre_seccion";
    elementoSeleccionado.value = sec;
    modalIcono.value = "📦";
    modalBotonConfirmar.value = "Sí, Archivar Sección en Repositorio";
    modalTitulo.value = `Cierre y Envío al Repositorio — Sección Par ${sec.sec_codigo}`;
    modalMensaje.value = `¿Confirmas realizar el cierre definitivo y archivar en el Repositorio Institucional todos los proyectos aprobados de la sección ${sec.sec_codigo}?`;
    modalConfirmacion.value?.abrir();
};

const ejecutarAccionModal = () => {
    if (accionModal.value === "promover_equipo") {
        router.post(`/coordinador/transicion-semestral/equipo/${elementoSeleccionado.value.equ_id}`);
    } else if (accionModal.value === "promover_seccion") {
        router.post(`/coordinador/transicion-semestral/seccion/${elementoSeleccionado.value.sec_id}`);
    } else if (accionModal.value === "cierre_equipo") {
        router.post(`/coordinador/transicion-semestral/equipo/${elementoSeleccionado.value.equ_id}/repositorio`);
    } else if (accionModal.value === "cierre_seccion") {
        router.post(`/coordinador/transicion-semestral/seccion/${elementoSeleccionado.value.sec_id}/repositorio`);
    }
};

const sincronizando = ref(false);
const sincronizarSogac = () => {
    if (!confirm("¿Deseas ejecutar la sincronización automática de transiciones semestrales con SOGAC? Las secciones impares se emparejarán automáticamente con las pares y los equipos aptos avanzarán preservando su expediente.")) {
        return;
    }
    sincronizando.value = true;
    router.post("/coordinador/transicion-semestral/sincronizar-sogac", {}, {
        preserveScroll: true,
        onFinish: () => {
            sincronizando.value = false;
        }
    });
};
</script>

<style scoped>
:root {
    --rojo: #dc3545;
    --bg-body: #f8f9fa;
    --bg-card: #ffffff;
    --bg-sidebar: #1a1d23;
    --bg-topbar: #ffffff;
    --border: #dee2e6;
    --text-main: #111111;
    --text-muted: #555555;
}
[data-theme="dark"] {
    --bg-body: #111317;
    --bg-card: #1e2128;
    --bg-sidebar: #0d0e11;
    --bg-topbar: #1e2128;
    --border: #2d323f;
    --text-main: #f0f2f5;
    --text-muted: #a0a6b5;
}
.sb {
    width: 260px;
    background: var(--bg-sidebar);
    color: #fff;
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    z-index: 1050;
    overflow-y: auto;
    transition: transform 0.3s ease;
}
.sb-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1040;
    display: none;
}
.sb-overlay.show {
    display: block;
}
@media (max-width: 991px) {
    .sb {
        transform: translateX(-100%);
    }
    .sb.show {
        transform: translateX(0);
    }
}
.sb-brand {
    padding: 1.25rem 1.4rem;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.sb-title {
    font-size: 1rem;
    font-weight: 800;
}
.sb-sub {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.5);
}
.sb-user {
    padding: 1rem 1.4rem;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
}
.sb-av {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #dc3545;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
}
.sb-name {
    font-size: 0.95rem;
    font-weight: 700;
}
.sb-role {
    font-size: 0.8rem;
    color: rgba(255, 255, 255, 0.5);
}
.sb-sec {
    padding: 0.75rem 1.4rem 0.25rem;
    font-size: 0.75rem;
    text-transform: uppercase;
    color: rgba(255, 255, 255, 0.4);
    font-weight: 700;
}
.sb-lnk {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0.7rem 1.4rem;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-size: 0.95rem;
    font-weight: 600;
    transition: 0.2s;
}
.sb-lnk:hover,
.sb-lnk.active {
    background: rgba(220, 53, 69, 0.15);
    color: #fff;
    border-left: 4px solid #dc3545;
}
.main {
    margin-left: 260px;
    min-height: 100vh;
}
@media (max-width: 991px) {
    .main {
        margin-left: 0;
    }
}
.topbar {
    height: 64px;
    background: var(--bg-topbar);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
}
.tb-title {
    font-size: 1.15rem;
    font-weight: 800;
}
.tb-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: 1px solid var(--border);
    background: none;
    color: var(--text-main);
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
