<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
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
                <Link href="/coordinador" class="sb-lnk">
                    <i class="bi bi-speedometer2"></i> Dashboard
                </Link>
                <Link href="/coordinador/secciones" class="sb-lnk">
                    <i class="bi bi-collection"></i> Secciones
                </Link>
                <Link href="/coordinador/comunidades" class="sb-lnk">
                    <i class="bi bi-building"></i> Comunidades
                </Link>
                <Link href="/coordinador/proyectos" class="sb-lnk">
                    <i class="bi bi-folder"></i> Proyectos
                </Link>
                <Link href="/coordinador/socializaciones" class="sb-lnk">
                    <i class="bi bi-mic"></i> Socializaciones
                </Link>
                <Link href="/coordinador/resultados" class="sb-lnk">
                    <i class="bi bi-trophy"></i> Resultados
                </Link>

                <!-- Tablas Maestras — desplegable -->
                <div
                    class="sb-sec sb-toggle"
                    @click="menuMaestras = !menuMaestras"
                    style="
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding-right: 1.2rem;
                    "
                >
                    <span>Tablas Maestras</span>
                    <i
                        class="bi"
                        :class="
                            menuMaestras ? 'bi-chevron-up' : 'bi-chevron-down'
                        "
                        style="font-size: 0.75rem"
                    ></i>
                </div>
                <div v-show="menuMaestras">
                    <Link href="/coordinador/tipos-proyecto" class="sb-lnk">
                        <i class="bi bi-tag"></i> Tipos de Proyecto
                    </Link>
                    <Link
                        href="/coordinador/maestras/modalidades"
                        class="sb-lnk"
                    >
                        <i class="bi bi-layers"></i> Modalidades
                    </Link>
                    <Link
                        href="/coordinador/maestras/tipos-jurado"
                        class="sb-lnk"
                    >
                        <i class="bi bi-person-badge"></i> Tipos de Jurado
                    </Link>
                    <Link
                        href="/coordinador/maestras/tipos-beneficiario"
                        class="sb-lnk"
                    >
                        <i class="bi bi-people"></i> Tipos Beneficiario
                    </Link>
                    <Link
                        href="/coordinador/maestras/tipos-entregable"
                        class="sb-lnk"
                    >
                        <i class="bi bi-file-earmark"></i> Tipos Entregable
                    </Link>
                    <Link
                        href="/coordinador/maestras/tipos-evento-equipo"
                        class="sb-lnk"
                    >
                        <i class="bi bi-calendar-event"></i> Eventos Equipo
                    </Link>
                    <Link href="/coordinador/maestras/turnos" class="sb-lnk">
                        <i class="bi bi-clock"></i> Turnos
                    </Link>
                    <Link
                        href="/coordinador/maestras/estados-proyecto"
                        class="sb-lnk"
                    >
                        <i class="bi bi-award"></i> Estados Proyecto
                    </Link>
                </div>

                <!-- Sistema — desplegable -->
                <div
                    class="sb-sec sb-toggle"
                    @click="menuSistema = !menuSistema"
                    style="
                        cursor: pointer;
                        display: flex;
                        align-items: center;
                        justify-content: space-between;
                        padding-right: 1.2rem;
                    "
                >
                    <span>Sistema</span>
                    <i
                        class="bi"
                        :class="
                            menuSistema ? 'bi-chevron-up' : 'bi-chevron-down'
                        "
                        style="font-size: 0.75rem"
                    ></i>
                </div>
                <div v-show="menuSistema">
                    <Link href="/coordinador/reportes" class="sb-lnk">
                        <i class="bi bi-bar-chart"></i> Reportes
                    </Link>
                    <Link href="/coordinador/cartas" class="sb-lnk">
                        <i class="bi bi-file-earmark-text"></i> Cartas
                    </Link>
                    <Link href="/coordinador/bitacora" class="sb-lnk">
                        <i class="bi bi-journal-text"></i> Bitácora
                    </Link>
                    <Link href="/coordinador/configuracion" class="sb-lnk">
                        <i class="bi bi-gear"></i> Configuración
                    </Link>
                </div>
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
                            class="bi bi-trophy me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Resultados
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button
                        class="btn btn-danger btn-sm fw-bold rounded-pill px-3"
                        @click="abrirModal()"
                    >
                        <i class="bi bi-plus-lg me-1"></i>Registrar
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
                        gap: 12px;
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
                            Resultados de Proyectos
                        </h4>
                        <p
                            style="
                                color: var(--text-muted);
                                font-size: 0.85rem;
                                margin-top: 2px;
                            "
                        >
                            Registro del estado final de cada proyecto
                        </p>
                    </div>

                    <!-- Filtros: Sección y Búsqueda -->
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <select
                            v-model="filtroSeccion"
                            class="form-select form-select-sm"
                            style="min-width: 200px; max-width: 260px;"
                        >
                            <option value="">Todas las secciones</option>
                            <option
                                v-for="sec in secciones"
                                :key="sec.sec_id"
                                :value="sec.sec_id"
                            >
                                Sección {{ sec.sec_codigo }} — {{ sec.trayecto?.tra_nombre }} ({{ sec.turno?.tur_nombre }})
                            </option>
                        </select>

                        <div class="srch">
                            <i class="bi bi-search"></i>
                            <input
                                v-model="busqueda"
                                type="text"
                                placeholder="Buscar por equipo, comunidad..."
                            />
                        </div>
                    </div>
                </div>
                <div class="sc">
                    <div class="table-responsive">
                        <table class="ct">
                            <thead>
                                <tr>
                                    <th class="ps-4">Equipo</th>
                                    <th>Sección</th>
                                    <th>Comunidad</th>
                                    <th>Trayecto</th>
                                    <th>Estado</th>
                                    <th>Fecha</th>
                                    <th>Tardío</th>
                                    <th>Observaciones</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="resultadosFiltrados.length === 0">
                                    <td
                                        colspan="9"
                                        class="text-center py-5"
                                        style="color: var(--text-muted)"
                                    >
                                        No hay resultados que coincidan con los filtros
                                    </td>
                                </tr>
                                <tr v-for="rpr in resultadosFiltrados" :key="rpr.rpr_id">
                                    <td
                                        class="ps-4 fw-bold"
                                        style="color: var(--rojo)"
                                    >
                                        <div>{{ cleanCodigo(obtenerEquipo(rpr)?.equ_codigo) }}</div>
                                        <div v-if="obtenerEquipo(rpr)?.equ_titulo" class="small text-muted fw-normal text-truncate" style="max-width: 200px;">
                                            {{ obtenerEquipo(rpr)?.equ_titulo }}
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            Sec. {{ obtenerEquipo(rpr)?.seccion?.sec_codigo || '—' }}
                                        </span>
                                    </td>
                                    <td style="font-size: 0.88rem">
                                        {{
                                            rpr.proyecto_comunidad?.comunidad
                                                ?.com_nombre || '—'
                                        }}
                                    </td>
                                    <td style="font-size: 0.85rem">
                                        {{ rpr.trayecto?.tra_nombre }}
                                    </td>
                                    <td>
                                        <span
                                            class="bs"
                                            :class="
                                                badgeRes(
                                                    rpr.estado_proyecto
                                                        ?.epr_nombre,
                                                )
                                            "
                                            >{{
                                                rpr.estado_proyecto?.epr_nombre
                                            }}</span
                                        >
                                    </td>
                                    <td style="font-size: 0.85rem">
                                        {{ rpr.rpr_fecha_registro }}
                                    </td>
                                    <td>
                                        <span
                                            v-if="rpr.rpr_habilitado_tardio"
                                            class="bs bw"
                                            >Habilitado</span
                                        >
                                        <button
                                            v-else
                                            class="btn btn-sm btn-outline-warning fw-semibold"
                                            style="
                                                font-size: 0.75rem;
                                                padding: 3px 8px;
                                            "
                                            @click="habilitarTardio(rpr)"
                                        >
                                            Habilitar tardío
                                        </button>
                                    </td>
                                    <td
                                        style="
                                            font-size: 0.85rem;
                                            color: var(--text-muted);
                                            max-width: 140px;
                                        "
                                        class="text-truncate"
                                    >
                                        {{ rpr.rpr_observaciones || "—" }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <button
                                            class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1 rounded-pill px-2.5 py-1"
                                            @click="verHistorialEquipo(rpr)"
                                            title="Ver historial académico e información del equipo"
                                        >
                                            <i class="bi bi-eye-fill"></i>
                                            <span style="font-size: 0.75rem; font-weight: 600;">Ver</span>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="mRes" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>Registrar Resultado</h5>
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
                                    >Proyecto
                                    <span class="text-danger">*</span></label
                                >
                                <select
                                    v-model="form.rpr_id_pco"
                                    class="form-select"
                                    :class="{
                                        'is-invalid': errores.rpr_id_pco,
                                    }"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option
                                        v-for="pco in proyectos"
                                        :key="pco.pco_id"
                                        :value="pco.pco_id"
                                    >
                                        {{ pco.equipo?.equ_codigo }} —
                                        {{ pco.comunidad?.com_nombre }}
                                    </option>
                                </select>
                                <div
                                    v-if="errores.rpr_id_pco"
                                    class="invalid-feedback"
                                >
                                    {{ errores.rpr_id_pco }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Estado Final
                                    <span class="text-danger">*</span></label
                                >
                                <select
                                    v-model="form.rpr_id_epr"
                                    class="form-select"
                                    :class="{
                                        'is-invalid': errores.rpr_id_epr,
                                    }"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option
                                        v-for="est in estados"
                                        :key="est.epr_id"
                                        :value="est.epr_id"
                                    >
                                        {{ est.epr_nombre }}
                                    </option>
                                </select>
                                <div
                                    v-if="errores.rpr_id_epr"
                                    class="invalid-feedback"
                                >
                                    {{ errores.rpr_id_epr }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Trayecto
                                    <span class="text-danger">*</span></label
                                >
                                <select
                                    v-model="form.rpr_id_tra"
                                    class="form-select"
                                    :class="{
                                        'is-invalid': errores.rpr_id_tra,
                                    }"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option
                                        v-for="tra in trayectos"
                                        :key="tra.tra_id"
                                        :value="tra.tra_id"
                                    >
                                        {{ tra.tra_nombre }}
                                    </option>
                                </select>
                                <div
                                    v-if="errores.rpr_id_tra"
                                    class="invalid-feedback"
                                >
                                    {{ errores.rpr_id_tra }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Fecha de Registro
                                    <span class="text-danger">*</span></label
                                >
                                <input
                                    v-model="form.rpr_fecha_registro"
                                    type="date"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            errores.rpr_fecha_registro,
                                    }"
                                />
                                <div
                                    v-if="errores.rpr_fecha_registro"
                                    class="invalid-feedback"
                                >
                                    {{ errores.rpr_fecha_registro }}
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Observaciones</label
                                >
                                <textarea
                                    v-model="form.rpr_observaciones"
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

        <div class="modal fade" id="mTardio" tabindex="-1">
            <div class="modal-dialog modal-sm modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body text-center p-4">
                        <div style="font-size: 2.5rem; margin-bottom: 0.75rem">
                            ⏰
                        </div>
                        <h6 class="fw-bold" style="color: var(--text-main)">
                            ¿Habilitar Entrega Tardía?
                        </h6>
                        <p
                            style="color: var(--text-muted); font-size: 0.9rem"
                            class="mb-0"
                        >
                            Esta acción permitirá al equipo hacer una entrega
                            fuera del plazo establecido.
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
                            class="btn btn-warning fw-bold px-4 text-dark"
                            @click="confirmarTardio"
                        >
                            Sí, habilitar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Historial Académico e Información del Equipo -->
        <div class="modal fade" id="mHistorialAcademico" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg overflow-hidden" style="background: var(--bg-card); border-radius: 12px;">
                    <div class="mh" style="background: #1a1d23; border-bottom: 2px solid #dc3545; padding: 1.1rem 1.4rem; display: flex; align-items: center; justify-content: space-between;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 36px; height: 36px; background: #dc3545; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800;">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-white fw-bold fs-6">Historial Académico del Equipo</h5>
                                <small class="text-white-50" v-if="equipoHistorial">
                                    <span class="badge bg-danger me-1">{{ cleanCodigo(equipoHistorial.equ_codigo) }}</span>
                                    <span>{{ equipoHistorial.equ_titulo }}</span>
                                </small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button
                                v-if="equipoHistorial?.equ_id"
                                type="button"
                                @click="irAExpediente(equipoHistorial.equ_id)"
                                class="btn btn-sm btn-outline-light rounded-pill px-3 fw-semibold"
                                title="Ver expediente completo del equipo"
                            >
                                <i class="bi bi-folder-fill text-warning me-1"></i> Expediente Completo
                            </button>
                            <button type="button" class="btn btn-sm text-white p-0 fs-5" data-bs-dismiss="modal">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                    <div class="modal-body p-4">
                        <div v-if="cargandoHistorialAcademico" class="text-center py-5">
                            <div class="spinner-border text-danger" role="status"></div>
                            <p class="text-muted mt-2">Cargando información del equipo e historial...</p>
                        </div>
                        <template v-else-if="equipoHistorial">
                            <!-- Resumen del Equipo -->
                            <div class="sc mb-3 p-3" style="border-left: 4px solid #dc3545 !important;">
                                <div class="row g-3 text-center text-md-start">
                                    <div class="col-md-3">
                                        <span class="small fw-bold text-uppercase d-block" style="color: var(--text-muted); font-size: 0.72rem;">Sección & Trayecto</span>
                                        <span class="fw-bold" style="color: var(--text-main);">Sec. {{ equipoHistorial.seccion?.sec_codigo }} — {{ equipoHistorial.trayecto?.tra_nombre }} ({{ equipoHistorial.seccion?.turno?.tur_nombre || 'N/A' }})</span>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="small fw-bold text-uppercase d-block" style="color: var(--text-muted); font-size: 0.72rem;">Comunidad</span>
                                        <span class="fw-bold" style="color: var(--text-main);">{{ equipoHistorial.proyecto_comunidad?.comunidad?.com_nombre || 'Sin asignar' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="small fw-bold text-uppercase d-block" style="color: var(--text-muted); font-size: 0.72rem;">Tipo / Modalidad</span>
                                        <span class="fw-bold" style="color: var(--text-main);">{{ equipoHistorial.proyecto_comunidad?.tipo_proyecto?.tpr_nombre || 'Sin definir' }} / {{ equipoHistorial.proyecto_comunidad?.modalidad?.mpr_nombre || 'General' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="small fw-bold text-uppercase d-block" style="color: var(--text-muted); font-size: 0.72rem;">Estado del Proyecto</span>
                                        <span class="bs" :class="equipoHistorial.resultado_proyecto?.estado_proyecto?.epr_nombre === 'Aprobado' ? 'ba' : 'bd'">
                                            {{ equipoHistorial.resultado_proyecto?.estado_proyecto?.epr_nombre || 'En Proceso' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Pestañas de Historial -->
                            <div class="d-flex gap-2 mb-3 pb-2 overflow-x-auto" style="border-bottom: 1px solid var(--border);">
                                <button
                                    type="button"
                                    class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                                    :style="tabHistorial === 'puntos' ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;' : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'"
                                    @click="tabHistorial = 'puntos'"
                                >
                                    <i class="bi bi-flag me-1"></i> Puntos de Control ({{ equipoHistorial.puntos_control?.length || 0 }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                                    :style="tabHistorial === 'entregables' ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;' : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'"
                                    @click="tabHistorial = 'entregables'"
                                >
                                    <i class="bi bi-file-earmark-check me-1"></i> Entregables ({{ equipoHistorial.entregables?.length || 0 }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                                    :style="tabHistorial === 'socializaciones' ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;' : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'"
                                    @click="tabHistorial = 'socializaciones'"
                                >
                                    <i class="bi bi-people me-1"></i> Socializaciones ({{ equipoHistorial.socializaciones?.length || 0 }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                                    :style="tabHistorial === 'integrantes' ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;' : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'"
                                    @click="tabHistorial = 'integrantes'"
                                >
                                    <i class="bi bi-person-lines-fill me-1"></i> Integrantes ({{ equipoHistorial.integrantes?.length || 0 }})
                                </button>
                            </div>

                            <!-- Pestaña: Puntos de Control -->
                            <div v-if="tabHistorial === 'puntos'">
                                <div v-if="!equipoHistorial.puntos_control || equipoHistorial.puntos_control.length === 0" class="text-center py-4 text-muted">
                                    <i class="bi bi-info-circle fs-3 d-block mb-2 text-danger"></i>
                                    No hay puntos de control registrados para este equipo.
                                </div>
                                <div v-else class="sc">
                                    <div class="table-responsive">
                                        <table class="ct">
                                            <thead>
                                                <tr>
                                                    <th>N°</th>
                                                    <th>Punto de Control</th>
                                                    <th>Fecha Límite</th>
                                                    <th>Estado</th>
                                                    <th>Observaciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(puc, idx) in equipoHistorial.puntos_control" :key="puc.puc_id">
                                                    <td><span class="bs bsec">{{ puc.puc_orden || idx + 1 }}</span></td>
                                                    <td>
                                                        <div class="fw-bold">{{ puc.puc_nombre }}</div>
                                                        <small class="text-muted" v-if="puc.puc_descripcion">{{ puc.puc_descripcion }}</small>
                                                    </td>
                                                    <td>
                                                        <span>{{ puc.puc_fecha_reprogramada || puc.puc_fecha_limite }}</span>
                                                        <span v-if="puc.puc_fecha_reprogramada" class="bs bw ms-1">Reprogramada</span>
                                                    </td>
                                                    <td>
                                                        <span v-if="puc.seguimientos && puc.seguimientos.length > 0 && puc.seguimientos[0].seq_cumplido" class="bs ba">
                                                            <i class="bi bi-check-circle me-1"></i>Cumplido
                                                        </span>
                                                        <span v-else-if="puc.seguimientos && puc.seguimientos.length > 0 && puc.seguimientos[0].seq_cumplido === false" class="bs bd">
                                                            <i class="bi bi-x-circle me-1"></i>No Cumplido
                                                        </span>
                                                        <span v-else class="bs bw">
                                                            <i class="bi bi-clock me-1"></i>Pendiente
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small v-if="puc.seguimientos && puc.seguimientos.length > 0 && puc.seguimientos[0].seq_observaciones">
                                                            {{ puc.seguimientos[0].seq_observaciones }}
                                                        </small>
                                                        <small v-else class="text-muted">—</small>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Pestaña: Entregables -->
                            <div v-if="tabHistorial === 'entregables'">
                                <div v-if="!equipoHistorial.entregables || equipoHistorial.entregables.length === 0" class="text-center py-4 text-muted">
                                    <i class="bi bi-info-circle fs-3 d-block mb-2 text-danger"></i>
                                    No hay entregables subidos por este equipo.
                                </div>
                                <div v-else class="sc">
                                    <div class="table-responsive">
                                        <table class="ct">
                                            <thead>
                                                <tr>
                                                    <th>Tipo / Archivo</th>
                                                    <th>Punto de Control</th>
                                                    <th>Versión</th>
                                                    <th>Estado</th>
                                                    <th class="text-end pe-3">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="ent in equipoHistorial.entregables" :key="ent.entr_id">
                                                    <td>
                                                        <div class="fw-bold">{{ ent.tipo_entregable?.tet_nombre || 'Entregable' }}</div>
                                                        <small class="text-muted"><i class="bi bi-file-earmark me-1"></i>{{ ent.entr_nombre_archivo }}</small>
                                                    </td>
                                                    <td>
                                                        <span v-if="ent.punto_control" class="bs bsec">{{ ent.punto_control.puc_nombre }}</span>
                                                        <span v-else class="text-muted">—</span>
                                                    </td>
                                                    <td><span class="bs bsec">v{{ ent.entr_version || 1 }}</span></td>
                                                    <td>
                                                        <span v-if="ent.entr_aprobado === true" class="bs ba">Aprobado</span>
                                                        <span v-else-if="ent.entr_aprobado === false" class="bs bd">Rechazado</span>
                                                        <span v-else class="bs bw">En Revisión</span>
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <a v-if="ent.entr_ruta" :href="`/storage/${ent.entr_ruta}`" target="_blank" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-download me-1"></i>Descargar
                                                        </a>
                                                        <span v-else class="text-muted">—</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Pestaña: Socializaciones -->
                            <div v-if="tabHistorial === 'socializaciones'">
                                <div v-if="!equipoHistorial.socializaciones || equipoHistorial.socializaciones.length === 0" class="text-center py-4 text-muted">
                                    <i class="bi bi-info-circle fs-3 d-block mb-2 text-danger"></i>
                                    No hay socializaciones registradas para este equipo.
                                </div>
                                <div v-else class="row g-3">
                                    <div v-for="soc in equipoHistorial.socializaciones" :key="soc.soc_id" class="col-md-6">
                                        <div class="sc h-100 p-3" style="border-left: 3px solid #dc3545 !important;">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="fw-bold mb-0" style="color: var(--text-main);">{{ soc.tipo_socializacion?.tso_nombre || 'Socialización' }}</h6>
                                                <span v-if="soc.soc_apto === true || soc.soc_aprobado === true" class="bs ba">
                                                    {{ soc.soc_apto !== null ? 'Apto' : 'Aprobado' }}
                                                </span>
                                                <span v-else-if="soc.soc_apto === false || soc.soc_aprobado === false" class="bs bd">
                                                    {{ soc.soc_apto !== null ? 'No Apto' : 'No Aprobado' }}
                                                </span>
                                                <span v-else class="bs bw">Pendiente</span>
                                            </div>
                                            <div class="small text-muted mb-2">
                                                <div><i class="bi bi-calendar-event me-1 text-danger"></i>{{ soc.soc_fecha }} a las {{ soc.soc_hora }}</div>
                                                <div><i class="bi bi-geo-alt me-1 text-danger"></i>{{ soc.soc_lugar }}</div>
                                            </div>
                                            <div v-if="soc.soc_observaciones" class="p-2 rounded small mb-2" style="background: var(--bg-th); border: 1px solid var(--border);">
                                                <strong>Observaciones:</strong> {{ soc.soc_observaciones }}
                                            </div>
                                            <div v-if="soc.jurados && soc.jurados.length > 0">
                                                <small class="fw-semibold text-muted d-block mb-1">Jurados:</small>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <span v-for="j in soc.jurados" :key="j.jso_id" class="bs bsec">
                                                        {{ j.usuario?.usu_primer_nombre }} {{ j.usuario?.usu_primer_apellido }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Pestaña: Integrantes -->
                            <div v-if="tabHistorial === 'integrantes'">
                                <div class="sc">
                                    <div class="table-responsive">
                                        <table class="ct">
                                            <thead>
                                                <tr>
                                                    <th>Cédula</th>
                                                    <th>Estudiante</th>
                                                    <th>Correo</th>
                                                    <th>Rol en Equipo</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="integ in equipoHistorial.integrantes" :key="integ.ein_id">
                                                    <td>{{ integ.usuario?.usu_cedula }}</td>
                                                    <td class="fw-bold">{{ integ.usuario?.usu_primer_nombre }} {{ integ.usuario?.usu_primer_apellido }}</td>
                                                    <td class="small text-muted">{{ integ.usuario?.usu_email || '—' }}</td>
                                                    <td>
                                                        <span v-if="integ.ein_es_lider" class="bs bd">
                                                            <i class="bi bi-star-fill me-1 text-warning"></i>Líder
                                                        </span>
                                                        <span v-else class="bs bsec">Integrante</span>
                                                    </td>
                                                    <td>
                                                        <span v-if="integ.ein_status" class="bs ba">Activo</span>
                                                        <span v-else class="bs bsec">Retirado</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="modal-footer d-flex justify-content-between align-items-center" style="background: var(--bg-th); border-top: 1px solid var(--border);">
                        <button
                            v-if="equipoHistorial?.equ_id"
                            type="button"
                            @click="irAExpediente(equipoHistorial.equ_id)"
                            class="btn btn-sm btn-outline-danger fw-bold rounded-pill px-3"
                        >
                            <i class="bi bi-folder-symlink me-1"></i> Ir al Expediente Completo
                        </button>
                        <span v-else></span>
                        <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
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
import { Link } from "@inertiajs/vue3";
const menuMaestras = ref(false);
const menuAcademico = ref(false);
const menuSistema = ref(false);
const props = defineProps({
    resultados: Array,
    proyectos: Array,
    estados: Array,
    trayectos: Array,
    secciones: Array,
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
const form = ref({
    rpr_id_pco: "",
    rpr_id_epr: "",
    rpr_id_tra: "",
    rpr_fecha_registro: "",
    rpr_observaciones: "",
});
const errores = ref({});
const loading = ref(false);
const itemSeleccionado = ref(null);
let modal = null;
let modalTardio = null;
const badgeRes = (n) => {
    if (n === "Aprobado") return "ba";
    if (n === "Aplazado") return "bw";
    if (n === "No Presentado") return "bd";
    return "bsec";
};
const abrirModal = () => {
    form.value = {
        rpr_id_pco: "",
        rpr_id_epr: "",
        rpr_id_tra: "",
        rpr_fecha_registro: new Date().toISOString().split("T")[0],
        rpr_observaciones: "",
    };
    errores.value = {};
    modal = modal || new Modal(document.getElementById("mRes"));
    modal.show();
};
const guardar = () => {
    loading.value = true;
    router.post("/coordinador/resultados", form.value, {
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
const habilitarTardio = (rpr) => {
    itemSeleccionado.value = rpr;
    modalTardio = modalTardio || new Modal(document.getElementById("mTardio"));
    modalTardio.show();
};
const confirmarTardio = () => {
    router.post(
        `/coordinador/resultados/${itemSeleccionado.value.rpr_id}/habilitar-tardio`,
    );
    modalTardio?.hide();
};

// ── Filtro y Búsqueda ────────────────────────────────────────────────
const filtroSeccion = ref("");
const busqueda = ref("");

const cleanCodigo = (cod) => {
    if (!cod) return "—";
    return cod.replace(/#DIS\d+$/, "");
};

const obtenerEquipo = (rpr) => {
    return rpr?.equipo || rpr?.proyecto_comunidad?.equipo;
};

const resultadosFiltrados = computed(() => {
    return (props.resultados || []).filter((rpr) => {
        const equipo = obtenerEquipo(rpr);
        const secId = equipo?.equ_id_sec || equipo?.seccion?.sec_id;

        if (filtroSeccion.value && String(secId) !== String(filtroSeccion.value)) {
            return false;
        }

        if (!busqueda.value.trim()) return true;
        const q = busqueda.value.toLowerCase();
        const cod = (equipo?.equ_codigo || "").toLowerCase();
        const tit = (equipo?.equ_titulo || "").toLowerCase();
        const com = (rpr.proyecto_comunidad?.comunidad?.com_nombre || "").toLowerCase();
        const tra = (rpr.trayecto?.tra_nombre || "").toLowerCase();
        const est = (rpr.estado_proyecto?.epr_nombre || "").toLowerCase();
        return cod.includes(q) || tit.includes(q) || com.includes(q) || tra.includes(q) || est.includes(q);
    });
});

// ── Historial Académico Modal ────────────────────────────────────────
const equipoHistorial = ref(null);
const cargandoHistorialAcademico = ref(false);
const tabHistorial = ref("puntos");
let modalHistorial = null;

const verHistorialEquipo = async (rpr) => {
    const eq = obtenerEquipo(rpr);
    if (!eq || !eq.equ_id) return;

    equipoHistorial.value = eq;
    tabHistorial.value = "puntos";
    cargandoHistorialAcademico.value = true;

    modalHistorial = modalHistorial || new Modal(document.getElementById("mHistorialAcademico"));
    modalHistorial.show();

    try {
        const res = await fetch(`/coordinador/equipos/${eq.equ_id}/historial-academico`, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();
        if (data.equipo) {
            equipoHistorial.value = data.equipo;
        }
    } catch (e) {
        console.error("Error al cargar historial académico", e);
    } finally {
        cargandoHistorialAcademico.value = false;
    }
};

const irAExpediente = (equId) => {
    if (!equId) return;
    if (modalHistorial) {
        modalHistorial.hide();
    }
    document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
    document.body.classList.remove('modal-open');
    document.body.style.removeProperty('overflow');
    document.body.style.removeProperty('padding-right');
    router.visit(`/coordinador/equipos/${equId}/expediente`);
};
</script>
<style>
.srch {
    position: relative;
    max-width: 280px;
}
.srch input {
    padding: 0.45rem 1rem 0.45rem 2.2rem;
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
    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1);
}
.srch i {
    position: absolute;
    left: 0.75rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}
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
