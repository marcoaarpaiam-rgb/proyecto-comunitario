<template>
    <div class="modal fade" id="mPunto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
            <form @submit.prevent="guardar" class="modal-content border-0 shadow-lg">
                <div class="mh">
                    <h5>
                        <i class="bi bi-flag-fill text-danger me-2"></i>Nuevo Punto de Control
                    </h5>
                    <button type="button" class="btn btn-sm text-white p-0 fs-5" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Sección</label>
                            <SelectBuscable
                                v-model="seccionSeleccionadaModal"
                                :options="opcionesSeccionesModal"
                                size="sm"
                                placeholder="Todas las secciones..."
                                search-placeholder="Buscar sección..."
                                @change="onSeccionModalChange"
                            />
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Equipo <span class="text-danger">*</span>
                            </label>
                            <SelectBuscableEquipo
                                v-model="form.puc_id_equ"
                                :equipos="equiposModalFiltrados"
                                :has-error="!!errores.puc_id_equ"
                                placeholder="🔍 Escribe código, título o sección del equipo..."
                            />
                            <div v-if="errores.puc_id_equ" class="text-danger small mt-1">
                                {{ errores.puc_id_equ }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">
                                Nombre del Punto <span class="text-danger">*</span>
                            </label>
                            <input
                                v-model="form.puc_nombre"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': errores.puc_nombre }"
                                placeholder="Ej: Punto de Control 1 — Diagnóstico"
                                required
                            />
                            <div v-if="errores.puc_nombre" class="invalid-feedback">
                                {{ errores.puc_nombre }}
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">
                                    Semana (1 a 18) <span class="text-danger">*</span>
                                </label>
                                <input
                                    v-model.number="form.puc_semana"
                                    type="number"
                                    min="1"
                                    max="18"
                                    step="1"
                                    class="form-control"
                                    :class="{ 'is-invalid': errorSemana || errores.puc_semana }"
                                    @input="validarSemanaInput"
                                    required
                                />
                                <div v-if="errorSemana || errores.puc_semana" class="invalid-feedback d-block">
                                    {{ errorSemana || errores.puc_semana }}
                                </div>
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-semibold">
                                    Fecha de Encuentro <span class="text-danger">*</span>
                                </label>
                                <input
                                    v-model="form.puc_fecha_limite"
                                    type="date"
                                    :min="minFechaPermitida"
                                    :max="maxFechaPermitida"
                                    class="form-control"
                                    :class="{ 'is-invalid': errorFecha || errores.puc_fecha_limite }"
                                    @change="validarFechaInput"
                                    required
                                />
                                <small v-if="periodoActivo?.lapso_academico" class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                    Lapso {{ periodoActivo.lapso_academico }}: {{ periodoActivo.fecha_inicio_formato }} al {{ periodoActivo.fecha_fin_formato }}
                                </small>
                                <div v-if="errorFecha || errores.puc_fecha_limite" class="invalid-feedback d-block">
                                    {{ errorFecha || errores.puc_fecha_limite }}
                                </div>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Días Aviso</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    v-model.number="form.puc_dias_aviso"
                                    min="1"
                                    max="30"
                                />
                            </div>
                        </div>

                        <div class="mt-3">
                            <label class="form-label fw-semibold">Descripción</label>
                            <textarea v-model="form.puc_descripcion" class="form-control" rows="2"></textarea>
                        </div>

                        <div class="mt-3">
                            <div
                                class="d-flex align-items-start gap-3 p-3 rounded"
                                style="background: rgba(220, 53, 69, 0.06); border: 1px solid rgba(220, 53, 69, 0.25);"
                            >
                                <div class="form-check form-switch m-0 p-0 d-flex align-items-center flex-shrink-0" style="min-height: 24px;">
                                    <input
                                        class="form-check-input m-0"
                                        type="checkbox"
                                        id="checkPucUltimoPunto"
                                        v-model="form.puc_es_ultimo_punto"
                                        style="cursor: pointer; width: 2.3em; height: 1.25em;"
                                    />
                                </div>
                                <label class="form-check-label flex-grow-1" for="checkPucUltimoPunto" style="cursor: pointer;">
                                    <span class="fw-bold d-block">
                                        <i class="bi bi-flag-fill text-danger me-1"></i>
                                        ¿Es el último punto de control del semestre?
                                    </span>
                                    <span class="small text-muted d-block mt-1">
                                        Sus observaciones se transferirán como antecedentes a la Pre-Socialización.
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger fw-bold" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm me-2"></span>
                            Guardar
                        </button>
                    </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Modal } from "bootstrap";
import SelectBuscableEquipo from "@/Components/SelectBuscableEquipo.vue";
import SelectBuscable from "@/Components/SelectBuscable.vue";

const props = defineProps({
    secciones: { type: Array, default: () => [] },
    equipos: { type: Array, default: () => [] },
});

