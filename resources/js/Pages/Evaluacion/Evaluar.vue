<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body, #f8f9fa);
            color: var(--text-main, #212529);
        "
    >
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

        <div class="main">
            <!-- Topbar -->
            <div class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <div class="tb-title">
                        <i class="bi bi-clipboard-check-fill me-2 text-danger d-none d-sm-inline"></i>
                        Evaluación con Rúbrica — {{ nombreRol }} ({{ recorrido.semestre }})
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <Link
                        :href="`/evaluaciones/recorrido/${recorrido.id_proyecto}`"
                        class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                    >
                        <i class="bi bi-arrow-left me-1"></i> Regresar al Recorrido
                    </Link>
                    <button class="tb-btn" @click="toggleTema" title="Cambiar tema">
                        <i class="bi" :class="tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'"></i>
                    </button>
                </div>
            </div>

            <div class="content p-3 p-md-4">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb small">
                        <li class="breadcrumb-item">
                            <Link href="/evaluaciones" class="text-decoration-none text-muted">Evaluaciones PST</Link>
                        </li>
                        <li class="breadcrumb-item">
                            <Link :href="`/evaluaciones/recorrido/${recorrido.id_proyecto}`" class="text-decoration-none text-muted">
                                Recorrido del Proyecto
                            </Link>
                        </li>
                        <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">
                            Evaluar: {{ nombreRol }} ({{ recorrido.semestre }})
                        </li>
                    </ol>
                </nav>

                <!-- Header Card con Rúbrica -->
                <div class="card shadow-sm border-0 mb-4 rounded-3">
                    <div class="card-body bg-light rounded-3 p-4">
                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                            <div>
                                <span class="badge bg-danger mb-2 px-3 py-2 fs-6 rounded-pill">
                                    <i class="bi bi-ui-checks-grid me-1"></i> Rúbrica Oficial PNFI
                                </span>
                                <h3 class="h4 fw-bold text-dark mb-1">{{ rubrica.titulo }}</h3>
                                <p class="text-muted mb-0 small">
                                    <strong>Equipo:</strong> <span class="badge bg-dark">{{ recorrido.codigo_equipo }}</span> &bull; 
                                    <strong>Proyecto:</strong> {{ recorrido.titulo_proyecto }} &bull;
                                    <strong>Trayecto:</strong> {{ recorrido.trayecto_nombre || 'PNFI' }} &bull;
                                    <strong>Comunidad:</strong> {{ recorrido.comunidad_nombre || 'N/A' }}
                                </p>
                            </div>
                            <div class="text-md-end">
                                <span class="badge bg-primary fs-6 px-3 py-2 mb-1 rounded-pill">
                                    {{ recorrido.semestre }} (Peso en Trayecto: {{ Number(recorrido.peso_semestre || 0).toFixed(0) }}%)
                                </span>
                                <div class="text-muted small">
                                    Ponderación del Evaluador: <strong>{{ Number(rubrica.peso_porcentual || 0).toFixed(0) }}%</strong> del Semestre
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alerta de Evaluación Previa -->
                <div v-if="evaluacionPrevia" class="alert alert-warning d-flex align-items-center shadow-sm mb-4 rounded-3" role="alert">
                    <i class="bi bi-info-circle-fill fs-3 me-3 text-warning"></i>
                    <div class="small">
                        <strong>Aviso de Edición:</strong> Ya existe una evaluación registrada para este rol con fecha 
                        <u>{{ formatearFechaHora(evaluacionPrevia.fecha_evaluacion) }}</u> 
                        con calificación de <strong>{{ formatearNota(evaluacionPrevia.calificacion_cuantitativa) }} / 20.00 pts</strong>.
                        Al guardar nuevamente, se actualizarán las calificaciones y la síntesis cualitativa.
                    </div>
                </div>

                <!-- Errores de Validación -->
                <div v-if="Object.keys(form.errors).length > 0" class="alert alert-danger alert-dismissible fade show shadow-sm rounded-3" role="alert">
                    <h6 class="alert-heading fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Por favor verifique los siguientes errores:</h6>
                    <ul class="mb-0 small">
                        <li v-for="(err, key) in form.errors" :key="key">{{ err }}</li>
                    </ul>
                </div>

                <!-- Formulario de Evaluación -->
                <form @submit.prevent="submitEvaluacion">
                    <!-- Datos del Evaluador -->
                    <div class="card shadow-sm border-0 mb-4 rounded-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-person-badge text-danger me-2"></i>Datos del Evaluador ({{ nombreRol }})
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <!-- Si es Comunidad -->
                            <div v-if="rol_evaluador === 'comunidad'" class="row align-items-center">
                                <div class="col-md-8">
                                    <p class="mb-1"><strong>Comunidad Beneficiaria:</strong> {{ recorrido.comunidad_nombre || 'No asignada' }}</p>
                                    <p class="text-muted small mb-0">
                                        La evaluación comunitaria es consignada con base en la interacción de la comunidad con los estudiantes, el impacto social y los productos entregados.
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                    <span class="badge bg-info text-dark fs-6 px-3 py-2 rounded-pill">
                                        <i class="bi bi-people-fill me-1"></i> Representante de la Comunidad ({{ Number(rubrica.peso_porcentual || 20).toFixed(0) }}%)
                                    </span>
                                </div>
                            </div>

                            <!-- Si es Estudiante -->
                            <div v-else-if="rol_evaluador === 'estudiante'" class="row align-items-center">
                                <div class="col-md-8">
                                    <p class="mb-1"><strong>Colectivo de Estudiantes / Equipo:</strong> {{ recorrido.codigo_equipo }} - {{ recorrido.titulo_proyecto }}</p>
                                    <p class="text-muted small mb-0">
                                        La autoevaluación y coevaluación estudiantil permite al equipo reflexionar sobre su autogestión, equidad en el trabajo, puntualidad y cumplimiento de metas.
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                                    <span class="badge bg-success fs-6 px-3 py-2 rounded-pill">
                                        <i class="bi bi-person-check-fill me-1"></i> Autoevaluación Estudiantil ({{ Number(rubrica.peso_porcentual || 10).toFixed(0) }}%)
                                    </span>
                                </div>
                            </div>

                            <!-- Si es Docente o Jurado -->
                            <div v-else class="row g-3">
                                <div class="col-md-6">
                                    <label for="id_profesor" class="form-label fw-bold small text-dark">
                                        Docente Evaluador / Jurado: <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        v-model="form.id_profesor"
                                        id="id_profesor"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.id_profesor }"
                                        required
                                    >
                                        <option value="">-- Seleccione el docente evaluador --</option>
                                        <option
                                            v-for="prof in profesores"
                                            :key="prof.usu_id"
                                            :value="prof.usu_id"
                                        >
                                            {{ prof.usu_primer_nombre }} {{ prof.usu_primer_apellido }} (C.I. {{ prof.usu_cedula }})
                                        </option>
                                    </select>
                                    <div class="form-text small">Docente del PNFI responsable de emitir esta calificación.</div>
                                </div>
                                <div class="col-md-6 d-flex align-items-center">
                                    <div class="p-3 bg-light rounded-3 w-100 border">
                                        <span class="fw-bold text-secondary d-block mb-1 small">Rol en esta evaluación:</span>
                                        <span
                                            class="badge fs-6 rounded-pill"
                                            :class="['tutor', 'docente_proyecto'].includes(rol_evaluador) ? 'bg-primary' : 'bg-warning text-dark'"
                                        >
                                            {{ ['tutor', 'docente_proyecto'].includes(rol_evaluador) ? 'Docente de Proyecto (40%)' : 'Jurado Evaluador (30%)' }}
                                        </span>
                                        <span class="ms-2 small text-muted">Peso asignado: {{ Number(rubrica.peso_porcentual || 0).toFixed(0) }}% del Semestre</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabla de Criterios de la Rúbrica -->
                    <div class="card shadow-sm border-0 mb-4 rounded-3">
                        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-clipboard-check text-danger me-2"></i>Criterios de Evaluación Oficiales
                            </h5>
                            <span class="badge bg-secondary rounded-pill">
                                Total Criterios: {{ criterios.length }}
                            </span>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light small text-uppercase">
                                        <tr>
                                            <th style="width: 5%;" class="text-center">#</th>
                                            <th style="width: 55%;">Criterio e Indicador de Logro</th>
                                            <th style="width: 15%;" class="text-center">Tipo</th>
                                            <th style="width: 10%;" class="text-center">Escala Máx.</th>
                                            <th style="width: 15%;" class="text-end pe-4">Calificación Asignada</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="(criterio, idx) in criterios" :key="criterio.id_criterio">
                                            <td class="text-center fw-bold text-muted">{{ idx + 1 }}</td>
                                            <td>
                                                <div class="fw-semibold text-dark">{{ criterio.nombre_criterio }}</div>
                                                <div class="text-muted small">
                                                    <template v-if="criterio.tipo_criterio === 'cuantitativo'">
                                                        Puntaje asignable entre 0.00 y {{ Number(criterio.puntaje_maximo).toFixed(2) }} pts.
                                                    </template>
                                                    <template v-else>
                                                        Apreciación cualitativa del avance y desenvolvimiento técnico.
                                                    </template>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    v-if="criterio.tipo_criterio === 'cuantitativo'"
                                                    class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill"
                                                >
                                                    Cuantitativo
                                                </span>
                                                <span
                                                    v-else
                                                    class="badge bg-success-subtle text-success border border-success-subtle rounded-pill"
                                                >
                                                    Cualitativo
                                                </span>
                                            </td>
                                            <td class="text-center fw-bold text-secondary">
                                                {{ Number(criterio.puntaje_maximo).toFixed(2) }} pts
                                            </td>
                                            <td class="text-end pe-4">
                                                <div class="input-group input-group-sm justify-content-end" style="max-width: 150px; margin-left: auto;">
                                                    <input
                                                        type="number"
                                                        step="0.25"
                                                        min="0"
                                                        :max="criterio.puntaje_maximo"
                                                        v-model.number="form.criterios[criterio.id_criterio]"
                                                        class="form-control form-control-sm text-end fw-bold text-danger border-danger-subtle"
                                                        :class="{ 'is-invalid': (Number(form.criterios[criterio.id_criterio]) > Number(criterio.puntaje_maximo)) }"
                                                        placeholder="0.00"
                                                        required
                                                    />
                                                    <span class="input-group-text bg-light text-muted">
                                                        / {{ Number(criterio.puntaje_maximo).toFixed(1) }}
                                                    </span>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                    <tfoot class="table-light border-top">
                                        <tr>
                                            <th colspan="3" class="text-end py-3 fs-5">Total Cuantitativo de la Rúbrica:</th>
                                            <th class="text-center py-3 fs-5 text-secondary">20.00 pts</th>
                                            <th class="text-end py-3 pe-4">
                                                <div class="d-flex align-items-center justify-content-end">
                                                    <span
                                                        class="fs-4 fw-bold me-2"
                                                        :class="totalPuntaje > 20 || hayExceso ? 'text-danger' : 'text-success'"
                                                    >
                                                        {{ totalPuntaje.toFixed(2) }}
                                                    </span>
                                                    <span class="text-muted fs-6">/ 20.00 pts</span>
                                                </div>
                                                <div class="small mt-1 text-end">
                                                    <span v-if="hayExceso" class="text-danger fw-bold">
                                                        <i class="bi bi-exclamation-triangle"></i> Hay criterios que superan su puntaje máximo.
                                                    </span>
                                                    <span v-else-if="totalPuntaje > 20" class="text-danger fw-bold">
                                                        <i class="bi bi-exclamation-octagon"></i> La suma supera el máximo de 20.00 pts.
                                                    </span>
                                                    <span v-else class="text-success">
                                                        <i class="bi bi-check-circle"></i> Ponderación cuantitativa válida ({{ totalPuntaje.toFixed(2) }} / 20.00 pts)
                                                    </span>
                                                </div>
                                            </th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Evaluación Cualitativa y Síntesis -->
                    <div class="card shadow-sm border-0 mb-4 rounded-3">
                        <div class="card-header bg-white py-3 border-bottom">
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="bi bi-chat-square-quote text-danger me-2"></i>Apreciación Cualitativa y Nivel de Logro
                            </h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="nivel_logro_cualitativo" class="form-label fw-bold small text-dark">
                                        Nivel de Logro Cualitativo Institucional <span class="text-danger">*</span>:
                                    </label>
                                    <select
                                        v-model="form.nivel_logro_cualitativo"
                                        id="nivel_logro_cualitativo"
                                        class="form-select"
                                        :class="{ 'is-invalid': form.errors.nivel_logro_cualitativo }"
                                        required
                                    >
                                        <option value="">-- Seleccione el nivel de logro alcanzado --</option>
                                        <option value="Logrado con Excelencia">
                                            Logrado con Excelencia (Dominio sobresaliente y aportes de alto impacto)
                                        </option>
                                        <option value="Logrado">
                                            Logrado (Cumplimiento satisfactorio de todas las competencias requeridas)
                                        </option>
                                        <option value="En Proceso">
                                            En Proceso (Competencias básicas alcanzadas con aspectos por consolidar)
                                        </option>
                                        <option value="No Logrado">
                                            No Logrado (No alcanza las competencias mínimas exigidas en el trayecto)
                                        </option>
                                    </select>
                                    <div class="form-text small">Refleja la apreciación formativa y cualitativa según lineamientos del PNFI.</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="observacion_cualitativa" class="form-label fw-bold small text-dark">
                                    Observación, Síntesis y Recomendaciones Cualitativas <span class="text-danger">*</span>:
                                </label>
                                <textarea
                                    v-model="form.observacion_cualitativa"
                                    id="observacion_cualitativa"
                                    rows="4"
                                    class="form-control"
                                    :class="{ 'is-invalid': form.errors.observacion_cualitativa }"
                                    placeholder="Escriba aquí la síntesis del desempeño del equipo, la calidad técnica de la solución desarrollada, el compromiso comunitario y las orientaciones formativas pertinentes (mínimo 15 caracteres)..."
                                    required
                                    minlength="15"
                                    maxlength="2000"
                                ></textarea>
                                <div class="d-flex justify-content-between form-text small mt-1">
                                    <span>Esta observación quedará registrada en el historial y en el Acta Final del Trayecto.</span>
                                    <span :class="form.observacion_cualitativa.length < 15 ? 'text-danger fw-bold' : 'text-muted'">
                                        {{ form.observacion_cualitativa.length }} / 2000 caracteres (mínimo 15)
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="card shadow-sm border-0 mb-5 rounded-3">
                        <div class="card-body bg-light rounded-3 d-flex flex-wrap justify-content-between align-items-center p-3 gap-2">
                            <Link
                                :href="`/evaluaciones/recorrido/${recorrido.id_proyecto}`"
                                class="btn btn-outline-secondary px-4 py-2 rounded-pill"
                            >
                                <i class="bi bi-arrow-left me-1"></i> Regresar al Recorrido
                            </Link>
                            <button
                                type="submit"
                                class="btn btn-danger px-5 py-2 fw-bold shadow-sm rounded-pill"
                                :disabled="form.processing || hayExceso || totalPuntaje > 20 || form.observacion_cualitativa.length < 15"
                            >
                                <i class="bi bi-check2-circle me-1"></i> Guardar y Consolidar Evaluación
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, usePage, useForm } from "@inertiajs/vue3";
import SidebarProfesor from "@/Components/SidebarProfesor.vue";
import SidebarCoordinador from "@/Components/SidebarCoordinador.vue";
import SidebarLider from "@/Components/SidebarLider.vue";

