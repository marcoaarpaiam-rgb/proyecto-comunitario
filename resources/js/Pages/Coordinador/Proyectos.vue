<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
        "
    >
                <SidebarCoordinador
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
                        <div style="min-width: 170px;">
                            <SelectBuscable
                                v-model="filtroAprobacion"
                                :options="opcionesFiltroAprobacion"
                                size="sm"
                                placeholder="Todos"
                            />
                        </div>
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
                                            {{
                                                codigoLimpio(
                                                    pco.equipo?.equ_codigo,
                                                )
                                            }}
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
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-inline-flex gap-1 align-items-center">
                                            <Link
                                                v-if="pco.pco_id_equ"
                                                :href="`/coordinador/equipos/${pco.pco_id_equ}/expediente`"
                                                class="btn btn-sm btn-outline-primary d-inline-flex align-items-center"
                                                title="Consultar Expediente del Equipo"
                                            >
                                                <i class="bi bi-eye me-1"></i> Consultar Expediente
                                            </Link>
                                        </div>
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
                                    <SelectBuscableEquipo
                                        v-model="form.pco_id_equ"
                                        :equipos="equipos"
                                        :has-error="!!errores.pco_id_equ"
                                        placeholder="🔍 Escribe código o título del equipo..."
                                    />
                                    <div
                                        v-if="errores.pco_id_equ"
                                        class="text-danger small mt-1"
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
                                    <SelectBuscableComunidad
                                        v-model="form.pco_id_com"
                                        :comunidades="comunidades"
                                        :has-error="!!errores.pco_id_com"
                                        size="md"
                                        placeholder="🔍 Escribe para buscar comunidad..."
                                    />
                                    <div
                                        v-if="errores.pco_id_com"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.pco_id_com }}
                                    </div>
                                </div>

                                <!-- RADAR DE COMUNIDAD Y DETECTOR ANTI-DUPLICIDAD EN VIVO -->
                                <div class="col-12" v-if="form.pco_id_com">
                                    <div class="p-3 rounded-3 border bg-light">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="fw-bold text-dark small text-uppercase">
                                                <i class="bi bi-radar text-danger me-1"></i>
                                                Radar de Comunidad: {{ radarData?.comunidad?.com_nombre || 'Consultando...' }}
                                            </span>
                                            <span v-if="loadingRadar" class="spinner-border spinner-border-sm text-danger"></span>
                                            <span v-else-if="radarData" class="badge bg-dark">
                                                {{ radarData.proyectos_activos?.length || 0 }} activos &bull; {{ radarData.proyectos_culminados?.length || 0 }} culminados
                                            </span>
                                        </div>

                                        <!-- Alerta de Similitud de Títulos -->
                                        <div
                                            v-if="radarData?.alerta_similitud?.existe"
                                            class="alert p-3 mb-2 rounded-3"
                                            :class="radarData.alerta_similitud.es_critico ? 'alert-danger border-danger' : 'alert-warning border-warning'"
                                        >
                                            <div class="fw-bold mb-1">
                                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                Posible Duplicidad Detectada (Similitud del {{ radarData.alerta_similitud.porcentaje }}%)
                                            </div>
                                            <p class="small mb-2">{{ radarData.alerta_similitud.mensaje }}</p>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-dark fw-bold mb-2"
                                                @click="marcarComoContinuacion"
                                            >
                                                <i class="bi bi-diagram-2-fill text-danger me-1"></i>
                                                Indicar como Continuación / Escalamiento del Proyecto Previo
                                            </button>

                                            <div class="mt-2 pt-2 border-top">
                                                <label class="form-label fw-bold text-dark small mb-1">
                                                    <i class="bi bi-shield-fill-exclamation text-danger me-1"></i>
                                                    Justificación Obligatoria contra Duplicidad <span class="text-danger">*</span>
                                                </label>
                                                <textarea
                                                    class="form-control form-control-sm bg-white"
                                                    rows="2"
                                                    v-model="form.pco_justificacion_duplicidad"
                                                    placeholder="Explique claramente el motivo (ej. Fase II, módulo complementario, nuevo alcance o usuarios distintos)..."
                                                ></textarea>
                                                <div class="form-text small" :class="(form.pco_justificacion_duplicidad || '').length >= 15 ? 'text-success' : 'text-danger'">
                                                    Mínimo 15 caracteres (actual: {{ (form.pco_justificacion_duplicidad || '').length }})
                                                </div>
                                                <div v-if="errores.justificacion_duplicidad" class="text-danger small mt-1 fw-semibold">
                                                    {{ errores.justificacion_duplicidad }}
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Lista de proyectos activos y culminados -->
                                        <div v-if="radarData && radarData.total_historico > 0" class="row g-2 small">
                                            <div class="col-md-6" v-if="radarData.proyectos_activos?.length">
                                                <div class="fw-bold text-danger mb-1">En desarrollo actual:</div>
                                                <ul class="list-unstyled mb-0">
                                                    <li v-for="pa in radarData.proyectos_activos" :key="pa.pco_id" class="mb-1 p-2 bg-white border rounded">
                                                        <strong class="text-dark">{{ pa.equ_codigo }}</strong>: {{ pa.equ_titulo }}
                                                        <div class="text-muted" style="font-size: 0.75rem;">{{ pa.tipo_proyecto }} &bull; {{ pa.trayecto }}</div>
                                                    </li>
                                                </ul>
                                            </div>
                                            <div class="col-md-6" v-if="radarData.proyectos_culminados?.length">
                                                <div class="fw-bold text-success mb-1">Culminados en lapsos anteriores:</div>
                                                <ul class="list-unstyled mb-0">
                                                    <li v-for="pc in radarData.proyectos_culminados" :key="pc.pco_id" class="mb-1 p-2 bg-white border rounded">
                                                        <strong class="text-dark">{{ pc.equ_codigo }}</strong>: {{ pc.equ_titulo }}
                                                        <div class="text-muted" style="font-size: 0.75rem;">Nota: {{ pc.calificacion ? pc.calificacion + ' pts' : 'Archivado' }}</div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div v-else-if="radarData && !loadingRadar" class="small text-success fw-semibold">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            Comunidad sin proyectos previos registrados en el historial.
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Línea de Investigación
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <SelectBuscable
                                        v-model="form.pco_id_tpr"
                                        :options="opcionesTiposInvProy"
                                        :has-error="!!errores.pco_id_tpr"
                                        placeholder="Seleccionar..."
                                        @change="verificarOtroTipoInv"
                                    />
                                    <div v-if="creandoOtroTipoInv" class="mt-2 p-2 rounded border bg-light">
                                        <label class="small fw-bold text-danger mb-1">Nombre de la nueva Línea de Investigación:</label>
                                        <div class="input-group input-group-sm">
                                            <input
                                                v-model="nuevoTipoInvNombre"
                                                type="text"
                                                class="form-control"
                                                placeholder="Ej: Redes Comunitarias, Ciberseguridad Popular..."
                                                @keyup.enter.prevent="guardarNuevoTipoInv"
                                            />
                                            <button
                                                class="btn btn-danger fw-bold"
                                                type="button"
                                                :disabled="!nuevoTipoInvNombre.trim() || guardandoTipoInv"
                                                @click="guardarNuevoTipoInv"
                                            >
                                                <span v-if="guardandoTipoInv" class="spinner-border spinner-border-sm me-1"></span>
                                                <i v-else class="bi bi-check-lg me-1"></i>Crear
                                            </button>
                                            <button
                                                class="btn btn-outline-secondary"
                                                type="button"
                                                @click="creandoOtroTipoInv = false"
                                            >
                                                Cancelar
                                            </button>
                                        </div>
                                    </div>
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
                                    <SelectBuscable
                                        v-model="form.pco_id_mpr"
                                        :options="opcionesModalidadesProy"
                                        :has-error="!!errores.pco_id_mpr"
                                        placeholder="Seleccionar..."
                                        @change="verificarOtraModalidad"
                                    />
                                    <div v-if="creandoOtraModalidad" class="mt-2 p-2 rounded border bg-light">
                                        <label class="small fw-bold text-danger mb-1">Nombre de la nueva Modalidad:</label>
                                        <div class="input-group input-group-sm">
                                            <input
                                                v-model="nuevaModalidadNombre"
                                                type="text"
                                                class="form-control"
                                                placeholder="Ej: Desarrollo de Software, Soporte Técnico..."
                                                @keyup.enter.prevent="guardarNuevaModalidad"
                                            />
                                            <button
                                                class="btn btn-danger fw-bold"
                                                type="button"
                                                :disabled="!nuevaModalidadNombre.trim() || guardandoModalidad"
                                                @click="guardarNuevaModalidad"
                                            >
                                                <span v-if="guardandoModalidad" class="spinner-border spinner-border-sm me-1"></span>
                                                <i v-else class="bi bi-check-lg me-1"></i>Crear
                                            </button>
                                            <button
                                                class="btn btn-outline-secondary"
                                                type="button"
                                                @click="creandoOtraModalidad = false"
                                            >
                                                Cancelar
                                            </button>
                                        </div>
                                    </div>
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
                                        :min="fechaManana"
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

                                <!-- Beneficiarios (Máximo 10.000 por tipo) -->
                                <div class="col-12 mt-3">
                                    <div
                                        class="d-flex justify-content-between align-items-center mb-2"
                                    >
                                        <label
                                            class="form-label fw-semibold m-0"
                                        >
                                            Beneficiarios del Proyecto
                                            <small class="text-muted fw-normal"
                                                >(Límite máx. 10.000 por
                                                tipo)</small
                                            >
                                        </label>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger rounded-pill"
                                            @click="agregarFilaBeneficiario"
                                        >
                                            <i
                                                class="bi bi-plus-circle me-1"
                                            ></i
                                            >Añadir Beneficiario
                                        </button>
                                    </div>

                                    <div
                                        v-if="errorBeneficiariosAlerta"
                                        class="alert alert-danger py-2 px-3 small mb-2 d-flex align-items-center"
                                    >
                                        <i
                                            class="bi bi-exclamation-triangle-fill me-2 fs-6"
                                        ></i>
                                        <span>{{
                                            errorBeneficiariosAlerta
                                        }}</span>
                                    </div>

                                    <div
                                        v-if="form.beneficiarios.length === 0"
                                        class="p-2 border rounded text-center small text-muted"
                                        style="background: var(--bg-th)"
                                    >
                                        No hay beneficiarios agregados. Haz clic
                                        en "Añadir Beneficiario" para registrar
                                        cantidades (máx. 10.000).
                                    </div>

                                    <div
                                        v-for="(ben, idx) in form.beneficiarios"
                                        :key="idx"
                                        class="row g-2 mb-2 align-items-center"
                                    >
                                        <div class="col-md-7">
                                            <SelectBuscable
                                                v-model="ben.tbe_id"
                                                :options="opcionesBeneficiariosProy"
                                                size="sm"
                                                placeholder="Selecciona tipo de beneficiario..."
                                            />
                                        </div>
                                        <div class="col-md-4">
                                            <div
                                                class="input-group input-group-sm"
                                            >
                                                <span class="input-group-text"
                                                    >Cant.</span
                                                >
                                                <input
                                                    v-model.number="
                                                        ben.cantidad
                                                    "
                                                    type="number"
                                                    min="1"
                                                    max="10000"
                                                    class="form-control form-control-sm"
                                                    :class="{
                                                        'is-invalid':
                                                            ben.cantidad >
                                                                10000 ||
                                                            ben.cantidad < 1,
                                                    }"
                                                    placeholder="1 a 10000"
                                                    required
                                                    @keydown="
                                                        bloquearExcesoBeneficiarios(
                                                            $event,
                                                            ben,
                                                        )
                                                    "
                                                    @input="
                                                        validarCantidadBeneficiario(
                                                            ben,
                                                        )
                                                    "
                                                />
                                            </div>
                                            <div
                                                v-if="ben.cantidad > 10000"
                                                class="text-danger small mt-1 fw-bold"
                                                style="font-size: 0.72rem"
                                            >
                                                ⚠️ Límite excedido: El tope
                                                máximo es 10.000 beneficiarios.
                                            </div>
                                        </div>
                                        <div class="col-md-1 text-end">
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger p-1"
                                                title="Eliminar fila"
                                                @click="
                                                    eliminarFilaBeneficiario(
                                                        idx,
                                                    )
                                                "
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
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
import SidebarCoordinador from "@/Components/SidebarCoordinador.vue";
import SelectBuscable from "@/Components/SelectBuscable.vue";
import { ref, computed, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Modal } from "bootstrap";
import { Link } from "@inertiajs/vue3";
import axios from "axios";
import SelectBuscableComunidad from "@/Components/SelectBuscableComunidad.vue";
import SelectBuscableEquipo from "@/Components/SelectBuscableEquipo.vue";
const menuMaestras = ref(false);
const menuAcademico = ref(false);
const menuSistema = ref(false);
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
    pco_justificacion_duplicidad: "",
    justificacion_duplicidad: "",
    beneficiarios: [],
});
const errores = ref({});
const loading = ref(false);
const itemSeleccionado = ref(null);
const radarData = ref(null);
const loadingRadar = ref(false);

