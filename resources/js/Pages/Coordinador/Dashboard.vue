<template>
    <AppLayout page-title="Dashboard" :notificaciones="3">
        <!-- Bienvenida -->
        <div class="d-flex align-items-center justify-content-between mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    Bienvenido, {{ $page.props.auth.user.usu_primer_nombre }} 👋
                </h4>
                <p class="text-muted mb-0 small">
                    Coordinación de Informática — Resumen general del sistema
                </p>
            </div>
            <div class="text-muted small">{{ fechaHoy }}</div>
        </div>

        <!-- Tarjetas de resumen -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div
                            class="icon-box bg-primary bg-opacity-10 text-primary"
                        >
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4">{{ stats.equipos }}</div>
                            <div class="text-muted small">Equipos activos</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div
                            class="icon-box bg-success bg-opacity-10 text-success"
                        >
                            <i class="bi bi-folder-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4">
                                {{ stats.proyectos }}
                            </div>
                            <div class="text-muted small">
                                Proyectos registrados
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div
                            class="icon-box bg-warning bg-opacity-10 text-warning"
                        >
                            <i class="bi bi-flag-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4">
                                {{ stats.puntosControl }}
                            </div>
                            <div class="text-muted small">
                                Puntos de control
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div
                            class="icon-box bg-danger bg-opacity-10 text-danger"
                        >
                            <i class="bi bi-building fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4">
                                {{ stats.comunidades }}
                            </div>
                            <div class="text-muted small">Comunidades</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Equipos por trayecto -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-bold mb-0">
                            <i
                                class="bi bi-bar-chart-fill text-primary me-2"
                            ></i
                            >Equipos por Trayecto
                        </h6>
                    </div>
                    <div class="card-body">
                        <div
                            v-for="t in equiposPorTrayecto"
                            :key="t.nombre"
                            class="mb-3"
                        >
                            <div
                                class="d-flex justify-content-between small mb-1"
                            >
                                <span>{{ t.nombre }}</span>
                                <span class="fw-semibold">{{
                                    t.cantidad
                                }}</span>
                            </div>
                            <div class="progress" style="height: 8px">
                                <div
                                    class="progress-bar bg-primary"
                                    :style="{ width: t.porcentaje + '%' }"
                                ></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actividad reciente -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-clock-history text-danger me-2"></i
                            >Actividad Reciente
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <ul class="list-group list-group-flush">
                            <li
                                v-for="act in actividades"
                                :key="act.id"
                                class="list-group-item d-flex align-items-center gap-3 px-3 py-2"
                            >
                                <div class="icon-box-sm" :class="act.color">
                                    <i
                                        :class="act.icon"
                                        style="font-size: 0.85rem"
                                    ></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="small fw-semibold">
                                        {{ act.texto }}
                                    </div>
                                    <div
                                        class="text-muted"
                                        style="font-size: 0.75rem"
                                    >
                                        {{ act.tiempo }}
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Proyectos por estado -->
            <div class="col-md-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-award-fill text-warning me-2"></i
                            >Resumen de Resultados
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 text-center">
                            <div
                                class="col-6 col-md-3"
                                v-for="est in estadosProyecto"
                                :key="est.nombre"
                            >
                                <div class="p-3 rounded-3" :class="est.bg">
                                    <div
                                        class="fw-bold fs-3"
                                        :class="est.color"
                                    >
                                        {{ est.cantidad }}
                                    </div>
                                    <div class="small" :class="est.color">
                                        {{ est.nombre }}
                                    </div>
                                </div>
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
    stats: {
        type: Object,
        default: () => ({
            equipos: 0,
            proyectos: 0,
            puntosControl: 0,
            comunidades: 0,
        }),
    },
    equiposPorTrayecto: {
        type: Array,
        default: () => [
            { nombre: "Trayecto I", cantidad: 0, porcentaje: 0 },
            { nombre: "Trayecto II", cantidad: 0, porcentaje: 0 },
            { nombre: "Trayecto III", cantidad: 0, porcentaje: 0 },
            { nombre: "Trayecto IV", cantidad: 0, porcentaje: 0 },
        ],
    },
    actividades: {
        type: Array,
        default: () => [],
    },
    estadosProyecto: {
        type: Array,
        default: () => [
            {
                nombre: "Aprobados",
                cantidad: 0,
                bg: "bg-success bg-opacity-10",
                color: "text-success",
            },
            {
                nombre: "Aplazados",
                cantidad: 0,
                bg: "bg-warning bg-opacity-10",
                color: "text-warning",
            },
            {
                nombre: "No Presentados",
                cantidad: 0,
                bg: "bg-danger bg-opacity-10",
                color: "text-danger",
            },
            {
                nombre: "En Progreso",
                cantidad: 0,
                bg: "bg-primary bg-opacity-10",
                color: "text-primary",
            },
        ],
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
.icon-box-sm {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
</style>
