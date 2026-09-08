<template>
    <AppLayout :page-title="'Sección ' + seccion.sec_codigo">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Sección {{ seccion.sec_codigo }}</h4>
                <p class="text-muted small mb-0">
                    {{ seccion.trayecto?.tra_nombre }} —
                    {{ seccion.turno?.tur_nombre }}
                </p>
            </div>
            <a href="/coordinador/secciones" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver
            </a>
        </div>

        <div v-if="equipos.length === 0" class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                No hay equipos en esta sección
            </div>
        </div>

        <div
            v-for="equ in equipos"
            :key="equ.equ_id"
            class="card border-0 shadow-sm mb-4"
        >
            <div class="card-header bg-white border-0 pt-3">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="fw-bold text-danger mb-0">
                            {{ equ.equ_codigo }}
                        </h6>
                        <small class="text-muted">{{ equ.equ_titulo }}</small>
                    </div>
                    <div class="text-end">
                        <div class="small text-muted mb-1">
                            {{
                                equ.proyecto_comunidad?.comunidad?.com_nombre ||
                                "Sin comunidad"
                            }}
                        </div>
                        <span
                            v-if="equ.resultado_proyecto"
                            class="badge"
                            :class="
                                badgeColor(
                                    equ.resultado_proyecto?.estado_proyecto
                                        ?.epr_nombre,
                                )
                            "
                        >
                            {{
                                equ.resultado_proyecto?.estado_proyecto
                                    ?.epr_nombre
                            }}
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
                            <th class="ps-4">Integrante</th>
                            <th>Cédula</th>
                            <th>Rol</th>
                            <th>Acceso al Sistema</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="int in equ.integrantes" :key="int.ein_id">
                            <td class="ps-4 fw-semibold">
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
                                >
                                    <i class="bi bi-check me-1"></i>Con acceso
                                </span>
                                <span
                                    v-else
                                    class="badge bg-light text-muted border"
                                >
                                    Sin acceso
                                </span>
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

defineProps({ seccion: Object, equipos: Array });

const badgeColor = (nombre) => {
    if (nombre === "Aprobado") return "bg-success";
    if (nombre === "Aplazado") return "bg-warning text-dark";
    if (nombre === "No Presentado") return "bg-danger";
    return "bg-secondary";
};
</script>