const listaTiposInvestigacion = ref([...(props.tipos || [])]);
const opcionesFiltroAprobacion = [
    { value: "", label: "Todos" },
    { value: "aprobado", label: "Aprobados" },
    { value: "pendiente", label: "Pendientes" },
];
const opcionesTiposInvProy = computed(() => [
    { value: "", label: "Seleccionar..." },
    ...listaTiposInvestigacion.value.map((t) => ({ value: t.tpr_id, label: t.tpr_nombre })),
    { value: "__OTRO__", label: "➕ Otro (Crear nueva Línea de Investigación...)" },
]);
const opcionesModalidadesProy = computed(() => [
    { value: "", label: "Seleccionar..." },
    ...(listaModalidades?.value || props.modalidades || []).map((m) => ({ value: m.mpr_id, label: m.mpr_nombre })),
    { value: "__OTRO__", label: "➕ Otro (Crear nueva Modalidad...)" },
]);
const opcionesBeneficiariosProy = computed(() => [
    { value: "", label: "Selecciona tipo de beneficiario..." },
    ...(props.beneficiarios || []).map((tb) => ({ value: tb.tbe_id, label: tb.tbe_nombre })),
]);
const creandoOtroTipoInv = ref(false);
const nuevoTipoInvNombre = ref("");
const guardandoTipoInv = ref(false);

