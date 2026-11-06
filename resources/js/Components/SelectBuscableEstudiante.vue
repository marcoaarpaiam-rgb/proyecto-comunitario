<template>
    <div
        class="searchable-student-select position-relative"
        :style="{ zIndex: mostrarDropdown ? 100 : 'auto' }"
        ref="rootRef"
    >
        <!-- Modo: Estudiante Seleccionado -->
        <div
            v-if="estudianteActual"
            class="selected-box d-flex align-items-center justify-content-between p-1 px-2 rounded border"
            :class="{
                'is-lider-box': isLider,
                'border-danger': hasError
            }"
            style="min-height: 35px;"
        >
            <div class="d-flex align-items-center gap-2 text-truncate me-2">
                <div
                    class="avatar-circle flex-shrink-0"
                    :class="isLider ? 'bg-danger text-white' : 'bg-secondary text-white'"
                >
                    {{ iniciales(estudianteActual) }}
                </div>
                <div class="text-truncate" style="font-size: 0.85rem;">
                    <span class="fw-bold student-name">
                        {{ estudianteActual.usu_primer_nombre }} {{ estudianteActual.usu_primer_apellido }}
                    </span>
                    <span class="text-muted ms-1 small">
                        (V-{{ estudianteActual.usu_cedula }})
                    </span>
                </div>
            </div>
            <button
                v-if="!disabled"
                type="button"
                class="btn btn-sm p-0 px-1 border-0 text-muted btn-clear"
                @click="deseleccionar"
                title="Cambiar estudiante"
            >
                <i class="bi bi-x-circle-fill text-danger fs-6"></i>
            </button>
        </div>

        <!-- Modo: Búsqueda y Selección -->
        <div v-else class="search-input-wrap position-relative">
            <div class="input-group input-group-sm">
                <span class="input-group-text search-icon-addon border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input
                    ref="inputRef"
                    type="text"
                    v-model="query"
                    class="form-control form-control-sm border-start-0"
                    :class="{ 'is-invalid': hasError, 'border-danger': isLider && !hasError }"
                    :placeholder="disabled ? 'Seleccione primero una sección...' : placeholder"
                    :disabled="disabled"
                    autocomplete="off"
                    @focus="abrir"
                    @input="abrir"
                    @keydown.down.prevent="navegarAbajo"
                    @keydown.up.prevent="navegarArriba"
                    @keydown.enter.prevent="seleccionarActivo"
                    @keydown.esc="cerrar"
                />
                <button
                    v-if="query"
                    type="button"
                    class="btn btn-sm btn-outline-secondary border-start-0 btn-clean-query"
                    @click="limpiarBusqueda"
                >
                    <i class="bi bi-x"></i>
                </button>
            </div>

            <!-- Menú Desplegable Flotante -->
            <div
                v-if="mostrarDropdown && !disabled"
                class="dropdown-menu-list shadow-lg rounded border"
            >
                <div
                    v-if="listaFiltrada.length === 0"
                    class="p-3 text-center text-muted small"
                >
                    <i class="bi bi-person-x d-block fs-4 mb-1 text-danger"></i>
                    <span v-if="query">
                        No se encontraron estudiantes con "<strong>{{ query }}</strong>"
                    </span>
                    <span v-else>
                        No hay estudiantes disponibles en esta sección
                    </span>
                </div>
                <div
                    v-else
                    class="list-group list-group-flush"
                >
                    <div
                        v-for="(est, i) in listaFiltrada"
                        :key="est.usu_id"
                        class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2 py-2"
                        :class="{ 'active-item': i === itemHoverIndex }"
                        style="cursor: pointer; font-size: 0.85rem;"
                        @mousedown.prevent="seleccionar(est)"
                        @mouseenter="itemHoverIndex = i"
                    >
                        <div class="d-flex align-items-center gap-2 text-truncate">
                            <div class="mini-avatar">
                                {{ iniciales(est) }}
                            </div>
                            <div class="text-truncate">
                                <span class="fw-semibold item-name">
                                    {{ est.usu_primer_nombre }} {{ est.usu_primer_apellido }}
                                </span>
                            </div>
                        </div>
                        <span class="badge border ms-2 flex-shrink-0 cedula-badge" style="font-size: 0.74rem;">
                            V-{{ est.usu_cedula }}
                        </span>
                    </div>
                </div>
                <div v-if="listaFiltrada.length > 0" class="dropdown-footer p-1 px-2 text-center border-top">
                    <small class="text-muted" style="font-size: 0.72rem;">
                        {{ listaFiltrada.length }} estudiante(s) &bull; Presiona Enter o clic para seleccionar
                    </small>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch } from "vue";

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: "",
    },
    estudiantes: {
        type: Array,
        default: () => [],
    },
    todosEstudiantes: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: "Buscar por nombre, apellido o cédula...",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    isLider: {
        type: Boolean,
        default: false,
    },
    hasError: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["update:modelValue", "change"]);

const rootRef = ref(null);
const inputRef = ref(null);
const query = ref("");
const mostrarDropdown = ref(false);
const itemHoverIndex = ref(0);