const props = defineProps({
    recorrido: Object,
    rubrica: Object,
    criterios: Array,
    rol_evaluador: String,
    evaluacionPrevia: Object,
    detallesPrevios: Object,
    profesores: Array,
});

const page = usePage();
const sidebarOpen = ref(false);

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
};

const nombresRoles = {
    docente_proyecto: "Docente de Proyecto",
    tutor: "Docente Tutor",
    jurado: "Jurado Evaluador",
    comunidad: "Representante de la Comunidad",
    estudiante: "Autoevaluación Estudiantil",
};

const nombreRol = computed(() => {
    return nombresRoles[props.rol_evaluador] || props.rol_evaluador;
});

// Inicializar criterios con valores previos si existen
const initialCriterios = {};
(props.criterios || []).forEach((c) => {
    const previo = props.detallesPrevios?.[c.id_criterio];
    initialCriterios[c.id_criterio] = previo !== undefined && previo !== null ? Number(previo) : 0;
});

const form = useForm({
    id_recorrido: props.recorrido.id_recorrido,
    id_proyecto: props.recorrido.id_proyecto,
    rol_evaluador: props.rol_evaluador,
    id_profesor: props.evaluacionPrevia?.id_profesor || (props.rol_evaluador !== "comunidad" && props.rol_evaluador !== "estudiante" ? page.props.auth?.user?.usu_id : null),
    criterios: initialCriterios,
    nivel_logro_cualitativo: props.evaluacionPrevia?.nivel_logro_cualitativo || "",
    observacion_cualitativa: props.evaluacionPrevia?.observacion_cualitativa || "",
});

