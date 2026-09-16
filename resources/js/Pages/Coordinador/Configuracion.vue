<template>
    <AppLayout page-title="Configuración">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Configuración</h4>
            <p class="text-muted small mb-0">
                Administra tu perfil y firma institucional
            </p>
        </div>

        <div class="row g-4">
            <!-- Perfil -->
            <div class="col-md-7">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-person-fill text-danger me-2"></i
                            >Mis Datos
                        </h6>
                    </div>
                    <form @submit.prevent="guardarPerfil">
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Primer Nombre</label
                                    >
                                    <input
                                        v-model="formPerfil.usu_primer_nombre"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                erroresPerfil.usu_primer_nombre,
                                        }"
                                    />
                                    <div
                                        v-if="erroresPerfil.usu_primer_nombre"
                                        class="invalid-feedback"
                                    >
                                        {{ erroresPerfil.usu_primer_nombre }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Segundo Nombre</label
                                    >
                                    <input
                                        v-model="formPerfil.usu_segundo_nombre"
                                        type="text"
                                        class="form-control"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Primer Apellido</label
                                    >
                                    <input
                                        v-model="formPerfil.usu_primer_apellido"
                                        type="text"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                erroresPerfil.usu_primer_apellido,
                                        }"
                                    />
                                    <div
                                        v-if="erroresPerfil.usu_primer_apellido"
                                        class="invalid-feedback"
                                    >
                                        {{ erroresPerfil.usu_primer_apellido }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Segundo Apellido</label
                                    >
                                    <input
                                        v-model="
                                            formPerfil.usu_segundo_apellido
                                        "
                                        type="text"
                                        class="form-control"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Correo Electrónico</label
                                    >
                                    <input
                                        v-model="formPerfil.usu_email"
                                        type="email"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                erroresPerfil.usu_email,
                                        }"
                                    />
                                    <div
                                        v-if="erroresPerfil.usu_email"
                                        class="invalid-feedback"
                                    >
                                        {{ erroresPerfil.usu_email }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Teléfono</label
                                    >
                                    <input
                                        v-model="formPerfil.usu_telefono"
                                        type="text"
                                        class="form-control"
                                    />
                                </div>
                            </div>
                        </div>
                        <div
                            class="card-footer bg-white border-0 text-end pb-3"
                        >
                            <button
                                type="submit"
                                class="btn btn-danger fw-semibold"
                                :disabled="loadingPerfil"
                            >
                                <span
                                    v-if="loadingPerfil"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Firma y Contraseña -->
            <div class="col-md-5">
                <!-- Firma -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-pen text-danger me-2"></i>Firma
                            Institucional
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            <div
                                v-if="coordinador.usu_firma_digital"
                                class="border rounded p-2 mb-2 bg-light"
                            >
                                <img
                                    :src="
                                        '/storage/' +
                                        coordinador.usu_firma_digital
                                    "
                                    alt="Firma actual"
                                    class="img-fluid"
                                    style="max-height: 80px"
                                />
                            </div>
                            <div v-else class="text-muted py-3 small">
                                <i class="bi bi-pen fs-2 d-block mb-1"></i>
                                Sin firma configurada
                            </div>
                        </div>
                        <form
                            @submit.prevent="subirFirma"
                            enctype="multipart/form-data"
                        >
                            <input
                                type="file"
                                class="form-control form-control-sm mb-2"
                                accept="image/png,image/jpeg"
                                @change="firmaFile = $event.target.files[0]"
                            />
                            <div class="form-text mb-2">
                                PNG o JPG con fondo blanco. Máx 2MB. Se usará en
                                todas las cartas de presentación.
                            </div>
                            <button
                                type="submit"
                                class="btn btn-sm btn-danger w-100"
                                :disabled="!firmaFile || loadingFirma"
                            >
                                <span
                                    v-if="loadingFirma"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                {{
                                    coordinador.usu_firma_digital
                                        ? "Actualizar Firma"
                                        : "Subir Firma"
                                }}
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Cambiar contraseña -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-3">
                        <h6 class="fw-bold mb-0">
                            <i class="bi bi-shield-lock text-danger me-2"></i
                            >Cambiar Contraseña
                        </h6>
                    </div>
                    <form @submit.prevent="cambiarPassword">
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold small"
                                    >Contraseña Actual</label
                                >
                                <input
                                    v-model="formPass.password_actual"
                                    type="password"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            erroresPass.password_actual,
                                    }"
                                />
                                <div
                                    v-if="erroresPass.password_actual"
                                    class="invalid-feedback"
                                >
                                    {{ erroresPass.password_actual }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small"
                                    >Nueva Contraseña</label
                                >
                                <input
                                    v-model="formPass.password_nuevo"
                                    type="password"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            erroresPass.password_nuevo,
                                    }"
                                />
                                <div
                                    v-if="erroresPass.password_nuevo"
                                    class="invalid-feedback"
                                >
                                    {{ erroresPass.password_nuevo }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small"
                                    >Confirmar Contraseña</label
                                >
                                <input
                                    v-model="
                                        formPass.password_nuevo_confirmation
                                    "
                                    type="password"
                                    class="form-control"
                                />
                            </div>
                            <button
                                type="submit"
                                class="btn btn-sm btn-danger w-100"
                                :disabled="loadingPass"
                            >
                                <span
                                    v-if="loadingPass"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                Cambiar Contraseña
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="card border-0 shadow-sm mt-4">
            <div class="card-header bg-white border-0 pt-3">
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-key text-danger me-2"></i>Resetear
                    Contraseña de Líder
                </h6>
            </div>
            <div class="card-body">
                <p class="text-muted small mb-3">
                    Resetea la contraseña del líder a su cédula como contraseña
                    inicial.
                </p>
                <div class="d-flex gap-2">
                    <select v-model="liderId" class="form-select">
                        <option value="">Seleccionar líder...</option>
                        <option
                            v-for="lid in lideres"
                            :key="lid.usu_id"
                            :value="lid.usu_id"
                        >
                            {{ lid.usu_primer_nombre }}
                            {{ lid.usu_primer_apellido }} ({{ lid.usu_cedula }})
                        </option>
                    </select>
                    <button
                        class="btn btn-danger fw-semibold"
                        :disabled="!liderId"
                        @click="resetLider"
                    >
                        <i class="bi bi-key me-1"></i>Resetear
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({ coordinador: Object });

const formPerfil = ref({
    usu_primer_nombre: props.coordinador.usu_primer_nombre,
    usu_segundo_nombre: props.coordinador.usu_segundo_nombre || "",
    usu_primer_apellido: props.coordinador.usu_primer_apellido,
    usu_segundo_apellido: props.coordinador.usu_segundo_apellido || "",
    usu_email: props.coordinador.usu_email || "",
    usu_telefono: props.coordinador.usu_telefono || "",
});

const formPass = ref({
    password_actual: "",
    password_nuevo: "",
    password_nuevo_confirmation: "",
});

const erroresPerfil = ref({});
const erroresPass = ref({});
const loadingPerfil = ref(false);
const loadingPass = ref(false);
const loadingFirma = ref(false);
const firmaFile = ref(null);

const guardarPerfil = () => {
    loadingPerfil.value = true;
    router.post("/coordinador/configuracion/perfil", formPerfil.value, {
        onError: (e) => {
            erroresPerfil.value = e;
            loadingPerfil.value = false;
        },
        onSuccess: () => {
            loadingPerfil.value = false;
        },
    });
};

const subirFirma = () => {
    if (!firmaFile.value) return;
    loadingFirma.value = true;
    const data = new FormData();
    data.append("firma", firmaFile.value);
    router.post("/coordinador/configuracion/firma", data, {
        forceFormData: true,
        onFinish: () => {
            loadingFirma.value = false;
        },
    });
};

const cambiarPassword = () => {
    loadingPass.value = true;
    router.post("/coordinador/configuracion/password", formPass.value, {
        onError: (e) => {
            erroresPass.value = e;
            loadingPass.value = false;
        },
        onSuccess: () => {
            loadingPass.value = false;
            formPass.value = {
                password_actual: "",
                password_nuevo: "",
                password_nuevo_confirmation: "",
            };
        },
    });
};
const props = defineProps({
    coordinador: Object,
    lideres: { type: Array, default: () => [] },
});
const liderId = ref("");
const resetLider = () => {
    if (!confirm("¿Resetear la contraseña de este líder a su cédula?")) return;
    router.post(
        "/coordinador/configuracion/reset-lider",
        { usu_id: liderId.value },
        {
            onSuccess: () => {
                liderId.value = "";
            },
        },
    );
};
</script>
