<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
        "
    >
        <SidebarLider
            :sidebar-open="sidebarOpen"
            @close="sidebarOpen = false"
        />
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
                            class="bi bi-building me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Mi Comunidad
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
                <BannerComunidadPendiente />
                <div style="margin-bottom: 1.25rem">
                    <h4
                        style="
                            font-weight: 800;
                            font-size: 1.25rem;
                            color: var(--text-main);
                            margin: 0;
                        "
                    >
                        Mi Proyecto
                    </h4>
                    <p
                        style="
                            color: var(--text-muted);
                            font-size: 0.85rem;
                            margin-top: 2px;
                        "
                    >
                        Información del proyecto comunitario
                    </p>
                </div>

                <!-- Alertas de errores y mensajes flash -->
                <div v-if="$page.props.flash?.error || Object.keys($page.props.errors || {}).length" class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span v-if="$page.props.flash?.error">{{ $page.props.flash.error }}</span>
                    <ul v-else class="mb-0 ps-3">
                        <li v-for="(err, k) in $page.props.errors" :key="k">{{ err }}</li>
                    </ul>
                </div>
                <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ $page.props.flash.success }}
                </div>

                <div v-if="!equipo" class="sc">
                    <div
                        class="p-5 text-center"
                        style="color: var(--text-muted)"
                    >
                        <i class="bi bi-building fs-1 d-block mb-3"></i>No tienes un equipo activo asignado
                    </div>
                </div>

                <!-- Etapa 2: Formulario de Postulación Comunitaria (cuando pco_comunidad_aprobada = false) -->
                <div v-else-if="!comunidadAprobada">
                    <!-- Vista de estado Pendiente de Aprobación Docente -->
                    <div
                        v-if="estadoPostulacion === 'pendiente_aprobacion' && !editandoPostulacion"
                        class="sc mb-3 p-4"
                    >
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                            <div>
                                <span class="badge bg-info text-dark px-3 py-2 rounded-pill mb-2">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    Estado: Pendiente de Aprobación Docente
                                </span>
                                <h5 class="fw-bold mb-1">Postulación Comunitaria Enviada</h5>
                                <p class="small text-muted mb-0">
                                    Tu Docente de Proyecto revisará la no duplicidad y aprobará los datos para desbloquear la Carta de Presentación y Entregables.
                                </p>
                            </div>
                            <button
                                type="button"
                                class="btn btn-outline-warning btn-sm fw-bold rounded-pill px-3"
                                @click="editandoPostulacion = true"
                            >
                                <i class="bi bi-pencil me-1"></i>Editar Postulación
                            </button>
                        </div>

                        <div class="row g-3 small mt-1">
                            <div class="col-md-6">
                                <div class="p-3 rounded border">
                                    <div class="text-muted">Comunidad Beneficiada</div>
                                    <div class="fw-bold fs-6">{{ equipo.proyecto_comunidad?.comunidad?.com_nombre || '—' }}</div>
                                    <div class="text-muted">{{ equipo.proyecto_comunidad?.comunidad?.com_ubicacion || '' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded border">
                                    <div class="text-muted">Responsable de la Comunidad</div>
                                    <div class="fw-bold fs-6">{{ responsable?.rco_nombre || '—' }}</div>
                                    <div class="text-muted">
                                        CI: {{ responsable?.rco_cedula || '—' }} · Tel: {{ responsable?.rco_telefono || '—' }}
                                        <span v-if="responsable?.rco_cargo"> · {{ responsable.rco_cargo }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded border">
                                    <div class="text-muted">Modalidad y Línea de Investigación</div>
                                    <div class="fw-bold">Modalidad: {{ equipo.proyecto_comunidad?.modalidad?.mpr_nombre || '—' }}</div>
                                    <div class="text-muted">Línea: {{ equipo.proyecto_comunidad?.tipo_proyecto?.tpr_nombre || '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 rounded border">
                                    <div class="text-muted">Resumen del Proyecto</div>
                                    <div>{{ equipo.proyecto_comunidad?.pco_resumen || '—' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Formulario completo de Postulación Comunitaria -->
                    <div v-else class="sc mb-3">
                        <div class="sh d-flex justify-content-between align-items-center">
                            <h6>
                                <i class="bi bi-building-add text-danger me-2"></i>
                                Postulación de Vinculación Comunitaria (Etapa 2)
                            </h6>
                            <button
                                v-if="estadoPostulacion === 'pendiente_aprobacion'"
                                type="button"
                                class="btn btn-sm btn-outline-secondary rounded-pill"
                                @click="editandoPostulacion = false"
                            >
                                Cancelar edición
                            </button>
                        </div>
                        <form class="p-4" @submit.prevent="enviarPostulacion">
                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">
                                    1. Comunidad Beneficiada <span class="text-danger">*</span>
                                </label>
                                <div class="d-flex gap-3 mb-3">
                                    <div class="form-check">
                                        <input
                                            id="modoCat"
                                            v-model="formPost.modo_comunidad"
                                            class="form-check-input"
                                            type="radio"
                                            value="catalogo"
                                        />
                                        <label class="form-check-label small fw-semibold" for="modoCat">
                                            Seleccionar del catálogo existente
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input
                                            id="modoNueva"
                                            v-model="formPost.modo_comunidad"
                                            class="form-check-input"
                                            type="radio"
                                            value="nueva"
                                        />
                                        <label class="form-check-label small fw-semibold" for="modoNueva">
                                            Registrar nueva comunidad
                                        </label>
                                    </div>
                                </div>

                                <div v-if="formPost.modo_comunidad === 'catalogo'">
                                    <SelectBuscable
                                        v-model="formPost.pco_id_com"
                                        :options="opcionesComunidadesCatalogo"
                                        placeholder="Seleccionar comunidad..."
                                        search-placeholder="Buscar comunidad..."
                                    />
                                </div>

                                <div v-else class="row g-2">
                                    <div class="col-md-5">
                                        <input
                                            v-model="formPost.nueva_comunidad_nombre"
                                            type="text"
                                            class="form-control"
                                            placeholder="Nombre de la comunidad u organización *"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-4">
                                        <input
                                            v-model="formPost.nueva_comunidad_ubicacion"
                                            type="text"
                                            class="form-control"
                                            placeholder="Ubicación / Dirección *"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-3">
                                        <SelectBuscable
                                            v-model="formPost.nueva_comunidad_tor"
                                            :options="opcionesTiposOrganizacion"
                                            placeholder="Tipo organización *"
                                        />
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3" />

                            <div class="mb-4">
                                <label class="form-label fw-bold small text-uppercase text-muted">
                                    2. Responsable de la Comunidad <span class="text-danger">*</span>
                                </label>
                                <div class="row g-2">
                                    <div class="col-md-3">
                                        <input
                                            v-model="formPost.rco_nombre"
                                            type="text"
                                            class="form-control"
                                            placeholder="Nombre *"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-3">
                                        <input
                                            v-model="formPost.rco_apellido"
                                            type="text"
                                            class="form-control"
                                            placeholder="Apellido"
                                        />
                                    </div>
                                    <div class="col-md-2">
                                        <input
                                            v-model="formPost.rco_cedula"
                                            type="text"
                                            class="form-control"
                                            placeholder="Cédula *"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-2">
                                        <input
                                            v-model="formPost.rco_telefono"
                                            type="text"
                                            class="form-control"
                                            placeholder="Teléfono *"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-2">
                                        <input
                                            v-model="formPost.rco_cargo"
                                            type="text"
                                            class="form-control"
                                            placeholder="Cargo (ej: Vocero Principal) *"
                                            required
                                        />
                                    </div>
                                </div>
                            </div>

                            <hr class="my-3" />

                            <div class="mb-3">
                                <label class="form-label fw-bold small text-uppercase text-muted">
                                    3. Datos del Proyecto y Modalidad <span class="text-danger">*</span>
                                </label>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Modalidad del Proyecto *</label>
                                        <SelectBuscable
                                            v-model="formPost.pco_id_mpr"
                                            :options="opcionesModalidadesProyecto"
                                            placeholder="Seleccionar modalidad..."
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-semibold">Línea de Investigación (PNFI) *</label>
                                        <SelectBuscable
                                            v-model="formPost.pco_id_tpr"
                                            :options="opcionesTiposProyecto"
                                            placeholder="Seleccionar línea..."
                                        />
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-semibold">Resumen del Proyecto *</label>
                                        <textarea
                                            v-model="formPost.pco_resumen"
                                            class="form-control"
                                            rows="3"
                                            maxlength="255"
                                            minlength="10"
                                            placeholder="Describe brevemente el problema detectado en la comunidad y la solución sociotecnológica propuesta..."
                                            required
                                        ></textarea>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button
                                    type="submit"
                                    class="btn btn-danger fw-bold rounded-pill px-4"
                                    :disabled="enviandoPostulacion"
                                >
                                    <span v-if="enviandoPostulacion" class="spinner-border spinner-border-sm me-2"></span>
                                    <i v-else class="bi bi-send-check me-2"></i>
                                    Enviar Postulación para Aprobación Docente
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div v-else class="row g-3">
                    <div class="col-md-7">
                        <div class="sc h-100">
                            <div class="sh">
                                <h6>
                                    <i class="bi bi-folder text-danger me-2"></i
                                    >Datos del Proyecto
                                </h6>
                            </div>
                            <div class="p-3">
                                <div class="info-row">
                                    <span class="info-label">Título</span
                                    ><span class="fw-semibold">{{
                                        equipo.equ_titulo
                                    }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Comunidad</span
                                    ><span>{{
                                        equipo.proyecto_comunidad.comunidad
                                            ?.com_nombre
                                    }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Ubicación</span
                                    ><span style="color: var(--text-muted)">{{
                                        equipo.proyecto_comunidad.comunidad
                                            ?.com_ubicacion
                                    }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Línea de Investigación</span
                                    ><span>{{
                                        equipo.proyecto_comunidad.tipo_proyecto
                                            ?.tpr_nombre
                                    }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Modalidad</span>
                                    <span>{{
                                        equipo.proyecto_comunidad.modalidad?.mpr_nombre ||
                                        equipo.proyecto_comunidad.modalidad_proyecto?.mpr_nombre ||
                                        'No especificada'
                                    }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Fecha Límite</span
                                    ><span
                                        class="fw-bold"
                                        style="color: var(--rojo)"
                                        >{{
                                            equipo.proyecto_comunidad
                                                .pco_fecha_limite
                                        }}</span
                                    >
                                </div>
                                <div class="info-row">
                                    <span class="info-label"
                                        >Aprobación Comunidad</span
                                    >
                                    <span
                                        v-if="
                                            equipo.proyecto_comunidad
                                                .pco_fecha_aprobacion
                                        "
                                        class="bs ba"
                                        >✓ Aprobada</span
                                    >
                                    <span v-else class="bs bw">Pendiente</span>
                                </div>
                                <div
                                    class="info-row"
                                    v-if="equipo.proyecto_comunidad.pco_resumen"
                                >
                                    <span class="info-label">Resumen</span>
                                    <span
                                        style="
                                            max-width: 220px;
                                            font-size: 0.85rem;
                                            text-align: right;
                                        "
                                        >{{
                                            equipo.proyecto_comunidad
                                                .pco_resumen
                                        }}</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="sc h-100 text-center">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-building text-danger me-2"
                                    ></i
                                    >La Comunidad
                                </h6>
                            </div>
                            <div class="p-4">
                                <div
                                    style="
                                        width: 64px;
                                        height: 64px;
                                        border-radius: 50%;
                                        background: rgba(220, 53, 69, 0.1);
                                        display: flex;
                                        align-items: center;
                                        justify-content: center;
                                        margin: 0 auto 1rem;
                                    "
                                >
                                    <i
                                        class="bi bi-building fs-2"
                                        style="color: var(--rojo)"
                                    ></i>
                                </div>
                                <h5 class="fw-bold mb-1">
                                    {{
                                        equipo.proyecto_comunidad.comunidad
                                            ?.com_nombre
                                    }}
                                </h5>
                                <p
                                    style="
                                        font-size: 0.85rem;
                                        color: var(--text-muted);
                                    "
                                >
                                    {{
                                        equipo.proyecto_comunidad.comunidad
                                            ?.com_ubicacion
                                    }}
                                </p>
                                <div class="mt-3">
                                    <span
                                        v-if="
                                            equipo.proyecto_comunidad
                                                .pco_fecha_aprobacion
                                        "
                                        class="bs ba"
                                        style="
                                            font-size: 0.82rem;
                                            padding: 6px 16px;
                                        "
                                    >
                                        <i class="bi bi-patch-check me-1"></i
                                        >Comunidad Aprobada
                                    </span>
                                    <span
                                        v-else
                                        class="bs bw"
                                        style="
                                            font-size: 0.82rem;
                                            padding: 6px 16px;
                                        "
                                    >
                                        <i class="bi bi-clock me-1"></i
                                        >Pendiente de aprobación
                                    </span>
                                </div>
                                <div
                                    v-if="
                                        equipo.proyecto_comunidad
                                            .pco_fecha_aprobacion
                                    "
                                    class="mt-4 p-3 rounded"
                                    style="
                                        background: rgba(25, 135, 84, 0.08);
                                        border: 1px solid rgba(25, 135, 84, 0.2);
                                    "
                                >
                                    <div
                                        style="
                                            font-size: 0.78rem;
                                            color: #0f5132;
                                            font-weight: 700;
                                        "
                                    >
                                        <i class="bi bi-info-circle me-1"></i>Ya
                                        puedes solicitar la carta de
                                        presentación
                                    </div>
                                    <a
                                        href="/lider/carta"
                                        class="btn btn-sm btn-success rounded-pill px-3 mt-2 fw-bold"
                                        >Solicitar Carta</a
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Personal de la comunidad -->
                <div class="sc mt-3" v-if="equipo?.proyecto_comunidad">
                    <div
                        class="sh d-flex align-items-center justify-content-between"
                    >
                        <span
                            ><i class="bi bi-people me-2 text-danger"></i
                            >Responsable de la Comunidad</span
                        >
                        <button
                            class="btn btn-sm btn-danger rounded-pill fw-bold"
                            @click="
                                mostrarFormResponsable = !mostrarFormResponsable
                            "
                        >
                            <i class="bi bi-plus-circle me-1"></i>
                            {{ responsable ? "Actualizar" : "Registrar" }}
                        </button>
                    </div>
                    <div class="p-3">
                        <!-- Datos actuales -->
                        <div
                            v-if="responsable && !mostrarFormResponsable"
                            class="row g-2"
                        >
                            <div class="col-6">
                                <div
                                    class="small fw-bold"
                                    style="color: var(--text-muted)"
                                >
                                    Nombre
                                </div>
                                <div>{{ responsable.rco_nombre }}</div>
                            </div>
                            <div class="col-6">
                                <div
                                    class="small fw-bold"
                                    style="color: var(--text-muted)"
                                >
                                    Cargo
                                </div>
                                <div>{{ responsable.rco_cargo ?? "—" }}</div>
                            </div>
                            <div class="col-6">
                                <div
                                    class="small fw-bold"
                                    style="color: var(--text-muted)"
                                >
                                    Teléfono
                                </div>
                                <div>{{ responsable.rco_telefono ?? "—" }}</div>
                            </div>
                            <div class="col-6">
                                <div
                                    class="small fw-bold"
                                    style="color: var(--text-muted)"
                                >
                                    Email
                                </div>
                                <div>{{ responsable.rco_email ?? "—" }}</div>
                            </div>
                            <div class="col-6">
                                <div
                                    class="small fw-bold"
                                    style="color: var(--text-muted)"
                                >
                                    Desde
                                </div>
                                <div>{{ responsable.rco_fecha_inicio }}</div>
                            </div>
                        </div>

                        <!-- Sin responsable -->
                        <div
                            v-else-if="!responsable && !mostrarFormResponsable"
                            class="text-center py-3"
                            style="color: var(--text-muted)"
                        >
                            <i class="bi bi-person-x fs-3 d-block mb-1"></i>
                            No hay responsable registrado
                        </div>

                        <!-- Formulario -->
                        <div v-if="mostrarFormResponsable">
                            <div class="row g-2 mb-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold"
                                        >Nombre*</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        v-model="formResponsable.rco_nombre"
                                        placeholder="Nombre del responsable de la comunidad"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold"
                                        >Cédula</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        v-model="formResponsable.rco_cedula"
                                        placeholder="Ej: 28123456"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold"
                                        >Apellido</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        v-model="formResponsable.rco_apellido"
                                        placeholder="Apellido del responsable"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold"
                                        >Cargo *</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        v-model="formResponsable.rco_cargo"
                                        placeholder="Ej: Responsable Principal"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold"
                                        >Teléfono</label
                                    >
                                    <input
                                        type="text"
                                        class="form-control form-control-sm"
                                        v-model="formResponsable.rco_telefono"
                                        placeholder="0412-1234567"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold"
                                        >Email</label
                                    >
                                    <input
                                        type="email"
                                        class="form-control form-control-sm"
                                        v-model="formResponsable.rco_email"
                                        placeholder="correo@ejemplo.com"
                                    />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold"
                                        >Fecha de inicio *</label
                                    >
                                    <input
                                        type="date"
                                        class="form-control form-control-sm"
                                        v-model="
                                            formResponsable.rco_fecha_inicio
                                        "
                                    />
                                </div>
                            </div>
                            <div class="d-flex gap-2">
                                <button
                                    class="btn btn-sm btn-danger fw-bold"
                                    @click="guardarResponsable"
                                >
                                    <i class="bi bi-check2 me-1"></i> Guardar
                                </button>
                                <button
                                    class="btn btn-sm btn-outline-secondary"
                                    @click="mostrarFormResponsable = false"
                                >
                                    Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Confirmación Cerrar Sesión -->
        <ModalConfirmar
            ref="modalLogout"
            titulo="Cerrar Sesión"
            mensaje="¿Estás seguro de que deseas cerrar sesión en el sistema?"
            icono="🚪"
            textoConfirmar="Sí, cerrar sesión"
            textoCancelar="Cancelar"
            colorConfirmar="#dc3545"
            @confirmar="ejecutarLogout"
        />
    </div>
</template>
<script setup>
import { ref, computed, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import SidebarLider from "@/Components/SidebarLider.vue";
import ModalConfirmar from "@/Components/ModalConfirmar.vue";
import BannerComunidadPendiente from "@/Components/BannerComunidadPendiente.vue";
import SelectBuscable from "@/Components/SelectBuscable.vue";

const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("lid-theme") || "light");
const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("lid-theme", tema.value);
};

const modalLogout = ref(null);
const logout = () => {
    modalLogout.value?.abrir();
};
const ejecutarLogout = () => {
    router.post("/logout");
};
const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});
const props = defineProps({
    equipo: Object,
    responsable: Object,
    comunidades: { type: Array, default: () => [] },
    tiposOrganizacion: { type: Array, default: () => [] },
    tiposProyecto: { type: Array, default: () => [] },
    modalidades: { type: Array, default: () => [] },
});

const comunidadAprobada = computed(
    () => Boolean(props.equipo?.proyecto_comunidad?.pco_comunidad_aprobada || props.equipo?.proyecto_comunidad?.pco_fecha_aprobacion),
);
const estadoPostulacion = computed(
    () => props.equipo?.proyecto_comunidad?.pco_estado_postulacion || "sin_postular",
);
const editandoPostulacion = ref(false);
const enviandoPostulacion = ref(false);

const opcionesComunidadesCatalogo = computed(() => [
    { value: "", label: "Seleccionar comunidad..." },
    ...(props.comunidades || []).map((c) => ({
        value: c.com_id,
        label: c.com_nombre,
        sublabel: c.com_ubicacion || "",
    })),
]);

const opcionesTiposOrganizacion = computed(() => [
    { value: "", label: "Tipo organización *" },
    ...(props.tiposOrganizacion || []).map((t) => ({
        value: t.tor_id,
        label: t.tor_nombre,
    })),
]);

const opcionesModalidadesProyecto = computed(() => [
    { value: "", label: "Seleccionar modalidad..." },
    ...(props.modalidades || []).map((m) => ({
        value: m.mpr_id,
        label: m.mpr_nombre,
    })),
]);

const opcionesTiposProyecto = computed(() => [
    { value: "", label: "Seleccionar línea..." },
    ...(props.tiposProyecto || []).map((tp) => ({
        value: tp.tpr_id,
        label: tp.tpr_nombre,
    })),
]);

const formPost = ref({
    modo_comunidad: "catalogo",
    pco_id_com: props.equipo?.proyecto_comunidad?.pco_id_com || "",
    nueva_comunidad_nombre: "",
    nueva_comunidad_ubicacion: "",
    nueva_comunidad_tor: "",
    rco_nombre: props.responsable?.rco_nombre?.split(" ")[0] || "",
    rco_apellido: props.responsable?.rco_nombre?.split(" ").slice(1).join(" ") || "",
    rco_cedula: props.responsable?.rco_cedula || "",
    rco_telefono: props.responsable?.rco_telefono || "",
    rco_cargo: props.responsable?.rco_cargo || "",
    pco_area_comunidad: props.equipo?.proyecto_comunidad?.pco_area_comunidad || "",
    pco_resumen: props.equipo?.proyecto_comunidad?.pco_resumen || "",
    pco_id_tpr: props.equipo?.proyecto_comunidad?.pco_id_tpr || "",
    pco_id_mpr: props.equipo?.proyecto_comunidad?.pco_id_mpr || "",
    pco_id_pco_previo: props.equipo?.proyecto_comunidad?.pco_id_pco_previo || "",
});

const enviarPostulacion = () => {
    enviandoPostulacion.value = true;
    router.post("/lider/proyecto/postular", formPost.value, {
        preserveScroll: true,
        onSuccess: () => {
            enviandoPostulacion.value = false;
            editandoPostulacion.value = false;
        },
        onError: () => {
            enviandoPostulacion.value = false;
        },
    });
};
const mostrarFormResponsable = ref(false);
const formResponsable = ref({
    rco_cedula: "",
    rco_nombre: "",
    rco_apellido: "",
    rco_cargo: "",
    rco_telefono: "",
    rco_email: "",
    rco_fecha_inicio: "",
});

const guardarResponsable = () => {
    router.post("/lider/responsable", formResponsable.value, {
        preserveScroll: true,
        onSuccess: () => {
            mostrarFormResponsable.value = false;
        },
    });
};
const notifNoLeidas = ref(0);
onMounted(() => {
    fetch("/notificaciones/lista")
        .then((r) => r.json())
        .then((d) => {
            notifNoLeidas.value = d.filter((n) => !n.not_leida).length;
        })
        .catch(() => {});
});
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
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 0.65rem 0;
    border-bottom: 1px solid var(--border);
    font-size: 0.9rem;
}
.info-row:last-child {
    border-bottom: none;
}
.info-label {
    color: var(--text-muted);
    font-weight: 700;
    font-size: 0.82rem;
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
[data-theme="dark"] .bw {
    background: rgba(255, 193, 7, 0.25);
    color: #ffe69c;
    border-color: rgba(255, 193, 7, 0.4);
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
