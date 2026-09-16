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
        <div class="sb-prof" :class="{ show: sidebarOpen }">
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
                        font-size: 1rem;
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
                    <div class="sb-role">Profesor de Proyecto</div>
                </div>
            </div>
            <div class="sb-nav">
                <div class="sb-sec">Principal</div>
                <a href="/profesor" class="sb-link active"
                    ><i class="bi bi-speedometer2"></i> Dashboard</a
                >
                <a href="/profesor/equipos" class="sb-link"
                    ><i class="bi bi-people"></i> Mis Equipos</a
                >
                <div class="sb-sec">Gestión</div>
                <a href="/profesor/puntos-control" class="sb-link"
                    ><i class="bi bi-flag"></i> Puntos de Control
                    <span v-if="stats.puntosVencer > 0" class="sb-badge">{{
                        stats.puntosVencer
                    }}</span></a
                >
                <a href="/profesor/socializaciones" class="sb-link"
                    ><i class="bi bi-mic"></i> Socializaciones</a
                >
                <a href="/profesor/entregables" class="sb-link"
                    ><i class="bi bi-file-earmark-check"></i> Entregables
                    <span
                        v-if="stats.entregablesPendientes > 0"
                        class="sb-badge"
                        >{{ stats.entregablesPendientes }}</span
                    ></a
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
        <div class="main-prof">
            <div class="topbar-prof">
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
                    <button class="topbar-btn" @click="toggleTema">
                        <i
                            class="bi"
                            :class="
                                tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'
                            "
                        ></i>
                    </button>
                    <button class="topbar-btn">
                        <i class="bi bi-bell"></i>
                        <span v-if="stats.notificaciones > 0" class="nd"></span>
                    </button>
                </div>
            </div>

            <div class="content-prof">
                <!-- STATS -->
                <div class="row g-3 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon si-blue">
                                <i class="bi bi-collection-fill"></i>
                            </div>
                            <div>
                                <div class="stat-num">
                                    {{ stats.secciones }}
                                </div>
                                <div class="stat-label">Mis Secciones</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon si-red">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <div class="stat-num">{{ stats.equipos }}</div>
                                <div class="stat-label">Equipos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon si-orange">
                                <i class="bi bi-flag-fill"></i>
                            </div>
                            <div>
                                <div class="stat-num">
                                    {{ stats.puntosVencer }}
                                </div>
                                <div class="stat-label">Puntos Por Vencer</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon si-green">
                                <i class="bi bi-file-earmark-check-fill"></i>
                            </div>
                            <div>
                                <div class="stat-num">
                                    {{ stats.entregablesPendientes }}
                                </div>
                                <div class="stat-label">
                                    Entregables Pendientes
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <!-- MIS EQUIPOS -->
                    <div class="col-lg-6">
                        <div class="section-card">
                            <div class="section-header">
                                <h6>
                                    <i
                                        class="bi bi-people-fill me-2 text-danger"
                                    ></i
                                    >Mis Equipos
                                </h6>
                                <a
                                    href="/profesor/equipos"
                                    class="btn btn-sm btn-danger rounded-pill px-3 fw-bold"
                                >
                                    <i class="bi bi-plus me-1"></i>Nuevo
                                </a>
                            </div>
                            <div
                                v-if="equipos.length === 0"
                                class="p-3 text-center text-muted small py-4"
                            >
                                Sin equipos asignados
                            </div>
                            <div
                                v-for="equ in equipos"
                                :key="equ.equ_id"
                                class="eq-card"
                            >
                                <div
                                    class="d-flex justify-content-between align-items-start mb-2"
                                >
                                    <div>
                                        <div class="eq-code">
                                            {{ equ.equ_codigo }}
                                        </div>
                                        <div class="eq-title">
                                            {{ equ.equ_titulo }}
                                        </div>
                                    </div>
                                    <span class="bs bp">{{
                                        equ.seccion?.sec_codigo
                                    }}</span>
                                </div>
                                <div class="d-flex flex-wrap mt-1">
                                    <span
                                        v-for="int in equ.integrantes"
                                        :key="int.ein_id"
                                        class="member-chip"
                                    >
                                        <div
                                            class="member-dot"
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
                                                font-size: 0.65rem;
                                            "
                                        ></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- PUNTOS Y ENTREGABLES -->
                    <div class="col-lg-6">
                        <div class="section-card">
                            <div class="section-header">
                                <h6>
                                    <i
                                        class="bi bi-flag-fill me-2 text-danger"
                                    ></i
                                    >Próximos Puntos de Control
                                </h6>
                                <a
                                    href="/profesor/puntos-control"
                                    class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold"
                                    >Ver todos</a
                                >
                            </div>
                            <div
                                v-if="proximosPuntos.length === 0"
                                class="p-3 text-center text-muted small py-4"
                            >
                                Sin puntos próximos
                            </div>
                            <div class="table-responsive">
                                <table
                                    class="ct"
                                    v-if="proximosPuntos.length > 0"
                                >
                                    <thead>
                                        <tr>
                                            <th>Punto</th>
                                            <th>Equipo</th>
                                            <th>Fecha</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="p in proximosPuntos"
                                            :key="p.id"
                                        >
                                            <td class="fw-semibold">
                                                {{ p.nombre }}
                                            </td>
                                            <td>
                                                <span class="bs bp">{{
                                                    p.equipo
                                                }}</span>
                                            </td>
                                            <td
                                                class="fw-bold"
                                                :style="{
                                                    color: p.urgente
                                                        ? '#dc3545'
                                                        : 'var(--text-main)',
                                                }"
                                            >
                                                {{ p.fecha }}
                                            </td>
                                            <td>
                                                <span
                                                    class="bs"
                                                    :class="
                                                        p.urgente ? 'bd' : 'bw'
                                                    "
                                                    >{{
                                                        p.urgente
                                                            ? "Urgente"
                                                            : "Próximo"
                                                    }}</span
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="section-card">
                            <div class="section-header">
                                <h6>
                                    <i
                                        class="bi bi-file-earmark-check-fill me-2 text-danger"
                                    ></i
                                    >Entregables por Revisar
                                </h6>
                                <a
                                    href="/profesor/entregables"
                                    class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold"
                                    >Ver todos</a
                                >
                            </div>
                            <div
                                v-if="entregablesPendientes.length === 0"
                                class="p-3 text-center text-muted small py-4"
                            >
                                Sin entregables pendientes
                            </div>
                            <div class="table-responsive">
                                <table
                                    class="ct"
                                    v-if="entregablesPendientes.length > 0"
                                >
                                    <thead>
                                        <tr>
                                            <th>Archivo</th>
                                            <th>Equipo</th>
                                            <th>Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="e in entregablesPendientes"
                                            :key="e.id"
                                        >
                                            <td class="fw-semibold">
                                                {{ e.nombre }}
                                            </td>
                                            <td>
                                                <span class="bs bp">{{
                                                    e.equipo
                                                }}</span>
                                            </td>
                                            <td>
                                                <span class="bs bw"
                                                    >Pendiente</span
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
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
    stats: {
        type: Object,
        default: () => ({
            secciones: 0,
            equipos: 0,
            puntosVencer: 0,
            entregablesPendientes: 0,
            notificaciones: 0,
        }),
    },
    equipos: { type: Array, default: () => [] },
    proximosPuntos: { type: Array, default: () => [] },
    entregablesPendientes: { type: Array, default: () => [] },
});

