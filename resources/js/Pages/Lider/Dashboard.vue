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

        <!-- SIDEBAR -->
        <div class="sb-lid" :class="{ show: sidebarOpen }">
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
                <div class="sb-avatar">{{ iniciales }}</div>
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
                <a href="/lider" class="sb-link active"
                    ><i class="bi bi-speedometer2"></i> Mi Dashboard</a
                >
                <a href="/lider/equipo" class="sb-link"
                    ><i class="bi bi-people"></i> Mi Equipo</a
                >
                <a href="/lider/proyecto" class="sb-link"
                    ><i class="bi bi-building"></i> Mi Comunidad</a
                >
                <div class="sb-sec">Acciones</div>
                <a href="/lider/entregables" class="sb-link"
                    ><i class="bi bi-cloud-upload"></i> Entregables</a
                >
                <a href="/lider/carta" class="sb-link"
                    ><i class="bi bi-send"></i> Carta de Presentación</a
                >
                <div style="padding: 0.75rem 1.4rem; margin-top: auto">
                    <form @submit.prevent="logout">
                        <button
                            type="submit"
                            class="sb-link w-100 text-start border-0 p-0"
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

        <!-- MAIN -->
        <div class="main-lid">
            <div class="topbar-lid">
                <div class="d-flex align-items-center gap-3">
                    <button
                        class="btn btn-outline-danger btn-sm toggle-btn px-2"
                        @click="sidebarOpen = true"
                    >
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div class="topbar-title">
                        <i
                            class="bi bi-speedometer2 me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Mi Dashboard
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="tb-btn" @click="toggleTema">
                        <i
                            class="bi"
                            :class="
                                tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'
                            "
                        ></i>
                    </button>
                    <button class="tb-btn">
                        <i class="bi bi-bell"></i>
                        <span
                            v-if="notificaciones.length > 0"
                            class="nd"
                        ></span>
                    </button>
                </div>
            </div>

            <div class="content-lid">
                <!-- BANNER -->
                <div class="banner-lid" v-if="equipo">
                    <div>
                        <div class="b-title">
                            <i
                                class="bi bi-star-fill me-2"
                                style="color: #ffd700"
                            ></i
                            >Eres el Líder — {{ equipo.equ_codigo }}
                        </div>
                        <div class="b-sub">{{ equipo.equ_titulo }}</div>
                    </div>
                    <a
                        href="/lider/entregables"
                        class="btn btn-light btn-sm rounded-pill px-3 fw-bold"
                        style="color: #1a1a1a"
                    >
                        <i class="bi bi-cloud-arrow-up me-1 text-danger"></i
                        >Subir Entregable
                    </a>
                </div>

                <div class="row g-3">
                    <!-- IZQUIERDA -->
                    <div class="col-lg-5">
                        <!-- Info proyecto -->
                        <div class="sc">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-info-circle me-2 text-danger"
                                    ></i
                                    >Mi Proyecto
                                </h6>
                            </div>
                            <div class="p-3" v-if="equipo?.proyecto_comunidad">
                                <div class="info-row">
                                    <span class="info-label">Código</span
                                    ><strong>{{ equipo.equ_codigo }}</strong>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Comunidad</span
                                    ><span>{{
                                        equipo.proyecto_comunidad.comunidad
                                            ?.com_nombre
                                    }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Tipo</span
                                    ><span>{{
                                        equipo.proyecto_comunidad.tipo_proyecto
                                            ?.tpr_nombre
                                    }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Modalidad</span
                                    ><span>{{
                                        equipo.proyecto_comunidad.modalidad
                                            ?.mpr_nombre
                                    }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Fecha límite</span
                                    ><strong style="color: #dc3545">{{
                                        equipo.proyecto_comunidad
                                            .pco_fecha_limite
                                    }}</strong>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"
                                        >Aprobación comunidad</span
                                    >
                                    <span
                                        v-if="
                                            equipo.proyecto_comunidad
                                                .pco_fecha_aprobacion
                                        "
                                        class="bs ba"
                                        >✓ Aprobada</span
                                    >
                                    <span v-else class="bs bw">Pendiente</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label"
                                        >Carta presentación</span
                                    >
                                    <span
                                        v-if="carta?.cpr_fecha_aprobacion"
                                        class="bs ba"
                                        >✓ Generada</span
                                    >
                                    <span
                                        v-else-if="carta?.cpr_solicitada"
                                        class="bs bw"
                                        >En revisión</span
                                    >
                                    <a
                                        v-else-if="
                                            equipo.proyecto_comunidad
                                                .pco_fecha_aprobacion
                                        "
                                        href="/lider/carta"
                                        class="bs bd"
                                        >Solicitar</a
                                    >
                                    <span
                                        v-else
                                        class="bs"
                                        style="
                                            background: var(--bg-th);
                                            color: var(--text-muted);
                                        "
                                        >No disponible</span
                                    >
                                </div>
                            </div>
                            <div
                                v-else
                                class="p-3 text-center text-muted small py-4"
                            >
                                Sin proyecto asignado
                            </div>
                        </div>

                        <!-- Mi equipo -->
                        <div class="sc">
                            <div class="sh">
                                <h6>
                                    <i class="bi bi-people me-2 text-danger"></i
                                    >Mi Equipo
                                </h6>
                            </div>
                            <div class="p-3">
                                <div
                                    v-if="!equipo?.integrantes?.length"
                                    class="text-center text-muted small py-3"
                                >
                                    Sin integrantes
                                </div>
                                <div v-else class="d-flex flex-wrap">
                                    <span
                                        v-for="int in equipo.integrantes"
                                        :key="int.ein_id"
                                        class="member-chip"
                                    >
                                        <div
                                            class="mdot"
                                            :style="{
                                                background: int.ein_es_lider
                                                    ? '#dc3545'
                                                    : '#495057',
                                            }"
                                        >
                                            {{
                                                int.usuario
                                                    ?.usu_primer_nombre?.[0]
                                            }}{{
                                                int.usuario
                                                    ?.usu_primer_apellido?.[0]
                                            }}
                                        </div>
                                        {{ int.usuario?.usu_primer_nombre }}
                                        {{ int.usuario?.usu_primer_apellido }}
                                        <i
                                            v-if="int.ein_es_lider"
                                            class="bi bi-star-fill ms-1"
                                            style="
                                                color: #ffd700;
                                                font-size: 0.6rem;
                                            "
                                        ></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DERECHA -->
                    <div class="col-lg-7">
                        <!-- Progreso -->
                        <div class="sc">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-list-check me-2 text-danger"
                                    ></i
                                    >Mis Puntos de Control
                                </h6>
                                <span class="bs bp"
                                    >{{
                                        puntosControl.filter(
                                            (p) => p.seguimiento?.seq_cumplido,
                                        ).length
                                    }}/{{
                                        puntosControl.length
                                    }}
                                    cumplidos</span
                                >
                            </div>
                            <div
                                v-if="puntosControl.length === 0"
                                class="p-3 text-center text-muted small py-4"
                            >
                                Sin puntos de control
                            </div>
                            <div
                                v-for="(punto, idx) in puntosControl"
                                :key="punto.puc_id"
                                class="step-row"
                            >
                                <div class="step-num" :class="estadoNum(punto)">
                                    <i
                                        v-if="estadoNum(punto) === 'sn-done'"
                                        class="bi bi-check-lg"
                                    ></i>
                                    <span v-else>{{ idx + 1 }}</span>
                                </div>
                                <div class="step-info flex-grow-1">
                                    <div class="s-name">
                                        {{ punto.puc_nombre }}
                                    </div>
                                    <div
                                        class="s-label"
                                        :style="{ color: colorLabel(punto) }"
                                    >
                                        {{ textoLabel(punto) }}
                                    </div>
                                </div>
                                <span class="bs" :class="badgePunto(punto)">{{
                                    labelBadge(punto)
                                }}</span>
                            </div>
                        </div>

                        <!-- Entregables -->
                        <div class="sc">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-file-earmark me-2 text-danger"
                                    ></i
                                    >Mis Entregables
                                </h6>
                                <a
                                    href="/lider/entregables"
                                    class="btn btn-sm btn-danger rounded-pill px-3 fw-bold"
                                >
                                    <i class="bi bi-plus me-1"></i>Subir
                                </a>
                            </div>
                            <div
                                v-if="entregables.length === 0"
                                class="p-3 text-center text-muted small py-4"
                            >
                                Sin entregables subidos
                            </div>
                            <div
                                v-for="ent in entregables"
                                :key="ent.entr_id"
                                class="ent-row"
                            >
                                <div class="d-flex align-items-center gap-2">
                                    <i
                                        class="bi fs-5"
                                        :class="
                                            ent.entr_aprobado
                                                ? 'bi-file-pdf text-danger'
                                                : ent.entr_aprobado === false
                                                  ? 'bi-file-x text-secondary'
                                                  : 'bi-file-earmark text-muted'
                                        "
                                    ></i>
                                    <div>
                                        <div
                                            style="
                                                font-weight: 700;
                                                font-size: 0.9rem;
                                            "
                                        >
                                            {{ ent.entr_nombre_archivo }}
                                        </div>
                                        <div
                                            style="
                                                font-size: 0.78rem;
                                                color: var(--text-muted);
                                            "
                                        >
                                            {{
                                                ent.tipo_entregable?.tet_nombre
                                            }}
                                        </div>
                                    </div>
                                </div>
                                <span
                                    class="bs"
                                    :class="
                                        ent.entr_aprobado === null
                                            ? 'bp'
                                            : ent.entr_aprobado
                                              ? 'ba'
                                              : 'bd'
                                    "
                                >
                                    {{
                                        ent.entr_aprobado === null
                                            ? "En revisión"
                                            : ent.entr_aprobado
                                              ? "Aprobado"
                                              : "Rechazado"
                                    }}
                                </span>
                            </div>
                        </div>

                        <!-- Notificaciones -->
                        <div class="sc" v-if="notificaciones.length > 0">
                            <div class="sh">
                                <h6>
                                    <i class="bi bi-bell me-2 text-danger"></i
                                    >Notificaciones
                                </h6>
                            </div>
                            <div
                                v-for="not in notificaciones.slice(0, 4)"
                                :key="not.not_id"
                                class="notif-item"
                            >
                                <div
                                    class="ni"
                                    :class="tipoNI(not.not_mensaje)"
                                >
                                    <i
                                        class="bi"
                                        :class="iconNI(not.not_mensaje)"
                                    ></i>
                                </div>
                                <div>
                                    <div class="nt">{{ not.not_mensaje }}</div>
                                    <div class="ntm">
                                        {{
                                            formatFecha(
                                                not.not_fecha_generacion,
                                            )
                                        }}
                                    </div>
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
import { ref, computed, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";

const props = defineProps({
    equipo: { type: Object, default: null },
    puntosControl: { type: Array, default: () => [] },
    entregables: { type: Array, default: () => [] },
    carta: { type: Object, default: null },
});

const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("lid-theme") || "light");
const notificaciones = ref([]);

const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});

