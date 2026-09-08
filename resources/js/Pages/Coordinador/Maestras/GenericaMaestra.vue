<template>
    <AppLayout :page-title="config.titulo">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">{{ config.titulo }}</h4>
                <p class="text-muted small mb-0">{{ config.subtitulo }}</p>
            </div>
            <button class="btn btn-danger fw-semibold" @click="abrirModal()">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Registro
            </button>
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
                            <th class="ps-4">#</th>
                            <th>Nombre</th>
                            <th v-if="config.extra">Trayecto</th>
                            <th>Estado</th>
                            <th class="text-end pe-4">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="registros.length === 0">
                            <td
                                :colspan="config.extra ? 5 : 4"
                                class="text-center text-muted py-4"
                            >
                                No hay registros
                            </td>
                        </tr>
                        <tr v-for="reg in registros" :key="reg[config.pk]">
                            <td class="ps-4 text-muted">
                                {{ reg[config.pk] }}
                            </td>
                            <td class="fw-semibold">{{ reg[config.campo] }}</td>
                            <td v-if="config.extra">
                                <span
                                    class="badge bg-primary bg-opacity-10 text-primary"
                                >
                                    Trayecto {{ reg[config.extra] }}
                                </span>
                            </td>
                            <td>
                                <span
                                    class="badge"
                                    :class="
                                        reg[config.status]
                                            ? 'bg-success'
                                            : 'bg-secondary'
                                    "
                                >
                                    {{
                                        reg[config.status]
                                            ? "Activo"
                                            : "Inactivo"
                                    }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <button
                                    class="btn btn-sm btn-outline-primary me-1"
                                    @click="abrirModal(reg)"
                                >
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button
                                    class="btn btn-sm btn-outline-danger"
                                    @click="desactivar(reg)"
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
        <div class="modal fade" id="modalMaestra" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            {{
                                editando ? "Editar Registro" : "Nuevo Registro"
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
                                <label class="form-label fw-semibold">
                                    Nombre <span class="text-danger">*</span>
                                </label>
                                <input
                                    v-model="form.nombre"
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': errores[config.campo],
                                    }"
                                    placeholder="Ingresa el nombre"
                                />
                                <div
                                    v-if="errores[config.campo]"
                                    class="invalid-feedback"
                                >
                                    {{ errores[config.campo] }}
                                </div>
                            </div>
                            <div v-if="config.extra" class="mb-3">
                                <label class="form-label fw-semibold">
                                    Trayecto <span class="text-danger">*</span>
                                </label>
                                <select
                                    v-model="form.extra"
                                    class="form-select"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option value="1">Trayecto I</option>
                                    <option value="2">Trayecto II</option>
                                    <option value="3">Trayecto III</option>
                                    <option value="4">Trayecto IV</option>
                                </select>
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
            modal-id="modalConfirmarMaestra"
            titulo="¿Desactivar Registro?"
            :mensaje="`¿Seguro que deseas desactivar este registro?`"
            icono="⚠️"
            btn-texto="Sí, desactivar"
            btn-clase="btn-danger"
            @confirmado="confirmarDesactivar"
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
    registros: Array,
    config: Object,
    tabla: String,
});

const form = ref({ nombre: "", extra: "" });
const errores = ref({});
const loading = ref(false);
const editando = ref(null);
let modal = null;

const abrirModal = (reg = null) => {
    editando.value = reg;
    form.value = {
        nombre: reg ? reg[props.config.campo] : "",
        extra: reg && props.config.extra ? reg[props.config.extra] : "",
    };
    errores.value = {};
    modal = modal || new Modal(document.getElementById("modalMaestra"));
    modal.show();
};

const guardar = () => {
    loading.value = true;
    const payload = { [props.config.campo]: form.value.nombre };
    if (props.config.extra) payload[props.config.extra] = form.value.extra;

    const base = `/coordinador/maestras/${props.tabla}`;
    const url = editando.value
        ? `${base}/${editando.value[props.config.pk]}`
        : base;
    const method = editando.value ? "put" : "post";

    router[method](url, payload, {
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

const desactivar = (reg) => {
    itemAEliminar.value = reg;
    modalConfirmarRef.value?.abrir();
};

const confirmarDesactivar = () => {
    router.delete(
        `/coordinador/maestras/${props.tabla}/${itemAEliminar.value[props.config.pk]}`,
    );
};
</script>
