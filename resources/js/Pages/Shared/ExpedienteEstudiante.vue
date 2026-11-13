<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
        "
    >
        <!-- TOPBAR -->
        <div class="topbar-dossier">
            <div class="container-fluid d-flex align-items-center justify-content-between px-3 px-md-4 py-2">
                <div class="d-flex align-items-center gap-2">
                    <a :href="backUrl" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="bi bi-arrow-left me-1"></i> Volver
                    </a>
                    <h5 class="m-0 fw-bold d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge-fill text-danger"></i>
                        <span>Expediente del Estudiante</span>
                    </h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-sm btn-outline-secondary rounded-circle" @click="toggleTema" title="Cambiar tema">
                        <i class="bi" :class="tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="container-fluid px-3 px-md-4 py-3">
            <!-- BUSCADOR RÁPIDO SUPERIOR -->
            <div class="card border-0 shadow-sm mb-3" style="background: var(--bg-card); border-radius: 14px;">
                <div class="card-body p-3">
                    <div class="row align-items-center g-2">
                        <div class="col-md-7">
                            <div class="input-group">
                                <span class="input-group-text border-end-0" style="background: var(--bg-card); color: var(--text-muted);">
                                    <i class="bi bi-search"></i>
                                </span>
                                <input
                                    v-model="busquedaInput"
                                    type="text"
                                    class="form-control border-start-0"
                                    style="background: var(--bg-card); color: var(--text-main);"
                                    placeholder="Buscar otro estudiante por Cédula o Nombre..."
                                    @input="buscarEstudiantes"
                                />
                            </div>
                        </div>
                        <div class="col-md-5 text-md-end text-muted small">
                            <i class="bi bi-shield-check text-success me-1"></i> Registro oficial con fines académicos y auditoría docente
                        </div>
                    </div>

                    <!-- Resultados de búsqueda rápida -->
                    <div v-if="resultadosBusqueda.length > 0" class="list-group mt-2 border rounded shadow-sm">
                        <a
                            v-for="est in resultadosBusqueda"
                            :key="est.usu_id"
                            :href="`${esCoordinador ? '/coordinador' : '/profesor'}/estudiantes/${est.usu_id}/expediente`"
                            class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-2"
                            style="background: var(--bg-card); color: var(--text-main);"
                        >
                            <div>
                                <strong>{{ est.usu_primer_nombre }} {{ est.usu_primer_apellido }}</strong>
                                <span class="badge bg-light text-dark border ms-2">C.I. {{ est.usu_cedula }}</span>
                            </div>
                            <span class="btn btn-sm btn-outline-danger py-0 px-2">Ver expediente</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- FICHA PRINCIPAL DEL ESTUDIANTE -->
            <div class="card border-0 shadow-sm mb-4" style="background: var(--bg-card); border-radius: 16px;">
                <div class="card-body p-3 p-md-4">
                    <div class="row align-items-center g-3">
                        <div class="col-auto">
                            <div
                                style="
                                    width: 72px;
                                    height: 72px;
                                    border-radius: 50%;
                                    background: linear-gradient(135deg, #dc3545, #b02a37);
                                    color: #fff;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    font-size: 1.8rem;
                                    font-weight: 800;
                                "
                            >
                                {{ iniciales }}
                            </div>
                        </div>
                        <div class="col">
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <h3 class="m-0 fw-bold" style="color: var(--text-main);">
                                    {{ estudiante.usu_primer_nombre }} {{ estudiante.usu_primer_apellido }}
                                </h3>
                                <span class="badge bg-danger rounded-pill px-3 py-1">
                                    C.I. {{ estudiante.usu_cedula }}
                                </span>
                            </div>
                            <div class="d-flex align-items-center gap-3 flex-wrap text-muted small mt-2">
                                <div><i class="bi bi-envelope me-1"></i> {{ estudiante.usu_correo || 'Sin correo registrado' }}</div>
                                <div><i class="bi bi-telephone me-1"></i> {{ estudiante.usu_telefono || 'Sin teléfono' }}</div>
                                <div v-if="seccionActual">
                                    <i class="bi bi-collection me-1 text-danger"></i>
                                    Sección <strong>{{ seccionActual.sec_codigo }}</strong> &bull;
                                    Trayecto {{ seccionActual.trayecto?.tra_nombre || 'N/A' }}
                                    (Semestre {{ seccionActual.sec_semestre || 'N/A' }})
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-md-auto text-md-end">
                            <div class="p-2 px-3 rounded text-center border" style="background: rgba(220, 53, 69, 0.05);">
                                <div class="small text-muted fw-bold">EQUIPO ACTUAL</div>
                                <div class="fw-bold text-danger fs-6">
                                    {{ equipoActual ? equipoActual.equ_codigo : 'Sin equipo asignado' }}
                                </div>
                                <div v-if="equipoActual" class="text-truncate small" style="max-width: 220px;">
                                    {{ equipoActual.equ_titulo }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABS DE NAVEGACIÓN DEL EXPEDIENTE -->
            <ul class="nav nav-pills gap-2 mb-3 border-bottom pb-2 flex-nowrap overflow-auto" role="tablist">
                <li class="nav-item">
                    <button
                        class="nav-link rounded-pill fw-bold text-nowrap px-3 py-2"
                        :class="{ active: tabActiva === 'equipos' }"
                        @click="tabActiva = 'equipos'"
                    >
                        <i class="bi bi-people me-1"></i> Equipos e Historial de Cambios ({{ historialEventos.length }})
                    </button>
                </li>
                <li class="nav-item">
                    <button
                        class="nav-link rounded-pill fw-bold text-nowrap px-3 py-2"
                        :class="{ active: tabActiva === 'puntos' }"
                        @click="tabActiva = 'puntos'"
                    >
                        <i class="bi bi-check2-circle me-1"></i> Puntos de Control y Asistencia ({{ asistenciasPuntos.length }})
                    </button>
                </li>
                <li class="nav-item">
                    <button
                        class="nav-link rounded-pill fw-bold text-nowrap px-3 py-2"
                        :class="{ active: tabActiva === 'socializaciones' }"
                        @click="tabActiva = 'socializaciones'"
                    >
                        <i class="bi bi-mic me-1"></i> Socializaciones y Avance ({{ socializaciones.length }})
                    </button>
                </li>
            </ul>

            <!-- CONTENIDO TAB 1: EQUIPOS E HISTORIAL DE CAMBIOS Y EVIDENCIAS -->
            <div v-show="tabActiva === 'equipos'">
                <div class="card border-0 shadow-sm" style="background: var(--bg-card); border-radius: 14px;">
                    <div class="card-header bg-transparent border-0 pt-3 px-3 px-md-4">
                        <h6 class="fw-bold m-0 text-danger">
                            <i class="bi bi-clock-history me-1"></i> Registro Histórico de Desvinculaciones, Traslados y Modificaciones
                        </h6>
                        <p class="text-muted small m-0 mt-1">
                            Evidencias formales registradas ante cualquier salida o traslado del estudiante, con fecha, motivo y responsable.
                        </p>
                    </div>
                    <div class="card-body p-0 p-md-3">
                        <div v-if="historialEventos.length === 0" class="text-center py-5 text-muted">
                            <i class="bi bi-info-circle fs-2 d-block mb-2"></i>
                            El estudiante no posee eventos disciplinarios, traslados ni desvinculaciones registradas.
                        </div>
                        <div v-else class="table-responsive">
                            <table class="table align-middle mb-0" style="color: var(--text-main);">
                                <thead class="table-light small text-uppercase">
                                    <tr>
                                        <th class="ps-3">Fecha y Hora</th>
                                        <th>Tipo de Evento</th>
                                        <th>Equipo Afectado</th>
                                        <th>Motivo / Evidencia Registrada</th>
                                        <th>Responsable</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="ev in historialEventos" :key="ev.heq_id">
                                        <td class="ps-3 small text-muted text-nowrap">
                                            {{ formatearFecha(ev.heq_fecha_evento) }}
                                        </td>
                                        <td>
                                            <span class="badge bg-danger rounded-pill px-2 py-1">
                                                {{ ev.tipo_evento?.tev_nombre || 'Evento de Equipo' }}
                                            </span>
                                        </td>
                                        <td class="fw-semibold">
                                            {{ ev.equipo?.equ_codigo || 'Equipo ' + ev.heq_id_equ }}
                                        </td>
                                        <td style="max-width: 320px;">
                                            <div class="p-2 rounded bg-light border text-dark small" style="white-space: pre-wrap;">
                                                {{ ev.heq_detalles || 'Sin detalles' }}
                                            </div>
                                        </td>
                                        <td class="small text-muted">
                                            {{ ev.usuario_modifico ? `${ev.usuario_modifico.usu_primer_nombre} ${ev.usuario_modifico.usu_primer_apellido}` : 'Sistema' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONTENIDO TAB 2: PUNTOS DE CONTROL Y ASISTENCIA -->
            <div v-show="tabActiva === 'puntos'">
                <div class="card border-0 shadow-sm" style="background: var(--bg-card); border-radius: 14px;">
                    <div class="card-header bg-transparent border-0 pt-3 px-3 px-md-4">
                        <h6 class="fw-bold m-0 text-danger">
                            <i class="bi bi-calendar-check me-1"></i> Control de Asistencia y Observaciones Docente (Semanas 6, 10, 14)
                        </h6>
                        <p class="text-muted small m-0 mt-1">
                            Historial punto a punto con justificaciones de inasistencia y anotaciones individuales sobre el desempeño del estudiante.
                        </p>
                    </div>
                    <div class="card-body p-0 p-md-3">
                        <div v-if="asistenciasPuntos.length === 0" class="text-center py-5 text-muted">
                            <i class="bi bi-calendar-x fs-2 d-block mb-2"></i>
                            No hay registros de puntos de control evaluados para este estudiante todavía.
                        </div>
                        <div v-else class="table-responsive">
                            <table class="table align-middle mb-0" style="color: var(--text-main);">
                                <thead class="table-light small text-uppercase">
                                    <tr>
                                        <th class="ps-3">Punto de Control</th>
                                        <th>Semana</th>
                                        <th>Equipo</th>
                                        <th>Asistencia</th>
                                        <th>Justificación / Motivo</th>
                                        <th>Observación Individual</th>
                                        <th>Observaciones Previas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="pt in asistenciasPuntos" :key="pt.apc_id">
                                        <td class="ps-3 fw-bold">
                                            {{ pt.puc_nombre }}
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary rounded-pill">
                                                Semana {{ pt.puc_semana }}
                                            </span>
                                        </td>
                                        <td class="small">{{ pt.equipo_codigo }}</td>
                                        <td>
                                            <span v-if="pt.asistio" class="badge bg-success rounded-pill px-2 py-1">
                                                <i class="bi bi-check-circle me-1"></i> Asistió
                                            </span>
                                            <span v-else class="badge bg-danger rounded-pill px-2 py-1">
                                                <i class="bi bi-x-circle me-1"></i> Inasistencia
                                            </span>
                                        </td>
                                        <td style="max-width: 250px;">
                                            <div v-if="!pt.asistio">
                                                <span v-if="pt.justificado" class="badge bg-warning text-dark me-1">Justificado</span>
                                                <span v-else class="badge bg-danger me-1">Sin justificar</span>
                                                <span class="small text-muted">{{ pt.justificativo || 'Sin justificativo registrado' }}</span>
                                            </div>
                                            <span v-else class="text-muted small">—</span>
                                        </td>
                                        <td style="max-width: 280px;">
                                            <div v-if="pt.observacion_indiv" class="p-2 rounded bg-light border text-dark small">
                                                {{ pt.observacion_indiv }}
                                            </div>
                                            <span v-else class="text-muted small">Sin anotaciones</span>
                                        </td>
                                        <td class="small" style="max-width: 220px;">
                                            <div v-if="pt.cumplio_previo !== null">
                                                <span :class="pt.cumplio_previo ? 'badge bg-success' : 'badge bg-danger'" class="me-1">
                                                    {{ pt.cumplio_previo ? 'Cumplió previas' : 'No cumplió' }}
                                                </span>
                                                <div class="text-muted small mt-1">{{ pt.verif_previo || '' }}</div>
                                            </div>
                                            <span v-else class="text-muted">—</span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONTENIDO TAB 3: SOCIALIZACIONES Y AVANCE -->
            <div v-show="tabActiva === 'socializaciones'">
                <div class="card border-0 shadow-sm" style="background: var(--bg-card); border-radius: 14px;">
                    <div class="card-header bg-transparent border-0 pt-3 px-3 px-md-4">
                        <h6 class="fw-bold m-0 text-danger">
                            <i class="bi bi-award me-1"></i> Participación en Pre-Socialización y Socialización Final
                        </h6>
                        <p class="text-muted small m-0 mt-1">
                            Según normativa UPTP, si el estudiante no asiste a la Pre-Socialización reprueba y no avanza a la defensa final.
                        </p>
                    </div>
                    <div class="card-body p-0 p-md-3">
                        <div v-if="socializaciones.length === 0" class="text-center py-5 text-muted">
                            <i class="bi bi-mic-mute fs-2 d-block mb-2"></i>
                            El estudiante no posee socializaciones registradas aún.
                        </div>
                        <div v-else class="table-responsive">
                            <table class="table align-middle mb-0" style="color: var(--text-main);">
                                <thead class="table-light small text-uppercase">
                                    <tr>
                                        <th class="ps-3">Tipo de Socialización</th>
                                        <th>Fecha y Lugar</th>
                                        <th>Equipo</th>
                                        <th>Asistencia</th>
                                        <th>Veredicto de Avance</th>
                                        <th>Justificación / Observaciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="soc in socializaciones" :key="soc.iso_id">
                                        <td class="ps-3 fw-bold">
                                            {{ soc.tipo }}
                                        </td>
                                        <td class="small">
                                            <div>{{ soc.fecha || 'Fecha por definir' }}</div>
                                            <div class="text-muted">{{ soc.lugar }}</div>
                                        </td>
                                        <td class="small">{{ soc.equipo_codigo }}</td>
                                        <td>
                                            <span v-if="soc.asistio" class="badge bg-success rounded-pill px-2 py-1">
                                                <i class="bi bi-check-circle me-1"></i> Asistió
                                            </span>
                                            <span v-else class="badge bg-danger rounded-pill px-2 py-1">
                                                <i class="bi bi-x-circle me-1"></i> Inasistencia (Reprueba)
                                            </span>
                                        </td>
                                        <td>
                                            <span v-if="soc.avanza" class="badge bg-success rounded-pill px-2 py-1">
                                                <i class="bi bi-arrow-right-circle me-1"></i> Avanza
                                            </span>
                                            <span v-else class="badge bg-danger rounded-pill px-2 py-1">
                                                <i class="bi bi-x-octagon me-1"></i> Reprobado / No Avanza
                                            </span>
                                        </td>
                                        <td style="max-width: 320px;">
                                            <div v-if="soc.justificacion" class="p-2 rounded bg-light border text-dark small">
                                                {{ soc.justificacion }}
                                            </div>
                                            <span v-else class="text-muted small">Sin observaciones</span>
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
import axios from "axios";

const props = defineProps({
    estudiante: Object,
    historialEventos: Array,
    asistenciasPuntos: Array,
    socializaciones: Array,
    backUrl: String,
    esCoordinador: Boolean,
});

const tabActiva = ref("equipos");
const tema = ref(localStorage.getItem("coord-theme") || "light");
const busquedaInput = ref("");
const resultadosBusqueda = ref([]);

const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("coord-theme", tema.value);
};

const iniciales = computed(() => {
    const nom = props.estudiante.usu_primer_nombre || "";
    const ape = props.estudiante.usu_primer_apellido || "";
    return `${nom[0] || ""}${ape[0] || ""}`.toUpperCase();
});

const seccionActual = computed(() => {
    return props.estudiante.secciones_estudiante?.[0]?.seccion || null;
});

const equipoActual = computed(() => {
    return props.estudiante.equipos_integrante?.[0]?.equipo || null;
});

const formatearFecha = (fechaStr) => {
    if (!fechaStr) return "N/A";
    const d = new Date(fechaStr);
    return isNaN(d) ? fechaStr : d.toLocaleString("es-VE", { dateStyle: "short", timeStyle: "short" });
};

let timerBusqueda = null;
const buscarEstudiantes = () => {
    clearTimeout(timerBusqueda);
    const q = busquedaInput.value.trim();
    if (q.length < 2) {
        resultadosBusqueda.value = [];
        return;
    }
    timerBusqueda = setTimeout(async () => {
        try {
            const url = `${props.esCoordinador ? '/coordinador' : '/profesor'}/estudiantes/buscar?q=${encodeURIComponent(q)}`;
            const res = await axios.get(url);
            resultadosBusqueda.value = res.data;
        } catch (e) {
            console.error(e);
        }
    }, 300);
};
</script>

<style scoped>
.topbar-dossier {
    background: var(--bg-card);
    border-bottom: 1px solid var(--border);
    position: sticky;
    top: 0;
    z-index: 1020;
}
.nav-pills .nav-link {
    color: var(--text-muted);
    background: var(--bg-card);
    border: 1px solid var(--border);
    transition: all 0.2s;
}
.nav-pills .nav-link.active {
    background: #dc3545 !important;
    color: #fff !important;
    border-color: #dc3545 !important;
}
@media (max-width: 768px) {
    .topbar-dossier h5 {
        font-size: 1.05rem;
    }
}
</style>
