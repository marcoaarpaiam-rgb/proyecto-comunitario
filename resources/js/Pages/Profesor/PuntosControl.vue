<template>
    <AppLayout page-title="Puntos de Control">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Puntos de Control</h4>
                <p class="text-muted small mb-0">
                    Gestión de puntos de control por equipo
                </p>
            </div>
            <button
                class="btn btn-danger fw-semibold"
                @click="abrirModalCrear()"
            >
                <i class="bi bi-plus-lg me-2"></i>Nuevo Punto
            </button>
        </div>

        <!-- Lista de puntos -->
        <div class="row g-3">
            <div v-if="puntosControl.length === 0" class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center text-muted py-5">
                        No hay puntos de control registrados
                    </div>
                </div>
            </div>
            <div
                v-for="puc in puntosControl"
                :key="puc.puc_id"
                class="col-md-6"
            >
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <div
                            class="d-flex justify-content-between align-items-start mb-2"
                        >
                            <div>
                                <span class="badge bg-danger me-2"
                                    >Punto {{ puc.puc_orden }}</span
                                >
                                <span
                                    v-if="puc.puc_es_entregable"
                                    class="badge bg-warning text-dark"
                                    >Entregable</span
                                >
                            </div>
                            <div class="d-flex gap-1">
                                <button
                                    class="btn btn-sm btn-outline-success"
                                    @click="abrirSeguimiento(puc)"
                                    title="Registrar seguimiento"
                                >
                                    <i class="bi bi-check2-circle"></i>
                                </button>
                                <button
                                    class="btn btn-sm btn-outline-warning"
                                    @click="abrirReprogramar(puc)"
                                    title="Reprogramar"
                                >
                                    <i class="bi bi-calendar-plus"></i>
                                </button>
                                <button
                                    class="btn btn-sm btn-outline-danger"
                                    @click="eliminar(puc)"
                                    title="Eliminar"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                        <h6 class="fw-bold">{{ puc.puc_nombre }}</h6>
                        <p
                            v-if="puc.puc_descripcion"
                            class="text-muted small mb-2"
                        >
                            {{ puc.puc_descripcion }}
                        </p>
                        <div
                            class="d-flex justify-content-between align-items-center mt-3"
                        >
                            <div>
                                <small class="text-muted">
                                    <i class="bi bi-people me-1"></i>
                                    {{ puc.equipo?.equ_codigo }}
                                </small>
                            </div>
                            <div class="text-end">
                                <small
                                    v-if="puc.puc_fecha_reprogramada"
                                    class="text-warning d-block"
                                >
                                    <i class="bi bi-arrow-repeat me-1"></i>
                                    Reprogramado:
                                    {{ puc.puc_fecha_reprogramada }}
                                </small>
                                <small
                                    :class="
                                        estaVencido(puc)
                                            ? 'text-danger fw-bold'
                                            : 'text-muted'
                                    "
                                >
                                    <i class="bi bi-calendar me-1"></i>
                                    {{ puc.puc_fecha_limite }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Crear Punto -->
        <div class="modal fade" id="modalCrearPunto" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            Nuevo Punto de Control
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <form @submit.prevent="guardarPunto">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">
                                        Equipo
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        v-model="formPunto.puc_id_equ"
                                        class="form-select"
                                        :class="{
                                            'is-invalid': errores.puc_id_equ,
                                        }"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option
                                            v-for="equ in equipos"
                                            :key="equ.equ_id"
                                            :value="equ.equ_id"
                                        >
                                            {{ equ.equ_codigo }} —
                                            {{ equ.seccion?.sec_codigo }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="errores.puc_id_equ"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.puc_id_equ }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold"
                                        >Orden</label
                                    >
                                    <input
                                        v-model="formPunto.puc_orden"
                                        type="number"
                                        class="form-control"
                                        min="1"
                                    />
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Nombre
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="formPunto.puc_nombre"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': errores.puc_nombre,
                                        }"
                                        placeholder="Ej: Revisión de avance 1"
                                    />
                                    <div
                                        v-if="errores.puc_nombre"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.puc_nombre }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold"
                                        >Descripción</label
                                    >
                                    <textarea
                                        v-model="formPunto.puc_descripcion"
                                        class="form-control"
                                        rows="2"
                                        placeholder="Aspectos a evaluar..."
                                    ></textarea>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Fecha Límite
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="formPunto.puc_fecha_limite"
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                errores.puc_fecha_limite,
                                        }"
                                    />
                                    <div
                                        v-if="errores.puc_fecha_limite"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.puc_fecha_limite }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Días de aviso</label
                                    >
                                    <input
                                        v-model="formPunto.puc_dias_aviso"
                                        type="number"
                                        class="form-control"
                                        min="1"
                                        max="30"
                                    />
                                </div>
                                <div class="col-12">
                                    <div class="form-check">
                                        <input
                                            v-model="
                                                formPunto.puc_es_entregable
                                            "
                                            type="checkbox"
                                            class="form-check-input"
                                            id="esEntregable"
                                        />
                                        <label
                                            class="form-check-label fw-semibold"
                                            for="esEntregable"
                                        >
                                            Este punto es para subida de
                                            entregables finales
                                        </label>
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
                                :disabled="loading"
                            >
                                <span
                                    v-if="loading"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Seguimiento -->
        <div class="modal fade" id="modalSeguimiento" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #198754">
                        <h5 class="modal-title text-white fw-bold">
                            Registrar Seguimiento —
                            {{ puntoSeleccionado?.puc_nombre }}
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <form @submit.prevent="guardarSeguimiento">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >¿El equipo cumplió?</label
                                >
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input
                                            v-model="formSeq.seq_cumplido"
                                            type="radio"
                                            :value="true"
                                            class="form-check-input"
                                            id="cumplioSi"
                                        />
                                        <label
                                            class="form-check-label"
                                            for="cumplioSi"
                                            >Sí</label
                                        >
                                    </div>
                                    <div class="form-check">
                                        <input
                                            v-model="formSeq.seq_cumplido"
                                            type="radio"
                                            :value="false"
                                            class="form-check-input"
                                            id="cumplioNo"
                                        />
                                        <label
                                            class="form-check-label"
                                            for="cumplioNo"
                                            >No</label
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Observaciones</label
                                >
                                <textarea
                                    v-model="formSeq.seq_observaciones"
                                    class="form-control"
                                    rows="2"
                                ></textarea>
                            </div>
                            <hr />
                            <h6 class="fw-bold mb-3">
                                <i class="bi bi-person-check me-2"></i
                                >Asistencia
                            </h6>
                            <div
                                v-for="(ast, idx) in formSeq.asistencias"
                                :key="idx"
                                class="d-flex align-items-center gap-3 mb-2 p-2 rounded bg-light"
                            >
                                <div class="flex-grow-1 fw-semibold small">
                                    {{ ast.nombre }}
                                </div>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input
                                            v-model="ast.asistio"
                                            type="radio"
                                            :value="true"
                                            class="form-check-input"
                                            :id="`ast-si-${idx}`"
                                        />
                                        <label
                                            class="form-check-label small"
                                            :for="`ast-si-${idx}`"
                                        >
                                            Asistió
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input
                                            v-model="ast.asistio"
                                            type="radio"
                                            :value="false"
                                            class="form-check-input"
                                            :id="`ast-no-${idx}`"
                                        />
                                        <label
                                            class="form-check-label small"
                                            :for="`ast-no-${idx}`"
                                        >
                                            No asistió
                                        </label>
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
                                class="btn btn-success fw-semibold"
                                :disabled="loadingSeq"
                            >
                                <span
                                    v-if="loadingSeq"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                Guardar Seguimiento
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Reprogramar -->
        <div class="modal fade" id="modalReprogramar" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #ffc107">
                        <h5 class="modal-title fw-bold">
                            Reprogramar Punto de Control
                        </h5>
                        <button
                            type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <form @submit.prevent="guardarReprogramacion">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Nueva Fecha
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    v-model="formReprogram.fecha"
                                    type="date"
                                    class="form-control"
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Motivo <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    v-model="formReprogram.motivo"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Explica por qué se reprograma..."
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
                                class="btn btn-warning fw-semibold"
                            >
                                Reprogramar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <ModalConfirmar
            ref="modalConfirmarRef"
            modal-id="modalConfirmarPunto"
            titulo="¿Eliminar Punto de Control?"
            :mensaje="`¿Seguro que deseas eliminar el punto ${itemAEliminar?.puc_nombre}?`"
            icono="🚩"
            btn-texto="Sí, eliminar"
            btn-clase="btn-danger"
            @confirmado="confirmarEliminar"
        />
    </AppLayout>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Modal } from "bootstrap";
