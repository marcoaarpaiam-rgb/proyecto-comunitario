<template>
    <div>
        <!-- Barra de búsqueda y filtros -->
        <div class="d-flex gap-2 mb-3 flex-wrap">
            <div class="input-group" style="max-width: 300px">
                <span class="input-group-text bg-white">
                    <i class="bi bi-search text-muted"></i>
                </span>
                <input
                    v-model="busqueda"
                    type="text"
                    class="form-control border-start-0"
                    :placeholder="placeholder"
                />
                <button
                    v-if="busqueda"
                    class="btn btn-outline-secondary"
                    @click="busqueda = ''"
                >
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <slot name="filtros" />
            <div class="ms-auto text-muted small d-flex align-items-center">
                {{ registrosFiltrados.length }} resultado(s)
            </div>
        </div>

        <!-- Tabla con slot -->
        <slot :registros="registrosPaginados" />

        <!-- Paginación -->
        <div
            v-if="totalPaginas > 1"
            class="d-flex justify-content-between align-items-center mt-3"
        >
            <small class="text-muted">
                Mostrando {{ desde + 1 }}–{{
                    Math.min(hasta, registrosFiltrados.length)
                }}
                de {{ registrosFiltrados.length }}
            </small>
            <div class="d-flex gap-1">
                <button
                    class="btn btn-sm btn-outline-secondary"
                    :disabled="paginaActual === 1"
                    @click="paginaActual--"
                >
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button
                    v-for="p in paginasVisibles"
                    :key="p"
                    class="btn btn-sm"
                    :class="
                        p === paginaActual
                            ? 'btn-danger'
                            : 'btn-outline-secondary'
                    "
                    @click="paginaActual = p"
                >
                    {{ p }}
                </button>
                <button
                    class="btn btn-sm btn-outline-secondary"
                    :disabled="paginaActual === totalPaginas"
                    @click="paginaActual++"
                >
                    <i class="bi bi-chevron-right"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";

const props = defineProps({
    items: { type: Array, default: () => [] },
    camposBusqueda: { type: Array, default: () => [] },
    porPagina: { type: Number, default: 10 },
    placeholder: { type: String, default: "Buscar..." },
});

const busqueda = ref("");
const paginaActual = ref(1);

watch(busqueda, () => {
    paginaActual.value = 1;
});

const registrosFiltrados = computed(() => {
    if (!busqueda.value.trim()) return props.items;
    const q = busqueda.value.toLowerCase();
    return props.items.filter((item) =>
        props.camposBusqueda.some((campo) => {
            const val = campo.split(".").reduce((o, k) => o?.[k], item);
            return String(val ?? "")
                .toLowerCase()
                .includes(q);
        }),
    );
});

const totalPaginas = computed(() =>
    Math.ceil(registrosFiltrados.value.length / props.porPagina),
);
const desde = computed(() => (paginaActual.value - 1) * props.porPagina);
const hasta = computed(() => desde.value + props.porPagina);
const registrosPaginados = computed(() =>
    registrosFiltrados.value.slice(desde.value, hasta.value),
);
const paginasVisibles = computed(() => {
    const total = totalPaginas.value;
    const actual = paginaActual.value;
    const paginas = [];
    const inicio = Math.max(1, actual - 2);
    const fin = Math.min(total, actual + 2);
    for (let i = inicio; i <= fin; i++) paginas.push(i);
    return paginas;
});
</script>
