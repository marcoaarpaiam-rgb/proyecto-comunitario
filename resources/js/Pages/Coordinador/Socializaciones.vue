<template>
    <AppLayout page-title="Socializaciones">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Socializaciones</h4>
            <p class="text-muted small mb-0">
                Vista general de todas las socializaciones
            </p>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Equipo</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Lugar</th>
                            <th>Trayecto</th>
                            <th>Resultado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="socializaciones.length === 0">
                            <td colspan="6" class="text-center text-muted py-4">
                                No hay socializaciones registradas
                            </td>
                        </tr>
                        <tr v-for="soc in socializaciones" :key="soc.soc_id">
                            <td class="ps-4 fw-bold text-danger">
                                {{ soc.equipo?.equ_codigo }}
                            </td>
                            <td>
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
                            </td>
                            <td class="small">{{ soc.soc_fecha }}</td>
                            <td class="small text-muted">
                                {{ soc.soc_lugar }}
                            </td>
                            <td class="small">
                                {{ soc.trayecto?.tra_nombre }}
                            </td>
                            <td>
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
                                    >Pendiente</span
                                >
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
defineProps({ socializaciones: Array });
</script>
