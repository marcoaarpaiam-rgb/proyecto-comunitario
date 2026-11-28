<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
        "
    >
        <!-- SIDEBAR PROFESOR ESTANDARIZADO -->
        <SidebarProfesor :sidebar-open="sidebarOpen" @close="sidebarOpen = false" />

        <!-- MAIN -->
        <div class="main-prof">
            <!-- TOPBAR -->
            <div class="topbar-prof">
                <div class="d-flex align-items-center gap-3">
                    <button
                        class="btn btn-outline-danger btn-sm toggle-btn px-2"
                        @click="sidebarOpen = true"
                    >
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div class="topbar-title">
                        <i class="bi bi-collection me-2 text-danger d-none d-sm-inline"></i>
                        Mis Secciones
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="topbar-btn" @click="toggleTema" title="Cambiar tema">
                        <i
                            class="bi"
                            :class="tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'"
                        ></i>
                    </button>
                    <button
                        class="topbar-btn"
                        @click="mostrarNotif = !mostrarNotif"
                        style="position: relative"
                        title="Notificaciones"
                    >
                        <i class="bi bi-bell"></i>
                        <span v-if="notifNoLeidas > 0" class="nd"></span>
                    </button>

                    <!-- Panel de notificaciones -->
                    <div
                        v-if="mostrarNotif"
                        style="
                            position: absolute;
                            top: 56px;
                            right: 16px;
                            width: 320px;
                            z-index: 1000;
                            background: var(--bg-card);
                            border: 1px solid var(--border);
                            border-radius: 12px;
                            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
                            overflow: hidden;
                        "
                    >
                        <div
                            style="
                                padding: 0.75rem 1rem;
                                border-bottom: 1px solid var(--border);
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                            "
                        >
                            <span style="font-weight: 800; font-size: 0.9rem">
                                Notificaciones
                                <span v-if="notifNoLeidas > 0" class="badge bg-danger rounded-pill ms-1">{{ notifNoLeidas }}</span>
                            </span>
                            <div class="d-flex align-items-center gap-2">
                                <button
                                    v-if="notifNoLeidas > 0"
                                    @click="marcarTodasLeidas"
                                    class="btn btn-link p-0 text-decoration-none"
                                    style="font-size: 0.75rem; color: #dc3545;"
                                >
                                    Marcar leídas
                                </button>
                                <button
                                    @click="mostrarNotif = false"
                                    style="
                                        background: none;
                                        border: none;
                                        color: var(--text-muted);
                                        cursor: pointer;
                                    "
                                >
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>
                        <div style="max-height: 300px; overflow-y: auto">
                            <div
                                v-if="!listaNotificaciones?.length"
                                class="text-center py-4"
                                style="
                                    color: var(--text-muted);
                                    font-size: 0.85rem;
                                "
                            >
                                <i class="bi bi-bell-slash d-block fs-3 mb-1"></i>
                                Sin notificaciones
                            </div>
                            <div
                                v-for="n in listaNotificaciones"
                                :key="n.not_id"
                                class="notif-item"
                                :style="{
                                    background: !n.not_leida ? 'rgba(220,53,69,.06)' : 'transparent',
                                    cursor: !n.not_leida ? 'pointer' : 'default'
                                }"
                                @click="marcarLeida(n)"
                            >
                                <div class="ni" :class="n.not_tipo === 'peligro' ? 'd' : n.not_tipo === 'alerta' ? 'w' : 'i'">
                                    <i
                                        class="bi"
                                        :class="n.not_tipo === 'peligro' ? 'bi-exclamation-octagon' : n.not_tipo === 'alerta' ? 'bi-exclamation-triangle' : 'bi-info-circle'"
                                    ></i>
                                </div>
                                <div>
                                    <div class="nt" v-html="n.not_mensaje"></div>
                                    <div class="ntm">
                                        {{ n.not_fecha_generacion ? String(n.not_fecha_generacion).slice(0, 16).replace('T', ' ') : '' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CONTENIDO -->
            <div class="content-prof">
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                    <div>
                        <h4 style="font-weight: 800; margin: 0; color: var(--text-main)">
                            Aulas Asignadas
                        </h4>
                        <p style="color: var(--text-muted); font-size: 0.88rem; margin: 2px 0 0">
                            Selecciona una sección para ver el seguimiento unificado de sus equipos, estudiantes y resultados
                        </p>
                    </div>
                    <div class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2 rounded-pill small fw-bold">
                        <i class="bi bi-journal-bookmark me-1"></i> {{ (secciones || []).length }} sección(es)
                    </div>
                </div>

                <div v-if="$page.props.flash?.error" class="alert alert-danger py-2 px-3 small mb-3">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i>{{ $page.props.flash.error }}
                </div>
                <div v-if="$page.props.flash?.success" class="alert alert-success py-2 px-3 small mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ $page.props.flash.success }}
                </div>
                <div v-if="Object.keys($page.props.errors || {}).length > 0" class="alert alert-danger py-2 px-3 small mb-3">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i>Ocurrió un error al procesar la solicitud:</div>
                    <ul class="mb-0 ps-3">
                        <li v-for="(err, k) in $page.props.errors" :key="k">{{ err }}</li>
                    </ul>
                </div>

                <!-- Sin secciones -->
                <div
                    v-if="!secciones?.length"
                    class="text-center py-5"
                    style="
                        background: var(--bg-card);
                        border: 1px solid var(--border);
                        border-radius: 14px;
                        padding: 3rem 1.5rem;
                    "
                >
                    <div style="font-size: 3rem; margin-bottom: 0.8rem">📋</div>
                    <h5 style="font-weight: 800; color: var(--text-main); margin-bottom: 0.5rem">
                        No tienes secciones asignadas
                    </h5>
                    <p style="color: var(--text-muted); font-size: 0.9rem; max-width: 420px; margin: 0 auto">
                        Comunícate con el Coordinador de PNF para que te vincule a las secciones correspondientes de este período.
                    </p>
                </div>

                <!-- Grid de secciones -->
                <div v-else class="row g-4">
                    <div
                        v-for="sec in secciones"
                        :key="sec.sec_id"
                        class="col-12 col-md-6 col-xl-4"
                    >
                        <div class="sec-card">
                            <!-- Header de la tarjeta -->
                            <div class="d-flex align-items-center gap-3 mb-3">
                                <div class="sec-badge">
                                    {{ sec.sec_codigo }}
                                </div>
                                <div style="min-width: 0; flex: 1">
                                    <div class="sec-title">
                                        Sección {{ sec.sec_codigo }}
                                    </div>
                                    <div class="sec-subtitle text-truncate">
                                        {{ sec.pnf || 'PNFI' }} &bull; {{ sec.trayecto || 'Trayecto Asignado' }}
                                    </div>
                                </div>
                            </div>

                            <!-- Estadísticas Rápidas -->
                            <div class="row g-2 mb-3">
                                <div class="col-4">
                                    <div class="stat-pill">
                                        <div class="sp-val text-danger">
                                            {{ sec.total_equipos || 0 }}
                                        </div>
                                        <div class="sp-lbl">Equipos</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-pill">
                                        <div class="sp-val text-primary">
                                            {{ sec.total_estudiantes || 0 }}
                                        </div>
                                        <div class="sp-lbl">Estudiantes</div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="stat-pill">
                                        <div class="sp-val text-success">
                                            {{ sec.turno || 'N/A' }}
                                        </div>
                                        <div class="sp-lbl">Turno</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botón de Acción y Descargas Oficiales -->
                            <Link
                                :href="`/profesor/secciones/${sec.sec_id}/equipos`"
                                class="btn-enter"
                            >
                                <span>Ver Equipos y Proyectos</span>
                                <i class="bi bi-arrow-right"></i>
                            </Link>

                            <div class="mt-2 pt-2 border-top" style="border-color: var(--border) !important;">
                                <a :href="`/profesor/secciones/${sec.sec_id}/excel/vinculacion`" class="btn btn-sm btn-outline-success w-100 py-1 small fw-bold text-nowrap" title="Descargar Planilla de Vinculación oficial UPTP">
                                    <i class="bi bi-download me-1"></i>Planilla de Vinculación (.xlsx)
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Confirmación Cerrar Sesión -->
        <ModalConfirmar
            ref="modalLogout"
            titulo="Cerrar Sesión"
            mensaje="¿Estás seguro de que deseas cerrar sesión en el sistema?"
            icono="🚪"
            textoConfirmar="Sí, cerrar sesión"
            textoCancelar="Cancelar"
            colorConfirmar="#dc3545"
            @confirmar="ejecutarLogout"
        />
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import ModalConfirmar from "@/Components/ModalConfirmar.vue";
import SidebarProfesor from "@/Components/SidebarProfesor.vue";

const props = defineProps({
    secciones: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("prof-theme") || "light");

const iniciales = computed(() => {
    const u = page.props.auth?.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});

const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("prof-theme", tema.value);
};

const modalLogout = ref(null);
const logout = () => {
    modalLogout.value?.abrir();
};
const ejecutarLogout = () => {
    router.post("/logout");
};

// Notificaciones
const mostrarNotif = ref(false);
const listaNotificaciones = ref([]);
const notifNoLeidas = ref(page.props.notificacionesNoLeidas || 0);

const cargarNotificaciones = async () => {
    try {
        const res = await window.axios.get("/notificaciones/lista");
        listaNotificaciones.value = res.data;
        notifNoLeidas.value = (res.data || []).filter((n) => !n.not_leida).length;
    } catch (e) {}
};

const marcarTodasLeidas = async () => {
    try {
        await window.axios.post("/notificaciones/leer-todas");
        listaNotificaciones.value.forEach((n) => (n.not_leida = true));
        notifNoLeidas.value = 0;
    } catch (e) {}
};

const marcarLeida = async (n) => {
    if (n.not_leida) return;
    try {
        await window.axios.post(`/notificaciones/${n.not_id}/leer`);
        n.not_leida = true;
        notifNoLeidas.value = Math.max(0, notifNoLeidas.value - 1);
    } catch (e) {}
};

onMounted(() => {
    cargarNotificaciones();
});
</script>

<style scoped>
:root {
    --rojo: #dc3545;
    --bg-body: #f0f2f5;
    --bg-card: #ffffff;
    --bg-sidebar: #1a1d23;
    --bg-topbar: #ffffff;
    --border: #dee2e6;
    --text-main: #1a1a1a;
    --text-muted: #6c757d;
    --bg-th: #f8f9fa;
    --bg-hover: #fff8f8;
    --notif-border: #f1f3f5;
    --border-side: rgba(220, 53, 69, 0.4);
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
    --notif-border: #2d3139;
    --border-side: rgba(220, 53, 69, 0.5);
}

.main-prof {
    transition: margin-left 0.3s;
}
.topbar-prof {
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
.topbar-title {
    font-weight: 800;
    color: var(--text-main);
    font-size: 1.15rem;
}
.topbar-btn {
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1.3rem;
    cursor: pointer;
    padding: 4px;
    position: relative;
    transition: color 0.2s;
}
.topbar-btn:hover {
    color: #dc3545;
}
.nd {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 8px;
    height: 8px;
    background: #dc3545;
    border-radius: 50%;
    border: 2px solid var(--bg-topbar);
}
.content-prof {
    padding: 1.4rem 1.25rem;
}

/* Tarjeta de Sección */
.sec-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 14px;
    padding: 1.4rem;
    transition: transform 0.2s, box-shadow 0.2s, border-color 0.2s;
    height: 100%;
    display: flex;
    flex-direction: column;
}
.sec-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(220, 53, 69, 0.12);
    border-color: rgba(220, 53, 69, 0.3);
}
.sec-badge {
    width: 48px;
    height: 48px;
    background: linear-gradient(135deg, #dc3545 0%, #a71d2a 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 900;
    font-size: 1.15rem;
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(220, 53, 69, 0.25);
}
.sec-title {
    font-weight: 800;
    font-size: 1.05rem;
    color: var(--text-main);
}
.sec-subtitle {
    font-size: 0.8rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-top: 2px;
}
.stat-pill {
    background: var(--bg-th);
    border: 1px solid var(--border);
    border-radius: 10px;
    padding: 0.6rem 0.35rem;
    text-align: center;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    overflow: hidden;
}
.sp-val {
    font-size: clamp(0.75rem, 1.05vw, 1.1rem);
    font-weight: 800;
    line-height: 1.2;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sp-lbl {
    font-size: 0.72rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-top: 2px;
}
.btn-enter {
    margin-top: auto;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #dc3545;
    color: #fff;
    border-radius: 10px;
    padding: 0.65rem 1rem;
    font-weight: 700;
    font-size: 0.88rem;
    text-decoration: none;
    transition: background 0.2s, transform 0.15s;
}
.btn-enter:hover {
    background: #bb2d3b;
    color: #fff;
    transform: translateY(-1px);
}

/* Notificaciones Dropdown */
.notif-item {
    display: flex;
    gap: 10px;
    padding: 0.85rem 1.1rem;
    border-bottom: 1px solid var(--notif-border);
    align-items: flex-start;
}
.notif-item:last-child {
    border-bottom: none;
}
.ni {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.95rem;
    flex-shrink: 0;
}
.ni.w {
    background: #fff3cd;
    color: #664d03;
}
.ni.d {
    background: #f8d7da;
    color: #842029;
}
.ni.i {
    background: #cff4fc;
    color: #055160;
}
[data-theme="dark"] .ni.w {
    background: rgba(255, 193, 7, 0.2);
    color: #ffe69c;
}
[data-theme="dark"] .ni.d {
    background: rgba(220, 53, 69, 0.2);
    color: #f8d7da;
}
[data-theme="dark"] .ni.i {
    background: rgba(13, 110, 253, 0.2);
    color: #9ec5fe;
}
.nt {
    font-size: 0.85rem;
    color: var(--text-main);
    font-weight: 500;
    line-height: 1.4;
}
.ntm {
    font-size: 0.72rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-top: 2px;
}

.sb-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1035;
    display: none;
}
@media (min-width: 992px) {
    .main-prof {
        margin-left: 250px;
    }
    .toggle-btn {
        display: none !important;
    }
}
@media (max-width: 991.98px) {
    .main-prof {
        margin-left: 0;
    }
    .content-prof {
        padding: 1rem;
    }
}
</style>