const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("lid-theme", tema.value);
};

const logout = () => router.post("/logout");

const estadoNum = (p) => {
    if (p.seguimiento?.seq_cumplido) return "sn-done";
    if (new Date(p.puc_fecha_limite) < new Date()) return "sn-active";
    return "sn-pending";
};

const colorLabel = (p) => {
    if (p.seguimiento?.seq_cumplido) return "#198754";
    if (new Date(p.puc_fecha_limite) < new Date()) return "#dc3545";
    return "var(--text-muted)";
};

const textoLabel = (p) => {
    if (p.seguimiento?.seq_cumplido) return "✓ Cumplido";
    const vencido = new Date(p.puc_fecha_limite) < new Date();
    return (
        (p.puc_fecha_reprogramada || p.puc_fecha_limite) +
        (vencido ? " — Pendiente" : "")
    );
};

const badgePunto = (p) => {
    if (p.seguimiento?.seq_cumplido) return "ba";
    if (new Date(p.puc_fecha_limite) < new Date()) return "bw";
    return "bp";
};

const labelBadge = (p) => {
    if (p.seguimiento?.seq_cumplido) return "Cumplido";
    if (new Date(p.puc_fecha_limite) < new Date()) return "Pendiente";
    return "Próximo";
};

const tipoNI = (msg) => {
    if (msg?.includes("vencido")) return "d";
    if (msg?.includes("vence")) return "w";
    return "i";
};

