<template>
    <div class="modal fade" id="mTrasladoEstudiante" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="mh" style="background: #1a1d23; border-bottom: 2px solid #ffc107; color: #fff; padding: 1rem 1.3rem; display: flex; justify-content: space-between; align-items: center;">
                    <h5 class="mb-0 fw-bold text-white">
                        <i class="bi bi-arrow-left-right text-warning me-2"></i>Trasladar Integrante
                    </h5>
                    <button type="button" class="btn btn-sm text-white p-0 fs-5" data-bs-dismiss="modal">
                        <i class="bi bi-x"></i>
                    </button>
                </div>

                <form @submit.prevent="confirmarTraslado">
                    <div class="modal-body p-4">
                        <div
                            class="alert py-2 small mb-3"
                            style="background: rgba(13, 110, 253, 0.08); border: 1px solid rgba(13, 110, 253, 0.2); color: var(--text-main);"
                        >
                            <i class="bi bi-info-circle-fill me-1 text-primary"></i>
                            Vas a trasladar a
                            <strong>
                                {{ integrante?.usuario?.usu_primer_nombre }}
                                {{ integrante?.usuario?.usu_primer_apellido }}
                            </strong>
                            (CI: {{ integrante?.usuario?.usu_cedula }}) desde
                            <strong>{{ equipoOrigen?.equ_codigo }}</strong> hacia otro equipo de la
                            <strong>misma sección académica</strong>.
                        </div>

                        <div v-if="errorMsg" class="alert alert-danger py-2 small mb-3">
                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                            {{ errorMsg }}
                        </div>

                        <!-- Si el estudiante trasladado era Líder, exigir nuevo líder en origen -->
                        <div v-if="integrante?.ein_es_lider" class="alert alert-warning border-warning p-3 mb-3">
                            <div class="fw-bold text-dark small mb-2">
                                <i class="bi bi-star-fill text-danger me-1"></i>
                                Este estudiante es el Líder actual del equipo origen
                            </div>
                            <p class="small text-muted mb-2">
                                Antes de completar el traslado, debes designar al nuevo Líder que asumirá la responsabilidad en <strong>{{ equipoOrigen?.equ_codigo }}</strong>:
                            </p>
                            <SelectBuscable
                                v-if="otrosIntegrantesOrigen.length > 0"
                                v-model="nuevoLiderOrigenId"
                                :options="opcionesOtrosIntegrantes"
                                size="sm"
                                placeholder="Seleccionar nuevo líder del equipo origen..."
                            />
                            <div v-else class="text-danger small fw-bold">
                                No hay otros integrantes activos en este equipo para asumir el liderazgo.
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label fw-semibold small">
                                Equipo de destino (misma sección) <span class="text-danger">*</span>
                            </label>
                            <SelectBuscable
                                v-model="equipoDestinoId"
                                :options="opcionesEquiposDestino"
                                placeholder="Seleccionar equipo de destino..."
                            />
                            <small v-if="equiposDestinoDisponibles.length === 0" class="text-danger d-block mt-1">
                                No hay otros equipos activos disponibles en esta sección académica.
                            </small>
                        </div>
                    </div>

                    <div class="modal-footer border-0">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            class="btn btn-warning text-dark fw-bold px-4"
                            :disabled="!puedeConfirmar || cargando"
                        >
                            <span v-if="cargando" class="spinner-border spinner-border-sm me-1"></span>
                            Confirmar Traslado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { Modal } from "bootstrap";
import SelectBuscable from "@/Components/SelectBuscable.vue";

const props = defineProps({
    equipos: { type: Array, default: () => [] },
});

const emit = defineEmits(["trasladado"]);

const equipoOrigen = ref(null);
const integrante = ref(null);
const equipoDestinoId = ref("");
const nuevoLiderOrigenId = ref("");
const cargando = ref(false);
const errorMsg = ref("");
let modalInst = null;

const otrosIntegrantesOrigen = computed(() => {
    if (!equipoOrigen.value || !integrante.value) return [];
    return (equipoOrigen.value.integrantes || []).filter(
        (i) => i.ein_status !== false && i.ein_id !== integrante.value.ein_id,
    );
});

const equiposDestinoDisponibles = computed(() => {
    if (!equipoOrigen.value) return [];
    return (props.equipos || []).filter(
        (e) =>
            e.equ_id !== equipoOrigen.value.equ_id &&
            e.equ_status &&
            String(e.equ_id_sec) === String(equipoOrigen.value.equ_id_sec),
    );
});

const opcionesOtrosIntegrantes = computed(() => [
    { value: "", label: "Seleccionar nuevo líder del equipo origen..." },
    ...otrosIntegrantesOrigen.value.map((cand) => ({
        value: cand.ein_id,
        label: `${cand.usuario?.usu_primer_nombre || ""} ${cand.usuario?.usu_primer_apellido || ""}`.trim(),
        sublabel: `CI: ${cand.usuario?.usu_cedula || ""}`,
    })),
]);

const opcionesEquiposDestino = computed(() => [
    { value: "", label: "Seleccionar equipo de destino..." },
    ...equiposDestinoDisponibles.value.map((eq) => ({
        value: eq.equ_id,
        label: `${eq.equ_codigo} — ${eq.equ_titulo || ""}`,
        sublabel: `${(eq.integrantes || []).filter((i) => i.ein_status !== false).length}/6 integrantes`,
    })),
]);

const puedeConfirmar = computed(() => {
    if (!equipoDestinoId.value) return false;
    if (integrante.value?.ein_es_lider) {
        return Boolean(nuevoLiderOrigenId.value) && otrosIntegrantesOrigen.value.length > 0;
    }
    return true;
});

const abrir = (equ, integ) => {
    equipoOrigen.value = equ;
    integrante.value = integ;
    equipoDestinoId.value = "";
    nuevoLiderOrigenId.value = "";
    errorMsg.value = "";
    const el = document.getElementById("mTrasladoEstudiante");
    if (el) {
        modalInst = Modal.getInstance(el) || new Modal(el);
        modalInst.show();
    }
};

const confirmarTraslado = () => {
    if (!puedeConfirmar.value) return;
    cargando.value = true;
    errorMsg.value = "";

    router.post(
        `/profesor/equipos/${equipoOrigen.value.equ_id}/integrantes/${integrante.value.ein_id}/trasladar`,
        {
            nuevo_equipo_id: equipoDestinoId.value,
            nuevo_lider_origen_id: integrante.value.ein_es_lider ? nuevoLiderOrigenId.value : null,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                cargando.value = false;
                modalInst?.hide();
                emit("trasladado");
            },
            onError: (err) => {
                cargando.value = false;
                errorMsg.value = Object.values(err)[0] || "Error al trasladar integrante.";
            },
        },
    );
};

defineExpose({ abrir });
</script>
