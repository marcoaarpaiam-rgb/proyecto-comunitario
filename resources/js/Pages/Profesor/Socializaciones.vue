<template>
    <AppLayout page-title="Socializaciones">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Socializaciones</h4>
                <p class="text-muted small mb-0">
                    Pre-socialización y Socialización Final
                </p>
            </div>
            <button class="btn btn-danger fw-semibold" @click="abrirModal()">
                <i class="bi bi-plus-lg me-2"></i>Nueva Socialización
            </button>
        </div>

        <!-- Lista -->
        <div class="row g-3">
            <div v-if="socializaciones.length === 0" class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-body text-center text-muted py-5">
                        No hay socializaciones registradas
                    </div>
                </div>
            </div>
            <div
                v-for="soc in socializaciones"
                :key="soc.soc_id"
                class="col-md-6"
            >
                <div class="card border-0 shadow-sm h-100">
                    <div
                        class="card-header border-0 pt-3 pb-0 d-flex justify-content-between"
                    >
                        <span
                            class="badge"
                            :class="
                                soc.tipo_socializacion?.tso_orden === 1
                                    ? 'bg-warning text-dark'
                                    : 'bg-success'
                            "
                        >
                            {{ soc.tipo_socializacion?.tso_nombre }}
                        </span>
                        <button
                            class="btn btn-sm btn-outline-primary"
                            @click="abrirResultado(soc)"
                        >
                            <i class="bi bi-clipboard-check me-1"></i>Registrar
                            Resultado
                        </button>
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold">{{ soc.equipo?.equ_codigo }}</h6>
                        <p class="text-muted small mb-1">
                            {{ soc.equipo?.equ_titulo }}
                        </p>
                        <div class="d-flex gap-3 small text-muted mt-2">
                            <span
                                ><i class="bi bi-calendar me-1"></i
                                >{{ soc.soc_fecha }}</span
                            >
                            <span
                                ><i class="bi bi-clock me-1"></i
                                >{{ soc.soc_hora }}</span
                            >
                            <span
                                ><i class="bi bi-geo-alt me-1"></i
                                >{{ soc.soc_lugar }}</span
                            >
                        </div>
                        <div class="mt-2">
                            <span
                                v-if="soc.soc_apto === true"
                                class="badge bg-success"
                                >Apto</span
                            >
                            <span
                                v-else-if="soc.soc_apto === false"
                                class="badge bg-danger"
                                >No Apto</span
                            >
                            <span
                                v-if="soc.soc_aprobado === true"
                                class="badge bg-success ms-1"
                                >Aprobado</span
                            >
                            <span
                                v-else-if="soc.soc_aprobado === false"
                                class="badge bg-danger ms-1"
                                >No Aprobado</span
                            >
                            <span
                                v-if="
                                    soc.soc_apto === null &&
                                    soc.soc_aprobado === null
                                "
                                class="badge bg-secondary"
                                >Pendiente de resultado</span
                            >
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Nueva Socialización -->
        <div class="modal fade" id="modalSoc" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            Nueva Socialización
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
                                        v-model="form.soc_id_equ"
                                        class="form-select"
                                        :class="{
                                            'is-invalid': errores.soc_id_equ,
                                        }"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option
                                            v-for="equ in equipos"
                                            :key="equ.equ_id"
                                            :value="equ.equ_id"
                                        >
                                            {{ equ.equ_codigo }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="errores.soc_id_equ"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.soc_id_equ }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        Tipo <span class="text-danger">*</span>
                                    </label>
                                    <select
                                        v-model="form.soc_id_tso"
                                        class="form-select"
                                        :class="{
                                            'is-invalid': errores.soc_id_tso,
                                        }"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option
                                            v-for="tipo in tiposSocializacion"
                                            :key="tipo.tso_id"
                                            :value="tipo.tso_id"
                                        >
                                            {{ tipo.tso_nombre }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="errores.soc_id_tso"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.soc_id_tso }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold"
                                        >Fecha
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        v-model="form.soc_fecha"
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': errores.soc_fecha,
                                        }"
                                    />
                                    <div
                                        v-if="errores.soc_fecha"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.soc_fecha }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold"
                                        >Hora
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        v-model="form.soc_hora"
                                        type="time"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': errores.soc_hora,
                                        }"
                                    />
                                    <div
                                        v-if="errores.soc_hora"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.soc_hora }}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold"
                                        >Lugar
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        v-model="form.soc_lugar"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': errores.soc_lugar,
                                        }"
                                        placeholder="Ej: Sala A"
                                    />
                                    <div
                                        v-if="errores.soc_lugar"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.soc_lugar }}
                                    </div>
                                </div>

                                <!-- Jurados -->
                                <div class="col-12">
                                    <div
                                        class="d-flex justify-content-between align-items-center mb-2"
                                    >
                                        <label
                                            class="form-label fw-semibold mb-0"
                                            >Jurados</label
                                        >
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            @click="agregarJurado"
                                        >
                                            <i class="bi bi-plus"></i> Agregar
                                            Jurado
                                        </button>
                                    </div>
                                    <div
                                        v-for="(jur, idx) in form.jurados"
                                        :key="idx"
                                        class="row g-2 mb-2 align-items-center"
                                    >
                                        <div class="col-5">
                                            <input
                                                v-model="jur.nombre_externo"
                                                type="text"
                                                class="form-control form-control-sm"
                                                placeholder="Nombre del jurado"
                                            />
                                        </div>
                                        <div class="col-4">
                                            <select
                                                v-model="jur.tju_id"
                                                class="form-select form-select-sm"
                                            >
                                                <option value="">
                                                    Tipo jurado...
                                                </option>
                                                <option
                                                    v-for="tju in tiposJurado"
                                                    :key="tju.tju_id"
                                                    :value="tju.tju_id"
                                                >
                                                    {{ tju.tju_nombre }}
                                                </option>
                                            </select>
                                        </div>
                                        <div class="col-2">
                                            <div class="form-check">
                                                <input
                                                    v-model="
                                                        jur.es_de_comunidad
                                                    "
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    :id="`com-${idx}`"
                                                />
                                                <label
                                                    class="form-check-label small"
                                                    :for="`com-${idx}`"
                                                >
                                                    Comunidad
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-1">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                @click="quitarJurado(idx)"
                                            >
                                                <i class="bi bi-x"></i>
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
                                Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Resultado -->
        <div class="modal fade" id="modalResultado" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #198754">
                        <h5 class="modal-title text-white fw-bold">
                            Registrar Resultado
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <form @submit.prevent="guardarResultado">
                        <div class="modal-body">
                            <!-- Pre-socialización: apto -->
                            <div
                                v-if="
                                    socSeleccionada?.tipo_socializacion
                                        ?.tso_orden === 1
                                "
                                class="mb-3"
                            >
                                <label class="form-label fw-semibold"
                                    >¿El equipo es apto?</label
                                >
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input
                                            v-model="formResultado.soc_apto"
                                            type="radio"
                                            :value="true"
                                            class="form-check-input"
                                            id="aptoSi"
                                        />
                                        <label
                                            class="form-check-label"
                                            for="aptoSi"
                                            >Sí, es apto</label
                                        >
                                    </div>
                                    <div class="form-check">
                                        <input
                                            v-model="formResultado.soc_apto"
                                            type="radio"
                                            :value="false"
                                            class="form-check-input"
                                            id="aptoNo"
                                        />
                                        <label
                                            class="form-check-label"
                                            for="aptoNo"
                                            >No es apto</label
                                        >
                                    </div>
                                </div>
                            </div>
                            <!-- Socialización final: aprobado -->
                            <div
                                v-if="
                                    socSeleccionada?.tipo_socializacion
                                        ?.tso_orden === 2
                                "
                                class="mb-3"
                            >
                                <label class="form-label fw-semibold"
                                    >¿El proyecto fue aprobado?</label
                                >
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input
                                            v-model="formResultado.soc_aprobado"
                                            type="radio"
                                            :value="true"
                                            class="form-check-input"
                                            id="aprobSi"
                                        />
                                        <label
                                            class="form-check-label"
                                            for="aprobSi"
                                            >Aprobado</label
                                        >
                                    </div>
                                    <div class="form-check">
                                        <input
                                            v-model="formResultado.soc_aprobado"
                                            type="radio"
                                            :value="false"
                                            class="form-check-input"
                                            id="aprobNo"
                                        />
                                        <label
                                            class="form-check-label"
                                            for="aprobNo"
                                            >No Aprobado</label
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Observaciones</label
                                >
                                <textarea
                                    v-model="formResultado.soc_observaciones"
                                    class="form-control"
                                    rows="3"
                                ></textarea>
                            </div>
                            <div
                                v-if="formResultado.soc_apto === false"
                                class="mb-3"
                            >
                                <label
                                    class="form-label fw-semibold text-danger"
                                >
                                    Acta de no aptitud
                                </label>
                                <textarea
                                    v-model="
                                        formResultado.soc_observaciones_no_apto
                                    "
                                    class="form-control"
                                    rows="3"
                                    placeholder="Explica por qué el equipo no es apto..."
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
                                class="btn btn-success fw-semibold"
                            >
                                Guardar Resultado
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
    socializaciones: Array,
    equipos: Array,
    tiposSocializacion: Array,
    tiposJurado: Array,
});

