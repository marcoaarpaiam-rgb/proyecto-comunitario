<template>
    <div class="modal fade" id="mNuevoEquipo" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form @submit.prevent="guardarEquipo" class="modal-content border-0 shadow-lg">
                <div class="mh">
                    <h5>
                        <i class="bi bi-people-fill text-danger me-2"></i>Nuevo Equipo
                    </h5>
                    <button type="button" class="btn btn-sm text-white p-0 fs-5" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="modal-body p-4 pb-5">
                        <div
                            v-if="Object.keys(errores).length > 0"
                            class="alert alert-danger py-2 px-3 small mb-3 rounded"
                        >
                            <div class="fw-bold mb-1">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>No se pudo crear el equipo:
                            </div>
                            <ul class="mb-0 ps-3">
                                <li v-for="(err, k) in errores" :key="k">{{ err }}</li>
                            </ul>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Sección Asignada <span class="text-danger">*</span>
                                </label>
                                <SelectBuscable
                                    v-model="form.equ_id_sec"
                                    :options="opcionesSeccionesEquipo"
                                    :has-error="!!errores.equ_id_sec"
                                    placeholder="Seleccionar sección..."
                                    search-placeholder="Buscar sección..."
                                    @change="onSeccionChange"
                                />
                                <div v-if="errores.equ_id_sec" class="invalid-feedback d-block">
                                    {{ errores.equ_id_sec }}
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Trayecto (automático)</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    :value="trayectoSeleccionado || 'Se asigna al seleccionar sección'"
                                    readonly
                                    style="background: var(--bg-th);"
                                />
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">
                                    N° Equipo <span class="text-danger">*</span>
                                </label>
                                <input
                                    type="number"
                                    v-model="form.equ_numero_grupo"
                                    class="form-control"
                                    :class="{ 'is-invalid': errores.equ_numero_grupo }"
                                    min="1"
                                    :max="form.equ_total_seccion || 99"
                                    required
                                />
                                <div v-if="errores.equ_numero_grupo" class="invalid-feedback">
                                    {{ errores.equ_numero_grupo }}
                                </div>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label fw-semibold small">Semestre</label>
                                <input
                                    type="number"
                                    v-model="form.equ_semestre"
                                    class="form-control"
                                    readonly
                                    style="background: var(--bg-th);"
                                />
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold small">
                                    Total Equipos <span class="text-danger">*</span>
                                </label>
                                <div :class="totalFijadoSeccion !== null ? 'input-group' : ''">
                                    <input
                                        type="number"
                                        v-model.number="form.equ_total_seccion"
                                        class="form-control"
                                        :class="{ 'is-invalid': errores.equ_total_seccion }"
                                        :readonly="totalFijadoSeccion !== null && !desbloquearTotal"
                                        :style="totalFijadoSeccion !== null && !desbloquearTotal ? 'background: var(--bg-th); cursor: not-allowed;' : ''"
                                        min="1"
                                        max="99"
                                        required
                                    />
                                    <button
                                        v-if="totalFijadoSeccion !== null"
                                        type="button"
                                        class="btn btn-sm"
                                        :class="desbloquearTotal ? 'btn-warning text-dark' : 'btn-outline-secondary'"
                                        @click="toggleDesbloquearTotal"
                                        :title="desbloquearTotal ? 'Volver a bloquear total fijado' : 'Modificar total de equipos de la sección'"
                                    >
                                        <i class="bi" :class="desbloquearTotal ? 'bi-unlock-fill' : 'bi-lock-fill'"></i>
                                    </button>
                                </div>
                                <small v-if="totalFijadoSeccion !== null && !desbloquearTotal" class="text-muted d-block mt-1" style="font-size: 0.7rem;">
                                    <i class="bi bi-lock-fill me-1"></i>Bloqueado en {{ totalFijadoSeccion }} tras el 1.º equipo (pulsa el candado para cambiarlo)
                                </small>
                                <div v-else-if="totalFijadoSeccion !== null && desbloquearTotal" class="mt-1 d-flex align-items-center justify-content-between gap-1">
                                    <small class="text-warning-emphasis" style="font-size: 0.7rem;">
                                        Se actualizará en toda la sección
                                    </small>
                                    <button
                                        v-if="form.equ_total_seccion && form.equ_total_seccion !== totalFijadoSeccion"
                                        type="button"
                                        class="btn btn-link btn-sm p-0 text-danger fw-semibold"
                                        style="font-size: 0.7rem; text-decoration: underline;"
                                        :disabled="actualizandoTotal"
                                        @click="aplicarSoloTotalSeccion"
                                    >
                                        {{ actualizandoTotal ? 'Guardando...' : 'Aplicar ahora' }}
                                    </button>
                                </div>
                                <div v-if="errores.equ_total_seccion" class="invalid-feedback d-block">
                                    {{ errores.equ_total_seccion }}
                                </div>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold small">Código generado</label>
                                <input
                                    type="text"
                                    class="form-control fw-bold text-center text-danger"
                                    :value="codigoPreview"
                                    readonly
                                    style="background: var(--bg-th);"
                                />
                            </div>

                            <div class="col-12">
                                <label class="form-label fw-semibold">
                                    Título del Proyecto <span class="text-danger">*</span>
                                </label>
                                <input
                                    v-model="form.equ_titulo"
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': errores.equ_titulo }"
                                    maxlength="150"
                                    placeholder="Ej: Sistema Web de Gestión Administrativa..."
                                    required
                                />
                                <div v-if="errores.equ_titulo" class="invalid-feedback">
                                    {{ errores.equ_titulo }}
                                </div>
                            </div>
                        </div>

                        <hr style="border-color: var(--border);" />

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-bold mb-0">
                                <i class="bi bi-people text-danger me-1"></i>
                                Integrantes Inscritos ({{ form.integrantes.length }}/6)
                            </h6>
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-primary rounded-pill px-3"
                                @click="agregarIntegrante"
                                :disabled="form.integrantes.length >= 6"
                            >
                                <i class="bi bi-plus-lg me-1"></i>Agregar Integrante
                            </button>
                        </div>

                        <div v-if="errores.integrantes" class="alert alert-danger py-2 small mb-2">
                            {{ errores.integrantes }}
                        </div>

                        <div
                            class="py-2 px-3 mb-2 small rounded"
                            style="background: rgba(220, 53, 69, 0.08); border-left: 3px solid #dc3545;"
                        >
                            <i class="bi bi-star-fill text-warning me-1"></i>
                            <strong>Líder de Equipo:</strong> El primer estudiante seleccionado quedará designado automáticamente como Líder del equipo para completar la vinculación comunitaria.
                        </div>

                        <div
                            v-for="(item, idx) in form.integrantes"
                            :key="idx"
                            class="row g-2 mb-2 align-items-center"
                        >
                            <div class="col-8">
                                <SelectBuscableEstudiante
                                    v-model="item.usu_id"
                                    :estudiantes="estudiantesDisponibles(item.usu_id)"
                                    :todos-estudiantes="estudiantes"
                                    :is-lider="idx === 0"
                                    :disabled="!form.equ_id_sec"
                                    :placeholder="!form.equ_id_sec ? 'Seleccione primero una sección...' : idx === 0 ? 'Buscar Líder del equipo...' : 'Buscar estudiante...'"
                                />
                            </div>
                            <div class="col-3 text-center">
                                <span v-if="idx === 0" class="badge bg-danger py-2 px-2 w-100">
                                    <i class="bi bi-star-fill text-warning me-1"></i>Líder
                                </span>
                                <span v-else class="badge bg-secondary py-2 px-2 w-100">
                                    Integrante
                                </span>
                            </div>
                            <div class="col-1 text-center">
                                <button
                                    v-if="idx > 0"
                                    type="button"
                                    class="btn btn-sm btn-outline-danger p-1 w-100"
                                    @click="quitarIntegrante(idx)"
                                    title="Quitar"
                                >
                                    <i class="bi bi-trash"></i>
                                </button>
                                <span v-else class="text-muted" title="Líder obligatorio">
                                    <i class="bi bi-lock-fill"></i>
                                </span>
                            </div>
                        </div>

                        <div
                            v-if="integrantesAgregados > 0 && integrantesAgregados < 3"
                            class="alert alert-warning py-2 small mt-3 mb-0"
                        >
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            El equipo tiene <strong>{{ integrantesAgregados }}</strong> integrante(s) (mínimo recomendado: 3). Indique justificación de cupo:
                            <textarea
                                v-model="form.justificacion_integrantes"
                                class="form-control form-control-sm mt-2"
                                rows="2"
                                placeholder="Motivo por el cual inicia con menos de 3 integrantes..."
                            ></textarea>
                            <div v-if="errores.justificacion_integrantes" class="text-danger small mt-1">
                                {{ errores.justificacion_integrantes }}
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger fw-bold rounded-pill px-4" :disabled="loading">
                            <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                            <i v-else class="bi bi-check2-circle me-1"></i>
                            Guardar Equipo
                        </button>
                    </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { Modal } from "bootstrap";
