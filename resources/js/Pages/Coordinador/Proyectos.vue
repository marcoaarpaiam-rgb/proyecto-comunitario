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
                <a href="/coordinador/proyectos" class="sb-lnk active"
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
                            class="bi bi-folder me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Proyectos
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
                <div
                    style="
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        margin-bottom: 1.25rem;
                        flex-wrap: wrap;
                        gap: 10px;
                    "
                >
                    <div>
                        <h4
                            style="
                                font-weight: 800;
                                font-size: 1.25rem;
                                color: var(--text-main);
                                margin: 0;
                            "
                        >
                            Proyectos Comunitarios
                        </h4>
                        <p
                            style="
                                color: var(--text-muted);
                                font-size: 0.85rem;
                                margin-top: 2px;
                            "
                        >
                            Gestión de proyectos por equipo
                        </p>
                    </div>
                    <button
                        class="btn btn-danger fw-bold rounded-pill px-4"
                        @click="abrirModal()"
                    >
                        <i class="bi bi-plus-lg me-2"></i>Nuevo Proyecto
                    </button>
                </div>

                <div class="sc">
                    <div
                        style="
                            padding: 0.9rem 1rem;
                            border-bottom: 1px solid var(--border);
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            flex-wrap: wrap;
                        "
                    >
                        <div class="srch">
                            <i class="bi bi-search"></i
                            ><input
                                v-model="busqueda"
                                type="text"
                                placeholder="Buscar..."
                            />
                        </div>
                        <select
                            v-model="filtroAprobacion"
                            class="form-select form-select-sm"
                            style="width: 170px"
                        >
                            <option value="">Todos</option>
                            <option value="aprobado">Aprobados</option>
                            <option value="pendiente">Pendientes</option>
                        </select>
                        <span
                            class="ms-auto"
                            style="
                                font-size: 0.82rem;
                                color: var(--text-muted);
                                font-weight: 600;
                            "
                            >{{ proyectosFiltrados.length }} resultado(s)</span
                        >
                    </div>
                    <div class="table-responsive">
                        <table class="ct">
                            <thead>
                                <tr>
                                    <th class="ps-4">Equipo</th>
                                    <th>Comunidad</th>
                                    <th>Tipo</th>
                                    <th>Modalidad</th>
                                    <th>Fecha Límite</th>
                                    <th>Aprobación</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="proyectosPaginados.length === 0">
                                    <td
                                        colspan="7"
                                        class="text-center py-5"
                                        style="color: var(--text-muted)"
                                    >
                                        Sin resultados
                                    </td>
                                </tr>
                                <tr
                                    v-for="pco in proyectosPaginados"
                                    :key="pco.pco_id"
                                >
                                    <td class="ps-4">
                                        <div
                                            class="fw-bold"
                                            style="color: var(--rojo)"
                                        >
                                            {{ pco.equipo?.equ_codigo }}
                                        </div>
                                        <div
                                            style="
                                                font-size: 0.8rem;
                                                color: var(--text-muted);
                                                max-width: 160px;
                                                white-space: nowrap;
                                                overflow: hidden;
                                                text-overflow: ellipsis;
                                            "
                                        >
                                            {{ pco.equipo?.equ_titulo }}
                                        </div>
                                    </td>
                                    <td style="font-size: 0.88rem">
                                        {{ pco.comunidad?.com_nombre }}
                                    </td>
                                    <td style="font-size: 0.85rem">
                                        {{ pco.tipo_proyecto?.tpr_nombre }}
                                    </td>
                                    <td>
                                        <span class="bs bp">{{
                                            pco.modalidad?.mpr_nombre
                                        }}</span>
                                    </td>
                                    <td
                                        style="
                                            font-size: 0.88rem;
                                            font-weight: 600;
                                        "
                                        :style="
                                            estaVencido(pco.pco_fecha_limite)
                                                ? 'color:#dc3545'
                                                : ''
                                        "
                                    >
                                        {{ pco.pco_fecha_limite }}
                                    </td>
                                    <td>
                                        <span
                                            v-if="pco.pco_fecha_aprobacion"
                                            class="bs ba"
                                            >✓ Aprobado</span
                                        >
                                        <button
                                            v-else
                                            class="btn btn-sm btn-outline-success fw-semibold"
                                            style="
                                                font-size: 0.78rem;
                                                padding: 3px 10px;
                                            "
                                            @click="aprobar(pco)"
                                        >
                                            <i class="bi bi-check-lg me-1"></i
                                            >Aprobar
                                        </button>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            @click="desactivar(pco)"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div
                        v-if="totalPaginas > 1"
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
                            >{{ desde + 1 }}–{{
                                Math.min(hasta, proyectosFiltrados.length)
                            }}
                            de {{ proyectosFiltrados.length }}</span
                        >
                        <div class="d-flex gap-1">
                            <button
                                class="btn btn-sm btn-outline-secondary"
                                :disabled="pagina === 1"
                                @click="pagina--"
                            >
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button
                                v-for="p in paginasV"
                                :key="p"
                                class="btn btn-sm"
                                :class="
                                    p === pagina
                                        ? 'btn-danger'
                                        : 'btn-outline-secondary'
                                "
                                @click="pagina = p"
                            >
                                {{ p }}
                            </button>
                            <button
                                class="btn btn-sm btn-outline-secondary"
                                :disabled="pagina === totalPaginas"
                                @click="pagina++"
                            >
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Nuevo Proyecto -->
        <div class="modal fade" id="mProy" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>Nuevo Proyecto</h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form @submit.prevent="guardar">
                        <div class="modal-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Equipo
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <select
                                        v-model="form.pco_id_equ"
                                        class="form-select"
                                        :class="{
                                            'is-invalid': errores.pco_id_equ,
                                        }"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option
                                            v-for="equ in equipos"
                                            :key="equ.equ_id"
                                            :value="equ.equ_id"
                                        >
                                            {{ equ.equ_codigo }} —
                                            {{ equ.equ_titulo }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="errores.pco_id_equ"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.pco_id_equ }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Comunidad
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <select
                                        v-model="form.pco_id_com"
                                        class="form-select"
                                        :class="{
                                            'is-invalid': errores.pco_id_com,
                                        }"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option
                                            v-for="com in comunidades"
                                            :key="com.com_id"
                                            :value="com.com_id"
                                        >
                                            {{ com.com_nombre }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="errores.pco_id_com"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.pco_id_com }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Tipo de Proyecto
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <select
                                        v-model="form.pco_id_tpr"
                                        class="form-select"
                                        :class="{
                                            'is-invalid': errores.pco_id_tpr,
                                        }"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option
                                            v-for="t in tipos"
                                            :key="t.tpr_id"
                                            :value="t.tpr_id"
                                        >
                                            {{ t.tpr_nombre }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="errores.pco_id_tpr"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.pco_id_tpr }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Modalidad
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <select
                                        v-model="form.pco_id_mpr"
                                        class="form-select"
                                        :class="{
                                            'is-invalid': errores.pco_id_mpr,
                                        }"
                                    >
                                        <option value="">Seleccionar...</option>
                                        <option
                                            v-for="m in modalidades"
                                            :key="m.mpr_id"
                                            :value="m.mpr_id"
                                        >
                                            {{ m.mpr_nombre }}
                                        </option>
                                    </select>
                                    <div
                                        v-if="errores.pco_id_mpr"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.pco_id_mpr }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Fecha Límite
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        v-model="form.pco_fecha_limite"
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                errores.pco_fecha_limite,
                                        }"
                                    />
                                    <div
                                        v-if="errores.pco_fecha_limite"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.pco_fecha_limite }}
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label fw-semibold"
                                        >Resumen
                                        <span class="text-danger">*</span>
                                        <small style="color: var(--text-muted)"
                                            >(máx. 150 caracteres)</small
                                        ></label
                                    >
                                    <textarea
                                        v-model="form.pco_resumen"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': errores.pco_resumen,
                                        }"
                                        rows="2"
                                        maxlength="150"
                                        placeholder="Describe brevemente el proyecto..."
                                    ></textarea>
                                    <div
                                        class="d-flex justify-content-between mt-1"
                                    >
                                        <div
                                            v-if="errores.pco_resumen"
                                            class="invalid-feedback d-block"
                                        >
                                            {{ errores.pco_resumen }}
                                        </div>
                                        <small
                                            class="ms-auto"
                                            style="color: var(--text-muted)"
                                            >{{
                                                form.pco_resumen.length
                                            }}/150</small
                                        >
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="btn btn-danger fw-bold"
                                :disabled="loading"
                            >
                                <span
                                    v-if="loading"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span
                                >Guardar Proyecto
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Aprobar -->
        <div class="modal fade" id="mAprobar" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center p-4">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem">
                            ✅
                        </div>
                        <h6 class="fw-bold" style="color: var(--text-main)">
                            ¿Aprobar Comunidad?
                        </h6>
                        <p
                            style="color: var(--text-muted); font-size: 0.9rem"
                            class="mb-0"
                        >
                            ¿Confirmas que el equipo
                            <strong>{{
                                itemSeleccionado?.equipo?.equ_codigo
                            }}</strong>
                            puede trabajar con esta comunidad?
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
                            Sí, aprobar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Desactivar -->
        <div class="modal fade" id="mDesact" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center p-4">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem">
                            🗑️
                        </div>
                        <h6 class="fw-bold" style="color: var(--text-main)">
                            ¿Desactivar Proyecto?
                        </h6>
                        <p
                            style="color: var(--text-muted); font-size: 0.9rem"
                            class="mb-0"
                        >
                            Esta acción no se puede deshacer fácilmente.
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
                            class="btn btn-danger fw-bold px-4"
                            @click="confirmarDesactivar"
                        >
                            Sí, desactivar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Modal } from "bootstrap";
