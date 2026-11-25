<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
        "
    >
        <SidebarLider
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
                            class="bi bi-cloud-upload me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Mis Entregables
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button
                        class="btn btn-danger btn-sm fw-bold rounded-pill px-3"
                        @click="abrirModal"
                    >
                        <i class="bi bi-cloud-arrow-up me-1"></i>Subir
                        Entregable
                    </button>
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
                <div
                    v-if="!cartaAprobada"
                    class="alert py-2 mb-3 d-flex align-items-center gap-2"
                    style="
                        background: rgba(220, 53, 69, 0.08);
                        border: 1px solid rgba(220, 53, 69, 0.2);
                        border-radius: 10px;
                    "
                >
                    <i class="bi bi-lock-fill text-danger flex-shrink-0"></i>
                    <div class="small">
                        <strong>Acción bloqueada:</strong> Debes tener la
                        <Link
                            href="/lider/carta"
                            style="color: #dc3545; font-weight: 700"
                            >carta de presentación</Link
                        >
                        aprobada antes de subir entregables.
                    </div>
                </div>
                <div style="margin-bottom: 1.25rem">
                    <h4
                        style="
                            font-weight: 800;
                            font-size: 1.25rem;
                            color: var(--text-main);
                            margin: 0;
                        "
                    >
                        Mis Entregables
                    </h4>
                    <p
                        style="
                            color: var(--text-muted);
                            font-size: 0.85rem;
                            margin-top: 2px;
                        "
                    >
                        Sube y da seguimiento a tus documentos de proyecto
                    </p>
                </div>

                <!-- Catálogo Oficial PNFI del Trayecto -->
                <div v-if="catalogoPnfi && catalogoPnfi.length" class="sc mb-4 p-3" style="border-left: 4px solid #dc3545;">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-danger d-flex align-items-center gap-2">
                                <i class="bi bi-card-checklist"></i>Ruta Oficial de Entregables PNFI
                                <span class="badge bg-danger-subtle text-danger" style="font-size: 0.75rem;">{{ semestreActivo }}</span>
                            </h6>
                            <p class="text-muted small mb-0">
                                Guía de entregables normativos requeridos para la aprobación de tu proyecto socio-tecnológico en el {{ semestreActivo }}.
                            </p>
                        </div>
                        <button
                            class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                            @click="mostrarChecklist = !mostrarChecklist"
                        >
                            <i class="bi" :class="mostrarChecklist ? 'bi-chevron-up' : 'bi-chevron-down'"></i>
                            {{ mostrarChecklist ? 'Ocultar' : 'Ver Detalles' }}
                        </button>
                    </div>

                    <div v-if="mostrarChecklist" class="table-responsive mt-3">
                        <table class="table table-sm align-middle mb-0" style="font-size: 0.84rem; --bs-table-bg: transparent; --bs-table-color: var(--text-main);">
                            <thead>
                                <tr style="background: var(--bg-th); color: var(--text-main);">
                                    <th style="color: var(--text-muted); font-size: 0.78rem;">Semestre</th>
                                    <th style="color: var(--text-muted); font-size: 0.78rem;">Entregable Normativo</th>
                                    <th style="color: var(--text-muted); font-size: 0.78rem;">Semanas Sugeridas</th>
                                    <th style="color: var(--text-muted); font-size: 0.78rem;">Tipo</th>
                                    <th class="text-center" style="color: var(--text-muted); font-size: 0.78rem;">Estado del Equipo</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="cep in catalogoPnfi" :key="cep.cep_id" style="border-bottom: 1px solid var(--border);">
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary">{{ cep.cep_semestre }}</span>
                                    </td>
                                    <td>
                                        <div style="font-weight: 700; color: var(--text-main);">{{ cep.cep_nombre }}</div>
                                        <div v-if="cep.cep_descripcion" style="color: var(--text-muted); font-size: 0.8rem;">{{ cep.cep_descripcion }}</div>
                                    </td>
                                    <td style="color: var(--text-main);">
                                        <span v-if="cep.cep_semana_inicio_sugerida && cep.cep_semana_limite_sugerida">
                                            Sem. {{ cep.cep_semana_inicio_sugerida }} a {{ cep.cep_semana_limite_sugerida }}
                                        </span>
                                        <span v-else style="color: var(--text-muted);">-</span>
                                    </td>
                                    <td>
                                        <span v-if="cep.cep_es_obligatorio" class="badge bg-danger-subtle text-danger">Obligatorio</span>
                                        <span v-else class="badge bg-light text-muted border">Opcional</span>
                                    </td>
                                    <td class="text-center">
                                        <span :class="['badge', getEstadoCatalogo(cep.cep_id).badge]">
                                            {{ getEstadoCatalogo(cep.cep_id).label }}
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="sc">
                    <div class="sh">
                        <h6>
                            <i
                                class="bi bi-file-earmark-fill text-danger me-2"
                            ></i
                            >Historial de Entregables
                        </h6>
                    </div>
                    <div
                        v-if="!entregables?.length"
                        class="p-5 text-center"
                        style="color: var(--text-muted)"
                    >
                        <i class="bi bi-cloud-upload fs-1 d-block mb-3"></i>No
                        has subido entregables aún
                    </div>
                    <div
                        v-for="ent in entregables"
                        :key="ent.entr_id"
                        class="ent-row"
                    >
                        <div class="d-flex align-items-center gap-3">
                            <div
                                style="
                                    width: 36px;
                                    height: 36px;
                                    border-radius: 8px;
                                    background: rgba(220, 53, 69, 0.1);
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    flex-shrink: 0;
                                "
                            >
                                <i
                                    class="bi"
                                    :class="
                                        ent.entr_aprobado
                                            ? 'bi-file-pdf text-danger'
                                            : 'bi-file-earmark text-muted'
                                    "
                                    style="font-size: 1.1rem"
                                ></i>
                            </div>
                            <div>
                                <div
                                    class="fw-semibold"
                                    style="font-size: 0.9rem"
                                >
                                    {{ ent.entr_nombre_archivo }}
                                </div>
                                <div v-if="ent.catalogo_pnfi?.cep_nombre" class="mt-1">
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.72rem; font-weight: 600;">
                                        <i class="bi bi-bookmark-check me-1"></i>{{ ent.catalogo_pnfi.cep_nombre }}
                                    </span>
                                </div>
                                <div
                                    style="
                                        font-size: 0.75rem;
                                        color: var(--text-muted);
                                    "
                                >
                                    {{ ent.tipo_entregable?.tet_nombre }} · v{{
                                        ent.entr_version || "1"
                                    }}
                                    ·
                                    {{
                                        new Date(
                                            ent.entr_fecha_subida,
                                        ).toLocaleDateString("es-VE")
                                    }}
                                </div>
                                <div
                                    v-if="ent.entr_observacion_rechazo"
                                    style="
                                        font-size: 0.75rem;
                                        color: #dc3545;
                                        margin-top: 2px;
                                    "
                                >
                                    <i class="bi bi-exclamation-circle me-1"></i
                                    >{{ ent.entr_observacion_rechazo }}
                                </div>
                            </div>
                        </div>
                        <span
                            class="bs"
                            :class="
                                ent.entr_aprobado === null
                                    ? 'bp'
                                    : ent.entr_aprobado
                                      ? 'ba'
                                      : 'bd'
                            "
                        >
                            {{
                                ent.entr_aprobado === null
                                    ? "En revisión"
                                    : ent.entr_aprobado
                                      ? "Aprobado"
                                      : "Rechazado"
                            }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mSubir" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>Subir Entregable</h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form
                        @submit.prevent="guardar"
                        enctype="multipart/form-data"
                    >
                        <div class="modal-body">
                            <div
                                class="alert py-2 small mb-3"
                                style="
                                    background: rgba(13, 110, 253, 0.08);
                                    border: 1px solid rgba(13, 110, 253, 0.2);
                                    color: var(--text-main);
                                "
                            >
                                <i
                                    class="bi bi-info-circle me-1 text-primary"
                                ></i
                                >El profesor revisará tu entregable y recibirás
                                una notificación con el resultado.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Tipo de Entregable
                                    <span class="text-danger">*</span></label
                                >
                                <select
                                    v-model="form.entr_id_tet"
                                    class="form-select"
                                    :class="{
                                        'is-invalid': errores.entr_id_tet,
                                    }"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option
                                        v-for="tipo in tiposEntregable"
                                        :key="tipo.tet_id"
                                        :value="tipo.tet_id"
                                    >
                                        {{ tipo.tet_nombre }}
                                    </option>
                                </select>
                                <div
                                    v-if="errores.entr_id_tet"
                                    class="invalid-feedback"
                                >
                                    {{ errores.entr_id_tet }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Vincular a Catálogo Oficial PNFI <span class="text-muted fw-normal small">({{ semestreActivo }})</span>
                                </label>
                                <select
                                    v-model="form.entr_id_cep"
                                    class="form-select"
                                >
                                    <option value="">-- Ninguno / Entregable adicional --</option>
                                    <option
                                        v-for="cep in catalogoPnfi"
                                        :key="cep.cep_id"
                                        :value="cep.cep_id"
                                    >
                                        {{ cep.cep_nombre }} {{ cep.cep_es_obligatorio ? '★ (Obligatorio)' : '' }}
                                    </option>
                                </select>
                                <div class="form-text small" style="color: var(--text-muted)">
                                    Vincular al catálogo oficial actualiza la ruta crítica y el Enfoque de Gestión de Proyectos y Procesos del proyecto.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Archivo
                                    <span class="text-danger">*</span></label
                                >
                                <input
                                    type="file"
                                    class="form-control"
                                    :class="{ 'is-invalid': errores.archivo }"
                                    @change="
                                        form.archivo = $event.target.files[0]
                                    "
                                    accept=".pdf,.doc,.docx,.zip"
                                />
                                <div
                                    class="form-text"
                                    style="color: var(--text-muted)"
                                >
                                    PDF, Word o ZIP. Máx 10MB.
                                </div>
                                <div
                                    v-if="errores.archivo"
                                    class="invalid-feedback"
                                >
                                    {{ errores.archivo }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Versión</label
                                >
                                <input
                                    v-model="form.entr_version"
                                    type="text"
                                    class="form-control"
                                    placeholder="Ej: 1.0, 2.1"
                                />
                            </div>
                            <div>
                                <label class="form-label fw-semibold"
                                    >Descripción</label
                                >
                                <textarea
                                    v-model="form.entr_descripcion"
                                    class="form-control"
                                    rows="2"
                                    placeholder="Describe brevemente el contenido..."
                                ></textarea>
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
                                ></span>
                                <i v-else class="bi bi-cloud-arrow-up me-2"></i
                                >Subir Entregable
                            </button>
                        </div>
                    </form>
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
import { Modal } from "bootstrap";
import { Link } from "@inertiajs/vue3";
import ModalConfirmar from "@/Components/ModalConfirmar.vue";
import SidebarLider from "@/Components/SidebarLider.vue";

const props = defineProps({
    entregables: { type: Array, default: () => [] },
    tiposEntregable: { type: Array, default: () => [] },
    catalogoPnfi: { type: Array, default: () => [] },
    cartaAprobada: { type: Boolean, default: false },
    semestreActivo: { type: String, default: "Semestre 1" },
});
const page = usePage();
const sidebarOpen = ref(false);
const mostrarChecklist = ref(true);
const tema = ref(localStorage.getItem("lid-theme") || "light");
const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("lid-theme", tema.value);
};

const getEstadoCatalogo = (cepId) => {
    const entregablesAsociados = (props.entregables || []).filter(e => e.entr_id_cep === cepId);
    if (!entregablesAsociados.length) return { estado: 'pendiente', label: 'Pendiente', badge: 'bg-light text-muted border' };
    if (entregablesAsociados.some(e => e.entr_aprobado === true)) return { estado: 'aprobado', label: 'Aprobado', badge: 'bg-success text-white' };
    if (entregablesAsociados.some(e => e.entr_aprobado === null)) return { estado: 'revision', label: 'En revisión', badge: 'bg-warning text-dark' };
    return { estado: 'rechazado', label: 'Rechazado', badge: 'bg-danger text-white' };
};

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
const form = ref({
    entr_id_tet: "",
    entr_id_cep: "",
    archivo: null,
    entr_version: "1.0",
    entr_descripcion: "",
});
const errores = ref({});
const loading = ref(false);
let modal = null;
const abrirModal = () => {
    form.value = {
        entr_id_tet: "",
        entr_id_cep: "",
        archivo: null,
        entr_version: "1.0",
        entr_descripcion: "",
    };
    errores.value = {};
    modal = modal || new Modal(document.getElementById("mSubir"));
    modal.show();
};
const guardar = () => {
    loading.value = true;
    const data = new FormData();
    data.append("entr_id_tet", form.value.entr_id_tet);
    if (form.value.entr_id_cep) data.append("entr_id_cep", form.value.entr_id_cep);
    if (form.value.archivo) data.append("archivo", form.value.archivo);
    data.append("entr_version", form.value.entr_version);
    data.append("entr_descripcion", form.value.entr_descripcion);
    data.append(
        "entr_nombre_archivo",
        form.value.archivo?.name ?? "entregable",
    );
    router.post("/lider/entregables", data, {
        forceFormData: true,
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
.ent-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.85rem 1.3rem;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
    gap: 8px;
}
.ent-row:last-child {
    border-bottom: none;
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
