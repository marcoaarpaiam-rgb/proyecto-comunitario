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
        <!-- Sidebar -->
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
            </div>
            <div class="sb-user">
                <div class="sb-av">{{ iniciales }}</div>
                <div>
                    <div class="sb-name">
                        {{ $page.props.auth.user.usu_primer_nombre }}
                        {{ $page.props.auth.user.usu_primer_apellido }}
                    </div>
                    <div class="sb-role">Profesor de Proyecto</div>
                </div>
            </div>
            <div class="sb-sec">Principal</div>
            <Link href="/profesor" class="sb-lnk"
                ><i class="bi bi-speedometer2"></i> Dashboard</Link
            >
            <Link href="/profesor/secciones" class="sb-lnk active"
                ><i class="bi bi-collection"></i> Mis Secciones</Link
            >
            <Link href="/profesor/puntos-control" class="sb-lnk"
                ><i class="bi bi-check2-square"></i> Puntos de Control</Link
            >
            <Link href="/profesor/socializaciones" class="sb-lnk"
                ><i class="bi bi-mic"></i> Socializaciones</Link
            >
            <Link href="/profesor/entregables" class="sb-lnk"
                ><i class="bi bi-file-earmark-arrow-up"></i> Entregables</Link
            >

            <div class="sb-sec" style="margin-top: auto"></div>
            <form @submit.prevent="cerrarSesion">
                <button
                    type="submit"
                    class="sb-lnk w-100 text-start border-0 bg-transparent"
                    style="color: var(--text-muted)"
                >
                    <i class="bi bi-box-arrow-left"></i> Cerrar sesión
                </button>
            </form>
        </div>

        <!-- Topbar -->
        <div class="topbar">
            <button
                class="btn btn-sm d-lg-none"
                @click="sidebarOpen = true"
                style="color: var(--text-main)"
            >
                <i class="bi bi-list fs-4"></i>
            </button>
            <div class="ms-auto d-flex align-items:center gap-3">
                <button
                    @click="toggleTema"
                    class="btn btn-sm"
                    style="color: var(--text-muted)"
                >
                    <i
                        class="bi"
                        :class="tema === 'dark' ? 'bi-sun' : 'bi-moon'"
                    ></i>
                </button>
            </div>
        </div>

        <!-- Contenido -->
        <div class="main-content">
            <div class="d-flex align-items-center gap-3 mb-4">
                <div>
                    <h4 style="font-weight: 800; margin: 0">Mis Secciones</h4>
                    <p
                        style="
                            color: var(--text-muted);
                            font-size: 0.9rem;
                            margin: 0;
                        "
                    >
                        Selecciona una sección para ver y gestionar sus equipos
                    </p>
                </div>
            </div>

            <!-- Sin secciones -->
            <div
                v-if="!secciones.length"
                style="text-align: center; padding: 4rem 2rem"
            >
                <div style="font-size: 3rem; margin-bottom: 1rem">📋</div>
                <h6 style="font-weight: 700; color: var(--text-main)">
                    No tienes secciones asignadas
                </h6>
                <p style="color: var(--text-muted); font-size: 0.9rem">
                    Contacta al coordinador para que te asigne una sección.
                </p>
            </div>

            <!-- Grid de secciones -->
            <div v-else class="row g-3">
                <div
                    v-for="sec in secciones"
                    :key="sec.sec_id"
                    class="col-12 col-md-6 col-lg-4"
                >
                    <div
                        style="
                            background: var(--bg-card);
                            border: 1px solid var(--border);
                            border-radius: 14px;
                            padding: 1.5rem;
                            transition: box-shadow 0.2s;
                        "
                        @mouseover="
                            (e) =>
                                (e.currentTarget.style.boxShadow =
                                    '0 4px 20px rgba(220,53,69,.15)')
                        "
                        @mouseout="
                            (e) => (e.currentTarget.style.boxShadow = 'none')
                        "
                    >
                        <!-- Header -->
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div
                                style="
                                    width: 48px;
                                    height: 48px;
                                    background: #dc3545;
                                    border-radius: 12px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    color: #fff;
                                    font-weight: 900;
                                    font-size: 1.1rem;
                                    flex-shrink: 0;
                                "
                            >
                                {{ sec.sec_codigo }}
                            </div>
                            <div>
                                <div
                                    style="
                                        font-weight: 800;
                                        font-size: 1rem;
                                        color: var(--text-main);
                                    "
                                >
                                    Sección {{ sec.sec_codigo }}
                                </div>
                                <div
                                    style="
                                        font-size: 0.8rem;
                                        color: var(--text-muted);
                                    "
                                >
                                    {{ sec.pnf }} — {{ sec.trayecto }}
                                </div>
                            </div>
                        </div>

                        <!-- Info -->
                        <div class="d-flex gap-3 mb-3">
                            <div
                                style="
                                    flex: 1;
                                    background: var(--bg-th);
                                    border-radius: 8px;
                                    padding: 0.6rem;
                                    text-align: center;
                                "
                            >
                                <div
                                    style="
                                        font-size: 1.4rem;
                                        font-weight: 900;
                                        color: #dc3545;
                                    "
                                >
                                    {{ sec.total_equipos }}
                                </div>
                                <div
                                    style="
                                        font-size: 0.75rem;
                                        color: var(--text-muted);
                                    "
                                >
                                    Equipos
                                </div>
                            </div>
                            <div
                                style="
                                    flex: 1;
                                    background: var(--bg-th);
                                    border-radius: 8px;
                                    padding: 0.6rem;
                                    text-align: center;
                                "
                            >
                                <div
                                    style="
                                        font-size: 1.4rem;
                                        font-weight: 900;
                                        color: #0d6efd;
                                    "
                                >
                                    {{ sec.total_estudiantes }}
                                </div>
                                <div
                                    style="
                                        font-size: 0.75rem;
                                        color: var(--text-muted);
                                    "
                                >
                                    Estudiantes
                                </div>
                            </div>
                            <div
                                style="
                                    flex: 1;
                                    background: var(--bg-th);
                                    border-radius: 8px;
                                    padding: 0.6rem;
                                    text-align: center;
                                "
                            >
                                <div
                                    style="
                                        font-size: 0.85rem;
                                        font-weight: 700;
                                        color: #198754;
                                    "
                                >
                                    {{ sec.turno }}
                                </div>
                                <div
                                    style="
                                        font-size: 0.75rem;
                                        color: var(--text-muted);
                                    "
                                >
                                    Turno
                                </div>
                            </div>
                        </div>

                        <!-- Botón -->
                        <Link
                            :href="`/profesor/secciones/${sec.sec_id}/equipos`"
                            style="
                                display: block;
                                text-align: center;
                                background: #dc3545;
                                color: #fff;
                                border-radius: 8px;
                                padding: 0.55rem;
                                font-weight: 700;
                                font-size: 0.9rem;
                                text-decoration: none;
                            "
                        >
                            <i class="bi bi-people-fill me-1"></i> Ver Equipos
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

