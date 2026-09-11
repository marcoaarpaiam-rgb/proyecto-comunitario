<template>
    <AppLayout page-title="Mis Entregables">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Mis Entregables</h4>
                <p class="text-muted small mb-0">
                    Sube los documentos del proyecto para revisión del profesor
                </p>
            </div>
            <button class="btn btn-danger fw-semibold" @click="abrirModal()">
                <i class="bi bi-cloud-upload me-2"></i>Subir Entregable
            </button>
        </div>

        <!-- Info del equipo -->
        <div
            v-if="equipo"
            class="card border-0 shadow-sm mb-4 border-start border-danger border-4"
        >
            <div class="card-body py-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="bi bi-people-fill text-danger fs-3"></i>
                    <div>
                        <div class="fw-bold">
                            {{ equipo.equ_codigo }} — {{ equipo.equ_titulo }}
                        </div>
                        <div class="text-muted small">
                            Trayecto: {{ equipo.trayecto?.tra_nombre }} |
                            Sección: {{ equipo.seccion?.sec_codigo }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Lista de entregables -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nombre</th>
                            <th>Tipo</th>
                            <th>Versión</th>
                            <th>Subido</th>
                            <th>A tiempo</th>
                            <th>Estado</th>
                            <th>Observación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="entregables.length === 0">
                            <td colspan="7" class="text-center text-muted py-4">
                                No has subido entregables aún
                            </td>
                        </tr>
                        <tr v-for="ent in entregables" :key="ent.entr_id">
                            <td class="ps-4 fw-semibold">
                                {{ ent.entr_nombre_archivo }}
                            </td>
                            <td class="small">
                                {{ ent.tipo_entregable?.tet_nombre }}
                            </td>
                            <td class="small text-muted">
                                {{ ent.entr_version || "—" }}
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
                                    v-if="ent.entr_a_tiempo === null"
                                    class="text-muted small"
                                    >—</span
                                >
                                <span
                                    v-else-if="ent.entr_a_tiempo"
                                    class="badge bg-success"
                                    >Sí</span
                                >
                                <span v-else class="badge bg-warning text-dark"
                                    >No</span
                                >
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
                            <td class="small text-danger">
                                {{ ent.entr_observacion_rechazo || "" }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal Subir -->
        <div class="modal fade" id="modalSubir" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            Subir Entregable
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <form @submit.prevent="subir" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Tipo de Entregable
                                    <span class="text-danger">*</span>
                                </label>
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
                                    Nombre del Archivo
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    v-model="form.entr_nombre_archivo"
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            errores.entr_nombre_archivo,
                                    }"
                                    placeholder="Ej: Manual de Usuario v2"
                                />
                                <div
                                    v-if="errores.entr_nombre_archivo"
                                    class="invalid-feedback"
                                >
                                    {{ errores.entr_nombre_archivo }}
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
                                    placeholder="Ej: 1.0"
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Archivo <span class="text-danger">*</span>
                                </label>
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
                                    v-if="errores.archivo"
                                    class="invalid-feedback"
                                >
                                    {{ errores.archivo }}
                                </div>
                                <div class="form-text">
                                    PDF, Word o ZIP. Máximo 20MB.
                                </div>
                            </div>
                            <div class="form-check">
                                <input
                                    v-model="form.entr_es_entregable_final"
                                    type="checkbox"
                                    class="form-check-input"
                                    id="esFinal"
                                />
                                <label
                                    class="form-check-label fw-semibold"
                                    for="esFinal"
                                >
                                    Este es el entregable final para el
                                    repositorio
                                </label>
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
                                <i v-else class="bi bi-cloud-upload me-2"></i>
                                Subir
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Modal } from "bootstrap";

const props = defineProps({
    equipo: Object,
    entregables: Array,
    tiposEntregable: Array,
});

const form = ref({
    entr_id_tet: "",
    entr_nombre_archivo: "",
    entr_version: "",
    entr_es_entregable_final: false,
    archivo: null,
});
const errores = ref({});
const loading = ref(false);
let modal = null;

const abrirModal = () => {
    form.value = {
        entr_id_tet: "",
        entr_nombre_archivo: "",
        entr_version: "",
        entr_es_entregable_final: false,
        archivo: null,
    };
    errores.value = {};
    modal = modal || new Modal(document.getElementById("modalSubir"));
    modal.show();
};

const subir = () => {
    loading.value = true;
    const data = new FormData();
    data.append("entr_id_tet", form.value.entr_id_tet);
    data.append("entr_nombre_archivo", form.value.entr_nombre_archivo);
    data.append("entr_version", form.value.entr_version);
    data.append(
        "entr_es_entregable_final",
        form.value.entr_es_entregable_final ? 1 : 0,
    );
    if (form.value.archivo) data.append("archivo", form.value.archivo);

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
</script>
