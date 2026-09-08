<template>
    <AppLayout page-title="Mi Equipo">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Mi Equipo</h4>
            <p class="text-muted small mb-0">
                Integrantes del equipo de proyecto
            </p>
        </div>

        <div v-if="!equipo" class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                No tienes equipo asignado aún
            </div>
        </div>

        <div v-else>
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold text-danger mb-1">
                        {{ equipo.equ_codigo }}
                    </h5>
                    <p class="mb-0">{{ equipo.equ_titulo }}</p>
                    <div class="text-muted small mt-1">
                        {{ equipo.trayecto?.tra_nombre }} — Sección
                        {{ equipo.seccion?.sec_codigo }}
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="fw-bold mb-0">
                        <i class="bi bi-people-fill text-danger me-2"></i>
                        Integrantes
                    </h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        <li
                            v-for="int in equipo.integrantes"
                            :key="int.ein_id"
                            class="list-group-item d-flex align-items-center gap-3 px-4 py-3"
                        >
                            <div class="avatar-sm">
                                {{ int.usuario?.usu_primer_nombre?.[0] }}
                                {{ int.usuario?.usu_primer_apellido?.[0] }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold">
                                    {{ int.usuario?.usu_primer_nombre }}
                                    {{ int.usuario?.usu_primer_apellido }}
                                </div>
                                <div class="text-muted small">
                                    CI: {{ int.usuario?.usu_cedula }}
                                </div>
                            </div>
                            <span
                                v-if="int.ein_es_lider"
                                class="badge bg-danger"
                                >Líder</span
                            >
                            <span v-else class="badge bg-secondary"
                                >Integrante</span
                            >
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
defineProps({ equipo: Object });
</script>

<style>
.avatar-sm {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #1f3864;
    color: white;
    font-weight: 700;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
</style>
