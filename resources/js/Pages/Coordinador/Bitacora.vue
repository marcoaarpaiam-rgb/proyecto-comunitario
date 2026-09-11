<template>
    <AppLayout page-title="Bitácora">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Bitácora del Sistema</h4>
            <p class="text-muted small mb-0">
                Registro inmutable de todas las acciones
            </p>
        </div>

        <!-- Filtros -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold small"
                            >Buscar acción</label
                        >
                        <input
                            v-model="filtros.busqueda"
                            type="text"
                            class="form-control form-control-sm"
                            placeholder="Ej: CREAR en equipo"
                        />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small"
                            >Entidad</label
                        >
                        <select
                            v-model="filtros.entidad_id"
                            class="form-select form-select-sm"
                        >
                            <option value="">Todas</option>
                            <option
                                v-for="ent in entidades"
                                :key="ent.ent_id"
                                :value="ent.ent_id"
                            >
                                {{ ent.ent_nombre }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small"
                            >Usuario</label
                        >
                        <select
                            v-model="filtros.usuario_id"
                            class="form-select form-select-sm"
                        >
                            <option value="">Todos</option>
                            <option
                                v-for="usu in usuarios"
                                :key="usu.usu_id"
                                :value="usu.usu_id"
                            >
                                {{ usu.usu_primer_nombre }}
                                {{ usu.usu_primer_apellido }}
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small"
                            >Desde</label
                        >
                        <input
                            v-model="filtros.fecha_desde"
                            type="date"
                            class="form-control form-control-sm"
                        />
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold small"
                            >Hasta</label
                        >
                        <input
                            v-model="filtros.fecha_hasta"
                            type="date"
                            class="form-control form-control-sm"
                        />
                    </div>
                    <div class="col-md-1 d-flex gap-1">
                        <button
                            class="btn btn-sm btn-danger w-100"
                            @click="buscar"
                        >
                            <i class="bi bi-search"></i>
                        </button>
                        <button
                            class="btn btn-sm btn-outline-secondary w-100"
                            @click="limpiar"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Fecha y Hora</th>
                            <th>Usuario</th>
                            <th>Acción</th>
                            <th>Entidad</th>
                            <th>ID Registro</th>
                            <th>Detalle</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="registros.data.length === 0">
                            <td colspan="6" class="text-center text-muted py-4">
                                No se encontraron registros
                            </td>
                        </tr>
                        <tr v-for="bit in registros.data" :key="bit.bit_id">
                            <td
                                class="ps-4 small text-muted"
                                style="white-space: nowrap"
                            >
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
                            <td class="small">
                                <span
                                    class="badge"
                                    :class="badgeAccion(bit.bit_accion)"
                                >
                                    {{ bit.bit_accion }}
                                </span>
                            </td>
                            <td>
                                <span
                                    v-if="bit.entidad"
                                    class="badge bg-primary bg-opacity-10 text-primary"
                                >
                                    {{ bit.entidad?.ent_nombre }}
                                </span>
                            </td>
                            <td class="small text-muted text-center">
                                {{ bit.bit_registro_id || "—" }}
                            </td>
                            <td>
                                <button
                                    v-if="
                                        bit.bit_estado_anterior ||
                                        bit.bit_estado_nuevo
                                    "
                                    class="btn btn-sm btn-outline-secondary"
                                    @click="verDetalle(bit)"
                                >
                                    <i class="bi bi-eye"></i>
                                </button>
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
                    Mostrando {{ registros.from }}–{{ registros.to }} de
                    {{ registros.total }} registros
                </small>
                <div class="d-flex gap-1">
                    <a
                        v-if="registros.prev_page_url"
                        :href="registros.prev_page_url"
                        class="btn btn-sm btn-outline-secondary"
                    >
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    <span class="btn btn-sm btn-danger disabled">
                        {{ registros.current_page }}
                    </span>
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

        <!-- Modal Detalle -->
        <div class="modal fade" id="modalDetalleBit" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            Detalle del Registro
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted small mb-2">
                                    ESTADO ANTERIOR
                                </h6>
                                <pre
                                    class="bg-light p-3 rounded small"
                                    style="
                                        max-height: 300px;
                                        overflow-y: auto;
                                        font-size: 0.75rem;
                                    "
                                    >{{ anterior }}</pre
                                >
                            </div>
                            <div class="col-md-6">
                                <h6 class="fw-bold text-muted small mb-2">
                                    ESTADO NUEVO
                                </h6>
                                <pre
                                    class="bg-light p-3 rounded small"
                                    style="
                                        max-height: 300px;
                                        overflow-y: auto;
                                        font-size: 0.75rem;
                                    "
                                    >{{ nuevo }}</pre
                                >
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Cerrar
                        </button>
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
import { Modal } from "bootstrap";

const props = defineProps({
    registros: Object,
    entidades: Array,
    usuarios: Array,
    filtros: Object,
});

const filtros = ref({ ...props.filtros });
const anterior = ref("");
const nuevo = ref("");
let modalDetalle = null;

const badgeAccion = (accion) => {
    if (accion?.startsWith("CREAR"))
        return "bg-success bg-opacity-10 text-success";
    if (accion?.startsWith("ACTUALIZAR"))
        return "bg-warning bg-opacity-10 text-warning";
    if (accion?.startsWith("ELIMINAR"))
        return "bg-danger bg-opacity-10 text-danger";
    return "bg-secondary bg-opacity-10 text-secondary";
};

const buscar = () => {
    router.get("/coordinador/bitacora", filtros.value, {
        preserveState: true,
        replace: true,
    });
};

const limpiar = () => {
    filtros.value = {
        busqueda: "",
        entidad_id: "",
        usuario_id: "",
        fecha_desde: "",
        fecha_hasta: "",
    };
    buscar();
};

const verDetalle = (bit) => {
    anterior.value = bit.bit_estado_anterior
        ? JSON.stringify(JSON.parse(bit.bit_estado_anterior), null, 2)
        : "Sin estado anterior";
    nuevo.value = bit.bit_estado_nuevo
        ? JSON.stringify(JSON.parse(bit.bit_estado_nuevo), null, 2)
        : "Sin estado nuevo";
    modalDetalle =
        modalDetalle || new Modal(document.getElementById("modalDetalleBit"));
    modalDetalle.show();
};
</script>
