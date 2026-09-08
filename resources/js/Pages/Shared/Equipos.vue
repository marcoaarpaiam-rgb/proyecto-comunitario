<template>
    <AppLayout page-title="Equipos">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Equipos de Proyecto</h4>
                <p class="text-muted small mb-0">
                    Gestión de equipos e integrantes
                </p>
            </div>
            <button
                class="btn btn-danger fw-semibold"
                @click="abrirModalCrear()"
            >
                <i class="bi bi-plus-lg me-2"></i>Nuevo Equipo
            </button>
        </div>

        <!-- Flash -->
        <div
            v-if="$page.props.flash?.success"
            class="alert alert-success alert-dismissible fade show mb-4"
        >
            {{ $page.props.flash.success }}
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
        <div
            v-if="$page.props.errors?.integrantes"
            class="alert alert-danger alert-dismissible fade show mb-4"
        >
            {{ $page.props.errors.integrantes }}
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>

        <!-- Tabla de equipos -->
        <TablaBuscable
            :items="equipos"
            :campos-busqueda="[
                'equ_codigo',
                'equ_titulo',
                'seccion.sec_codigo',
                'trayecto.tra_nombre',
            ]"
            :por-pagina="8"
            placeholder="Buscar por código, título o sección..."
        >
            <template #default="{ registros }">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Código</th>
                                    <th>Título del Proyecto</th>
                                    <th>Sección</th>
                                    <th>Trayecto</th>
                                    <th>Integrantes</th>
                                    <th>Comunidad</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="registros.length === 0">
                                    <td
                                        colspan="7"
                                        class="text-center text-muted py-4"
                                    >
                                        No se encontraron equipos
                                    </td>
                                </tr>
                                <tr v-for="equ in registros" :key="equ.equ_id">
                                    <td class="ps-4 fw-bold text-danger">
                                        {{ equ.equ_codigo }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold">
                                            {{ equ.equ_titulo }}
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary"
                                        >
                                            {{ equ.seccion?.sec_codigo }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ equ.trayecto?.tra_nombre }}
                                    </td>
                                    <td>
                                        <button
                                            class="btn btn-sm btn-outline-secondary"
                                            @click="verIntegrantes(equ)"
                                        >
                                            <i class="bi bi-people me-1"></i>
                                            {{ equ.integrantes?.length || 0 }}
                                        </button>
                                    </td>
                                    <td class="small text-muted">
                                        {{
                                            equ.proyecto_comunidad?.comunidad
                                                ?.com_nombre || "—"
                                        }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <button
                                            class="btn btn-sm btn-outline-primary me-1"
                                            @click="abrirModalEditar(equ)"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            @click="desactivar(equ)"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </TablaBuscable>

        <!-- Modal Crear Equipo -->
        <div class="modal fade" id="modalCrearEquipo" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            Nuevo Equipo
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <form @submit.prevent="guardarNuevo">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold"
                                        >Código
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        v-model="formNuevo.equ_codigo"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': errores.equ_codigo,
                                        }"
                                        placeholder="Ej: EQ-331-001"
                                    />
                                    <div
                                        v-if="errores.equ_codigo"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.equ_codigo }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold"
                                        >Sección
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <select
                                        v-model="formNuevo.equ_id_sec"
                                        class="form-select"
                                        :class="{
                                            'is-invalid': errores.equ_id_sec,
                                        }"
                                        @change="autoTrayecto"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option
                                            v-for="sec in secciones"
                                            :key="sec.sec_id"
                                            :value="sec.sec_id"
                                            :data-tra="sec.sec_id_tra"
                                        >
                                            {{ sec.sec_codigo }} —
                                            {{ sec.trayecto?.tra_nombre }} ({{
                                                sec.turno?.tur_nombre
                                            }})
                                        </option>
                                    </select>
                                    <div
                                        v-if="errores.equ_id_sec"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.equ_id_sec }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold"
                                        >Trayecto
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <select
                                        v-model="formNuevo.equ_id_tra"
                                        class="form-select"
                                        :class="{
                                            'is-invalid': errores.equ_id_tra,
                                        }"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option
                                            v-for="tra in trayectos"
                                            :key="tra.tra_id"
                                            :value="tra.tra_id"
                                        >
                                            {{ tra.tra_nombre }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="errores.equ_id_tra"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.equ_id_tra }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold"
                                        >Título del Proyecto
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        v-model="formNuevo.equ_titulo"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': errores.equ_titulo,
                                        }"
                                        placeholder="Ej: Sistema de Control de Inventario..."
                                    />
                                    <div
                                        v-if="errores.equ_titulo"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.equ_titulo }}
                                    </div>
                                </div>
                            </div>

                            <!-- Integrantes -->
                            <hr class="my-4" />
                            <div
                                class="d-flex justify-content-between align-items-center mb-3"
                            >
                                <h6 class="fw-bold mb-0">
                                    <i
                                        class="bi bi-people-fill me-2 text-danger"
                                    ></i
                                    >Integrantes
                                </h6>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-danger"
                                    @click="agregarIntegrante"
                                >
                                    <i class="bi bi-plus me-1"></i>Agregar
                                </button>
                            </div>

                            <div
                                v-if="errores.integrantes"
                                class="alert alert-danger py-2 small mb-3"
                            >
                                {{ errores.integrantes }}
                            </div>

                            <div
                                v-for="(item, idx) in formNuevo.integrantes"
                                :key="idx"
                                class="row g-2 mb-2 align-items-center"
                            >
                                <div class="col-md-7">
                                    <select
                                        v-model="item.usu_id"
                                        class="form-select form-select-sm"
                                    >
                                        <option value="">
                                            Seleccionar estudiante...
                                        </option>
                                        <option
                                            v-for="est in estudiantesDisponibles(
                                                item.usu_id,
                                            )"
                                            :key="est.usu_id"
                                            :value="est.usu_id"
                                        >
                                            {{ est.usu_primer_nombre }}
                                            {{ est.usu_primer_apellido }} ({{
                                                est.usu_cedula
                                            }})
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check ms-2">
                                        <input
                                            type="radio"
                                            class="form-check-input"
                                            :name="'lider'"
                                            :value="idx"
                                            v-model="liderIndex"
                                            @change="actualizarLider"
                                        />
                                        <label
                                            class="form-check-label small fw-semibold text-danger"
                                        >
                                            Líder
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger w-100"
                                        @click="quitarIntegrante(idx)"
                                        :disabled="
                                            formNuevo.integrantes.length === 1
                                        "
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="alert alert-info py-2 small mt-3">
                                <i class="bi bi-info-circle me-1"></i>
                                El estudiante marcado como líder recibirá acceso
                                al sistema. Su usuario y contraseña inicial
                                serán su cédula.
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
                                class="btn btn-danger fw-semibold"
                                :disabled="loading"
                            >
                                <span
                                    v-if="loading"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                Guardar Equipo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Editar Equipo -->
        <div class="modal fade" id="modalEditarEquipo" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            Editar Equipo
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <form @submit.prevent="guardarEdicion">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold"
                                        >Código</label
                                    >
                                    <input
                                        v-model="formEditar.equ_codigo"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                erroresEditar.equ_codigo,
                                        }"
                                    />
                                    <div
                                        v-if="erroresEditar.equ_codigo"
                                        class="invalid-feedback"
                                    >
                                        {{ erroresEditar.equ_codigo }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold"
                                        >Sección</label
                                    >
                                    <select
                                        v-model="formEditar.equ_id_sec"
                                        class="form-select"
                                    >
                                        <option
                                            v-for="sec in secciones"
                                            :key="sec.sec_id"
                                            :value="sec.sec_id"
                                        >
                                            {{ sec.sec_codigo }} —
                                            {{ sec.trayecto?.tra_nombre }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold"
                                        >Trayecto</label
                                    >
                                    <select
                                        v-model="formEditar.equ_id_tra"
                                        class="form-select"
                                    >
                                        <option
                                            v-for="tra in trayectos"
                                            :key="tra.tra_id"
                                            :value="tra.tra_id"
                                        >
                                            {{ tra.tra_nombre }}
                                        </option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold"
                                        >Título</label
                                    >
                                    <input
                                        v-model="formEditar.equ_titulo"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                erroresEditar.equ_titulo,
                                        }"
                                    />
                                    <div
                                        v-if="erroresEditar.equ_titulo"
                                        class="invalid-feedback"
                                    >
                                        {{ erroresEditar.equ_titulo }}
                                    </div>
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
                                class="btn btn-danger fw-semibold"
                                :disabled="loadingEditar"
                            >
                                <span
                                    v-if="loadingEditar"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Ver Integrantes -->
        <div class="modal fade" id="modalIntegrantes" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            Integrantes — {{ equipoSeleccionado?.equ_codigo }}
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <div class="modal-body p-0">
                        <ul class="list-group list-group-flush">
                            <li
                                v-for="int in equipoSeleccionado?.integrantes"
                                :key="int.ein_id"
                                class="list-group-item d-flex align-items-center gap-3 px-4 py-3"
                            >
                                <div class="avatar-sm">
                                    {{ int.usuario?.usu_primer_nombre?.[0]
                                    }}{{
                                        int.usuario?.usu_primer_apellido?.[0]
                                    }}
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">
                                        {{ int.usuario?.usu_primer_nombre }}
                                        {{ int.usuario?.usu_primer_apellido }}
                                    </div>
                                    <div class="text-muted small">
                                        CI: {{ int.usuario?.usu_cedula }}
                                    </div>
                                </div>
                                <span
                                    v-if="int.ein_es_lider"
                                    class="badge bg-danger"
                                    >Líder</span
                                >
                                <span v-else class="badge bg-secondary"
                                    >Integrante</span
                                >
                            </li>
                        </ul>
                    </div>
                    <div class="modal-footer border-0">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Agrega el componente al final del template -->
        <ModalConfirmar
            ref="modalConfirmarRef"
            modal-id="modalConfirmarEquipo"
            titulo="¿Desactivar Equipo?"
            :mensaje="`¿Estás seguro de desactivar el equipo ${equipoAEliminar?.equ_codigo}?`"
            icono="🗑️"
            btn-texto="Sí, desactivar"
            btn-clase="btn-danger"
            @confirmado="confirmarDesactivar"
        />
    </AppLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Modal } from "bootstrap";
