<template>
    <AppLayout page-title="Carta de Presentación">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Carta de Presentación</h4>
            <p class="text-muted small mb-0">
                Solicita la carta al inicio del trayecto para presentarte ante
                la comunidad
            </p>
        </div>

        <!-- Sin proyecto -->
        <div v-if="!proyecto" class="card border-0 shadow-sm">
            <div class="card-body text-center text-muted py-5">
                <i class="bi bi-file-earmark-x fs-1 mb-3 d-block"></i>
                Tu equipo aún no tiene un proyecto aprobado. No puedes solicitar
                la carta hasta que la comunidad sea aprobada.
            </div>
        </div>

        <div v-else class="row g-4">
            <!-- Info del proyecto -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold text-danger mb-3">
                            <i class="bi bi-building me-2"></i>Datos del
                            Proyecto
                        </h6>
                        <table class="table table-borderless small">
                            <tr>
                                <td class="fw-semibold text-muted pe-3">
                                    Equipo
                                </td>
                                <td>{{ equipo?.equ_codigo }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">
                                    Comunidad
                                </td>
                                <td>{{ proyecto.comunidad?.com_nombre }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">
                                    Ubicación
                                </td>
                                <td>{{ proyecto.comunidad?.com_ubicacion }}</td>
                            </tr>
                            <tr>
                                <td class="fw-semibold text-muted">
                                    Aprobación
                                </td>
                                <td>
                                    <span
                                        v-if="proyecto.pco_fecha_aprobacion"
                                        class="badge bg-success"
                                        >Aprobada</span
                                    >
                                    <span
                                        v-else
                                        class="badge bg-warning text-dark"
                                        >Pendiente</span
                                    >
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Estado de la carta -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body">
                        <h6 class="fw-bold mb-3">
                            <i
                                class="bi bi-file-earmark-text text-danger me-2"
                            ></i>
                            Estado de la Carta
                        </h6>

                        <!-- Sin carta solicitada -->
                        <div v-if="!carta" class="text-center py-4">
                            <i
                                class="bi bi-send fs-1 text-muted mb-3 d-block"
                            ></i>
                            <p class="text-muted mb-4">
                                Aún no has solicitado la carta de presentación.
                                Una vez solicitada, la coordinación la revisará
                                y generará con firma y sello institucional.
                            </p>
                            <button
                                class="btn btn-danger fw-semibold px-4"
                                @click="solicitar"
                                :disabled="
                                    !proyecto.pco_fecha_aprobacion || loading
                                "
                            >
                                <span
                                    v-if="loading"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                <i v-else class="bi bi-send me-2"></i>
                                Solicitar Carta de Presentación
                            </button>
                            <div
                                v-if="!proyecto.pco_fecha_aprobacion"
                                class="text-muted small mt-2"
                            >
                                La comunidad debe estar aprobada para solicitar
                                la carta.
                            </div>
                        </div>

                        <!-- Carta solicitada pendiente -->
                        <div
                            v-else-if="
                                carta.cpr_solicitada &&
                                !carta.cpr_fecha_aprobacion
                            "
                            class="text-center py-4"
                        >
                            <i
                                class="bi bi-hourglass-split fs-1 text-warning mb-3 d-block"
                            ></i>
                            <h6 class="fw-bold text-warning">
                                Carta en Revisión
                            </h6>
                            <p class="text-muted small">
                                Tu solicitud fue enviada. La coordinación está
                                revisando y generando la carta con firma y sello
                                institucional. Te notificaremos cuando esté
                                lista.
                            </p>
                            <span
                                class="badge bg-warning text-dark fs-6 px-3 py-2"
                            >
                                Pendiente de aprobación
                            </span>
                        </div>

                        <!-- Carta aprobada y lista -->
                        <div
                            v-else-if="carta.cpr_fecha_aprobacion"
                            class="text-center py-4"
                        >
                            <i
                                class="bi bi-file-earmark-check fs-1 text-success mb-3 d-block"
                            ></i>
                            <h6 class="fw-bold text-success">¡Carta Lista!</h6>
                            <p class="text-muted small mb-4">
                                La carta fue aprobada y generada con firma y
                                sello de la coordinación. Ya puedes presentarte
                                ante la comunidad.
                            </p>
                            <div
                                class="d-flex gap-3 justify-content-center flex-wrap"
                            >
                                <span class="badge bg-success fs-6 px-3 py-2">
                                    <i class="bi bi-patch-check me-1"></i
                                    >Aprobada
                                </span>
                                <span
                                    v-if="carta.cpr_sello"
                                    class="badge bg-primary fs-6 px-3 py-2"
                                >
                                    <i class="bi bi-award me-1"></i>Con Sello
                                </span>
                                <span
                                    v-if="carta.cpr_firma_digital"
                                    class="badge bg-info fs-6 px-3 py-2"
                                >
                                    <i class="bi bi-pen me-1"></i>Con Firma
                                </span>
                            </div>
                            <a
                                v-if="carta?.cpr_fecha_aprobacion"
                                :href="`/lider/carta/${carta.cpr_id}/descargar`"
                                class="btn btn-success fw-semibold px-4 mt-3"
                                target="_blank"
                            >
                                <i class="bi bi-download me-2"></i>Descargar PDF
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
    carta: Object,
    proyecto: Object,
    equipo: Object,
});

const loading = ref(false);

const solicitar = () => {
    if (!props.proyecto) return;
    loading.value = true;
    router.post(
        "/lider/carta",
        {
            cpr_id_pco: props.proyecto.pco_id,
        },
        {
            onError: () => {
                loading.value = false;
            },
            onFinish: () => {
                loading.value = false;
            },
        },
    );
};
</script>
