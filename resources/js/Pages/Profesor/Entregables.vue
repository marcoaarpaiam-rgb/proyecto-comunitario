<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
        "
    >
        <SidebarProfesor
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
                            class="bi bi-file-earmark-check me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Entregables
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
                            Entregables
                        </h4>
                        <p
                            style="color:var(--text-muted);font-size:.85rem;margin-top:2px;"
                        >
                            <span
                                v-if="pendientes > 0"
                                style="color: #dc3545; font-weight: 700"
                                >{{ pendientes }} pendiente(s) por revisar</span
                            ><span v-else>Todos los entregables revisados</span>
                        </p>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <div style="min-width: 240px;">
                                <SelectBuscable
                                    v-model="equipoFiltro"
                                    :options="opcionesEquiposFiltro"
                                    size="sm"
                                    placeholder="Todos los equipos"
                                    search-placeholder="Buscar equipo..."
                                />
                            </div>
                            <button
                                class="btn btn-sm btn-outline-secondary rounded-pill px-3 fw-semibold"
                                @click="mostrarCatalogo = !mostrarCatalogo"
                            >
                                <i class="bi bi-journal-bookmark me-1 text-primary"></i>
                                {{ mostrarCatalogo ? 'Ocultar Lista de Verificación' : 'Ver Lista de Verificación de Recaudos' }}
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Catálogo Oficial PNFI Desplegable con Lista de Verificación de Recaudos -->
                <div v-if="mostrarCatalogo" class="sc mb-4 p-3" style="border-left: 4px solid #0d6efd;">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-primary">
                                <i class="bi bi-card-checklist me-2"></i>Lista de Verificación de Recaudos Oficiales PNFI
                            </h6>
                            <p class="text-muted small mb-0">
                                {{ equipoFiltro ? 'Mostrando estado de cumplimiento del equipo seleccionado' : 'Selecciona un equipo arriba para ver la lista de verificación de recaudos individualizada' }}
                            </p>
                        </div>
                        <span class="badge bg-primary">{{ catalogoFiltrado.length }} entregables en catálogo</span>
                    </div>
                    <div class="table-responsive mt-2">
                        <table class="table table-sm align-middle mb-0" style="font-size: 0.84rem;">
                            <thead class="table-light">
                                <tr>
                                    <th>Trayecto</th>
                                    <th>Sem.</th>
                                    <th>Entregable Oficial</th>
                                    <th>Semanas Sugeridas</th>
                                    <th>Obligatorio</th>
                                    <th v-if="equipoFiltro" class="text-center">Estado del Equipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cep in catalogoFiltrado" :key="cep.cep_id">
                                    <td class="fw-semibold">
                                        {{ cep.trayecto?.tra_nombre || ('Trayecto ' + (cep.cep_id_trayecto || '')) }}
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary">Semestre {{ cep.cep_semestre }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-dark">{{ cep.cep_nombre }}</div>
                                        <div v-if="cep.cep_descripcion" class="text-muted small">{{ cep.cep_descripcion }}</div>
                                    </td>
                                    <td>
                                        <span v-if="cep.cep_semana_inicio_sugerida && cep.cep_semana_limite_sugerida">
                                            Sem. {{ cep.cep_semana_inicio_sugerida }} a {{ cep.cep_semana_limite_sugerida }}
                                        </span>
                                        <span v-else class="text-muted">-</span>
                                    </td>
                                    <td>
                                        <span v-if="cep.cep_es_obligatorio" class="badge bg-danger-subtle text-danger">Obligatorio</span>
                                        <span v-else class="badge bg-light text-muted border">Opcional</span>
                                    </td>
                                    <td v-if="equipoFiltro" class="text-center">
                                        <span :class="['badge', getEstadoCatalogoEquipo(cep.cep_id, equipoFiltro).badge]">
                                            {{ getEstadoCatalogoEquipo(cep.cep_id, equipoFiltro).label }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TABS DE NAVEGACIÓN: PENDIENTES VS ARCHIVADOS -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <ul class="nav nav-pills gap-2" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-bold d-flex align-items-center gap-2 rounded-pill px-3 py-2"
                                :class="tabEntregables === 'pendientes' ? 'active bg-danger text-white shadow-sm' : 'btn-outline-secondary'"
                                type="button"
                                @click="tabEntregables = 'pendientes'"
                            >
                                <i class="bi bi-clock-history"></i>
                                <span>Pendientes por Revisar</span>
                                <span
                                    class="badge rounded-pill"
                                    :class="tabEntregables === 'pendientes' ? 'bg-white text-danger' : 'bg-secondary text-white'"
                                >
                                    {{ totalPendientes }}
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-bold d-flex align-items-center gap-2 rounded-pill px-3 py-2"
                                :class="tabEntregables === 'archivados' ? 'active bg-danger text-white shadow-sm' : 'btn-outline-secondary'"
                                type="button"
                                @click="tabEntregables = 'archivados'"
                            >
                                <i class="bi bi-archive-fill"></i>
                                <span>Revisados / Archivados</span>
                                <span
                                    class="badge rounded-pill"
                                    :class="tabEntregables === 'archivados' ? 'bg-white text-danger' : 'bg-secondary text-white'"
                                >
                                    {{ totalArchivados }}
                                </span>
                            </button>
                        </li>
                    </ul>

                    <div v-if="equipoFiltro" class="d-flex align-items-center gap-2">
                        <Link
                            :href="`/profesor/equipos/${equipoFiltro}/expediente`"
                            class="btn btn-sm btn-outline-info rounded-pill px-3 fw-semibold"
                        >
                            <i class="bi bi-folder2-open me-1"></i>Ver Expediente de este Equipo
                        </Link>
                    </div>
                </div>

                <div class="sc">
                    <div class="table-responsive">
                        <table class="ct">
                            <thead>
                                <tr>
                                    <th class="ps-4">Archivo</th>
                                    <th>Equipo</th>
                                    <th>Tipo</th>
                                    <th>Versión</th>
                                    <th>Subido</th>
                                    <th>Estado</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="entregablesFiltrados.length === 0">
                                    <td
                                        colspan="7"
                                        class="text-center py-5"
                                        style="color: var(--text-muted)"
                                    >
                                        {{ tabEntregables === 'pendientes' ? 'No hay entregables pendientes por revisar' : 'No hay entregables revisados / archivados' }}
                                    </td>
                                </tr>
                                <tr
                                    v-for="ent in entregablesFiltrados"
                                    :key="ent.entr_id"
                                >
                                    <td class="ps-4">
                                        <div
                                            class="fw-semibold"
                                            style="font-size: 0.88rem"
                                        >
                                            {{ ent.entr_nombre_archivo }}
                                        </div>
                                        <div v-if="ent.catalogo_pnfi?.cep_nombre" class="mt-1">
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.72rem; font-weight: 600;">
                                                <i class="bi bi-bookmark-check me-1"></i>{{ ent.catalogo_pnfi.cep_nombre }}
                                            </span>
                                        </div>
                                        <div
                                            v-if="ent.entr_observacion_rechazo"
                                            style="
                                                font-size: 0.75rem;
                                                color: #dc3545;
                                                margin-top: 2px;
                                            "
                                        >
                                            <i
                                                class="bi bi-exclamation-circle me-1"
                                            ></i
                                            >{{ ent.entr_observacion_rechazo }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                            <span class="bs bp">{{
                                                ent.equipo?.equ_codigo
                                            }}</span>
                                            <Link
                                                :href="`/profesor/equipos/${ent.entr_id_equ}/expediente`"
                                                class="btn btn-xs btn-outline-info py-0 px-2 rounded-pill d-inline-flex align-items-center gap-1"
                                                style="font-size: 0.72rem;"
                                                title="Ver expediente del equipo"
                                            >
                                                <i class="bi bi-folder2-open"></i>
                                                <span>Expediente</span>
                                            </Link>
                                        </div>
                                    </td>
                                    <td
                                        style="
                                            font-size: 0.85rem;
                                            color: var(--text-muted);
                                        "
                                    >
                                        {{ ent.tipo_entregable?.tet_nombre }}
                                    </td>
                                    <td
                                        style="
                                            font-size: 0.82rem;
                                            color: var(--text-muted);
                                        "
                                    >
                                        v{{ ent.entr_version || "1" }}
                                    </td>
                                    <td
                                        style="
                                            font-size: 0.82rem;
                                            color: var(--text-muted);
                                        "
                                    >
                                        {{
                                            new Date(
                                                ent.entr_fecha_subida,
                                            ).toLocaleDateString("es-VE")
                                        }}
                                    </td>
                                    <td>
                                        <span
                                            v-if="ent.entr_aprobado === null"
                                            class="bs bw"
                                            >Pendiente</span
                                        >
                                        <span
                                            v-else-if="ent.entr_aprobado"
                                            class="bs ba"
                                            >Aprobado</span
                                        >
                                        <span v-else class="bs bd"
                                            >Rechazado</span
                                        >
                                    </td>
                                    <td class="text-end pe-4">
                                        <template
                                            v-if="ent.entr_aprobado === null"
                                        >
                                            <a
                                                :href="`/storage/${ent.entr_ruta}`"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-primary me-1"
                                            >
                                                <i class="bi bi-eye"></i> Ver
                                            </a>
                                            <button
                                                class="btn btn-sm btn-outline-success me-1"
                                                @click="aprobar(ent)"
                                                title="Aprobar"
                                            >
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button
                                                class="btn btn-sm btn-outline-danger"
                                                @click="abrirRechazar(ent)"
                                                title="Rechazar"
                                            >
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <a
                                                v-if="ent.entr_ruta"
                                                :href="`/storage/${ent.entr_ruta}`"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-secondary me-1 py-0 px-2"
                                                title="Ver / Descargar archivo"
                                            >
                                                <i class="bi bi-download me-1"></i>Ver
                                            </a>
                                            <span
                                                class="badge"
                                                :class="ent.entr_aprobado ? 'bg-success-subtle text-success border border-success' : 'bg-danger-subtle text-danger border border-danger'"
                                                style="font-size: 0.76rem;"
                                            >
                                                {{ ent.entr_aprobado ? 'Aprobado' : 'Rechazado' }}
                                            </span>
                                        </template>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Aprobar -->
        <div class="modal fade" id="mAp" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center p-4">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem">
                            ✅
                        </div>
                        <h6 class="fw-bold" style="color: var(--text-main)">
                            ¿Aprobar Entregable?
                        </h6>
                        <p
                            style="color: var(--text-muted); font-size: 0.9rem"
                            class="mb-0"
                        >
                            ¿Confirmas que el entregable
                            <strong>{{
                                entSeleccionado?.entr_nombre_archivo
                            }}</strong>
                            es correcto?
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
                            class="btn btn-success fw-bold px-4"
                            @click="confirmarAprobar"
                        >
                            Sí, aprobar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Rechazar -->
        <div class="modal fade" id="mRech" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>Rechazar Entregable</h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form @submit.prevent="confirmarRechazar">
                        <div class="modal-body">
                            <div class="alert alert-warning py-2 small mb-3">
                                <i class="bi bi-exclamation-triangle me-1"></i
                                >El líder recibirá una notificación con el
                                motivo del rechazo.
                            </div>
                            <label class="form-label fw-semibold"
                                >Motivo del Rechazo
                                <span class="text-danger">*</span></label
                            >
                            <textarea
                                v-model="observacionRechazo"
                                class="form-control"
                                :class="{
                                    'is-invalid':
                                        !observacionRechazo && intentoRechazar,
                                }"
                                rows="3"
                                placeholder="Indica qué debe corregir el equipo..."
                            ></textarea>
                            <div
                                v-if="!observacionRechazo && intentoRechazar"
                                class="invalid-feedback"
                            >
                                El motivo es obligatorio.
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
                            >
                                Rechazar Entregable
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import SelectBuscable from "@/Components/SelectBuscable.vue";
import { Modal } from "bootstrap";
import { Link } from "@inertiajs/vue3";
import SidebarProfesor from "@/Components/SidebarProfesor.vue";

const props = defineProps({
    entregables: { type: Array, default: () => [] },
    catalogoPnfi: { type: Array, default: () => [] },
    equipos: { type: Array, default: () => [] },
    semestreActivo: { type: String, default: "Semestre 1" },
});
const page = usePage();
const sidebarOpen = ref(false);
const opcionesEquiposFiltro = computed(() => [
    { value: "", label: "Todos los equipos" },
    ...(props.equipos || []).map((eq) => ({
        value: eq.equ_id,
        label: `${eq.equ_codigo} - ${(eq.equ_titulo || "").substring(0, 30)}`,
    })),
]);
const mostrarCatalogo = ref(false);
const equipoFiltro = ref("");
const tabEntregables = ref("pendientes"); // "pendientes" | "archivados"

const totalPendientes = computed(
    () => (props.entregables || []).filter((e) => e.entr_aprobado === null).length,
);

const totalArchivados = computed(
    () => (props.entregables || []).filter((e) => e.entr_aprobado !== null).length,
);

const catalogoFiltrado = computed(() => {
    let list = props.catalogoPnfi || [];
    if (equipoFiltro.value) {
        const eq = props.equipos?.find((e) => e.equ_id == equipoFiltro.value);
        if (eq && eq.equ_id_tra) {
            return list.filter((c) => c.cep_id_trayecto == eq.equ_id_tra);
        }
    }
    if (props.equipos && props.equipos.length > 0) {
        const trayectosProf = [...new Set(props.equipos.map((e) => e.equ_id_tra).filter(Boolean))];
        if (trayectosProf.length > 0) {
            return list.filter((c) => trayectosProf.includes(c.cep_id_trayecto));
        }
    }
    return list;
});

const getEstadoCatalogoEquipo = (cepId, eqId) => {
    const entregablesAsociados = (props.entregables || []).filter(
        (e) => e.entr_id_cep === cepId && (!eqId || e.entr_id_equ == eqId),
    );
    if (!entregablesAsociados.length)
        return { estado: "pendiente", label: "Sin subir", badge: "bg-light text-muted border" };
    if (entregablesAsociados.some((e) => e.entr_aprobado === true))
        return { estado: "aprobado", label: "Aprobado", badge: "bg-success text-white" };
    if (entregablesAsociados.some((e) => e.entr_aprobado === null))
        return { estado: "revision", label: "En revisión", badge: "bg-warning text-dark" };
    return { estado: "rechazado", label: "Rechazado", badge: "bg-danger text-white" };
};

const entregablesFiltrados = computed(() => {
    let list = props.entregables || [];
    if (tabEntregables.value === "pendientes") {
        list = list.filter((e) => e.entr_aprobado === null);
    } else {
        list = list.filter((e) => e.entr_aprobado !== null);
    }
    if (equipoFiltro.value) {
        list = list.filter((e) => e.entr_id_equ == equipoFiltro.value);
    }
    return list;
});

const tema = ref(localStorage.getItem("prof-theme") || "light");
const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("prof-theme", tema.value);
};
const logout = () => router.post("/logout");
const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});
const pendientes = computed(
    () => props.entregables.filter((e) => e.entr_aprobado === null).length,
);
const entSeleccionado = ref(null);
const observacionRechazo = ref("");
const intentoRechazar = ref(false);
let mAp = null;
let mRech = null;
const aprobar = (ent) => {
    entSeleccionado.value = ent;
    mAp = mAp || new Modal(document.getElementById("mAp"));
    mAp.show();
};
const confirmarAprobar = () => {
    router.post(
        `/profesor/entregables/${entSeleccionado.value.entr_id}/aprobar`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                tabEntregables.value = "archivados";
                mAp?.hide();
            },
        },
    );
};
const abrirRechazar = (ent) => {
    entSeleccionado.value = ent;
    observacionRechazo.value = "";
    intentoRechazar.value = false;
    mRech = mRech || new Modal(document.getElementById("mRech"));
    mRech.show();
};
const confirmarRechazar = () => {
    intentoRechazar.value = true;
    if (!observacionRechazo.value.trim()) return;
    router.post(
        `/profesor/entregables/${entSeleccionado.value.entr_id}/rechazar`,
        { observacion: observacionRechazo.value },
        {
            preserveScroll: true,
            onSuccess: () => {
                tabEntregables.value = "archivados";
                mRech?.hide();
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
.sb-badge {
    margin-left: auto;
    background: #dc3545;
    color: #fff;
    font-size: 0.65rem;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 20px;
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
    font-size: 0.88rem;
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
