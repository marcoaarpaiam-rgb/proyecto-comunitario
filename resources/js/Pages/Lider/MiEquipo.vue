<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
        "
    >
        <!-- SIDEBAR LIDER UNIFICADO -->
        <SidebarLider :sidebar-open="sidebarOpen" @close="sidebarOpen = false" />

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
                            class="bi bi-people me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Mi Equipo
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
                <div style="margin-bottom: 1.25rem">
                    <h4
                        style="
                            font-weight: 800;
                            font-size: 1.25rem;
                            color: var(--text-main);
                            margin: 0;
                        "
                    >
                        Mi Equipo
                    </h4>
                    <p
                        style="
                            color: var(--text-muted);
                            font-size: 0.85rem;
                            margin-top: 2px;
                        "
                    >
                        Integrantes del equipo de proyecto
                    </p>
                </div>

                <!-- Alertas de errores y mensajes flash -->
                <div v-if="$page.props.flash?.error || Object.keys($page.props.errors || {}).length" class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span v-if="$page.props.flash?.error">{{ $page.props.flash.error }}</span>
                    <ul v-else class="mb-0 ps-3">
                        <li v-for="(err, k) in $page.props.errors" :key="k">{{ err }}</li>
                    </ul>
                </div>
                <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ $page.props.flash.success }}
                </div>

                <div v-if="!equipo" class="sc">
                    <div
                        class="p-5 text-center"
                        style="color: var(--text-muted)"
                    >
                        <i class="bi bi-people fs-1 d-block mb-3"></i>No tienes
                        equipo asignado
                    </div>
                </div>

                <div v-else>
                    <!-- BLOQUE 6: Panel de Acompañamiento, Semáforo y Transparencia del Líder -->
                    <div v-if="diagnostico" class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <span>📌</span> Estado del Proyecto y Ruta Crítica
                            </h5>
                        </div>
                        <SemaforoEquipo :diagnostico="diagnostico" />

                        <!-- Acuerdos y compromisos pendientes -->
                        <div v-if="acuerdosPendientes && acuerdosPendientes.length > 0" class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-warning bg-opacity-10 border-0 py-2 d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-dark small">
                                    <i class="bi bi-list-check me-1 text-warning"></i> Compromisos y Correcciones Pendientes ({{ acuerdosPendientes.length }})
                                </span>
                            </div>
                            <div class="card-body p-2">
                                <ul class="list-group list-group-flush">
                                    <li
                                        v-for="ac in acuerdosPendientes"
                                        :key="ac.acue_id"
                                        class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center border-0 border-bottom"
                                    >
                                        <div>
                                            <div class="small fw-semibold text-dark">{{ ac.acue_descripcion }}</div>
                                            <small v-if="ac.acue_fecha_limite" class="text-muted">
                                                <i class="bi bi-clock me-1"></i> Límite: {{ ac.acue_fecha_limite }}
                                            </small>
                                        </div>
                                        <span :class="ac.acue_estado === 'en_proceso' ? 'badge bg-info text-dark' : 'badge bg-warning text-dark'">
                                            {{ ac.acue_estado === 'en_proceso' ? 'En proceso' : 'Pendiente' }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- BLOQUE DE TRANSPARENCIA GRUPAL: Asistencias, Participaciones y Traza de Auditoría -->
                        <div class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-white border-bottom py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                        <i class="bi bi-shield-check text-success fs-5"></i>
                                        <span>Transparencia Grupal del Equipo</span>
                                    </h6>
                                    <small class="text-muted">
                                        Supervisión de asistencias, participación activa y traza de modificaciones docentes de todos los integrantes.
                                    </small>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <label class="small text-muted fw-semibold mb-0 d-none d-sm-inline">Filtrar:</label>
                                    <select v-model="filtroIntegrante" class="form-select form-select-sm" style="min-width: 190px;">
                                        <option value="todos">Todos los integrantes ({{ resumenIntegrantes.length }})</option>
                                        <option v-for="int in resumenIntegrantes" :key="int.usu_id" :value="int.usu_id">
                                            {{ int.nombre }} {{ int.es_lider ? '(Líder)' : '' }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Tarjetas resumen por integrante -->
                            <div v-if="resumenIntegrantes && resumenIntegrantes.length > 0" class="card-body bg-light border-bottom p-3">
                                <div class="row g-2">
                                    <div v-for="int in resumenIntegrantes" :key="int.usu_id" class="col-md-4 col-sm-6">
                                        <div class="bg-white p-2 rounded border shadow-sm h-100">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <div>
                                                    <span class="fw-bold text-dark small d-block text-truncate" style="max-width: 140px;">{{ int.nombre }}</span>
                                                    <span class="text-muted" style="font-size: 0.72rem;">C.I. {{ int.cedula }}</span>
                                                </div>
                                                <span v-if="int.es_lider" class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle" style="font-size: 0.65rem;">Líder</span>
                                                <span v-else class="badge bg-light text-muted border" style="font-size: 0.65rem;">Integrante</span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top" style="font-size: 0.75rem;">
                                                <span>Asistencia:</span>
                                                <span :class="int.porcentaje_asistencia >= 85 ? 'fw-bold text-success' : (int.porcentaje_asistencia >= 75 ? 'fw-bold text-warning' : 'fw-bold text-danger')">
                                                    {{ int.porcentaje_asistencia }}% ({{ int.asistencias + int.justificados }}/{{ int.total_encuentros }})
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between text-muted" style="font-size: 0.72rem;">
                                                <span>Participaciones:</span>
                                                <span class="fw-semibold text-primary">{{ int.participaciones }} activa(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tabla detallada de asistencias y traza de modificaciones -->
                            <div class="card-body p-0">
                                <div v-if="listaAsistencias.length === 0" class="p-4 text-center text-muted small">
                                    <i class="bi bi-calendar-x fs-4 d-block mb-2"></i>
                                    No se registran encuentros de control de asistencia para los criterios seleccionados.
                                </div>
                                <div v-else class="table-responsive">
                                    <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.82rem">
                                        <thead class="table-light">
                                            <tr>
                                                <th class="ps-3">Integrante</th>
                                                <th>Punto de Control</th>
                                                <th>Fecha</th>
                                                <th>Asistencia</th>
                                                <th>Participación</th>
                                                <th class="pe-3" style="min-width: 200px;">Traza de Auditoría / Edición</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="asist in listaAsistencias" :key="asist.apc_id">
                                                <td class="ps-3">
                                                    <div class="fw-bold text-dark">{{ asist.estudiante || 'Estudiante' }}</div>
                                                    <small class="text-muted" style="font-size: 0.72rem;">C.I. {{ asist.cedula }}</small>
                                                </td>
                                                <td>
                                                    <div class="fw-semibold">{{ asist.punto_control }}</div>
                                                    <small v-if="asist.semana" class="badge bg-secondary bg-opacity-10 text-secondary" style="font-size: 0.65rem;">
                                                        Semana {{ asist.semana }}
                                                    </small>
                                                </td>
                                                <td class="text-muted text-nowrap">
                                                    {{ asist.fecha }}
                                                </td>
                                                <td>
                                                    <span v-if="asist.asistio" class="badge bg-success">
                                                        <i class="bi bi-check-lg me-1"></i> Presente
                                                    </span>
                                                    <span v-else-if="asist.justificado" class="badge bg-warning text-dark">
                                                        <i class="bi bi-file-earmark-medical me-1"></i> Justificado
                                                    </span>
                                                    <span v-else class="badge bg-danger">
                                                        <i class="bi bi-x-lg me-1"></i> Ausente
                                                    </span>
                                                </td>
                                                <td>
                                                    <span v-if="asist.participo" class="badge bg-primary" title="Participación activa verificada por el docente">
                                                        <i class="bi bi-hand-thumbs-up-fill me-1"></i> Participó
                                                    </span>
                                                    <span v-else class="text-muted small">
                                                        —
                                                    </span>
                                                </td>
                                                <td class="pe-3">
                                                    <div v-if="asist.updated_at || asist.motivo_edicion" class="p-1 px-2 rounded bg-warning bg-opacity-10 border border-warning-subtle text-dark" style="font-size: 0.72rem;">
                                                        <div class="d-flex align-items-center gap-1 text-warning-emphasis fw-bold">
                                                            <i class="bi bi-pencil-square"></i>
                                                            <span>Modificada el {{ asist.updated_at }}</span>
                                                        </div>
                                                        <div v-if="asist.editado_por" class="text-muted mt-0" style="font-size: 0.7rem;">
                                                            Por: {{ asist.editado_por }}
                                                        </div>
                                                        <div v-if="asist.motivo_edicion" class="mt-1 small fst-italic">
                                                            "{{ asist.motivo_edicion }}"
                                                        </div>
                                                    </div>
                                                    <span v-else class="text-muted small" style="font-size: 0.72rem;">
                                                        <i class="bi bi-check2 text-success me-1"></i>Registro original sin cambios
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sc mb-3">
                        <div class="sh">
                            <h6>
                                <i
                                    class="bi bi-info-circle text-danger me-2"
                                ></i
                                >Datos del Equipo
                            </h6>
                        </div>
                        <div class="p-3">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <div
                                        style="
                                            font-size: 0.78rem;
                                            color: var(--text-muted);
                                            font-weight: 700;
                                        "
                                    >
                                        Código
                                    </div>
                                    <div
                                        class="fw-bold"
                                        style="color: var(--rojo)"
                                    >
                                        {{ equipo.equ_codigo }}
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div
                                        style="
                                            font-size: 0.78rem;
                                            color: var(--text-muted);
                                            font-weight: 700;
                                        "
                                    >
                                        Título
                                    </div>
                                    <div
                                        class="fw-semibold"
                                        style="font-size: 0.9rem"
                                    >
                                        {{ equipo.equ_titulo }}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div
                                        style="
                                            font-size: 0.78rem;
                                            color: var(--text-muted);
                                            font-weight: 700;
                                        "
                                    >
                                        Sección
                                    </div>
                                    <div>
                                        <span
                                            class="bs"
                                            style="
                                                background: rgba(
                                                    13,
                                                    110,
                                                    253,
                                                    0.1
                                                );
                                                color: #055160;
                                                border: 1px solid
                                                    rgba(13, 110, 253, 0.2);
                                            "
                                            >{{
                                                equipo.seccion?.sec_codigo
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div
                                        style="
                                            font-size: 0.78rem;
                                            color: var(--text-muted);
                                            font-weight: 700;
                                        "
                                    >
                                        Trayecto
                                    </div>
                                    <div style="font-size: 0.88rem">
                                        {{ equipo.trayecto?.tra_nombre }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sc">
                        <div class="sh">
                            <h6>
                                <i
                                    class="bi bi-people-fill text-danger me-2"
                                ></i
                                >Integrantes
                                <span
                                    class="ms-2"
                                    style="
                                        font-size: 0.82rem;
                                        color: var(--text-muted);
                                        font-weight: 500;
                                    "
                                    >({{ (equipo.integrantes || []).filter(i => i.ein_status !== false).length }} /
                                    6)</span
                                >
                            </h6>
                        </div>
                        <div
                            v-for="int in (equipo.integrantes || []).filter(i => i.ein_status !== false)"
                            :key="int.ein_id"
                            class="miembro-card"
                        >
                            <div
                                class="av"
                                :style="{
                                    background: int.ein_es_lider
                                        ? '#dc3545'
                                        : '#495057',
                                }"
                            >
                                {{ int.usuario?.usu_primer_nombre?.[0]
                                }}{{ int.usuario?.usu_primer_apellido?.[0] }}
                            </div>
                            <div class="flex-grow-1">
                                <div
                                    class="fw-semibold"
                                    style="font-size: 0.92rem"
                                >
                                    {{ int.usuario?.usu_primer_nombre }}
                                    {{ int.usuario?.usu_primer_apellido }}
                                </div>
                                <div
                                    style="
                                        font-size: 0.78rem;
                                        color: var(--text-muted);
                                    "
                                >
                                    CI: {{ int.usuario?.usu_cedula }}
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span v-if="int.ein_es_lider" class="bs bd"
                                    ><i
                                        class="bi bi-star-fill me-1"
                                        style="font-size: 0.65rem"
                                    ></i
                                    >Líder</span
                                >
                                <span v-else class="bs bsec">Integrante</span>
                                <span
                                    v-if="int.usuario?.usu_tiene_acceso"
                                    class="bs ba"
                                    style="font-size: 0.68rem"
                                    ><i class="bi bi-check me-1"></i>Con
                                    acceso</span
                                >
                            </div>
                        </div>
                    </div>
                    <!-- Tutor del proyecto -->
                    <div class="sc mt-3">
                        <div
                            class="sh d-flex align-items-center justify-content-between"
                        >
                            <span
                                ><i
                                    class="bi bi-person-badge me-2 text-danger"
                                ></i
                                >Tutor del Proyecto</span
                            >
                            <button
                                class="btn btn-sm btn-danger rounded-pill fw-bold"
                                @click="mostrarFormTutor = !mostrarFormTutor"
                            >
                                <i class="bi bi-pencil me-1"></i>
                                {{ tutor ? "Cambiar" : "Asignar" }}
                            </button>
                        </div>
                        <div class="p-3">
                            <!-- Tutor actual -->
                            <div v-if="tutor && !mostrarFormTutor">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        style="
                                            width: 42px;
                                            height: 42px;
                                            background: #dc3545;
                                            border-radius: 50%;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            color: #fff;
                                            font-weight: 800;
                                            font-size: 1rem;
                                            flex-shrink: 0;
                                        "
                                    >
                                        {{
                                            tutor.usuario
                                                ?.usu_primer_nombre?.[0]
                                        }}{{
                                            tutor.usuario
                                                ?.usu_primer_apellido?.[0]
                                        }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">
                                            {{
                                                tutor.usuario?.usu_primer_nombre
                                            }}
                                            {{
                                                tutor.usuario
                                                    ?.usu_primer_apellido
                                            }}
                                        </div>
                                        <div
                                            class="small"
                                            style="color: var(--text-muted)"
                                        >
                                            C.I.
                                            {{ tutor.usuario?.usu_cedula }} —
                                            Profesor de Proyecto
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sin tutor -->
                            <div
                                v-else-if="!tutor && !mostrarFormTutor"
                                class="text-center py-3"
                                style="color: var(--text-muted)"
                            >
                                <i
                                    class="bi bi-person-badge fs-3 d-block mb-1"
                                ></i>
                                No hay tutor asignado
                            </div>

                            <!-- Formulario -->
                            <div v-if="mostrarFormTutor">
                                <label class="form-label small fw-bold"
                                    >Selecciona el tutor</label
                                >
                                <select
                                    class="form-select form-select-sm mb-3"
                                    v-model="formTutor.tut_id_usu"
                                >
                                    <option value="">
                                        — Seleccionar profesor —
                                    </option>
                                    <option
                                        v-for="prof in profesoresDisponibles"
                                        :key="prof.usu_id"
                                        :value="prof.usu_id"
                                    >
                                        {{ prof.usu_primer_nombre }}
                                        {{ prof.usu_primer_apellido }} (C.I.
                                        {{ prof.usu_cedula }})
                                    </option>
                                </select>
                                <p
                                    class="small mb-3"
                                    style="color: var(--text-muted)"
                                >
                                    <i class="bi bi-info-circle me-1"></i>
                                    Solo se muestran los profesores del PNF
                                    Informática registrados en el sistema.
                                    Cuando se integre el SOGAC, esta lista se
                                    completará automáticamente.
                                </p>
                                <div class="d-flex gap-2">
                                    <button
                                        class="btn btn-sm btn-danger fw-bold"
                                        @click="guardarTutor"
                                    >
                                        <i class="bi bi-check2 me-1"></i>
                                        Guardar
                                    </button>
                                    <button
                                        class="btn btn-sm btn-outline-secondary"
                                        @click="mostrarFormTutor = false"
                                    >
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
import { ref, computed, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import ModalConfirmar from "@/Components/ModalConfirmar.vue";
import SemaforoEquipo from "@/Components/SemaforoEquipo.vue";
import SidebarLider from "@/Components/SidebarLider.vue";

const props = defineProps({
    equipo: Object,
    tutor: Object,
    profesoresDisponibles: Array,
    diagnostico: { type: Object, default: null },
    acuerdosPendientes: { type: Array, default: () => [] },
    misAsistencias: { type: Array, default: () => [] },
    asistenciasGrupo: { type: Array, default: () => [] },
    resumenIntegrantes: { type: Array, default: () => [] },
});
const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("lid-theme") || "light");
const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("lid-theme", tema.value);
};

const filtroIntegrante = ref("todos");
const listaAsistencias = computed(() => {
    const list = props.asistenciasGrupo && props.asistenciasGrupo.length > 0
        ? props.asistenciasGrupo
        : props.misAsistencias;
    if (filtroIntegrante.value === "todos") {
        return list;
    }
    return list.filter((a) => String(a.usu_id) === String(filtroIntegrante.value));
});

const modalLogout = ref(null);
const logout = () => {
    modalLogout.value?.abrir();
};
const ejecutarLogout = () => {
    router.post("/logout");
};
const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});
const mostrarFormTutor = ref(false);
const formTutor = ref({ tut_id_usu: props.tutor?.tut_id_usu ?? "" });

const guardarTutor = () => {
    router.post("/lider/tutor", formTutor.value, {
        preserveScroll: true,
        onSuccess: () => {
            mostrarFormTutor.value = false;
        },
    });
};
const notifNoLeidas = ref(0);
onMounted(() => {
    fetch("/notificaciones/lista")
        .then((r) => r.json())
        .then((d) => {
            notifNoLeidas.value = d.filter((n) => !n.not_leida).length;
        })
        .catch(() => {});
});
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
    margin-bottom: 1.2rem;
}
.sh {
    padding: 1rem 1.3rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 8px;
}
.sh h6 {
    margin: 0;
    font-weight: 800;
    color: var(--text-main);
    font-size: 0.97rem;
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
[data-theme="dark"] .bsec {
    background: #252830;
    color: #f1f3f5;
    border-color: #2d3139;
}
.miembro-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0.85rem 1.3rem;
    border-bottom: 1px solid var(--border);
}
.miembro-card:last-child {
    border-bottom: none;
}
.av {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.85rem;
    color: #fff;
    flex-shrink: 0;
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
