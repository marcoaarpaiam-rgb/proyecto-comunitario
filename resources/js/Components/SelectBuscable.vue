<template>
    <div
        class="select-buscable position-relative"
        :style="{ zIndex: mostrarDropdown ? 1060 : 'auto' }"
        ref="rootRef"
    >
        <!-- Caja seleccionada / trigger -->
        <button
            v-if="!mostrarDropdown"
            type="button"
            class="sb-trigger form-control text-start d-flex align-items-center justify-content-between gap-2"
            :class="{
                'form-control-sm': size === 'sm',
                'is-invalid': hasError,
                'is-disabled': disabled,
            }"
            :disabled="disabled"
            @click="abrir"
        >
            <span class="text-truncate flex-grow-1" :class="{ 'sb-placeholder': !tieneValorSeleccionado }">
                {{ textoMostrado }}
            </span>
            <span
                v-if="puedeLimpiar && tieneValorSeleccionado && !disabled"
                role="button"
                tabindex="-1"
                class="sb-clear text-danger px-1 flex-shrink-0"
                title="Limpiar selección"
                @click.stop="limpiar"
            >
                <i class="bi bi-x-circle-fill"></i>
            </span>
            <i class="bi bi-chevron-down small text-muted flex-shrink-0"></i>
        </button>

        <!-- Modo búsqueda activa -->
        <div v-else class="sb-search-box">
            <div class="input-group" :class="{ 'input-group-sm': size === 'sm' }">
                <span class="input-group-text sb-addon">
                    <i class="bi bi-search"></i>
                </span>
                <input
                    ref="inputRef"
                    type="text"
                    v-model="query"
                    class="form-control"
                    :class="{
                        'form-control-sm': size === 'sm',
                        'is-invalid': hasError,
                    }"
                    :placeholder="searchPlaceholder || placeholder"
                    autocomplete="off"
                    @keydown.down.prevent="navegar(1)"
                    @keydown.up.prevent="navegar(-1)"
                    @keydown.enter.prevent="seleccionarActivo"
                    @keydown.esc.prevent="cerrar"
                />
                <button
                    type="button"
                    class="btn btn-outline-secondary"
                    :class="{ 'btn-sm': size === 'sm' }"
                    tabindex="-1"
                    @mousedown.prevent="cerrar"
                    title="Cerrar"
                >
                    <i class="bi bi-chevron-up"></i>
                </button>
            </div>

            <!-- Lista de opciones filtradas -->
            <div class="sb-dropdown shadow-lg rounded border">
                <div v-if="opcionesFiltradas.length === 0" class="p-3 text-center text-muted small">
                    Sin resultados para "<strong>{{ query }}</strong>"
                </div>
                <div v-else class="list-group list-group-flush">
                    <button
                        v-for="(opt, idx) in opcionesFiltradas"
                        :key="keyOpcion(opt, idx)"
                        type="button"
                        class="list-group-item list-group-item-action text-start d-flex align-items-center justify-content-between gap-2 py-2 px-3 sb-item"
                        :class="{
                            'sb-active': idx === hoverIdx,
                            'sb-selected': esMismoValor(opt.value, modelValue),
                            'disabled opacity-50': opt.disabled,
                        }"
                        :disabled="opt.disabled"
                        @mousedown.prevent="!opt.disabled && seleccionar(opt)"
                        @mouseenter="hoverIdx = idx"
                    >
                        <div class="text-truncate flex-grow-1" style="min-width: 0;">
                            <div class="text-truncate fw-medium">{{ opt.label }}</div>
                            <div v-if="opt.sublabel" class="text-muted text-truncate" style="font-size: 0.74rem;">
                                {{ opt.sublabel }}
                            </div>
                        </div>
                        <i v-if="esMismoValor(opt.value, modelValue)" class="bi bi-check2 text-danger fw-bold flex-shrink-0"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, nextTick, onMounted, onBeforeUnmount } from "vue";

