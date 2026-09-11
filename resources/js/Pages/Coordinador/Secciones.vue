<template>
    <AppLayout page-title="Secciones">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Secciones</h4>
                <p class="text-muted small mb-0">
                    Secciones académicas del PNF Informática
                </p>
            </div>
            <button class="btn btn-danger fw-semibold" @click="abrirModal()">
                <i class="bi bi-plus-lg me-2"></i>Nueva Sección
            </button>
        </div>

        <TablaBuscable
            :items="secciones"
            :campos-busqueda="[
                'sec_codigo',
                'trayecto.tra_nombre',
                'turno.tur_nombre',
            ]"
            :por-pagina="8"
            placeholder="Buscar por código, trayecto o turno..."
        >
            <template #default="{ registros }">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Código</th>
                                    <th>Trayecto</th>
                                    <th>Turno</th>
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
                                        No se encontraron secciones
                                    </td>
                                </tr>
                                <tr v-for="sec in registros" :key="sec.sec_id">
                                    <td class="ps-4 fw-bold">
                                        {{ sec.sec_codigo }}
                                    </td>
                                    <td>{{ sec.trayecto?.tra_nombre }}</td>
                                    <td>{{ sec.turno?.tur_nombre }}</td>
                                    <td>
                                        <span
                                            class="badge"
                                            :class="
                                                sec.sec_status
                                                    ? 'bg-success'
                                                    : 'bg-secondary'
                                            "
                                        >
                                            {{
                                                sec.sec_status
                                                    ? "Activa"
                                                    : "Inactiva"
                                            }}
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a
                                            :href="`/coordinador/secciones/${sec.sec_id}/detalle`"
                                            class="btn btn-sm btn-outline-info me-1"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <button
                                            class="btn btn-sm btn-outline-primary me-1"
                                            @click="abrirModal(sec)"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            @click="desactivar(sec)"
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
        <div class="modal fade" id="modalSeccion" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            {{ editando ? "Editar Sección" : "Nueva Sección" }}
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
                                    >Código
                                    <span class="text-danger">*</span></label
                                >
                                <input
                                    v-model="form.sec_codigo"
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': errores.sec_codigo,
                                    }"
                                    placeholder="Ej: 331"
                                />
                                <div
                                    v-if="errores.sec_codigo"
                                    class="invalid-feedback"
                                >
                                    {{ errores.sec_codigo }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Trayecto
                                    <span class="text-danger">*</span></label
                                >
                                <select
                                    v-model="form.sec_id_tra"
                                    class="form-select"
                                    :class="{
                                        'is-invalid': errores.sec_id_tra,
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
                                    v-if="errores.sec_id_tra"
                                    class="invalid-feedback"
                                >
                                    {{ errores.sec_id_tra }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Turno
                                    <span class="text-danger">*</span></label
                                >
                                <select
                                    v-model="form.sec_id_tur"
                                    class="form-select"
                                    :class="{
                                        'is-invalid': errores.sec_id_tur,
                                    }"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option
                                        v-for="tur in turnos"
                                        :key="tur.tur_id"
                                        :value="tur.tur_id"
                                    >
                                        {{ tur.tur_nombre }}
                                    </option>
                                </select>
                                <div
                                    v-if="errores.sec_id_tur"
                                    class="invalid-feedback"
                                >
                                    {{ errores.sec_id_tur }}
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
            modal-id="modalConfirmarSeccion"
            titulo="¿Desactivar Sección?"
            :mensaje="`¿Seguro que deseas desactivar la sección ${itemAEliminar?.sec_codigo}?`"
            icono="📚"
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
    secciones: Array,
    trayectos: Array,
    turnos: Array,
});

const form = ref({ sec_codigo: "", sec_id_tra: "", sec_id_tur: "" });
const errores = ref({});
const loading = ref(false);
const editando = ref(null);
let modal = null;

const abrirModal = (sec = null) => {
    editando.value = sec;
    form.value = {
        sec_codigo: sec?.sec_codigo || "",
        sec_id_tra: sec?.sec_id_tra || "",
        sec_id_tur: sec?.sec_id_tur || "",
    };
    errores.value = {};
    modal = modal || new Modal(document.getElementById("modalSeccion"));
    modal.show();
};

const guardar = () => {
    loading.value = true;
    const url = editando.value
        ? `/coordinador/secciones/${editando.value.sec_id}`
        : "/coordinador/secciones";
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

const desactivar = (sec) => {
    itemAEliminar.value = sec;
    modalConfirmarRef.value?.abrir();
};

const confirmarDesactivar = () => {
    router.delete(`/coordinador/secciones/${itemAEliminar.value.sec_id}`);
};
</script>
