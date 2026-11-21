<template>
    <div>
        <div
            class="sb-overlay"
            :class="{ show: sidebarOpen }"
            @click="$emit('close')"
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
                    @click="$emit('close')"
                >
                    <i class="bi bi-x"></i>
                </button>
            </div>

            <div class="sb-user">
                <div class="sb-av">{{ iniciales }}</div>
                <div>
                    <div class="sb-name">
                        {{ $page.props.auth?.user?.usu_primer_nombre }}
                        {{ $page.props.auth?.user?.usu_primer_apellido }}
                    </div>
                    <div class="sb-role">Coordinador PNF</div>
                </div>
            </div>

            <div class="sb-nav">
                <!-- Principal -->
                <div class="sb-sec">Principal</div>
                <Link
                    href="/coordinador"
                    class="sb-lnk"
                    :class="{ active: currentPath === '/coordinador' }"
                >
                    <i class="bi bi-speedometer2"></i> Dashboard
                </Link>
                <Link
                    href="/coordinador/secciones"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/coordinador/secciones') }"
                >
                    <i class="bi bi-collection"></i> Secciones
                </Link>
                <Link
                    href="/coordinador/equipos"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/coordinador/equipos') }"
                >
                    <i class="bi bi-people"></i> Equipos
                </Link>
                <Link
                    href="/coordinador/comunidades"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/coordinador/comunidades') }"
                >
                    <i class="bi bi-building"></i> Comunidades
                </Link>
                <Link
                    href="/coordinador/proyectos"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/coordinador/proyectos') }"
                >
                    <i class="bi bi-folder"></i> Proyectos
                </Link>
                <Link
                    href="/coordinador/socializaciones"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/coordinador/socializaciones') }"
                >
                    <i class="bi bi-mic"></i> Socializaciones
                </Link>
                <Link
                    href="/evaluaciones"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/evaluaciones') }"
                >
                    <i class="bi bi-journal-check"></i> Rúbricas Oficiales
                </Link>
                <Link
                    href="/coordinador/resultados"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/coordinador/resultados') }"
                >
                    <i class="bi bi-trophy"></i> Resultados
                </Link>
                <Link
                    href="/coordinador/transicion-semestral"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/coordinador/transicion-semestral') }"
                >
                    <i class="bi bi-arrow-left-right"></i> Transición Semestral
                </Link>

                <!-- Tablas Maestras -->
                <div
                    class="sb-sec sb-toggle"
                    @click="menuMaestras = !menuMaestras"
                    style="
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding-right: 1.2rem;
                    "
                >
                    <span>Tablas Maestras</span>
                    <i
                        class="bi"
                        :class="menuMaestras ? 'bi-chevron-up' : 'bi-chevron-down'"
                        style="font-size: 0.75rem"
                    ></i>
                </div>
                <div v-show="menuMaestras">
                    <Link
                        href="/coordinador/tipos-proyecto"
                        class="sb-lnk"
                        :class="{ active: currentPath === '/coordinador/tipos-proyecto' }"
                    >
                        <i class="bi bi-tag"></i> Tipos de Proyecto
                    </Link>
                    <Link
                        href="/coordinador/maestras/modalidades"
                        class="sb-lnk"
                        :class="{ active: currentPath.includes('/modalidades') }"
                    >
                        <i class="bi bi-layers"></i> Modalidades
                    </Link>
                    <Link
                        href="/coordinador/maestras/tipos-jurado"
                        class="sb-lnk"
                        :class="{ active: currentPath.includes('/tipos-jurado') }"
                    >
                        <i class="bi bi-person-badge"></i> Tipos de Jurado
                    </Link>
                    <Link
                        href="/coordinador/maestras/tipos-beneficiario"
                        class="sb-lnk"
                        :class="{ active: currentPath.includes('/tipos-beneficiario') }"
                    >
                        <i class="bi bi-people"></i> Tipos Beneficiario
                    </Link>
                    <Link
                        href="/coordinador/maestras/tipos-entregable"
                        class="sb-lnk"
                        :class="{ active: currentPath.includes('/tipos-entregable') }"
                    >
                        <i class="bi bi-file-earmark"></i> Tipos Entregable
                    </Link>
                    <Link
                        href="/coordinador/maestras/tipos-evento-equipo"
                        class="sb-lnk"
                        :class="{ active: currentPath.includes('/tipos-evento-equipo') }"
                    >
                        <i class="bi bi-calendar-event"></i> Eventos Equipo
                    </Link>
                    <Link
                        href="/coordinador/maestras/turnos"
                        class="sb-lnk"
                        :class="{ active: currentPath.includes('/turnos') }"
                    >
                        <i class="bi bi-clock"></i> Turnos
                    </Link>
                    <Link
                        href="/coordinador/maestras/estados-proyecto"
                        class="sb-lnk"
                        :class="{ active: currentPath.includes('/estados-proyecto') }"
                    >
                        <i class="bi bi-award"></i> Estados Proyecto
                    </Link>
                </div>

                <!-- Sistema -->
                <div
                    class="sb-sec sb-toggle"
                    @click="menuSistema = !menuSistema"
                    style="
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding-right: 1.2rem;
                    "
                >
                    <span>Sistema</span>
                    <i
                        class="bi"
                        :class="menuSistema ? 'bi-chevron-up' : 'bi-chevron-down'"
                        style="font-size: 0.75rem"
                    ></i>
                </div>
                <div v-show="menuSistema">
                    <Link
                        href="/coordinador/reportes"
                        class="sb-lnk"
                        :class="{ active: currentPath.startsWith('/coordinador/reportes') }"
                    >
                        <i class="bi bi-bar-chart"></i> Reportes
                    </Link>
                    <Link
                        href="/coordinador/cartas"
                        class="sb-lnk"
                        :class="{ active: currentPath.startsWith('/coordinador/cartas') }"
                    >
                        <i class="bi bi-file-earmark-text"></i> Cartas
                    </Link>
                    <Link
                        href="/coordinador/estudiantes/buscar"
                        class="sb-lnk"
                        :class="{ active: currentPath.startsWith('/coordinador/estudiantes') }"
                    >
                        <i class="bi bi-person-badge"></i> Expediente Estudiantes
                    </Link>
                    <Link
                        href="/coordinador/bitacora"
                        class="sb-lnk"
                        :class="{ active: currentPath.startsWith('/coordinador/bitacora') }"
                    >
                        <i class="bi bi-journal-text"></i> Bitácora
                    </Link>
                    <Link
                        href="/coordinador/configuracion"
                        class="sb-lnk"
                        :class="{ active: currentPath.startsWith('/coordinador/configuracion') }"
                    >
                        <i class="bi bi-gear"></i> Configuración
                    </Link>
                </div>

                <!-- Cerrar Sesión -->
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
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