import SelectBuscableEstudiante from "@/Components/SelectBuscableEstudiante.vue";
import SelectBuscable from "@/Components/SelectBuscable.vue";

const props = defineProps({
    secciones: { type: Array, default: () => [] },
    equipos: { type: Array, default: () => [] },
    estudiantes: { type: Array, default: () => [] },
    sede: { type: String, default: "ACA" },
    seccionInicial: { type: [String, Number], default: "" },
});

const opcionesSeccionesEquipo = computed(() => [
    { value: "", label: "Seleccionar sección..." },
    ...(props.secciones || []).map((sec) => ({
        value: sec.sec_id,
        label: `${sec.sec_codigo} — ${trayectoTextoSeccion(sec)} (${sec.turno?.tur_nombre || "—"})`,
    })),
]);

const emit = defineEmits(["creado"]);

const loading = ref(false);
const actualizandoTotal = ref(false);
const desbloquearTotal = ref(false);
const errores = ref({});
let modalInst = null;

const form = ref({
    equ_id_sec: "",
    equ_numero_grupo: 1,
    equ_semestre: 1,
    equ_total_seccion: 7,
    equ_titulo: "",
    integrantes: [{ usu_id: "", es_lider: true }],
    justificacion_integrantes: "",
});

const ROMANOS = { 1: "I", 2: "II", 3: "III", 4: "IV" };