const verificarOtroTipoInv = () => {
    if (form.value.pco_id_tpr === "__OTRO__") {
        creandoOtroTipoInv.value = true;
        nuevoTipoInvNombre.value = "";
    } else {
        creandoOtroTipoInv.value = false;
    }
};

const guardarNuevoTipoInv = async () => {
    if (!nuevoTipoInvNombre.value.trim()) return;
    guardandoTipoInv.value = true;
    try {
        const res = await axios.post("/catalogos/crear-opcion", {
            catalogo: "linea_investigacion",
            nombre: nuevoTipoInvNombre.value.trim(),
        });
        if (res.data.success) {
            listaTiposInvestigacion.value.push({ tpr_id: res.data.id, tpr_nombre: res.data.nombre });
            form.value.pco_id_tpr = res.data.id;
            creandoOtroTipoInv.value = false;
            nuevoTipoInvNombre.value = "";
        }
    } catch (e) {
        console.error("Error al crear línea de investigación:", e);
    } finally {
        guardandoTipoInv.value = false;
    }
};

const listaModalidades = ref([...(props.modalidades || [])]);
const creandoOtraModalidad = ref(false);
const nuevaModalidadNombre = ref("");
const guardandoModalidad = ref(false);

const verificarOtraModalidad = () => {
    if (form.value.pco_id_mpr === "__OTRO__") {
        creandoOtraModalidad.value = true;
        nuevaModalidadNombre.value = "";
    } else {
        creandoOtraModalidad.value = false;
    }
};

