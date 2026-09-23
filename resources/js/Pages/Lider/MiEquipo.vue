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
                <a href="/lider/equipo" class="sb-lnk active"
                    ><i class="bi bi-people"></i> Mi Equipo</a
                >
                <a href="/lider/proyecto" class="sb-lnk"
                    ><i class="bi bi-building"></i> Mi Comunidad</a
                >
                <div class="sb-sec">Acciones</div>
                <a href="/lider/entregables" class="sb-lnk"
                    ><i class="bi bi-cloud-upload"></i> Entregables</a
                >
                <a href="/lider/carta" class="sb-lnk"
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
                            class="bi bi-people me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Mi Equipo
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
                        Mi Equipo
                    </h4>
                    <p
                        style="
                            color: var(--text-muted);
                            font-size: 0.85rem;
                            margin-top: 2px;
                        "
                    >
                        Integrantes del equipo de proyecto
                    </p>
                </div>

                <div v-if="!equipo" class="sc">
                    <div
                        class="p-5 text-center"
                        style="color: var(--text-muted)"
                    >
                        <i class="bi bi-people fs-1 d-block mb-3"></i>No tienes
                        equipo asignado
                    </div>
                </div>

                <div v-else>
                    <div class="sc mb-3">
                        <div class="sh">
                            <h6>
                                <i
                                    class="bi bi-info-circle text-danger me-2"
                                ></i
                                >Datos del Equipo
                            </h6>
                        </div>
                        <div class="p-3">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <div
                                        style="
                                            font-size: 0.78rem;
                                            color: var(--text-muted);
                                            font-weight: 700;
                                        "
                                    >
                                        Código
                                    </div>
                                    <div
                                        class="fw-bold"
                                        style="color: var(--rojo)"
                                    >
                                        {{ equipo.equ_codigo }}
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div
                                        style="
                                            font-size: 0.78rem;
                                            color: var(--text-muted);
                                            font-weight: 700;
                                        "
                                    >
                                        Título
                                    </div>
                                    <div
                                        class="fw-semibold"
                                        style="font-size: 0.9rem"
                                    >
                                        {{ equipo.equ_titulo }}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div
                                        style="
                                            font-size: 0.78rem;
                                            color: var(--text-muted);
                                            font-weight: 700;
                                        "
                                    >
                                        Sección
                                    </div>
                                    <div>
                                        <span
                                            class="bs"
                                            style="
                                                background: rgba(
                                                    13,
                                                    110,
                                                    253,
                                                    0.1
                                                );
                                                color: #055160;
                                                border: 1px solid
                                                    rgba(13, 110, 253, 0.2);
                                            "
                                            >{{
                                                equipo.seccion?.sec_codigo
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div
                                        style="
                                            font-size: 0.78rem;
                                            color: var(--text-muted);
                                            font-weight: 700;
                                        "
                                    >
                                        Trayecto
                                    </div>
                                    <div style="font-size: 0.88rem">
                                        {{ equipo.trayecto?.tra_nombre }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sc">
                        <div class="sh">
                            <h6>
                                <i
                                    class="bi bi-people-fill text-danger me-2"
                                ></i
                                >Integrantes
                                <span
                                    class="ms-2"
                                    style="
                                        font-size: 0.82rem;
                                        color: var(--text-muted);
                                        font-weight: 500;
                                    "
                                    >({{ equipo.integrantes?.length || 0 }} /
                                    6)</span
                                >
                            </h6>
                        </div>
                        <div
                            v-for="int in equipo.integrantes"
                            :key="int.ein_id"
                            class="miembro-card"
                        >
                            <div
                                class="av"
                                :style="{
                                    background: int.ein_es_lider
                                        ? '#dc3545'
                                        : '#495057',
                                }"
                            >
                                {{ int.usuario?.usu_primer_nombre?.[0]
                                }}{{ int.usuario?.usu_primer_apellido?.[0] }}
                            </div>
                            <div class="flex-grow-1">
                                <div
                                    class="fw-semibold"
                                    style="font-size: 0.92rem"
                                >
                                    {{ int.usuario?.usu_primer_nombre }}
                                    {{ int.usuario?.usu_primer_apellido }}
                                </div>
                                <div
                                    style="
                                        font-size: 0.78rem;
                                        color: var(--text-muted);
                                    "
                                >
                                    CI: {{ int.usuario?.usu_cedula }}
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span v-if="int.ein_es_lider" class="bs bd"
                                    ><i
                                        class="bi bi-star-fill me-1"
                                        style="font-size: 0.65rem"
                                    ></i
                                    >Líder</span
                                >
                                <span v-else class="bs bsec">Integrante</span>
                                <span
                                    v-if="int.usuario?.usu_tiene_acceso"
                                    class="bs ba"
                                    style="font-size: 0.68rem"
                                    ><i class="bi bi-check me-1"></i>Con
                                    acceso</span
                                >
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
const props = defineProps({ equipo: Object });
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
.bd {
    background: #f8d7da;
    color: #842029;
    border: 1px solid #f5c2c7;
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
[data-theme="dark"] .bd {
    background: rgba(220, 53, 69, 0.25);
    color: #f8d7da;
    border-color: rgba(220, 53, 69, 0.4);
}
[data-theme="dark"] .bsec {
    background: #252830;
    color: #f1f3f5;
    border-color: #2d3139;
}
.miembro-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0.85rem 1.3rem;
    border-bottom: 1px solid var(--border);
}
.miembro-card:last-child {
    border-bottom: none;
}
.av {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.85rem;
    color: #fff;
    flex-shrink: 0;
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
