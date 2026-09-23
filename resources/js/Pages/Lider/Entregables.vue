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
                <a href="/lider/equipo" class="sb-lnk"
                    ><i class="bi bi-people"></i> Mi Equipo</a
                >
                <a href="/lider/proyecto" class="sb-lnk"
                    ><i class="bi bi-building"></i> Mi Comunidad</a
                >
                <div class="sb-sec">Acciones</div>
                <a href="/lider/entregables" class="sb-lnk active"
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
                            class="bi bi-cloud-upload me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Mis Entregables
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button
                        class="btn btn-danger btn-sm fw-bold rounded-pill px-3"
                        @click="abrirModal"
                    >
                        <i class="bi bi-cloud-arrow-up me-1"></i>Subir
                        Entregable
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
                <div style="margin-bottom: 1.25rem">
                    <h4
                        style="
                            font-weight: 800;
                            font-size: 1.25rem;
                            color: var(--text-main);
                            margin: 0;
                        "
                    >
                        Mis Entregables
                    </h4>
                    <p
                        style="
                            color: var(--text-muted);
                            font-size: 0.85rem;
                            margin-top: 2px;
                        "
                    >
                        Sube y da seguimiento a tus documentos de proyecto
                    </p>
                </div>

                <div class="sc">
                    <div class="sh">
                        <h6>
                            <i
                                class="bi bi-file-earmark-fill text-danger me-2"
                            ></i
                            >Historial de Entregables
                        </h6>
                    </div>
                    <div
                        v-if="!entregables?.length"
                        class="p-5 text-center"
                        style="color: var(--text-muted)"
                    >
                        <i class="bi bi-cloud-upload fs-1 d-block mb-3"></i>No
                        has subido entregables aún
                    </div>
                    <div
                        v-for="ent in entregables"
                        :key="ent.entr_id"
                        class="ent-row"
                    >
                        <div class="d-flex align-items-center gap-3">
                            <div
                                style="
                                    width: 36px;
                                    height: 36px;
                                    border-radius: 8px;
                                    background: rgba(220, 53, 69, 0.1);
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    flex-shrink: 0;
                                "
                            >
                                <i
                                    class="bi"
                                    :class="
                                        ent.entr_aprobado
                                            ? 'bi-file-pdf text-danger'
                                            : 'bi-file-earmark text-muted'
                                    "
                                    style="font-size: 1.1rem"
                                ></i>
                            </div>
                            <div>
                                <div
                                    class="fw-semibold"
                                    style="font-size: 0.9rem"
                                >
                                    {{ ent.entr_nombre_archivo }}
                                </div>
                                <div
                                    style="
                                        font-size: 0.75rem;
                                        color: var(--text-muted);
                                    "
                                >
                                    {{ ent.tipo_entregable?.tet_nombre }} · v{{
                                        ent.entr_version || "1"
                                    }}
                                    ·
                                    {{
                                        new Date(
                                            ent.entr_fecha_subida,
                                        ).toLocaleDateString("es-VE")
                                    }}
                                </div>
                                <div
                                    v-if="ent.entr_observacion_rechazo"
                                    style="
                                        font-size: 0.75rem;
                                        color: #dc3545;
                                        margin-top: 2px;
                                    "
                                >
                                    <i class="bi bi-exclamation-circle me-1"></i
                                    >{{ ent.entr_observacion_rechazo }}
                                </div>
                            </div>
                        </div>
                        <span
                            class="bs"
                            :class="
                                ent.entr_aprobado === null
                                    ? 'bp'
                                    : ent.entr_aprobado
                                      ? 'ba'
                                      : 'bd'
                            "
                        >
                            {{
                                ent.entr_aprobado === null
                                    ? "En revisión"
                                    : ent.entr_aprobado
                                      ? "Aprobado"
                                      : "Rechazado"
                            }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mSubir" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>Subir Entregable</h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form
                        @submit.prevent="guardar"
                        enctype="multipart/form-data"
                    >
                        <div class="modal-body">
                            <div
                                class="alert py-2 small mb-3"
                                style="
                                    background: rgba(13, 110, 253, 0.08);
                                    border: 1px solid rgba(13, 110, 253, 0.2);
                                    color: var(--text-main);
                                "
                            >
                                <i
                                    class="bi bi-info-circle me-1 text-primary"
                                ></i
                                >El profesor revisará tu entregable y recibirás
                                una notificación con el resultado.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Tipo de Entregable
                                    <span class="text-danger">*</span></label
                                >
                                <select
                                    v-model="form.entr_id_tet"
                                    class="form-select"
                                    :class="{
                                        'is-invalid': errores.entr_id_tet,
                                    }"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option
                                        v-for="tipo in tiposEntregable"
                                        :key="tipo.tet_id"
                                        :value="tipo.tet_id"
                                    >
                                        {{ tipo.tet_nombre }}
                                    </option>
                                </select>
                                <div
                                    v-if="errores.entr_id_tet"
                                    class="invalid-feedback"
                                >
                                    {{ errores.entr_id_tet }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Archivo
                                    <span class="text-danger">*</span></label
                                >
                                <input
                                    type="file"
                                    class="form-control"
                                    :class="{ 'is-invalid': errores.archivo }"
                                    @change="
                                        form.archivo = $event.target.files[0]
                                    "
                                    accept=".pdf,.doc,.docx,.zip"
                                />
                                <div
                                    class="form-text"
                                    style="color: var(--text-muted)"
                                >
                                    PDF, Word o ZIP. Máx 10MB.
                                </div>
                                <div
                                    v-if="errores.archivo"
                                    class="invalid-feedback"
                                >
                                    {{ errores.archivo }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Versión</label
                                >
                                <input
                                    v-model="form.entr_version"
                                    type="text"
                                    class="form-control"
                                    placeholder="Ej: 1.0, 2.1"
                                />
                            </div>
                            <div>
                                <label class="form-label fw-semibold"
                                    >Descripción</label
                                >
                                <textarea
                                    v-model="form.entr_descripcion"
                                    class="form-control"
                                    rows="2"
                                    placeholder="Describe brevemente el contenido..."
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
                                ></span>
                                <i v-else class="bi bi-cloud-arrow-up me-2"></i
                                >Subir Entregable
                            </button>
                        </div>
                    </form>
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
    entregables: Array,
    tiposEntregable: { type: Array, default: () => [] },
});
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
const form = ref({
    entr_id_tet: "",
    archivo: null,
    entr_version: "1.0",
    entr_descripcion: "",
});
const errores = ref({});
const loading = ref(false);
let modal = null;
const abrirModal = () => {
    form.value = {
        entr_id_tet: "",
        archivo: null,
        entr_version: "1.0",
        entr_descripcion: "",
    };
    errores.value = {};
    modal = modal || new Modal(document.getElementById("mSubir"));
    modal.show();
};
const guardar = () => {
    loading.value = true;
    const data = new FormData();
    data.append("entr_id_tet", form.value.entr_id_tet);
    if (form.value.archivo) data.append("archivo", form.value.archivo);
    data.append("entr_version", form.value.entr_version);
    data.append("entr_descripcion", form.value.entr_descripcion);
    router.post("/lider/entregables", data, {
        forceFormData: true,
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
.ent-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.85rem 1.3rem;
    border-bottom: 1px solid var(--border);
    flex-wrap: wrap;
    gap: 8px;
}
.ent-row:last-child {
    border-bottom: none;
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
