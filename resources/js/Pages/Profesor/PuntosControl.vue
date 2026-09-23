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
                    <div class="sb-role">Profesor de Proyecto</div>
                </div>
            </div>
            <div class="sb-nav">
                <div class="sb-sec">Principal</div>
                <a href="/profesor" class="sb-lnk"
                    ><i class="bi bi-speedometer2"></i> Dashboard</a
                >
                <a href="/profesor/equipos" class="sb-lnk"
                    ><i class="bi bi-people"></i> Mis Equipos</a
                >
                <div class="sb-sec">Gestión</div>
                <a href="/profesor/puntos-control" class="sb-lnk active"
                    ><i class="bi bi-flag"></i> Puntos de Control</a
                >
                <a href="/profesor/socializaciones" class="sb-lnk"
                    ><i class="bi bi-mic"></i> Socializaciones</a
                >
                <a href="/profesor/entregables" class="sb-lnk"
                    ><i class="bi bi-file-earmark-check"></i> Entregables</a
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
                            class="bi bi-flag me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Puntos de Control
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button
                        class="btn btn-danger btn-sm fw-bold rounded-pill px-3"
                        @click="abrirModal()"
                    >
                        <i class="bi bi-plus-lg me-1"></i>Nuevo Punto
                    </button>
                    <button class="tb-btn" @click="toggleTema">
                        <i
                            class="bi"
                            :class="
                                tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'
                            "
                        ></i>
                    </button>
                </div>
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
                            Puntos de Control
                        </h4>
                        <p
                            style="
                                color: var(--text-muted);
                                font-size: 0.85rem;
                                margin-top: 2px;
                            "
                        >
                            Seguimiento del avance de cada equipo
                        </p>
                    </div>
                    <div class="srch">
                        <i class="bi bi-search"></i
                        ><input
                            v-model="busqueda"
                            type="text"
                            placeholder="Buscar..."
                        />
                    </div>
                </div>
                <div class="sc">
                    <div class="table-responsive">
                        <table class="ct">
                            <thead>
                                <tr>
                                    <th class="ps-4">Punto</th>
                                    <th>Equipo</th>
                                    <th>Fecha Límite</th>
                                    <th>Días Aviso</th>
                                    <th>Estado</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="filtrados.length === 0">
                                    <td
                                        colspan="6"
                                        class="text-center py-5"
                                        style="color: var(--text-muted)"
                                    >
                                        Sin puntos de control
                                    </td>
                                </tr>
                                <tr v-for="puc in filtrados" :key="puc.puc_id">
                                    <td class="ps-4 fw-semibold">
                                        {{ puc.puc_nombre }}
                                    </td>
                                    <td>
                                        <span class="bs bp">{{
                                            puc.equipo?.equ_codigo
                                        }}</span>
                                    </td>
                                    <td>
                                        <span
                                            :style="
                                                estaVencido(
                                                    puc.puc_fecha_limite,
                                                )
                                                    ? 'color:#dc3545;font-weight:700'
                                                    : ''
                                            "
                                        >
                                            {{
                                                puc.puc_fecha_reprogramada ||
                                                puc.puc_fecha_limite
                                            }}
                                        </span>
                                        <span
                                            v-if="puc.puc_fecha_reprogramada"
                                            class="ms-1"
                                            style="
                                                font-size: 0.72rem;
                                                color: var(--text-muted);
                                            "
                                            >(reprogramado)</span
                                        >
                                    </td>
                                    <td style="color: var(--text-muted)">
                                        {{ puc.puc_dias_aviso }} días
                                    </td>
                                    <td>
                                        <span
                                            v-if="puc.seguimiento?.seq_cumplido"
                                            class="bs ba"
                                            >Cumplido</span
                                        >
                                        <span
                                            v-else-if="
                                                estaVencido(
                                                    puc.puc_fecha_limite,
                                                )
                                            "
                                            class="bs bd"
                                            >Vencido</span
                                        >
                                        <span
                                            v-else-if="
                                                esCercano(puc.puc_fecha_limite)
                                            "
                                            class="bs bw"
                                            >Próximo</span
                                        >
                                        <span v-else class="bs bp"
                                            >Pendiente</span
                                        >
                                    </td>
                                    <td class="text-end pe-4">
                                        <button
                                            class="btn btn-sm btn-outline-success me-1"
                                            @click="abrirSeguimiento(puc)"
                                            title="Registrar seguimiento"
                                        >
                                            <i
                                                class="bi bi-clipboard-check"
                                            ></i>
                                        </button>
                                        <button
                                            class="btn btn-sm btn-outline-warning me-1"
                                            @click="abrirReprogramar(puc)"
                                            title="Reprogramar"
                                        >
                                            <i class="bi bi-calendar-event"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            @click="eliminar(puc)"
                                            title="Eliminar"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Nuevo Punto -->
        <div class="modal fade" id="mPunto" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>Nuevo Punto de Control</h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form @submit.prevent="guardar">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Equipo
                                    <span class="text-danger">*</span></label
                                >
                                <select
                                    v-model="form.puc_id_equ"
                                    class="form-select"
                                    :class="{
                                        'is-invalid': errores.puc_id_equ,
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
                                    v-if="errores.puc_id_equ"
                                    class="invalid-feedback"
                                >
                                    {{ errores.puc_id_equ }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Nombre del Punto
                                    <span class="text-danger">*</span></label
                                >
                                <input
                                    v-model="form.puc_nombre"
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': errores.puc_nombre,
                                    }"
                                    placeholder="Ej: Punto de Control 1 — Diagnóstico"
                                />
                                <div
                                    v-if="errores.puc_nombre"
                                    class="invalid-feedback"
                                >
                                    {{ errores.puc_nombre }}
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Fecha Límite
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        v-model="form.puc_fecha_limite"
                                        type="date"
                                        class="form-control"
                                        :class="{
                                            'is-invalid':
                                                errores.puc_fecha_limite,
                                        }"
                                    />
                                    <div
                                        v-if="errores.puc_fecha_limite"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.puc_fecha_limite }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Días de Aviso</label
                                    >
                                    <input
                                        v-model="form.puc_dias_aviso"
                                        type="number"
                                        class="form-control"
                                        min="1"
                                        placeholder="Ej: 3"
                                    />
                                </div>
                            </div>
                            <div class="mt-3">
                                <label class="form-label fw-semibold"
                                    >Descripción</label
                                >
                                <textarea
                                    v-model="form.puc_descripcion"
                                    class="form-control"
                                    rows="2"
                                ></textarea>
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
                                >Guardar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Seguimiento -->
        <div class="modal fade" id="mSeg" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>
                            Registrar Seguimiento —
                            {{ puntoActual?.puc_nombre }}
                        </h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form @submit.prevent="guardarSeguimiento">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >¿El equipo cumplió este punto?</label
                                >
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input
                                            type="radio"
                                            class="form-check-input"
                                            v-model="formSeg.seq_cumplido"
                                            :value="true"
                                            id="si"
                                        /><label
                                            class="form-check-label fw-semibold"
                                            for="si"
                                            style="color: #198754"
                                            >Sí, cumplido</label
                                        >
                                    </div>
                                    <div class="form-check">
                                        <input
                                            type="radio"
                                            class="form-check-input"
                                            v-model="formSeg.seq_cumplido"
                                            :value="false"
                                            id="no"
                                        /><label
                                            class="form-check-label fw-semibold"
                                            for="no"
                                            style="color: #dc3545"
                                            >No cumplido</label
                                        >
                                    </div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Observaciones</label
                                >
                                <textarea
                                    v-model="formSeg.seq_observaciones"
                                    class="form-control"
                                    rows="3"
                                ></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Asistencia de Integrantes</label
                                >
                                <div
                                    v-if="
                                        !puntoActual?.equipo?.integrantes
                                            ?.length
                                    "
                                    class="text-muted small"
                                >
                                    Sin integrantes cargados
                                </div>
                                <div
                                    v-for="int in puntoActual?.equipo
                                        ?.integrantes"
                                    :key="int.ein_id"
                                    class="d-flex align-items-center justify-content-between py-2 border-bottom"
                                    style="
                                        border-color: var(--border) !important;
                                    "
                                >
                                    <span style="font-size: 0.9rem"
                                        >{{ int.usuario?.usu_primer_nombre }}
                                        {{
                                            int.usuario?.usu_primer_apellido
                                        }}</span
                                    >
                                    <div class="form-check mb-0">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            :id="'asist_' + int.ein_id"
                                            v-model="
                                                formSeg.asistencias[
                                                    int.ein_id_usu
                                                ]
                                            "
                                        />
                                        <label
                                            class="form-check-label small fw-bold"
                                            :for="'asist_' + int.ein_id"
                                            >Asistió</label
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
                                :disabled="loadingSeg"
                            >
                                <span
                                    v-if="loadingSeg"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span
                                >Registrar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Reprogramar -->
        <div class="modal fade" id="mReprog" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>Reprogramar Punto</h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form @submit.prevent="guardarReprogramar">
                        <div class="modal-body">
                            <label class="form-label fw-semibold"
                                >Nueva Fecha
                                <span class="text-danger">*</span></label
                            >
                            <input
                                v-model="formReprog.nueva_fecha"
                                type="date"
                                class="form-control"
                            />
                            <label class="form-label fw-semibold mt-3"
                                >Motivo</label
                            >
                            <textarea
                                v-model="formReprog.motivo"
                                class="form-control"
                                rows="2"
                            ></textarea>
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
                                class="btn btn-warning fw-bold text-dark"
                            >
                                Reprogramar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Confirmar Eliminar -->
        <div class="modal fade" id="mElim" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center p-4">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem">
                            🚩
                        </div>
                        <h6 class="fw-bold" style="color: var(--text-main)">
                            ¿Eliminar Punto?
                        </h6>
                        <p
                            style="color: var(--text-muted); font-size: 0.9rem"
                            class="mb-0"
                        >
                            ¿Seguro que deseas eliminar
                            <strong>{{ puntoAEliminar?.puc_nombre }}</strong
                            >?
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
                            @click="confirmarEliminar"
                        >
                            Sí, eliminar
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
const props = defineProps({ puntosControl: Array, equipos: Array });
const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("prof-theme") || "light");
const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("prof-theme", tema.value);
};
const logout = () => router.post("/logout");
const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});
const busqueda = ref("");
const form = ref({
    puc_id_equ: "",
    puc_nombre: "",
    puc_fecha_limite: "",
    puc_dias_aviso: 3,
    puc_descripcion: "",
});
const errores = ref({});
const loading = ref(false);
const formSeg = ref({
    seq_cumplido: true,
    seq_observaciones: "",
    asistencias: {},
});
const loadingSeg = ref(false);
const formReprog = ref({ nueva_fecha: "", motivo: "" });
const puntoActual = ref(null);
const puntoAEliminar = ref(null);
let mPunto = null;
let mSeg = null;
let mReprog = null;
let mElim = null;
const estaVencido = (f) => f && new Date(f) < new Date();
const esCercano = (f) => {
    if (!f) return false;
    const d = new Date(f) - new Date();
    return d > 0 && d < 3 * 86400000;
};
const filtrados = computed(() => {
    if (!busqueda.value.trim()) return props.puntosControl;
    const q = busqueda.value.toLowerCase();
    return props.puntosControl.filter(
        (p) =>
            p.puc_nombre?.toLowerCase().includes(q) ||
            p.equipo?.equ_codigo?.toLowerCase().includes(q),
    );
});
const abrirModal = () => {
    form.value = {
        puc_id_equ: "",
        puc_nombre: "",
        puc_fecha_limite: "",
        puc_dias_aviso: 3,
        puc_descripcion: "",
    };
    errores.value = {};
    mPunto = mPunto || new Modal(document.getElementById("mPunto"));
    mPunto.show();
};
const guardar = () => {
    loading.value = true;
    router.post("/profesor/puntos-control", form.value, {
        onError: (e) => {
            errores.value = e;
            loading.value = false;
        },
        onSuccess: () => {
            loading.value = false;
            mPunto?.hide();
        },
    });
};
const abrirSeguimiento = (puc) => {
    puntoActual.value = puc;
    formSeg.value = {
        seq_cumplido: true,
        seq_observaciones: "",
        asistencias: {},
    };
    mSeg = mSeg || new Modal(document.getElementById("mSeg"));
    mSeg.show();
};
const guardarSeguimiento = () => {
    loadingSeg.value = true;
    router.post(
        `/profesor/puntos-control/${puntoActual.value.puc_id}/seguimiento`,
        formSeg.value,
        {
            onSuccess: () => {
                loadingSeg.value = false;
                mSeg?.hide();
            },
            onError: () => {
                loadingSeg.value = false;
            },
        },
    );
};
const abrirReprogramar = (puc) => {
    puntoActual.value = puc;
    formReprog.value = { nueva_fecha: "", motivo: "" };
    mReprog = mReprog || new Modal(document.getElementById("mReprog"));
    mReprog.show();
};
const guardarReprogramar = () => {
    router.post(
        `/profesor/puntos-control/${puntoActual.value.puc_id}/reprogramar`,
        formReprog.value,
        { onSuccess: () => mReprog?.hide() },
    );
};
const eliminar = (puc) => {
    puntoAEliminar.value = puc;
    mElim = mElim || new Modal(document.getElementById("mElim"));
    mElim.show();
};
const confirmarEliminar = () => {
    router.delete(`/profesor/puntos-control/${puntoAEliminar.value.puc_id}`);
    mElim?.hide();
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
.sb-badge {
    margin-left: auto;
    background: #dc3545;
    color: #fff;
    font-size: 0.65rem;
    font-weight: 800;
    padding: 2px 6px;
    border-radius: 20px;
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
    font-size: 0.88rem;
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
[data-theme="dark"] .bsec {
    background: #252830;
    color: #f1f3f5;
    border-color: #2d3139;
}
.srch {
    position: relative;
    max-width: 240px;
}
.srch input {
    padding: 0.4rem 0.9rem 0.4rem 2rem;
    border-radius: 8px;
    border: 1px solid var(--border);
    background: var(--bg-card);
    color: var(--text-main);
    font-size: 0.88rem;
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
