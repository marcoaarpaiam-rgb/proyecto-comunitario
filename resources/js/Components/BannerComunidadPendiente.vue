<template>
    <div>
        <BannerAlertaPasswordLider />

        <div
            v-if="status && !status.aprobada"
            class="alert border-2 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-3 shadow-sm"
            :class="status.estado_postulacion === 'pendiente_aprobacion' ? 'alert-info border-info' : status.estado_postulacion === 'rechazada' ? 'alert-danger border-danger' : 'alert-warning border-warning'"
            role="alert"
        >
            <div class="d-flex align-items-center gap-3">
                <i
                    class="bi fs-3 flex-shrink-0"
                    :class="status.estado_postulacion === 'pendiente_aprobacion' ? 'bi-hourglass-split text-info' : 'bi-exclamation-triangle-fill text-danger'"
                ></i>
                <div>
                    <div class="fw-bold text-dark">
                        Tu equipo debe postular los datos comunitarios para habilitar los entregables y la Carta de Presentación
                    </div>
                    <div class="small text-muted mt-1">
                        <span v-if="status.estado_postulacion === 'pendiente_aprobacion'" class="badge bg-info text-dark me-1">
                            Estado: Pendiente de Aprobación Docente
                        </span>
                        <span v-else-if="status.estado_postulacion === 'rechazada'" class="badge bg-danger me-1">
                            Estado: Devuelta para Ajuste — Revisa las observaciones y vuelve a postular
                        </span>
                        <span v-else class="badge bg-warning text-dark me-1">
                            Estado: Sin postulación comunitaria
                        </span>
                        Completa la información de la comunidad beneficiada, responsable de la comunidad, resumen y modalidad.
                    </div>
                </div>
            </div>
            <Link
                href="/lider/proyecto"
                class="btn btn-sm btn-danger fw-bold rounded-pill px-3 flex-shrink-0"
            >
                <i class="bi bi-building-add me-1"></i>
                {{ status.estado_postulacion === 'pendiente_aprobacion' ? 'Ver Postulación' : 'Postular Comunidad' }}
            </Link>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import BannerAlertaPasswordLider from "@/Components/BannerAlertaPasswordLider.vue";

const page = usePage();
const status = computed(() => page.props.liderComunidadStatus || null);
</script>
