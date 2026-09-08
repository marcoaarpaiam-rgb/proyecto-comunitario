<template>
    <AppLayout page-title="Dashboard" :notificaciones="notificacionesSinLeer">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">
                    Bienvenido, {{ $page.props.auth.user.usu_primer_nombre }} 👋
                </h4>
                <p class="text-muted mb-0 small">
                    Profesor de Proyecto — Resumen de tus secciones
                </p>
            </div>
            <div class="text-muted small">{{ fechaHoy }}</div>
        </div>

        <!-- Tarjetas resumen -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <div
                            class="icon-box bg-primary bg-opacity-10 text-primary"
                        >
                            <i class="bi bi-collection-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4">
                                {{ stats.secciones }}
                            </div>
                            <div class="text-muted small">Mis secciones</div>
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
                            <i class="bi bi-people-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4">{{ stats.equipos }}</div>
                            <div class="text-muted small">
                                Equipos asignados
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
                            <i class="bi bi-file-earmark-check-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4">
                                {{ stats.entregablesPendientes }}
                            </div>
                            <div class="text-muted small">
                                Entregables pendientes
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
                            <i class="bi bi-bell-fill fs-4"></i>
                        </div>
                        <div>
                            <div class="fw-bold fs-4">
                                {{ notificacionesSinLeer }}
                            </div>
                            <div class="text-muted small">Notificaciones</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Mis equipos con integrantes -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-people-fill text-danger me-2"></i
                            >Mis Equipos
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div
                            v-if="!equipos || equipos.length === 0"
                            class="text-center text-muted py-4 small"
                        >
                            No tienes equipos asignados
                        </div>
                        <div
                            v-for="equ in equipos"
                            :key="equ.equ_id"
                            class="border-bottom px-3 py-3"
                        >
                            <div
                                class="d-flex justify-content-between align-items-start mb-2"
                            >
                                <div>
                                    <span class="fw-bold text-danger">{{
                                        equ.equ_codigo
                                    }}</span>
                                    <span class="text-muted small ms-2">
                                        {{ equ.seccion?.sec_codigo }} —
                                        {{ equ.trayecto?.tra_nombre }}
                                    </span>
                                </div>
                                <span class="badge bg-success">Activo</span>
                            </div>
                            <div class="text-muted small mb-2">
                                {{ equ.equ_titulo }}
                            </div>
                            <div class="d-flex flex-wrap gap-1">
                                <span
                                    v-for="int in equ.integrantes"
                                    :key="int.ein_id"
                                    class="badge px-2 py-1"
                                    :class="
                                        int.ein_es_lider
                                            ? 'bg-danger'
                                            : 'bg-light text-dark border'
                                    "
                                >
                                    {{ int.usuario?.usu_primer_nombre }}
                                    {{ int.usuario?.usu_primer_apellido }}
                                    <span v-if="int.ein_es_lider" class="ms-1"
                                        >(Líder)</span
                                    >
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Próximos puntos y entregables -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-flag-fill text-warning me-2"></i>
                            Próximos Puntos de Control
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div
                            v-if="proximosPuntos.length === 0"
                            class="text-center text-muted py-3 small"
                        >
                            No hay puntos próximos
                        </div>
                        <ul v-else class="list-group list-group-flush">
                            <li
                                v-for="punto in proximosPuntos"
                                :key="punto.id"
                                class="list-group-item px-3 py-2"
                            >
                                <div
                                    class="d-flex align-items-center justify-content-between"
                                >
                                    <div>
                                        <div class="small fw-semibold">
                                            {{ punto.nombre }}
                                        </div>
                                        <div
                                            class="text-muted"
                                            style="font-size: 0.75rem"
                                        >
                                            {{ punto.equipo }}
                                        </div>
                                    </div>
                                    <span
                                        class="badge"
                                        :class="
                                            punto.urgente
                                                ? 'bg-danger'
                                                : 'bg-warning text-dark'
                                        "
                                    >
                                        {{ punto.fecha }}
                                    </span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-3 pb-0">
                        <h6 class="fw-bold mb-0">
                            <i
                                class="bi bi-file-earmark-check-fill text-success me-2"
                            ></i>
                            Entregables por Revisar
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div
                            v-if="entregablesPendientes.length === 0"
                            class="text-center text-muted py-3 small"
                        >
                            No hay entregables pendientes
                        </div>
                        <ul v-else class="list-group list-group-flush">
                            <li
                                v-for="ent in entregablesPendientes"
                                :key="ent.id"
                                class="list-group-item px-3 py-2 d-flex align-items-center justify-content-between"
                            >
                                <div>
                                    <div class="small fw-semibold">
                                        {{ ent.nombre }}
                                    </div>
                                    <div
                                        class="text-muted"
                                        style="font-size: 0.75rem"
                                    >
                                        {{ ent.equipo }}
                                    </div>
                                </div>
                                <a
                                    href="/profesor/entregables"
                                    class="badge bg-warning text-dark text-decoration-none"
                                >
                                    Revisar
                                </a>
                            </li>
                        </ul>
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
        default: () => ({ secciones: 0, equipos: 0, entregablesPendientes: 0 }),
    },
    notificacionesSinLeer: { type: Number, default: 0 },
    proximosPuntos: { type: Array, default: () => [] },
    entregablesPendientes: { type: Array, default: () => [] },
    equipos: { type: Array, default: () => [] },
});

const fechaHoy = computed(() =>
    new Date().toLocaleDateString("es-VE", {
        weekday: "long",
        year: "numeric",
        month: "long",
        day: "numeric",
    }),
);
</script>

<style>
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
