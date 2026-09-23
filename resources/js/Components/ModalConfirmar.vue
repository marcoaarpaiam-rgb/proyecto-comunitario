<template>
    <Teleport to="body">
        <div
            v-if="visible"
            style="
                position: fixed;
                inset: 0;
                z-index: 2000;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 1rem;
            "
        >
            <!-- Fondo -->
            <div
                style="
                    position: fixed;
                    inset: 0;
                    background: rgba(0, 0, 0, 0.55);
                "
                @click="cancelar"
            ></div>
            <!-- Card -->
            <div
                style="
                    position: relative;
                    background: var(--bg-card, #fff);
                    border-radius: 16px;
                    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.25);
                    border: 1px solid var(--border, #dee2e6);
                    width: 100%;
                    max-width: 380px;
                    overflow: hidden;
                    animation: mfadeIn 0.15s ease;
                "
            >
                <style>
                    @keyframes mfadeIn {
                        from {
                            opacity: 0;
                            transform: scale(0.95);
                        }
                        to {
                            opacity: 1;
                            transform: scale(1);
                        }
                    }
                </style>

                <!-- Ícono + Título -->
                <div style="padding: 1.75rem 1.5rem 1rem; text-align: center">
                    <div
                        style="
                            font-size: 2.6rem;
                            line-height: 1;
                            margin-bottom: 0.75rem;
                        "
                    >
                        {{ icono }}
                    </div>
                    <h6
                        style="
                            font-weight: 800;
                            color: var(--text-main, #1a1a1a);
                            font-size: 1.05rem;
                            margin: 0 0 0.4rem;
                        "
                    >
                        {{ titulo }}
                    </h6>
                    <p
                        v-if="mensaje"
                        style="
                            color: var(--text-muted, #6c757d);
                            font-size: 0.9rem;
                            margin: 0;
                            line-height: 1.5;
                        "
                    >
                        {{ mensaje }}
                    </p>
                    <!-- Slot para contenido extra (ej: campo de motivo) -->
                    <slot></slot>
                </div>

                <!-- Botones -->
                <div
                    style="
                        display: flex;
                        gap: 0.6rem;
                        padding: 0.85rem 1.5rem 1.5rem;
                        justify-content: center;
                    "
                >
                    <button
                        @click="cancelar"
                        style="
                            flex: 1;
                            padding: 0.55rem 1rem;
                            border-radius: 8px;
                            border: 1px solid var(--border, #dee2e6);
                            background: var(--bg-th, #f8f9fa);
                            color: var(--text-main, #1a1a1a);
                            font-weight: 700;
                            font-size: 0.9rem;
                            cursor: pointer;
                            transition: background 0.15s;
                        "
                        @mouseover="
                            (e) =>
                                (e.target.style.background =
                                    'var(--bg-hover,#eee)')
                        "
                        @mouseout="
                            (e) =>
                                (e.target.style.background =
                                    'var(--bg-th,#f8f9fa)')
                        "
                    >
                        {{ textoCancelar }}
                    </button>
                    <button
                        @click="confirmar"
                        :disabled="loading"
                        :style="`flex:1;padding:.55rem 1rem;border-radius:8px;border:none;background:${colorConfirmar};color:#fff;font-weight:700;font-size:.9rem;cursor:pointer;opacity:${loading ? 0.7 : 1};transition:opacity .15s;`"
                    >
                        <span
                            v-if="loading"
                            style="
                                display: inline-block;
                                width: 14px;
                                height: 14px;
                                border: 2px solid #fff;
                                border-top-color: transparent;
                                border-radius: 50%;
                                animation: spin 0.6s linear infinite;
                                margin-right: 6px;
                                vertical-align: middle;
                            "
                        ></span>
                        <style>
                            @keyframes spin {
                                to {
                                    transform: rotate(360deg);
                                }
                            }
                        </style>
                        {{ textoConfirmar }}
                    </button>
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
import { ref } from "vue";

const props = defineProps({
    titulo: { type: String, default: "¿Estás seguro?" },
    mensaje: { type: String, default: "" },
    icono: { type: String, default: "⚠️" },
    textoConfirmar: { type: String, default: "Confirmar" },
    textoCancelar: { type: String, default: "Cancelar" },
    colorConfirmar: { type: String, default: "#dc3545" },
});

const emit = defineEmits(["confirmar", "cancelar"]);
const visible = ref(false);
const loading = ref(false);

// Exponer métodos para uso desde el padre
const abrir = () => {
    visible.value = true;
};
const cerrar = () => {
    visible.value = false;
    loading.value = false;
};
const confirmar = () => {
    loading.value = true;
    emit("confirmar", { cerrar });
};
const cancelar = () => {
    visible.value = false;
    emit("cancelar");
};

defineExpose({ abrir, cerrar });
</script>
