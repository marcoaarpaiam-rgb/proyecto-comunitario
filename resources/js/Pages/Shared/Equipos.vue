<template>
    <AppLayout page-title="Equipos">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Equipos de Proyecto</h4>
                <p class="text-muted small mb-0">
                    Gestión de equipos e integrantes
                </p>
            </div>
            <button
                v-if="puedeCrear"
                class="btn btn-danger fw-semibold"
                @click="abrirModalCrear()"
            >
                <i class="bi bi-plus-lg me-2"></i>Nuevo Equipo
            </button>
        </div>

        <div
            v-if="$page.props.errors?.integrantes"
            class="alert alert-danger alert-dismissible fade show mb-4"
        >
            {{ $page.props.errors.integrantes }}
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>
        <div
            v-if="$page.props.errors?.equ_numero_grupo"
            class="alert alert-danger alert-dismissible fade show mb-4"
        >
            {{ $page.props.errors.equ_numero_grupo }}
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="alert"
            ></button>
        </div>

        <TablaBuscable
            :items="equipos"
            :campos-busqueda="[
                'equ_codigo',
                'equ_titulo',
                'seccion.sec_codigo',
                'trayecto.tra_nombre',
            ]"
            :por-pagina="8"
            placeholder="Buscar por código, título o sección..."
        >
            <template #default="{ registros }">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Código</th>
                                    <th>Título</th>
                                    <th>Sección</th>
                                    <th>Trayecto</th>
                                    <th>Integrantes</th>
                                    <th>Comunidad</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="registros.length === 0">
                                    <td
                                        colspan="7"
                                        class="text-center text-muted py-4"
                                    >
                                        No se encontraron equipos
                                    </td>
                                </tr>
                                <tr v-for="equ in registros" :key="equ.equ_id">
                                    <td class="ps-4 fw-bold text-danger">
                                        {{ equ.equ_codigo }}
                                    </td>
                                    <td>
                                        <div class="fw-semibold small">
                                            {{ equ.equ_titulo }}
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="badge bg-primary bg-opacity-10 text-primary"
                                        >
                                            {{ equ.seccion?.sec_codigo }}
                                        </span>
                                    </td>
                                    <td class="text-muted small">
                                        {{ equ.trayecto?.tra_nombre }}
                                    </td>
                                    <td>
                                        <button
                                            class="btn btn-sm btn-outline-secondary"
                                            @click="verIntegrantes(equ)"
                                        >
                                            <i class="bi bi-people me-1"></i>
                                            {{ equ.integrantes?.length || 0 }}
                                        </button>
                                    </td>
                                    <td class="small text-muted">
                                        {{
                                            equ.proyecto_comunidad?.comunidad
                                                ?.com_nombre || "—"
                                        }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <a
                                            :href="`/coordinador/equipos/${equ.equ_id}/expediente`"
                                            class="btn btn-sm btn-outline-info me-1"
                                            title="Ver expediente"
                                        >
                                            <i class="bi bi-folder2-open"></i>
                                        </a>
                                        <button
                                            class="btn btn-sm btn-outline-primary me-1"
                                            @click="abrirModalEditar(equ)"
                                            title="Editar título"
                                        >
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            @click="abrirDisolver(equ)"
                                            title="Disolver equipo"
                                        >
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </template>
        </TablaBuscable>

        <!-- Modal Crear Equipo -->
        <div class="modal fade" id="modalCrearEquipo" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            Nuevo Equipo
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <form
                        @submit.prevent="
                            pasoActual === 1 ? irPaso2() : guardarNuevo()
                        "
                    >
                        <div class="modal-body">
                            <!-- Indicador de pasos -->
                            <div class="d-flex align-items-center mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                        :class="
                                            pasoActual >= 1
                                                ? 'bg-danger text-white'
                                                : 'bg-light text-muted'
                                        "
                                        style="width: 32px; height: 32px"
                                    >
                                        1
                                    </div>
                                    <span
                                        class="small fw-semibold"
                                        :class="
                                            pasoActual >= 1
                                                ? 'text-danger'
                                                : 'text-muted'
                                        "
                                    >
                                        Equipo e Integrantes
                                    </span>
                                </div>
                                <div
                                    class="flex-grow-1 mx-3 border-top"
                                    :class="
                                        pasoActual >= 2
                                            ? 'border-danger'
                                            : 'border-secondary'
                                    "
                                ></div>
                                <div class="d-flex align-items-center gap-2">
                                    <div
                                        class="rounded-circle d-flex align-items-center justify-content-center fw-bold"
                                        :class="
                                            pasoActual >= 2
                                                ? 'bg-danger text-white'
                                                : 'bg-light text-muted'
                                        "
                                        style="width: 32px; height: 32px"
                                    >
                                        2
                                    </div>
                                    <span
                                        class="small fw-semibold"
                                        :class="
                                            pasoActual >= 2
                                                ? 'text-danger'
                                                : 'text-muted'
                                        "
                                    >
                                        Proyecto (opcional)
                                    </span>
                                </div>
                            </div>

                            <!-- PASO 1: Equipo e integrantes -->
                            <div v-if="pasoActual === 1">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">
                                            Sección
                                            <span class="text-danger">*</span>
                                        </label>
                                        <select
                                            v-model="formNuevo.equ_id_sec"
                                            class="form-select"
                                            :class="{
                                                'is-invalid':
                                                    errores.equ_id_sec,
                                            }"
                                            @change="onSeccionChange"
                                        >
                                            <option value="">
                                                Seleccionar...
                                            </option>
                                            <option
                                                v-for="sec in secciones"
                                                :key="sec.sec_id"
                                                :value="sec.sec_id"
                                            >
                                                {{ sec.sec_codigo }} —
                                                {{ sec.trayecto?.tra_nombre }}
                                                ({{ sec.turno?.tur_nombre }})
                                            </option>
                                        </select>
                                        <div
                                            v-if="errores.equ_id_sec"
                                            class="invalid-feedback"
                                        >
                                            {{ errores.equ_id_sec }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                            >Trayecto (automático)</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control bg-light"
                                            :value="
                                                trayectoSeleccionado ||
                                                'Se asigna al seleccionar sección'
                                            "
                                            readonly
                                        />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold">
                                            N° de Grupo
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input
                                            v-model="formNuevo.equ_numero_grupo"
                                            type="number"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    errores.equ_numero_grupo,
                                            }"
                                            min="1"
                                            placeholder="Ej: 1"
                                        />
                                        <div
                                            v-if="errores.equ_numero_grupo"
                                            class="invalid-feedback"
                                        >
                                            {{ errores.equ_numero_grupo }}
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold"
                                            >Código (automático)</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control bg-light fw-bold text-danger"
                                            :value="codigoPreview"
                                            readonly
                                        />
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">
                                            Título del Proyecto
                                            <span class="text-danger">*</span>
                                        </label>
                                        <input
                                            v-model="formNuevo.equ_titulo"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    errores.equ_titulo,
                                            }"
                                            placeholder="Ej: Sistema de Control de Inventario..."
                                        />
                                        <div
                                            v-if="errores.equ_titulo"
                                            class="invalid-feedback"
                                        >
                                            {{ errores.equ_titulo }}
                                        </div>
                                    </div>
                                </div>

                                <hr class="my-3" />
                                <div
                                    class="d-flex justify-content-between align-items-center mb-3"
                                >
                                    <h6 class="fw-bold mb-0">
                                        <i
                                            class="bi bi-people-fill me-2 text-danger"
                                        ></i>
                                        Integrantes ({{
                                            formNuevo.integrantes.length
                                        }}/6)
                                    </h6>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        @click="agregarIntegrante"
                                        :disabled="
                                            formNuevo.integrantes.length >= 6
                                        "
                                    >
                                        <i class="bi bi-plus me-1"></i>Agregar
                                    </button>
                                </div>

                                <div
                                    v-if="errores.integrantes"
                                    class="alert alert-danger py-2 small mb-3"
                                >
                                    {{ errores.integrantes }}
                                </div>

                                <div
                                    v-for="(item, idx) in formNuevo.integrantes"
                                    :key="idx"
                                    class="row g-2 mb-2 align-items-center"
                                >
                                    <div class="col-md-7">
                                        <select
                                            v-model="item.usu_id"
                                            class="form-select form-select-sm"
                                        >
                                            <option value="">
                                                Seleccionar estudiante...
                                            </option>
                                            <option
                                                v-for="est in estudiantesDisponibles(
                                                    item.usu_id,
                                                )"
                                                :key="est.usu_id"
                                                :value="est.usu_id"
                                            >
                                                {{ est.usu_primer_nombre }}
                                                {{ est.usu_primer_apellido }}
                                                ({{ est.usu_cedula }})
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-check ms-2">
                                            <input
                                                type="radio"
                                                class="form-check-input"
                                                name="lider"
                                                :value="idx"
                                                v-model="liderIndex"
                                                @change="actualizarLider"
                                            />
                                            <label
                                                class="form-check-label small fw-semibold text-danger"
                                            >
                                                Líder
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger w-100"
                                            @click="quitarIntegrante(idx)"
                                            :disabled="
                                                formNuevo.integrantes.length ===
                                                1
                                            "
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="alert alert-info py-2 small mt-3">
                                    <i class="bi bi-info-circle me-1"></i>
                                    El líder recibirá acceso al sistema con su
                                    cédula como usuario y contraseña inicial.
                                </div>
                            </div>

                            <!-- PASO 2: Proyecto (opcional) -->
                            <div v-if="pasoActual === 2">
                                <div
                                    class="alert alert-light border small mb-3"
                                >
                                    <i
                                        class="bi bi-info-circle me-1 text-primary"
                                    ></i>
                                    Este paso es opcional. Puedes asignar el
                                    proyecto ahora o después desde el módulo
                                    Proyectos.
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                            >Comunidad</label
                                        >
                                        <select
                                            v-model="formProyecto.pco_id_com"
                                            class="form-select"
                                        >
                                            <option value="">
                                                Seleccionar comunidad...
                                            </option>
                                            <option
                                                v-for="com in comunidades"
                                                :key="com.com_id"
                                                :value="com.com_id"
                                            >
                                                {{ com.com_nombre }} —
                                                {{ com.com_ubicacion }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                            >Tipo de Proyecto</label
                                        >
                                        <select
                                            v-model="formProyecto.pco_id_tpr"
                                            class="form-select"
                                        >
                                            <option value="">
                                                Seleccionar tipo...
                                            </option>
                                            <option
                                                v-for="tipo in tiposProyecto"
                                                :key="tipo.tpr_id"
                                                :value="tipo.tpr_id"
                                            >
                                                {{ tipo.tpr_nombre }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                            >Modalidad</label
                                        >
                                        <select
                                            v-model="formProyecto.pco_id_mpr"
                                            class="form-select"
                                        >
                                            <option value="">
                                                Seleccionar modalidad...
                                            </option>
                                            <option
                                                v-for="mod in modalidades"
                                                :key="mod.mpr_id"
                                                :value="mod.mpr_id"
                                            >
                                                {{ mod.mpr_nombre }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                            >Fecha Límite</label
                                        >
                                        <input
                                            v-model="
                                                formProyecto.pco_fecha_limite
                                            "
                                            type="date"
                                            class="form-control"
                                        />
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold">
                                            Resumen
                                            <small class="text-muted"
                                                >(máx. 150 caracteres)</small
                                            >
                                        </label>
                                        <textarea
                                            v-model="formProyecto.pco_resumen"
                                            class="form-control"
                                            rows="2"
                                            maxlength="150"
                                            placeholder="Describe brevemente el proyecto..."
                                        ></textarea>
                                        <small class="text-muted">
                                            {{
                                                formProyecto.pco_resumen.length
                                            }}/150
                                        </small>
                                    </div>

                                    <!-- Beneficiarios -->
                                    <div class="col-12">
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-2"
                                        >
                                            <label
                                                class="form-label fw-semibold mb-0"
                                                >Beneficiarios</label
                                            >
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                @click="agregarBeneficiario"
                                            >
                                                <i class="bi bi-plus me-1"></i
                                                >Agregar
                                            </button>
                                        </div>
                                        <div
                                            v-for="(
                                                ben, idx
                                            ) in formProyecto.beneficiarios"
                                            :key="idx"
                                            class="row g-2 mb-2 align-items-center"
                                        >
                                            <div class="col-7">
                                                <select
                                                    v-model="ben.tbe_id"
                                                    class="form-select form-select-sm"
                                                >
                                                    <option value="">
                                                        Tipo de beneficiario...
                                                    </option>
                                                    <option
                                                        v-for="tbe in tipoBeneficiario"
                                                        :key="tbe.tbe_id"
                                                        :value="tbe.tbe_id"
                                                    >
                                                        {{ tbe.tbe_nombre }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <input
                                                    v-model="ben.cantidad"
                                                    type="number"
                                                    class="form-control form-control-sm"
                                                    placeholder="Cantidad"
                                                    min="1"
                                                />
                                            </div>
                                            <div class="col-2">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger w-100"
                                                    @click="
                                                        quitarBeneficiario(idx)
                                                    "
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="modal-footer border-0 justify-content-between"
                        >
                            <div>
                                <button
                                    v-if="pasoActual === 2"
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    @click="pasoActual = 1"
                                >
                                    <i class="bi bi-arrow-left me-1"></i>Atrás
                                </button>
                            </div>
                            <div class="d-flex gap-2">
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal"
                                >
                                    Cancelar
                                </button>
                                <button
                                    v-if="pasoActual === 1"
                                    type="submit"
                                    class="btn btn-danger fw-semibold"
                                >
                                    Siguiente
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                                <button
                                    v-if="pasoActual === 2"
                                    type="submit"
                                    class="btn btn-danger fw-semibold"
                                    :disabled="loading"
                                >
                                    <span
                                        v-if="loading"
                                        class="spinner-border spinner-border-sm me-2"
                                    ></span>
                                    Guardar Equipo
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Editar (solo título) -->
        <div class="modal fade" id="modalEditarEquipo" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            Editar Equipo
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <form @submit.prevent="guardarEdicion">
                        <div class="modal-body">
                            <div class="alert alert-light border small mb-3">
                                <i
                                    class="bi bi-info-circle me-1 text-primary"
                                ></i>
                                El código, sección y trayecto no se pueden
                                modificar una vez creado el equipo.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Código</label
                                >
                                <input
                                    type="text"
                                    class="form-control bg-light fw-bold text-danger"
                                    :value="equipoEditando?.equ_codigo"
                                    readonly
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Título del Proyecto
                                    <span class="text-danger">*</span>
                                </label>
                                <input
                                    v-model="formEditar.equ_titulo"
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': erroresEditar.equ_titulo,
                                    }"
                                />
                                <div
                                    v-if="erroresEditar.equ_titulo"
                                    class="invalid-feedback"
                                >
                                    {{ erroresEditar.equ_titulo }}
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
                                class="btn btn-danger fw-semibold"
                                :disabled="loadingEditar"
                            >
                                <span
                                    v-if="loadingEditar"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span>
                                Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Ver Integrantes -->
        <div class="modal fade" id="modalIntegrantes" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header" style="background: #1f3864">
                        <h5 class="modal-title text-white fw-bold">
                            {{ equipoSeleccionado?.equ_codigo }} — Integrantes e
                            Historial
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <div class="modal-body p-0">
                        <!-- Tabs -->
                        <ul class="nav nav-tabs px-3 pt-2">
                            <li class="nav-item">
                                <button
                                    class="nav-link fw-semibold"
                                    :class="
                                        tabActivo === 'integrantes'
                                            ? 'active'
                                            : ''
                                    "
                                    @click="tabActivo = 'integrantes'"
                                >
                                    <i class="bi bi-people me-1"></i>Integrantes
                                </button>
                            </li>
                            <li class="nav-item">
                                <button
                                    class="nav-link fw-semibold"
                                    :class="
                                        tabActivo === 'historial'
                                            ? 'active'
                                            : ''
                                    "
                                    @click="cargarHistorial"
                                >
                                    <i class="bi bi-clock-history me-1"></i
                                    >Historial
                                </button>
                            </li>
                        </ul>

                        <!-- Tab Integrantes -->
                        <div v-if="tabActivo === 'integrantes'" class="p-3">
                            <!-- Cambiar líder -->
                            <div class="alert alert-light border mb-3">
                                <h6 class="fw-bold small mb-2">
                                    <i
                                        class="bi bi-arrow-repeat text-danger me-1"
                                    ></i>
                                    Cambiar Líder
                                </h6>
                                <div class="d-flex gap-2">
                                    <select
                                        v-model="nuevoLiderId"
                                        class="form-select form-select-sm"
                                    >
                                        <option value="">
                                            Seleccionar nuevo líder...
                                        </option>
                                        <option
                                            v-for="int in integrantesNoLider"
                                            :key="int.ein_id"
                                            :value="int.ein_id"
                                        >
                                            {{ int.usuario?.usu_primer_nombre }}
                                            {{
                                                int.usuario?.usu_primer_apellido
                                            }}
                                        </option>
                                    </select>
                                    <button
                                        class="btn btn-sm btn-danger fw-semibold"
                                        :disabled="!nuevoLiderId"
                                        @click="confirmarCambioLider"
                                    >
                                        Cambiar
                                    </button>
                                </div>
                            </div>

                            <!-- Lista de integrantes -->
                            <ul class="list-group list-group-flush">
                                <li
                                    v-for="int in equipoSeleccionado?.integrantes"
                                    :key="int.ein_id"
                                    class="list-group-item d-flex align-items-center gap-3 px-2 py-2"
                                    :class="!int.ein_status ? 'opacity-50' : ''"
                                >
                                    <div class="avatar-sm">
                                        {{
                                            int.usuario?.usu_primer_nombre?.[0]
                                        }}
                                        {{
                                            int.usuario
                                                ?.usu_primer_apellido?.[0]
                                        }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold small">
                                            {{ int.usuario?.usu_primer_nombre }}
                                            {{
                                                int.usuario?.usu_primer_apellido
                                            }}
                                        </div>
                                        <div
                                            class="text-muted"
                                            style="font-size: 0.75rem"
                                        >
                                            CI: {{ int.usuario?.usu_cedula }}
                                            <span
                                                v-if="!int.ein_status"
                                                class="text-danger ms-2"
                                            >
                                                (Removido)
                                            </span>
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center gap-2"
                                    >
                                        <span
                                            v-if="int.ein_es_lider"
                                            class="badge bg-danger"
                                            >Líder</span
                                        >
                                        <span v-else class="badge bg-secondary"
                                            >Integrante</span
                                        >
                                        <button
                                            v-if="
                                                int.ein_status &&
                                                !int.ein_es_lider
                                            "
                                            class="btn btn-sm btn-outline-danger"
                                            @click="removerIntegrante(int)"
                                            title="Remover del equipo"
                                        >
                                            <i class="bi bi-person-dash"></i>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Tab Historial -->
                        <div v-if="tabActivo === 'historial'" class="p-3">
                            <div
                                v-if="cargandoHistorial"
                                class="text-center py-4"
                            >
                                <div class="spinner-border text-danger"></div>
                            </div>
                            <div
                                v-else-if="historialEquipo.length === 0"
                                class="text-center text-muted py-4 small"
                            >
                                No hay eventos registrados
                            </div>
                            <ul v-else class="list-group list-group-flush">
                                <li
                                    v-for="evt in historialEquipo"
                                    :key="evt.heq_id"
                                    class="list-group-item px-2 py-3"
                                >
                                    <div class="d-flex align-items-start gap-3">
                                        <div
                                            class="badge-evento mt-1"
                                            :class="
                                                colorEvento(
                                                    evt.tipo_evento?.tee_nombre,
                                                )
                                            "
                                        >
                                            <i
                                                :class="
                                                    iconoEvento(
                                                        evt.tipo_evento
                                                            ?.tee_nombre,
                                                    )
                                                "
                                            ></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="fw-semibold small">
                                                {{
                                                    evt.tipo_evento?.tee_nombre
                                                }}
                                            </div>
                                            <div class="text-muted small">
                                                {{ evt.heq_motivo }}
                                            </div>
                                            <div
                                                class="text-muted"
                                                style="font-size: 0.72rem"
                                            >
                                                {{ evt.heq_fecha_evento }}
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
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

        <!-- Modal Disolver -->
        <div class="modal fade" id="modalDisolver" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-x-circle me-2"></i>Disolver Equipo
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <form @submit.prevent="confirmarDisolver">
                        <div class="modal-body">
                            <div class="alert alert-danger small mb-3">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Esta acción desactivará el equipo y todos sus
                                integrantes. Se registrará en el historial. No
                                se puede deshacer.
                            </div>
                            <div class="mb-3">
                                <p class="fw-semibold mb-1">
                                    Equipo:
                                    <span class="text-danger">
                                        {{ equipoADisolver?.equ_codigo }}
                                    </span>
                                </p>
                                <p class="text-muted small mb-0">
                                    {{ equipoADisolver?.equ_titulo }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Motivo de la disolución
                                    <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    v-model="motivoDisolucion"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            !motivoDisolucion &&
                                            intentoDisolver,
                                    }"
                                    rows="3"
                                    placeholder="Explica por qué se disuelve el equipo..."
                                ></textarea>
                                <div
                                    v-if="!motivoDisolucion && intentoDisolver"
                                    class="invalid-feedback"
                                >
                                    El motivo es obligatorio.
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
                                class="btn btn-danger fw-semibold"
                            >
                                <i class="bi bi-x-circle me-2"></i>Disolver
                                Equipo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import TablaBuscable from "@/Components/TablaBuscable.vue";
import { Modal } from "bootstrap";

// ── Estado ──────────────────────────────────────────
const errores = ref({});
const erroresEditar = ref({});
const loading = ref(false);
const loadingEditar = ref(false);
const equipoEditando = ref(null);
const equipoSeleccionado = ref(null);
const equipoADisolver = ref(null);
const motivoDisolucion = ref("");
const intentoDisolver = ref(false);
const liderIndex = ref(0);

const formNuevo = ref({
    equ_id_sec: "",
    equ_numero_grupo: "",
    equ_titulo: "",
    integrantes: [{ usu_id: "", es_lider: true }],
});

const formEditar = ref({ equ_titulo: "" });

// ── Sección seleccionada ─────────────────────────────
const seccionSeleccionada = computed(() =>
    props.secciones.find((s) => s.sec_id == formNuevo.value.equ_id_sec),
);

const trayectoSeleccionado = computed(
    () => seccionSeleccionada.value?.trayecto?.tra_nombre || "",
);

// ── Código preview ───────────────────────────────────
const codigoPreview = computed(() => {
    const sec = seccionSeleccionada.value;
    if (!sec || !formNuevo.value.equ_numero_grupo) return "—";
    const pnf = sec.trayecto?.pnf?.pnf_siglas || "PNFI";
    const tra = sec.trayecto?.tra_numero || "";
    return `${pnf.toUpperCase()}-${sec.sec_codigo}-G${formNuevo.value.equ_numero_grupo}-T${tra}`;
});

const onSeccionChange = () => {
    // Limpiar número de grupo al cambiar sección
    formNuevo.value.equ_numero_grupo = "";
};

// ── Integrantes ──────────────────────────────────────
const estudiantesDisponibles = (idActual) => {
    const seleccionados = formNuevo.value.integrantes
        .map((i) => i.usu_id)
        .filter((id) => id && id !== idActual);
    return props.estudiantes.filter((e) => !seleccionados.includes(e.usu_id));
};

const actualizarLider = () => {
    formNuevo.value.integrantes.forEach((item, idx) => {
        item.es_lider = idx === liderIndex.value;
    });
};

const agregarIntegrante = () => {
    if (formNuevo.value.integrantes.length >= 6) return;
    formNuevo.value.integrantes.push({ usu_id: "", es_lider: false });
};

const quitarIntegrante = (idx) => {
    formNuevo.value.integrantes.splice(idx, 1);
    if (liderIndex.value >= formNuevo.value.integrantes.length) {
        liderIndex.value = 0;
    }
    actualizarLider();
};

// ── Modales ──────────────────────────────────────────
let modalCrear = null;
let modalEditar = null;
let modalInts = null;
let modalDisolver = null;

const abrirModalCrear = () => {
    pasoActual.value = 1;
    formNuevo.value = {
        equ_id_sec: "",
        equ_numero_grupo: "",
        equ_titulo: "",
        integrantes: [{ usu_id: "", es_lider: true }],
    };
    formProyecto.value = {
        pco_id_com: "",
        pco_id_tpr: "",
        pco_id_mpr: "",
        pco_fecha_limite: "",
        pco_resumen: "",
        beneficiarios: [],
    };
    liderIndex.value = 0;
    errores.value = {};
    modalCrear =
        modalCrear || new Modal(document.getElementById("modalCrearEquipo"));
    modalCrear.show();
};

const abrirModalEditar = (equ) => {
    equipoEditando.value = equ;
    formEditar.value = { equ_titulo: equ.equ_titulo };
    erroresEditar.value = {};
    modalEditar =
        modalEditar || new Modal(document.getElementById("modalEditarEquipo"));
    modalEditar.show();
};

const verIntegrantes = (equ) => {
    equipoSeleccionado.value = equ;
    modalInts =
        modalInts || new Modal(document.getElementById("modalIntegrantes"));
    modalInts.show();
};

const abrirDisolver = (equ) => {
    equipoADisolver.value = equ;
    motivoDisolucion.value = "";
    intentoDisolver.value = false;
    modalDisolver =
        modalDisolver || new Modal(document.getElementById("modalDisolver"));
    modalDisolver.show();
};

// ── CRUD ─────────────────────────────────────────────
const guardarNuevo = () => {
    actualizarLider();
    loading.value = true;
    const base = window.location.pathname.includes("profesor")
        ? "/profesor/equipos"
        : "/coordinador/equipos";

    const payload = {
        ...formNuevo.value,
        proyecto: formProyecto.value.pco_id_com ? formProyecto.value : null,
    };

    router.post(base, payload, {
        onError: (e) => {
            errores.value = e;
            loading.value = false;
            pasoActual.value = 1;
        },
        onSuccess: () => {
            loading.value = false;
            pasoActual.value = 1;
            modalCrear?.hide();
        },
    });
};

const guardarEdicion = () => {
    loadingEditar.value = true;
    const base = window.location.pathname.includes("profesor")
        ? "/profesor/equipos"
        : "/coordinador/equipos";
    router.put(`${base}/${equipoEditando.value.equ_id}`, formEditar.value, {
        onError: (e) => {
            erroresEditar.value = e;
            loadingEditar.value = false;
        },
        onSuccess: () => {
            loadingEditar.value = false;
            modalEditar?.hide();
        },
    });
};

const confirmarDisolver = () => {
    intentoDisolver.value = true;
    if (!motivoDisolucion.value.trim()) return;
    const base = window.location.pathname.includes("profesor")
        ? "/profesor/equipos"
        : "/coordinador/equipos";
    router.post(
        `${base}/${equipoADisolver.value.equ_id}/disolver`,
        { motivo: motivoDisolucion.value },
        { onSuccess: () => modalDisolver?.hide() },
    );
};
const tabActivo = ref("integrantes");
const historialEquipo = ref([]);
const cargandoHistorial = ref(false);
const nuevoLiderId = ref("");

const integrantesNoLider = computed(() =>
    (equipoSeleccionado.value?.integrantes || []).filter(
        (i) => !i.ein_es_lider && i.ein_status,
    ),
);

const cargarHistorial = async () => {
    tabActivo.value = "historial";
    if (!equipoSeleccionado.value) return;
    cargandoHistorial.value = true;
    const base = window.location.pathname.includes("profesor")
        ? "/profesor"
        : "/coordinador";
    const res = await fetch(
        `${base}/equipos/${equipoSeleccionado.value.equ_id}/historial`,
    );
    historialEquipo.value = await res.json();
    cargandoHistorial.value = false;
};

const removerIntegrante = (integrante) => {
    if (
        !confirm(
            `¿Remover a ${integrante.usuario?.usu_primer_nombre} del equipo?`,
        )
    )
        return;
    const base = window.location.pathname.includes("profesor")
        ? "/profesor"
        : "/coordinador";
    router.delete(
        `${base}/equipos/${equipoSeleccionado.value.equ_id}/integrantes/${integrante.ein_id}`,
        { onSuccess: () => {} },
    );
};

const confirmarCambioLider = () => {
    if (!nuevoLiderId.value) return;
    const int = equipoSeleccionado.value?.integrantes.find(
        (i) => i.ein_id == nuevoLiderId.value,
    );
    const nombre =
        int?.usuario?.usu_primer_nombre +
        " " +
        int?.usuario?.usu_primer_apellido;
    if (!confirm(`¿Asignar a ${nombre} como nuevo líder?`)) return;
    const base = window.location.pathname.includes("profesor")
        ? "/profesor"
        : "/coordinador";
    router.post(
        `${base}/equipos/${equipoSeleccionado.value.equ_id}/cambiar-lider`,
        { nuevo_lider_id: nuevoLiderId.value },
        {
            onSuccess: () => {
                nuevoLiderId.value = "";
            },
        },
    );
};

const colorEvento = (nombre) => {
    if (nombre === "Disuelto") return "evento-rojo";
    if (nombre === "Líder Cambiado") return "evento-amarillo";
    if (nombre === "Integrante Removido") return "evento-naranja";
    if (nombre === "Integrante Agregado") return "evento-verde";
    return "evento-gris";
};

const iconoEvento = (nombre) => {
    if (nombre === "Disuelto") return "bi bi-x-circle";
    if (nombre === "Líder Cambiado") return "bi bi-arrow-repeat";
    if (nombre === "Integrante Removido") return "bi bi-person-dash";
    if (nombre === "Integrante Agregado") return "bi bi-person-plus";
    return "bi bi-circle";
};
const props = defineProps({
    equipos: Array,
    secciones: Array,
    trayectos: Array,
    estudiantes: Array,
    comunidades: { type: Array, default: () => [] },
    tiposProyecto: { type: Array, default: () => [] },
    modalidades: { type: Array, default: () => [] },
    tipoBeneficiario: { type: Array, default: () => [] },
    puedeCrear: { type: Boolean, default: true },
});
const pasoActual = ref(1);

const formProyecto = ref({
    pco_id_com: "",
    pco_id_tpr: "",
    pco_id_mpr: "",
    pco_fecha_limite: "",
    pco_resumen: "",
    beneficiarios: [],
});

const irPaso2 = () => {
    // Validar paso 1 antes de avanzar
    if (!formNuevo.value.equ_id_sec) {
        errores.value.equ_id_sec = "La sección es obligatoria.";
        return;
    }
    if (!formNuevo.value.equ_numero_grupo) {
        errores.value.equ_numero_grupo = "El número de grupo es obligatorio.";
        return;
    }
    if (!formNuevo.value.equ_titulo) {
        errores.value.equ_titulo = "El título es obligatorio.";
        return;
    }
    const sinUsuario = formNuevo.value.integrantes.some((i) => !i.usu_id);
    if (sinUsuario) {
        errores.value.integrantes =
            "Todos los integrantes deben ser seleccionados.";
        return;
    }
    errores.value = {};
    pasoActual.value = 2;
};

const agregarBeneficiario = () => {
    formProyecto.value.beneficiarios.push({ tbe_id: "", cantidad: "" });
};

const quitarBeneficiario = (idx) => {
    formProyecto.value.beneficiarios.splice(idx, 1);
};
const verDisueltos = ref(false);
const reactivar = (equ) => {
    if (!confirm(`¿Reactivar ${equ.equ_codigo}?`)) return;
    const base = window.location.pathname.includes("profesor")
        ? "/profesor"
        : "/coordinador";
    router.post(`${base}/equipos/${equ.equ_id}/reactivar`);
};
</script>

<style>
.avatar-sm {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #1f3864;
    color: white;
    font-weight: 700;
    font-size: 0.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.badge-evento {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
    flex-shrink: 0;
}
.evento-rojo {
    background: #f8d7da;
    color: #842029;
}
.evento-amarillo {
    background: #fff3cd;
    color: #856404;
}
.evento-naranja {
    background: #ffe5d0;
    color: #984c0c;
}
.evento-verde {
    background: #d1e7dd;
    color: #0f5132;
}
.evento-gris {
    background: #e2e3e5;
    color: #41464b;
}
</style>