const guardarNuevaModalidad = async () => {
    if (!nuevaModalidadNombre.value.trim()) return;
    guardandoModalidad.value = true;
    try {
        const res = await axios.post("/catalogos/crear-opcion", {
            catalogo: "modalidad",
            nombre: nuevaModalidadNombre.value.trim(),
        });
        if (res.data.success) {
            listaModalidades.value.push({ mpr_id: res.data.id, mpr_nombre: res.data.nombre });
            form.value.pco_id_mpr = res.data.id;
            creandoOtraModalidad.value = false;
            nuevaModalidadNombre.value = "";
        }
    } catch (e) {
        console.error("Error al crear modalidad:", e);
    } finally {
        guardandoModalidad.value = false;
    }
};

const consultarRadarComunidad = async () => {
    const comId = form.value.pco_id_com;
    if (!comId) {
        radarData.value = null;
        return;
    }
    loadingRadar.value = true;
    try {
        const equipoSel = (props.equipos || []).find((e) => e.equ_id == form.value.pco_id_equ);
        const titulo = equipoSel?.equ_titulo || "";
        const res = await axios.get(`/api/comunidades/${comId}/radar-proyectos`, {
            params: {
                titulo: titulo,
                equipo_id: form.value.pco_id_equ || "",
            },
        });
        radarData.value = res.data;
    } catch (e) {
        console.error("Error consultando radar de comunidad:", e);
    } finally {
        loadingRadar.value = false;
    }
};

watch(
    () => [form.value.pco_id_com, form.value.pco_id_equ],
    () => {
        consultarRadarComunidad();
    }
);

