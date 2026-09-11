<template>
    <div
        class="toast-container position-fixed bottom-0 end-0 p-3"
        style="z-index: 1100"
    >
        <div
            v-for="toast in toasts"
            :key="toast.id"
            class="toast show align-items-center border-0 shadow mb-2"
            :class="toast.clase"
        >
            <div class="d-flex">
                <div class="toast-body d-flex align-items-center gap-2">
                    <i :class="toast.icono + ' fs-5'"></i>
                    <span>{{ toast.mensaje }}</span>
                </div>
                <button
                    type="button"
                    class="btn-close btn-close-white me-2 m-auto"
                    @click="quitar(toast.id)"
                ></button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const toasts = ref([]);

const agregar = (mensaje, tipo = "success") => {
    const configs = {
        success: {
            clase: "bg-success text-white",
            icono: "bi bi-check-circle-fill",
        },
        error: { clase: "bg-danger text-white", icono: "bi bi-x-circle-fill" },
        warning: {
            clase: "bg-warning text-dark",
            icono: "bi bi-exclamation-triangle-fill",
        },
        info: { clase: "bg-info text-white", icono: "bi bi-info-circle-fill" },
    };
    const id = Date.now();
    toasts.value.push({ id, mensaje, ...configs[tipo] });
    setTimeout(() => quitar(id), 4000);
};

const quitar = (id) => {
    toasts.value = toasts.value.filter((t) => t.id !== id);
};

defineExpose({ agregar });
</script>
