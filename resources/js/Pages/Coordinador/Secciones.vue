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
                            class="bi bi-collection me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Secciones
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
                        <div class="d-flex align-items-center gap-2">
                            <h4
                                style="
                                    font-weight: 800;
                                    font-size: 1.25rem;
                                    color: var(--text-main);
                                    margin: 0;
                                "
                            >
                                Secciones Académicas
                            </h4>
                            <span v-if="periodoActivo?.lapso_academico" class="badge bg-dark text-white fw-bold px-3 py-1 rounded-pill">
                                <i class="bi bi-calendar-event text-danger me-1"></i>Lapso Activo: {{ periodoActivo.lapso_academico }}
                            </span>
                        </div>
                        <p
                            style="
                                color: var(--text-muted);
                                font-size: 0.85rem;
                                margin-top: 2px;
                            "
                        >
                            Consulta académica del PNF Informática &bull; Inicio en Semestre Impar y Cierre en Semestre Par
                        </p>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <button
                            type="button"
                            class="btn btn-sm btn-danger fw-bold rounded-pill px-3 shadow-sm"
                            :disabled="sincronizandoSogac"
                            @click="sincronizarSogac"
                        >
                            <span v-if="sincronizandoSogac" class="spinner-border spinner-border-sm me-1"></span>
                            <i v-else class="bi bi-arrow-repeat me-1"></i>
                            Sincronizar Transición SOGAC
                        </button>
                        <Link
                            href="/coordinador/transicion-semestral"
                            class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold"
                        >
                            <i class="bi bi-clock-history me-1"></i>Historial y Cierre
                        </Link>
                    </div>
                </div>

                <div
                    v-if="$page.props.flash?.error || Object.keys($page.props.errors || {}).length"
                    class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3"
                >
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span v-if="$page.props.flash?.error">{{ $page.props.flash.error }}</span>
                    <ul v-else class="mb-0 ps-3">
                        <li v-for="(err, k) in $page.props.errors" :key="k">{{ err }}</li>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <div
                    v-if="$page.props.flash?.success"
                    class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3"
                >
                    <i class="bi bi-check-circle-fill me-2"></i>{{ $page.props.flash.success }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <div class="sc">
                    <!-- Barra de Separación Diferenciada Impar vs Par -->
                    <div
                        style="
                            padding: 0.8rem 1rem;
                            border-bottom: 1px solid var(--border);
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            flex-wrap: wrap;
                            gap: 10px;
                            background: rgba(0, 0, 0, 0.02);
                        "
                    >
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="small fw-bold text-uppercase text-muted me-1">Filtrar Semestre:</span>
                            <button
                                type="button"
                                class="btn btn-sm fw-bold px-3 rounded-pill btn-filtro-todos"
                                :class="{ active: filtroTipoSemestre === 'todos' }"
                                @click="filtroTipoSemestre = 'todos'"
                            >
                                Todas ({{ (secciones || []).length }})
                            </button>
                            <button
                                type="button"
                                class="btn btn-sm fw-bold px-3 rounded-pill btn-filtro-impar"
                                :class="{ active: filtroTipoSemestre === 'impar' }"
                                @click="filtroTipoSemestre = 'impar'"
                                title="Secciones que inician proyecto en semestre impar"
                            >
                                <i class="bi bi-rocket-takeoff me-1"></i>
                                Semestre Impar: 131, 331, 531... ({{ conteoImpares }})
                            </button>
                            <button
                                type="button"
                                class="btn btn-sm fw-bold px-3 rounded-pill btn-filtro-par"
                                :class="{ active: filtroTipoSemestre === 'par' }"
                                @click="filtroTipoSemestre = 'par'"
                                title="Secciones que cierran proyecto en semestre par"
                            >
                                <i class="bi bi-cloud-arrow-up me-1"></i>
                                Semestre Par: 231, 431, 631... ({{ conteoPares }})
                            </button>
                        </div>

                        <div class="small text-muted">
                            <i class="bi bi-arrow-left-right text-danger me-1"></i>
                            <Link href="/coordinador/transicion-semestral" class="fw-bold text-danger text-decoration-none">
                                Gestión de Transición y Cierre &rarr;
                            </Link>
                        </div>
                    </div>

                    <div
                        style="
                            padding: 0.9rem 1rem;
                            border-bottom: 1px solid var(--border);
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            flex-wrap: wrap;
                            gap: 8px;
                        "
                    >
                        <div class="srch">
                            <i class="bi bi-search"></i
                            ><input
                                v-model="busqueda"
                                type="text"
                                placeholder="Buscar por código, trayecto o turno..."
                            />
                        </div>
                        <span
                            style="
                                font-size: 0.82rem;
                                color: var(--text-muted);
                                font-weight: 600;
                            "
                            >{{ filtradas.length }} resultado(s)</span
                        >
                    </div>
                    <div class="table-responsive">
                        <table class="ct">
                            <thead>
                                <tr>
                                    <th class="ps-4">Código</th>
                                    <th>Trayecto</th>
                                    <th>Semestre</th>
                                    <th>Turno</th>
                                    <th>Profesor Asignado</th>
                                    <th>Estado</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="paginadas.length === 0">
                                    <td
                                        colspan="7"
                                        class="text-center py-5"
                                        style="color: var(--text-muted)"
                                    >
                                        Sin resultados
                                    </td>
                                </tr>
                                <tr v-for="sec in paginadas" :key="sec.sec_id">
                                    <td
                                        class="ps-4 fw-bold"
                                        style="color: var(--rojo)"
                                    >
                                        {{ sec.sec_codigo }}
                                    </td>
                                    <td>{{ sec.trayecto?.tra_nombre }}</td>
                                    <td>
                                        <span
                                            class="badge"
                                            :class="
                                                semestreDeSeccion(sec) % 2 !== 0
                                                    ? 'bg-warning text-dark'
                                                    : 'bg-primary'
                                            "
                                        >
                                            Semestre {{ semestreDeSeccion(sec) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="bs"
                                            :class="
                                                badgeTurno(
                                                    sec.turno?.tur_nombre,
                                                )
                                            "
                                            >{{
                                                sec.turno?.tur_nombre || "N/A"
                                            }}</span
                                        >
                                    </td>
                                    <td>
                                        <div
                                            v-if="sec.profesor_actual?.usuario"
                                            class="d-flex align-items-center gap-1"
                                        >
                                            <i
                                                class="bi bi-person-check-fill text-success"
                                            ></i>
                                            <span class="fw-semibold"
                                                >{{
                                                    sec.profesor_actual.usuario
                                                        .usu_primer_nombre
                                                }}
                                                {{
                                                    sec.profesor_actual.usuario
                                                        .usu_primer_apellido
                                                }}</span
                                            >
                                        </div>
                                        <span v-else class="text-muted small">
                                            <i
                                                class="bi bi-person-x text-danger me-1"
                                            ></i
                                            >Sin asignar
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="bs"
                                            :class="
                                                sec.sec_status ? 'ba' : 'bd'
                                            "
                                            >{{
                                                sec.sec_status
                                                    ? "Activa"
                                                    : "Inactiva"
                                            }}</span
                                        >
                                    </td>
                                    <td class="text-end pe-4 text-nowrap">
                                        <!-- Gestión Diferenciada Impar vs Par -->
                                        <a
                                            v-if="semestreDeSeccion(sec) % 2 !== 0"
                                            href="/coordinador/transicion-semestral?tab=impar"
                                            class="btn btn-sm btn-outline-warning text-dark fw-bold me-1"
                                            title="Promover equipos al semestre par (Transición Semestral)"
                                        >
                                            <i class="bi bi-rocket-takeoff-fill me-1"></i>Transición Par
                                        </a>
                                        <a
                                            v-else
                                            href="/coordinador/transicion-semestral?tab=par"
                                            class="btn btn-sm btn-outline-dark fw-bold me-1"
                                            title="Cierre Definitivo de Trayecto y Envío al Repositorio"
                                        >
                                            <i class="bi bi-cloud-arrow-up-fill text-danger me-1"></i>Cierre y Repositorio
                                        </a>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-warning text-dark me-1"
                                            @click="abrirAsignarProfesor(sec)"
                                            title="Asignar / Reasignar Profesor de Proyecto"
                                        >
                                            <i class="bi bi-person-plus-fill"></i>
                                        </button>
                                        <a
                                            :href="`/coordinador/secciones/${sec.sec_id}/detalle`"
                                            class="btn btn-sm btn-outline-info me-1"
                                            title="Ver detalle y gestión de equipos"
                                            ><i class="bi bi-eye"></i
                                        ></a>
                                        <a
                                            :href="`/coordinador/secciones/${sec.sec_id}/excel/vinculacion`"
                                            class="btn btn-sm btn-outline-success"
                                            title="Descargar Planilla de Vinculación oficial (.xlsx)"
                                            ><i
                                                class="bi bi-file-earmark-spreadsheet"
                                            ></i
                                        ></a>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        v-if="totalPaginas > 1"
                        style="
                            padding: 0.75rem 1rem;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            border-top: 1px solid var(--border);
                        "
                    >
                        <span
                            style="font-size: 0.82rem; color: var(--text-muted)"
                            >{{ desde + 1 }}–{{
                                Math.min(hasta, filtradas.length)
                            }}
                            de {{ filtradas.length }}</span
                        >
                        <div class="d-flex gap-1">
                            <button
                                class="btn btn-sm btn-outline-secondary"
                                :disabled="pagina === 1"
                                @click="pagina--"
                            >
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button
                                v-for="p in paginasV"
                                :key="p"
                                class="btn btn-sm"
                                :class="
                                    p === pagina
                                        ? 'btn-danger'
                                        : 'btn-outline-secondary'
                                "
                                @click="pagina = p"
                            >
                                {{ p }}
                            </button>
                            <button
                                class="btn btn-sm btn-outline-secondary"
                                :disabled="pagina === totalPaginas"
                                @click="pagina++"
                            >
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mSec" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>
                            {{ editando ? "Editar Sección" : "Nueva Sección" }}
                        </h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form @submit.prevent="guardar">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Código
                                    <span class="text-danger">*</span></label
                                >
                                <input
                                    v-model="form.sec_codigo"
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': errores.sec_codigo,
                                    }"
                                    placeholder="Ej: 331"
                                />
                                <div
                                    v-if="errores.sec_codigo"
                                    class="invalid-feedback"
                                >
                                    {{ errores.sec_codigo }}
                                </div>
                            </div>
                            <div class="row g-2 mb-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Trayecto
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <SelectBuscable
                                        v-model="form.sec_id_tra"
                                        :options="opcionesTrayectosSec"
                                        :has-error="!!errores.sec_id_tra"
                                        placeholder="Seleccionar..."
                                    />
                                    <div
                                        v-if="errores.sec_id_tra"
                                        class="invalid-feedback d-block"
                                    >
                                        {{ errores.sec_id_tra }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Semestre</label
                                    >
                                    <SelectBuscable
                                        v-model="form.sec_semestre"
                                        :options="opcionesSemestresSec"
                                        placeholder="Semestre..."
                                    />
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Profesor de Proyecto</label
                                >
                                <SelectBuscable
                                    v-model="form.pse_id_usu"
                                    :options="opcionesProfesoresSec"
                                    placeholder="Sin profesor asignado..."
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Turno
                                    <span class="text-danger">*</span></label
                                >
                                <SelectBuscable
                                    v-model="form.sec_id_tur"
                                    :options="opcionesTurnosSec"
                                    :has-error="!!errores.sec_id_tur"
                                    placeholder="Seleccionar..."
                                />
                                <div
                                    v-if="errores.sec_id_tur"
                                    class="invalid-feedback"
                                >
                                    {{ errores.sec_id_tur }}
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="btn btn-danger fw-bold"
                                :disabled="loading"
                            >
                                <span
                                    v-if="loading"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span
                                >{{ editando ? "Actualizar" : "Guardar" }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mConf" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center p-4">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem">
                            📚
                        </div>
                        <h6 class="fw-bold" style="color: var(--text-main)">
                            ¿Desactivar Sección?
                        </h6>
                        <p
                            style="color: var(--text-muted); font-size: 0.9rem"
                            class="mb-0"
                        >
                            ¿Seguro que deseas desactivar la sección
                            <strong>{{ itemAEliminar?.sec_codigo }}</strong
                            >?
                        </p>
                    </div>
                    <div
                        class="modal-footer border-0 justify-content-center pb-4 gap-2"
                    >
                        <button
                            class="btn btn-secondary px-4"
                            data-bs-dismiss="modal"
                        >
                            Cancelar
                        </button>
                        <button
                            class="btn btn-danger fw-bold px-4"
                            @click="confirmarDesactivar"
                        >
                            Sí, desactivar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Asignar / Reasignar Profesor a Sección -->
        <div class="modal fade" id="mAsigProf" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>
                            <i class="bi bi-person-check-fill text-warning me-2"></i>
                            Asignar Profesor — Sección {{ secAsigProf?.sec_codigo }}
                        </h5>
                        <button
                            type="button"
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form @submit.prevent="confirmarAsignarProfesor">
                        <div class="modal-body">
                            <div
                                v-if="secAsigProf?.profesor_actual?.usuario"
                                class="alert alert-light border small py-2 px-3 mb-3"
                            >
                                <strong>Profesor actual:</strong>
                                {{ secAsigProf.profesor_actual.usuario.usu_primer_nombre }}
                                {{ secAsigProf.profesor_actual.usuario.usu_primer_apellido }}
                            </div>
                            <div v-if="errorAsigProf" class="alert alert-danger py-2 px-3 small mb-3">
                                {{ errorAsigProf }}
                            </div>
                            <div class="mb-2">
                                <label class="form-label fw-semibold small">
                                    Profesor de Proyecto <span class="text-danger">*</span>
                                </label>
                                <SelectBuscable
                                    v-model="profAsigId"
                                    :options="opcionesProfesoresAsig"
                                    placeholder="Seleccionar profesor..."
                                    search-placeholder="Buscar por nombre o cédula..."
                                />
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="btn btn-danger fw-bold"
                                :disabled="!profAsigId || loadingAsigProf"
                            >
                                <span v-if="loadingAsigProf" class="spinner-border spinner-border-sm me-2"></span>
                                Guardar Asignación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Modal } from "bootstrap";
import { Link } from "@inertiajs/vue3";
import SidebarCoordinador from "@/Components/SidebarCoordinador.vue";
import SelectBuscable from "@/Components/SelectBuscable.vue";
const menuMaestras = ref(false);
const menuAcademico = ref(false);
const menuSistema = ref(false);
const props = defineProps({
    secciones: Array,
    trayectos: Array,
    turnos: Array,
    periodoActivo: Object,
    profesores: { type: Array, default: () => [] },
});
const page = usePage();
const secAsigProf = ref(null);
const profAsigId = ref("");
const loadingAsigProf = ref(false);
const errorAsigProf = ref("");
let modalAsigProf = null;

const opcionesProfesoresAsig = computed(() => [
    { value: "", label: "Seleccionar profesor..." },
    ...(props.profesores || []).map((p) => ({
        value: p.usu_id,
        label: `${p.usu_primer_nombre} ${p.usu_primer_apellido}`,
        sublabel: `C.I. ${p.usu_cedula}`,
    })),
]);

const abrirAsignarProfesor = (sec) => {
    secAsigProf.value = sec;
    profAsigId.value = sec?.profesor_actual?.usuario?.usu_id || "";
    errorAsigProf.value = "";
    const el = document.getElementById("mAsigProf");
    if (el) {
        modalAsigProf = Modal.getInstance(el) || new Modal(el);
        modalAsigProf.show();
    }
};

const confirmarAsignarProfesor = () => {
    if (!secAsigProf.value || !profAsigId.value) return;
    loadingAsigProf.value = true;
    errorAsigProf.value = "";
    router.post(
        `/coordinador/secciones/${secAsigProf.value.sec_id}/profesores`,
        { pse_id_usu: profAsigId.value },
        {
            preserveScroll: true,
            onSuccess: () => {
                loadingAsigProf.value = false;
                modalAsigProf?.hide();
            },
            onError: (errs) => {
                loadingAsigProf.value = false;
                errorAsigProf.value = Object.values(errs)[0] || "No se pudo asignar el profesor.";
            },
        },
    );
};
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
const busqueda = ref("");
const pagina = ref(1);
const porPagina = 10;
const form = ref({ sec_codigo: "", sec_id_tra: "", sec_id_tur: "" });
const errores = ref({});
const loading = ref(false);
const editando = ref(null);
const itemAEliminar = ref(null);
let modal = null;
let modalConf = null;
const filtroTipoSemestre = ref("todos");
const sincronizandoSogac = ref(false);
const sincronizarSogac = () => {
    sincronizandoSogac.value = true;
    router.post("/coordinador/transicion-semestral/sincronizar-sogac", {}, {
        preserveScroll: true,
        onFinish: () => {
            sincronizandoSogac.value = false;
        },
    });
};
watch([busqueda, filtroTipoSemestre], () => {
    pagina.value = 1;
});
const badgeTurno = (t) => {
    if (t === "Mañana") return "bw";
    if (t === "Tarde") return "bp";
    return "ba";
};

const conteoImpares = computed(() => {
    return (props.secciones || []).filter((s) => semestreDeSeccion(s) % 2 !== 0).length;
});
const conteoPares = computed(() => {
    return (props.secciones || []).filter((s) => semestreDeSeccion(s) % 2 === 0).length;
});

const filtradas = computed(() => {
    return (props.secciones || []).filter((s) => {
        const sem = semestreDeSeccion(s);
        const esImpar = sem % 2 !== 0;
        if (filtroTipoSemestre.value === "impar" && !esImpar) return false;
        if (filtroTipoSemestre.value === "par" && esImpar) return false;

        const q = busqueda.value.toLowerCase().trim();
        if (!q) return true;

        return (
            s.sec_codigo.toLowerCase().includes(q) ||
            s.trayecto?.tra_nombre?.toLowerCase().includes(q) ||
            s.turno?.tur_nombre?.toLowerCase().includes(q) ||
            s.profesor_actual?.usuario?.usu_primer_nombre
                ?.toLowerCase()
                .includes(q) ||
            s.profesor_actual?.usuario?.usu_primer_apellido
                ?.toLowerCase()
                .includes(q)
        );
    });
});
const totalPaginas = computed(() =>
    Math.ceil(filtradas.value.length / porPagina),
);
const desde = computed(() => (pagina.value - 1) * porPagina);
const hasta = computed(() => desde.value + porPagina);
const paginadas = computed(() =>
    filtradas.value.slice(desde.value, hasta.value),
);
const paginasV = computed(() => {
    const t = totalPaginas.value,
        a = pagina.value,
        p = [],
        i = Math.max(1, a - 2),
        f = Math.min(t, a + 2);
    for (let x = i; x <= f; x++) p.push(x);
    return p;
});
const semestreDeSeccion = (sec) => {
    if (!sec) return 1;
    if (sec.sec_semestre) return parseInt(sec.sec_semestre);
    if (sec.sec_codigo) {
        const m = String(sec.sec_codigo).match(/\d/);
        if (m) return parseInt(m[0]);
    }
    return sec.sec_id_tra || 1;
};

const opcionesTrayectosSec = computed(() => [
    { value: "", label: "Seleccionar..." },
    ...(props.trayectos || []).map((tra) => ({ value: tra.tra_id, label: tra.tra_nombre })),
]);
const opcionesSemestresSec = [
    { value: 1, label: "Semestre 1 (Cualitativo)" },
    { value: 2, label: "Semestre 2 (Cuantitativo)" },
    { value: 3, label: "Semestre 3 (Cualitativo)" },
    { value: 4, label: "Semestre 4 (Cuantitativo)" },
    { value: 5, label: "Semestre 5 (Cualitativo)" },
    { value: 6, label: "Semestre 6 (Cuantitativo)" },
    { value: 7, label: "Semestre 7 (Cualitativo)" },
    { value: 8, label: "Semestre 8 (Cuantitativo)" },
];
const opcionesProfesoresSec = computed(() => [
    { value: "", label: "Sin profesor asignado..." },
    ...(props.profesores || []).map((p) => ({
        value: p.usu_id,
        label: `${p.usu_primer_nombre} ${p.usu_primer_apellido} (C.I. ${p.usu_cedula})`,
    })),
]);
const opcionesTurnosSec = computed(() => [
    { value: "", label: "Seleccionar..." },
    ...(props.turnos || []).map((tur) => ({ value: tur.tur_id, label: tur.tur_nombre })),
]);

const abrirModal = (sec = null) => {
    editando.value = sec;
    form.value = {
        sec_codigo: sec?.sec_codigo || "",
        sec_id_tra: sec?.sec_id_tra || "",
        sec_semestre: sec?.sec_semestre || 1,
        sec_id_tur: sec?.sec_id_tur || "",
        pse_id_usu: sec?.profesor_actual?.pse_id_usu || "",
    };
    errores.value = {};
    modal = modal || new Modal(document.getElementById("mSec"));
    modal.show();
};
const guardar = () => {
    loading.value = true;
    const url = editando.value
        ? `/coordinador/secciones/${editando.value.sec_id}`
        : "/coordinador/secciones";
    const method = editando.value ? "put" : "post";
    router[method](url, form.value, {
        onError: (e) => {
            errores.value = e;
            loading.value = false;
        },
        onSuccess: () => {
            loading.value = false;
            modal?.hide();
        },
    });
};
const desactivar = (sec) => {
    itemAEliminar.value = sec;
    modalConf = modalConf || new Modal(document.getElementById("mConf"));
    modalConf.show();
};
const confirmarDesactivar = () => {
    router.delete(`/coordinador/secciones/${itemAEliminar.value.sec_id}`);
    modalConf?.hide();
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
.srch {
    position: relative;
    max-width: 280px;
}
.srch input {
    padding: 0.45rem 1rem 0.45rem 2.2rem;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--bg-card);
    color: var(--text-main);
    font-size: 0.9rem;
    width: 100%;
}
.srch input:focus {
    outline: none;
    border-color: #dc3545;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}
.srch i {
    position: absolute;
    left: 0.7rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}
.mh {
    background: #1a1d23;
    padding: 1rem 1.3rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mh h5 {
    color: #fff;
    font-weight: 800;
    margin: 0;
    font-size: 1rem;
}
.form-control,
.form-select {
    background: var(--bg-card) !important;
    color: var(--text-main) !important;
    border-color: var(--border) !important;
}
.form-label {
    color: var(--text-main);
}
.modal-content {
    background: var(--bg-card) !important;
    border: 1px solid var(--border) !important;
}
.modal-footer,
.modal-body {
    background: var(--bg-card);
}
.sb-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1035;
    display: none;
}
.btn-filtro-todos {
    background-color: var(--bg-card) !important;
    color: var(--text-main) !important;
    border: 1.5px solid #495057 !important;
}
.btn-filtro-todos.active,
.btn-filtro-todos:hover {
    background-color: #212529 !important;
    color: #ffffff !important;
    border-color: #212529 !important;
}
.btn-filtro-impar {
    background-color: var(--bg-card) !important;
    color: #997404 !important;
    border: 1.5px solid #ffc107 !important;
}
.btn-filtro-impar.active,
.btn-filtro-impar:hover {
    background-color: #ffc107 !important;
    color: #1a1a1a !important;
    border-color: #ffc107 !important;
}
.btn-filtro-par {
    background-color: var(--bg-card) !important;
    color: #dc3545 !important;
    border: 1.5px solid #dc3545 !important;
}
.btn-filtro-par.active,
.btn-filtro-par:hover {
    background-color: #dc3545 !important;
    color: #ffffff !important;
    border-color: #dc3545 !important;
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