const props = defineProps({
    modelValue: {
        type: [String, Number, Boolean, Object, null],
        default: "",
    },
    options: {
        type: Array,
        default: () => [],
    },
    valueKey: {
        type: String,
        default: "value",
    },
    labelKey: {
        type: String,
        default: "label",
    },
    sublabelKey: {
        type: String,
        default: "sublabel",
    },
    placeholder: {
        type: String,
        default: "Seleccionar...",
    },
    searchPlaceholder: {
        type: String,
        default: "Buscar...",
    },
    size: {
        type: String,
        default: "md",
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    hasError: {
        type: Boolean,
        default: false,
    },
    clearable: {
        type: Boolean,
        default: false,
    },
    emptyValue: {
        type: [String, Number, Boolean, null],
        default: undefined,
    },
});

const emit = defineEmits(["update:modelValue", "change"]);

const rootRef = ref(null);
const inputRef = ref(null);
const mostrarDropdown = ref(false);
const query = ref("");
const hoverIdx = ref(0);

const opcionesNormalizadas = computed(() => {
    return (props.options || []).map((item) => {
        if (item !== null && typeof item === "object" && !Array.isArray(item)) {
            const val = item[props.valueKey] !== undefined ? item[props.valueKey] : item.id ?? item.value;
            const lbl = item[props.labelKey] !== undefined ? item[props.labelKey] : item.nombre ?? item.label ?? String(val ?? "");
            const sub = item[props.sublabelKey] ?? item.sublabel ?? "";
            return {
                value: val,
                label: String(lbl ?? ""),
                sublabel: sub ? String(sub) : "",
                disabled: Boolean(item.disabled),
            };
        }
        return {
            value: item,
            label: String(item ?? ""),
            sublabel: "",
            disabled: false,
        };
    });
});

const opcionVacia = computed(() =>
    opcionesNormalizadas.value.find((o) => o.value === "" || o.value === null || o.value === undefined),
);

const puedeLimpiar = computed(() => props.clearable || Boolean(opcionVacia.value));

const valorAlLimpiar = computed(() => {
    if (props.emptyValue !== undefined) return props.emptyValue;
    if (opcionVacia.value) return opcionVacia.value.value;
    return "";
});

const esMismoValor = (a, b) => {
    if (a === b) return true;
    if (
        typeof a === "boolean" ||
        typeof b === "boolean" ||
        typeof a === "object" ||
        typeof b === "object" ||
        a === null ||
        b === null ||
        a === undefined ||
        b === undefined
    ) {
        return false;
    }
    return String(a) === String(b);
};

const opcionActual = computed(() =>
    opcionesNormalizadas.value.find((o) => esMismoValor(o.value, props.modelValue)) || null,
);

const tieneValorSeleccionado = computed(() => {
    if (props.modelValue === "" || props.modelValue === null || props.modelValue === undefined) {
        return false;
    }
    return Boolean(opcionActual.value) || props.modelValue !== "";
});

const textoMostrado = computed(() => {
    if (opcionActual.value) return opcionActual.value.label;
    if (opcionVacia.value) return opcionVacia.value.label;
    return props.placeholder;
});

const normalizarTxt = (s) =>
    String(s || "")
        .toLowerCase()
        .normalize("NFD")
        .replace(/[\u0300-\u036f]/g, "");

const opcionesFiltradas = computed(() => {
    const q = normalizarTxt(query.value).trim();
    if (!q) return opcionesNormalizadas.value;
    return opcionesNormalizadas.value.filter(
        (o) => normalizarTxt(o.label).includes(q) || normalizarTxt(o.sublabel).includes(q),
    );
});

const keyOpcion = (opt, idx) => `${idx}-${typeof opt.value}-${String(opt.value)}`;

const abrir = () => {
    if (props.disabled) return;
    query.value = "";
    hoverIdx.value = 0;
    mostrarDropdown.value = true;
    nextTick(() => {
        inputRef.value?.focus();
    });
};

const cerrar = () => {
    mostrarDropdown.value = false;
    query.value = "";
};

const seleccionar = (opt) => {
    emit("update:modelValue", opt.value);
    emit("change", opt.value);
    cerrar();
};

const limpiar = () => {
    const val = valorAlLimpiar.value;
    emit("update:modelValue", val);
    emit("change", val);
    cerrar();
};

const navegar = (delta) => {
    const len = opcionesFiltradas.value.length;
    if (!len) return;
    hoverIdx.value = (hoverIdx.value + delta + len) % len;
};

const seleccionarActivo = () => {
    const opt = opcionesFiltradas.value[hoverIdx.value];
    if (opt && !opt.disabled) {
        seleccionar(opt);
    }
};

const handleClickFuera = (ev) => {
    if (rootRef.value && !rootRef.value.contains(ev.target)) {
        cerrar();
    }
};

onMounted(() => document.addEventListener("mousedown", handleClickFuera));
onBeforeUnmount(() => document.removeEventListener("mousedown", handleClickFuera));
</script>

<style scoped>
.sb-trigger {
    background-color: var(--bg-card, #ffffff);
    color: var(--text-main, #1a1a1a);
    border-color: var(--border, #dee2e6);
    cursor: pointer;
    min-height: 36px;
}
.sb-trigger.form-control-sm {
    min-height: 31px;
    font-size: 0.85rem;
}
.sb-trigger.is-disabled {
    opacity: 0.65;
    cursor: not-allowed;
    background-color: var(--bg-th, #f8f9fa);
}
.sb-placeholder {
    color: var(--text-muted, #6c757d);
}
.sb-clear:hover {
    opacity: 0.8;
}
.sb-addon {
    background-color: var(--bg-th, #f8f9fa);
    color: var(--text-muted, #6c757d);
    border-color: var(--border, #dee2e6);
}
.sb-search-box .form-control {
    background-color: var(--bg-card, #ffffff);
    color: var(--text-main, #1a1a1a);
    border-color: var(--border, #dee2e6);
}
.sb-dropdown {
    position: absolute;
    top: calc(100% + 4px);
    left: 0;
    min-width: 100%;
    width: max-content;
    max-width: min(90vw, 420px);
    max-height: 240px;
    overflow-y: auto;
    background-color: var(--bg-card, #ffffff);
    border-color: var(--border, #dee2e6) !important;
    z-index: 1065;
}
.sb-item {
    background-color: var(--bg-card, #ffffff);
    color: var(--text-main, #1a1a1a);
    border-color: var(--border, #dee2e6);
    font-size: 0.86rem;
}
.sb-item.sb-active,
.sb-item:hover {
    background-color: rgba(220, 53, 69, 0.08);
    color: var(--text-main, #1a1a1a);
}
.sb-item.sb-selected {
    background-color: rgba(220, 53, 69, 0.14);
    font-weight: 600;
}
</style>
