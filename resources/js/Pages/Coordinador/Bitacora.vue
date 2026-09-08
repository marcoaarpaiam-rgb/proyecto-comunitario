<template>
    <AppLayout page-title="Bitácora">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Bitácora del Sistema</h4>
            <p class="text-muted small mb-0">
                Registro inmutable de todas las acciones realizadas
            </p>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Fecha y Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Entidad</th>
                            <th>Registro ID</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="registros.data.length === 0">
                            <td colspan="5" class="text-center text-muted py-4">
                                No hay registros en la bitácora
                            </td>
                        </tr>
                        <tr v-for="bit in registros.data" :key="bit.bit_id">
                            <td class="ps-4 small text-muted">
                                {{
                                    new Date(bit.bit_fecha_hora).toLocaleString(
                                        "es-VE",
                                    )
                                }}
                            </td>
                            <td class="small fw-semibold">
                                {{ bit.usuario?.usu_primer_nombre }}
                                {{ bit.usuario?.usu_primer_apellido }}
                            </td>
                            <td class="small">{{ bit.bit_accion }}</td>
                            <td>
                                <span
                                    v-if="bit.entidad"
                                    class="badge bg-primary bg-opacity-10 text-primary"
                                >
                                    {{ bit.entidad?.ent_nombre }}
                                </span>
                            </td>
                            <td class="small text-muted">
                                {{ bit.bit_registro_id || "—" }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- Paginación -->
            <div
                class="card-footer bg-white border-0 d-flex justify-content-between align-items-center py-3"
            >
                <small class="text-muted">
                    Mostrando {{ registros.from }} - {{ registros.to }} de
                    {{ registros.total }} registros
                </small>
                <div class="d-flex gap-2">
                    <a
                        v-if="registros.prev_page_url"
                        :href="registros.prev_page_url"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <a
                        v-if="registros.next_page_url"
                        :href="registros.next_page_url"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
defineProps({ registros: Object });
</script>
