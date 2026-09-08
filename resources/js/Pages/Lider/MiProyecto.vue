<template>
    <AppLayout page-title="Mi Proyecto">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Mi Proyecto</h4>
            <p class="text-muted small mb-0">
                Información del proyecto comunitario
            </p>
        </div>

        <div v-if="!equipo?.proyecto_comunidad" class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                Tu equipo aún no tiene un proyecto asignado
            </div>
        </div>

        <div v-else class="row g-4">
            <div class="col-md-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h5 class="fw-bold text-danger mb-3">
                            {{ equipo.equ_titulo }}
                        </h5>
                        <table class="table table-borderless small">
                            <tr>
                                <td
                                    class="fw-semibold text-muted pe-3"
                                    style="width: 140px"
                                >
                                    Comunidad
                                </td>
                                <td>
                                    {{
                                        equipo.proyecto_comunidad.comunidad
                                            ?.com_nombre
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Tipo</td>
                                <td>
                                    {{
                                        equipo.proyecto_comunidad.tipo_proyecto
                                            ?.tpr_nombre
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">
                                    Modalidad
                                </td>
                                <td>
                                    {{
                                        equipo.proyecto_comunidad.modalidad
                                            ?.mpr_nombre
                                    }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">Resumen</td>
                                <td>
                                    {{ equipo.proyecto_comunidad.pco_resumen }}
                                </td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">
                                    Fecha Límite
                                </td>
                                <td>
                                    {{
                                        equipo.proyecto_comunidad
                                            .pco_fecha_limite
                                    }}
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body text-center py-4">
                        <div class="mb-3">
                            <i class="bi bi-building text-danger fs-1"></i>
                        </div>
                        <h6 class="fw-bold">
                            {{
                                equipo.proyecto_comunidad.comunidad?.com_nombre
                            }}
                        </h6>
                        <p class="text-muted small">
                            {{
                                equipo.proyecto_comunidad.comunidad
                                    ?.com_ubicacion
                            }}
                        </p>
                        <div class="mt-3">
                            <span
                                v-if="
                                    equipo.proyecto_comunidad
                                        .pco_fecha_aprobacion
                                "
                                class="badge bg-success fs-6 px-3 py-2"
                            >
                                <i class="bi bi-check-circle me-1"></i>Comunidad
                                Aprobada
                            </span>
                            <span
                                v-else
                                class="badge bg-warning text-dark fs-6 px-3 py-2"
                            >
                                <i class="bi bi-clock me-1"></i>Pendiente de
                                aprobación
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
defineProps({ equipo: Object });
</script>
