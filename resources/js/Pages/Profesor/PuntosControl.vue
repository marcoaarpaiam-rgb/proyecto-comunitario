<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
        "
    >
        <!-- SIDEBAR PROFESOR ESTANDARIZADO -->
        <SidebarProfesor :sidebar-open="sidebarOpen" @close="sidebarOpen = false" />

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
                    <!-- Botón Exportar Libro INVENTARIO (XLSX) -->
                    <div class="dropdown" v-if="props.secciones && props.secciones.length > 0">
                        <button
                            class="btn btn-outline-success btn-sm fw-bold rounded-pill px-3 dropdown-toggle d-inline-flex align-items-center gap-1"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                        >
                            <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                            Exportar Libro INVENTARIO (.xlsx)
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="min-width: 260px;">
                            <li class="dropdown-header text-uppercase small fw-bold text-success">Plantilla Oficial UPTP (7 Hojas)</li>
                            <li v-for="sec in props.secciones" :key="sec.sec_id">
                                <a
                                    class="dropdown-item d-flex align-items-center justify-content-between py-2"
                                    :href="`/inventario/excel/libro-seccion/${sec.sec_id}`"
                                    target="_blank"
                                >
                                    <span><strong>Sección:</strong> {{ sec.sec_codigo }}</span>
                                    <span class="badge bg-success-subtle text-success ms-2">Descargar</span>
                                </a>
                            </li>
                        </ul>
                    </div>

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

                <!-- BARRA COMPACTA DE FILTROS POR SEMÁFORO Y PANEL DE DETALLE -->
                <div v-if="diagnosticosEquipos && Object.keys(diagnosticosEquipos).length > 0" class="mb-4">
                    <div class="card border-0 shadow-sm p-3">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <!-- Filtros rápidos por estado de semáforo -->
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-bold small text-muted">Semáforo Semana {{ semanaActual }}:</span>
                                <button
                                    type="button"
                                    class="btn btn-sm rounded-pill py-0 px-2"
                                    :class="filtroSemaforo === '' ? 'btn-dark' : 'btn-outline-secondary'"
                                    style="font-size: 0.78rem;"
                                    @click="filtroSemaforo = ''"
                                >
                                    Todos ({{ Object.keys(diagnosticosEquipos).length }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm rounded-pill py-0 px-2"
                                    :class="filtroSemaforo === 'en_ruta' ? 'btn-success' : 'btn-outline-success'"
                                    style="font-size: 0.78rem;"
                                    @click="filtroSemaforo = filtroSemaforo === 'en_ruta' ? '' : 'en_ruta'"
                                >
                                    🟢 En ruta ({{ contarPorSemaforo('en_ruta') }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm rounded-pill py-0 px-2"
                                    :class="filtroSemaforo === 'acelerar' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark'"
                                    style="font-size: 0.78rem;"
                                    @click="filtroSemaforo = filtroSemaforo === 'acelerar' ? '' : 'acelerar'"
                                >
                                    🟡 Acelerar ({{ contarPorSemaforo('acelerar') }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm rounded-pill py-0 px-2"
                                    :class="filtroSemaforo === 'riesgo_alto' ? 'btn-danger' : 'btn-outline-danger'"
                                    style="font-size: 0.78rem;"
                                    @click="filtroSemaforo = filtroSemaforo === 'riesgo_alto' ? '' : 'riesgo_alto'"
                                >
                                    🔴 Riesgo alto ({{ contarPorSemaforo('riesgo_alto') }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm rounded-pill py-0 px-2"
                                    :class="filtroSemaforo === 'inviable' ? 'btn-dark' : 'btn-outline-dark'"
                                    style="font-size: 0.78rem;"
                                    @click="filtroSemaforo = filtroSemaforo === 'inviable' ? '' : 'inviable'"
                                >
                                    ⛔ Inviable ({{ contarPorSemaforo('inviable') }})
                                </button>
                            </div>

                            <!-- Selector de equipo para ver diagnóstico detallado -->
                            <div class="d-flex align-items-center gap-2">
                                <label class="small text-muted fw-semibold mb-0">Detalle:</label>
                                <select
                                    v-model="equipoSeleccionadoDiag"
                                    class="form-select form-select-sm"
                                    style="max-width: 270px; font-size: 0.8rem;"
                                >
                                    <option value="">Seleccione equipo para ver ruta crítica...</option>
                                    <option
                                        v-for="(diag, idEq) in diagnosticosEquipos"
                                        :key="idEq"
                                        :value="idEq"
                                    >
                                        {{ getIconoSemaforo(diag.semaforo) }} {{ diag.nombre_equipo || ('Equipo #' + idEq) }}
                                    </option>
                                </select>
                                <button
                                    v-if="equipoSeleccionadoDiag"
                                    type="button"
                                    class="btn btn-sm btn-link text-muted p-0"
                                    @click="equipoSeleccionadoDiag = ''"
                                    title="Cerrar detalle"
                                >
                                    <i class="bi bi-x-circle fs-6"></i>
                                </button>
                            </div>
                        </div>

                        <!-- BANNER DEL ESQUEMA DE EVALUACIÓN ASIGNADO Y SU RESPALDO FORMATIVO -->
                        <div v-if="esquemaActual" class="mt-3 p-3 rounded-3 bg-white border border-light-subtle shadow-xs">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2 pb-2 border-bottom">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-danger bg-opacity-10 text-danger p-2 rounded-circle">
                                        <i class="bi bi-file-earmark-ruled fs-6"></i>
                                    </span>
                                    <div>
                                        <div class="fw-bold text-dark small d-flex align-items-center gap-2">
                                            <span>Esquema de Evaluación Asignado: {{ esquemaActual.nombre }}</span>
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border" style="font-size: 0.68rem;">
                                                Lapso {{ esquemaActual.lapso }}
                                            </span>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            Modalidad: <strong class="text-capitalize">{{ (esquemaActual.modo || '').replace('_', ' ') }}</strong> · Escala: <strong>{{ esquemaActual.escala_min }} - {{ esquemaActual.escala_max }} pts</strong> (Mín. Aprobatorio: {{ esquemaActual.nota_aprobatoria }} pts)
                                        </small>
                                    </div>
                                </div>
                                <Link
                                    href="/profesor/esquemas-evaluacion"
                                    class="btn btn-sm btn-outline-danger py-1 px-3 rounded-pill fw-semibold"
                                    style="font-size: 0.73rem;"
                                >
                                    <i class="bi bi-sliders me-1"></i>Ver / Gestionar Esquemas
                                </Link>
                            </div>

                            <!-- Respaldo de los indicadores para la evaluación -->
                            <div class="row g-2 pt-1">
                                <div class="col-md-4">
                                    <div class="p-2 rounded bg-light border-0 d-flex align-items-start gap-2 h-100">
                                        <i class="bi bi-person-check-fill text-success mt-1"></i>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.76rem;">Asistencia y Puntualidad</div>
                                            <div class="text-muted" style="font-size: 0.71rem;">Garantiza la participación continua en los puntos de control requeridos por el esquema.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2 rounded bg-light border-0 d-flex align-items-start gap-2 h-100">
                                        <i class="bi bi-file-earmark-check-fill text-primary mt-1"></i>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.76rem;">Entregables del Catálogo PNFI</div>
                                            <div class="text-muted" style="font-size: 0.71rem;">Constituyen las evidencias técnicas auditables evaluadas según los criterios del PNF.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2 rounded bg-light border-0 d-flex align-items-start gap-2 h-100">
                                        <i class="bi bi-check2-circle text-warning mt-1"></i>
                                        <div>
                                            <div class="fw-bold text-dark" style="font-size: 0.76rem;">Acuerdos Rectificados</div>
                                            <div class="text-muted" style="font-size: 0.71rem;">Certifican la subsanación de correcciones metodológicas para el cierre evaluativo formal.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PANEL COMPLETO SOLO PARA EL EQUIPO SELECCIONADO -->
                        <div v-if="equipoSeleccionadoDiag && diagnosticosEquipos[equipoSeleccionadoDiag]" class="mt-3 pt-3 border-top">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-dark small">
                                    <i class="bi bi-compass-fill text-danger me-1"></i> Diagnóstico Completo: {{ diagnosticosEquipos[equipoSeleccionadoDiag].nombre_equipo }}
                                </span>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-secondary py-0 px-2 rounded-pill"
                                    style="font-size: 0.75rem;"
                                    @click="equipoSeleccionadoDiag = ''"
                                >
                                    Cerrar panel
                                </button>
                            </div>
                            <SemaforoEquipo :diagnostico="diagnosticosEquipos[equipoSeleccionadoDiag]" />
                        </div>
                    </div>
                </div>

                <!-- TABS DE NAVEGACIÓN: PENDIENTES VS ARCHIVADOS -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <ul class="nav nav-pills gap-2" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-bold d-flex align-items-center gap-2 rounded-pill px-3 py-2"
                                :class="tabPuntos === 'pendientes' ? 'active bg-danger text-white shadow-sm' : 'btn-outline-secondary'"
                                type="button"
                                @click="tabPuntos = 'pendientes'"
                            >
                                <i class="bi bi-clock-history"></i>
                                <span>Pendientes por Confirmar</span>
                                <span
                                    class="badge rounded-pill"
                                    :class="tabPuntos === 'pendientes' ? 'bg-white text-danger' : 'bg-secondary text-white'"
                                >
                                    {{ totalPendientes }}
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-bold d-flex align-items-center gap-2 rounded-pill px-3 py-2"
                                :class="tabPuntos === 'archivados' ? 'active bg-danger text-white shadow-sm' : 'btn-outline-secondary'"
                                type="button"
                                @click="tabPuntos = 'archivados'"
                            >
                                <i class="bi bi-archive-fill"></i>
                                <span>Confirmados / Archivados</span>
                                <span
                                    class="badge rounded-pill"
                                    :class="tabPuntos === 'archivados' ? 'bg-white text-danger' : 'bg-secondary text-white'"
                                >
                                    {{ totalArchivados }}
                                </span>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- ALERTA DE CONFIRMACIÓN EXITOSA CON ENLACE AL EXPEDIENTE -->
                <div
                    v-if="puntoConfirmadoReciente"
                    class="alert alert-success alert-dismissible fade show d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3 shadow-sm border-success"
                    role="alert"
                >
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                        <span>
                            Punto de control <strong>{{ puntoConfirmadoReciente.nombre_punto }}</strong> confirmado exitosamente y archivado.
                        </span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <Link
                            :href="`/profesor/equipos/${puntoConfirmadoReciente.id_equipo}/expediente`"
                            class="btn btn-sm btn-success fw-bold text-white rounded-pill px-3"
                        >
                            <i class="bi bi-folder2-open me-1"></i>Ver Expediente del {{ puntoConfirmadoReciente.codigo_equipo }}
                        </Link>
                        <button
                            type="button"
                            class="btn-close position-static p-2"
                            @click="puntoConfirmadoReciente = null"
                            aria-label="Close"
                        ></button>
                    </div>
                </div>

                <div class="sc">
                    <div class="table-responsive">
                        <table class="ct">
                            <thead>
                                <tr>
                                    <th class="ps-4">Punto</th>
                                    <th>Equipo</th>
                                    <th>Fecha de Encuentro</th>
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
                                        {{ tabPuntos === 'pendientes' ? 'No hay puntos de control pendientes por confirmar' : 'No hay puntos de control confirmados / archivados' }}
                                    </td>
                                </tr>
                                <tr v-for="puc in filtrados" :key="puc.puc_id">
                                    <td class="ps-4 fw-semibold">
                                        {{ puc.puc_nombre }}
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                            <span class="bs bp">{{ puc.equipo?.equ_codigo }}</span>
                                            <Link
                                                :href="`/profesor/equipos/${puc.puc_id_equ}/expediente`"
                                                class="btn btn-xs btn-outline-info py-0 px-2 rounded-pill d-inline-flex align-items-center gap-1"
                                                style="font-size: 0.72rem;"
                                                title="Ver expediente del equipo"
                                            >
                                                <i class="bi bi-folder2-open"></i>
                                                <span>Expediente</span>
                                            </Link>
                                            <button
                                                v-if="diagnosticosEquipos && diagnosticosEquipos[puc.puc_id_equ]"
                                                type="button"
                                                class="badge border-0 cursor-pointer"
                                                :class="'bg-' + (diagnosticosEquipos[puc.puc_id_equ].color || 'secondary')"
                                                style="font-size: 0.72rem; cursor: pointer;"
                                                title="Ver Diagnóstico y Ruta Crítica del Equipo"
                                                @click="equipoSeleccionadoDiag = puc.puc_id_equ"
                                            >
                                                {{ getEtiquetaSemaforo(diagnosticosEquipos[puc.puc_id_equ].semaforo) }}
                                            </button>
                                        </div>
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
                                        <template v-if="estaEvaluado(puc)">
                                            <span
                                                v-if="
                                                    puc.seguimientos[0]
                                                        .seq_cumplido
                                                "
                                                class="bs ba"
                                                >Cumplido</span
                                            >
                                            <span v-else class="bs bd"
                                                >No cumplido</span
                                            >
                                        </template>
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
                                        <!-- Si ya fue evaluado: corte Excel, consultar/editar -->
                                        <template v-if="estaEvaluado(puc)">
                                            <a
                                                :href="`/inventario/excel/punto-control/${puc.puc_id}`"
                                                class="btn btn-sm btn-outline-success me-1 d-inline-flex align-items-center gap-1"
                                                title="Descargar Corte Oficial Excel (Semana real)"
                                                target="_blank"
                                            >
                                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="8" y1="13" x2="16" y2="13"/><line x1="8" y1="17" x2="16" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                                                Excel
                                            </a>
                                            <button
                                                class="btn btn-sm btn-outline-primary me-1"
                                                @click="
                                                    abrirSeguimiento(puc, false)
                                                "
                                                title="Consultar o editar seguimiento"
                                            >
                                                <i class="bi bi-pencil-square me-1"></i>Editar
                                            </button>
                                        </template>
                                        <!-- Si no está evaluado: registrar, reprogramar, eliminar -->
                                        <template v-else>
                                            <button
                                                class="btn btn-sm btn-outline-success me-1"
                                                @click="
                                                    abrirSeguimiento(puc, false)
                                                "
                                                title="Registrar seguimiento"
                                            >
                                                <i
                                                    class="bi bi-clipboard-check"
                                                ></i>
                                            </button>
                                            <button
                                                class="btn btn-sm btn-outline-warning rounded-pill me-1"
                                                @click="abrirReprogramar(puc)"
                                                title="Reprogramar fecha"
                                            >
                                                <i
                                                    class="bi bi-calendar-event"
                                                ></i>
                                            </button>
                                            <button
                                                class="btn btn-sm btn-outline-danger"
                                                @click="eliminar(puc)"
                                                title="Eliminar"
                                            >
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </template>
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
                                    >Sección</label
                                >
                                <select
                                    v-model="seccionSeleccionadaModal"
                                    class="form-select form-select-sm"
                                    @change="onSeccionModalChange"
                                >
                                    <option value="">
                                        Todas las secciones...
                                    </option>
                                    <option
                                        v-for="sec in secciones"
                                        :key="sec.sec_id"
                                        :value="sec.sec_id"
                                    >
                                        Sección {{ sec.sec_codigo }}
                                    </option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Equipo <span class="text-danger">*</span>
                                </label>
                                <SelectBuscableEquipo
                                    v-model="form.puc_id_equ"
                                    :equipos="equiposModalFiltrados"
                                    :has-error="!!errores.puc_id_equ"
                                    placeholder="🔍 Escribe código, título o sección del equipo..."
                                />
                                <div
                                    v-if="errores.puc_id_equ"
                                    class="text-danger small mt-1"
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
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold"
                                        >Semana de Corte / Encuentro <span class="text-danger">*</span></label
                                    >
                                    <div class="input-group">
                                        <span
                                            class="input-group-text"
                                            style="
                                                background: var(--bg-card);
                                                color: var(--text-muted);
                                            "
                                            >Semana</span
                                        >
                                        <input
                                            v-model.number="form.puc_semana"
                                            type="number"
                                            min="1"
                                            max="18"
                                            step="1"
                                            class="form-control"
                                            :class="{
                                                'is-invalid': errorSemana || errores.puc_semana,
                                            }"
                                            placeholder="1 a 18"
                                            @keydown="bloquearCaracteresSemana($event)"
                                            @input="validarSemanaInput"
                                            required
                                        />
                                    </div>
                                    <div
                                        v-if="errorSemana || errores.puc_semana"
                                        class="invalid-feedback d-block"
                                    >
                                        {{ errorSemana || errores.puc_semana }}
                                    </div>
                                    <small
                                        class="text-muted"
                                        style="font-size: 0.72rem"
                                        >Rango estricto del 1 al 18. Sugerido: 6 (Corte 1), 10 (Corte 2), 14 (Corte 3)</small
                                    >
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold"
                                        >Fecha de Encuentro
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        v-model="form.puc_fecha_limite"
                                        type="date"
                                        :min="fechaHoy"
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
                                <div class="col-md-3">
                                    <label class="form-label fw-semibold"
                                        >Días Aviso</label
                                    >
                                    <!-- Días de aviso en PuntosControl.vue -->
                                    <input
                                        type="number"
                                        class="form-control"
                                        v-model="form.puc_dias_aviso"
                                        min="1"
                                        max="30"
                                        @keypress="
                                            (e) =>
                                                !/[0-9]/.test(e.key) &&
                                                e.preventDefault()
                                        "
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
                            {{
                                modoSoloLecturaSeg
                                    ? "Evaluación del Punto — "
                                    : "Registrar Seguimiento — "
                            }}
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
                            <div
                                v-if="modoSoloLecturaSeg"
                                class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-3"
                            >
                                <i
                                    class="bi bi-lock-fill fs-5 text-secondary"
                                ></i>
                                <div>
                                    <strong>Evaluación Registrada:</strong> Este
                                    punto de control ya fue evaluado como
                                    <span
                                        class="badge"
                                        :class="
                                            formSeg.seq_cumplido
                                                ? 'bg-success'
                                                : 'bg-danger'
                                        "
                                    >
                                        {{
                                            formSeg.seq_cumplido
                                                ? "Cumplido"
                                                : "No cumplido"
                                        }} </span
                                    >. No es posible modificarlo ni volver a
                                    subir el resultado.
                                </div>
                            </div>
                            <div
                                v-if="puntoActual?.seguimientos?.length"
                                class="mt-2"
                            >
                                <p
                                    class="small fw-bold mb-1"
                                    style="color: var(--text-muted)"
                                >
                                    Asistencias registradas:
                                </p>
                                <table class="table table-sm small mb-0">
                                    <thead>
                                        <tr>
                                            <th>Integrante</th>
                                            <th class="text-center">Asistió</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="a in puntoActual
                                                ?.seguimientos?.[0]
                                                ?.asistencias"
                                            :key="a.apc_id"
                                        >
                                            <td>
                                                {{
                                                    a.usuario?.usu_primer_nombre
                                                }}
                                                {{
                                                    a.usuario
                                                        ?.usu_primer_apellido
                                                }}
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    :class="
                                                        a.apc_asistio
                                                            ? 'ba'
                                                            : 'bd'
                                                    "
                                                >
                                                    {{
                                                        a.apc_asistio
                                                            ? "Sí"
                                                            : "No"
                                                    }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Búsqueda / Verificación encadenada de observaciones previas -->
                            <div
                                v-if="puntoActual?.punto_previo_observaciones"
                                class="alert alert-warning py-2 px-3 mb-3 small border-warning"
                            >
                                <div
                                    class="fw-bold text-dark d-flex align-items-center gap-1 mb-1"
                                >
                                    <i
                                        class="bi bi-clock-history text-warning fs-6"
                                    ></i>
                                    <span
                                        >Observaciones del Punto de Control
                                        Previo ({{
                                            puntoActual.punto_previo_nombre
                                        }}):</span
                                    >
                                </div>
                                <div
                                    class="p-2 rounded bg-white text-dark border mb-2"
                                    style="white-space: pre-wrap"
                                >
                                    {{ puntoActual.punto_previo_observaciones }}
                                </div>
                                <label class="form-label fw-bold mb-1"
                                    >¿El equipo cumplió y subsanó las
                                    observaciones anteriores?</label
                                >
                                <div class="d-flex gap-3 mb-2">
                                    <div class="form-check">
                                        <input
                                            type="radio"
                                            class="form-check-input"
                                            v-model="formSeg.seq_cumplio_previo"
                                            :value="true"
                                            :disabled="modoSoloLecturaSeg"
                                            :id="'cumplioPrevSi_' + (puntoActual?.puc_id || 'item')"
                                        />
                                        <label
                                            class="form-check-label fw-semibold text-success"
                                            :for="'cumplioPrevSi_' + (puntoActual?.puc_id || 'item')"
                                            >Sí, cumplió</label
                                        >
                                    </div>
                                    <div class="form-check">
                                        <input
                                            type="radio"
                                            class="form-check-input"
                                            v-model="formSeg.seq_cumplio_previo"
                                            :value="false"
                                            :disabled="modoSoloLecturaSeg"
                                            :id="'cumplioPrevNo_' + (puntoActual?.puc_id || 'item')"
                                        />
                                        <label
                                            class="form-check-label fw-semibold text-danger"
                                            :for="'cumplioPrevNo_' + (puntoActual?.puc_id || 'item')"
                                            >No cumplió</label
                                        >
                                    </div>
                                </div>
                                <input
                                    type="text"
                                    v-model="
                                        formSeg.seq_obs_previas_verificacion
                                    "
                                    :disabled="modoSoloLecturaSeg"
                                    class="form-control form-control-sm bg-white"
                                    placeholder="Detalle de verificación de las observaciones anteriores..."
                                />
                            </div>

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
                                            :disabled="modoSoloLecturaSeg"
                                            :id="'cumplido_si_' + (puntoActual?.puc_id || 'item')"
                                        /><label
                                            class="form-check-label fw-semibold"
                                            :for="'cumplido_si_' + (puntoActual?.puc_id || 'item')"
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
                                            :disabled="modoSoloLecturaSeg"
                                            :id="'cumplido_no_' + (puntoActual?.puc_id || 'item')"
                                        /><label
                                            class="form-check-label fw-semibold"
                                            :for="'cumplido_no_' + (puntoActual?.puc_id || 'item')"
                                            style="color: #dc3545"
                                            >No cumplido</label
                                        >
                                    </div>
                                </div>
                                <div v-if="erroresSeg.seq_cumplido" class="text-danger small mt-1">
                                    {{ erroresSeg.seq_cumplido }}
                                </div>
                            </div>

                            <!-- Tipo de Encuentro -->
                            <div class="card border p-3 mb-3 bg-light bg-opacity-50">
                                <label class="form-label fw-bold d-flex align-items-center gap-1 mb-1">
                                    <i class="bi bi-tag-fill text-danger"></i> Tipo de Encuentro <span class="text-danger">*</span>
                                </label>
                                <small class="text-muted mb-2 d-block" style="font-size: 0.73rem">
                                    Modalidad de acompañamiento según el catálogo oficial
                                </small>
                                <select
                                    v-model="formSeg.seq_id_tipo_encuentro"
                                    class="form-select form-select-sm"
                                    :disabled="modoSoloLecturaSeg"
                                >
                                    <option :value="null">Seleccione tipo de encuentro...</option>
                                    <option v-for="te in props.tiposEncuentro" :key="te.tep_id" :value="te.tep_id">
                                        {{ te.tep_nombre }}
                                    </option>
                                </select>
                            </div>

                            <!-- Dos Campos Visualmente Separados: Nivel de Avance Técnico y Semáforo del Encuentro -->
                            <div class="row g-3 mb-3">
                                <!-- CAMPO 1: Nivel / % de Avance Técnico -->
                                <div class="col-md-6">
                                    <div class="card h-100 border p-3 bg-light bg-opacity-50">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label class="form-label fw-bold mb-0">Nivel de Avance Técnico</label>
                                            <span class="badge bg-dark fw-bold">{{ formSeg.seq_valoracion_avance || 0 }}%</span>
                                        </div>
                                        <small class="text-muted mb-2 d-block" style="font-size: 0.72rem">
                                            Estimación porcentual de lo ejecutado en este punto
                                        </small>
                                        <div class="input-group input-group-sm mb-2">
                                            <input
                                                type="number"
                                                min="0"
                                                max="100"
                                                v-model.number="formSeg.seq_valoracion_avance"
                                                class="form-control"
                                                placeholder="0 - 100"
                                                :disabled="modoSoloLecturaSeg"
                                            />
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div
                                                class="progress-bar bg-danger"
                                                role="progressbar"
                                                :style="{ width: `${formSeg.seq_valoracion_avance || 0}%` }"
                                            ></div>
                                        </div>
                                    </div>
                                </div>

                                <!-- CAMPO 2: Valoración / Semáforo del Encuentro -->
                                <div class="col-md-6">
                                    <div class="card h-100 border p-3 bg-light bg-opacity-50">
                                        <label class="form-label fw-bold mb-1">Valoración / Semáforo del Encuentro</label>
                                        <small class="text-muted mb-2 d-block" style="font-size: 0.72rem">
                                            Cualificación del desempeño en la sesión
                                        </small>
                                        <div class="d-flex flex-column gap-2">
                                            <div class="form-check">
                                                <input
                                                    type="radio"
                                                    class="form-check-input"
                                                    v-model="formSeg.seq_estado_semaforo"
                                                    value="positivo"
                                                    :disabled="modoSoloLecturaSeg"
                                                    id="sem_pos"
                                                />
                                                <label class="form-check-label text-success fw-bold small" for="sem_pos">
                                                    🟢 Positivo (Avance adecuado)
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input
                                                    type="radio"
                                                    class="form-check-input"
                                                    v-model="formSeg.seq_estado_semaforo"
                                                    value="neutral"
                                                    :disabled="modoSoloLecturaSeg"
                                                    id="sem_neu"
                                                />
                                                <label class="form-check-label text-warning fw-bold small" for="sem_neu">
                                                    🟡 Neutral (Parcial con observaciones)
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input
                                                    type="radio"
                                                    class="form-check-input"
                                                    v-model="formSeg.seq_estado_semaforo"
                                                    value="negativo"
                                                    :disabled="modoSoloLecturaSeg"
                                                    id="sem_neg"
                                                />
                                                <label class="form-check-label text-danger fw-bold small" for="sem_neg">
                                                    🔴 Negativo (Riesgo o incumplimiento)
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Acuerdos y Correcciones para el próximo encuentro -->
                            <div class="card border p-3 mb-3 bg-light bg-opacity-50">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <label class="form-label fw-bold mb-0">Acuerdos y Correcciones para el Próximo Encuentro</label>
                                        <small class="text-muted d-block" style="font-size: 0.72rem">
                                            Compromisos con fecha límite y seguimiento de estado
                                        </small>
                                    </div>
                                    <button
                                        v-if="!modoSoloLecturaSeg"
                                        type="button"
                                        class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill"
                                        @click="agregarAcuerdoModal"
                                    >
                                        <i class="bi bi-plus-circle me-1"></i> Agregar Compromiso
                                    </button>
                                </div>
                                <div v-if="!formSeg.acuerdos || formSeg.acuerdos.length === 0" class="text-muted small p-2 bg-white rounded border">
                                    Sin acuerdos específicos registrados para este encuentro.
                                </div>
                                <div v-for="(acue, aIdx) in formSeg.acuerdos" :key="aIdx" class="row g-2 mb-2 align-items-center p-2 bg-white rounded border">
                                    <div class="col-md-5">
                                        <label class="form-label text-muted small mb-0" style="font-size: 0.7rem">Descripción</label>
                                        <input
                                            v-model="acue.descripcion"
                                            type="text"
                                            class="form-control form-control-sm"
                                            placeholder="Descripción del compromiso o corrección..."
                                            :disabled="modoSoloLecturaSeg"
                                        />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted small mb-0" style="font-size: 0.7rem">Fecha límite</label>
                                        <input
                                            v-model="acue.fecha_limite"
                                            type="date"
                                            class="form-control form-control-sm"
                                            :disabled="modoSoloLecturaSeg"
                                        />
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted small mb-0" style="font-size: 0.7rem">Estado</label>
                                        <select
                                            v-model="acue.estado"
                                            class="form-select form-select-sm"
                                            :disabled="modoSoloLecturaSeg"
                                        >
                                            <option value="pendiente">⏳ Pendiente</option>
                                            <option value="en_proceso">🔄 En Proceso</option>
                                            <option value="rectificado">✅ Rectificado</option>
                                        </select>
                                    </div>
                                    <div class="col-md-1 text-end" v-if="!modoSoloLecturaSeg">
                                        <label class="form-label d-block text-transparent small mb-0" style="font-size: 0.7rem">&nbsp;</label>
                                        <button type="button" class="btn btn-sm btn-outline-danger p-0 px-1" @click="eliminarAcuerdoModal(aIdx)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Nuevas Observaciones para este punto</label
                                >
                                <textarea
                                    v-model="formSeg.seq_observaciones"
                                    :disabled="modoSoloLecturaSeg"
                                    class="form-control"
                                    rows="3"
                                    placeholder="Indique las observaciones y correcciones que el equipo debe atender para el próximo corte..."
                                ></textarea>
                            </div>

                            <!-- Control Individual de Asistencia y Participación -->
                            <div class="card border p-3 mb-3 bg-light bg-opacity-50">
                                <div class="mb-2">
                                    <label class="form-label fw-bold mb-0">Control Individual de Asistencia y Participación</label>
                                    <small class="text-muted d-block" style="font-size: 0.72rem">
                                        Marque asistencia, participación verificable y registre justificativo o motivo de edición si aplica
                                    </small>
                                </div>
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
                                    class="p-2 mb-2 rounded border bg-white"
                                >
                                    <template v-if="formSeg.asistencias && formSeg.asistencias[int.ein_id_usu]">
                                        <div
                                            class="d-flex align-items-center justify-content-between flex-wrap gap-2"
                                        >
                                            <div>
                                                <strong style="font-size: 0.9rem"
                                                    >{{
                                                        int.usuario
                                                            ?.usu_primer_nombre
                                                    }}
                                                    {{
                                                        int.usuario
                                                            ?.usu_primer_apellido
                                                    }}</strong
                                                >
                                                <span
                                                    class="badge bg-light text-dark border ms-2 small"
                                                    >C.I.
                                                    {{
                                                        int.usuario?.usu_cedula
                                                    }}</span
                                                >
                                            </div>
                                            <div class="d-flex align-items-center gap-3">
                                                <!-- Switch Asistió -->
                                                <div
                                                    class="form-check form-switch mb-0"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        :id="'asist_' + int.ein_id"
                                                        :disabled="modoSoloLecturaSeg"
                                                        v-model="
                                                            formSeg.asistencias[
                                                                int.ein_id_usu
                                                            ].asistio
                                                        "
                                                    />
                                                    <label
                                                        class="form-check-label small fw-bold"
                                                        :for="'asist_' + int.ein_id"
                                                    >
                                                        {{
                                                            formSeg.asistencias[
                                                                int.ein_id_usu
                                                            ]?.asistio
                                                                ? "Asistió"
                                                                : "Inasistencia"
                                                        }}
                                                    </label>
                                                </div>

                                                <!-- Switch Participación -->
                                                <div
                                                    class="form-check form-switch mb-0"
                                                    v-if="formSeg.asistencias[int.ein_id_usu]?.asistio"
                                                >
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        :id="'part_' + int.ein_id"
                                                        :disabled="modoSoloLecturaSeg"
                                                        v-model="
                                                            formSeg.asistencias[
                                                                int.ein_id_usu
                                                            ].participo
                                                        "
                                                    />
                                                    <label
                                                        class="form-check-label small fw-semibold text-primary"
                                                        :for="'part_' + int.ein_id"
                                                    >
                                                        Participó
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- Si falta, justificación -->
                                        <div
                                            v-if="
                                                !formSeg.asistencias[int.ein_id_usu]
                                                    ?.asistio
                                            "
                                            class="mt-2 p-2 rounded bg-danger bg-opacity-10 border border-danger-subtle"
                                        >
                                            <div
                                                class="d-flex align-items-center gap-2 mb-1"
                                            >
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    :id="'just_' + int.ein_id"
                                                    :disabled="modoSoloLecturaSeg"
                                                    v-model="
                                                        formSeg.asistencias[
                                                            int.ein_id_usu
                                                        ].justificado
                                                    "
                                                />
                                                <label
                                                    class="form-check-label small fw-bold text-danger"
                                                    :for="'just_' + int.ein_id"
                                                >
                                                    ¿Presentó Justificativo?
                                                </label>
                                            </div>
                                            <input
                                                type="text"
                                                v-model="
                                                    formSeg.asistencias[
                                                        int.ein_id_usu
                                                    ].justificativo
                                                "
                                                :disabled="modoSoloLecturaSeg"
                                                class="form-control form-control-sm"
                                                placeholder="Motivo o constancia de la inasistencia..."
                                            />
                                        </div>
                                        <!-- Observación individual y motivo de edición -->
                                        <div class="mt-2 row g-2">
                                            <div :class="formSeg.asistencias[int.ein_id_usu]?.es_existente ? 'col-md-7' : 'col-12'">
                                                <input
                                                    type="text"
                                                    v-model="
                                                        formSeg.asistencias[
                                                            int.ein_id_usu
                                                        ].observacion
                                                    "
                                                    :disabled="modoSoloLecturaSeg"
                                                    class="form-control form-control-sm"
                                                    placeholder="Observación individual..."
                                                />
                                            </div>
                                            <div v-if="formSeg.asistencias[int.ein_id_usu]?.es_existente" class="col-md-5">
                                                <input
                                                    type="text"
                                                    v-model="
                                                        formSeg.asistencias[
                                                            int.ein_id_usu
                                                        ].motivo_edicion
                                                    "
                                                    :disabled="modoSoloLecturaSeg"
                                                    class="form-control form-control-sm border-warning"
                                                    placeholder="Motivo si editó asistencia..."
                                                />
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button
                                type="button"
                                class="btn btn-secondary"
                                data-bs-dismiss="modal"
                            >
                                {{ modoSoloLecturaSeg ? "Cerrar" : "Cancelar" }}
                            </button>
                            <button
                                v-if="!modoSoloLecturaSeg"
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
        <div
            class="modal fade"
            id="mReprog"
            tabindex="-1"
            aria-labelledby="mReprogLabel"
            aria-hidden="true"
        >
            <div
                class="modal-dialog modal-dialog-centered"
                style="max-width: 450px"
            >
                <div
                    class="modal-content border-0 shadow-lg"
                    style="
                        border-radius: 12px;
                        overflow: hidden;
                        background: var(--bg-card);
                    "
                >
                    <div
                        class="mh"
                        style="
                            background: #1a1d23;
                            border-bottom: 2px solid #dc3545;
                            color: #fff;
                            padding: 1rem 1.25rem;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;
                        "
                    >
                        <h5
                            id="mReprogLabel"
                            class="mb-0 fw-bold fs-6 text-white d-flex align-items-center"
                        >
                            <i
                                class="bi bi-calendar-event text-warning me-2"
                            ></i
                            >Reprogramar Encuentro
                        </h5>
                        <button
                            type="button"
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                            aria-label="Cerrar"
                        >
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <form @submit.prevent="guardarReprogramar">
                        <div
                            class="modal-body p-4"
                            style="color: var(--text-main)"
                        >
                            <div
                                v-if="puntoReprog"
                                class="alert py-2 px-3 small mb-3 rounded"
                                style="
                                    background: var(--bg-th);
                                    border: 1px solid var(--border);
                                    color: var(--text-main);
                                "
                            >
                                <div class="fw-bold text-danger">
                                    {{ puntoReprog.puc_nombre }}
                                </div>
                                <div
                                    class="text-muted"
                                    style="font-size: 0.75rem"
                                >
                                    Equipo:
                                    <strong>{{
                                        puntoReprog.equipo?.equ_codigo
                                    }}</strong>
                                    &bull; Fecha actual:
                                    {{
                                        puntoReprog.puc_fecha_reprogramada ||
                                        puntoReprog.puc_fecha_limite
                                    }}
                                </div>
                            </div>

                            <div
                                v-if="erroresReprog.general || erroresReprog.error"
                                class="alert alert-danger py-2 px-3 small mb-3"
                            >
                                {{ erroresReprog.general || erroresReprog.error }}
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small"
                                    >Nueva Fecha de Encuentro
                                    <span class="text-danger">*</span></label
                                >
                                <input
                                    v-model="formReprog.nueva_fecha"
                                    type="date"
                                    :min="fechaHoy"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': erroresReprog.nueva_fecha,
                                    }"
                                    required
                                />
                                <div
                                    v-if="erroresReprog.nueva_fecha"
                                    class="invalid-feedback"
                                >
                                    {{ erroresReprog.nueva_fecha }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold small"
                                    >Nueva Semana de Encuentro (Opcional, 1 a 18)</label
                                >
                                <div class="input-group">
                                    <span
                                        class="input-group-text"
                                        style="
                                            background: var(--bg-card);
                                            color: var(--text-muted);
                                        "
                                        >Semana</span
                                    >
                                    <input
                                        v-model.number="formReprog.nueva_semana"
                                        type="number"
                                        min="1"
                                        max="18"
                                        step="1"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': errorSemanaReprog || erroresReprog.nueva_semana,
                                        }"
                                        placeholder="1 a 18"
                                        @keydown="bloquearCaracteresSemanaReprog($event)"
                                        @input="validarSemanaReprogInput"
                                    />
                                </div>
                                <div
                                    v-if="errorSemanaReprog || erroresReprog.nueva_semana"
                                    class="invalid-feedback d-block"
                                >
                                    {{ errorSemanaReprog || erroresReprog.nueva_semana }}
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label fw-semibold small"
                                    >Motivo de la Reprogramación
                                    <span class="text-danger">*</span></label
                                >
                                <textarea
                                    v-model="formReprog.motivo"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': erroresReprog.motivo,
                                    }"
                                    rows="3"
                                    placeholder="Indica la razón por la cual se reprograma el encuentro..."
                                    required
                                ></textarea>
                                <div
                                    v-if="erroresReprog.motivo"
                                    class="invalid-feedback"
                                >
                                    {{ erroresReprog.motivo }}
                                </div>
                            </div>
                        </div>
                        <div
                            class="modal-footer border-0 p-3 pt-0 d-flex justify-content-end gap-2"
                            style="background: var(--bg-card)"
                        >
                            <button
                                type="button"
                                class="btn btn-sm btn-secondary px-3"
                                data-bs-dismiss="modal"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="btn btn-sm btn-warning fw-bold text-dark px-3"
                                :disabled="loadingReprog"
                            >
                                <span
                                    v-if="loadingReprog"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>
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
import { Link } from "@inertiajs/vue3";
import SelectBuscableEquipo from "@/Components/SelectBuscableEquipo.vue";
import SemaforoEquipo from "@/Components/SemaforoEquipo.vue";
import SidebarProfesor from "@/Components/SidebarProfesor.vue";

const props = defineProps({
    puntosControl: Array,
    equipos: Array,
    secciones: { type: Array, default: () => [] },
    diagnosticosEquipos: { type: Object, default: () => ({}) },
    semanaActual: { type: Number, default: 1 },
    tiposEncuentro: { type: Array, default: () => [] },
    esquemasPorSeccion: { type: Object, default: () => ({}) },
});
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
const seccionSeleccionadaModal = ref("");
const filtroSemaforo = ref("");
const equipoSeleccionadoDiag = ref("");
const tabPuntos = ref("pendientes"); // "pendientes" | "archivados"
const puntoConfirmadoReciente = ref(null);

const esConfirmado = (puc) => {
    const seg = puc?.seguimientos?.[0] || puc?.seguimiento;
    return Boolean(seg && (seg.seq_cumplido === true || seg.seq_cumplido === 1));
};

const totalPendientes = computed(() => {
    return (props.puntosControl || []).filter((p) => !esConfirmado(p)).length;
});

const totalArchivados = computed(() => {
    return (props.puntosControl || []).filter((p) => esConfirmado(p)).length;
});

const esquemaActual = computed(() => {
    if (equipoSeleccionadoDiag.value) {
        const eq = (props.equipos || []).find((e) => String(e.equ_id) === String(equipoSeleccionadoDiag.value));
        if (eq?.equ_id_sec && props.esquemasPorSeccion?.[eq.equ_id_sec]) {
            return props.esquemasPorSeccion[eq.equ_id_sec];
        }
    }
    const keys = Object.keys(props.esquemasPorSeccion || {});
    if (keys.length > 0) {
        return props.esquemasPorSeccion[keys[0]];
    }
    return null;
});

const contarPorSemaforo = (tipo) => {
    if (!props.equipos || !props.diagnosticosEquipos) return 0;
    return props.equipos.filter((eq) => {
        const d = props.diagnosticosEquipos[eq.equ_id];
        return d && d.semaforo === tipo;
    }).length;
};

const getIconoSemaforo = (tipo) => {
    switch (tipo) {
        case "en_ruta":
            return { icon: "🟢", class: "bg-success text-white" };
        case "acelerar":
            return { icon: "🟡", class: "bg-warning text-dark" };
        case "riesgo_alto":
            return { icon: "🔴", class: "bg-danger text-white" };
        case "inviable":
            return { icon: "⛔", class: "bg-dark text-white" };
        default:
            return { icon: "⚪", class: "bg-secondary text-white" };
    }
};

const getEtiquetaSemaforo = (tipo) => {
    switch (tipo) {
        case "en_ruta":
            return "En ruta";
        case "acelerar":
            return "Acelerar";
        case "riesgo_alto":
            return "Riesgo alto";
        case "inviable":
            return "Inviable";
        default:
            return "Pendiente";
    }
};

const fechaHoy = computed(() => {
    const d = new Date();
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, "0");
    const day = String(d.getDate()).padStart(2, "0");
    return `${y}-${m}-${day}`;
});
const fechaManana = computed(() => {
    const d = new Date();
    d.setDate(d.getDate() + 1);
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, "0");
    const day = String(d.getDate()).padStart(2, "0");
    return `${y}-${m}-${day}`;
});

const onSeccionModalChange = () => {
    if (seccionSeleccionadaModal.value && form.value.puc_id_equ) {
        const eq = props.equipos?.find(
            (e) => e.equ_id == form.value.puc_id_equ,
        );
        if (eq && eq.equ_id_sec != seccionSeleccionadaModal.value) {
            form.value.puc_id_equ = "";
        }
    }
};

const equiposModalFiltrados = computed(() => {
    let list = props.equipos || [];
    if (seccionSeleccionadaModal.value) {
        list = list.filter(
            (e) => e.equ_id_sec == seccionSeleccionadaModal.value,
        );
    }
    return list;
});

const form = ref({
    puc_id_equ: "",
    puc_nombre: "",
    puc_fecha_limite: "",
    puc_dias_aviso: 3,
    puc_descripcion: "",
});
const errores = ref({});
const erroresSeg = ref({});
const loading = ref(false);
const formSeg = ref({
    seq_cumplido: null,
    seq_cumplio_previo: null,
    seq_obs_previas_verificacion: "",
    seq_observaciones: "",
    seq_id_tipo_encuentro: null,
    seq_estado_semaforo: "positivo",
    seq_valoracion_avance: 50,
    acuerdos: [],
    asistencias: {},
});
const loadingSeg = ref(false);
const puntoActual = ref(null);
const puntoReprog = ref(null);
const formReprog = ref({ nueva_fecha: "", nueva_semana: "", motivo: "" });
const erroresReprog = ref({});
const loadingReprog = ref(false);
const errorSemana = ref("");
const errorSemanaReprog = ref("");

const bloquearCaracteresSemana = (e) => {
    const prohibidos = ['e', 'E', '+', '-', '.', ',', ' '];
    if (prohibidos.includes(e.key)) {
        e.preventDefault();
        errorSemana.value = "Solo se permiten números enteros del 1 al 18.";
    }
};

const validarSemanaInput = () => {
    if (form.value.puc_semana === "" || form.value.puc_semana === null || form.value.puc_semana === undefined) {
        errorSemana.value = "La semana es obligatoria.";
        return false;
    }
    const val = Number(form.value.puc_semana);
    if (isNaN(val) || !Number.isInteger(val)) {
        errorSemana.value = "La semana debe ser un número entero sin decimales ni caracteres especiales.";
        return false;
    } else if (val < 1 || val > 18) {
        errorSemana.value = "La semana debe estar estrictamente en el rango del 1 al 18.";
        return false;
    } else {
        errorSemana.value = "";
        return true;
    }
};

const bloquearCaracteresSemanaReprog = (e) => {
    const prohibidos = ['e', 'E', '+', '-', '.', ',', ' '];
    if (prohibidos.includes(e.key)) {
        e.preventDefault();
        errorSemanaReprog.value = "Solo se permiten números enteros del 1 al 18.";
    }
};

const validarSemanaReprogInput = () => {
    if (formReprog.value.nueva_semana === "" || formReprog.value.nueva_semana === null || formReprog.value.nueva_semana === undefined) {
        errorSemanaReprog.value = "";
        return true;
    }
    const val = Number(formReprog.value.nueva_semana);
    if (isNaN(val) || !Number.isInteger(val)) {
        errorSemanaReprog.value = "La semana debe ser un número entero sin decimales ni caracteres especiales.";
        return false;
    } else if (val < 1 || val > 18) {
        errorSemanaReprog.value = "La semana debe estar estrictamente en el rango del 1 al 18.";
        return false;
    } else {
        errorSemanaReprog.value = "";
        return true;
    }
};

const puntoAEliminar = ref(null);
let mPunto = null;
let mSeg = null;
let mReprog = null;
let mElim = null;
const estaVencido = (f) => {
    if (!f) return false;
    const [y, m, d] = f.split('-').map(Number);
    const fechaLocal = new Date(y, m - 1, d, 23, 59, 59);
    return fechaLocal < new Date();
};
const esCercano = (f) => {
    if (!f) return false;
    const [y, m, d] = f.split('-').map(Number);
    const fechaLocal = new Date(y, m - 1, d, 23, 59, 59);
    const diff = fechaLocal - new Date();
    return diff > 0 && diff < 3 * 86400000;
};
const filtrados = computed(() => {
    let list = props.puntosControl || [];
    if (tabPuntos.value === 'pendientes') {
        list = list.filter((p) => !esConfirmado(p));
    } else {
        list = list.filter((p) => esConfirmado(p));
    }
    if (busqueda.value.trim()) {
        const q = busqueda.value.toLowerCase();
        list = list.filter(
            (p) =>
                p.puc_nombre?.toLowerCase().includes(q) ||
                p.equipo?.equ_codigo?.toLowerCase().includes(q) ||
                p.equipo?.equ_titulo?.toLowerCase().includes(q),
        );
    }
    if (filtroSemaforo.value) {
        list = list.filter((p) => {
            const diag = props.diagnosticosEquipos?.[p.equipo?.equ_id];
            return diag && diag.semaforo === filtroSemaforo.value;
        });
    }
    return list;
});
const abrirModal = () => {
    seccionSeleccionadaModal.value = "";
    errorSemana.value = "";
    form.value = {
        puc_id_equ: "",
        puc_nombre: "",
        puc_semana: 6,
        puc_fecha_limite: "",
        puc_dias_aviso: 3,
        puc_descripcion: "",
    };
    errores.value = {};
    mPunto = mPunto || new Modal(document.getElementById("mPunto"));
    mPunto.show();
};
const guardar = () => {
    if (!validarSemanaInput()) {
        return;
    }
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
const modoSoloLecturaSeg = ref(false);
const estaEvaluado = (puc) => {
    const seg = puc?.seguimientos?.[0];
    return (
        seg !== undefined &&
        seg !== null &&
        seg.seq_cumplido !== null &&
        seg.seq_cumplido !== undefined
    );
};

const abrirSeguimiento = (puc, soloLectura = false) => {
    puntoActual.value = puc;
    modoSoloLecturaSeg.value = soloLectura;
    const initialAsist = {};
    const ints = puc.equipo?.integrantes || [];
    const prevSeg = puc.seguimientos?.[0];
    const prevAsist = prevSeg?.asistencias || [];
    const prevMap = {};
    prevAsist.forEach((a) => {
        prevMap[a.apc_id_usu] = {
            asistio: Boolean(a.apc_asistio),
            participo: Boolean(a.apc_participo),
            justificado: Boolean(a.apc_justificado),
            justificativo: a.apc_justificativo || "",
            observacion: a.apc_observaciones || "",
            motivo_edicion: a.apc_motivo_edicion || "",
        };
    });

    ints.forEach((i) => {
        const prev = prevMap[i.ein_id_usu];
        initialAsist[i.ein_id_usu] = prev
            ? {
                  asistio: prev.asistio,
                  participo: prev.participo,
                  justificado: prev.justificado,
                  justificativo: prev.justificativo,
                  observacion: prev.observacion,
                  motivo_edicion: prev.motivo_edicion,
                  es_existente: true,
              }
            : {
                  asistio: true,
                  participo: false,
                  justificado: false,
                  justificativo: "",
                  observacion: "",
                  motivo_edicion: "",
                  es_existente: false,
              };
    });

    formSeg.value = {
        seq_cumplido:
            prevSeg?.seq_cumplido !== undefined && prevSeg?.seq_cumplido !== null
                ? Boolean(prevSeg.seq_cumplido)
                : null,
        seq_cumplio_previo:
            prevSeg?.seq_cumplio_previo !== undefined &&
            prevSeg?.seq_cumplio_previo !== null
                ? Boolean(prevSeg.seq_cumplio_previo)
                : null,
        seq_obs_previas_verificacion:
            prevSeg?.seq_obs_previas_verificacion || "",
        seq_observaciones: prevSeg?.seq_observaciones || "",
        seq_id_tipo_encuentro:
            prevSeg?.seq_id_tipo_encuentro || (props.tiposEncuentro?.[0]?.tep_id || null),
        seq_estado_semaforo:
            prevSeg?.seq_estado_semaforo || "positivo",
        seq_valoracion_avance:
            prevSeg?.seq_valoracion_avance !== undefined && prevSeg?.seq_valoracion_avance !== null
                ? Number(prevSeg.seq_valoracion_avance)
                : 50,
        acuerdos: prevSeg?.acuerdos?.length
            ? prevSeg.acuerdos.map((a) => ({
                  acue_id: a.acue_id,
                  descripcion: a.acue_descripcion,
                  fecha_limite: a.acue_fecha_limite ? a.acue_fecha_limite.split("T")[0] : "",
                  estado: a.acue_estado || "pendiente",
              }))
            : [],
        asistencias: initialAsist,
    };
    erroresSeg.value = {};
    mSeg = mSeg || new Modal(document.getElementById("mSeg"));
    mSeg.show();
};

const agregarAcuerdoModal = () => {
    if (!formSeg.value.acuerdos) formSeg.value.acuerdos = [];
    formSeg.value.acuerdos.push({ descripcion: "", fecha_limite: "", estado: "pendiente" });
};

const eliminarAcuerdoModal = (idx) => {
    formSeg.value.acuerdos.splice(idx, 1);
};
const guardarSeguimiento = () => {
    if (modoSoloLecturaSeg.value) return;
    if (formSeg.value.seq_cumplido === null || formSeg.value.seq_cumplido === undefined) {
        erroresSeg.value = { seq_cumplido: "Debe indicar si el equipo cumplió o no con este punto de control." };
        return;
    }
    loadingSeg.value = true;
    const idPunto = puntoActual.value?.puc_id;
    const idEquipo = puntoActual.value?.puc_id_equ;
    const codEquipo = puntoActual.value?.equipo?.equ_codigo || `Equipo #${idEquipo}`;
    const nomPunto = puntoActual.value?.puc_nombre || "Punto de control";
    const esConfirmadoGuardado = Boolean(formSeg.value.seq_cumplido);

    router.post(
        `/profesor/puntos-control/${idPunto}/seguimiento`,
        formSeg.value,
        {
            preserveScroll: true,
            onSuccess: () => {
                loadingSeg.value = false;
                mSeg?.hide();
                if (esConfirmadoGuardado) {
                    tabPuntos.value = "archivados";
                    puntoConfirmadoReciente.value = {
                        id_equipo: idEquipo,
                        codigo_equipo: codEquipo,
                        nombre_punto: nomPunto,
                    };
                }
            },
            onError: (e) => {
                loadingSeg.value = false;
                erroresSeg.value = e;
            },
        },
    );
};
const abrirReprogramar = (puc) => {
    puntoReprog.value = puc;
    errorSemanaReprog.value = "";
    formReprog.value = {
        nueva_fecha: puc.puc_fecha_reprogramada || "",
        nueva_semana: puc.puc_semana || "",
        motivo: puc.puc_motivo_reprogramacion || "",
    };
    erroresReprog.value = {};
    const modalEl = document.getElementById("mReprog");
    if (modalEl) {
        mReprog = Modal.getInstance(modalEl) || new Modal(modalEl);
        mReprog.show();
    }
};
const guardarReprogramar = () => {
    if (!puntoReprog.value) return;
    if (!validarSemanaReprogInput()) {
        return;
    }
    loadingReprog.value = true;
    erroresReprog.value = {};

    router.post(
        `/profesor/puntos-control/${puntoReprog.value.puc_id}/reprogramar`,
        formReprog.value,
        {
            preserveScroll: true,
            onSuccess: () => {
                loadingReprog.value = false;
                const modalEl = document.getElementById("mReprog");
                const inst = Modal.getInstance(modalEl);
                if (inst) inst.hide();
                // Limpiar posibles backdrops residuales
                document
                    .querySelectorAll(".modal-backdrop")
                    .forEach((el) => el.remove());
                document.body.classList.remove("modal-open");
                document.body.style.removeProperty("overflow");
                document.body.style.removeProperty("padding-right");
                puntoReprog.value = null;
            },
            onError: (errs) => {
                loadingReprog.value = false;
                erroresReprog.value = errs;
            },
        },
    );
};
const eliminar = (puc) => {
    puntoAEliminar.value = puc;
    const modalEl = document.getElementById("mElim");
    if (modalEl) {
        mElim = Modal.getInstance(modalEl) || new Modal(modalEl);
        mElim.show();
    }
};
const confirmarEliminar = () => {
    router.delete(`/profesor/puntos-control/${puntoAEliminar.value.puc_id}`, {
        onSuccess: () => {
            const modalEl = document.getElementById("mElim");
            const inst = Modal.getInstance(modalEl);
            if (inst) inst.hide();
            document
                .querySelectorAll(".modal-backdrop")
                .forEach((el) => el.remove());
            document.body.classList.remove("modal-open");
        },
    });
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
