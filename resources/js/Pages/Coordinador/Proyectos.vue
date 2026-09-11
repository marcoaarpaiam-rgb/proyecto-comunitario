<template>
    <AppLayout page-title="Proyectos">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Proyectos Comunitarios</h4>
                <p class="text-muted small mb-0">
                    Gestión de proyectos por equipo
                </p>
            </div>
            <button class="btn btn-danger fw-semibold" @click="abrirModal()">
                <i class="bi bi-plus-lg me-2"></i>Nuevo Proyecto
            </button>
        </div>

        <!-- Tabla -->
        <TablaBuscable
            :items="proyectos"
            :campos-busqueda="[
                'equipo.equ_codigo',
                'equipo.equ_titulo',
                'comunidad.com_nombre',
                'tipo_proyecto.tpr_nombre',
                'modalidad.mpr_nombre',
            ]"
            :por-pagina="8"
            placeholder="Buscar por equipo, comunidad o tipo..."
        >
            <template #filtros>
                <select
                    v-model="filtroAprobacion"
                    class="form-select form-select-sm"
                    style="width: 180px"
                >
                    <option value="">Todos los estados</option>
                    <option value="aprobado">Aprobados</option>
                    <option value="pendiente">Pendientes</option>
                </select>
            </template>

            <template #default="{ registros }">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Equipo</th>
                                    <th>Comunidad</th>
                                    <th>Tipo</th>
                                    <th>Modalidad</th>
                                    <th>Fecha Límite</th>
                                    <th>Aprobación</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-if="
                                        proyectosFiltrados(registros).length ===
                                        0
                                    "
                                >
                                    <td
                                        colspan="7"
                                        class="text-center text-muted py-4"
                                    >
                                        No se encontraron proyectos
                                    </td>
                                </tr>
                                <tr
                                    v-for="pco in proyectosFiltrados(registros)"
                                    :key="pco.pco_id"
                                >
                                    <td class="ps-4">
                                        <div class="fw-semibold text-danger">
                                            {{ pco.equipo?.equ_codigo }}
                                        </div>
                                        <div
                                            class="text-muted small text-truncate"
                                            style="max-width: 180px"
                                        >
                                            {{ pco.equipo?.equ_titulo }}
                                        </div>
                                    </td>
                                    <td class="small">
                                        {{ pco.comunidad?.com_nombre }}
                                    </td>
                                    <td class="small">
                                        {{ pco.tipo_proyecto?.tpr_nombre }}
                                    </td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            {{ pco.modalidad?.mpr_nombre }}
                                        </span>
                                    </td>
                                    <td class="small">
                                        {{ pco.pco_fecha_limite }}
                                    </td>
                                    <td>
                                        <span
                                            v-if="pco.pco_fecha_aprobacion"
                                            class="badge bg-success"
                                            >Aprobado</span
                                        >
                                        <button
                                            v-else
                                            class="btn btn-sm btn-outline-success"
                                            @click="aprobar(pco)"
                                        >
                                            <i class="bi bi-check-lg me-1"></i
                                            >Aprobar
                                        </button>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            @click="desactivar(pco)"
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

        <!-- Modal Nuevo Proyecto -->
        <div class="modal fade" id="modalProyecto" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            Nuevo Proyecto
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <form @submit.prevent="guardar">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Equipo
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        v-model="form.pco_id_equ"
                                        class="form-select"
                                        :class="{
                                            'is-invalid': errores.pco_id_equ,
                                        }"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option
                                            v-for="equ in equipos"
                                            :key="equ.equ_id"
                                            :value="equ.equ_id"
                                        >
                                            {{ equ.equ_codigo }} —
                                            {{ equ.equ_titulo }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="errores.pco_id_equ"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.pco_id_equ }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Comunidad
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        v-model="form.pco_id_com"
                                        class="form-select"
                                        :class="{
                                            'is-invalid': errores.pco_id_com,
                                        }"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option
                                            v-for="com in comunidades"
                                            :key="com.com_id"
                                            :value="com.com_id"
                                        >
                                            {{ com.com_nombre }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="errores.pco_id_com"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.pco_id_com }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Tipo de Proyecto
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        v-model="form.pco_id_tpr"
                                        class="form-select"
                                        :class="{
                                            'is-invalid': errores.pco_id_tpr,
                                        }"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option
                                            v-for="tipo in tipos"
                                            :key="tipo.tpr_id"
                                            :value="tipo.tpr_id"
                                        >
                                            {{ tipo.tpr_nombre }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="errores.pco_id_tpr"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.pco_id_tpr }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Modalidad
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        v-model="form.pco_id_mpr"
                                        class="form-select"
                                        :class="{
                                            'is-invalid': errores.pco_id_mpr,
                                        }"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option
                                            v-for="mod in modalidades"
                                            :key="mod.mpr_id"
                                            :value="mod.mpr_id"
                                        >
                                            {{ mod.mpr_nombre }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="errores.pco_id_mpr"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.pco_id_mpr }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Fecha Límite
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input
                                        v-model="form.pco_fecha_limite"
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                errores.pco_fecha_limite,
                                        }"
                                    />
                                    <div
                                        v-if="errores.pco_fecha_limite"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.pco_fecha_limite }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        Resumen
                                        <span class="text-danger">*</span>
                                        <small class="text-muted"
                                            >(máx. 150 caracteres)</small
                                        >
                                    </label>
                                    <textarea
                                        v-model="form.pco_resumen"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': errores.pco_resumen,
                                        }"
                                        rows="2"
                                        maxlength="150"
                                        placeholder="Describe brevemente el proyecto..."
                                    ></textarea>
                                    <div class="d-flex justify-content-between">
                                        <div
                                            v-if="errores.pco_resumen"
                                            class="invalid-feedback d-block"
                                        >
                                            {{ errores.pco_resumen }}
                                        </div>
                                        <small class="text-muted ms-auto">
                                            {{ form.pco_resumen.length }}/150
                                        </small>
                                    </div>
                                </div>

                                <!-- Beneficiarios -->
                                <div class="col-12">
                                    <div
                                        class="d-flex justify-content-between align-items-center mb-2"
                                    >
                                        <label
                                            class="form-label fw-semibold mb-0"
                                        >
                                            Beneficiarios
                                        </label>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            @click="agregarBeneficiario"
                                        >
                                            <i class="bi bi-plus me-1"></i
                                            >Agregar
                                        </button>
                                    </div>
                                    <div
                                        v-for="(ben, idx) in form.beneficiarios"
                                        :key="idx"
                                        class="row g-2 mb-2 align-items-center"
                                    >
                                        <div class="col-7">
                                            <select
                                                v-model="ben.tbe_id"
                                                class="form-select form-select-sm"
                                            >
                                                <option value="">
                                                    Tipo de beneficiario...
                                                </option>
                                                <option
                                                    v-for="tbe in beneficiarios"
                                                    :key="tbe.tbe_id"
                                                    :value="tbe.tbe_id"
                                                >
                                                    {{ tbe.tbe_nombre }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-3">
                                            <input
                                                v-model="ben.cantidad"
                                                type="number"
                                                class="form-control form-control-sm"
                                                placeholder="Cantidad"
                                                min="1"
                                            />
                                        </div>
                                        <div class="col-2">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger w-100"
                                                @click="quitarBeneficiario(idx)"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
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
                                Guardar Proyecto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <ModalConfirmar
            ref="modalAprobarRef"
            modal-id="modalAprobarProyecto"
            titulo="¿Aprobar Comunidad?"
            :mensaje="`¿Confirmas que el equipo ${itemSeleccionado?.equipo?.equ_codigo} puede trabajar con esta comunidad?`"
            icono="✅"
            btn-texto="Sí, aprobar"
            btn-clase="btn-success"
            @confirmado="confirmarAprobar"
        />

        <ModalConfirmar
            ref="modalDesactivarRef"
            modal-id="modalDesactivarProyecto"
            titulo="¿Desactivar Proyecto?"
            mensaje="¿Seguro que deseas desactivar este proyecto?"
            icono="🗑️"
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
    proyectos: Array,
    equipos: Array,
    comunidades: Array,
    tipos: Array,
    modalidades: Array,
    beneficiarios: Array,
});

