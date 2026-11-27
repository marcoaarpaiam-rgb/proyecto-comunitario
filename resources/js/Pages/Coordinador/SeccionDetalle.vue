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
                        >Sección {{ seccion.sec_codigo }}
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a
                        href="/coordinador/secciones"
                        class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                        ><i class="bi bi-arrow-left me-1"></i>Volver</a
                    >
                    <button class="tb-btn" @click="toggleTema">
                        <i
                            class="bi"
                            :class="
                                tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'
                            "
                        ></i>
                    </button>
                </div>
            </div>

            <div class="content">
                <div class="sc mb-3">
                    <div class="sh">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-person-badge me-2 text-danger"></i>
                            <h6 class="mb-0">Profesor de proyecto</h6>
                        </div>
                    </div>
                    <div class="p-3 d-flex flex-wrap align-items-center gap-2">
                        <div class="me-auto">
                            <div v-if="profesorActual" class="fw-bold">
                                {{ profesorActual.usu_primer_nombre }}
                                {{ profesorActual.usu_primer_apellido }}
                            </div>
                            <div v-else style="color: var(--text-muted)">
                                Sin profesor asignado
                            </div>
                        </div>
                        <div style="min-width: 260px;">
                            <SelectBuscable
                                v-model="profesorSel"
                                :options="opcionesProfesoresDetalle"
                                size="sm"
                                placeholder="Seleccionar profesor..."
                                search-placeholder="Buscar profesor o C.I...."
                            />
                        </div>
                        <button
                            class="btn btn-sm btn-danger fw-bold"
                            :disabled="!profesorSel || loadingAsignar"
                            @click="asignarProfesor"
                        >
                            <span v-if="loadingAsignar" class="spinner-border spinner-border-sm me-1"></span>
                            Asignar
                        </button>
                    </div>

                    <div v-if="mensajeAsignado" class="alert alert-success alert-dismissible fade show py-2 px-3 small mt-2 mb-0">
                        <i class="bi bi-check-circle-fill me-1"></i> {{ mensajeAsignado }}
                        <button type="button" class="btn-close p-2" @click="mensajeAsignado = ''"></button>
                    </div>

                    <!-- Botones de Descarga Excel Oficiales -->
                    <div class="d-flex align-items-center gap-2 flex-wrap mt-3 pt-3 border-top" style="border-color: var(--border) !important;">
                        <span class="text-muted small fw-bold"><i class="bi bi-file-earmark-spreadsheet text-success me-1"></i> Formatos Excel Oficiales UPTP:</span>
                        <a :href="`/coordinador/secciones/${seccion.sec_id}/excel/vinculacion`" class="btn btn-sm btn-outline-success fw-bold rounded-pill px-3">
                            <i class="bi bi-download me-1"></i> Planilla de Vinculación (.xlsx)
                        </a>
                    </div>
                </div>
                <!-- Info de la sección -->
                <div class="info-card">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="info-row">
                                <span class="info-label">Código</span
                                ><strong style="color: var(--rojo)">{{
                                    seccion.sec_codigo
                                }}</strong>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-row">
                                <span class="info-label">Trayecto</span
                                ><span>{{ seccion.trayecto?.tra_nombre }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-row">
                                <span class="info-label">Turno</span
                                ><span>{{ seccion.turno?.tur_nombre }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-row">
                                <span class="info-label">Equipos</span
                                ><strong>{{ equipos.length }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Diagnóstico y Situación del Proyecto de Equipos (Filtros Compactos) -->
                <div v-if="diagnosticosEquipos && Object.keys(diagnosticosEquipos).length > 0" class="card border-0 shadow-sm p-3 mb-3 bg-white">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fs-5">🧭</span>
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">Ruta Crítica y Situación del Proyecto</h6>
                                <small class="text-muted" style="font-size: 0.75rem">Semana {{ semanaActual }} &bull; Haz clic en los filtros para filtrar equipos</small>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <button
                            class="btn btn-sm"
                            :class="filtroSemaforo === '' ? 'btn-danger' : 'btn-outline-secondary'"
                            @click="filtroSemaforo = ''"
                        >
                            Todos ({{ equipos.length }})
                        </button>
                        <button
                            class="btn btn-sm"
                            :class="filtroSemaforo === 'conforme' || filtroSemaforo === 'en_ruta' ? 'btn-success' : 'btn-outline-success'"
                            @click="filtroSemaforo = (filtroSemaforo === 'conforme' || filtroSemaforo === 'en_ruta') ? '' : 'conforme'"
                            title="Conforme: Cumple con los estándares y metas."
                        >
                            🟢 Conforme ({{ contarPorSemaforo('conforme') }})
                        </button>
                        <button
                            class="btn btn-sm"
                            :class="filtroSemaforo === 'observado' || filtroSemaforo === 'acelerar' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark'"
                            @click="filtroSemaforo = (filtroSemaforo === 'observado' || filtroSemaforo === 'acelerar') ? '' : 'observado'"
                            title="Observado: Tiene parametros menores que deben mejorarse."
                        >
                            🟡 Observado ({{ contarPorSemaforo('observado') }})
                        </button>
                        <button
                            class="btn btn-sm"
                            :class="filtroSemaforo === 'no_conforme' || filtroSemaforo === 'riesgo_alto' ? 'btn-danger' : 'btn-outline-danger'"
                            @click="filtroSemaforo = (filtroSemaforo === 'no_conforme' || filtroSemaforo === 'riesgo_alto') ? '' : 'no_conforme'"
                            title="No conforme: Incumplimiento grave de los parámetros del proyecto."
                        >
                            🔴 No conforme ({{ contarPorSemaforo('no_conforme') }})
                        </button>
                        <button
                            class="btn btn-sm"
                            :class="filtroSemaforo === 'no_viable' || filtroSemaforo === 'inviable' ? 'btn-dark' : 'btn-outline-dark'"
                            @click="filtroSemaforo = (filtroSemaforo === 'no_viable' || filtroSemaforo === 'inviable') ? '' : 'no_viable'"
                            title="No viable: Cierre administrativo por falta de factibilidad."
                        >
                            ⚫ No viable ({{ contarPorSemaforo('no_viable') }})
                        </button>
                    </div>
                </div>

                <!-- Sin equipos -->
                <div v-if="equiposFiltrados.length === 0" class="sc">
                    <div
                        class="p-5 text-center"
                        style="color: var(--text-muted)"
                    >
                        <i class="bi bi-people fs-1 d-block mb-3"></i>No hay
                        equipos {{ filtroSemaforo ? 'con la Situación del Proyecto seleccionada' : 'en esta sección' }}
                    </div>
                </div>

                <!-- Equipos -->
                <div v-for="equ in equiposFiltrados" :key="equ.equ_id" class="sc">
                    <div class="sh">
                        <div>
                            <span
                                style="
                                    font-weight: 800;
                                    color: var(--rojo);
                                    font-size: 1rem;
                                "
                                >{{ codigoLimpio(equ.equ_codigo) }}</span
                            >
                            <span
                                style="
                                    color: var(--text-muted);
                                    font-size: 0.85rem;
                                    margin-left: 0.5rem;
                                "
                                >{{ equ.equ_titulo }}</span
                            >
                            <!-- Badge Compacta de Semáforo -->
                            <span
                                v-if="diagnosticosEquipos?.[equ.equ_id]"
                                class="badge ms-2"
                                :class="getIconoSemaforo(diagnosticosEquipos[equ.equ_id]?.semaforo)?.class"
                            >
                                {{ getIconoSemaforo(diagnosticosEquipos[equ.equ_id]?.semaforo)?.icon }}
                                {{ getEtiquetaSemaforo(diagnosticosEquipos[equ.equ_id]?.semaforo) }}
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span
                                v-if="equ.proyecto_comunidad?.comunidad"
                                style="
                                    font-size: 0.82rem;
                                    color: var(--text-muted);
                                "
                            >
                                <i class="bi bi-building me-1"></i
                                >{{
                                    equ.proyecto_comunidad.comunidad.com_nombre
                                }}
                            </span>
                            <span
                                v-if="equ.resultado_proyecto"
                                class="bs"
                                :class="
                                    colorRes(
                                        equ.resultado_proyecto?.estado_proyecto
                                            ?.epr_nombre,
                                    )
                                "
                            >
                                {{
                                    equ.resultado_proyecto?.estado_proyecto
                                        ?.epr_nombre
                                }}
                            </span>
                            <span v-else class="bs bsec">Sin resultado</span>
                            <a
                                :href="`/coordinador/equipos/${equ.equ_id}/expediente`"
                                class="btn btn-sm btn-outline-info rounded-pill px-2 d-inline-flex align-items-center gap-1"
                                title="Ver expediente y diagnóstico completo"
                            >
                                <i class="bi bi-folder2-open"></i>
                                <span class="d-none d-sm-inline">Expediente</span>
                            </a>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="ct">
                            <thead>
                                <tr>
                                    <th class="ps-4">Integrante</th>
                                    <th>Cédula</th>
                                    <th>Rol</th>
                                    <th>Acceso al Sistema</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="int in equ.integrantes"
                                    :key="int.ein_id"
                                >
                                    <td class="ps-4 fw-semibold">
                                        {{ int.usuario?.usu_primer_nombre }}
                                        {{ int.usuario?.usu_primer_apellido }}
                                    </td>
                                    <td style="color: var(--text-muted)">
                                        {{ int.usuario?.usu_cedula }}
                                    </td>
                                    <td>
                                        <span
                                            class="bs"
                                            :class="
                                                int.ein_es_lider ? 'bd' : 'bsec'
                                            "
                                            >{{
                                                int.ein_es_lider
                                                    ? "Líder"
                                                    : "Integrante"
                                            }}</span
                                        >
                                    </td>
                                    <td>
                                        <span
                                            v-if="int.usuario?.usu_tiene_acceso"
                                            class="bs ba"
                                            ><i class="bi bi-check me-1"></i>Con
                                            acceso</span
                                        >
                                        <span v-else class="bs bsec"
                                            >Sin acceso</span
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import SemaforoEquipo from "@/Components/SemaforoEquipo.vue";
import SidebarCoordinador from "@/Components/SidebarCoordinador.vue";

const props = defineProps({
    seccion: Object,
    equipos: Array,
    profesorActual: Object,
    profesores: Array,
    diagnosticosEquipos: { type: Object, default: () => ({}) },
    semanaActual: { type: Number, default: 1 },
});

const filtroSemaforo = ref("");

const contarPorSemaforo = (tipo) => {
    if (!props.equipos || !props.diagnosticosEquipos) return 0;
    return props.equipos.filter((eq) => {
        const d = props.diagnosticosEquipos[eq.equ_id];
        if (!d) return false;
        if (tipo === 'conforme' || tipo === 'en_ruta') return d.semaforo === 'conforme' || d.semaforo === 'en_ruta';
        if (tipo === 'observado' || tipo === 'acelerar') return d.semaforo === 'observado' || d.semaforo === 'acelerar';
        if (tipo === 'no_conforme' || tipo === 'riesgo_alto') return d.semaforo === 'no_conforme' || d.semaforo === 'riesgo_alto';
        if (tipo === 'no_viable' || tipo === 'inviable') return d.semaforo === 'no_viable' || d.semaforo === 'inviable';
        return d.semaforo === tipo;
    }).length;
};

const getIconoSemaforo = (tipo) => {
    switch (tipo) {
        case "en_ruta":
        case "conforme":
            return { icon: "🟢", class: "bg-success text-white" };
        case "acelerar":
        case "observado":
            return { icon: "🟡", class: "bg-warning text-dark" };
        case "riesgo_alto":
        case "no_conforme":
            return { icon: "🔴", class: "bg-danger text-white" };
        case "inviable":
        case "no_viable":
            return { icon: "⚫", class: "bg-dark text-white" };
        default:
            return { icon: "⚪", class: "bg-secondary text-white" };
    }
};

const getEtiquetaSemaforo = (tipo) => {
    switch (tipo) {
        case "en_ruta":
        case "conforme":
            return "Conforme";
        case "acelerar":
        case "observado":
            return "Observado";
        case "riesgo_alto":
        case "no_conforme":
            return "No conforme";
        case "inviable":
        case "no_viable":
            return "No viable";
        default:
            return "Pendiente";
    }
};

const getSignificadoSemaforo = (tipo) => {
    switch (tipo) {
        case "en_ruta":
        case "conforme":
            return "Cumple con los estándares y metas.";
        case "acelerar":
        case "observado":
            return "Tiene parametros menores que deben mejorarse.";
        case "riesgo_alto":
        case "no_conforme":
            return "Incumplimiento grave de los parámetros del proyecto.";
        case "inviable":
        case "no_viable":
            return "Cierre administrativo por falta de factibilidad.";
        default:
            return "Pendiente de evaluación.";
    }
};

const equiposFiltrados = computed(() => {
    if (!filtroSemaforo.value) return props.equipos || [];
    return (props.equipos || []).filter((eq) => {
        const d = props.diagnosticosEquipos?.[eq.equ_id];
        return d && d.semaforo === filtroSemaforo.value;
    });
});

const profesorSel = ref("");
const opcionesProfesoresDetalle = computed(() => [
    { value: "", label: "Seleccionar profesor..." },
    ...(props.profesores || []).map((p) => ({
        value: p.usu_id,
        label: `${p.usu_primer_nombre} ${p.usu_primer_apellido}`,
        sublabel: `C.I. ${p.usu_cedula}`,
    })),
]);
const loadingAsignar = ref(false);
const mensajeAsignado = ref("");
const asignarProfesor = () => {
    if (!profesorSel.value) return;
    loadingAsignar.value = true;
    mensajeAsignado.value = "";
    router.post(
        `/coordinador/secciones/${props.seccion.sec_id}/profesores`,
        { pse_id_usu: profesorSel.value },
        {
            preserveScroll: true,
            onSuccess: () => {
                profesorSel.value = "";
                loadingAsignar.value = false;
                mensajeAsignado.value = "Profesor asignado exitosamente a la sección.";
            },
            onError: (err) => {
                loadingAsignar.value = false;
                const msg = Object.values(err)[0] || 'Error al asignar profesor';
                alert(msg);
            },
        },
    );
};
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
const colorRes = (n) => {
    if (n === "Aprobado") return "ba";
    if (n === "Aplazado") return "bw";
    return "bd";
};
const codigoLimpio = (codigo) => {
    if (!codigo) return "";
    return String(codigo).replace(/#DIS\d+$/, "");
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
.ct {
    width: 100%;
    border-collapse: collapse;
}
.ct thead th {
    padding: 0.65rem 1rem;
    background: var(--bg-th);
    color: var(--text-muted);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid var(--border);
}
.ct tbody td {
    padding: 0.65rem 1rem;
    border-bottom: 1px solid var(--border);
    color: var(--text-main);
    font-size: 0.88rem;
}
.ct tbody tr:hover td {
    background: var(--bg-hover);
}
.bs {
    padding: 4px 12px;
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
.bp {
    background: #cff4fc;
    color: #055160;
    border: 1px solid #b6effb;
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
[data-theme="dark"] .bsec {
    background: #252830;
    color: #f1f3f5;
    border-color: #2d3139;
}
.info-card {
    background: var(--bg-card);
    border-radius: 13px;
    border: 1px solid var(--border);
    padding: 1.2rem 1.4rem;
    margin-bottom: 1.2rem;
}
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 0.55rem 0;
    border-bottom: 1px solid var(--border);
    font-size: 0.9rem;
}
.info-row:last-child {
    border-bottom: none;
}
.info-label {
    color: var(--text-muted);
    font-weight: 700;
    font-size: 0.85rem;
}
.chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    background: var(--bg-th);
    border: 1px solid var(--border);
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-main);
    margin: 2px;
}
.chip-dot {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    font-weight: 800;
    color: #fff;
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