const totalPuntaje = computed(() => {
    let sum = 0;
    for (const val of Object.values(form.criterios)) {
        sum += Number(val) || 0;
    }
    return Math.round(sum * 100) / 100;
});

const hayExceso = computed(() => {
    for (const criterio of props.criterios || []) {
        const val = Number(form.criterios[criterio.id_criterio]) || 0;
        const max = Number(criterio.puntaje_maximo) || 20;
        if (val > max) return true;
    }
    return false;
});

const submitEvaluacion = () => {
    form.post("/evaluaciones/guardar");
};

const formatearNota = (val) => {
    if (val === null || val === undefined || isNaN(val)) return "0.00";
    return Number(val).toFixed(2);
};

const formatearFechaHora = (val) => {
    if (!val) return "—";
    const d = new Date(val);
    return isNaN(d) ? val : d.toLocaleString();
};
</script>

<style scoped>
.main {
    margin-left: 250px;
    min-height: 100vh;
    background: var(--bg-body, #f8f9fa);
    color: var(--text-main, #212529);
    transition: background 0.2s, color 0.2s;
}
.topbar {
    height: 65px;
    background: var(--bg-topbar, #fff);
    border-bottom: 1px solid var(--border, #dee2e6);
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
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--text-main, #343a40);
}
.tb-btn {
    background: none;
    border: none;
    color: var(--text-muted, #6c757d);
    font-size: 1.2rem;
    cursor: pointer;
    padding: 4px;
    transition: color 0.2s;
}
.tb-btn:hover {
    color: #dc3545;
}
@media (max-width: 991.98px) {
    .main {
        margin-left: 0;
    }
}
</style>
