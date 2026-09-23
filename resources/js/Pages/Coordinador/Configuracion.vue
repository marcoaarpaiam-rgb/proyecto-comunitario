<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
            font-family: &quot;Segoe UI&quot;, system-ui, sans-serif;
        "
    >
        <div
            class="sb-overlay"
            :class="{ show: sidebarOpen }"
            @click="sidebarOpen = false"
        ></div>
        <div class="sb" :class="{ show: sidebarOpen }">
            <div class="sb-brand">
                <div
                    style="
                        width: 34px;
                        height: 34px;
                        background: #dc3545;
                        border-radius: 8px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #fff;
                        font-weight: 800;
                    "
                >
                    💻
                </div>
                <div>
                    <div class="sb-title">UPTP — Proyectos</div>
                    <div class="sb-sub">PNF Informática</div>
                </div>
                <button
                    class="btn btn-sm text-white ms-auto fs-5 p-0 d-lg-none"
                    @click="sidebarOpen = false"
                >
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="sb-user">
                <div class="sb-av">{{ iniciales }}</div>
                <div>
                    <div class="sb-name">
                        {{ $page.props.auth.user.usu_primer_nombre }}
                        {{ $page.props.auth.user.usu_primer_apellido }}
                    </div>
                    <div class="sb-role">Coordinador PNF</div>
                </div>
            </div>
            <div class="sb-nav">
                <div class="sb-sec">Principal</div>
                <a href="/coordinador" class="sb-lnk"
                    ><i class="bi bi-speedometer2"></i> Dashboard</a
                >
                <a href="/coordinador/secciones" class="sb-lnk"
                    ><i class="bi bi-collection"></i> Secciones</a
                >
                <a href="/coordinador/comunidades" class="sb-lnk"
                    ><i class="bi bi-building"></i> Comunidades</a
                >
                <a href="/coordinador/equipos" class="sb-lnk"
                    ><i class="bi bi-people"></i> Equipos</a
                >
                <a href="/coordinador/proyectos" class="sb-lnk"
                    ><i class="bi bi-folder"></i> Proyectos</a
                >
                <div class="sb-sec">Tablas Maestras</div>
                <a href="/coordinador/tipos-proyecto" class="sb-lnk"
                    ><i class="bi bi-tag"></i> Tipos de Proyecto</a
                >
                <a href="/coordinador/maestras/modalidades" class="sb-lnk"
                    ><i class="bi bi-layers"></i> Modalidades</a
                >
                <a href="/coordinador/maestras/tipos-jurado" class="sb-lnk"
                    ><i class="bi bi-person-badge"></i> Tipos de Jurado</a
                >
                <a
                    href="/coordinador/maestras/tipos-beneficiario"
                    class="sb-lnk"
                    ><i class="bi bi-people"></i> Tipos Beneficiario</a
                >
                <a href="/coordinador/maestras/tipos-entregable" class="sb-lnk"
                    ><i class="bi bi-file-earmark"></i> Tipos Entregable</a
                >
                <a
                    href="/coordinador/maestras/tipos-evento-equipo"
                    class="sb-lnk"
                    ><i class="bi bi-calendar-event"></i> Eventos Equipo</a
                >
                <a href="/coordinador/maestras/turnos" class="sb-lnk"
                    ><i class="bi bi-clock"></i> Turnos</a
                >
                <a href="/coordinador/maestras/estados-proyecto" class="sb-lnk"
                    ><i class="bi bi-award"></i> Estados Proyecto</a
                >
                <div class="sb-sec">Académico</div>
                <a href="/coordinador/socializaciones" class="sb-lnk"
                    ><i class="bi bi-mic"></i> Socializaciones</a
                >
                <a href="/coordinador/resultados" class="sb-lnk"
                    ><i class="bi bi-trophy"></i> Resultados</a
                >
                <div class="sb-sec">Sistema</div>
                <a href="/coordinador/reportes" class="sb-lnk"
                    ><i class="bi bi-bar-chart"></i> Reportes</a
                >
                <a href="/coordinador/cartas" class="sb-lnk"
                    ><i class="bi bi-file-earmark-text"></i> Cartas</a
                >
                <a href="/coordinador/bitacora" class="sb-lnk"
                    ><i class="bi bi-journal-text"></i> Bitácora</a
                >
                <a href="/coordinador/configuracion" class="sb-lnk active"
                    ><i class="bi bi-gear"></i> Configuración</a
                >
                <div style="padding: 0.75rem 1.4rem">
                    <form @submit.prevent="logout">
                        <button
                            type="submit"
                            class="sb-lnk w-100 text-start border-0 p-0"
                            style="
                                color: #ff8591;
                                font-weight: 700;
                                background: none;
                                font-size: 0.88rem;
                            "
                        >
                            <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <div class="main">
            <div class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <button
                        class="btn btn-outline-danger btn-sm toggle-btn px-2"
                        @click="sidebarOpen = true"
                    >
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div class="tb-title">
                        <i
                            class="bi bi-gear me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Configuración
                    </div>
                </div>
                <button class="tb-btn" @click="toggleTema">
                    <i
                        class="bi"
                        :class="tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'"
                    ></i>
                </button>
            </div>
            <div class="content">
                <div style="margin-bottom: 1.25rem">
                    <h4
                        style="
                            font-weight: 800;
                            font-size: 1.25rem;
                            color: var(--text-main);
                            margin: 0;
                        "
                    >
                        Configuración
                    </h4>
                    <p
                        style="
                            color: var(--text-muted);
                            font-size: 0.85rem;
                            margin-top: 2px;
                        "
                    >
                        Gestiona tu perfil, firma y seguridad
                    </p>
                </div>

                <div class="row g-3">
                    <!-- Perfil -->
                    <div class="col-lg-7">
                        <div class="sc">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-person-fill text-danger me-2"
                                    ></i
                                    >Mis Datos
                                </h6>
                            </div>
                            <form @submit.prevent="guardarPerfil">
                                <div class="p-3">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label
                                                class="form-label fw-semibold"
                                                >Primer Nombre</label
                                            >
                                            <input
                                                v-model="
                                                    formPerfil.usu_primer_nombre
                                                "
                                                type="text"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        erroresPerfil.usu_primer_nombre,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    erroresPerfil.usu_primer_nombre
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    erroresPerfil.usu_primer_nombre
                                                }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label
                                                class="form-label fw-semibold"
                                                >Segundo Nombre</label
                                            >
                                            <input
                                                v-model="
                                                    formPerfil.usu_segundo_nombre
                                                "
                                                type="text"
                                                class="form-control"
                                            />
                                        </div>
                                        <div class="col-md-6">
                                            <label
                                                class="form-label fw-semibold"
                                                >Primer Apellido</label
                                            >
                                            <input
                                                v-model="
                                                    formPerfil.usu_primer_apellido
                                                "
                                                type="text"
                                                class="form-control"
                                                :class="{
                                                    'is-invalid':
                                                        erroresPerfil.usu_primer_apellido,
                                                }"
                                            />
                                            <div
                                                v-if="
                                                    erroresPerfil.usu_primer_apellido
                                                "
                                                class="invalid-feedback"
                                            >
                                                {{
                                                    erroresPerfil.usu_primer_apellido
                                                }}
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <label
                                                class="form-label fw-semibold"
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
                                            <label
                                                class="form-label fw-semibold"
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
                                            <label
                                                class="form-label fw-semibold"
                                                >Teléfono</label
                                            >
                                            <input
                                                v-model="
                                                    formPerfil.usu_telefono
                                                "
                                                type="text"
                                                class="form-control"
                                            />
                                        </div>
                                    </div>
                                </div>
                                <div
                                    style="
                                        padding: 0.75rem 1rem;
                                        border-top: 1px solid var(--border);
                                        text-align: right;
                                    "
                                >
                                    <button
                                        type="submit"
                                        class="btn btn-danger fw-bold rounded-pill px-4"
                                        :disabled="loadingPerfil"
                                    >
                                        <span
                                            v-if="loadingPerfil"
                                            class="spinner-border spinner-border-sm me-2"
                                        ></span
                                        >Guardar Cambios
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Firma + Contraseña + Reset -->
                    <div class="col-lg-5">
                        <!-- Firma -->
                        <div class="sc mb-3">
                            <div class="sh">
                                <h6>
                                    <i class="bi bi-pen text-danger me-2"></i
                                    >Firma Institucional
                                </h6>
                            </div>
                            <div class="p-3">
                                <div
                                    v-if="coordinador.usu_firma_digital"
                                    class="mb-3 text-center p-2 rounded"
                                    style="
                                        border: 1px solid var(--border);
                                        background: var(--bg-th);
                                    "
                                >
                                    <img
                                        :src="
                                            '/storage/' +
                                            coordinador.usu_firma_digital
                                        "
                                        alt="Firma"
                                        class="img-fluid"
                                        style="max-height: 70px"
                                    />
                                </div>
                                <div
                                    v-else
                                    class="mb-3 text-center py-3"
                                    style="color: var(--text-muted)"
                                >
                                    <i class="bi bi-pen fs-2 d-block mb-1"></i
                                    ><small>Sin firma configurada</small>
                                </div>
                                <form
                                    @submit.prevent="subirFirma"
                                    enctype="multipart/form-data"
                                >
                                    <input
                                        type="file"
                                        class="form-control form-control-sm mb-2"
                                        accept="image/png,image/jpeg"
                                        @change="
                                            firmaFile = $event.target.files[0]
                                        "
                                    />
                                    <div
                                        style="
                                            font-size: 0.75rem;
                                            color: var(--text-muted);
                                            margin-bottom: 0.5rem;
                                        "
                                    >
                                        PNG o JPG con fondo blanco. Máx 2MB.
                                    </div>
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger w-100 fw-bold rounded-pill"
                                        :disabled="!firmaFile || loadingFirma"
                                    >
                                        <span
                                            v-if="loadingFirma"
                                            class="spinner-border spinner-border-sm me-1"
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

                        <!-- Contraseña -->
                        <div class="sc mb-3">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-shield-lock text-danger me-2"
                                    ></i
                                    >Cambiar Contraseña
                                </h6>
                            </div>
                            <form @submit.prevent="cambiarPassword">
                                <div class="p-3">
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold"
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
                                    <div class="mb-2">
                                        <label class="form-label fw-semibold"
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
                                        <label class="form-label fw-semibold"
                                            >Confirmar</label
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
                                        class="btn btn-sm btn-danger w-100 fw-bold rounded-pill"
                                        :disabled="loadingPass"
                                    >
                                        <span
                                            v-if="loadingPass"
                                            class="spinner-border spinner-border-sm me-1"
                                        ></span
                                        >Cambiar Contraseña
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Reset Líder -->
                        <div class="sc">
                            <div class="sh">
                                <h6>
                                    <i class="bi bi-key text-danger me-2"></i
                                    >Resetear Contraseña de Líder
                                </h6>
                            </div>
                            <div class="p-3">
                                <p
                                    style="
                                        font-size: 0.82rem;
                                        color: var(--text-muted);
                                        margin-bottom: 0.75rem;
                                    "
                                >
                                    Resetea la contraseña del líder a su cédula
                                    como contraseña inicial.
                                </p>
                                <div class="d-flex gap-2">
                                    <select
                                        v-model="liderId"
                                        class="form-select form-select-sm"
                                    >
                                        <option value="">
                                            Seleccionar líder...
                                        </option>
                                        <option
                                            v-for="lid in lideres"
                                            :key="lid.usu_id"
                                            :value="lid.usu_id"
                                        >
                                            {{ lid.usu_primer_nombre }}
                                            {{ lid.usu_primer_apellido }} ({{
                                                lid.usu_cedula
                                            }})
                                        </option>
                                    </select>
                                    <button
                                        class="btn btn-sm btn-danger fw-bold"
                                        :disabled="!liderId"
                                        @click="resetLider"
                                    >
                                        <i class="bi bi-key me-1"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
