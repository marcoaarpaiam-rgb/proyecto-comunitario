<template>
    <div
        class="login-page"
        :data-theme="theme"
        :data-bs-theme="theme"
    >
        <!-- ── BARRA SUPERIOR INSTITUCIONAL ── -->
        <div class="topbar-institution bg-dark text-white py-1">
            <div class="container d-flex justify-content-between align-items-center small">
                <span>
                    <i class="bi bi-geo-alt-fill text-danger me-2"></i>Acarigua, Portuguesa
                </span>
                <div class="d-none d-sm-flex align-items-center">
                    <span class="me-3">
                        <i class="bi bi-envelope-fill text-danger me-1"></i>contacto@uptp.edu.ve
                    </span>
                    <span>
                        <i class="bi bi-clock-fill text-danger me-1"></i>Lun - Vie: 8:00 AM - 4:00 PM
                    </span>
                </div>
            </div>
        </div>

        <!-- ── HEADER ── -->
        <header class="login-header py-3 border-bottom shadow-sm">
            <div class="container position-relative">
                <div class="d-flex align-items-center justify-content-between">
                    <!-- Acción izquierda -->
                    <div class="header-action-col d-none d-md-flex align-items-center">
                        <Link href="/home" class="btn btn-outline-secondary btn-sm rounded-pill px-3 header-back-btn">
                            <i class="bi bi-arrow-left me-1"></i>Volver al Inicio
                        </Link>
                    </div>

                    <!-- Logo de la institución CENTRADO -->
                    <div class="header-logo-center text-center mx-auto">
                        <Link href="/home" class="text-decoration-none d-inline-flex align-items-center justify-content-center">
                            <img
                                :src="'/images/images.png'"
                                alt="Logo UPTP"
                                style="height: 60px; object-fit: contain;"
                            />
                            <div class="ms-3 text-start">
                                <span class="d-block fw-bold fs-4 header-brand-text lh-1">UPTP</span>
                                <small class="header-brand-sub fw-semibold" style="font-size: 0.85rem;">Juan de Jesús Montilla</small>
                            </div>
                        </Link>
                    </div>

                    <!-- Acción derecha -->
                    <div class="header-action-col d-none d-md-flex align-items-center justify-content-end">
                        <a
                            href="https://uptp.sytes.net/daece/"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-outline-danger btn-sm fw-bold rounded-pill px-3"
                        >
                            <i class="bi bi-box-arrow-up-right me-1"></i>Ir al SOGAC
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <!-- ── NAVBAR ── -->
        <nav class="navbar navbar-expand-md navbar-dark bg-danger sticky-top shadow-sm py-2">
            <div class="container d-flex justify-content-between align-items-center">
                <span class="navbar-brand fw-semibold fs-6 mb-0 d-flex align-items-center">
                    <i class="bi bi-pc-display-horizontal me-2 fs-5"></i>
                    <span class="d-none d-sm-inline">Sistema de Gestión de Proyectos Comunitarios</span>
                    <span class="d-inline d-sm-none">Gestión de Proyectos</span>
                </span>
                <div class="d-flex align-items-center gap-2">
                    <!-- Botón de cambio de Modo Claro / Modo Oscuro -->
                    <button
                        type="button"
                        class="btn btn-outline-light btn-sm rounded-pill px-3 d-flex align-items-center gap-1 shadow-sm"
                        @click="toggleTheme"
                        :title="theme === 'dark' ? 'Cambiar a modo claro' : 'Cambiar a modo oscuro'"
                    >
                        <i class="bi" :class="theme === 'dark' ? 'bi-sun-fill text-warning' : 'bi-moon-stars-fill'"></i>
                        <span class="small fw-semibold">{{ theme === 'dark' ? 'Modo Claro' : 'Modo Oscuro' }}</span>
                    </button>

                    <Link href="/home" class="btn btn-outline-light btn-sm rounded-pill px-3">
                        <i class="bi bi-house-door me-1"></i>Inicio
                    </Link>
                </div>
            </div>
        </nav>

        <!-- ── MAIN (Centrado con fondo institucional y overlay elegante) ── -->
        <main class="login-wrapper">
            <div class="login-card-container">
                <div class="login-card">
                    <div class="card-body-content">
                        <!-- Logo & Universidad -->
                        <div class="logo-area">
                            <img
                                :src="'/images/images.png'"
                                alt="Logo UPTP"
                                class="logo-img"
                            />
                            <h5 class="logo-title">UPTP <span class="logo-subtitle fw-normal">Juan de Jesús Montilla</span></h5>
                            <div>
                                <span class="badge-sistema">
                                    <i class="bi bi-code-slash me-1"></i>Coordinación de Informática
                                </span>
                            </div>
                        </div>

                        <div class="text-start mb-3">
                            <h6 class="login-title">Iniciar Sesión</h6>
                            <p class="login-subtitle">Ingresa tus credenciales para acceder al sistema</p>
                        </div>

                        <!-- Alerta de Error -->
                        <div
                            v-if="errorMessage || errors.usu_username || errors.usu_password"
                            class="alert alert-danger d-flex align-items-start gap-2 py-2 px-3 mb-3 border-danger-subtle rounded-3"
                            role="alert"
                        >
                            <i class="bi bi-exclamation-triangle-fill text-danger fs-6 mt-1 flex-shrink-0"></i>
                            <div style="font-size: 0.85rem; line-height: 1.4;">
                                {{ errorMessage || errors.usu_username || errors.usu_password }}
                            </div>
                        </div>

                        <!-- Formulario -->
                        <form @submit.prevent="submit" novalidate>
                            <div class="mb-3 text-start">
                                <label class="form-label" for="cedula">
                                    <i class="bi bi-person-badge me-1 text-danger"></i>Cédula de Identidad o Usuario
                                </label>
                                <div class="input-with-icon">
                                    <input
                                        id="cedula"
                                        v-model="form.usu_username"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors.usu_username }"
                                        placeholder="Ej. 12345678"
                                        autofocus
                                        required
                                        @input="limpiarErrores"
                                    />
                                </div>
                            </div>

                            <div class="mb-3 text-start">
                                <label class="form-label" for="password">
                                    <i class="bi bi-lock me-1 text-danger"></i>Contraseña
                                </label>
                                <div class="input-group">
                                    <input
                                        id="password"
                                        v-model="form.usu_password"
                                        :type="showPassword ? 'text' : 'password'"
                                        class="form-control"
                                        :class="{ 'is-invalid': errors.usu_password }"
                                        placeholder="Ingresa tu contraseña"
                                        required
                                        @input="limpiarErrores"
                                    />
                                    <button
                                        class="btn btn-eye"
                                        type="button"
                                        @click="showPassword = !showPassword"
                                        :title="showPassword ? 'Ocultar contraseña' : 'Ver contraseña'"
                                    >
                                        <i class="bi" :class="showPassword ? 'bi-eye-slash-fill' : 'bi-eye-fill'"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <div class="form-check text-start">
                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        id="recordar"
                                        v-model="form.recordar"
                                    />
                                    <label class="form-check-label check-recordar" for="recordar" style="font-size: 0.82rem; cursor: pointer;">
                                        Recordarme
                                    </label>
                                </div>
                                <a
                                    href="#"
                                    class="forgot-link"
                                    @click.prevent="modalOlvido = true"
                                >
                                    ¿Olvidaste tu contraseña?
                                </a>
                            </div>

                            <button
                                type="submit"
                                class="btn-login"
                                :disabled="loading"
                            >
                                <span
                                    v-if="loading"
                                    class="spinner-border spinner-border-sm me-2"
                                    role="status"
                                    aria-hidden="true"
                                ></span>
                                <i v-else class="bi bi-box-arrow-in-right me-2"></i>
                                {{ loading ? "Verificando..." : "Ingresar al Sistema" }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </main>

        <!-- ── FOOTER INSTITUCIONAL ── -->
        <footer id="contacto" class="bg-dark text-white pt-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <img
                            :src="'/images/images.png'"
                            alt="Logo UPTP"
                            height="65"
                            class="mb-3"
                            style="filter: drop-shadow(0 2px 4px rgba(0,0,0,0.4));"
                        />
                        <p class="small text-secondary mb-3" style="line-height: 1.6;">
                            UPTP "Juan de Jesús Montilla". Impulsando la soberanía tecnológica, el desarrollo comunitario
                            y la excelencia académica en el estado Portuguesa.
                        </p>
                        <div class="d-flex gap-3 fs-5">
                            <a
                                href="https://www.instagram.com/uptpjjmontillaacarigua/"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="social-icon"
                                title="Instagram"
                            >
                                <i class="bi bi-instagram"></i>
                            </a>
                            <a
                                href="https://www.facebook.com/profile.php?id=100064113455547"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="social-icon"
                                title="Facebook"
                            >
                                <i class="bi bi-facebook"></i>
                            </a>
                            <a
                                href="https://x.com/UptpJuandeJesus/"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="social-icon"
                                title="X (Twitter)"
                            >
                                <i class="bi bi-twitter-x"></i>
                            </a>
                            <a
                                href="https://www.youtube.com/@educaciondigital-uptpjjmon6898"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="social-icon"
                                title="YouTube"
                            >
                                <i class="bi bi-youtube"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <h5 class="fw-bold mb-3 border-bottom border-danger border-2 d-inline-block pb-1">
                            Navegación
                        </h5>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <Link href="/home" class="text-secondary text-decoration-none small footer-link">
                                    <i class="bi bi-chevron-right me-1 text-danger"></i>Inicio
                                </Link>
                            </li>
                            <li class="mb-2">
                                <Link href="/home#nosotros" class="text-secondary text-decoration-none small footer-link">
                                    <i class="bi bi-chevron-right me-1 text-danger"></i>Nuestra Universidad
                                </Link>
                            </li>
                            <li class="mb-2">
                                <Link href="/home#programas" class="text-secondary text-decoration-none small footer-link">
                                    <i class="bi bi-chevron-right me-1 text-danger"></i>PNF en Informática
                                </Link>
                            </li>
                            <li class="mb-2">
                                <a href="https://uptp.sytes.net/daece/" target="_blank" rel="noopener noreferrer" class="text-secondary text-decoration-none small footer-link">
                                    <i class="bi bi-chevron-right me-1 text-danger"></i>Portal SOGAC (DAECE)
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-lg-4 col-md-12">
                        <h5 class="fw-bold mb-3 border-bottom border-danger border-2 d-inline-block pb-1">
                            Contacto Directo
                        </h5>
                        <ul class="list-unstyled small text-secondary mb-0">
                            <li class="mb-3 d-flex align-items-start">
                                <i class="bi bi-geo-alt-fill text-danger me-2 fs-5 mt-n1"></i>
                                <span>Av. Circunvalación, Sector Bellas Artes, Acarigua, Edo. Portuguesa.</span>
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i class="bi bi-envelope-fill text-danger me-2 fs-5"></i>
                                <a href="mailto:contacto@uptp.edu.ve" class="text-secondary text-decoration-none">
                                    contacto@uptp.edu.ve
                                </a>
                            </li>
                            <li class="d-flex align-items-center">
                                <i class="bi bi-clock-fill text-danger me-2 fs-5"></i>
                                <span>Atención: 8:00 AM - 4:00 PM</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="bg-black py-3 mt-5">
                <div class="container text-center">
                    <p class="mb-0 text-secondary" style="font-size: 0.78rem;">
                        &copy; 2026 UPTP "Juan de Jesús Montilla" &bull; Desarrollado por el Grupo 1 — Trayecto 3 Sección 331
                    </p>
                </div>
            </div>
        </footer>

        <!-- ── MODAL RECUPERACIÓN DE CONTRASEÑA ── -->
        <div
            v-if="modalOlvido"
            class="modal-backdrop-custom"
            @click.self="modalOlvido = false"
        >
            <div class="modal-dialog-custom">
                <div class="modal-content-custom">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-circle bg-danger text-white">
                                <i class="bi bi-key-fill"></i>
                            </div>
                            <h5 class="modal-title fw-bold mb-0">Recuperar Contraseña</h5>
                        </div>
                        <button
                            type="button"
                            class="btn-close-custom"
                            @click="modalOlvido = false"
                        >
                            &times;
                        </button>
                    </div>
                    <p class="modal-desc small mb-3">
                        Por motivos de seguridad institucional, el restablecimiento de contraseñas se gestiona a través de la coordinación académica:
                    </p>
                    <div class="info-box-modal mb-3">
                        <div class="d-flex align-items-start gap-2 mb-2">
                            <i class="bi bi-check-circle-fill text-danger mt-1"></i>
                            <div>
                                <strong class="info-box-title">Estudiantes y Líderes de Equipo:</strong>
                                <p class="info-box-desc small mb-0">
                                    Acude con tu profesor de proyecto o a la Coordinación del PNF Informática con tu cédula de identidad laminada.
                                </p>
                            </div>
                        </div>
                        <div class="d-flex align-items-start gap-2">
                            <i class="bi bi-check-circle-fill text-danger mt-1"></i>
                            <div>
                                <strong class="info-box-title">Profesores y Jurados:</strong>
                                <p class="info-box-desc small mb-0">
                                    Comunícate directamente con la Coordinación de Proyecto o soporte DAECE para reactivar tu cuenta.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="text-end">
                        <button
                            type="button"
                            class="btn btn-danger btn-sm px-4 rounded-pill fw-bold"
                            @click="modalOlvido = false"
                        >
                            Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const page = usePage();
const errors = ref(page.props.errors || {});
const errorMessage = ref("");
const showPassword = ref(false);
const loading = ref(false);
const modalOlvido = ref(false);

const theme = ref("light");

const aplicarTema = (nuevoTema) => {
    theme.value = nuevoTema;
    localStorage.setItem("login-theme", nuevoTema);
    localStorage.setItem("theme", nuevoTema);
    document.documentElement.setAttribute("data-bs-theme", nuevoTema);
    document.documentElement.setAttribute("data-theme", nuevoTema);
    if (document.body) {
        document.body.setAttribute("data-bs-theme", nuevoTema);
        document.body.setAttribute("data-theme", nuevoTema);
    }
};

const toggleTheme = () => {
    aplicarTema(theme.value === "dark" ? "light" : "dark");
};

onMounted(() => {
    const saved = localStorage.getItem("login-theme") || localStorage.getItem("theme") || "light";
    aplicarTema(saved);
});

const form = ref({
    usu_username: "",
    usu_password: "",
    recordar: false,
});

const limpiarErrores = () => {
    errors.value = {};
    errorMessage.value = "";
};

const submit = () => {
    errorMessage.value = "";
    errors.value = {};

    const username = form.value.usu_username.trim();
    const password = form.value.usu_password.trim();

    if (!username) {
        errorMessage.value = "Por favor, ingresa tu cédula de identidad o usuario.";
        return;
    }
    if (!password) {
        errorMessage.value = "Por favor, ingresa tu contraseña institucional.";
        return;
    }

    loading.value = true;

    router.post(
        "/login",
        {
            usu_username: username,
            usu_password: password,
            recordar: form.value.recordar,
        },
        {
            onError: (errs) => {
                errors.value = errs;
                errorMessage.value = errs.usu_username || errs.usu_password || "Cédula o contraseña incorrectas. Intenta de nuevo.";
                loading.value = false;
            },
            onFinish: () => {
                loading.value = false;
            },
        }
    );
};
</script>

<style scoped>
/* ── VARIABLES POR TEMA ── */
.login-page {
    --login-bg: #f8f9fa;
    --login-header-bg: #ffffff;
    --login-header-border: #dee2e6;
    --login-card-bg: #ffffff;
    --login-card-border: rgba(0, 0, 0, 0.08);
    --login-text-title: #1a1a1a;
    --login-text-sub: #6c757d;
    --login-label: #212529;
    --login-input-bg: #ffffff;
    --login-input-border: #dee2e6;
    --login-input-text: #212529;
    --login-btn-eye-bg: #ffffff;
    --login-btn-eye-text: #6c757d;
    --login-modal-bg: #ffffff;
    --login-modal-box: #f8f9fa;
    --login-modal-box-border: #e9ecef;

    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background-color: var(--login-bg);
    transition: background-color 0.25s ease, color 0.25s ease;
}

/* ── SOBREESCRITURA COMPLETA MODO OSCURO ── */
.login-page[data-theme="dark"],
.login-page[data-bs-theme="dark"] {
    --login-bg: #0d0f13;
    --login-header-bg: #16191f;
    --login-header-border: #2d333f;
    --login-card-bg: #16191f;
    --login-card-border: rgba(255, 255, 255, 0.12);
    --login-text-title: #f1f3f5;
    --login-text-sub: #cbd5e1;
    --login-label: #f1f3f5;
    --login-input-bg: #0f1115;
    --login-input-border: #3b4252;
    --login-input-text: #ffffff;
    --login-btn-eye-bg: #1f232b;
    --login-btn-eye-text: #cbd5e1;
    --login-modal-bg: #16191f;
    --login-modal-box: #0f1115;
    --login-modal-box-border: #2d333f;
}

/* ── BARRA SUPERIOR ── */
.topbar-institution {
    font-size: 0.82rem;
    letter-spacing: 0.2px;
}

/* ── HEADER ── */
.login-header {
    background-color: var(--login-header-bg) !important;
    border-bottom: 1px solid var(--login-header-border) !important;
    transition: background-color 0.25s ease, border-color 0.25s ease;
}
.header-action-col {
    flex: 1 1 0;
}
.header-logo-center {
    flex: 0 0 auto;
}
.header-brand-text {
    color: var(--login-text-title) !important;
}
.header-brand-sub {
    color: var(--login-text-sub) !important;
}
.header-back-btn {
    color: var(--login-text-title) !important;
    border-color: var(--login-header-border) !important;
}
.header-back-btn:hover {
    background-color: rgba(220, 53, 69, 0.1) !important;
    color: #dc3545 !important;
    border-color: #dc3545 !important;
}

@media (max-width: 767.98px) {
    .header-logo-center {
        width: 100%;
    }
}

/* ── MAIN LAYOUT ── */
.login-wrapper {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 3.5rem 1.25rem;
    background-image: url('/images/ingenieria.jpg');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    position: relative;
}

/* Overlay semitransparente oscuro de alto contraste */
.login-wrapper::before {
    content: "";
    position: absolute;
    inset: 0;
    background: linear-gradient(135deg, rgba(15, 23, 42, 0.82) 0%, rgba(30, 41, 59, 0.82) 50%, rgba(220, 53, 69, 0.40) 100%);
    backdrop-filter: blur(2px);
    z-index: 1;
}

.login-card-container {
    position: relative;
    z-index: 2;
    width: 100%;
    max-width: 450px;
    animation: fadeInUp 0.4s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(18px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ── TARJETA DE LOGIN ── */
.login-card {
    background-color: var(--login-card-bg) !important;
    border-radius: 18px;
    box-shadow: 0 16px 40px rgba(0, 0, 0, 0.45);
    overflow: hidden;
    border: 1px solid var(--login-card-border) !important;
    transition: background-color 0.25s ease, border-color 0.25s ease, box-shadow 0.25s ease;
}

.card-body-content {
    padding: 2.5rem 2.25rem;
}

.logo-area {
    text-align: center;
    margin-bottom: 1.8rem;
}

.logo-area .logo-img {
    height: 72px;
    object-fit: contain;
    display: block;
    margin: 0 auto 0.6rem auto;
    filter: drop-shadow(0 3px 6px rgba(0, 0, 0, 0.15));
}

.logo-title {
    font-weight: 800;
    font-size: 1.15rem;
    color: var(--login-text-title) !important;
    margin: 0;
}

.logo-subtitle {
    color: var(--login-text-sub) !important;
}

.badge-sistema {
    display: inline-block;
    background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
    color: white;
    font-size: 0.75rem;
    font-weight: 700;
    padding: 4px 14px;
    border-radius: 20px;
    margin-top: 0.6rem;
    box-shadow: 0 2px 6px rgba(220, 53, 69, 0.3);
}

.login-title {
    font-size: 1.35rem;
    font-weight: 800;
    color: var(--login-text-title) !important;
    margin-bottom: 0.25rem;
}

.login-subtitle {
    font-size: 0.85rem;
    color: var(--login-text-sub) !important;
    margin-bottom: 1.25rem;
}

.form-label {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--login-label) !important;
    margin-bottom: 6px;
}

.form-control {
    border-radius: 8px;
    border: 1.5px solid var(--login-input-border) !important;
    background-color: var(--login-input-bg) !important;
    color: var(--login-input-text) !important;
    padding: 0.65rem 0.9rem;
    font-size: 0.92rem;
    transition: border-color 0.2s, box-shadow 0.2s, background-color 0.2s, color 0.2s;
}

.form-control::placeholder {
    color: var(--login-text-sub) !important;
    opacity: 0.75;
}

.form-control:focus {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.25) !important;
}

.input-group .form-control {
    border-radius: 8px 0 0 8px;
}

.input-group .btn-eye {
    border: 1.5px solid var(--login-input-border) !important;
    border-left: none !important;
    background-color: var(--login-btn-eye-bg) !important;
    color: var(--login-btn-eye-text) !important;
    border-radius: 0 8px 8px 0;
    padding: 0 0.95rem;
    font-size: 1rem;
    transition: color 0.15s, background-color 0.15s, border-color 0.15s;
}

.input-group .btn-eye:hover {
    color: #dc3545 !important;
    background-color: rgba(220, 53, 69, 0.15) !important;
}

.check-recordar {
    color: var(--login-text-sub) !important;
}

.btn-login {
    background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%);
    border: none;
    color: white !important;
    font-weight: 700;
    font-size: 0.98rem;
    padding: 0.78rem;
    border-radius: 8px;
    width: 100%;
    margin-top: 0.5rem;
    box-shadow: 0 4px 12px rgba(220, 53, 69, 0.35);
    transition: background 0.2s, transform 0.15s, box-shadow 0.2s;
    cursor: pointer;
}

