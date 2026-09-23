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
                <a href="/coordinador/cartas" class="sb-lnk active"
                    ><i class="bi bi-file-earmark-text"></i> Cartas</a
                >
                <a href="/coordinador/bitacora" class="sb-lnk"
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
                            class="bi bi-file-earmark-text me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Cartas de Presentación
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
                        Cartas de Presentación
                    </h4>
                    <p
                        style="
                            color: var(--text-muted);
                            font-size: 0.85rem;
                            margin-top: 2px;
                        "
                    >
                        Aprueba solicitudes y gestiona tu firma institucional
                    </p>
                </div>

                <div class="row g-3">
                    <!-- Solicitudes pendientes -->
                    <div class="col-12">
                        <div class="sc">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-hourglass-split me-2 text-warning"
                                    ></i
                                    >Solicitudes Pendientes<span
                                        v-if="solicitudes.length > 0"
                                        class="bs bw ms-2"
                                        >{{ solicitudes.length }}</span
                                    >
                                </h6>
                            </div>
                            <div
                                v-if="solicitudes.length === 0"
                                class="p-4 text-center"
                                style="
                                    color: var(--text-muted);
                                    font-size: 0.9rem;
                                "
                            >
                                Sin solicitudes pendientes
                            </div>
                            <div class="table-responsive" v-else>
                                <table class="ct">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Equipo</th>
                                            <th>Comunidad</th>
                                            <th>Solicitada</th>
                                            <th class="text-end pe-4">
                                                Acción
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="sol in solicitudes"
                                            :key="sol.cpr_id"
                                        >
                                            <td
                                                class="ps-4 fw-bold"
                                                style="color: var(--rojo)"
                                            >
                                                {{
                                                    sol.proyecto_comunidad
                                                        ?.equipo?.equ_codigo
                                                }}
                                            </td>
                                            <td style="font-size: 0.88rem">
                                                {{
                                                    sol.proyecto_comunidad
                                                        ?.comunidad?.com_nombre
                                                }}
                                            </td>
                                            <td
                                                style="
                                                    font-size: 0.85rem;
                                                    color: var(--text-muted);
                                                "
                                            >
                                                {{
                                                    new Date(
                                                        sol.cpr_fecha_generacion,
                                                    ).toLocaleDateString(
                                                        "es-VE",
                                                    )
                                                }}
                                            </td>
                                            <td class="text-end pe-4">
                                                <button
                                                    class="btn btn-sm btn-success fw-bold"
                                                    @click="aprobar(sol)"
                                                    :disabled="!firma"
                                                    style="font-size: 0.82rem"
                                                >
                                                    <i
                                                        class="bi bi-check-lg me-1"
                                                    ></i
                                                    >Aprobar y Generar
                                                </button>
                                                <div
                                                    v-if="!firma"
                                                    style="
                                                        font-size: 0.75rem;
                                                        color: #dc3545;
                                                        margin-top: 2px;
                                                    "
                                                >
                                                    Sube tu firma primero
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Firma -->
                    <div class="col-md-5">
                        <div class="sc" style="height: 100%">
                            <div class="sh">
                                <h6>
                                    <i class="bi bi-pen me-2 text-danger"></i>Mi
                                    Firma Institucional
                                </h6>
                            </div>
                            <div class="p-3 text-center">
                                <div
                                    v-if="firma"
                                    class="mb-3 p-2 rounded"
                                    style="
                                        border: 1px solid var(--border);
                                        background: var(--bg-th);
                                    "
                                >
                                    <img
                                        :src="'/storage/' + firma"
                                        alt="Firma"
                                        class="img-fluid"
                                        style="max-height: 80px"
                                    />
                                    <div
                                        style="
                                            font-size: 0.78rem;
                                            color: #198754;
                                            margin-top: 6px;
                                            font-weight: 700;
                                        "
                                    >
                                        <i class="bi bi-check-circle me-1"></i
                                        >Firma configurada
                                    </div>
                                </div>
                                <div
                                    v-else
                                    class="py-3 mb-3"
                                    style="color: var(--text-muted)"
                                >
                                    <i class="bi bi-pen fs-2 d-block mb-2"></i>
                                    <small>Sin firma configurada</small>
                                </div>
                                <form
                                    @submit.prevent="subirFirma"
                                    enctype="multipart/form-data"
                                >
                                    <input
                                        type="file"
                                        class="form-control form-control-sm mb-2"
                                        accept="image/png,image/jpeg"
                                        @change="
                                            firmaFile = $event.target.files[0]
                                        "
                                    />
                                    <div
                                        style="
                                            font-size: 0.75rem;
                                            color: var(--text-muted);
                                            margin-bottom: 0.5rem;
                                        "
                                    >
                                        PNG o JPG. Máx 2MB. Se usará en todas
                                        las cartas.
                                    </div>
                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-danger w-100 fw-bold rounded-pill"
                                        :disabled="!firmaFile || loadingFirma"
                                    >
                                        <span
                                            v-if="loadingFirma"
                                            class="spinner-border spinner-border-sm me-1"
                                        ></span>
                                        {{
                                            firma
                                                ? "Actualizar Firma"
                                                : "Subir Firma"
                                        }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Historial -->
                    <div class="col-md-7">
                        <div class="sc" style="height: 100%">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-archive me-2 text-primary"
                                    ></i
                                    >Cartas Generadas
                                </h6>
                            </div>
                            <div
                                v-if="cartas.length === 0"
                                class="p-4 text-center"
                                style="
                                    color: var(--text-muted);
                                    font-size: 0.9rem;
                                "
                            >
                                Sin cartas generadas
                            </div>
                            <ul v-else class="list-group list-group-flush">
                                <li
                                    v-for="carta in cartas"
                                    :key="carta.cpr_id"
                                    class="list-group-item d-flex align-items-center justify-content-between px-3 py-2"
                                    style="
                                        background: transparent;
                                        border-color: var(--border);
                                    "
                                >
                                    <div>
                                        <div
                                            class="fw-semibold"
                                            style="
                                                font-size: 0.9rem;
                                                color: var(--text-main);
                                            "
                                        >
                                            {{
                                                carta.proyecto_comunidad?.equipo
                                                    ?.equ_codigo
                                            }}
                                        </div>
                                        <div
                                            style="
                                                font-size: 0.78rem;
                                                color: var(--text-muted);
                                            "
                                        >
                                            {{
                                                carta.proyecto_comunidad
                                                    ?.comunidad?.com_nombre
                                            }}
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center gap-2"
                                    >
                                        <span class="bs ba">Aprobada</span>
                                        <a
                                            :href="`/coordinador/cartas/${carta.cpr_id}/descargar`"
                                            target="_blank"
                                            class="btn btn-sm btn-outline-danger"
                                            title="Descargar PDF"
                                            ><i class="bi bi-download"></i
                                        ></a>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mAprobar" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center p-4">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem">
                            📄
                        </div>
                        <h6 class="fw-bold" style="color: var(--text-main)">
                            ¿Generar Carta?
                        </h6>
                        <p
                            style="color: var(--text-muted); font-size: 0.9rem"
                            class="mb-0"
                        >
                            Se generará el PDF con tu firma y sello
                            institucional.
                        </p>
                    </div>
                    <div
                        class="modal-footer border-0 justify-content-center pb-4 gap-2"
                    >
                        <button
                            class="btn btn-secondary px-4"
                            data-bs-dismiss="modal"
                        >
                            Cancelar
                        </button>
                        <button
                            class="btn btn-success fw-bold px-4"
                            @click="confirmarAprobar"
                        >
                            Sí, generar
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
    cartas: Array,
    solicitudes: Array,
    proyectos: Array,
    firma: String,
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
const firmaFile = ref(null);
const loadingFirma = ref(false);
const cartaSeleccionada = ref(null);
let modalAp = null;
const aprobar = (carta) => {
    cartaSeleccionada.value = carta;
    modalAp = modalAp || new Modal(document.getElementById("mAprobar"));
    modalAp.show();
};
const confirmarAprobar = () => {
    router.post(
        `/coordinador/cartas/${cartaSeleccionada.value.cpr_id}/aprobar`,
    );
    modalAp?.hide();
};
const subirFirma = () => {
    if (!firmaFile.value) return;
    loadingFirma.value = true;
    const data = new FormData();
    data.append("firma", firmaFile.value);
    router.post("/coordinador/cartas/firma", data, {
        forceFormData: true,
        onFinish: () => {
            loadingFirma.value = false;
        },
    });
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
    padding: 0.7rem 1rem;
    background: var(--bg-th);
    color: var(--text-muted);
    font-size: 0.78rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid var(--border);
}
.ct tbody td {
    padding: 0.7rem 1rem;
    border-bottom: 1px solid var(--border);
    color: var(--text-main);
    font-size: 0.9rem;
}
.ct tbody tr:hover td {
    background: var(--bg-hover);
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
[data-theme="dark"] .bd {
    background: rgba(220, 53, 69, 0.25);
    color: #f8d7da;
    border-color: rgba(220, 53, 69, 0.4);
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
.form-control,
.form-select {
    background: var(--bg-card) !important;
    color: var(--text-main) !important;
    border-color: var(--border) !important;
}
.form-label {
    color: var(--text-main);
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
