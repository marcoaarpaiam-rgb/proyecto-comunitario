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
                    <div class="sb-role">
                        <i
                            class="bi bi-star-fill me-1"
                            style="color: #ffd700; font-size: 0.65rem"
                        ></i
                        >Líder de Equipo
                    </div>
                </div>
            </div>
            <div class="sb-nav">
                <div class="sb-sec">Mi Proyecto</div>
                <a href="/lider" class="sb-lnk"
                    ><i class="bi bi-speedometer2"></i> Mi Dashboard</a
                >
                <a href="/lider/equipo" class="sb-lnk"
                    ><i class="bi bi-people"></i> Mi Equipo</a
                >
                <a href="/lider/proyecto" class="sb-lnk"
                    ><i class="bi bi-building"></i> Mi Comunidad</a
                >
                <div class="sb-sec">Acciones</div>
                <a href="/lider/entregables" class="sb-lnk"
                    ><i class="bi bi-cloud-upload"></i> Entregables</a
                >
                <a href="/lider/carta" class="sb-lnk active"
                    ><i class="bi bi-send"></i> Carta de Presentación</a
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
                            class="bi bi-send me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Carta de Presentación
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
                        Carta de Presentación
                    </h4>
                    <p
                        style="
                            color: var(--text-muted);
                            font-size: 0.85rem;
                            margin-top: 2px;
                        "
                    >
                        Solicita la carta para presentarte ante la comunidad
                    </p>
                </div>

                <div v-if="!proyecto" class="sc">
                    <div class="estado-box" style="color: var(--text-muted)">
                        <i class="bi bi-file-earmark-x estado-icono"></i>
                        <h6 class="fw-bold mb-2">Sin proyecto asignado</h6>
                        <p class="small mb-0">
                            Tu equipo aún no tiene un proyecto aprobado. No
                            puedes solicitar la carta hasta que la comunidad sea
                            aprobada por el profesor.
                        </p>
                    </div>
                </div>

                <div v-else class="row g-3">
                    <div class="col-md-5">
                        <div class="sc h-100">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-building text-danger me-2"
                                    ></i
                                    >Datos del Proyecto
                                </h6>
                            </div>
                            <div class="p-3">
                                <div class="info-row">
                                    <span class="info-label">Equipo</span
                                    ><span
                                        class="fw-bold"
                                        style="color: var(--rojo)"
                                        >{{ equipo?.equ_codigo }}</span
                                    >
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Comunidad</span
                                    ><span>{{
                                        proyecto.comunidad?.com_nombre
                                    }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Ubicación</span
                                    ><span
                                        style="
                                            color: var(--text-muted);
                                            font-size: 0.85rem;
                                        "
                                        >{{
                                            proyecto.comunidad?.com_ubicacion
                                        }}</span
                                    >
                                </div>
                                <div class="info-row">
                                    <span class="info-label"
                                        >Estado comunidad</span
                                    >
                                    <span
                                        v-if="proyecto.pco_fecha_aprobacion"
                                        class="bs ba"
                                        >✓ Aprobada</span
                                    >
                                    <span v-else class="bs bw">Pendiente</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-7">
                        <div class="sc h-100">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-file-earmark-text text-danger me-2"
                                    ></i
                                    >Estado de la Carta
                                </h6>
                            </div>

                            <!-- Sin carta -->
                            <div v-if="!carta" class="estado-box">
                                <i
                                    class="bi bi-send estado-icono"
                                    style="color: var(--text-muted)"
                                ></i>
                                <h6
                                    class="fw-bold mb-2"
                                    style="color: var(--text-main)"
                                >
                                    Sin carta solicitada
                                </h6>
                                <p
                                    class="small mb-4"
                                    style="color: var(--text-muted)"
                                >
                                    Una vez solicitada, la coordinación la
                                    revisará y generará con firma y sello
                                    institucional.
                                </p>
                                <button
                                    class="btn btn-danger fw-bold rounded-pill px-4"
                                    @click="solicitar"
                                    :disabled="
                                        !proyecto.pco_fecha_aprobacion ||
                                        loading
                                    "
                                >
                                    <span
                                        v-if="loading"
                                        class="spinner-border spinner-border-sm me-2"
                                    ></span>
                                    <i v-else class="bi bi-send me-2"></i
                                    >Solicitar Carta de Presentación
                                </button>
                                <div
                                    v-if="!proyecto.pco_fecha_aprobacion"
                                    style="
                                        font-size: 0.78rem;
                                        color: var(--text-muted);
                                        margin-top: 0.5rem;
                                    "
                                >
                                    La comunidad debe estar aprobada para
                                    solicitar la carta.
                                </div>
                            </div>

                            <!-- Carta en revisión -->
                            <div
                                v-else-if="
                                    carta.cpr_solicitada &&
                                    !carta.cpr_fecha_aprobacion
                                "
                                class="estado-box"
                            >
                                <i
                                    class="bi bi-hourglass-split estado-icono"
                                    style="color: #856404"
                                ></i>
                                <h6 class="fw-bold mb-2" style="color: #856404">
                                    Carta en Revisión
                                </h6>
                                <p
                                    class="small mb-3"
                                    style="color: var(--text-muted)"
                                >
                                    Tu solicitud fue enviada. La coordinación
                                    está revisando y generando la carta con
                                    firma y sello institucional.
                                </p>
                                <span
                                    class="bs bw"
                                    style="
                                        font-size: 0.85rem;
                                        padding: 6px 18px;
                                    "
                                    ><i class="bi bi-clock me-1"></i>Pendiente
                                    de aprobación</span
                                >
                            </div>

                            <!-- Carta aprobada -->
                            <div
                                v-else-if="carta.cpr_fecha_aprobacion"
                                class="estado-box"
                            >
                                <i
                                    class="bi bi-file-earmark-check-fill estado-icono"
                                    style="color: #198754"
                                ></i>
                                <h6 class="fw-bold mb-2" style="color: #198754">
                                    ¡Carta Lista!
                                </h6>
                                <p
                                    class="small mb-3"
                                    style="color: var(--text-muted)"
                                >
                                    La carta fue aprobada y generada con firma y
                                    sello de la coordinación. Ya puedes
                                    presentarte ante la comunidad.
                                </p>
                                <div
                                    class="d-flex gap-2 justify-content-center flex-wrap mb-3"
                                >
                                    <span class="bs ba"
                                        ><i class="bi bi-patch-check me-1"></i
                                        >Aprobada</span
                                    >
                                    <span
                                        v-if="carta.cpr_sello"
                                        class="bs"
                                        style="
                                            background: rgba(13, 110, 253, 0.1);
                                            color: #055160;
                                            border: 1px solid
                                                rgba(13, 110, 253, 0.2);
                                        "
                                        ><i class="bi bi-award me-1"></i>Con
                                        Sello</span
                                    >
                                    <span
                                        v-if="carta.cpr_firma_digital"
                                        class="bs"
                                        style="
                                            background: rgba(111, 66, 193, 0.1);
                                            color: #432874;
                                            border: 1px solid
                                                rgba(111, 66, 193, 0.2);
                                        "
                                        ><i class="bi bi-pen me-1"></i>Con
                                        Firma</span
                                    >
                                </div>
                                <a
                                    :href="`/lider/carta/${carta.cpr_id}/descargar`"
                                    target="_blank"
                                    class="btn btn-success fw-bold rounded-pill px-4"
                                >
                                    <i class="bi bi-download me-2"></i>Descargar
                                    PDF
                                </a>
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
const props = defineProps({ carta: Object, proyecto: Object, equipo: Object });
const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("lid-theme") || "light");
const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("lid-theme", tema.value);
};
const logout = () => router.post("/logout");
const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});
const loading = ref(false);
const solicitar = () => {
    if (!props.proyecto) return;
    loading.value = true;
    router.post(
        "/lider/carta",
        { cpr_id_pco: props.proyecto.pco_id },
        {
            onError: () => {
                loading.value = false;
            },
            onFinish: () => {
                loading.value = false;
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
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 0.6rem 0;
    border-bottom: 1px solid var(--border);
    font-size: 0.88rem;
}
.info-row:last-child {
    border-bottom: none;
}
.info-label {
    color: var(--text-muted);
    font-weight: 700;
    font-size: 0.82rem;
}
.bs {
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.76rem;
    font-weight: 700;
}
.ba {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
}
.bw {
    background: #fff3cd;
    color: #664d03;
    border: 1px solid #ffecb5;
}
.bsec {
    background: #f8f9fa;
    color: #212529;
    border: 1px solid #dee2e6;
}
[data-theme="dark"] .ba {
    background: rgba(25, 135, 84, 0.25);
    color: #a3cfbb;
    border-color: rgba(25, 135, 84, 0.4);
}
[data-theme="dark"] .bw {
    background: rgba(255, 193, 7, 0.25);
    color: #ffe69c;
    border-color: rgba(255, 193, 7, 0.4);
}
[data-theme="dark"] .bsec {
    background: #252830;
    color: #f1f3f5;
    border-color: #2d3139;
}
.estado-box {
    text-align: center;
    padding: 2rem 1.5rem;
}
.estado-icono {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
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
