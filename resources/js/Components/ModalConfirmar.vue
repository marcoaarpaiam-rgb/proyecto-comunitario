<template>
    <div class="modal fade" :id="modalId" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-body text-center p-4">
                    <div class="mb-3" style="font-size: 2.5rem">
                        {{ icono }}
                    </div>
                    <h6 class="fw-bold mb-2">{{ titulo }}</h6>
                    <p class="text-muted small mb-0">{{ mensaje }}</p>
                </div>
                <div
                    class="modal-footer border-0 justify-content-center gap-2 pb-4"
                >
                    <button
                        type="button"
                        class="btn btn-secondary px-4"
                        data-bs-dismiss="modal"
                    >
                        Cancelar
                    </button>
                    <button
                        type="button"
                        class="btn px-4 fw-semibold"
                        :class="btnClase"
                        @click="confirmar"
                    >
                        {{ btnTexto }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { onMounted, ref } from "vue";
import { Modal } from "bootstrap";

const props = defineProps({
    modalId: { type: String, default: "modalConfirmar" },
    titulo: { type: String, default: "¿Estás seguro?" },
    mensaje: { type: String, default: "Esta acción no se puede deshacer." },
    icono: { type: String, default: "⚠️" },
    btnTexto: { type: String, default: "Confirmar" },
    btnClase: { type: String, default: "btn-danger" },
});

const emit = defineEmits(["confirmado"]);
let modal = null;

onMounted(() => {
    modal = new Modal(document.getElementById(props.modalId));
});

const abrir = () => modal?.show();
const confirmar = () => {
    modal?.hide();
    emit("confirmado");
};

defineExpose({ abrir });
</script>