const props = defineProps({
    coordinador: Object,
    lideres: { type: Array, default: () => [] },
});
const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("coord-theme") || "light");
const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("coord-theme", tema.value);
};
const logout = () => router.post("/logout");
const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});
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
const liderId = ref("");
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
<style>
:root {
    --rojo: #dc3545;
    --bg-body: #f0f2f5;
    --bg-card: #fff;
    --bg-sidebar: #1a1d23;
    --bg-topbar: #fff;
    --border: #dee2e6;
    --text-main: #1a1a1a;
    --text-muted: #6c757d;
    --bg-th: #f8f9fa;
    --bg-hover: #fff8f8;
    --border-side: rgba(220, 53, 69, 0.25);
}
[data-theme="dark"] {
    --bg-body: #111317;
    --bg-card: #1e2128;
    --bg-sidebar: #0d0e11;
    --bg-topbar: #1e2128;
    --border: #2d3139;
    --text-main: #f1f3f5;
    --text-muted: #adb5bd;
    --bg-th: #252830;
    --bg-hover: #252830;
    --border-side: rgba(220, 53, 69, 0.5);
}
.sb {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    background: var(--bg-sidebar);
    display: flex;
    flex-direction: column;
    z-index: 1040;
    box-shadow: 3px 0 15px rgba(0, 0, 0, 0.15);
    border-right: 2px solid var(--border-side);
    transition: transform 0.3s;
}
.sb-brand {
    padding: 1.3rem 1.4rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    gap: 10px;
}
.sb-title {
    font-weight: 800;
    font-size: 1rem;
    color: #fff;
}
.sb-sub {
    font-size: 0.75rem;
    font-weight: 700;
    color: #ff4d5e;
}
.sb-user {
    padding: 1rem 1.4rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    display: flex;
    align-items: center;
    gap: 10px;
}
.sb-av {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #dc3545;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 800;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.sb-name {
    color: #fff;
    font-size: 0.88rem;
    font-weight: 700;
}
.sb-role {
    color: #94a3b8;
    font-size: 0.73rem;
    font-weight: 600;
    margin-top: 1px;
}
.sb-nav {
    flex: 1;
    overflow-y: auto;
    padding: 0.4rem 0;
    scrollbar-width: none;
}
.sb-nav::-webkit-scrollbar {
    display: none;
}
.sb-sec {
    padding: 0.8rem 1.4rem 0.25rem;
    color: #64748b;
    font-size: 0.7rem;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    font-weight: 700;
}
.sb-lnk {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.65rem 1.4rem;
    color: #cbd5e1;
    text-decoration: none;
    font-size: 0.88rem;
    font-weight: 500;
    border-left: 3px solid transparent;
    transition: all 0.15s;
}
.sb-lnk:hover {
    background: rgba(255, 255, 255, 0.06);
    color: #fff;
}
.sb-lnk.active {
    background: rgba(220, 53, 69, 0.18);
    color: #fff;
    border-left-color: #dc3545;
    font-weight: 700;
}
.sb-lnk i {
    font-size: 1rem;
    width: 20px;
    text-align: center;
}
.main {
    transition: margin-left 0.3s;
}
.topbar {
    background: var(--bg-topbar);
    padding: 0.85rem 1.4rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    position: sticky;
    top: 0;
    z-index: 1030;
    border-bottom: 2px solid rgba(220, 53, 69, 0.1);
    transition: background 0.3s;
}
.tb-title {
    font-weight: 800;
    color: var(--text-main);
    font-size: 1.15rem;
}
.tb-btn {
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1.3rem;
    cursor: pointer;
    padding: 4px;
    transition: color 0.2s;
}
.tb-btn:hover {
    color: #dc3545;
}
.content {
    padding: 1.4rem 1.25rem;
}
.sc {
    background: var(--bg-card);
    border-radius: 13px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--border);
    overflow: hidden;
    transition:
        background 0.3s,
        border-color 0.3s;
    margin-bottom: 1.2rem;
}
.sh {
    padding: 1rem 1.3rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 8px;
}
.sh h6 {
    margin: 0;
    font-weight: 800;
    color: var(--text-main);
    font-size: 0.97rem;
}
.form-control,
.form-select {
    background: var(--bg-card) !important;
    color: var(--text-main) !important;
    border-color: var(--border) !important;
}
.form-label {
    color: var(--text-main);
    font-size: 0.88rem;
}
.modal-content {
    background: var(--bg-card) !important;
}
.sb-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1035;
    display: none;
}
@media (min-width: 992px) {
    .sb {
        transform: translateX(0) !important;
    }
    .main {
        margin-left: 250px;
    }
    .toggle-btn {
        display: none !important;
    }
}
@media (max-width: 991.98px) {
    .sb {
        transform: translateX(-100%);
    }
    .main {
        margin-left: 0;
    }
    .sb.show {
        transform: translateX(0);
    }
    .sb-overlay.show {
        display: block !important;
    }
    .content {
        padding: 1rem;
    }
}
</style>
