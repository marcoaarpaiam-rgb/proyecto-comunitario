<template>
    <div
        v-if="debeCambiar"
        class="alert alert-warning border-start border-4 border-warning shadow-sm d-flex align-items-center justify-content-between flex-wrap gap-3 mb-3 p-3 rounded-3"
        style="background: #fffdf5; border-color: #f59e0b !important;"
        role="alert"
    >
        <div class="d-flex align-items-center gap-3">
            <div class="p-2 bg-warning bg-opacity-25 rounded-circle text-warning flex-shrink-0 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                <i class="bi bi-shield-lock-fill fs-4 text-dark"></i>
            </div>
            <div>
                <div class="fw-bold text-dark d-flex align-items-center gap-2 flex-wrap">
                    <span>Cambio Obligatorio de Contraseña</span>
                    <span class="badge bg-danger">Primer Acceso</span>
                </div>
                <div class="small text-muted mt-1">
                    Por seguridad institucional, debes actualizar tu clave de acceso temporal (actualmente es tu número de cédula). No utilices tu cédula como nueva clave.
                </div>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2 flex-shrink-0">
            <button
                type="button"
                class="btn btn-sm btn-danger fw-bold rounded-pill px-3 shadow-sm d-inline-flex align-items-center gap-1"
                @click="abrirModalClave"
            >
                <i class="bi bi-key-fill"></i>
                <span>Actualizar Contraseña Ahora</span>
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

const debeCambiar = computed(() => {
    return Boolean(
        page.props.auth?.debeCambiarPassword ||
        page.props.auth?.user?.debe_cambiar_password
    );
});

const abrirModalClave = () => {
    window.dispatchEvent(new CustomEvent("abrir-modal-cambio-clave"));
};
</script>
