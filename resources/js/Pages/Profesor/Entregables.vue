<template>
    <AppLayout page-title="Entregables">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Entregables por Revisar</h4>
            <p class="text-muted small mb-0">
                Aprueba o rechaza los entregables subidos por los líderes
            </p>
        </div>

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

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Archivo</th>
                            <th>Equipo</th>
                            <th>Tipo</th>
                            <th>Subido</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="entregables.length === 0">
                            <td colspan="6" class="text-center text-muted py-4">
                                No hay entregables registrados
                            </td>
                        </tr>
                        <tr v-for="ent in entregables" :key="ent.entr_id">
                            <td class="ps-4">
                                <div class="fw-semibold">
                                    {{ ent.entr_nombre_archivo }}
                                </div>
                                <div
                                    class="text-muted small"
                                    v-if="ent.entr_version"
                                >
                                    v{{ ent.entr_version }}
                                </div>
                            </td>
                            <td class="small fw-semibold text-danger">
                                {{ ent.equipo?.equ_codigo }}
                            </td>
                            <td class="small">
                                {{ ent.tipo_entregable?.tet_nombre }}
                            </td>
                            <td class="small text-muted">
                                {{
                                    new Date(
                                        ent.entr_fecha_subida,
                                    ).toLocaleDateString("es-VE")
                                }}
                            </td>
                            <td>
                                <span
                                    v-if="ent.entr_aprobado === null"
                                    class="badge bg-warning text-dark"
                                    >Pendiente</span
                                >
                                <span
                                    v-else-if="ent.entr_aprobado"
                                    class="badge bg-success"
                                    >Aprobado</span
                                >
                                <span v-else class="badge bg-danger"
                                    >Rechazado</span
                                >
                            </td>
                            <td class="text-end pe-4">
                                <template v-if="ent.entr_aprobado === null">
                                    <button
                                        class="btn btn-sm btn-success me-1"
                                        @click="aprobar(ent)"
                                    >
                                        <i class="bi bi-check-lg me-1"></i
                                        >Aprobar
                                    </button>
                                    <button
                                        class="btn btn-sm btn-outline-danger"
                                        @click="abrirRechazo(ent)"
                                    >
                                        <i class="bi bi-x-lg me-1"></i>Rechazar
                                    </button>
                                </template>
                                <span v-else class="text-muted small"
                                    >Revisado</span
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Rechazo -->
        <div class="modal fade" id="modalRechazo" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold">Rechazar Entregable</h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <form @submit.prevent="rechazar">
                        <div class="modal-body">
                            <label class="form-label fw-semibold">
                                Motivo del rechazo
                                <span class="text-danger">*</span>
                            </label>
                            <textarea
                                v-model="motivoRechazo"
                                class="form-control"
                                :class="{
                                    'is-invalid': erroresRechazo.observacion,
                                }"
                                rows="4"
                                placeholder="Explica por qué se rechaza el entregable..."
                            ></textarea>
                            <div
                                v-if="erroresRechazo.observacion"
                                class="invalid-feedback"
                            >
                                {{ erroresRechazo.observacion }}
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
                            >
                                Confirmar Rechazo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <ModalConfirmar
            ref="modalAprobarRef"
            modal-id="modalAprobarEntregable"
            titulo="¿Aprobar Entregable?"
            :mensaje="`¿Confirmas que el entregable ${entregableSeleccionado?.entr_nombre_archivo} está correcto y puede enviarse al repositorio?`"
            icono="✅"
            btn-texto="Sí, aprobar"
            btn-clase="btn-success"
            @confirmado="confirmarAprobar"
        />
    </AppLayout>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Modal } from "bootstrap";
import ModalConfirmar from "@/Components/ModalConfirmar.vue";

const props = defineProps({ entregables: Array });

const motivoRechazo = ref("");
const erroresRechazo = ref({});
const entregableSeleccionado = ref(null);
let modalRechazo = null;

const modalAprobarRef = ref(null);
const entregableSeleccionado = ref(null);

const aprobar = (ent) => {
    entregableSeleccionado.value = ent;
    modalAprobarRef.value?.abrir();
};

const confirmarAprobar = () => {
    router.post(
        `/profesor/entregables/${entregableSeleccionado.value.entr_id}/aprobar`,
    );
};

const abrirRechazo = (ent) => {
    entregableSeleccionado.value = ent;
    motivoRechazo.value = "";
    erroresRechazo.value = {};
    modalRechazo =
        modalRechazo || new Modal(document.getElementById("modalRechazo"));
    modalRechazo.show();
};

const rechazar = () => {
    router.post(
        `/profesor/entregables/${entregableSeleccionado.value.entr_id}/rechazar`,
        { observacion: motivoRechazo.value },
        {
            onError: (e) => {
                erroresRechazo.value = e;
            },
            onSuccess: () => modalRechazo?.hide(),
        },
    );
};
</script>
