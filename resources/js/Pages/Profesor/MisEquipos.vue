<template>
    <div
        :data-theme="tema"
        style="min-height: 100vh; background: var(--bg-body); color: var(--text-main);"
    >
        <SidebarProfesor
            :sidebar-open="sidebarOpen"
            @close="sidebarOpen = false"
        />

        <div class="main">
            <div class="topbar">
                <div class="d-flex align-items-center gap-2 gap-sm-3">
                    <button
                        class="btn btn-outline-danger btn-sm toggle-btn px-2"
                        @click="sidebarOpen = true"
                    >
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <Link
                        v-if="seccion"
                        href="/profesor/secciones"
                        class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                    >
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </Link>
                    <div class="tb-title">
                        <span v-if="seccion">
                            Equipos — Sección {{ seccion.sec_codigo }}
                        </span>
                        <span v-else>Mis Equipos</span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button
                        v-if="puedeCrear && seccion"
                        class="btn btn-danger btn-sm fw-bold rounded-pill px-3"
                        @click="abrirNuevoEquipo"
                    >
                        <i class="bi bi-plus-lg me-1"></i>Nuevo Equipo
                    </button>
                    <button class="tb-btn" @click="toggleTema" title="Cambiar tema">
                        <i class="bi" :class="tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'"></i>
                    </button>
                </div>
            </div>

            <div class="content">
                <!-- Flash Messages -->
                <div
                    v-if="$page.props.flash?.success"
                    class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3"
                >
                    <i class="bi bi-check-circle-fill me-2"></i>{{ $page.props.flash.success }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <div
                    v-if="Object.keys($page.props.errors || {}).length > 0"
                    class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-3"
                >
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span v-for="(err, k) in $page.props.errors" :key="k" class="d-block">
                        {{ err }}
                    </span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <!-- KPIs -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card-p kpi-card kpi-rojo h-100">
                            <div class="text-muted small fw-semibold">Equipos Activos</div>
                            <div class="fs-3 fw-bold mt-1">{{ equipos.length }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card-p kpi-card kpi-verde h-100">
                            <div class="text-muted small fw-semibold">Comunidades Aprobadas</div>
                            <div class="fs-3 fw-bold text-success mt-1">{{ totalAprobados }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card-p kpi-card kpi-amarillo h-100">
                            <div class="text-muted small fw-semibold">Pendientes de Aprobación</div>
                            <div class="fs-3 fw-bold text-warning mt-1">{{ totalPendientes }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card-p kpi-card kpi-gris h-100">
                            <div class="text-muted small fw-semibold">Sin Postulación (Etapa 2)</div>
                            <div class="fs-3 fw-bold text-secondary mt-1">{{ totalSinPostular }}</div>
                        </div>
                    </div>
                </div>

                <!-- Filtros -->
                <div class="card-p mb-4">
                    <div class="row g-2 align-items-end">
                        <div class="col-12 col-md-5">
                            <label class="form-label small fw-semibold mb-1">Buscar</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text"><i class="bi bi-search"></i></span>
                                <input
                                    v-model="busqueda"
                                    type="text"
                                    class="form-control"
                                    placeholder="Código, título, estudiante o comunidad..."
                                />
                            </div>
                        </div>
                        <div v-if="!seccion" class="col-6 col-md-3">
                            <label class="form-label small fw-semibold mb-1">Sección</label>
                            <SelectBuscable
                                v-model="filtroSeccion"
                                :options="opcionesSeccionesFiltro"
                                size="sm"
                                placeholder="Todas mis secciones"
                                search-placeholder="Buscar sección..."
                            />
                        </div>
                        <div class="col-6 col-md-4">
                            <label class="form-label small fw-semibold mb-1">Estado Comunitario</label>
                            <SelectBuscable
                                v-model="filtroComunidad"
                                :options="opcionesEstadoComunidad"
                                size="sm"
                                placeholder="Todos"
                            />
                        </div>
                    </div>
                </div>

                <!-- Lista de Equipos -->
                <div v-if="equiposFiltrados.length === 0" class="card-p text-center py-5 text-muted">
                    <i class="bi bi-people fs-1 d-block mb-2 opacity-50"></i>
                    No hay equipos que coincidan con los filtros actuales.
                </div>

                <div class="d-flex flex-column gap-3">
                    <div
                        v-for="eq in equiposFiltrados"
                        :key="eq.equ_id"
                        class="card-p"
                    >
                        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-3">
                            <div style="min-width: 0; flex: 1 1 260px;">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <span class="badge bg-dark font-monospace">{{ eq.equ_codigo }}</span>
                                    <span class="badge bg-secondary-subtle text-secondary border">
                                        Sección {{ eq.seccion?.sec_codigo }}
                                    </span>
                                    <span class="badge bg-info-subtle text-info-emphasis border">
                                        {{ eq.trayecto?.tra_nombre }} · Semestre {{ eq.equ_semestre || 1 }}
                                    </span>
                                    <span
                                        v-if="eq.equ_justificacion_cupo"
                                        class="badge bg-warning text-dark"
                                        :title="eq.equ_justificacion_cupo_motivo || 'Excepción docente de cupo'"
                                    >
                                        <i class="bi bi-shield-exclamation me-1"></i>Excepción de Cupo
                                    </span>
                                </div>
                                <h6 class="fw-bold mb-1">{{ eq.equ_titulo }}</h6>
                                <div class="small text-muted">
                                    <span v-if="eq.proyecto_comunidad?.pco_comunidad_aprobada" class="text-success fw-semibold">
                                        <i class="bi bi-building-check me-1"></i>
                                        Comunidad Aprobada: {{ eq.proyecto_comunidad?.comunidad?.com_nombre }}
                                        <span v-if="obtenerResponsable(eq)" class="text-muted fw-normal">
                                            · Resp.: {{ obtenerResponsable(eq).nombre }} ({{ obtenerResponsable(eq).telefono }})
                                        </span>
                                    </span>
                                    <span
                                        v-else-if="esPendienteAprobacion(eq)"
                                        class="text-warning-emphasis fw-semibold"
                                    >
                                        <i class="bi bi-hourglass-split me-1"></i>
                                        Postulación Pendiente de Aprobación: {{ eq.proyecto_comunidad?.comunidad?.com_nombre }}
                                        <span v-if="obtenerResponsable(eq)" class="text-muted fw-normal">
                                            · Resp.: {{ obtenerResponsable(eq).nombre }}
                                        </span>
                                    </span>
                                    <span
                                        v-else-if="eq.proyecto_comunidad?.pco_estado_postulacion === 'rechazada'"
                                        class="text-danger fw-semibold"
                                    >
                                        <i class="bi bi-arrow-return-left me-1"></i>
                                        Postulación devuelta al Líder para corrección
                                    </span>
                                    <span v-else class="text-secondary">
                                        <i class="bi bi-clock-history me-1"></i>
                                        Etapa 2 pendiente: el Líder aún no postula la comunidad
                                    </span>
                                </div>
                            </div>

                            <div class="d-flex flex-wrap align-items-center justify-content-md-end gap-2" style="flex: 0 1 auto;">
                                <SemaforoEquipo
                                    v-if="diagnosticosEquipos[eq.equ_id]"
                                    :diagnostico="diagnosticosEquipos[eq.equ_id]"
                                    compact
                                />
                                <button
                                    v-if="esPendienteAprobacion(eq)"
                                    class="btn btn-warning btn-sm fw-bold text-nowrap"
                                    @click="abrirRevisionComunidad(eq)"
                                >
                                    <i class="bi bi-clipboard2-check me-1"></i>Revisar Postulación
                                </button>
                                <Link
                                    :href="`/profesor/equipos/${eq.equ_id}/expediente`"
                                    class="btn btn-outline-primary btn-sm text-nowrap"
                                    title="Consultar expediente completo"
                                >
                                    <i class="bi bi-eye me-1"></i>Consultar
                                </Link>
                                <Link
                                    v-if="eq.proyecto_comunidad?.pco_comunidad_aprobada && eq.proyecto_comunidad?.pco_id"
                                    :href="`/evaluaciones/recorrido/${eq.proyecto_comunidad.pco_id}`"
                                    class="btn btn-outline-primary btn-sm"
                                    title="Ver recorrido evaluativo"
                                >
                                    <i class="bi bi-award me-1"></i>Evaluar
                                </Link>
                                <button
                                    class="btn btn-outline-warning btn-sm"
                                    title="Editar título del proyecto"
                                    @click="abrirEditarEquipo(eq)"
                                >
                                    <i class="bi bi-pencil me-1"></i>Editar
                                </button>
                                <button
                                    class="btn btn-outline-danger btn-sm"
                                    title="Disolver equipo"
                                    @click="abrirDisolver(eq)"
                                >
                                    <i class="bi bi-x-circle me-1"></i>Desactivar
                                </button>
                            </div>
                        </div>

                        <!-- Integrantes -->
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0" style="font-size: 0.85rem;">
                                <thead>
                                    <tr>
                                        <th>Integrante</th>
                                        <th>Cédula</th>
                                        <th>Rol</th>
                                        <th class="text-end">Movilidad y Gestión</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="integ in integrantesActivos(eq)" :key="integ.ein_id">
                                        <td class="fw-semibold">
                                            {{ integ.usuario?.usu_primer_nombre }}
                                            {{ integ.usuario?.usu_primer_apellido }}
                                        </td>
                                        <td class="font-monospace text-muted">
                                            {{ integ.usuario?.usu_cedula }}
                                        </td>
                                        <td>
                                            <span v-if="integ.ein_es_lider" class="badge bg-danger">
                                                <i class="bi bi-star-fill text-warning me-1"></i>Líder
                                            </span>
                                            <span v-else class="badge bg-secondary-subtle text-secondary">
                                                Integrante
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-1">
                                                <button
                                                    v-if="!integ.ein_es_lider"
                                                    class="btn btn-outline-warning btn-sm py-0 px-2"
                                                    title="Designar como Líder"
                                                    @click="cambiarLider(eq, integ)"
                                                >
                                                    <i class="bi bi-star"></i> Líder
                                                </button>
                                                <button
                                                    class="btn btn-outline-primary btn-sm py-0 px-2"
                                                    title="Trasladar a otro equipo de la misma sección"
                                                    @click="abrirTraslado(eq, integ)"
                                                >
                                                    <i class="bi bi-arrow-left-right"></i> Trasladar
                                                </button>
                                                <button
                                                    v-if="!integ.ein_es_lider"
                                                    class="btn btn-outline-danger btn-sm py-0 px-2"
                                                    title="Registrar baja justificada"
                                                    @click="abrirBaja(eq, integ)"
                                                >
                                                    <i class="bi bi-person-dash"></i> Baja
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Equipos Disueltos -->
                <div v-if="equiposDisueltos.length > 0" class="card-p mt-4">
                    <h6 class="fw-bold text-muted mb-3">
                        <i class="bi bi-archive me-2"></i>Equipos Desactivados ({{ equiposDisueltos.length }})
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Código</th>
                                    <th>Título</th>
                                    <th>Sección</th>
                                    <th class="text-end">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="dis in equiposDisueltos" :key="dis.equ_id">
                                    <td class="font-monospace small">{{ dis.equ_codigo }}</td>
                                    <td class="small">{{ dis.equ_titulo }}</td>
                                    <td class="small">{{ dis.seccion?.sec_codigo }}</td>
                                    <td class="text-end">
                                        <button
                                            class="btn btn-outline-primary btn-sm py-0 px-2"
                                            @click="reactivarEquipo(dis)"
                                        >
                                            <i class="bi bi-arrow-counterclockwise me-1"></i>Reactivar
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modales Reutilizables -->
        <NuevoEquipo
            ref="nuevoEquipoRef"
            :secciones="secciones"
            :equipos="equipos"
            :estudiantes="estudiantes"
            :sede="sede"
            :seccion-inicial="seccion?.sec_id || ''"
        />
        <ModalTrasladoEstudiante ref="modalTrasladoRef" :equipos="equipos" />

        <!-- Modal Revisar Postulación Comunitaria -->
        <div class="modal fade" id="mRevisionComunidad" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh" style="background: #1a1d23; color: #fff; border-bottom: 2px solid #ffc107; padding: 1rem 1.3rem; display: flex; justify-content: space-between; align-items: center;">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-clipboard2-check text-warning me-2"></i>
                            Revisión de Vinculación Comunitaria
                        </h5>
                        <button type="button" class="btn btn-sm text-white p-0 fs-5" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div v-if="equipoRevision" class="modal-body p-4">
                        <div v-if="Object.keys($page.props.errors || {}).length > 0" class="alert alert-danger py-2 px-3 small mb-3">
                            <ul class="mb-0 ps-3"><li v-for="(err, k) in $page.props.errors" :key="k">{{ err }}</li></ul>
                        </div>
                        <div class="mb-3">
                            <span class="badge bg-dark me-2">{{ equipoRevision.equ_codigo }}</span>
                            <strong>{{ equipoRevision.equ_titulo }}</strong>
                        </div>
                        <div class="row g-3 small mb-3">
                            <div class="col-md-6">
                                <div class="p-2 rounded border">
                                    <div class="text-muted">Comunidad Beneficiada</div>
                                    <div class="fw-bold">{{ equipoRevision.proyecto_comunidad?.comunidad?.com_nombre || '—' }}</div>
                                    <div>{{ equipoRevision.proyecto_comunidad?.comunidad?.com_ubicacion || '' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-2 rounded border">
                                    <div class="text-muted">Responsable de la Comunidad</div>
                                    <div class="fw-bold">{{ obtenerResponsable(equipoRevision)?.nombre || '—' }}</div>
                                    <div>
                                        CI: {{ obtenerResponsable(equipoRevision)?.cedula || '—' }} ·
                                        Tel: {{ obtenerResponsable(equipoRevision)?.telefono || '—' }}
                                        <span v-if="obtenerResponsable(equipoRevision)?.cargo"> · {{ obtenerResponsable(equipoRevision)?.cargo }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-2 rounded border">
                                    <div class="text-muted">Modalidad y Línea de Investigación</div>
                                    <div class="fw-bold">Modalidad: {{ equipoRevision.proyecto_comunidad?.modalidad?.mpr_nombre || '—' }}</div>
                                    <div>Línea: {{ equipoRevision.proyecto_comunidad?.tipo_proyecto?.tpr_nombre || '—' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-2 rounded border">
                                    <div class="text-muted">Resumen del Proyecto</div>
                                    <div>{{ equipoRevision.proyecto_comunidad?.pco_resumen || '—' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label small fw-semibold">
                                Justificación de No Duplicidad (si aplica misma comunidad/temática)
                            </label>
                            <input
                                v-model="formRevision.justificacion_duplicidad"
                                type="text"
                                class="form-control form-control-sm"
                                placeholder="Opcional si no hay coincidencia previa..."
                            />
                        </div>

                        <div v-if="mostrarRechazo" class="mb-2">
                            <label class="form-label small fw-semibold text-danger">
                                Motivo / Correcciones requeridas para devolver al Líder <span class="text-danger">*</span>
                            </label>
                            <textarea
                                v-model="formRevision.motivo"
                                class="form-control form-control-sm"
                                rows="2"
                                placeholder="Indica al líder qué debe corregir en su postulación..."
                            ></textarea>
                        </div>
                    </div>
                    <div class="modal-footer border-0 justify-content-between">
                        <div>
                            <button
                                v-if="!mostrarRechazo"
                                type="button"
                                class="btn btn-outline-danger btn-sm"
                                @click="mostrarRechazo = true"
                            >
                                <i class="bi bi-arrow-return-left me-1"></i>Devolver con Observaciones
                            </button>
                            <button
                                v-else
                                type="button"
                                class="btn btn-danger btn-sm"
                                :disabled="cargandoAccion || (formRevision.motivo || '').trim().length < 5"
                                @click="confirmarRechazoComunidad"
                            >
                                Confirmar Devolución al Líder
                            </button>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">
                                Cerrar
                            </button>
                            <button
                                type="button"
                                class="btn btn-success btn-sm fw-bold px-3"
                                :disabled="cargandoAccion"
                                @click="aprobarComunidad"
                            >
                                <i class="bi bi-check2-circle me-1"></i>Aprobar y Desbloquear Carta/Entregables
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Editar Equipo -->
        <div class="modal fade" id="mEditarEquipoProf" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh" style="background: #1a1d23; color: #fff; border-bottom: 2px solid #ffc107; padding: 1rem 1.3rem; display: flex; justify-content: space-between; align-items: center;">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-pencil-square text-warning me-2"></i>Editar Proyecto del Equipo
                        </h5>
                        <button type="button" class="btn btn-sm text-white p-0 fs-5" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form @submit.prevent="guardarEdicion">
                        <div class="modal-body p-4">
                            <div v-if="Object.keys($page.props.errors || {}).length > 0" class="alert alert-danger py-2 px-3 small mb-3">
                                <ul class="mb-0 ps-3"><li v-for="(err, k) in $page.props.errors" :key="k">{{ err }}</li></ul>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Título del Proyecto <span class="text-danger">*</span></label>
                                <input v-model="formEdit.equ_titulo" type="text" class="form-control" maxlength="150" required />
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning btn-sm fw-bold" :disabled="cargandoAccion">
                                Guardar Ajustes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Baja Justificada -->
        <div class="modal fade" id="mBajaIntegrante" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh" style="background: #1a1d23; color: #fff; border-bottom: 2px solid #dc3545; padding: 1rem 1.3rem; display: flex; justify-content: space-between; align-items: center;">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-person-dash text-danger me-2"></i>Desvinculación / Baja Justificada
                        </h5>
                        <button type="button" class="btn btn-sm text-white p-0 fs-5" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form @submit.prevent="confirmarBaja">
                        <div class="modal-body p-4">
                            <div v-if="Object.keys($page.props.errors || {}).length > 0" class="alert alert-danger py-2 px-3 small mb-3">
                                <ul class="mb-0 ps-3"><li v-for="(err, k) in $page.props.errors" :key="k">{{ err }}</li></ul>
                            </div>
                            <p class="small mb-2">
                                Estás registrando la baja de
                                <strong>{{ bajaTarget.integ?.usuario?.usu_primer_nombre }} {{ bajaTarget.integ?.usuario?.usu_primer_apellido }}</strong>
                                del equipo <strong>{{ bajaTarget.equipo?.equ_codigo }}</strong>.
                            </p>
                            <div
                                v-if="bajaTarget.equipo && integrantesActivos(bajaTarget.equipo).length <= 3"
                                class="alert alert-warning py-2 small"
                            >
                                <i class="bi bi-shield-exclamation me-1"></i>
                                Al registrar esta baja el equipo quedará con menos de 3 integrantes. El sistema activará automáticamente la bandera de excepción docente (<code>equ_justificacion_cupo</code>) sin bloquear la continuidad.
                            </div>
                            <label class="form-label small fw-semibold">
                                Motivo del retiro / abandono <span class="text-danger">*</span>
                            </label>
                            <textarea
                                v-model="bajaTarget.motivo_baja"
                                class="form-control form-control-sm"
                                rows="3"
                                required
                                minlength="5"
                                placeholder="Ej: Retiro voluntario formalizado ante control de estudios..."
                            ></textarea>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger btn-sm fw-bold" :disabled="cargandoAccion">
                                Confirmar Baja Justificada
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Confirmar Disolución -->
        <div class="modal fade" id="mDisolverProf" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh" style="background: #1a1d23; color: #fff; border-bottom: 2px solid #dc3545; padding: 1rem 1.3rem; display: flex; justify-content: space-between; align-items: center;">
                        <h5 class="mb-0 fw-bold">
                            <i class="bi bi-exclamation-octagon text-danger me-2"></i>Desactivar / Disolver Equipo
                        </h5>
                        <button type="button" class="btn btn-sm text-white p-0 fs-5" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form @submit.prevent="confirmarDisolver">
                        <div class="modal-body p-4">
                            <div v-if="Object.keys($page.props.errors || {}).length > 0" class="alert alert-danger py-2 px-3 small mb-3">
                                <ul class="mb-0 ps-3"><li v-for="(err, k) in $page.props.errors" :key="k">{{ err }}</li></ul>
                            </div>
                            <p class="small">
                                ¿Confirmas desactivar el equipo <strong>{{ disolverTarget.equipo?.equ_codigo }}</strong>? Se cerrará la pertenencia activa de sus integrantes y podrá reactivarse luego si los cupos están libres.
                            </p>
                            <label class="form-label small fw-semibold">Motivo <span class="text-danger">*</span></label>
                            <input
                                v-model="disolverTarget.motivo"
                                type="text"
                                class="form-control form-control-sm"
                                required
                                placeholder="Motivo de disolución..."
                            />
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger btn-sm fw-bold" :disabled="cargandoAccion">
                                Desactivar Equipo
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
import { Link, router } from "@inertiajs/vue3";
import { Modal } from "bootstrap";
import SidebarProfesor from "@/Components/SidebarProfesor.vue";
import SemaforoEquipo from "@/Components/SemaforoEquipo.vue";
import NuevoEquipo from "@/Components/NuevoEquipo.vue";
import ModalTrasladoEstudiante from "@/Components/ModalTrasladoEstudiante.vue";
import SelectBuscable from "@/Components/SelectBuscable.vue";

const props = defineProps({
    seccion: { type: Object, default: null },
    equipos: { type: Array, default: () => [] },
    diagnosticosEquipos: { type: Object, default: () => ({}) },
    equiposDisueltos: { type: Array, default: () => [] },
    secciones: { type: Array, default: () => [] },
    estudiantes: { type: Array, default: () => [] },
    puedeCrear: { type: Boolean, default: true },
    sede: { type: String, default: "ACA" },
});

const opcionesSeccionesFiltro = computed(() => [
    { value: "", label: "Todas mis secciones" },
    ...(props.secciones || []).map((s) => ({
        value: s.sec_id,
        label: `${s.sec_codigo} — ${s.trayecto?.tra_nombre || ""}`,
    })),
]);

const opcionesEstadoComunidad = [
    { value: "", label: "Todos" },
    { value: "pendiente", label: "Pendiente de Aprobación" },
    { value: "sin_postular", label: "Sin Postular" },
    { value: "aprobada", label: "Comunidad Aprobada" },
    { value: "rechazada", label: "Devuelta al Líder" },
];

const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("prof-theme") || "light");
const toggleTema = () => {
    tema.value = tema.value === "light" ? "dark" : "light";
    localStorage.setItem("prof-theme", tema.value);
};

const busqueda = ref("");
const filtroSeccion = ref("");
const filtroComunidad = ref("");
const cargandoAccion = ref(false);

const nuevoEquipoRef = ref(null);
const modalTrasladoRef = ref(null);

const integrantesActivos = (eq) =>
    (eq.integrantes || []).filter((i) => i.ein_status !== false);

const esPendienteAprobacion = (eq) =>
    ["pendiente", "pendiente_aprobacion"].includes(
        eq.proyecto_comunidad?.pco_estado_postulacion,
    );

const obtenerResponsable = (eq) => {
    const lista = eq?.proyecto_comunidad?.comunidad?.responsables || [];
    const activo = lista.find((r) => r.rco_status !== false) || lista[0];
    if (!activo) return null;
    const u = activo.usuario;
    if (!u) return null;
    const cargoActivo = (u.cargos || []).find((c) => c.pca_status !== false) || u.cargos?.[0];
    return {
        nombre: `${u.usu_primer_nombre || ""} ${u.usu_primer_apellido || ""}`.trim(),
        cedula: u.usu_cedula || "—",
        telefono: u.usu_telefono || "—",
        cargo: cargoActivo?.pca_cargo || null,
    };
};

const totalAprobados = computed(
    () => props.equipos.filter((e) => e.proyecto_comunidad?.pco_comunidad_aprobada).length,
);
const totalPendientes = computed(
    () => props.equipos.filter((e) => !e.proyecto_comunidad?.pco_comunidad_aprobada && esPendienteAprobacion(e)).length,
);
const totalSinPostular = computed(
    () => props.equipos.filter((e) => !e.proyecto_comunidad?.pco_comunidad_aprobada && !esPendienteAprobacion(e)).length,
);

const equiposFiltrados = computed(() => {
    const q = busqueda.value.trim().toLowerCase();
    return props.equipos.filter((eq) => {
        if (filtroSeccion.value && String(eq.equ_id_sec) !== String(filtroSeccion.value)) {
            return false;
        }
        if (filtroComunidad.value) {
            const rawEst = eq.proyecto_comunidad?.pco_comunidad_aprobada
                ? "aprobada"
                : esPendienteAprobacion(eq)
                  ? "pendiente"
                  : eq.proyecto_comunidad?.pco_estado_postulacion || "sin_postular";
            if (rawEst !== filtroComunidad.value) return false;
        }
        if (!q) return true;
        const com = eq.proyecto_comunidad?.comunidad?.com_nombre?.toLowerCase() || "";
        const ints = (eq.integrantes || [])
            .map((i) => `${i.usuario?.usu_primer_nombre || ""} ${i.usuario?.usu_primer_apellido || ""} ${i.usuario?.usu_cedula || ""}`.toLowerCase())
            .join(" ");
        return (
            (eq.equ_codigo || "").toLowerCase().includes(q) ||
            (eq.equ_titulo || "").toLowerCase().includes(q) ||
            com.includes(q) ||
            ints.includes(q)
        );
    });
});

const abrirNuevoEquipo = () => {
    nuevoEquipoRef.value?.abrir(props.seccion?.sec_id || "");
};

const abrirModal = (id) => {
    const el = document.getElementById(id);
    if (el) (Modal.getInstance(el) || new Modal(el)).show();
};
const cerrarModal = (id) => {
    const el = document.getElementById(id);
    if (el) Modal.getInstance(el)?.hide();
};

const equipoRevision = ref(null);
const mostrarRechazo = ref(false);
const formRevision = ref({ justificacion_duplicidad: "", motivo: "" });

const abrirRevisionComunidad = (eq) => {
    equipoRevision.value = eq;
    mostrarRechazo.value = false;
    formRevision.value = {
        justificacion_duplicidad: eq.proyecto_comunidad?.pco_justificacion_duplicidad || "",
        motivo: "",
    };
    abrirModal("mRevisionComunidad");
};

const aprobarComunidad = () => {
    if (!equipoRevision.value) return;
    cargandoAccion.value = true;
    router.post(
        `/profesor/equipos/${equipoRevision.value.equ_id}/aprobar-comunidad`,
        { justificacion_duplicidad: formRevision.value.justificacion_duplicidad },
        {
            preserveScroll: true,
            onSuccess: () => {
                cargandoAccion.value = false;
                cerrarModal("mRevisionComunidad");
            },
            onError: () => {
                cargandoAccion.value = false;
            },
        },
    );
};

const confirmarRechazoComunidad = () => {
    if (!equipoRevision.value) return;
    cargandoAccion.value = true;
    router.post(
        `/profesor/equipos/${equipoRevision.value.equ_id}/rechazar-comunidad`,
        { motivo: formRevision.value.motivo },
        {
            preserveScroll: true,
            onSuccess: () => {
                cargandoAccion.value = false;
                cerrarModal("mRevisionComunidad");
            },
            onError: () => {
                cargandoAccion.value = false;
            },
        },
    );
};

const formEdit = ref({ equ_id: null, equ_titulo: "" });
const abrirEditarEquipo = (eq) => {
    formEdit.value = { equ_id: eq.equ_id, equ_titulo: eq.equ_titulo };
    abrirModal("mEditarEquipoProf");
};
const guardarEdicion = () => {
    cargandoAccion.value = true;
    router.put(
        `/profesor/equipos/${formEdit.value.equ_id}`,
        { equ_titulo: formEdit.value.equ_titulo },
        {
            preserveScroll: true,
            onSuccess: () => {
                cargandoAccion.value = false;
                cerrarModal("mEditarEquipoProf");
            },
            onError: () => {
                cargandoAccion.value = false;
            },
        },
    );
};

const cambiarLider = (eq, integ) => {
    router.post(
        `/profesor/equipos/${eq.equ_id}/cambiar-lider`,
        { nuevo_lider_id: integ.ein_id },
        { preserveScroll: true },
    );
};

const abrirTraslado = (eq, integ) => {
    modalTrasladoRef.value?.abrir(eq, integ);
};

const bajaTarget = ref({ equipo: null, integ: null, motivo_baja: "" });
const abrirBaja = (eq, integ) => {
    bajaTarget.value = { equipo: eq, integ, motivo_baja: "" };
    abrirModal("mBajaIntegrante");
};
const confirmarBaja = () => {
    cargandoAccion.value = true;
    router.delete(
        `/profesor/equipos/${bajaTarget.value.equipo.equ_id}/integrantes/${bajaTarget.value.integ.ein_id}`,
        {
            data: { motivo_baja: bajaTarget.value.motivo_baja },
            preserveScroll: true,
            onSuccess: () => {
                cargandoAccion.value = false;
                cerrarModal("mBajaIntegrante");
            },
            onError: () => {
                cargandoAccion.value = false;
            },
        },
    );
};

const disolverTarget = ref({ equipo: null, motivo: "" });
const abrirDisolver = (eq) => {
    disolverTarget.value = { equipo: eq, motivo: "" };
    abrirModal("mDisolverProf");
};
const confirmarDisolver = () => {
    cargandoAccion.value = true;
    router.post(
        `/profesor/equipos/${disolverTarget.value.equipo.equ_id}/disolver`,
        { motivo: disolverTarget.value.motivo },
        {
            preserveScroll: true,
            onSuccess: () => {
                cargandoAccion.value = false;
                cerrarModal("mDisolverProf");
            },
            onError: () => {
                cargandoAccion.value = false;
            },
        },
    );
};

const reactivarEquipo = (eq) => {
    router.post(`/profesor/equipos/${eq.equ_id}/reactivar`, {}, { preserveScroll: true });
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
    border-bottom: 2px solid rgba(220, 53, 69, 0.12);
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
.card-p {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: 13px;
    padding: 1.15rem 1.25rem;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
    transition: background 0.3s, border-color 0.3s;
}
.kpi-card {
    border-left-width: 4px;
}
.kpi-rojo {
    border-left-color: #dc3545;
}
.kpi-verde {
    border-left-color: #198754;
}
.kpi-amarillo {
    border-left-color: #ffc107;
}
.kpi-gris {
    border-left-color: #6c757d;
}
.table {
    --bs-table-bg: transparent;
    --bs-table-color: var(--text-main);
    border-color: var(--border);
}
.table thead th {
    background: var(--bg-th);
    color: var(--text-muted);
    font-size: 0.74rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    border-bottom: 1px solid var(--border);
}
.table tbody tr:hover td {
    background: var(--bg-hover);
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
    background: var(--bg-card);
    color: var(--text-main);
    border-color: var(--border);
}
.modal-content,
.modal-body,
.modal-footer {
    background: var(--bg-card);
    color: var(--text-main);
}
@media (min-width: 992px) {
    .main {
        margin-left: 250px;
    }
    .toggle-btn {
        display: none !important;
    }
}
@media (max-width: 991.98px) {
    .main {
        margin-left: 0;
    }
    .content {
        padding: 1rem;
    }
}
</style>
