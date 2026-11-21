<template>
    <div class="card border-0 shadow-sm mb-3">
        <div
            class="card-header d-flex align-items-center justify-content-between py-2 px-3 border-0 rounded-top"
            :class="bgHeaderClass"
        >
            <div class="d-flex align-items-center gap-2">
                <span class="fs-5">{{ iconoSemaforo }}</span>
                <span :class="`badge bg-${diagnostico.color || 'secondary'} text-white fw-bold`">
                    {{ etiquetaSemaforo }}
                </span>
                <small class="text-muted fw-semibold">Semana {{ diagnostico.semana_actual || 1 }}</small>
            </div>
            <button
                v-if="mostrarBotonDetalle"
                type="button"
                class="btn btn-sm btn-outline-dark bg-white shadow-xs rounded-pill px-2 py-0"
                style="font-size: 0.78rem"
                @click="$emit('ver-detalles', diagnostico.id_equipo)"
            >
                Detalles
            </button>
        </div>

        <div class="card-body p-3">
            <!-- Mensaje en lenguaje humano -->
            <p class="mb-2 small text-dark fw-medium lh-sm">
                {{ diagnostico.mensaje }}
            </p>

            <div class="row g-2 mt-1">
                <!-- Avance entregables -->
                <div class="col-6">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted" style="font-size: 0.75rem">Entregables</span>
                        <strong style="font-size: 0.78rem">{{ diagnostico.avance_entregables || 0 }}%</strong>
                    </div>
                    <div class="progress" style="height: 6px; background-color: #e9ecef">
                        <div
                            class="progress-bar"
                            :class="`bg-${diagnostico.color || 'secondary'}`"
                            role="progressbar"
                            :style="{ width: `${diagnostico.avance_entregables || 0}%` }"
                        ></div>
                    </div>
                </div>

                <!-- Asistencia -->
                <div class="col-6">
                    <div class="d-flex justify-content-between small mb-1">
                        <span class="text-muted" style="font-size: 0.75rem">Asistencia</span>
                        <strong style="font-size: 0.78rem">{{ diagnostico.asistencia_promedio || 0 }}%</strong>
                    </div>
                    <div class="progress" style="height: 6px; background-color: #e9ecef">
                        <div
                            class="progress-bar"
                            :class="(diagnostico.asistencia_promedio || 0) >= 75 ? 'bg-success' : 'bg-danger'"
                            role="progressbar"
                            :style="{ width: `${diagnostico.asistencia_promedio || 0}%` }"
                        ></div>
                    </div>
                </div>
            </div>

            <!-- Correcciones pendientes -->
            <div v-if="diagnostico.correcciones_pendientes > 0" class="mt-2 pt-1">
                <span class="badge bg-warning text-dark py-1 px-2 d-inline-flex align-items-center gap-1" style="font-size: 0.72rem">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    {{ diagnostico.correcciones_pendientes }} corrección(es) pendiente(s)
                </span>
            </div>

            <!-- Alerta de inviabilidad o riesgo crítico -->
            <div
                v-if="diagnostico.es_viable === false || diagnostico.semaforo === 'inviable'"
                class="mt-2 alert alert-dark py-1 px-2 mb-0 small d-flex align-items-center gap-1 border-0"
                style="font-size: 0.75rem"
            >
                <i class="bi bi-shield-slash-fill text-danger"></i>
                <span>Atención: Proyecto en ruta crítica con riesgo de aplazamiento.</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    diagnostico: {
        type: Object,
        required: true,
    },
    mostrarBotonDetalle: {
        type: Boolean,
        default: false,
    },
})

defineEmits(['ver-detalles'])

const iconoSemaforo = computed(() => {
    switch (props.diagnostico?.semaforo) {
        case 'en_ruta':
            return '🟢'
        case 'acelerar':
            return '🟡'
        case 'riesgo_alto':
            return '🔴'
        case 'inviable':
            return '⛔'
        default:
            return '⚪'
    }
})

const etiquetaSemaforo = computed(() => {
    switch (props.diagnostico?.semaforo) {
        case 'en_ruta':
            return 'En Ruta'
        case 'acelerar':
            return 'Acelerar'
        case 'riesgo_alto':
            return 'Riesgo Alto'
        case 'inviable':
            return 'Inviable'
        default:
            return 'En Diagnóstico'
    }
})

const bgHeaderClass = computed(() => {
    switch (props.diagnostico?.semaforo) {
        case 'en_ruta':
            return 'bg-success bg-opacity-10'
        case 'acelerar':
            return 'bg-warning bg-opacity-10'
        case 'riesgo_alto':
            return 'bg-danger bg-opacity-10'
        case 'inviable':
            return 'bg-dark bg-opacity-10'
        default:
            return 'bg-light'
    }
})
</script>
