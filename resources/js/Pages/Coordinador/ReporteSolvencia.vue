<template>
    <AppLayout page-title="Reporte de Solvencia">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Reporte de Solvencia</h4>
                <p class="text-muted small mb-0">
                    Sección {{ seccion.sec_codigo }} —
                    {{ seccion.trayecto?.tra_nombre }} —
                    {{ seccion.turno?.tur_nombre }}
                </p>
            </div>
            <a href="/coordinador/reportes" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>

        <div
            v-for="equipo in reporte"
            :key="equipo.equipo"
            class="card border-0 shadow-sm mb-4"
        >
            <div class="card-header bg-white border-0 pt-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="fw-bold mb-0 text-danger">
                            {{ equipo.equipo }}
                        </h6>
                        <small class="text-muted">{{ equipo.titulo }}</small>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted">
                            Comunidad: {{ equipo.comunidad || "—" }}
                        </div>
                        <span
                            v-if="equipo.resultado"
                            class="badge"
                            :class="badgeColor(equipo.resultado)"
                        >
                            {{ equipo.resultado }}
                        </span>
                        <span v-else class="badge bg-secondary"
                            >Sin resultado</span
                        >
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Estudiante</th>
                            <th>Cédula</th>
                            <th>Rol</th>
                            <th>Asistencias</th>
                            <th>%</th>
                            <th>Solvente</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="int in equipo.integrantes" :key="int.cedula">
                            <td class="ps-4 fw-semibold">{{ int.nombre }}</td>
                            <td class="text-muted small">{{ int.cedula }}</td>
                            <td>
                                <span
                                    v-if="int.es_lider"
                                    class="badge bg-danger"
                                    >Líder</span
                                >
                                <span v-else class="badge bg-secondary"
                                    >Integrante</span
                                >
                            </td>
                            <td class="small">
                                {{ int.asistencias }} / {{ int.total }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        class="progress flex-grow-1"
                                        style="height: 8px"
                                    >
                                        <div
                                            class="progress-bar"
                                            :class="
                                                int.porcentaje >= 75
                                                    ? 'bg-success'
                                                    : 'bg-danger'
                                            "
                                            :style="{
                                                width: int.porcentaje + '%',
                                            }"
                                        ></div>
                                    </div>
                                    <span class="small fw-semibold"
                                        >{{ int.porcentaje }}%</span
                                    >
                                </div>
                            </td>
                            <td>
                                <span
                                    class="badge"
                                    :class="
                                        int.solvente
                                            ? 'bg-success'
                                            : 'bg-danger'
                                    "
                                >
                                    {{
                                        int.solvente
                                            ? "Solvente"
                                            : "No Solvente"
                                    }}
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div v-if="reporte.length === 0" class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                No hay equipos en esta sección
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";

defineProps({ seccion: Object, reporte: Array });

const badgeColor = (nombre) => {
    if (nombre === "Aprobado") return "bg-success";
    if (nombre === "Aplazado") return "bg-warning text-dark";
    if (nombre === "No Presentado") return "bg-danger";
    return "bg-secondary";
};
</script>