defineProps({
    sidebarOpen: { type: Boolean, default: false },
});

defineEmits(["close"]);

const page = usePage();
const currentPath = computed(() => {
    return page.url.split("?")[0];
});

const menuMaestras = ref(
    page.url.includes("/maestras/") || page.url.includes("/tipos-proyecto")
);
const menuSistema = ref(
    page.url.includes("/reportes") ||
    page.url.includes("/cartas") ||
    page.url.includes("/estudiantes") ||
    page.url.includes("/bitacora") ||
    page.url.includes("/configuracion")
);

const iniciales = computed(() => {
    const u = page.props.auth?.user;
    if (!u) return "C";
    return (
        (u.usu_primer_nombre?.[0] || "") + (u.usu_primer_apellido?.[0] || "")
    ).toUpperCase();
});

const logout = () => {
    router.post("/logout");
};
</script>

<style scoped>
.sb {
    position: fixed;
    top: 0;
    left: 0;
    width: 250px;
    height: 100vh;
    background: #1a1d23;
    display: flex;
    flex-direction: column;
    z-index: 1040;
    box-shadow: 3px 0 15px rgba(0, 0, 0, 0.15);
    border-right: 2px solid rgba(220, 53, 69, 0.25);
    transition: transform 0.3s ease;
    overflow-y: auto;
}
.sb-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1030;
    display: none;
}
.sb-overlay.show {
    display: block;
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
    line-height: 1.2;
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
    color: #fff;
    font-weight: 800;
    font-size: 0.85rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.sb-name {
    font-weight: 700;
    font-size: 0.85rem;
    color: #fff;
    line-height: 1.2;
}
.sb-role {
    font-size: 0.72rem;
    color: rgba(255, 255, 255, 0.5);
}
.sb-nav {
    padding: 0.75rem 0;
    flex: 1;
}
.sb-sec {
    padding: 0.6rem 1.4rem 0.2rem;
    font-size: 0.68rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: rgba(255, 255, 255, 0.35);
}
.sb-lnk {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 0.55rem 1.4rem;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.15s ease;
    border-left: 3px solid transparent;
}
.sb-lnk:hover {
    color: #fff;
    background: rgba(255, 255, 255, 0.06);
}
.sb-lnk.active {
    color: #ff4d5e;
    background: rgba(220, 53, 69, 0.12);
    border-left-color: #dc3545;
    font-weight: 700;
}
.sb-lnk i {
    font-size: 0.95rem;
    width: 18px;
    text-align: center;
}
@media (max-width: 991.98px) {
    .sb {
        transform: translateX(-100%);
    }
    .sb.show {
        transform: translateX(0);
    }
}
</style>