import ModalConfirmar from "@/Components/ModalConfirmar.vue";

const props = defineProps({
    puntosControl: Array,
    equipos: Array,
    trayectos: Array,
});

const errores = ref({});
const loading = ref(false);
const loadingSeq = ref(false);
const puntoSeleccionado = ref(null);

const formPunto = ref({
    puc_id_equ: "",
    puc_id_tra: "",
    puc_nombre: "",
    puc_descripcion: "",
    puc_fecha_limite: "",
    puc_orden: 1,
    puc_dias_aviso: 7,
    puc_es_entregable: false,
});

const formSeq = ref({
    seq_cumplido: true,
    seq_observaciones: "",
    asistencias: [],
});
const formReprogram = ref({ fecha: "", motivo: "" });

let modalCrear = null;
let modalSeq = null;
let modalReprogram = null;

const estaVencido = (puc) => {
    return new Date(puc.puc_fecha_limite) < new Date();
};

const abrirModalCrear = () => {
    formPunto.value = {
        puc_id_equ: "",
        puc_id_tra: "",
        puc_nombre: "",
        puc_descripcion: "",
        puc_fecha_limite: "",
        puc_orden: 1,
        puc_dias_aviso: 7,
        puc_es_entregable: false,
    };
    errores.value = {};
    modalCrear =
        modalCrear || new Modal(document.getElementById("modalCrearPunto"));
    modalCrear.show();
};

