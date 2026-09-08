<template>
    <div
        class="min-vh-100 d-flex align-items-center justify-content-center bg-light"
    >
        <div class="card shadow" style="width: 420px">
            <div
                class="card-header text-white text-center py-4"
                style="background-color: #1f3864"
            >
                <h4 class="mb-1 fw-bold">Sistema de Proyectos</h4>
                <small>PNF Informática — UPTP Juan de Jesús Montilla</small>
            </div>
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
                class="card-footer text-center text-muted py-3"
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
    router.post("/login", form.value, {
        onError: (e) => {
            errors.value = e;
            loading.value = false;
        },
        onFinish: () => {
            loading.value = false;
        },
    });
};
</script>