const opcionesSeccionesModal = computed(() => [
    { value: "", label: "Todas las secciones..." },
    ...(props.secciones || []).map((sec) => ({
        value: sec.sec_id,
        label: `Sección ${sec.sec_codigo}`,
        sublabel: sec.trayecto?.tra_nombre || "",
    })),
]);

const emit = defineEmits(["guardado"]);
const page = usePage();

const periodoActivo = computed(() => page.props.periodoActivo || null);
const fechaHoy = computed(() => new Date().toISOString().split("T")[0]);

const minFechaPermitida = computed(() => {
    const ini = periodoActivo.value?.fecha_inicio;
    if (ini && ini > fechaHoy.value) return ini;
    return fechaHoy.value;
});

const maxFechaPermitida = computed(() => periodoActivo.value?.fecha_fin || null);

const seccionSeleccionadaModal = ref("");
const loading = ref(false);
const errores = ref({});
const errorSemana = ref("");
const errorFecha = ref("");
let mPunto = null;

const form = ref({
    puc_id_equ: "",
    puc_nombre: "",
    puc_semana: 6,
    puc_fecha_limite: "",
    puc_dias_aviso: 3,
    puc_descripcion: "",
    puc_es_ultimo_punto: false,
});

const equiposModalFiltrados = computed(() => {
    if (!seccionSeleccionadaModal.value) return props.equipos || [];
    return (props.equipos || []).filter(
        (e) => String(e.equ_id_sec) === String(seccionSeleccionadaModal.value),
    );
});

const onSeccionModalChange = () => {
    if (
        form.value.puc_id_equ &&
        !equiposModalFiltrados.value.some((e) => String(e.equ_id) === String(form.value.puc_id_equ))
    ) {
        form.value.puc_id_equ = "";
    }
};

const validarSemanaInput = () => {
    const val = Number(form.value.puc_semana);
    if (!Number.isInteger(val) || val < 1 || val > 18) {
        errorSemana.value = "La semana debe estar estrictamente entre 1 y 18.";
        return false;
    }
    errorSemana.value = "";
    return true;
};

const validarFechaInput = () => {
    const f = form.value.puc_fecha_limite;
    if (!f) {
        errorFecha.value = "La fecha de encuentro es obligatoria.";
        return false;
    }
    if (f < fechaHoy.value) {
        errorFecha.value = "La fecha de encuentro no puede ser anterior al día de hoy.";
        return false;
    }
    if (periodoActivo.value?.fecha_inicio && f < periodoActivo.value.fecha_inicio) {
        errorFecha.value = `La fecha debe estar dentro del lapso activo (desde ${periodoActivo.value.fecha_inicio_formato}).`;
        return false;
    }
    if (periodoActivo.value?.fecha_fin && f > periodoActivo.value.fecha_fin) {
        errorFecha.value = `La fecha debe estar dentro del lapso activo (hasta ${periodoActivo.value.fecha_fin_formato}).`;
        return false;
    }
    errorFecha.value = "";
    return true;
};

const abrir = () => {
    seccionSeleccionadaModal.value = "";
    errorSemana.value = "";
    errorFecha.value = "";
    errores.value = {};
    form.value = {
        puc_id_equ: "",
        puc_nombre: "",
        puc_semana: 6,
        puc_fecha_limite: "",
        puc_dias_aviso: 3,
        puc_descripcion: "",
        puc_es_ultimo_punto: false,
    };
    const el = document.getElementById("mPunto");
    if (el) {
        mPunto = Modal.getInstance(el) || new Modal(el);
        mPunto.show();
    }
};

const guardar = () => {
    if (!validarSemanaInput() || !validarFechaInput()) return;
    loading.value = true;
    router.post("/profesor/puntos-control", form.value, {
        onError: (e) => {
            errores.value = e;
            loading.value = false;
        },
        onSuccess: () => {
            loading.value = false;
            mPunto?.hide();
            emit("guardado");
        },
    });
};

defineExpose({ abrir });
</script>

<style scoped>
.mh {
    background: #1a1d23;
    border-bottom: 2px solid #dc3545;
    padding: 1rem 1.3rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.mh h5 {
    color: #fff;
    font-weight: 800;
    margin: 0;
    font-size: 1rem;
    display: flex;
    align-items: center;
}
.modal-content {
    background: var(--bg-card, #ffffff);
    color: var(--text-main, #1a1a1a);
    border: 1px solid var(--border, #dee2e6) !important;
    overflow: hidden;
}
.modal-body,
.modal-footer {
    background: var(--bg-card, #ffffff);
    color: var(--text-main, #1a1a1a);
    border-color: var(--border, #dee2e6) !important;
}
.form-control,
.form-select {
    background: var(--bg-card, #ffffff);
    color: var(--text-main, #1a1a1a);
    border-color: var(--border, #dee2e6);
}
</style>
