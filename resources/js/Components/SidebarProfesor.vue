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
                    <div class="sb-role">Profesor de Proyecto</div>
                </div>
            </div>

            <div class="sb-nav">
                <div class="sb-sec">Principal</div>
                <Link
                    href="/profesor"
                    class="sb-lnk"
                    :class="{ active: currentPath === '/profesor' }"
                >
                    <i class="bi bi-speedometer2"></i> Dashboard
                </Link>
                <Link
                    href="/profesor/secciones"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/profesor/secciones') }"
                >
                    <i class="bi bi-collection"></i> Mis Secciones
                </Link>
                <Link
                    href="/profesor/equipos"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/profesor/equipos') }"
                >
                    <i class="bi bi-people"></i> Mis Equipos
                </Link>

                <div class="sb-sec">Gestión y Seguimiento</div>
                <Link
                    href="/profesor/puntos-control"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/profesor/puntos-control') }"
                >
                    <i class="bi bi-flag"></i> Puntos de Control
                </Link>
                <Link
                    href="/profesor/esquemas-evaluacion"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/profesor/esquemas-evaluacion') }"
                >
                    <i class="bi bi-sliders"></i> Esquemas de Evaluación
                </Link>
                <Link
                    href="/evaluaciones"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/evaluaciones') }"
                >
                    <i class="bi bi-journal-check"></i> Rúbricas Oficiales
                </Link>
                <Link
                    href="/profesor/socializaciones"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/profesor/socializaciones') }"
                >
                    <i class="bi bi-mic"></i> Socializaciones
                </Link>
                <Link
                    href="/profesor/entregables"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/profesor/entregables') }"
                >
                    <i class="bi bi-file-earmark-check"></i> Entregables
                </Link>
                <Link
                    href="/profesor/estudiantes/buscar"
                    class="sb-lnk"
                    :class="{ active: currentPath.startsWith('/profesor/estudiantes') }"
                >
                    <i class="bi bi-person-badge"></i> Expediente Estudiantes
                </Link>

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
import { computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";

defineProps({
    sidebarOpen: { type: Boolean, default: false },
});

defineEmits(["close"]);

const page = usePage();

const currentPath = computed(() => {
    return page.url.split("?")[0];
});

const iniciales = computed(() => {
    const u = page.props.auth?.user;
    if (!u) return "P";
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