const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("prof-theme") || "light");

const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});

const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("prof-theme", tema.value);
};

const logout = () => router.post("/logout");
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
.sb-prof {
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
.sb-badge {
    margin-left: auto;
    background: #dc3545;
    color: #fff;
    font-size: 0.65rem;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 20px;
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
.stat-card {
    background: var(--bg-card);
    border-radius: 13px;
    padding: 1.1rem;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--border);
    transition:
        background 0.3s,
        border-color 0.3s;
}
.stat-icon {
    width: 46px;
    height: 46px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
.si-red {
    background: rgba(220, 53, 69, 0.12);
    color: #dc3545;
}
.si-blue {
    background: rgba(13, 110, 253, 0.12);
    color: #0d6efd;
}
.si-green {
    background: rgba(25, 135, 84, 0.12);
    color: #198754;
}
.si-orange {
    background: rgba(255, 143, 0, 0.12);
    color: #ff8f00;
}
.stat-num {
    font-size: 1.5rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1;
}
.stat-label {
    font-size: 0.78rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-top: 2px;
}
.section-card {
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
.section-header {
    padding: 1rem 1.3rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 8px;
}
.section-header h6 {
    margin: 0;
    font-weight: 800;
    color: var(--text-main);
    font-size: 0.97rem;
}
.ct {
    width: 100%;
    border-collapse: collapse;
}
.ct thead th {
    padding: 0.65rem 0.9rem;
    background: var(--bg-th);
    color: var(--text-muted);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid var(--border);
}
.ct tbody td {
    padding: 0.65rem 0.9rem;
    border-bottom: 1px solid var(--border);
    color: var(--text-main);
    font-size: 0.88rem;
}
.ct tbody tr:hover td {
    background: var(--bg-hover);
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
.eq-card {
    padding: 1rem 1.3rem;
    border-bottom: 1px solid var(--border);
}
.eq-card:last-child {
    border-bottom: none;
}
.eq-code {
    font-weight: 800;
    color: #dc3545;
    font-size: 0.95rem;
}
.eq-title {
    color: var(--text-muted);
    font-size: 0.82rem;
    margin-top: 1px;
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
.member-dot {
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
    padding: 0.85rem 1.3rem;
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
.nt strong {
    color: #dc3545;
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
    .sb-prof {
        transform: translateX(0) !important;
    }
    .main-prof {
        margin-left: 250px;
    }
    .toggle-btn {
        display: none !important;
    }
}
@media (max-width: 991.98px) {
    .sb-prof {
        transform: translateX(-100%);
    }
    .main-prof {
        margin-left: 0;
    }
    .sb-prof.show {
        transform: translateX(0);
    }
    .sb-overlay.show {
        display: block !important;
    }
    .content-prof {
        padding: 1rem;
    }
}
</style>
