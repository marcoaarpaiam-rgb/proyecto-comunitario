<template>
    <div
        v-if="visible"
        class="siscon-feedback-container"
        role="alert"
        aria-live="assertive"
    >
        <div
            class="siscon-feedback-card shadow-lg border-0"
            :class="tipo === 'success' ? 'siscon-feedback-success' : 'siscon-feedback-error'"
        >
            <div class="d-flex align-items-start gap-3">
                <div class="siscon-feedback-icon">
                    <i
                        class="bi fs-4"
                        :class="tipo === 'success' ? 'bi-check-circle-fill text-success' : 'bi-exclamation-triangle-fill text-danger'"
                    ></i>
                </div>
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-1">
                        <strong class="siscon-feedback-title">
                            {{ titulo }}
                        </strong>
                        <button
                            type="button"
                            class="btn-close btn-sm"
                            aria-label="Cerrar"
                            @click="cerrar"
                        ></button>
                    </div>
                    <p v-if="mensaje" class="mb-1 siscon-feedback-msg">
                        {{ mensaje }}
                    </p>
                    <ul v-if="detalles.length > 0" class="mb-0 ps-3 siscon-feedback-list">
                        <li v-for="(det, idx) in detalles" :key="idx">
                            {{ det }}
                        </li>
                    </ul>
                </div>
            </div>
            <div v-if="tipo === 'success'" class="siscon-progress-bar"></div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { router } from "@inertiajs/vue3";

const visible = ref(false);
const tipo = ref("success"); // 'success' | 'error'
const titulo = ref("");
const mensaje = ref("");
const detalles = ref([]);
let timer = null;
let lastSuccessMsg = "";
let lastSuccessTime = 0;

const cerrar = () => {
    visible.value = false;
    if (timer) {
        clearTimeout(timer);
        timer = null;
    }
};

const mostrar = ({ type = "success", title = "", message = "", details = [], autoClose = true }) => {
    if (timer) clearTimeout(timer);
    tipo.value = type;
    titulo.value = title || (type === "success" ? "Operación Exitosa" : "Atención — Error de Validación");
    mensaje.value = message;
    detalles.value = details;
    visible.value = true;

    if (autoClose) {
        timer = setTimeout(() => {
            visible.value = false;
        }, type === "success" ? 5000 : 9000);
    }
};

let removeSuccessListener = null;
let removeErrorListener = null;
let removeInvalidListener = null;

const onCustomFeedback = (ev) => {
    if (!ev?.detail) return;
    mostrar(ev.detail);
};

onMounted(() => {
    window.addEventListener("siscon:feedback", onCustomFeedback);

    removeSuccessListener = router.on("success", (event) => {
        const flash = event?.detail?.page?.props?.flash || {};
        const now = Date.now();
        if (flash.success) {
            if (flash.success !== lastSuccessMsg || now - lastSuccessTime > 800) {
                lastSuccessMsg = flash.success;
                lastSuccessTime = now;
                mostrar({
                    type: "success",
                    title: "Acción completada correctamente",
                    message: flash.success,
                    details: [],
                    autoClose: true,
                });
            }
        } else if (flash.error) {
            mostrar({
                type: "error",
                title: "No se pudo completar la acción",
                message: flash.error,
                details: [],
                autoClose: true,
            });
        }
    });

    removeErrorListener = router.on("error", (event) => {
        const errors = event?.detail?.errors || {};
        const lista = [];
        Object.entries(errors).forEach(([campo, val]) => {
            if (Array.isArray(val)) {
                val.forEach((v) => lista.push(v));
            } else if (val) {
                lista.push(String(val));
            }
        });
        if (lista.length > 0) {
            mostrar({
                type: "error",
                title: "Verifique los campos del formulario",
                message: "Se detectaron los siguientes inconvenientes:",
                details: lista,
                autoClose: true,
            });
        }
    });

    removeInvalidListener = router.on("invalid", (event) => {
        const status = event?.detail?.response?.status;
        if (status && status >= 400) {
            mostrar({
                type: "error",
                title: `Error de servidor (${status})`,
                message: "La respuesta del servidor no tuvo el formato esperado. Intente recargar la página o verifique su sesión.",
                details: [],
                autoClose: false,
            });
        }
    });
});

onUnmounted(() => {
    window.removeEventListener("siscon:feedback", onCustomFeedback);
    if (typeof removeSuccessListener === "function") removeSuccessListener();
    if (typeof removeErrorListener === "function") removeErrorListener();
    if (typeof removeInvalidListener === "function") removeInvalidListener();
    if (timer) clearTimeout(timer);
});
</script>

<style scoped>
.siscon-feedback-container {
    position: fixed;
    top: 1.25rem;
    right: 1.25rem;
    z-index: 10990;
    max-width: 440px;
    width: calc(100vw - 2.5rem);
    pointer-events: auto;
    animation: sisconSlideIn 0.25s ease-out;
}

.siscon-feedback-card {
    background: #ffffff;
    color: #1e293b;
    border-radius: 14px;
    padding: 1rem 1.15rem;
    position: relative;
    overflow: hidden;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.22) !important;
}

:global([data-theme="dark"]) .siscon-feedback-card {
    background: #1e293b;
    color: #f1f5f9;
}

.siscon-feedback-success {
    border-left: 5px solid #198754 !important;
}

.siscon-feedback-error {
    border-left: 5px solid #dc3545 !important;
}

.siscon-feedback-title {
    font-size: 0.95rem;
}

.siscon-feedback-msg {
    font-size: 0.87rem;
    line-height: 1.4;
}

.siscon-feedback-list {
    font-size: 0.84rem;
    color: #b91c1c;
    font-weight: 500;
}

:global([data-theme="dark"]) .siscon-feedback-list {
    color: #fca5a5;
}

.siscon-progress-bar {
    position: absolute;
    bottom: 0;
    left: 0;
    height: 3px;
    background: #198754;
    width: 100%;
    animation: sisconProgress 5s linear forwards;
}

@keyframes sisconSlideIn {
    from {
        opacity: 0;
        transform: translateY(-14px) scale(0.97);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes sisconProgress {
    from { width: 100%; }
    to { width: 0%; }
}
</style>
