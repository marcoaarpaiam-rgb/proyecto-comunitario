<template>
    <div
        style="
            min-height: 100vh;
            background: #0d0e11;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: &quot;Segoe UI&quot;, system-ui, sans-serif;
            padding: 2rem;
        "
    >
        <div style="text-align: center; max-width: 420px">
            <!-- Logo -->
            <div
                style="
                    display: inline-flex;
                    align-items: center;
                    gap: 10px;
                    margin-bottom: 2.5rem;
                "
            >
                <div
                    style="
                        width: 40px;
                        height: 40px;
                        background: #dc3545;
                        border-radius: 10px;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 1.2rem;
                    "
                >
                    💻
                </div>
                <div>
                    <div style="font-weight: 800; font-size: 1rem; color: #fff">
                        UPTP — Proyectos
                    </div>
                    <div
                        style="
                            font-size: 0.72rem;
                            font-weight: 700;
                            color: #ff4d5e;
                        "
                    >
                        PNF Informática
                    </div>
                </div>
            </div>

            <!-- Código de error -->
            <div
                style="
                    font-size: 6rem;
                    font-weight: 900;
                    color: #dc3545;
                    line-height: 1;
                    letter-spacing: -4px;
                    margin-bottom: 0.5rem;
                "
            >
                403
            </div>

            <!-- Mensaje -->
            <h2
                style="
                    color: #f1f3f5;
                    font-weight: 800;
                    font-size: 1.35rem;
                    margin: 0 0 0.75rem;
                "
            >
                Acceso Denegado
            </h2>
            <p
                style="
                    color: #94a3b8;
                    font-size: 0.95rem;
                    line-height: 1.6;
                    margin: 0 0 2rem;
                "
            >
                No tienes permiso para ver esta página. Si crees que esto es un
                error, contacta al coordinador del sistema.
            </p>

            <!-- Info de usuario -->
            <div
                v-if="$page?.props?.auth?.user"
                style="
                    background: #1e2128;
                    border: 1px solid #2d3139;
                    border-radius: 12px;
                    padding: 1rem;
                    margin-bottom: 2rem;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                "
            >
                <div
                    style="
                        width: 38px;
                        height: 38px;
                        border-radius: 50%;
                        background: #dc3545;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        color: #fff;
                        font-weight: 800;
                        font-size: 0.85rem;
                        flex-shrink: 0;
                    "
                >
                    {{ iniciales }}
                </div>
                <div style="text-align: left">
                    <div
                        style="
                            color: #f1f3f5;
                            font-weight: 700;
                            font-size: 0.9rem;
                        "
                    >
                        {{ $page.props.auth.user.usu_primer_nombre }}
                        {{ $page.props.auth.user.usu_primer_apellido }}
                    </div>
                    <div style="color: #64748b; font-size: 0.78rem">
                        Rol actual:
                        <span style="color: #94a3b8; font-weight: 600">{{
                            rolNombre
                        }}</span>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div
                style="
                    display: flex;
                    gap: 0.75rem;
                    justify-content: center;
                    flex-wrap: wrap;
                "
            >
                <a
                    :href="dashboardUrl"
                    style="
                        display: inline-flex;
                        align-items: center;
                        gap: 6px;
                        background: #dc3545;
                        color: #fff;
                        text-decoration: none;
                        padding: 0.6rem 1.4rem;
                        border-radius: 8px;
                        font-weight: 700;
                        font-size: 0.9rem;
                        transition: background 0.15s;
                    "
                    @mouseover="(e) => (e.target.style.background = '#bb2d3b')"
                    @mouseout="(e) => (e.target.style.background = '#dc3545')"
                >
                    <i class="bi bi-house-fill"></i> Ir a mi Dashboard
                </a>
                <a
                    href="javascript:history.back()"
                    style="
                        display: inline-flex;
                        align-items: center;
                        gap: 6px;
                        background: #1e2128;
                        color: #94a3b8;
                        text-decoration: none;
                        padding: 0.6rem 1.4rem;
                        border-radius: 8px;
                        font-weight: 700;
                        font-size: 0.9rem;
                        border: 1px solid #2d3139;
                    "
                >
                    <i class="bi bi-arrow-left"></i> Volver
                </a>
            </div>

            <!-- Footer -->
            <div style="margin-top: 3rem; color: #374151; font-size: 0.78rem">
                Sistema de Seguimiento de Proyectos Comunitarios · UPTP
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();
const user = computed(() => page.props.auth?.user);

const iniciales = computed(() => {
    const u = user.value;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});

const rolNombre = computed(() => {
    const roles = {
        coordinador: "Coordinador PNF",
        profesor: "Profesor de Proyecto",
        lider: "Líder de Equipo",
    };
    return roles[user.value?.usu_rol] || user.value?.usu_rol || "Sin rol";
});

const dashboardUrl = computed(() => {
    const rutas = {
        coordinador: "/coordinador",
        profesor: "/profesor",
        lider: "/lider",
    };
    return rutas[user.value?.usu_rol] || "/login";
});
</script>
