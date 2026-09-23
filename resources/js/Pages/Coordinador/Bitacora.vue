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
                    <div class="sb-role">Coordinador PNF</div>
                </div>
            </div>
            <div class="sb-nav">
                <div class="sb-sec">Principal</div>
                <a href="/coordinador" class="sb-lnk"
                    ><i class="bi bi-speedometer2"></i> Dashboard</a
                >
                <a href="/coordinador/secciones" class="sb-lnk"
                    ><i class="bi bi-collection"></i> Secciones</a
                >
                <a href="/coordinador/comunidades" class="sb-lnk"
                    ><i class="bi bi-building"></i> Comunidades</a
                >
                <a href="/coordinador/equipos" class="sb-lnk"
                    ><i class="bi bi-people"></i> Equipos</a
                >
                <a href="/coordinador/proyectos" class="sb-lnk"
                    ><i class="bi bi-folder"></i> Proyectos</a
                >
                <div class="sb-sec">Tablas Maestras</div>
                <a href="/coordinador/tipos-proyecto" class="sb-lnk"
                    ><i class="bi bi-tag"></i> Tipos de Proyecto</a
                >
                <a href="/coordinador/maestras/modalidades" class="sb-lnk"
                    ><i class="bi bi-layers"></i> Modalidades</a
                >
                <a href="/coordinador/maestras/tipos-jurado" class="sb-lnk"
                    ><i class="bi bi-person-badge"></i> Tipos de Jurado</a
                >
                <a
                    href="/coordinador/maestras/tipos-beneficiario"
                    class="sb-lnk"
                    ><i class="bi bi-people"></i> Tipos Beneficiario</a
                >
                <a href="/coordinador/maestras/tipos-entregable" class="sb-lnk"
                    ><i class="bi bi-file-earmark"></i> Tipos Entregable</a
                >
                <a
                    href="/coordinador/maestras/tipos-evento-equipo"
                    class="sb-lnk"
                    ><i class="bi bi-calendar-event"></i> Eventos Equipo</a
                >
                <a href="/coordinador/maestras/turnos" class="sb-lnk"
                    ><i class="bi bi-clock"></i> Turnos</a
                >
                <a href="/coordinador/maestras/estados-proyecto" class="sb-lnk"
                    ><i class="bi bi-award"></i> Estados Proyecto</a
                >
                <div class="sb-sec">Académico</div>
                <a href="/coordinador/socializaciones" class="sb-lnk"
                    ><i class="bi bi-mic"></i> Socializaciones</a
                >
                <a href="/coordinador/resultados" class="sb-lnk"
                    ><i class="bi bi-trophy"></i> Resultados</a
                >
                <div class="sb-sec">Sistema</div>
                <a href="/coordinador/reportes" class="sb-lnk"
                    ><i class="bi bi-bar-chart"></i> Reportes</a
                >
                <a href="/coordinador/cartas" class="sb-lnk"
                    ><i class="bi bi-file-earmark-text"></i> Cartas</a
                >
                <a href="/coordinador/bitacora" class="sb-lnk active"
                    ><i class="bi bi-journal-text"></i> Bitácora</a
                >
                <a href="/coordinador/configuracion" class="sb-lnk"
                    ><i class="bi bi-gear"></i> Configuración</a
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
                            class="bi bi-journal-text me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Bitácora del Sistema
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
                        Bitácora
                    </h4>
                    <p
                        style="
                            color: var(--text-muted);
                            font-size: 0.85rem;
                            margin-top: 2px;
                        "
                    >
                        Registro inmutable de todas las acciones del sistema
                    </p>
                </div>

                <!-- Filtros -->
                <div class="filter-box">
                    <div class="row g-2 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label fw-semibold"
                                >Buscar acción</label
                            >
                            <input
                                v-model="filtros.busqueda"
                                type="text"
                                class="form-control"
                                placeholder="Ej: CREAR en equipo"
                            />
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold"
                                >Entidad</label
                            >
                            <select
                                v-model="filtros.entidad_id"
                                class="form-select"
                            >
                                <option value="">Todas</option>
                                <option
                                    v-for="ent in entidades"
                                    :key="ent.ent_id"
                                    :value="ent.ent_id"
                                >
                                    {{ ent.ent_nombre }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold"
                                >Usuario</label
                            >
                            <select
                                v-model="filtros.usuario_id"
                                class="form-select"
                            >
                                <option value="">Todos</option>
                                <option
                                    v-for="usu in usuarios"
                                    :key="usu.usu_id"
                                    :value="usu.usu_id"
                                >
                                    {{ usu.usu_primer_nombre }}
                                    {{ usu.usu_primer_apellido }}
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Desde</label>
                            <input
                                v-model="filtros.fecha_desde"
                                type="date"
                                class="form-control"
                            />
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-semibold">Hasta</label>
                            <input
                                v-model="filtros.fecha_hasta"
                                type="date"
                                class="form-control"
                            />
                        </div>
                        <div class="col-md-1 d-flex gap-1">
                            <button
                                class="btn btn-danger btn-sm w-100"
                                @click="buscar"
                            >
                                <i class="bi bi-search"></i>
                            </button>
                            <button
                                class="btn btn-outline-secondary btn-sm w-100"
                                @click="limpiar"
                            >
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="sc">
                    <div class="table-responsive">
                        <table class="ct">
                            <thead>
                                <tr>
                                    <th class="ps-4">Fecha y Hora</th>
                                    <th>Usuario</th>
                                    <th>Acción</th>
                                    <th>Entidad</th>
                                    <th>ID</th>
                                    <th class="text-end pe-4">Detalle</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="registros.data.length === 0">
                                    <td
                                        colspan="6"
                                        class="text-center py-5"
                                        style="color: var(--text-muted)"
                                    >
                                        Sin registros
                                    </td>
                                </tr>
                                <tr
                                    v-for="bit in registros.data"
                                    :key="bit.bit_id"
                                >
                                    <td
                                        class="ps-4"
                                        style="
                                            white-space: nowrap;
                                            color: var(--text-muted);
                                            font-size: 0.8rem;
                                        "
                                    >
                                        {{
                                            new Date(
                                                bit.bit_fecha_hora,
                                            ).toLocaleString("es-VE")
                                        }}
                                    </td>
                                    <td
                                        class="fw-semibold"
                                        style="font-size: 0.85rem"
                                    >
                                        {{ bit.usuario?.usu_primer_nombre }}
                                        {{ bit.usuario?.usu_primer_apellido }}
                                    </td>
                                    <td>
                                        <span
                                            class="bs"
                                            :class="badgeAccion(bit.bit_accion)"
                                            >{{ bit.bit_accion }}</span
                                        >
                                    </td>
                                    <td>
                                        <span
                                            v-if="bit.entidad"
                                            class="bs ba-o"
                                            >{{ bit.entidad?.ent_nombre }}</span
                                        >
                                    </td>
                                    <td
                                        style="
                                            color: var(--text-muted);
                                            font-size: 0.82rem;
                                        "
                                    >
                                        {{ bit.bit_registro_id || "—" }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <button
                                            v-if="
                                                bit.bit_estado_anterior ||
                                                bit.bit_estado_nuevo
                                            "
                                            class="btn btn-sm btn-outline-secondary"
                                            @click="verDetalle(bit)"
                                        >
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        style="
                            padding: 0.75rem 1rem;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                            border-top: 1px solid var(--border);
                        "
                    >
                        <span
                            style="font-size: 0.82rem; color: var(--text-muted)"
                            >Mostrando {{ registros.from }}–{{
                                registros.to
                            }}
                            de {{ registros.total }}</span
                        >
                        <div class="d-flex gap-1">
                            <a
                                v-if="registros.prev_page_url"
                                :href="registros.prev_page_url"
                                class="btn btn-sm btn-outline-secondary"
                                ><i class="bi bi-chevron-left"></i
                            ></a>
                            <span class="btn btn-sm btn-danger disabled">{{
                                registros.current_page
                            }}</span>
                            <a
                                v-if="registros.next_page_url"
                                :href="registros.next_page_url"
                                class="btn btn-sm btn-outline-secondary"
                                ><i class="bi bi-chevron-right"></i
                            ></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal detalle -->
        <div class="modal fade" id="mDetalle" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>Detalle del Registro</h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <h6
                                    class="fw-bold small mb-2"
                                    style="color: var(--text-muted)"
                                >
                                    ESTADO ANTERIOR
                                </h6>
                                <pre
                                    style="
                                        background: var(--bg-th);
                                        padding: 0.75rem;
                                        border-radius: 8px;
                                        font-size: 0.72rem;
                                        max-height: 280px;
                                        overflow-y: auto;
                                        color: var(--text-main);
                                        border: 1px solid var(--border);
                                    "
                                    >{{ anterior }}</pre
                                >
                            </div>
                            <div class="col-md-6">
                                <h6
                                    class="fw-bold small mb-2"
                                    style="color: var(--text-muted)"
                                >
                                    ESTADO NUEVO
                                </h6>
                                <pre
                                    style="
                                        background: var(--bg-th);
                                        padding: 0.75rem;
                                        border-radius: 8px;
                                        font-size: 0.72rem;
                                        max-height: 280px;
                                        overflow-y: auto;
                                        color: var(--text-main);
                                        border: 1px solid var(--border);
                                    "
                                    >{{ nuevo }}</pre
                                >
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Modal } from "bootstrap";
const props = defineProps({
    registros: Object,
    entidades: Array,
    usuarios: Array,
    filtros: Object,
});
const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("coord-theme") || "light");
const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("coord-theme", tema.value);
};
const logout = () => router.post("/logout");
const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});
const filtros = ref({ ...props.filtros });
const anterior = ref("");
const nuevo = ref("");
let modalDet = null;
const badgeAccion = (a) => {
    if (a?.startsWith("CREAR")) return "ba-c";
    if (a?.startsWith("ACTUALIZAR")) return "ba-u";
    if (a?.startsWith("ELIMINAR")) return "ba-d";
    return "ba-o";
};
const buscar = () => {
    router.get("/coordinador/bitacora", filtros.value, {
        preserveState: true,
        replace: true,
    });
};
const limpiar = () => {
    filtros.value = {
        busqueda: "",
        entidad_id: "",
        usuario_id: "",
        fecha_desde: "",
        fecha_hasta: "",
    };
    buscar();
};
const verDetalle = (bit) => {
    anterior.value = bit.bit_estado_anterior
        ? JSON.stringify(JSON.parse(bit.bit_estado_anterior), null, 2)
        : "Sin estado anterior";
    nuevo.value = bit.bit_estado_nuevo
        ? JSON.stringify(JSON.parse(bit.bit_estado_nuevo), null, 2)
        : "Sin estado nuevo";
    modalDet = modalDet || new Modal(document.getElementById("mDetalle"));
    modalDet.show();
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
    --bg-hover: #fff8f8;
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
.ct {
    width: 100%;
    border-collapse: collapse;
}
.ct thead th {
    padding: 0.65rem 1rem;
    background: var(--bg-th);
    color: var(--text-muted);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid var(--border);
}
.ct tbody td {
    padding: 0.65rem 1rem;
    border-bottom: 1px solid var(--border);
    color: var(--text-main);
    font-size: 0.85rem;
}
.ct tbody tr:hover td {
    background: var(--bg-hover);
}
.bs {
    padding: 3px 10px;
    border-radius: 20px;
    font-size: 0.73rem;
    font-weight: 700;
}
.ba-c {
    background: rgba(25, 135, 84, 0.12);
    color: #0f5132;
    border: 1px solid rgba(25, 135, 84, 0.3);
}
.ba-u {
    background: rgba(255, 193, 7, 0.12);
    color: #664d03;
    border: 1px solid rgba(255, 193, 7, 0.3);
}
.ba-d {
    background: rgba(220, 53, 69, 0.12);
    color: #842029;
    border: 1px solid rgba(220, 53, 69, 0.3);
}
.ba-o {
    background: rgba(108, 117, 125, 0.12);
    color: #41464b;
    border: 1px solid rgba(108, 117, 125, 0.3);
}
[data-theme="dark"] .ba-c {
    background: rgba(25, 135, 84, 0.25);
    color: #a3cfbb;
}
[data-theme="dark"] .ba-u {
    background: rgba(255, 193, 7, 0.25);
    color: #ffe69c;
}
[data-theme="dark"] .ba-d {
    background: rgba(220, 53, 69, 0.25);
    color: #f8d7da;
}
[data-theme="dark"] .ba-o {
    background: rgba(108, 117, 125, 0.25);
    color: #adb5bd;
}
.filter-box {
    background: var(--bg-card);
    border-radius: 13px;
    border: 1px solid var(--border);
    padding: 1rem;
    margin-bottom: 1.2rem;
}
.form-control,
.form-select {
    background: var(--bg-card) !important;
    color: var(--text-main) !important;
    border-color: var(--border) !important;
    font-size: 0.88rem;
}
.form-label {
    color: var(--text-main);
    font-size: 0.82rem;
}
.modal-content {
    background: var(--bg-card) !important;
    border: 1px solid var(--border) !important;
}
.modal-footer,
.modal-body {
    background: var(--bg-card);
}
.mh {
    background: #1a1d23;
    padding: 1rem 1.3rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.mh h5 {
    color: #fff;
    font-weight: 800;
    margin: 0;
    font-size: 1rem;
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