const iconNI = (msg) => {
    if (msg?.includes("vencido")) return "bi-exclamation-triangle";
    if (msg?.includes("vence")) return "bi-clock";
    return "bi-info-circle";
};

const formatFecha = (f) => {
    const d = new Date(f),
        diff = Math.floor((new Date() - d) / 1000);
    if (diff < 60) return "Hace un momento";
    if (diff < 3600) return `Hace ${Math.floor(diff / 60)} min`;
    if (diff < 86400) return `Hace ${Math.floor(diff / 3600)} h`;
    return d.toLocaleDateString("es-VE");
};

onMounted(() => {
    fetch("/notificaciones/lista")
        .then((r) => r.json())
        .then((d) => {
            notificaciones.value = d;
        })
        .catch(() => {});
});
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
    --notif-border: #e9ecef;
    --border-side: rgba(220, 53, 69, 0.25);
    --row-border: #dee2e6;
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
    --row-border: #2d3139;
}
.sb-lid {
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
.sb-avatar {
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
.sb-link {
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
.sb-link:hover {
    background: rgba(255, 255, 255, 0.06);
    color: #fff;
}
.sb-link.active {
    background: rgba(220, 53, 69, 0.18);
    color: #fff;
    border-left-color: #dc3545;
    font-weight: 700;
}
.sb-link i {
    font-size: 1rem;
    width: 20px;
    text-align: center;
}
.main-lid {
    transition: margin-left 0.3s;
}
.topbar-lid {
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
.tb-btn {
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1.3rem;
    cursor: pointer;
    padding: 4px;
    position: relative;
    transition: color 0.2s;
}
.tb-btn:hover {
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
.content-lid {
    padding: 1.4rem 1.25rem;
}
.banner-lid {
    background: linear-gradient(135deg, #1e2227, #090a0c);
    border-radius: 13px;
    padding: 1.3rem 1.5rem;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    border: 1px solid var(--border-side);
    margin-bottom: 1.2rem;
}
.banner-lid .b-title {
    font-weight: 800;
    font-size: 1.05rem;
}
.banner-lid .b-sub {
    opacity: 0.85;
    font-size: 0.85rem;
    margin-top: 3px;
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
    justify-content: space-between;
    flex-wrap: wrap;
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
    align-items: center;
    padding: 0.7rem 0;
    border-bottom: 1px solid var(--row-border);
    font-size: 0.9rem;
    flex-wrap: wrap;
    gap: 6px;
    transition: border-color 0.3s;
}
.info-row:last-child {
    border-bottom: none;
}
.info-label {
    color: var(--text-muted);
    font-weight: 700;
    font-size: 0.85rem;
}
.step-row {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0.75rem 1.3rem;
    border-bottom: 1px solid var(--border);
}
.step-row:last-child {
    border-bottom: none;
}
.step-num {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.85rem;
    flex-shrink: 0;
}
.sn-done {
    background: #dc3545;
    color: #fff;
}
.sn-active {
    background: var(--bg-card);
    border: 2px solid #dc3545;
    color: #dc3545;
}
.sn-pending {
    background: var(--bg-th);
    color: var(--text-muted);
    border: 2px solid var(--border);
}
.step-info .s-name {
    font-weight: 700;
    color: var(--text-main);
    font-size: 0.9rem;
}
.step-info .s-label {
    font-size: 0.78rem;
    margin-top: 1px;
}
.ent-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 1.3rem;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
    gap: 8px;
}
.ent-row:last-child {
    border-bottom: none;
}
.bs {
    padding: 4px 11px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 700;
}
.ba {
    background: #d1e7dd;
    color: #0f5132;
    border: 1px solid #badbcc;
}
.bp {
    background: #cff4fc;
    color: #055160;
    border: 1px solid #b6effb;
}
.bw {
    background: #fff3cd;
    color: #664d03;
    border: 1px solid #ffecb5;
}
.bd {
    background: #f8d7da;
    color: #842029;
    border: 1px solid #f5c2c7;
}
[data-theme="dark"] .ba {
    background: rgba(25, 135, 84, 0.25);
    color: #a3cfbb;
    border-color: rgba(25, 135, 84, 0.4);
}
[data-theme="dark"] .bp {
    background: rgba(13, 110, 253, 0.25);
    color: #9ec5fe;
    border-color: rgba(13, 110, 253, 0.4);
}
[data-theme="dark"] .bw {
    background: rgba(255, 193, 7, 0.25);
    color: #ffe69c;
    border-color: rgba(255, 193, 7, 0.4);
}
[data-theme="dark"] .bd {
    background: rgba(220, 53, 69, 0.25);
    color: #f8d7da;
    border-color: rgba(220, 53, 69, 0.4);
}
.member-chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    background: var(--bg-th);
    border: 1px solid var(--border);
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-main);
    margin: 2px;
}
.mdot {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    font-weight: 800;
    color: #fff;
}
.notif-item {
    display: flex;
    gap: 10px;
    padding: 0.8rem 1.3rem;
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
    font-size: 0.86rem;
    color: var(--text-main);
    font-weight: 500;
    line-height: 1.5;
}
.ntm {
    font-size: 0.74rem;
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
    .sb-lid {
        transform: translateX(0) !important;
    }
    .main-lid {
        margin-left: 250px;
    }
    .toggle-btn {
        display: none !important;
    }
}
@media (max-width: 991.98px) {
    .sb-lid {
        transform: translateX(-100%);
    }
    .main-lid {
        margin-left: 0;
    }
    .sb-lid.show {
        transform: translateX(0);
    }
    .sb-overlay.show {
        display: block !important;
    }
    .content-lid {
        padding: 1rem;
    }
    .banner-lid {
        text-align: center;
        justify-content: center;
    }
}
</style>