.btn-login:hover:not(:disabled) {
    background: linear-gradient(135deg, #c82333 0%, #901f2b 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(220, 53, 69, 0.45);
}

.btn-login:active:not(:disabled) {
    transform: translateY(0);
}

.btn-login:disabled {
    opacity: 0.85;
    cursor: not-allowed;
}

.forgot-link {
    font-size: 0.82rem;
    color: #dc3545;
    text-decoration: none;
    font-weight: 600;
    transition: color 0.15s;
}

.forgot-link:hover {
    color: #b02a37;
    text-decoration: underline;
}

/* ── FOOTER LINK & SOCIAL ── */
.footer-link {
    transition: color 0.2s, transform 0.15s;
    display: inline-block;
}

.footer-link:hover {
    color: #ffffff !important;
    transform: translateX(3px);
}

.social-icon {
    color: #adb5bd;
    transition: color 0.2s, transform 0.2s;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.social-icon:hover {
    color: #dc3545;
    transform: translateY(-2px);
}

/* ── MODAL FLOTANTE CUSTOM ── */
.modal-backdrop-custom {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.7);
    z-index: 1060;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
    backdrop-filter: blur(3px);
}

.modal-dialog-custom {
    width: 100%;
    max-width: 500px;
    animation: fadeInUp 0.25s ease-out;
}

.modal-content-custom {
    background-color: var(--login-modal-bg) !important;
    border-radius: 16px;
    padding: 1.8rem;
    border: 1px solid var(--login-card-border) !important;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    color: var(--login-text-title) !important;
}

.modal-title {
    color: var(--login-text-title) !important;
}

.modal-desc {
    color: var(--login-text-sub) !important;
}

.icon-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.btn-close-custom {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: var(--login-text-sub) !important;
    cursor: pointer;
    line-height: 1;
}

.btn-close-custom:hover {
    color: #dc3545 !important;
}

.info-box-modal {
    background-color: var(--login-modal-box) !important;
    border: 1px solid var(--login-modal-box-border) !important;
    border-radius: 10px;
    padding: 1rem;
}

.info-box-title {
    color: var(--login-text-title) !important;
}

.info-box-desc {
    color: var(--login-text-sub) !important;
}

@media (max-width: 576px) {
    .card-body-content {
        padding: 2rem 1.5rem;
    }
    .logo-area .logo-img {
        height: 60px;
    }
}
</style>