const props = defineProps({ secciones: Array });
const page = usePage();
const tema = ref(localStorage.getItem("prof-theme") || "light");
const sidebarOpen = ref(false);

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

const cerrarSesion = () => router.post("/logout");
</script>
<style scoped>
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
}
.sb {
    position: fixed;
    top: 0;
    left: 0;
    width: 240px;
    height: 100vh;
    background: var(--bg-sidebar);
    overflow-y: auto;
    z-index: 1000;
    display: flex;
    flex-direction: column;
    padding: 1rem 0;
}
.sb-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.75rem 1.2rem 1rem;
}
.sb-title {
    font-weight: 800;
    font-size: 0.9rem;
    color: #fff;
}
.sb-sub {
    font-size: 0.7rem;
    font-weight: 700;
    color: #ff4d5e;
}
.sb-user {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.75rem 1.2rem;
    margin-bottom: 0.5rem;
    border-top: 1px solid #2d3139;
    border-bottom: 1px solid #2d3139;
}
.sb-av {
    width: 34px;
    height: 34px;
    border-radius: 50%;
    background: #dc3545;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-weight: 800;
    font-size: 0.8rem;
    flex-shrink: 0;
}
.sb-name {
    font-size: 0.82rem;
    font-weight: 700;
    color: #f1f3f5;
}
.sb-role {
    font-size: 0.7rem;
    color: #94a3b8;
}
.sb-sec {
    padding: 0.5rem 1.2rem 0.25rem;
    font-size: 0.68rem;
    font-weight: 800;
    color: #4a5568;
    text-transform: uppercase;
    letter-spacing: 0.08em;
}
.sb-lnk {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.55rem 1.2rem;
    font-size: 0.85rem;
    font-weight: 600;
    color: #94a3b8;
    text-decoration: none;
    transition: all 0.15s;
}
.sb-lnk:hover,
.sb-lnk.active {
    color: #fff;
    background: rgba(220, 53, 69, 0.15);
    border-right: 3px solid #dc3545;
}
.topbar {
    position: fixed;
    top: 0;
    left: 240px;
    right: 0;
    height: 56px;
    background: var(--bg-topbar);
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    padding: 0 1.5rem;
    z-index: 999;
}
.main-content {
    margin-left: 240px;
    padding: 80px 1.5rem 2rem;
    min-height: 100vh;
    background: var(--bg-body);
}
@media (max-width: 991px) {
    .sb {
        transform: translateX(-100%);
        transition: transform 0.3s;
    }
    .sb.show {
        transform: translateX(0);
    }
    .topbar {
        left: 0;
    }
    .main-content {
        margin-left: 0;
    }
}
</style>