import ModalConfirmar from "@/Components/ModalConfirmar.vue";
import TablaBuscable from "@/Components/TablaBuscable.vue";

const props = defineProps({
    equipos: Array,
    secciones: Array,
    trayectos: Array,
    estudiantes: Array,
});

// ── Estado ──
const errores = ref({});
const erroresEditar = ref({});
const loading = ref(false);
const loadingEditar = ref(false);
const equipoEditando = ref(null);
const equipoSeleccionado = ref(null);
const liderIndex = ref(0);

const formNuevo = ref({
    equ_codigo: "",
    equ_titulo: "",
    equ_id_sec: "",
    equ_id_tra: "",
    integrantes: [{ usu_id: "", es_lider: true }],
});

const formEditar = ref({
    equ_codigo: "",
    equ_titulo: "",
    equ_id_sec: "",
    equ_id_tra: "",
});

let modalCrear = null;
let modalEditar = null;
let modalInts = null;

// ── Helpers ──
const estudiantesDisponibles = (idActual) => {
    const seleccionados = formNuevo.value.integrantes
        .map((i) => i.usu_id)
        .filter((id) => id && id !== idActual);
    return props.estudiantes.filter((e) => !seleccionados.includes(e.usu_id));
};

const autoTrayecto = () => {
    const sec = props.secciones.find(
        (s) => s.sec_id == formNuevo.value.equ_id_sec,
    );
    if (sec) formNuevo.value.equ_id_tra = sec.sec_id_tra;
};

