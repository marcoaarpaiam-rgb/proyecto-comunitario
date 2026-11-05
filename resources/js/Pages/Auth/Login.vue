<template>
    <div
        class="min-vh-100 d-flex align-items-center justify-content-center bg-light"
    >
        <div class="card shadow border-0 overflow-hidden" style="width: 420px; border-radius: 12px;">
            <div
                class="card-header text-white text-center py-4 border-0 position-relative"
                style="background-color: #1f3864"
            >
                <h4 class="mb-1 fw-bold">Sistema de Proyectos</h4>
                <small class="text-white-50">PNF Informática — UPTP Juan de Jesús Montilla</small>
            </div>
            <!-- Barra de progreso fluida y sutil -->
            <div v-if="loading" class="login-progress-bar"></div>
            <div v-else style="height: 3px; background: transparent;"></div>

            <div class="card-body p-4">
                <form @submit.prevent="submit">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Usuario</label>
                        <input
                            v-model="form.usu_username"
                            type="text"
                            class="form-control"
                            :class="{ 'is-invalid': errors.usu_username }"
                            placeholder="Ingresa tu usuario"
                            autofocus
                        />
                        <div
                            v-if="errors.usu_username"
                            class="invalid-feedback"
                        >
                            {{ errors.usu_username }}
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Contraseña</label>
                        <input
                            v-model="form.usu_password"
                            type="password"
                            class="form-control"
                            :class="{ 'is-invalid': errors.usu_password }"
                            placeholder="Ingresa tu contraseña"
                        />
                        <div
                            v-if="errors.usu_password"
                            class="invalid-feedback"
                        >
                            {{ errors.usu_password }}
                        </div>
                    </div>
                    <button
                        type="submit"
                        class="btn w-100 text-white fw-bold py-2"
                        style="background-color: #dc3545"
                        :disabled="loading"
                    >
                        <span
                            v-if="loading"
                            class="spinner-border spinner-border-sm me-2"
                        ></span>
                        {{ loading ? "Ingresando..." : "Ingresar al Sistema" }}
                    </button>
                </form>
            </div>
            <div
                class="card-footer text-center text-muted py-3 bg-white border-0"
                style="font-size: 0.8rem"
            >
                © 2026 UPTP Juan de Jesús Montilla
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";

const page = usePage();
const errors = ref(page.props.errors || {});

const form = ref({
    usu_username: "",
    usu_password: "",
});

const loading = ref(false);

const submit = () => {
    loading.value = true;
    errors.value = {};
    router.post(
        "/login",
        {
            usu_username: form.value.usu_username,
            usu_password: form.value.usu_password,
        },
        {
            onError: (e) => {
                errors.value = e;
                loading.value = false;
            },
            onFinish: () => {
                loading.value = false;
            },
        },
    );
};
</script>