const abrirSeguimiento = (puc) => {
    puntoSeleccionado.value = puc;
    const equipo = props.equipos.find((e) => e.equ_id === puc.puc_id_equ);
    formSeq.value = {
        seq_cumplido: true,
        seq_observaciones: "",
        asistencias: (equipo?.integrantes || []).map((int) => ({
            usu_id: int.usu_id || int.ein_id_usu,
            nombre: `${int.usuario?.usu_primer_nombre} ${int.usuario?.usu_primer_apellido}`,
            asistio: true,
            observacion: "",
        })),
    };
    modalSeq =
        modalSeq || new Modal(document.getElementById("modalSeguimiento"));
    modalSeq.show();
};

const abrirReprogramar = (puc) => {
    puntoSeleccionado.value = puc;
    formReprogram.value = { fecha: "", motivo: "" };
    modalReprogram =
        modalReprogram ||
        new Modal(document.getElementById("modalReprogramar"));
    modalReprogram.show();
};

const guardarPunto = () => {
    const equ = props.equipos.find(
        (e) => e.equ_id == formPunto.value.puc_id_equ,
    );
    if (equ) formPunto.value.puc_id_tra = equ.equ_id_tra;
    loading.value = true;
    router.post("/profesor/puntos-control", formPunto.value, {
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

const guardarSeguimiento = () => {
    loadingSeq.value = true;
    router.post(
        `/profesor/puntos-control/${puntoSeleccionado.value.puc_id}/seguimiento`,
        formSeq.value,
        {
            onError: () => {
                loadingSeq.value = false;
            },
            onSuccess: () => {
                loadingSeq.value = false;
                modalSeq?.hide();
            },
        },
    );
};

const guardarReprogramacion = () => {
    router.post(
        `/profesor/puntos-control/${puntoSeleccionado.value.puc_id}/reprogramar`,
        {
            puc_fecha_reprogramada: formReprogram.value.fecha,
            puc_motivo_reprogramacion: formReprogram.value.motivo,
        },
        { onSuccess: () => modalReprogram?.hide() },
    );
};

const modalConfirmarRef = ref(null);
const itemAEliminar = ref(null);

const eliminar = (puc) => {
    itemAEliminar.value = puc;
    modalConfirmarRef.value?.abrir();
};

const confirmarEliminar = () => {
    router.delete(`/profesor/puntos-control/${itemAEliminar.value.puc_id}`);
};
</script>
