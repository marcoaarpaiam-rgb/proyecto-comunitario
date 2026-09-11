<template>
    <AppLayout page-title="Cartas de Presentación">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Cartas de Presentación</h4>
            <p class="text-muted small mb-0">
                Aprueba las solicitudes y actualiza tu firma institucional
            </p>
        </div>

        <div class="row g-4">
            <!-- Solicitudes pendientes -->
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0">
                            <i
                                class="bi bi-hourglass-split text-warning me-2"
                            ></i>
                            Solicitudes Pendientes
                            <span
                                v-if="solicitudes.length > 0"
                                class="badge bg-warning text-dark ms-2"
                            >
                                {{ solicitudes.length }}
                            </span>
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div
                            v-if="solicitudes.length === 0"
                            class="text-center text-muted py-4 small"
                        >
                            No hay solicitudes pendientes
                        </div>
                        <table v-else class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Equipo</th>
                                    <th>Comunidad</th>
                                    <th>Solicitada</th>
                                    <th class="text-end pe-4">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr
                                    v-for="sol in solicitudes"
                                    :key="sol.cpr_id"
                                >
                                    <td class="ps-4 fw-bold text-danger">
                                        {{
                                            sol.proyecto_comunidad?.equipo
                                                ?.equ_codigo
                                        }}
                                    </td>
                                    <td class="small">
                                        {{
                                            sol.proyecto_comunidad?.comunidad
                                                ?.com_nombre
                                        }}
                                    </td>
                                    <td class="small text-muted">
                                        {{
                                            new Date(
                                                sol.cpr_fecha_generacion,
                                            ).toLocaleDateString("es-VE")
                                        }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <button
                                            class="btn btn-sm btn-success fw-semibold"
                                            @click="aprobar(sol)"
                                            :disabled="!firma"
                                        >
                                            <i class="bi bi-check-lg me-1"></i
                                            >Aprobar y Generar
                                        </button>
                                        <div
                                            v-if="!firma"
                                            class="text-danger small mt-1"
                                        >
                                            Debes subir tu firma primero
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Firma del coordinador -->
            <div class="col-md-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-pen text-danger me-2"></i>Mi Firma
                            Institucional
                        </h6>
                    </div>
                    <div class="card-body text-center">
                        <div v-if="firma" class="mb-3">
                            <img
                                :src="'/storage/' + firma"
                                alt="Firma actual"
                                class="img-fluid border rounded"
                                style="max-height: 100px"
                            />
                            <div class="text-success small mt-2">
                                <i class="bi bi-check-circle me-1"></i>Firma
                                configurada
                            </div>
                        </div>
                        <div v-else class="text-muted mb-3 py-3">
                            <i class="bi bi-pen fs-1 mb-2 d-block"></i>
                            <small>No tienes firma configurada</small>
                        </div>
                        <form
                            @submit.prevent="subirFirma"
                            enctype="multipart/form-data"
                        >
                            <input
                                type="file"
                                class="form-control form-control-sm mb-2"
                                accept="image/png,image/jpeg"
                                @change="firmaFile = $event.target.files[0]"
                            />
                            <button
                                type="submit"
                                class="btn btn-sm btn-danger w-100"
                                :disabled="!firmaFile || loadingFirma"
                            >
                                <span
                                    v-if="loadingFirma"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                {{ firma ? "Actualizar Firma" : "Subir Firma" }}
                            </button>
                        </form>
                        <div class="text-muted small mt-2">
                            PNG o JPG. Máx. 2MB. Se usará en todas las cartas.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historial de cartas -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-archive text-primary me-2"></i>
                            Cartas Generadas
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div
                            v-if="cartas.length === 0"
                            class="text-center text-muted py-4 small"
                        >
                            No hay cartas generadas
                        </div>
                        <ul v-else class="list-group list-group-flush">
                            <li
                                v-for="carta in cartas"
                                :key="carta.cpr_id"
                                class="list-group-item px-3 py-3"
                            >
                                <div
                                    class="d-flex align-items-center justify-content-between"
                                >
                                    <div>
                                        <div class="fw-semibold small">
                                            {{
                                                carta.proyecto_comunidad?.equipo
                                                    ?.equ_codigo
                                            }}
                                        </div>
                                        <div
                                            class="text-muted"
                                            style="font-size: 0.78rem"
                                        >
                                            {{
                                                carta.proyecto_comunidad
                                                    ?.comunidad?.com_nombre
                                            }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="badge bg-success d-block mb-1"
                                            >Aprobada</span
                                        >
                                        <span
                                            class="text-muted"
                                            style="font-size: 0.75rem"
                                        >
                                            {{
                                                new Date(
                                                    carta.cpr_fecha_aprobacion,
                                                ).toLocaleDateString("es-VE")
                                            }}
                                        </span>
                                    </div>
                                    <a
                                        :href="`/coordinador/cartas/${carta.cpr_id}/descargar`"
                                        class="btn btn-sm btn-outline-success ms-2"
                                        target="_blank"
                                        title="Descargar PDF"
                                    >
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <ModalConfirmar
            ref="modalConfirmarRef"
            modal-id="modalConfirmarCarta"
            titulo="¿Generar Carta?"
            mensaje="Se generará el PDF con tu firma y sello institucional. Esta acción notificará al líder del equipo."
            icono="📄"
            btn-texto="Sí, generar"
            btn-clase="btn-success"
            @confirmado="confirmarAprobar"
        />
    </AppLayout>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import ModalConfirmar from "@/Components/ModalConfirmar.vue";

const props = defineProps({
    cartas: Array,
    solicitudes: Array,
    proyectos: Array,
    firma: String,
});

const firmaFile = ref(null);
const loadingFirma = ref(false);

const modalConfirmarRef = ref(null);
const cartaSeleccionada = ref(null);

const aprobar = (carta) => {
    cartaSeleccionada.value = carta;
    modalConfirmarRef.value?.abrir();
};

const confirmarAprobar = () => {
    router.post(
        `/coordinador/cartas/${cartaSeleccionada.value.cpr_id}/aprobar`,
    );
};

const subirFirma = () => {
    if (!firmaFile.value) return;
    loadingFirma.value = true;
    const data = new FormData();
    data.append("firma", firmaFile.value);
    router.post("/coordinador/cartas/firma", data, {
        forceFormData: true,
        onFinish: () => {
            loadingFirma.value = false;
        },
    });
};
</script>
