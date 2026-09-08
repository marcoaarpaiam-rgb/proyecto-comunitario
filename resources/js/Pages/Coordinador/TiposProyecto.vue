<template>
    <AppLayout page-title="Tipos de Proyecto">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Tipos de Proyecto</h4>
                <p class="text-muted small mb-0">
                    Catálogo de tipos de proyecto disponibles
                </p>
            </div>
            <button class="btn btn-danger fw-semibold" @click="abrirModal()">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Tipo
            </button>
        </div>

        <!-- Alerta de éxito -->
        <div
            v-if="$page.props.flash?.success"
            class="alert alert-success alert-dismissible fade show mb-4"
            role="alert"
        >
            {{ $page.props.flash.success }}
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>

        <!-- Tabla -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">#</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="tipos.length === 0">
                            <td colspan="4" class="text-center text-muted py-4">
                                No hay tipos de proyecto registrados
                            </td>
                        </tr>
                        <tr v-for="tipo in tipos" :key="tipo.tpr_id">
                            <td class="ps-4 text-muted">{{ tipo.tpr_id }}</td>
                            <td class="fw-semibold">{{ tipo.tpr_nombre }}</td>
                            <td>
                                <span
                                    class="badge"
                                    :class="
                                        tipo.tpr_status
                                            ? 'bg-success'
                                            : 'bg-secondary'
                                    "
                                >
                                    {{
                                        tipo.tpr_status ? "Activo" : "Inactivo"
                                    }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button
                                    class="btn btn-sm btn-outline-primary me-1"
                                    @click="abrirModal(tipo)"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button
                                    class="btn btn-sm btn-outline-danger"
                                    @click="desactivar(tipo)"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="modalTipo" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            {{
                                editando
                                    ? "Editar Tipo"
                                    : "Nuevo Tipo de Proyecto"
                            }}
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
                                <label class="form-label fw-semibold"
                                    >Nombre
                                    <span class="text-danger">*</span></label
                                >
                                <input
                                    v-model="form.tpr_nombre"
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': errores.tpr_nombre,
                                    }"
                                    placeholder="Ej: Control de Estudios"
                                />
                                <div
                                    v-if="errores.tpr_nombre"
                                    class="invalid-feedback"
                                >
                                    {{ errores.tpr_nombre }}
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
                                {{ editando ? "Actualizar" : "Guardar" }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <ModalConfirmar
            ref="modalConfirmarRef"
            modal-id="modalConfirmarTipo"
            titulo="¿Desactivar Tipo de Proyecto?"
            :mensaje="`¿Seguro que deseas desactivar ${itemAEliminar?.tpr_nombre}?`"
            icono="🏷️"
            btn-texto="Sí, desactivar"
            btn-clase="btn-danger"
            @confirmado="confirmarDesactivar"
        />
    </AppLayout>
</template>

<script setup>
import { ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Modal } from "bootstrap";
import ModalConfirmar from "@/Components/ModalConfirmar.vue";

const props = defineProps({ tipos: Array });
const page = usePage();

const form = ref({ tpr_nombre: "" });
const errores = ref({});
const loading = ref(false);
const editando = ref(null);
let modal = null;

const abrirModal = (tipo = null) => {
    editando.value = tipo;
    form.value = { tpr_nombre: tipo?.tpr_nombre || "" };
    errores.value = {};
    modal = modal || new Modal(document.getElementById("modalTipo"));
    modal.show();
};

const guardar = () => {
    loading.value = true;
    const url = editando.value
        ? `/coordinador/tipos-proyecto/${editando.value.tpr_id}`
        : "/coordinador/tipos-proyecto";
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

const modalConfirmarRef = ref(null);
const itemAEliminar = ref(null);

const desactivar = (tipo) => {
    itemAEliminar.value = tipo;
    modalConfirmarRef.value?.abrir();
};

const confirmarDesactivar = () => {
    router.delete(`/coordinador/tipos-proyecto/${itemAEliminar.value.tpr_id}`);
};
</script>