// Busca el estudiante actual tanto en la lista filtrada como en la maestra completa
const estudianteActual = computed(() => {
    if (!props.modelValue) return null;
    const pool = props.todosEstudiantes?.length ? props.todosEstudiantes : props.estudiantes;
    return (
        pool?.find((e) => String(e.usu_id) === String(props.modelValue)) ||
        props.estudiantes?.find((e) => String(e.usu_id) === String(props.modelValue)) ||
        null
    );
});

const iniciales = (est) => {
    if (!est) return "??";
    const p1 = (est.usu_primer_nombre || "")[0] || "";
    const p2 = (est.usu_primer_apellido || "")[0] || "";
    return (p1 + p2).toUpperCase();
};

const normalizar = (str) => {
    return String(str || "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .trim();
};

const listaFiltrada = computed(() => {
    const list = props.estudiantes || [];
    if (!query.value.trim()) {
        return list.slice(0, 30);
    }
    const q = normalizar(query.value);
    const terminos = q.split(/\s+/).filter(Boolean);

    return list.filter((est) => {
        const nombreCompleto = normalizar(
            `${est.usu_primer_nombre || ""} ${est.usu_segundo_nombre || ""} ${est.usu_primer_apellido || ""} ${est.usu_segundo_apellido || ""}`
        );
        const cedula = String(est.usu_cedula || "").trim();

        return terminos.every(
            (term) => nombreCompleto.includes(term) || cedula.includes(term)
        );
    }).slice(0, 40);
});

const abrir = () => {
    if (props.disabled) return;
    mostrarDropdown.value = true;
    itemHoverIndex.value = 0;
};

const cerrar = () => {
    mostrarDropdown.value = false;
};

const limpiarBusqueda = () => {
    query.value = "";
    inputRef.value?.focus();
};

const seleccionar = (est) => {
    emit("update:modelValue", est.usu_id);
    emit("change", est);
    query.value = "";
    cerrar();
};

const deseleccionar = () => {
    emit("update:modelValue", "");
    emit("change", null);
    query.value = "";
};

const navegarAbajo = () => {
    if (!mostrarDropdown.value) {
        abrir();
        return;
    }
    if (itemHoverIndex.value < listaFiltrada.value.length - 1) {
        itemHoverIndex.value++;
    }
};

const navegarArriba = () => {
    if (itemHoverIndex.value > 0) {
        itemHoverIndex.value--;
    }
};

const seleccionarActivo = () => {
    if (
        mostrarDropdown.value &&
        listaFiltrada.value.length > 0 &&
        itemHoverIndex.value >= 0 &&
        itemHoverIndex.value < listaFiltrada.value.length
    ) {
        seleccionar(listaFiltrada.value[itemHoverIndex.value]);
    }
};

// Cierra al hacer clic fuera del componente
const handleClickOutside = (e) => {
    if (rootRef.value && !rootRef.value.contains(e.target)) {
        cerrar();
    }
};

onMounted(() => {
    document.addEventListener("mousedown", handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener("mousedown", handleClickOutside);
});

watch(
    () => props.disabled,
    (dis) => {
        if (dis) cerrar();
    }
);
</script>

<style scoped>
.searchable-student-select {
    width: 100%;
}

.selected-box {
    background: var(--bg-card, #ffffff);
    border-color: var(--border, #dee2e6) !important;
    transition: background 0.2s, border-color 0.2s;
}

.selected-box.is-lider-box {
    background: rgba(220, 53, 69, 0.08);
    border-color: rgba(220, 53, 69, 0.45) !important;
}

.student-name,
.item-name {
    color: var(--text-main, #212529);
}

.search-icon-addon,
.btn-clean-query {
    background: var(--bg-card, #ffffff) !important;
    border-color: var(--border, #ced4da) !important;
    color: var(--text-muted, #6c757d) !important;
}

.avatar-circle {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    font-weight: 800;
}

.mini-avatar {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: rgba(108, 117, 125, 0.15);
    color: var(--text-main, #495057);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.62rem;
    font-weight: 800;
    flex-shrink: 0;
}

.dropdown-menu-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--bg-card, #ffffff);
    z-index: 1060;
    max-height: 230px;
    overflow-y: auto;
    margin-top: 3px;
    border-color: var(--border, #dee2e6) !important;
}

.list-group-item {
    background-color: transparent !important;
    transition: background-color 0.15s;
    border-left: none;
    border-right: none;
    border-color: var(--border, #f0f0f0);
}

.list-group-item:hover,
.list-group-item.active-item {
    background-color: rgba(220, 53, 69, 0.1) !important;
}

.cedula-badge {
    background: var(--bg-th, #f8f9fa);
    color: var(--text-muted, #495057);
    border-color: var(--border, #dee2e6) !important;
}

.dropdown-footer {
    background: var(--bg-th, #f8f9fa);
    border-color: var(--border, #dee2e6) !important;
}

.btn-clear:hover {
    transform: scale(1.1);
}
</style>
