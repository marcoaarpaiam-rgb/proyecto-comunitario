<template>
    <AppLayout page-title="Resultados">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Resultados de Proyectos</h4>
                <p class="text-muted small mb-0">
                    Registro del estado final de cada proyecto
                </p>
            </div>
            <button class="btn btn-danger fw-semibold" @click="abrirModal()">
                <i class="bi bi-plus-lg me-2"></i>Registrar Resultado
            </button>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Equipo</th>
                            <th>Comunidad</th>
                            <th>Trayecto</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                            <th>Tardío</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="resultados.length === 0">
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay resultados registrados
                            </td>
                        </tr>
                        <tr v-for="rpr in resultados" :key="rpr.rpr_id">
                            <td class="ps-4 fw-bold text-danger">
                                {{ rpr.proyecto_comunidad?.equipo?.equ_codigo }}
                            </td>
                            <td class="small">
                                {{
                                    rpr.proyecto_comunidad?.comunidad
                                        ?.com_nombre
                                }}
                            </td>
                            <td class="small">
                                {{ rpr.trayecto?.tra_nombre }}
                            </td>
                            <td>
                                <span
                                    class="badge"
                                    :class="
                                        badgeColor(
                                            rpr.estado_proyecto?.epr_nombre,
                                        )
                                    "
                                >
                                    {{ rpr.estado_proyecto?.epr_nombre }}
                                </span>
                            </td>
                            <td class="small text-muted">
                                {{ rpr.rpr_fecha_registro }}
                            </td>
                            <td>
                                <span
                                    v-if="rpr.rpr_habilitado_tardio"
                                    class="badge bg-warning text-dark"
                                    >Habilitado</span
                                >
                                <button
                                    v-else
                                    class="btn btn-sm btn-outline-warning"
                                    @click="habilitarTardio(rpr)"
                                >
                                    Habilitar tardío
                                </button>
                            </td>
                            <td class="text-end pe-4 text-muted small">
                                {{ rpr.rpr_observaciones || "—" }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalResultado" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            Registrar Resultado
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <form @submit.prevent="guardar">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Proyecto <span class="text-danger">*</span>
                                </label>
                                <select
                                    v-model="form.rpr_id_pco"
                                    class="form-select"
                                    :class="{
                                        'is-invalid': errores.rpr_id_pco,
                                    }"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option
                                        v-for="pco in proyectos"
                                        :key="pco.pco_id"
                                        :value="pco.pco_id"
                                    >
                                        {{ pco.equipo?.equ_codigo }} —
                                        {{ pco.comunidad?.com_nombre }}
                                    </option>
                                </select>
                                <div
                                    v-if="errores.rpr_id_pco"
                                    class="invalid-feedback"
                                >
                                    {{ errores.rpr_id_pco }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Estado Final
                                    <span class="text-danger">*</span>
                                </label>
                                <select
                                    v-model="form.rpr_id_epr"
                                    class="form-select"
                                    :class="{
                                        'is-invalid': errores.rpr_id_epr,
                                    }"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option
                                        v-for="est in estados"
                                        :key="est.epr_id"
                                        :value="est.epr_id"
                                    >
                                        {{ est.epr_nombre }}
                                    </option>
                                </select>
                                <div
                                    v-if="errores.rpr_id_epr"
                                    class="invalid-feedback"
                                >
                                    {{ errores.rpr_id_epr }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Trayecto <span class="text-danger">*</span>
                                </label>
                                <select
                                    v-model="form.rpr_id_tra"
                                    class="form-select"
                                    :class="{
                                        'is-invalid': errores.rpr_id_tra,
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
                                    v-if="errores.rpr_id_tra"
                                    class="invalid-feedback"
                                >
                                    {{ errores.rpr_id_tra }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Fecha de Registro
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    v-model="form.rpr_fecha_registro"
                                    type="date"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            errores.rpr_fecha_registro,
                                    }"
                                />
                                <div
                                    v-if="errores.rpr_fecha_registro"
                                    class="invalid-feedback"
                                >
                                    {{ errores.rpr_fecha_registro }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Observaciones</label
                                >
                                <textarea
                                    v-model="form.rpr_observaciones"
                                    class="form-control"
                                    rows="2"
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
        <ModalConfirmar
            ref="modalConfirmarRef"
            modal-id="modalConfirmarTardio"
            titulo="¿Habilitar Entrega Tardía?"
            mensaje="Esta acción permitirá al equipo hacer una entrega fuera del plazo establecido."
            icono="⏰"
            btn-texto="Sí, habilitar"
            btn-clase="btn-warning"
            @confirmado="confirmarHabilitar"
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
    resultados: Array,
    proyectos: Array,
    estados: Array,
    trayectos: Array,
});

const form = ref({
    rpr_id_pco: "",
    rpr_id_epr: "",
    rpr_id_tra: "",
    rpr_fecha_registro: "",
    rpr_observaciones: "",
});
const errores = ref({});
const loading = ref(false);
let modal = null;

const badgeColor = (nombre) => {
    if (nombre === "Aprobado") return "bg-success";
    if (nombre === "Aplazado") return "bg-warning text-dark";
    if (nombre === "No Presentado") return "bg-danger";
    return "bg-secondary";
};

const abrirModal = () => {
    form.value = {
        rpr_id_pco: "",
        rpr_id_epr: "",
        rpr_id_tra: "",
        rpr_fecha_registro: new Date().toISOString().split("T")[0],
        rpr_observaciones: "",
    };
    errores.value = {};
    modal = modal || new Modal(document.getElementById("modalResultado"));
    modal.show();
};

const guardar = () => {
    loading.value = true;
    router.post("/coordinador/resultados", form.value, {
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

const modalConfirmarRef = ref(null);
const itemSeleccionado = ref(null);

const habilitarTardio = (rpr) => {
    itemSeleccionado.value = rpr;
    modalConfirmarRef.value?.abrir();
};

const confirmarHabilitar = () => {
    router.post(
        `/coordinador/resultados/${itemSeleccionado.value.rpr_id}/habilitar-tardio`,
    );
};
</script>