const trayectoNumeroSeccion = (sec) => {
    if (!sec) return 1;
    const m = String(sec.sec_codigo || "").match(/^([1-8])/);
    if (m) return Math.ceil(parseInt(m[1]) / 2);
    return sec.trayecto?.tra_numero ?? 1;
};

const trayectoTextoSeccion = (sec) => {
    const num = trayectoNumeroSeccion(sec);
    return ROMANOS[num] ? `Trayecto ${ROMANOS[num]}` : sec.trayecto?.tra_nombre || `Trayecto ${num}`;
};

const seccionSeleccionada = computed(() =>
    (props.secciones || []).find((s) => String(s.sec_id) === String(form.value.equ_id_sec)),
);

const trayectoSeleccionado = computed(() =>
    seccionSeleccionada.value ? trayectoTextoSeccion(seccionSeleccionada.value) : "",
);

const totalFijadoSeccion = computed(() => {
    if (!form.value.equ_id_sec) return null;
    const equiposSec = (props.equipos || []).filter(
        (e) => e.equ_status !== false && String(e.equ_id_sec) === String(form.value.equ_id_sec),
    );
    if (!equiposSec.length) return null;
    let total = null;
    equiposSec.forEach((e) => {
        const m = String(e.equ_codigo || "").match(/\/(\d+)$/);
        if (m) {
            total = parseInt(m[1]);
        }
    });
    return total;
});

const codigoPreview = computed(() => {
    const sec = seccionSeleccionada.value;
    if (!sec || !form.value.equ_numero_grupo) return "—";
    const pnf = (sec.trayecto?.pnf?.pnf_siglas || "PNFI").toUpperCase();
    const sede = (props.sede || "ACA").toUpperCase();
    const tra = trayectoNumeroSeccion(sec);
    const sem = form.value.equ_semestre || 1;
    const num = form.value.equ_numero_grupo;
    const tot =
        totalFijadoSeccion.value !== null && !desbloquearTotal.value
            ? totalFijadoSeccion.value
            : form.value.equ_total_seccion || num;
    return `${pnf}-${sede}${tra}${sem}-${sec.sec_codigo}-${num}/${tot}`;
});

const integrantesAgregados = computed(
    () => form.value.integrantes.filter((i) => i.usu_id).length,
);

