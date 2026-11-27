<template>
    <div>
        <!-- Modal Cambiar Contraseña -->
        <div class="modal fade" id="mCambiarClaveLider" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <form @submit.prevent="guardar" class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>
                            <i class="bi bi-shield-lock-fill text-danger me-2"></i>
                            Cambiar Contraseña de Acceso
                        </h5>
                        <button type="button" class="btn btn-sm text-white p-0 fs-5" data-bs-dismiss="modal" aria-label="Cerrar">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div v-if="Object.keys(errores).length > 0" class="alert alert-danger py-2 small mb-3">
                            <div v-for="(msg, k) in errores" :key="k">{{ msg }}</div>
                        </div>
                        <p class="small text-muted mb-3">
                            Si es tu primer ingreso al sistema, tu contraseña actual es tu número de cédula.
                        </p>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Contraseña Actual *</label>
                            <input
                                v-model="form.password_actual"
                                type="password"
                                class="form-control"
                                required
                                placeholder="Ingresa tu contraseña actual o cédula..."
                            />
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Nueva Contraseña (mínimo 6 caracteres) *</label>
                            <input
                                v-model="form.password"
                                type="password"
                                class="form-control"
                                minlength="6"
                                required
                                placeholder="Escribe tu nueva contraseña..."
                            />
                        </div>
                        <div class="mb-2">
                            <label class="form-label small fw-semibold">Confirmar Nueva Contraseña *</label>
                            <input
                                v-model="form.password_confirmation"
                                type="password"
                                class="form-control"
                                minlength="6"
                                required
                                placeholder="Repite la nueva contraseña..."
                            />
                        </div>
                    </div>
                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-3" data-bs-dismiss="modal">
                            Cancelar
                        </button>
                        <button type="submit" class="btn btn-danger fw-bold rounded-pill px-4" :disabled="cargando">
                            <span v-if="cargando" class="spinner-border spinner-border-sm me-1"></span>
                            Guardar Nueva Contraseña
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Modal } from "bootstrap";

const page = usePage();
const debeCambiar = computed(() => Boolean(page.props.auth?.debeCambiarPassword || page.props.auth?.user?.debe_cambiar_password));
const cargando = ref(false);
const errores = ref({});
let modalInst = null;

const form = ref({
    password_actual: "",
    password: "",
    password_confirmation: "",
});

const abrir = () => {
    errores.value = {};
    form.value = {
        password_actual: "",
        password: "",
        password_confirmation: "",
    };
    const el = document.getElementById("mCambiarClaveLider");
    if (el) {
        modalInst = Modal.getInstance(el) || new Modal(el);
        modalInst.show();
    }
};

const guardar = () => {
    cargando.value = true;
    errores.value = {};
    router.post("/lider/cambiar-password", form.value, {
        preserveScroll: true,
        onSuccess: () => {
            cargando.value = false;
            modalInst?.hide();
        },
        onError: (e) => {
            cargando.value = false;
            errores.value = e;
        },
    });
};

onMounted(() => {
    window.addEventListener("abrir-modal-cambio-clave", abrir);
    if (debeCambiar.value && !sessionStorage.getItem("lider_clave_modal_visto")) {
        sessionStorage.setItem("lider_clave_modal_visto", "1");
        setTimeout(() => abrir(), 400);
    }
});

onUnmounted(() => {
    window.removeEventListener("abrir-modal-cambio-clave", abrir);
});

defineExpose({ abrir });
</script>

<style scoped>
.mh {
    background: #1a1d23;
    border-bottom: 2px solid #dc3545;
    padding: 1rem 1.3rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mh h5 {
    color: #fff;
    font-weight: 800;
    margin: 0;
    font-size: 1rem;
    display: flex;
    align-items: center;
}
.modal-content,
.modal-body,
.modal-footer {
    background: var(--bg-card, #ffffff);
    color: var(--text-main, #1a1a1a);
    border-color: var(--border, #dee2e6) !important;
}
.form-control {
    background: var(--bg-card, #ffffff);
    color: var(--text-main, #1a1a1a);
    border-color: var(--border, #dee2e6);
}
</style>
