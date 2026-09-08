<template>
    <AppLayout page-title="Reportes">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Reportes del Sistema</h4>
            <p class="text-muted small mb-0">
                Estadísticas y solvencia estudiantil
            </p>
        </div>

        <!-- Resumen General -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-2-4">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-2 text-primary">
                        {{ resumenGeneral.total_equipos }}
                    </div>
                    <div class="text-muted small">Total Equipos</div>
                </div>
            </div>
            <div class="col-6 col-md-2-4">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-2 text-success">
                        {{ resumenGeneral.aprobados }}
                    </div>
                    <div class="text-muted small">Aprobados</div>
                </div>
            </div>
            <div class="col-6 col-md-2-4">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-2 text-warning">
                        {{ resumenGeneral.aplazados }}
                    </div>
                    <div class="text-muted small">Aplazados</div>
                </div>
            </div>
            <div class="col-6 col-md-2-4">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-2 text-danger">
                        {{ resumenGeneral.no_presentados }}
                    </div>
                    <div class="text-muted small">No Presentados</div>
                </div>
            </div>
            <div class="col-6 col-md-2-4">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-2 text-secondary">
                        {{ resumenGeneral.sin_resultado }}
                    </div>
                    <div class="text-muted small">Sin Resultado</div>
                </div>
            </div>
        </div>
        <!-- Reporte por Sección -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-collection-fill text-primary me-2"></i>
                    Equipos por Sección
                </h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Sección</th>
                            <th>Trayecto</th>
                            <th>Turno</th>
                            <th class="text-center">Total</th>
                            <th class="text-center">Aprobados</th>
                            <th class="text-center">Aplazados</th>
                            <th class="text-center">Sin Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="reportePorSeccion.length === 0">
                            <td colspan="7" class="text-center text-muted py-4">
                                No hay datos
                            </td>
                        </tr>
                        <tr v-for="rep in reportePorSeccion" :key="rep.seccion">
                            <td class="ps-4 fw-bold">{{ rep.seccion }}</td>
                            <td class="small">{{ rep.trayecto }}</td>
                            <td class="small text-muted">{{ rep.turno }}</td>
                            <td class="text-center fw-bold">{{ rep.total }}</td>
                            <td class="text-center">
                                <span class="badge bg-success">{{
                                    rep.aprobados
                                }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning text-dark">{{
                                    rep.aplazados
                                }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{
                                    rep.sin_resultado
                                }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Reporte de Solvencia -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-person-check-fill text-danger me-2"></i>
                    Reporte de Solvencia por Sección
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold"
                            >Seleccionar Sección</label
                        >
                        <select
                            v-model="seccionSeleccionada"
                            class="form-select"
                        >
                            <option value="">Seleccionar...</option>
                            <option
                                v-for="sec in secciones"
                                :key="sec.sec_id"
                                :value="sec.sec_id"
                            >
                                {{ sec.sec_codigo }} —
                                {{ sec.trayecto?.tra_nombre }} ({{
                                    sec.turno?.tur_nombre
                                }})
                            </option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button
                            class="btn btn-danger w-100 fw-semibold"
                            :disabled="!seccionSeleccionada"
                            @click="generarSolvencia"
                        >
                            <i class="bi bi-bar-chart-fill me-2"></i>Generar
                            Reporte
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    secciones: Array,
    trayectos: Array,
    resumenGeneral: Object,
    reportePorSeccion: Array,
});

const seccionSeleccionada = ref("");

const generarSolvencia = () => {
    if (seccionSeleccionada.value) {
        router.get("/coordinador/reportes/solvencia", {
            sec_id: seccionSeleccionada.value,
        });
    }
};
</script>