const form = ref({
    soc_id_equ: "",
    soc_id_tso: "",
    soc_fecha: "",
    soc_hora: "",
    soc_lugar: "",
    jurados: [],
    integrantes: [],
});
const formResultado = ref({
    soc_apto: null,
    soc_aprobado: null,
    soc_observaciones: "",
    soc_observaciones_no_apto: "",
});
const errores = ref({});
const loading = ref(false);
const socSeleccionada = ref(null);
let modal = null;
let modalRes = null;

const abrirModal = () => {
    form.value = {
        soc_id_equ: "",
        soc_id_tso: "",
        soc_fecha: "",
        soc_hora: "",
        soc_lugar: "",
        jurados: [],
        integrantes: [],
    };
    errores.value = {};
    modal = modal || new Modal(document.getElementById("modalSoc"));
    modal.show();
};

const abrirResultado = (soc) => {
    socSeleccionada.value = soc;
    formResultado.value = {
        soc_apto: soc.soc_apto,
        soc_aprobado: soc.soc_aprobado,
        soc_observaciones: soc.soc_observaciones || "",
        soc_observaciones_no_apto: soc.soc_observaciones_no_apto || "",
        integrantes: (soc.integrantes || []).map((i) => ({
            usu_id: i.iso_id_usu,
            avanza: i.iso_avanza,
            justificacion: i.iso_justificacion || "",
        })),
    };
    modalRes = modalRes || new Modal(document.getElementById("modalResultado"));
    modalRes.show();
};

const agregarJurado = () => {
    form.value.jurados.push({
        nombre_externo: "",
        tju_id: "",
        es_de_comunidad: false,
    });
};
const quitarJurado = (idx) => form.value.jurados.splice(idx, 1);

const guardar = () => {
    loading.value = true;
    router.post("/profesor/socializaciones", form.value, {
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

const guardarResultado = () => {
    router.post(
        `/profesor/socializaciones/${socSeleccionada.value.soc_id}/resultado`,
        formResultado.value,
        { onSuccess: () => modalRes?.hide() },
    );
};
</script>