const form = ref({
    pco_id_equ: "",
    pco_id_com: "",
    pco_id_tpr: "",
    pco_id_mpr: "",
    pco_resumen: "",
    pco_fecha_limite: "",
    beneficiarios: [],
});
const errores = ref({});
const loading = ref(false);
let modal = null;

const abrirModal = () => {
    form.value = {
        pco_id_equ: "",
        pco_id_com: "",
        pco_id_tpr: "",
        pco_id_mpr: "",
        pco_resumen: "",
        pco_fecha_limite: "",
        beneficiarios: [],
    };
    errores.value = {};
    modal = modal || new Modal(document.getElementById("modalProyecto"));
    modal.show();
};

const agregarBeneficiario = () => {
    form.value.beneficiarios.push({ tbe_id: "", cantidad: "" });
};

const quitarBeneficiario = (idx) => {
    form.value.beneficiarios.splice(idx, 1);
};

const guardar = () => {
    loading.value = true;
    router.post("/coordinador/proyectos", form.value, {
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

const modalAprobarRef = ref(null);
const modalDesactivarRef = ref(null);
const itemSeleccionado = ref(null);

const aprobar = (pco) => {
    itemSeleccionado.value = pco;
    modalAprobarRef.value?.abrir();
};

const confirmarAprobar = () => {
    router.post(
        `/coordinador/proyectos/${itemSeleccionado.value.pco_id}/aprobar`,
    );
};

const desactivar = (pco) => {
    itemSeleccionado.value = pco;
    modalDesactivarRef.value?.abrir();
};

const confirmarDesactivar = () => {
    router.delete(`/coordinador/proyectos/${itemSeleccionado.value.pco_id}`);
};
const filtroAprobacion = ref("");

const proyectosFiltrados = (registros) => {
    if (!filtroAprobacion.value) return registros;
    return registros.filter((pco) => {
        if (filtroAprobacion.value === "aprobado")
            return !!pco.pco_fecha_aprobacion;
        if (filtroAprobacion.value === "pendiente")
            return !pco.pco_fecha_aprobacion;
        return true;
    });
};
</script>