const props = defineProps({
    proyectos: Array,
    equipos: Array,
    comunidades: Array,
    tipos: Array,
    modalidades: Array,
    beneficiarios: Array,
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
const busqueda = ref("");
const filtroAprobacion = ref("");
const pagina = ref(1);
const porPagina = 8;
const form = ref({
    pco_id_equ: "",
    pco_id_com: "",
    pco_id_tpr: "",
    pco_id_mpr: "",
    pco_resumen: "",
    pco_fecha_limite: "",
    beneficiarios: [],
});
const errores = ref({});
const loading = ref(false);
const itemSeleccionado = ref(null);
let modal = null;
let modalAp = null;
let modalDes = null;
watch([busqueda, filtroAprobacion], () => {
    pagina.value = 1;
});
const estaVencido = (f) => f && new Date(f) < new Date();
const proyectosFiltrados = computed(() => {
    let r = props.proyectos;
    if (busqueda.value.trim()) {
        const q = busqueda.value.toLowerCase();
        r = r.filter(
            (p) =>
                p.equipo?.equ_codigo?.toLowerCase().includes(q) ||
                p.equipo?.equ_titulo?.toLowerCase().includes(q) ||
                p.comunidad?.com_nombre?.toLowerCase().includes(q),
        );
    }
    if (filtroAprobacion.value === "aprobado")
        r = r.filter((p) => !!p.pco_fecha_aprobacion);
    if (filtroAprobacion.value === "pendiente")
        r = r.filter((p) => !p.pco_fecha_aprobacion);
    return r;
});
const totalPaginas = computed(() =>
    Math.ceil(proyectosFiltrados.value.length / porPagina),
);
const desde = computed(() => (pagina.value - 1) * porPagina);
const hasta = computed(() => desde.value + porPagina);
const proyectosPaginados = computed(() =>
    proyectosFiltrados.value.slice(desde.value, hasta.value),
);
const paginasV = computed(() => {
    const t = totalPaginas.value,
        a = pagina.value,
        p = [],
        i = Math.max(1, a - 2),
        f = Math.min(t, a + 2);
    for (let x = i; x <= f; x++) p.push(x);
    return p;
});
const abrirModal = () => {
    form.value = {
        pco_id_equ: "",
        pco_id_com: "",
        pco_id_tpr: "",
        pco_id_mpr: "",
        pco_resumen: "",
        pco_fecha_limite: "",
        beneficiarios: [],
    };
    errores.value = {};
    modal = modal || new Modal(document.getElementById("mProy"));
    modal.show();
};
const guardar = () => {
    loading.value = true;
    router.post("/coordinador/proyectos", form.value, {
        onError: (e) => {
            errores.value = e;
            loading.value = false;
        },
        onSuccess: () => {
            loading.value = false;
            modal?.hide();
        },
    });
};
const aprobar = (pco) => {
    itemSeleccionado.value = pco;
    modalAp = modalAp || new Modal(document.getElementById("mAprobar"));
    modalAp.show();
};
const confirmarAprobar = () => {
    router.post(
        `/coordinador/proyectos/${itemSeleccionado.value.pco_id}/aprobar`,
    );
    modalAp?.hide();
};
const desactivar = (pco) => {
    itemSeleccionado.value = pco;
    modalDes = modalDes || new Modal(document.getElementById("mDesact"));
    modalDes.show();
};
const confirmarDesactivar = () => {
    router.delete(`/coordinador/proyectos/${itemSeleccionado.value.pco_id}`);
    modalDes?.hide();
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
.srch {
    position: relative;
    max-width: 220px;
}
.srch input {
    padding: 0.4rem 0.9rem 0.4rem 2rem;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--bg-card);
    color: var(--text-main);
    font-size: 0.85rem;
    width: 100%;
}
.srch input:focus {
    outline: none;
    border-color: #dc3545;
}
.srch i {
    position: absolute;
    left: 0.6rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.85rem;
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