const onSeccionChange = () => {
    desbloquearTotal.value = false;
    const sec = seccionSeleccionada.value;
    if (!sec) return;

    const matchPrimerNum = String(sec.sec_codigo || "").match(/\d/);
    form.value.equ_semestre = matchPrimerNum
        ? parseInt(matchPrimerNum[0])
        : sec.sec_semestre || sec.trayecto?.tra_numero || 1;

    const equiposSec = (props.equipos || []).filter(
        (e) => e.equ_status !== false && String(e.equ_id_sec) === String(sec.sec_id),
    );
    let maxNum = 0;
    let totalSec = null;
    equiposSec.forEach((e) => {
        const m = String(e.equ_codigo || "").match(/-(\d+)\/(\d+)$/);
        if (m) {
            maxNum = Math.max(maxNum, parseInt(m[1]));
            totalSec = parseInt(m[2]) || totalSec;
        }
    });
    form.value.equ_numero_grupo = maxNum + 1;
    form.value.equ_total_seccion = totalSec !== null ? totalSec : Math.max(7, maxNum + 1);

    // Limpiar estudiantes que no sean de la sección
    form.value.integrantes.forEach((item) => {
        if (!item.usu_id) return;
        const est = (props.estudiantes || []).find(
            (e) => String(e.usu_id) === String(item.usu_id),
        );
        if (est && est.sec_id && String(est.sec_id) !== String(sec.sec_id)) {
            item.usu_id = "";
        }
    });
};

const estudiantesDisponibles = (idActual) => {
    const sel = form.value.integrantes
        .map((i) => i.usu_id)
        .filter((id) => id && id !== idActual);
    const secId = form.value.equ_id_sec;

    return (props.estudiantes || []).filter((e) => {
        if (sel.includes(e.usu_id)) return false;
        if (e.usu_id === idActual) return true;
        if (secId && e.sec_id && String(e.sec_id) !== String(secId)) return false;
        return true;
    });
};

const agregarIntegrante = () => {
    if (form.value.integrantes.length >= 6) return;
    form.value.integrantes.push({ usu_id: "", es_lider: false });
};

const quitarIntegrante = (idx) => {
    if (idx === 0) return;
    form.value.integrantes.splice(idx, 1);
};

const toggleDesbloquearTotal = () => {
    desbloquearTotal.value = !desbloquearTotal.value;
    if (!desbloquearTotal.value && totalFijadoSeccion.value !== null) {
        form.value.equ_total_seccion = totalFijadoSeccion.value;
    }
};

const aplicarSoloTotalSeccion = () => {
    if (!form.value.equ_id_sec || !form.value.equ_total_seccion) return;
    errores.value = {};
    actualizandoTotal.value = true;
    router.post(
        `/profesor/secciones/${form.value.equ_id_sec}/total-equipos`,
        { equ_total_seccion: form.value.equ_total_seccion },
        {
            preserveScroll: true,
            onError: (e) => {
                errores.value = e;
                actualizandoTotal.value = false;
            },
            onSuccess: () => {
                actualizandoTotal.value = false;
                desbloquearTotal.value = false;
            },
        },
    );
};

const abrir = () => {
    errores.value = {};
    desbloquearTotal.value = false;
    form.value = {
        equ_id_sec: props.seccionInicial || (props.secciones?.length === 1 ? props.secciones[0].sec_id : ""),
        equ_numero_grupo: 1,
        equ_semestre: 1,
        equ_total_seccion: 7,
        equ_titulo: "",
        integrantes: [{ usu_id: "", es_lider: true }],
        justificacion_integrantes: "",
    };
    if (form.value.equ_id_sec) {
        onSeccionChange();
    }
    const el = document.getElementById("mNuevoEquipo");
    if (el) {
        modalInst = Modal.getInstance(el) || new Modal(el);
        modalInst.show();
    }
};

const guardarEquipo = () => {
    errores.value = {};
    const validos = form.value.integrantes
        .filter((i) => i.usu_id)
        .map((i, idx) => ({ usu_id: i.usu_id, es_lider: idx === 0 }));

    if (!validos.length) {
        errores.value.integrantes = "Debe seleccionar al menos al Líder del equipo.";
        return;
    }

    loading.value = true;
    router.post(
        "/profesor/equipos",
        {
            ...form.value,
            modificar_total_seccion: desbloquearTotal.value,
            equ_codigo: codigoPreview.value !== "—" ? codigoPreview.value : "",
            integrantes: validos,
            proyecto: null,
        },
        {
            preserveScroll: true,
            onError: (e) => {
                errores.value = e;
                loading.value = false;
            },
            onSuccess: () => {
                loading.value = false;
                modalInst?.hide();
                emit("creado");
            },
        },
    );
};

defineExpose({ abrir });
</script>

<style scoped>
.mh {
    background: #1a1d23;
    border-bottom: 2px solid #dc3545;
    padding: 1rem 1.35rem;
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
.form-label {
    color: var(--text-main, #1a1a1a);
}
</style>
