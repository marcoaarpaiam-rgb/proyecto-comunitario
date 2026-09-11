<template>
    <AppLayout page-title="Comunidades">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Comunidades</h4>
                <p class="text-muted small mb-0">
                    Catálogo permanente de comunidades
                </p>
            </div>
            <button class="btn btn-danger fw-semibold" @click="abrirModal()">
                <i class="bi bi-plus-lg me-2"></i>Nueva Comunidad
            </button>
        </div>

        <TablaBuscable
            :items="comunidades"
            :campos-busqueda="[
                'com_nombre',
                'com_ubicacion',
                'tipo_organizacion.tor_nombre',
            ]"
            :por-pagina="8"
            placeholder="Buscar por nombre, ubicación o tipo..."
        >
            <template #default="{ registros }">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Nombre</th>
                                    <th>Ubicación</th>
                                    <th>Tipo</th>
                                    <th>Estado</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="registros.length === 0">
                                    <td
                                        colspan="5"
                                        class="text-center text-muted py-4"
                                    >
                                        No se encontraron comunidades
                                    </td>
                                </tr>
                                <tr v-for="com in registros" :key="com.com_id">
                                    <td class="ps-4 fw-semibold">
                                        {{ com.com_nombre }}
                                    </td>
                                    <td class="text-muted small">
                                        {{ com.com_ubicacion }}
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary"
                                        >
                                            {{
                                                com.tipo_organizacion
                                                    ?.tor_nombre
                                            }}
                                        </span>
                                    </td>
                                    <td>
                                        <span
                                            class="badge"
                                            :class="
                                                com.com_status
                                                    ? 'bg-success'
                                                    : 'bg-secondary'
                                            "
                                        >
                                            {{
                                                com.com_status
                                                    ? "Activa"
                                                    : "Inactiva"
                                            }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button
                                            class="btn btn-sm btn-outline-primary me-1"
                                            @click="abrirModal(com)"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            @click="desactivar(com)"
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

        <!-- Modal -->
        <div class="modal fade" id="modalComunidad" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            {{
                                editando
                                    ? "Editar Comunidad"
                                    : "Nueva Comunidad"
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
                                    v-model="form.com_nombre"
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': errores.com_nombre,
                                    }"
                                    placeholder="Ej: Consejo Comunal Simón Bolívar"
                                />
                                <div
                                    v-if="errores.com_nombre"
                                    class="invalid-feedback"
                                >
                                    {{ errores.com_nombre }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Ubicación
                                    <span class="text-danger">*</span></label
                                >
                                <input
                                    v-model="form.com_ubicacion"
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': errores.com_ubicacion,
                                    }"
                                    placeholder="Ej: Sector Bolívar, Acarigua"
                                />
                                <div
                                    v-if="errores.com_ubicacion"
                                    class="invalid-feedback"
                                >
                                    {{ errores.com_ubicacion }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Tipo de Organización
                                    <span class="text-danger">*</span></label
                                >
                                <select
                                    v-model="form.com_id_tor"
                                    class="form-select"
                                    :class="{
                                        'is-invalid': errores.com_id_tor,
                                    }"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option
                                        v-for="tipo in tiposOrganizacion"
                                        :key="tipo.tor_id"
                                        :value="tipo.tor_id"
                                    >
                                        {{ tipo.tor_nombre }}
                                    </option>
                                </select>
                                <div
                                    v-if="errores.com_id_tor"
                                    class="invalid-feedback"
                                >
                                    {{ errores.com_id_tor }}
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
            modal-id="modalConfirmarComunidad"
            titulo="¿Desactivar Comunidad?"
            :mensaje="`¿Seguro que deseas desactivar la comunidad ${itemAEliminar?.com_nombre}?`"
            icono="🏢"
            btn-texto="Sí, desactivar"
            btn-clase="btn-danger"
            @confirmado="confirmarDesactivar"
        />
    </AppLayout>
</template>

<script setup>
import TablaBuscable from "@/Components/TablaBuscable.vue";
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Modal } from "bootstrap";
import ModalConfirmar from "@/Components/ModalConfirmar.vue";

const props = defineProps({
    comunidades: Array,
    tiposOrganizacion: Array,
});

const form = ref({ com_nombre: "", com_ubicacion: "", com_id_tor: "" });
const errores = ref({});
const loading = ref(false);
const editando = ref(null);
let modal = null;

const abrirModal = (com = null) => {
    editando.value = com;
    form.value = {
        com_nombre: com?.com_nombre || "",
        com_ubicacion: com?.com_ubicacion || "",
        com_id_tor: com?.com_id_tor || "",
    };
    errores.value = {};
    modal = modal || new Modal(document.getElementById("modalComunidad"));
    modal.show();
};

const guardar = () => {
    loading.value = true;
    const url = editando.value
        ? `/coordinador/comunidades/${editando.value.com_id}`
        : "/coordinador/comunidades";
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

const desactivar = (com) => {
    itemAEliminar.value = com;
    modalConfirmarRef.value?.abrir();
};

const confirmarDesactivar = () => {
    router.delete(`/coordinador/comunidades/${itemAEliminar.value.com_id}`);
};
</script>
