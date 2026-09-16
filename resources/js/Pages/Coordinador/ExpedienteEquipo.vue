<template>
    <AppLayout :page-title="'Expediente — ' + equipo.equ_codigo">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    Expediente: {{ equipo.equ_codigo }}
                </h4>
                <p class="text-muted small mb-0">
                    {{ equipo.equ_titulo }} — Sección:
                    {{ equipo.seccion?.sec_codigo }}
                </p>
            </div>
            <a href="/coordinador/equipos" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>

        <!-- Pestañas (Tabs) de navegación -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body p-2">
                <div class="nav nav-pills gap-2">
                    <button
                        class="nav-link"
                        :class="{ active: tabActivo === 'general' }"
                        @click="tabActivo = 'general'"
                    >
                        General
                    </button>
                    <button
                        class="nav-link"
                        :class="{ active: tabActivo === 'seguimiento' }"
                        @click="tabActivo = 'seguimiento'"
                    >
                        Seguimiento
                    </button>
                    <button
                        class="nav-link"
                        :class="{ active: tabActivo === 'entregables' }"
                        @click="tabActivo = 'entregables'"
                    >
                        Entregables
                    </button>
                    <button
                        class="nav-link"
                        :class="{ active: tabActivo === 'socializaciones' }"
                        @click="tabActivo = 'socializaciones'"
                    >
                        Socializaciones
                    </button>
                    <button
                        class="nav-link"
                        :class="{ active: tabActivo === 'historial' }"
                        @click="tabActivo = 'historial'"
                    >
                        Historial
                    </button>
                    <button
                        class="nav-link"
                        :class="{ active: tabActivo === 'resultado' }"
                        @click="tabActivo = 'resultado'"
                    >
                        Resultado
                    </button>
                </div>
            </div>
        </div>

        <!-- Contenido de las pestañas -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <!-- TAB 1: GENERAL -->
                <div v-if="tabActivo === 'general'">
                    <h5 class="fw-bold mb-3">Información General del Equipo</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">
                                Código del Equipo
                            </p>
                            <p class="fw-semibold">{{ equipo.equ_codigo }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">
                                Título del Proyecto
                            </p>
                            <p class="fw-semibold">{{ equipo.equ_titulo }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">
                                Comunidad Asignada
                            </p>
                            <p class="fw-semibold">
                                {{
                                    equipo.proyecto_comunidad?.comunidad
                                        ?.com_nombre || "Sin comunidad asignada"
                                }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-muted small">Trayecto / PNF</p>
                            <p class="fw-semibold">
                                {{ equipo.trayecto?.tra_nombre || "N/D" }}
                            </p>
                        </div>
                    </div>

                    <h6 class="fw-bold mt-4 mb-3">Integrantes del Equipo</h6>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Integrante</th>
                                    <th>Cédula</th>
                                    <th>Rol</th>
                                    <th>Acceso</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="int in equipo.integrantes"
                                    :key="int.ein_id"
                                >
                                    <td class="ps-3 fw-semibold">
                                        {{ int.usuario?.usu_primer_nombre }}
                                        {{ int.usuario?.usu_primer_apellido }}
                                    </td>
                                    <td class="small text-muted">
                                        {{ int.usuario?.usu_cedula }}
                                    </td>
                                    <td>
                                        <span
                                            v-if="int.ein_es_lider"
                                            class="badge bg-danger"
                                            >Líder</span
                                        >
                                        <span v-else class="badge bg-secondary"
                                            >Integrante</span
                                        >
                                    </td>
                                    <td>
                                        <span
                                            v-if="int.usuario?.usu_tiene_acceso"
                                            class="badge bg-success"
                                            >Con acceso</span
                                        >
                                        <span
                                            v-else
                                            class="badge bg-light text-muted border"
                                            >Sin acceso</span
                                        >
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- TAB 2: SEGUIMIENTO -->
                <div v-else-if="tabActivo === 'seguimiento'">
                    <h5 class="fw-bold mb-3">Seguimiento y Bitácoras</h5>
                    <p class="text-muted">
                        Aquí se visualizan los registros de seguimiento y
                        avances del equipo.
                    </p>
                </div>

                <!-- TAB 3: ENTREGABLES -->
                <div v-else-if="tabActivo === 'entregables'">
                    <h5 class="fw-bold mb-3">Entregables del Proyecto</h5>
                    <p class="text-muted">
                        Listado de archivos y fases entregadas por el equipo.
                    </p>
                </div>

                <!-- TAB 4: SOCIALIZACIONES -->
                <div v-else-if="tabActivo === 'socializaciones'">
                    <h5 class="fw-bold mb-3">Socializaciones</h5>
                    <p class="text-muted">
                        Detalles de las evaluaciones y presentaciones de
                        socialización.
                    </p>
                </div>

                <!-- TAB 5: HISTORIAL -->
                <div v-else-if="tabActivo === 'historial'">
                    <h5 class="fw-bold mb-3">Historial de Cambios</h5>
                    <p class="text-muted">
                        Registro de modificaciones y estatus históricos del
                        equipo.
                    </p>
                </div>

                <!-- TAB 6: RESULTADO -->
                <div v-else-if="tabActivo === 'resultado'">
                    <h5 class="fw-bold mb-3">Resultado Final del Proyecto</h5>
                    <div v-if="equipo.resultado_proyecto">
                        <span
                            class="badge mb-2"
                            :class="
                                badgeColor(
                                    equipo.resultado_proyecto?.estado_proyecto
                                        ?.epr_nombre,
                                )
                            "
                        >
                            {{
                                equipo.resultado_proyecto?.estado_proyecto
                                    ?.epr_nombre
                            }}
                        </span>
                        <p class="text-muted mt-2">
                            Observaciones:
                            {{
                                equipo.resultado_proyecto?.erp_observacion ||
                                "Sin observaciones"
                            }}
                        </p>
                    </div>
                    <p v-else class="text-muted">
                        El equipo aún no cuenta con un resultado calificado.
                    </p>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";

defineProps({ equipo: Object });

const tabActivo = ref("general");

const badgeColor = (nombre) => {
    if (nombre === "Aprobado") return "bg-success";
    if (nombre === "Aplazado") return "bg-warning text-dark";
    if (nombre === "No Presentado") return "bg-danger";
    return "bg-secondary";
};
</script>