const marcarComoContinuacion = () => {
    const modCont = (props.modalidades || []).find((m) =>
        ["continuación", "continuacion", "reingeniería", "reingenieria"].includes(
            (m.mpr_nombre || "").toLowerCase()
        )
    );
    if (modCont) {
        form.value.pco_id_mpr = modCont.mpr_id;
    }
};
let modal = null;
let modalAp = null;
let modalDes = null;
const codigoLimpio = (codigo) => {
    if (!codigo) return "";
    return String(codigo).replace(/#DIS\d+$/, "");
};

const fechaManana = computed(() => {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    return d.toISOString().split("T")[0];
});

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
                codigoLimpio(p.equipo?.equ_codigo).toLowerCase().includes(q) ||
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
        pco_justificacion_duplicidad: "",
        justificacion_duplicidad: "",
        beneficiarios: [],
    };
    radarData.value = null;
    errores.value = {};
    modal = modal || new Modal(document.getElementById("mProy"));
    modal.show();
};

const agregarFilaBeneficiario = () => {
    form.value.beneficiarios.push({ tbe_id: "", cantidad: 1 });
};

const eliminarFilaBeneficiario = (idx) => {
    form.value.beneficiarios.splice(idx, 1);
};

const errorBeneficiariosAlerta = ref("");

const bloquearExcesoBeneficiarios = (event, ben) => {
    // Teclas permitidas: Backspace, Delete, Tab, Escape, Enter, Flechas
    const permitidas = [
        "Backspace",
        "Delete",
        "Tab",
        "Escape",
        "Enter",
        "ArrowLeft",
        "ArrowRight",
        "ArrowUp",
        "ArrowDown",
    ];
    if (permitidas.includes(event.key) || event.ctrlKey || event.metaKey) {
        return;
    }
    // Si no es un dígito, bloquear
    if (!/^\d$/.test(event.key)) {
        event.preventDefault();
        return;
    }
    const valorFuturo = parseInt(`${ben.cantidad || ""}${event.key}`, 10);
    if (valorFuturo > 10000) {
        event.preventDefault();
        ben.cantidad = 10000;
        errorBeneficiariosAlerta.value =
            "Has alcanzado el tope máximo de 10.000 beneficiarios.";
        setTimeout(() => {
            errorBeneficiariosAlerta.value = "";
        }, 3500);
    }
};

const validarCantidadBeneficiario = (ben) => {
    if (ben.cantidad > 10000) {
        ben.cantidad = 10000;
        errorBeneficiariosAlerta.value =
            "No es posible ingresar más de 10.000 beneficiarios.";
        setTimeout(() => {
            errorBeneficiariosAlerta.value = "";
        }, 3500);
    } else if (
        ben.cantidad !== null &&
        ben.cantidad !== undefined &&
        ben.cantidad < 1
    ) {
        ben.cantidad = 1;
    }
};

// Observar profundamente el arreglo de beneficiarios
watch(
    () => form.value.beneficiarios,
    (nuevos) => {
        nuevos.forEach((b) => {
            if (b.cantidad > 10000) {
                b.cantidad = 10000;
            }
        });
    },
    { deep: true },
);

const guardar = () => {
    // Si el radar de duplicidad detectó similitud, exigir justificación mínima de 15 caracteres
    if (radarData.value?.alerta_similitud?.existe) {
        const just = (form.value.pco_justificacion_duplicidad || form.value.justificacion_duplicidad || "").trim();
        if (just.length < 15) {
            errores.value = {
                ...errores.value,
                justificacion_duplicidad: "Debe ingresar una justificación detallada (mínimo 15 caracteres) explicando por qué este proyecto no es un duplicado.",
            };
            return;
        }
        form.value.justificacion_duplicidad = just;
        form.value.pco_justificacion_duplicidad = just;
    }

    // Validar que ningún beneficiario supere 10000 antes del envío
    for (const ben of form.value.beneficiarios) {
        if (!ben.tbe_id) {
            alert("Por favor selecciona el tipo de beneficiario en cada fila.");
            return;
        }
        if (ben.cantidad > 10000) {
            alert(
                "La cantidad de beneficiarios no puede exceder los 10.000 por tipo.",
            );
            ben.cantidad = 10000;
            return;
        }
        if (!ben.cantidad || ben.cantidad < 1) {
            alert("La cantidad de beneficiarios debe ser al menos 1.");
            return;
        }
    }
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
    padding: 4px 10px;
    border-radius: 14px;
    font-size: 0.75rem;
    font-weight: 700;
    line-height: 1.25;
    text-align: center;
    display: inline-block;
    max-width: 150px;
    white-space: normal;
    word-break: normal;
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