const actualizarLider = () => {
    formNuevo.value.integrantes.forEach((item, idx) => {
        item.es_lider = idx === liderIndex.value;
    });
};

const agregarIntegrante = () => {
    formNuevo.value.integrantes.push({ usu_id: "", es_lider: false });
};

const quitarIntegrante = (idx) => {
    formNuevo.value.integrantes.splice(idx, 1);
    if (liderIndex.value >= formNuevo.value.integrantes.length) {
        liderIndex.value = 0;
    }
    actualizarLider();
};

// ── Modales ──
const abrirModalCrear = () => {
    formNuevo.value = {
        equ_codigo: "",
        equ_titulo: "",
        equ_id_sec: "",
        equ_id_tra: "",
        integrantes: [{ usu_id: "", es_lider: true }],
    };
    liderIndex.value = 0;
    errores.value = {};
    modalCrear =
        modalCrear || new Modal(document.getElementById("modalCrearEquipo"));
    modalCrear.show();
};

const abrirModalEditar = (equ) => {
    equipoEditando.value = equ;
    formEditar.value = {
        equ_codigo: equ.equ_codigo,
        equ_titulo: equ.equ_titulo,
        equ_id_sec: equ.equ_id_sec,
        equ_id_tra: equ.equ_id_tra,
    };
    erroresEditar.value = {};
    modalEditar =
        modalEditar || new Modal(document.getElementById("modalEditarEquipo"));
    modalEditar.show();
};

const verIntegrantes = (equ) => {
    equipoSeleccionado.value = equ;
    modalInts =
        modalInts || new Modal(document.getElementById("modalIntegrantes"));
    modalInts.show();
};

// ── CRUD ──
const guardarNuevo = () => {
    actualizarLider();
    loading.value = true;
    router.post("/coordinador/equipos", formNuevo.value, {
        onError: (e) => {
            errores.value = e;
            loading.value = false;
        },
        onSuccess: () => {
            loading.value = false;
            modalCrear?.hide();
        },
    });
};

const guardarEdicion = () => {
    loadingEditar.value = true;
    router.put(
        `/coordinador/equipos/${equipoEditando.value.equ_id}`,
        formEditar.value,
        {
            onError: (e) => {
                erroresEditar.value = e;
                loadingEditar.value = false;
            },
            onSuccess: () => {
                loadingEditar.value = false;
                modalEditar?.hide();
            },
        },
    );
};

const modalConfirmarRef = ref(null);
const equipoAEliminar = ref(null);

const desactivar = (equ) => {
    equipoAEliminar.value = equ;
    modalConfirmarRef.value?.abrir();
};

const confirmarDesactivar = () => {
    router.delete(`/coordinador/equipos/${equipoAEliminar.value.equ_id}`);
};
</script>

<style>
.avatar-sm {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #1f3864;
    color: white;
    font-weight: 700;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
</style>
