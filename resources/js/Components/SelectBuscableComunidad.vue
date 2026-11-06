<template>
    <div
        class="searchable-comunidad-select position-relative"
        :style="{ zIndex: mostrarDropdown ? 100 : 'auto' }"
        ref="rootRef"
    >
        <!-- Modo: Comunidad Seleccionada -->
        <div
            v-if="comunidadActual"
            class="selected-box d-flex align-items-center justify-content-between p-1 px-2 rounded border"
            :class="{
                'border-danger': hasError,
                'box-sm': size === 'sm',
                'box-md': size !== 'sm'
            }"
        >
            <div class="d-flex align-items-center gap-2 text-truncate me-2 flex-grow-1" style="min-width: 0;">
                <div
                    class="avatar-circle flex-shrink-0"
                    :class="size === 'sm' ? 'avatar-sm' : 'avatar-md'"
                >
                    <i class="bi bi-building text-danger"></i>
                </div>
                <div class="text-truncate flex-grow-1" style="min-width: 0;" :style="{ fontSize: size === 'sm' ? '0.85rem' : '0.92rem' }">
                    <span class="fw-bold item-title text-truncate">
                        {{ comunidadActual.com_nombre }}
                    </span>
                    <span
                        v-if="comunidadActual.com_ubicacion"
                        class="text-muted ms-1 small text-truncate d-none d-lg-inline"
                    >
                        &bull; {{ comunidadActual.com_ubicacion }}
                    </span>
                </div>
            </div>
            <button
                v-if="!disabled"
                type="button"
                class="btn btn-sm p-0 px-1 border-0 text-muted btn-clear flex-shrink-0"
                @click="deseleccionar"
                title="Cambiar comunidad"
            >
                <i class="bi bi-x-circle-fill text-danger fs-6"></i>
            </button>
        </div>

        <!-- Modo: Búsqueda y Selección -->
        <div v-else class="search-input-wrap position-relative w-100" style="min-width: 0;">
            <div class="input-group" :class="{ 'input-group-sm': size === 'sm' }">
                <span class="input-group-text search-icon-addon border-end-0">
                    <i class="bi bi-search"></i>
                </span>
                <input
                    ref="inputRef"
                    type="text"
                    v-model="query"
                    class="form-control border-start-0"
                    :class="{
                        'form-control-sm': size === 'sm',
                        'is-invalid': hasError
                    }"
                    :placeholder="placeholder"
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
                    <i class="bi bi-building-slash d-block fs-4 mb-1 text-danger"></i>
                    <span v-if="query">
                        No se encontraron comunidades con "<strong>{{ query }}</strong>"
                    </span>
                    <span v-else>
                        No hay comunidades disponibles
                    </span>
                </div>
                <div
                    v-else
                    class="list-group list-group-flush"
                >
                    <div
                        v-for="(com, i) in listaFiltrada"
                        :key="com.com_id"
                        class="list-group-item list-group-item-action d-flex align-items-center justify-content-between p-2 py-2"
                        :class="{ 'active-item': i === itemHoverIndex }"
                        style="cursor: pointer; font-size: 0.85rem;"
                        @mousedown.prevent="seleccionar(com)"
                        @mouseenter="itemHoverIndex = i"
                    >
                        <div class="d-flex align-items-center gap-2 text-truncate me-2 flex-grow-1" style="min-width: 0;">
                            <div class="mini-icon flex-shrink-0">
                                <i class="bi bi-building"></i>
                            </div>
                            <div class="text-truncate flex-grow-1" style="min-width: 0;">
                                <div class="fw-semibold item-name text-truncate">
                                    {{ com.com_nombre }}
                                </div>
                                <div
                                    v-if="com.com_ubicacion"
                                    class="text-muted small text-truncate"
                                    style="font-size: 0.74rem;"
                                >
                                    <i class="bi bi-geo-alt me-1"></i>{{ com.com_ubicacion }}
                                </div>
                            </div>
                        </div>
                        <span
                            v-if="com.proyectos?.length"
                            class="badge border flex-shrink-0 proyectos-badge"
                            style="font-size: 0.72rem;"
                        >
                            {{ com.proyectos.length }} proyecto(s)
                        </span>
                    </div>
                </div>
                <div v-if="listaFiltrada.length > 0" class="dropdown-footer p-1 px-2 text-center border-top">
                    <small class="text-muted" style="font-size: 0.72rem;">
                        {{ listaFiltrada.length }} comunidad(es) encontrada(s) &bull; Presiona Enter o clic para seleccionar
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
    comunidades: {
        type: Array,
        default: () => [],
    },
    placeholder: {
        type: String,
        default: "🔍 Escribe para buscar comunidad...",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    hasError: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: "sm",
    },
});

const emit = defineEmits(["update:modelValue", "change"]);

const rootRef = ref(null);
const inputRef = ref(null);
const query = ref("");
const mostrarDropdown = ref(false);
const itemHoverIndex = ref(0);

const comunidadActual = computed(() => {
    if (!props.modelValue) return null;
    return (
        props.comunidades?.find(
            (c) => String(c.com_id) === String(props.modelValue)
        ) || null
    );
});

const normalizar = (str) => {
    return String(str || "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "")
        .trim();
};

const listaFiltrada = computed(() => {
    const list = props.comunidades || [];
    if (!query.value.trim()) {
        return list.slice(0, 35);
    }
    const q = normalizar(query.value);
    const terminos = q.split(/\s+/).filter(Boolean);

    return list.filter((com) => {
        const nombre = normalizar(com.com_nombre || "");
        const ubicacion = normalizar(com.com_ubicacion || "");
        const combinada = `${nombre} ${ubicacion}`;

        return terminos.every((term) => combinada.includes(term));
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

const seleccionar = (com) => {
    emit("update:modelValue", com.com_id);
    emit("change", com);
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
.searchable-comunidad-select {
    flex: 1 1 auto;
    min-width: 0;
    width: 0;
}

.selected-box {
    background: var(--bg-card, #ffffff);
    border-color: var(--border, #dee2e6) !important;
    transition: background 0.2s, border-color 0.2s;
    min-width: 0;
    width: 100%;
}

.selected-box.box-sm {
    min-height: 35px;
}

.selected-box.box-md {
    min-height: 38px;
}

.item-title,
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
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: rgba(220, 53, 69, 0.1);
}

.avatar-sm {
    width: 24px;
    height: 24px;
    font-size: 0.75rem;
}

.avatar-md {
    width: 28px;
    height: 28px;
    font-size: 0.85rem;
}

.mini-icon {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    background: rgba(220, 53, 69, 0.08);
    color: #dc3545;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
}

.dropdown-menu-list {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--bg-card, #ffffff);
    z-index: 1060;
    max-height: 240px;
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
    background-color: rgba(220, 53, 69, 0.08) !important;
}

.proyectos-badge {
    background: var(--bg-th, #f8f9fa);
    color: var(--text-muted, #6c757d);
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
