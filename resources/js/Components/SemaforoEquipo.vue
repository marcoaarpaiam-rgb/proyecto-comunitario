<template>
    <div class="card border-0 shadow-sm mb-3">
        <div
            class="card-header d-flex align-items-center justify-content-between py-2 px-3 border-0 rounded-top"
            :class="bgHeaderClass"
        >
            <div class="d-flex align-items-center gap-2">
                <span class="fs-5" :title="significadoSemaforo" style="cursor: help;">{{ iconoSemaforo }}</span>
                <span
                    :class="`badge bg-${diagnostico.color || 'secondary'} text-white fw-bold`"
                    :title="significadoSemaforo"
                    style="cursor: help;"
                >
                    {{ etiquetaSemaforo }}
                    <span v-if="diagnostico.porcentaje_situacion !== undefined" class="ms-1">({{ diagnostico.porcentaje_situacion }}%)</span>
                </span>
                <span class="badge bg-white text-dark border shadow-xs d-inline-flex align-items-center gap-1" style="font-size: 0.75rem;">
                    <i class="bi bi-calendar3 text-primary"></i>
                    <strong>Semana {{ diagnostico.semana_actual || 1 }}</strong> del Lapso
                </span>
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
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-uppercase fw-bold text-muted" style="font-size: 0.72rem; letter-spacing: 0.5px;">
                    Situación del Proyecto
                </span>
                <span v-if="diagnostico.porcentaje_situacion !== undefined" class="fw-bold small" :class="`text-${diagnostico.color || 'secondary'}`">
                    {{ diagnostico.porcentaje_situacion }}% cumplimiento
                </span>
            </div>
            <div v-if="diagnostico.porcentaje_situacion !== undefined" class="progress mb-2" style="height: 8px; background-color: #e9ecef">
                <div
                    class="progress-bar"
                    :class="`bg-${diagnostico.color || 'secondary'}`"
                    role="progressbar"
                    :style="{ width: `${diagnostico.porcentaje_situacion || 0}%` }"
                ></div>
            </div>
            <!-- Mensaje en lenguaje humano con significado al posar el mouse -->
            <p class="mb-2 small text-dark fw-medium lh-sm" :title="significadoSemaforo" style="cursor: help;">
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

            <!-- Alerta de no viabilidad o riesgo crítico -->
            <div
                v-if="diagnostico.es_viable === false || diagnostico.semaforo === 'inviable' || diagnostico.semaforo === 'no_viable'"
                class="mt-2 alert alert-dark py-1 px-2 mb-0 small d-flex align-items-center gap-1 border-0"
                style="font-size: 0.75rem"
                title="Cierre administrativo por falta de factibilidad."
            >
                <i class="bi bi-shield-slash-fill text-danger"></i>
                <span>Atención: Proyecto en cierre administrativo o no viable por falta de factibilidad.</span>
            </div>

            <!-- Leyenda Interactiva con tooltips al pasar el mouse -->
            <div class="mt-3 pt-2 border-top">
                <div class="d-flex flex-wrap gap-2 align-items-center" style="font-size: 0.72rem;">
                    <span class="text-muted fw-bold">Situación del Proyecto:</span>
                    <span
                        class="badge bg-light text-dark border px-2 py-1"
                        style="cursor: help;"
                        title="75% a 100%: Cumple con los estándares y metas."
                    >
                        🟢 Conforme (≥75%)
                    </span>
                    <span
                        class="badge bg-light text-dark border px-2 py-1"
                        style="cursor: help;"
                        title="50% a 74%: Tiene parámetros menores que deben mejorarse."
                    >
                        🟡 Observado (50–74%)
                    </span>
                    <span
                        class="badge bg-light text-dark border px-2 py-1"
                        style="cursor: help;"
                        title="25% a 49%: Incumplimiento grave de los parámetros del proyecto."
                    >
                        🔴 No conforme (25–49%)
                    </span>
                    <span
                        class="badge bg-light text-dark border px-2 py-1"
                        style="cursor: help;"
                        title="Menor a 25%: Cierre administrativo por falta de factibilidad."
                    >
                        ⚫ No viable (&lt;25%)
                    </span>
                </div>
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
        case 'conforme':
            return '🟢'
        case 'acelerar':
        case 'observado':
            return '🟡'
        case 'riesgo_alto':
        case 'no_conforme':
            return '🔴'
        case 'inviable':
        case 'no_viable':
            return '⚫'
        default:
            return '⚪'
    }
})

const etiquetaSemaforo = computed(() => {
    switch (props.diagnostico?.semaforo) {
        case 'en_ruta':
        case 'conforme':
            return 'Conforme'
        case 'acelerar':
        case 'observado':
            return 'Observado'
        case 'riesgo_alto':
        case 'no_conforme':
            return 'No conforme'
        case 'inviable':
        case 'no_viable':
            return 'No viable'
        default:
            return 'En Diagnóstico'
    }
})

const significadoSemaforo = computed(() => {
    switch (props.diagnostico?.semaforo) {
        case 'en_ruta':
        case 'conforme':
            return 'Cumple con los estándares y metas.'
        case 'acelerar':
        case 'observado':
            return 'Tiene parametros menores que deben mejorarse.'
        case 'riesgo_alto':
        case 'no_conforme':
            return 'Incumplimiento grave de los parámetros del proyecto.'
        case 'inviable':
        case 'no_viable':
            return 'Cierre administrativo por falta de factibilidad.'
        default:
            return 'Situación del Proyecto'
    }
})

const bgHeaderClass = computed(() => {
    switch (props.diagnostico?.semaforo) {
        case 'en_ruta':
        case 'conforme':
            return 'bg-success bg-opacity-10'
        case 'acelerar':
        case 'observado':
            return 'bg-warning bg-opacity-10'
        case 'riesgo_alto':
        case 'no_conforme':
            return 'bg-danger bg-opacity-10'
        case 'inviable':
        case 'no_viable':
            return 'bg-dark bg-opacity-10'
        default:
            return 'bg-light'
    }
})
</script>
