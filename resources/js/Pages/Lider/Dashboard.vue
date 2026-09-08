<template>
    <AppLayout page-title="Dashboard">
        <!-- Bienvenida -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    Bienvenido, {{ $page.props.auth.user.usu_primer_nombre }} 👋
                </h4>
                <p class="text-muted mb-0 small">
                    Líder de Equipo — Panel de control de tu proyecto
                </p>
            </div>
            <div class="text-muted small">{{ fechaHoy }}</div>
        </div>

        <!-- Info del equipo -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center gap-3">
                            <div
                                class="icon-box bg-primary bg-opacity-10 text-primary"
                            >
                                <i class="bi bi-people-fill fs-4"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">
                                    {{
                                        equipo?.equ_titulo ||
                                        "Sin proyecto asignado"
                                    }}
                                </h5>
                                <div class="text-muted small">
                                    Código:
                                    <strong>{{
                                        equipo?.equ_codigo || "—"
                                    }}</strong>
                                    · Sección:
                                    <strong>{{
                                        equipo?.seccion?.sec_codigo || "—"
                                    }}</strong>
                                    · Trayecto:
                                    <strong>{{
                                        equipo?.trayecto?.tra_nombre || "—"
                                    }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <span class="badge bg-success fs-6 px-3 py-2"
                            >Equipo Activo</span
                        >
                    </div>
                </div>
            </div>
        </div>

        <!-- Tarjetas resumen -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-3 text-primary">
                        {{ stats.integrantes }}
                    </div>
                    <div class="text-muted small">Integrantes</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-3 text-success">
                        {{ stats.puntosCompletados }}
                    </div>
                    <div class="text-muted small">Puntos completados</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-3 text-warning">
                        {{ stats.entregablesSubidos }}
                    </div>
                    <div class="text-muted small">Entregables subidos</div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm text-center py-3">
                    <div class="fw-bold fs-3 text-danger">
                        {{ stats.pendientes }}
                    </div>
                    <div class="text-muted small">Pendientes</div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Progreso general -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-graph-up text-primary me-2"></i
                            >Progreso del Proyecto
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <div
                                class="d-flex justify-content-between small mb-1"
                            >
                                <span>Puntos de control</span>
                                <span class="fw-semibold"
                                    >{{ progreso.puntos }}%</span
                                >
                            </div>
                            <div class="progress" style="height: 10px">
                                <div
                                    class="progress-bar bg-primary"
                                    :style="{ width: progreso.puntos + '%' }"
                                ></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div
                                class="d-flex justify-content-between small mb-1"
                            >
                                <span>Entregables</span>
                                <span class="fw-semibold"
                                    >{{ progreso.entregables }}%</span
                                >
                            </div>
                            <div class="progress" style="height: 10px">
                                <div
                                    class="progress-bar bg-success"
                                    :style="{
                                        width: progreso.entregables + '%',
                                    }"
                                ></div>
                            </div>
                        </div>
                        <div>
                            <div
                                class="d-flex justify-content-between small mb-1"
                            >
                                <span>Asistencia</span>
                                <span class="fw-semibold"
                                    >{{ progreso.asistencia }}%</span
                                >
                            </div>
                            <div class="progress" style="height: 10px">
                                <div
                                    class="progress-bar bg-warning"
                                    :style="{
                                        width: progreso.asistencia + '%',
                                    }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Accesos rápidos -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-bold mb-0">
                            <i
                                class="bi bi-lightning-fill text-warning me-2"
                            ></i
                            >Accesos Rápidos
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-6">
                                <a
                                    href="/lider/entregables"
                                    class="btn btn-outline-primary w-100 py-3 d-flex flex-column align-items-center gap-1"
                                >
                                    <i class="bi bi-cloud-upload-fill fs-4"></i>
                                    <span class="small fw-semibold"
                                        >Subir Entregable</span
                                    >
                                </a>
                            </div>
                            <div class="col-6">
                                <a
                                    href="/lider/carta"
                                    class="btn btn-outline-danger w-100 py-3 d-flex flex-column align-items-center gap-1"
                                >
                                    <i
                                        class="bi bi-file-earmark-text-fill fs-4"
                                    ></i>
                                    <span class="small fw-semibold"
                                        >Carta de Presentación</span
                                    >
                                </a>
                            </div>
                            <div class="col-6">
                                <a
                                    href="/lider/proyecto"
                                    class="btn btn-outline-success w-100 py-3 d-flex flex-column align-items-center gap-1"
                                >
                                    <i class="bi bi-folder-fill fs-4"></i>
                                    <span class="small fw-semibold"
                                        >Ver Proyecto</span
                                    >
                                </a>
                            </div>
                            <div class="col-6">
                                <a
                                    href="/lider/equipo"
                                    class="btn btn-outline-secondary w-100 py-3 d-flex flex-column align-items-center gap-1"
                                >
                                    <i class="bi bi-people-fill fs-4"></i>
                                    <span class="small fw-semibold"
                                        >Mi Equipo</span
                                    >
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    equipo: { type: Object, default: null },
    stats: {
        type: Object,
        default: () => ({
            integrantes: 0,
            puntosCompletados: 0,
            entregablesSubidos: 0,
            pendientes: 0,
        }),
    },
    progreso: {
        type: Object,
        default: () => ({ puntos: 0, entregables: 0, asistencia: 0 }),
    },
});

const fechaHoy = computed(() => {
    return new Date().toLocaleDateString("es-VE", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    });
});
</script>

<style scoped>
.icon-box {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
</style>
