<template>
    <div
        class="puntos-page"
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
                    <button
                        class="btn btn-outline-danger btn-sm fw-bold rounded-pill px-3"
                        @click="abrirModalNuevoEntregable"
                    >
                        <i class="bi bi-file-earmark-plus me-1"></i>+ Nuevo Entregable
                    </button>
                    <button
                        class="btn btn-danger btn-sm fw-bold rounded-pill px-3"
                        :disabled="Boolean(filtroSeccion && seccionFiltroSinEsquema)"
                        :title="filtroSeccion && seccionFiltroSinEsquema ? 'Primero debes confirmar el Esquema de Evaluación para esta sección' : 'Crear nuevo punto de control'"
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
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <div v-if="(secciones || []).length > 0" style="min-width: 210px;">
                            <SelectBuscable
                                v-model="filtroSeccion"
                                :options="opcionesSeccionesFiltro"
                                size="sm"
                                placeholder="Todas mis secciones..."
                                search-placeholder="Filtrar sección..."
                            />
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
                </div>

                <!-- Alertas Flash / Validación -->
                <div
                    v-if="$page.props.flash?.error"
                    class="alert alert-danger alert-dismissible fade show py-2 px-3 small mb-3"
                    role="alert"
                >
                    <i class="bi bi-exclamation-octagon-fill me-2"></i>{{ $page.props.flash.error }}
                </div>
                <div
                    v-if="$page.props.flash?.success"
                    class="alert alert-success alert-dismissible fade show py-2 px-3 small mb-3"
                    role="alert"
                >
                    <i class="bi bi-check-circle-fill me-2"></i>{{ $page.props.flash.success }}
                </div>
                <div
                    v-if="Object.keys($page.props.errors || {}).length > 0"
                    class="alert alert-danger py-2 px-3 small mb-3"
                >
                    <div class="fw-bold mb-1">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i>No se pudo completar la operación:
                    </div>
                    <ul class="mb-0 ps-3">
                        <li v-for="(err, k) in $page.props.errors" :key="k">{{ err }}</li>
                    </ul>
                </div>

                <!-- ALERTA DE CONFIGURACIÓN OBLIGATORIA DE ESQUEMA DE EVALUACIÓN -->
                <div
                    v-if="(filtroSeccion && seccionFiltroSinEsquema) || (!filtroSeccion && seccionesSinEsquema.length > 0)"
                    class="alert alert-warning border-warning shadow-sm rounded-3 p-3 mb-4"
                >
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-exclamation-octagon-fill text-warning fs-5"></i>
                                Configuración Requerida: Esquema de Evaluación Pendiente
                            </div>
                            <div class="text-muted small">
                                <template v-if="filtroSeccion && seccionFiltroSinEsquema">
                                    La sección actual aún no tiene esquema de evaluación confirmado en el sistema.
                                    <span v-if="seccionFiltroSinEsquema.es_semestre_impar" class="fw-semibold text-primary">
                                        Para semestres impares corresponde la <strong>Evaluación Cualitativa Literal</strong> (baremo A, B, C, D) con respaldo documental.
                                    </span>
                                    <span v-else class="fw-semibold text-danger">
                                        Para semestres pares corresponde la <strong>Evaluación Cuantitativa</strong> (escala 1 a 20 pts, mínima aprobatoria 16 pts).
                                    </span>
                                </template>
                                <template v-else>
                                    Tienes <strong>{{ seccionesSinEsquema.length }}</strong> sección(es) sin esquema de evaluación oficial configurado. Debes confirmarlo para asegurar la coherencia evaluativa.
                                </template>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 flex-shrink-0">
                            <button
                                type="button"
                                class="btn btn-danger btn-sm fw-bold px-3 py-2 text-nowrap shadow-sm"
                                @click="confirmarEsquemaRecomendado"
                                :disabled="confirmandoEsquema"
                            >
                                <span v-if="confirmandoEsquema" class="spinner-border spinner-border-sm me-1"></span>
                                <i v-else class="bi bi-check2-circle me-1"></i>
                                {{ filtroSeccion ? 'Confirmar Esquema Oficial' : 'Confirmar Esquemas Oficiales' }}
                            </button>
                            <Link
                                href="/profesor/esquemas-evaluacion"
                                class="btn btn-outline-dark btn-sm fw-semibold px-3 py-2 text-nowrap"
                            >
                                <i class="bi bi-sliders me-1"></i>
                                Ver Esquemas
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- BARRA COMPACTA DE FILTROS POR SEMÁFORO Y PANEL DE DETALLE -->
                <div v-if="diagnosticosEquipos && Object.keys(diagnosticosEquipos).length > 0" class="mb-4">
                    <div class="sc p-3 mb-0">
                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <!-- Filtros rápidos por Situación del Proyecto (porcentaje) -->
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="fw-bold small text-muted">Situación del Proyecto (Semana {{ semanaActual }}):</span>
                                <button
                                    type="button"
                                    class="btn btn-sm rounded-pill py-1 px-2"
                                    :class="filtroSemaforo === '' ? 'btn-dark' : 'btn-outline-secondary'"
                                    style="font-size: 0.78rem;"
                                    @click="filtroSemaforo = ''"
                                >
                                    Todos ({{ Object.keys(diagnosticosEquipos).length }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm rounded-pill py-1 px-2"
                                    :class="filtroSemaforo === 'conforme' || filtroSemaforo === 'en_ruta' ? 'btn-success' : 'btn-outline-success'"
                                    style="font-size: 0.78rem;"
                                    @click="filtroSemaforo = (filtroSemaforo === 'conforme' || filtroSemaforo === 'en_ruta') ? '' : 'conforme'"
                                    title="75% a 100%: Cumple con los estándares y metas."
                                >
                                    🟢 Conforme ≥75% ({{ contarPorSemaforo('conforme') }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm rounded-pill py-1 px-2"
                                    :class="filtroSemaforo === 'observado' || filtroSemaforo === 'acelerar' ? 'btn-warning text-dark' : 'btn-outline-warning'"
                                    style="font-size: 0.78rem;"
                                    @click="filtroSemaforo = (filtroSemaforo === 'observado' || filtroSemaforo === 'acelerar') ? '' : 'observado'"
                                    title="50% a 74%: Tiene parámetros menores que deben mejorarse."
                                >
                                    🟡 Observado 50–74% ({{ contarPorSemaforo('observado') }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm rounded-pill py-1 px-2"
                                    :class="filtroSemaforo === 'no_conforme' || filtroSemaforo === 'riesgo_alto' ? 'btn-danger' : 'btn-outline-danger'"
                                    style="font-size: 0.78rem;"
                                    @click="filtroSemaforo = (filtroSemaforo === 'no_conforme' || filtroSemaforo === 'riesgo_alto') ? '' : 'no_conforme'"
                                    title="25% a 49%: Incumplimiento grave de los parámetros del proyecto."
                                >
                                    🔴 No conforme 25–49% ({{ contarPorSemaforo('no_conforme') }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm rounded-pill py-1 px-2"
                                    :class="filtroSemaforo === 'no_viable' || filtroSemaforo === 'inviable' ? 'btn-dark' : 'btn-outline-secondary'"
                                    style="font-size: 0.78rem;"
                                    @click="filtroSemaforo = (filtroSemaforo === 'no_viable' || filtroSemaforo === 'inviable') ? '' : 'no_viable'"
                                    title="Menor a 25%: Cierre administrativo por falta de factibilidad."
                                >
                                    ⚫ No viable &lt;25% ({{ contarPorSemaforo('no_viable') }})
                                </button>
                            </div>

                            <!-- Selector de equipo con buscador para ver diagnóstico detallado -->
                            <div class="d-flex align-items-center gap-2" style="min-width: 280px;">
                                <label class="small text-muted fw-semibold mb-0 flex-shrink-0">Detalle:</label>
                                <div class="flex-grow-1">
                                    <SelectBuscable
                                        v-model="equipoSeleccionadoDiag"
                                        :options="opcionesEquiposDiag"
                                        size="sm"
                                        placeholder="Buscar equipo para ver porcentaje..."
                                        search-placeholder="Código o nombre del equipo..."
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- BANNER DEL ESQUEMA DE EVALUACIÓN ASIGNADO -->
                        <div v-if="esquemaActual" class="mt-3 p-3 rounded-3 border" style="background: var(--bg-th); border-color: var(--border) !important;">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2 pb-2 border-bottom" style="border-color: var(--border) !important;">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-danger bg-opacity-10 text-danger p-2 rounded-circle">
                                        <i class="bi bi-file-earmark-ruled fs-6"></i>
                                    </span>
                                    <div>
                                        <div class="fw-bold small d-flex align-items-center gap-2">
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
                                    <div class="p-2 rounded border d-flex align-items-start gap-2 h-100" style="background: var(--bg-card); border-color: var(--border) !important;">
                                        <i class="bi bi-person-check-fill text-success mt-1"></i>
                                        <div>
                                            <div class="fw-bold" style="font-size: 0.76rem;">Asistencia y Puntualidad (30%)</div>
                                            <div class="text-muted" style="font-size: 0.71rem;">Participación continua en los encuentros de puntos de control.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2 rounded border d-flex align-items-start gap-2 h-100" style="background: var(--bg-card); border-color: var(--border) !important;">
                                        <i class="bi bi-file-earmark-check-fill text-primary mt-1"></i>
                                        <div>
                                            <div class="fw-bold" style="font-size: 0.76rem;">Entregables y Avance (50%)</div>
                                            <div class="text-muted" style="font-size: 0.71rem;">Evidencias técnicas y valoración porcentual o literal en seguimientos.</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-2 rounded border d-flex align-items-start gap-2 h-100" style="background: var(--bg-card); border-color: var(--border) !important;">
                                        <i class="bi bi-check2-circle text-warning mt-1"></i>
                                        <div>
                                            <div class="fw-bold" style="font-size: 0.76rem;">Cumplimiento y Acuerdos (20%)</div>
                                            <div class="text-muted" style="font-size: 0.71rem;">Subsanación de acuerdos y metas dentro de cada punto de control.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- PANEL COMPLETO SOLO PARA EL EQUIPO SELECCIONADO -->
                        <div v-if="equipoSeleccionadoDiag && diagnosticosEquipos[equipoSeleccionadoDiag]" class="mt-3 pt-3 border-top" style="border-color: var(--border) !important;">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold small">
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
                                class="nav-link fw-bold d-flex align-items-center gap-2 rounded-pill px-3 py-2 border"
                                :class="tabPuntos === 'pendientes' ? 'active bg-danger text-white border-danger shadow-sm' : 'text-body'"
                                :style="tabPuntos !== 'pendientes' ? 'background: var(--bg-card); border-color: var(--border) !important;' : ''"
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
                                class="nav-link fw-bold d-flex align-items-center gap-2 rounded-pill px-3 py-2 border"
                                :class="tabPuntos === 'archivados' ? 'active bg-danger text-white border-danger shadow-sm' : 'text-body'"
                                :style="tabPuntos !== 'archivados' ? 'background: var(--bg-card); border-color: var(--border) !important;' : ''"
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
                                <tr v-for="puc in paginados" :key="puc.puc_id">
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
                                                :title="getEtiquetaSemaforo(diagnosticosEquipos[puc.puc_id_equ].semaforo) + ': ' + getSignificadoSemaforo(diagnosticosEquipos[puc.puc_id_equ].semaforo)"
                                                @click="equipoSeleccionadoDiag = puc.puc_id_equ"
                                            >
                                                <span v-if="diagnosticosEquipos[puc.puc_id_equ].porcentaje_situacion !== undefined" class="me-1">
                                                    {{ diagnosticosEquipos[puc.puc_id_equ].porcentaje_situacion }}% ·
                                                </span>
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
                                        <!-- Si ya fue evaluado: consultar/editar -->
                                        <template v-if="estaEvaluado(puc)">
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
                        <span style="font-size: 0.82rem; color: var(--text-muted)">
                            {{ textoPaginacion }}
                        </span>
                        <div class="d-flex gap-1">
                            <button
                                class="btn btn-sm btn-outline-secondary"
                                :disabled="paginaActual === 1"
                                @click="paginaActual--"
                            >
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button
                                v-for="p in paginasV"
                                :key="p"
                                class="btn btn-sm"
                                :class="p === paginaActual ? 'btn-danger' : 'btn-outline-secondary'"
                                @click="paginaActual = p"
                            >
                                {{ p }}
                            </button>
                            <button
                                class="btn btn-sm btn-outline-secondary"
                                :disabled="paginaActual === totalPaginas"
                                @click="paginaActual++"
                            >
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <ModalPuntoControl ref="modalPuntoRef" :secciones="secciones" :equipos="equipos" />
        <ModalSeguimiento ref="modalSegRef" :tipos-encuentro="tiposEncuentro" :esquemas-por-seccion="esquemasPorSeccion" />

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
                                    :min="fechaMinimaReprog"
                                    :max="fechaMaximaReprog"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': erroresReprog.nueva_fecha,
                                    }"
                                    required
                                />
                                <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                    Límite permitido: desde hoy hasta un mes máximo ({{ fechaMinimaReprog }} al {{ fechaMaximaReprog }}).
                                </small>
                                <small v-if="$page.props.periodoActivo?.lapso_academico" class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                    Lapso activo: {{ $page.props.periodoActivo.fecha_inicio_formato }} - {{ $page.props.periodoActivo.fecha_fin_formato }}
                                </small>
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

        <!-- MODAL NUEVO ENTREGABLE EN TABLA MAESTRA -->
        <div class="modal fade" id="modalNuevoEntregableMaestro" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white">
                        <h6 class="modal-title fw-bold">
                            <i class="bi bi-file-earmark-plus text-danger me-2"></i>Registrar Nuevo Entregable en Catálogo Maestro
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form @submit.prevent="guardarNuevoEntregableMaestro">
                        <div class="modal-body p-4">
                            <div v-if="errorNuevoEntregable" class="alert alert-danger py-2 small mb-3">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ errorNuevoEntregable }}
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-uppercase text-muted">Nombre del Entregable *</label>
                                <input
                                    v-model="formNuevoEntregable.nombre"
                                    type="text"
                                    class="form-control"
                                    placeholder="Ej. Manual de Arquitectura, Guía de Despliegue..."
                                    required
                                />
                                <div class="form-text small">
                                    Se validará que no sea duplicado ni contenga opciones genéricas como "Otro".
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold small text-uppercase text-muted">Trayecto PNFI *</label>
                                <select v-model="formNuevoEntregable.trayecto" class="form-select" disabled>
                                    <option :value="1">Trayecto I (Alfabetización Tecnológica)</option>
                                    <option :value="2">Trayecto II (Desarrollo de Software)</option>
                                    <option :value="3">Trayecto III (Desarrollo de Software)</option>
                                    <option :value="4">Trayecto IV (Desarrollo de Software / Grado)</option>
                                </select>
                                <div class="form-text small text-primary">
                                    <i class="bi bi-lock-fill me-1"></i>Trayecto asignado automáticamente según la sección en curso.
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light p-3">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger btn-sm fw-bold px-3" :disabled="guardandoEntregable">
                                <span v-if="guardandoEntregable" class="spinner-border spinner-border-sm me-1"></span>
                                <i v-else class="bi bi-check-lg me-1"></i>Guardar en Catálogo Maestro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, computed, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Modal } from "bootstrap";
import { Link } from "@inertiajs/vue3";
import SelectBuscableEquipo from "@/Components/SelectBuscableEquipo.vue";
import SelectBuscable from "@/Components/SelectBuscable.vue";
import SemaforoEquipo from "@/Components/SemaforoEquipo.vue";
import SidebarProfesor from "@/Components/SidebarProfesor.vue";
import ModalPuntoControl from "@/Components/ModalPuntoControl.vue";
import ModalSeguimiento from "@/Components/ModalSeguimiento.vue";

const modalPuntoRef = ref(null);
const modalSegRef = ref(null);

const props = defineProps({
    puntosControl: { type: [Array, Object], default: () => [] },
    equipos: { type: Array, default: () => [] },
    secciones: { type: Array, default: () => [] },
    diagnosticosEquipos: { type: Object, default: () => ({}) },
    semanaActual: { type: Number, default: 1 },
    tiposEncuentro: { type: Array, default: () => [] },
    esquemasPorSeccion: { type: Object, default: () => ({}) },
    plantillasEsquemas: { type: Array, default: () => [] },
    tiposEntregable: { type: Array, default: () => [] },
});

const listaPuntosControl = computed(() => {
    if (Array.isArray(props.puntosControl)) return props.puntosControl;
    if (props.puntosControl?.data && Array.isArray(props.puntosControl.data)) return props.puntosControl.data;
    return [];
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
const filtroSeccion = ref("");
const seccionSeleccionadaModal = ref("");
const filtroSemaforo = ref("");
const equipoSeleccionadoDiag = ref("");
const tabPuntos = ref("pendientes"); // "pendientes" | "archivados"
const puntoConfirmadoReciente = ref(null);

const seccionesSinEsquema = computed(() => {
    return (props.secciones || []).filter(sec => {
        const esqInfo = props.esquemasPorSeccion?.[sec.sec_id];
        return esqInfo && !esqInfo.tiene_esquema_configurado;
    });
});

const seccionFiltroSinEsquema = computed(() => {
    if (filtroSeccion.value) {
        const esqInfo = props.esquemasPorSeccion?.[filtroSeccion.value];
        return esqInfo && !esqInfo.tiene_esquema_configurado ? esqInfo : null;
    }
    return null;
});

const confirmandoEsquema = ref(false);

const confirmarEsquemaRecomendado = () => {
    confirmandoEsquema.value = true;
    const info = seccionFiltroSinEsquema.value;
    const esqId = info?.id_esquema_recomendado || info?.esq_id;
    if (filtroSeccion.value && info && esqId) {
        router.post("/profesor/esquemas-evaluacion/asignar", {
            esq_sec_id_esq: esqId,
            esq_sec_id_sec: filtroSeccion.value,
            esq_sec_lapso: info.lapso,
            accion: "asignar",
        }, {
            preserveScroll: true,
            onFinish: () => { confirmandoEsquema.value = false; }
        });
    } else {
        router.post("/profesor/esquemas-evaluacion/auto-asignar", {
            sec_id: filtroSeccion.value || null,
        }, {
            preserveScroll: true,
            onFinish: () => { confirmandoEsquema.value = false; }
        });
    }
};

// Modal Nuevo Entregable en Catálogo Maestro
const modalNuevoEntregable = ref(null);
const guardandoEntregable = ref(false);
const errorNuevoEntregable = ref("");
const formNuevoEntregable = ref({
    nombre: "",
    trayecto: 1,
});

const abrirModalNuevoEntregable = () => {
    const secElegida = filtroSeccion.value
        ? (props.secciones || []).find((s) => String(s.sec_id) === String(filtroSeccion.value))
        : props.secciones?.[0];
    const trayectoAuto = Number(secElegida?.sec_id_tra || secElegida?.trayecto?.tra_numero || 1);
    formNuevoEntregable.value = {
        nombre: "",
        trayecto: trayectoAuto,
    };
    errorNuevoEntregable.value = "";
    const el = document.getElementById("modalNuevoEntregableMaestro");
    if (el) {
        modalNuevoEntregable.value = modalNuevoEntregable.value || new Modal(el);
        modalNuevoEntregable.value.show();
    }
};

const guardarNuevoEntregableMaestro = async () => {
    const nom = formNuevoEntregable.value.nombre.trim();
    if (!nom) {
        errorNuevoEntregable.value = "El nombre del entregable es obligatorio.";
        return;
    }
    if (["otro", "otros", "otra", "otras"].includes(nom.toLowerCase())) {
        errorNuevoEntregable.value = "No se permite registrar opciones genéricas como 'Otro' o 'Otros'.";
        return;
    }
    guardandoEntregable.value = true;
    errorNuevoEntregable.value = "";
    try {
        const res = await window.axios.post("/catalogos/crear-opcion", {
            catalogo: "tipo_entregable",
            nombre: nom,
            trayecto: formNuevoEntregable.value.trayecto,
        });
        if (res.data.success) {
            props.tiposEntregable.push({
                tet_id: res.data.id,
                tet_nombre: res.data.nombre,
                tet_trayecto_numero: formNuevoEntregable.value.trayecto,
                tet_status: 1,
            });
            modalNuevoEntregable.value?.hide();
            window.dispatchEvent(new CustomEvent("siscon:feedback", {
                detail: { type: "success", title: "Entregable registrado", message: res.data.message || "El nuevo tipo de entregable fue guardado en el catálogo maestro." }
            }));
        } else {
            errorNuevoEntregable.value = res.data.message || "Error al registrar el entregable.";
        }
    } catch (e) {
        errorNuevoEntregable.value = e.response?.data?.message || "Error al registrar el entregable.";
    } finally {
        guardandoEntregable.value = false;
    }
};

const opcionesSeccionesFiltro = computed(() => [
    { value: "", label: "Todas mis secciones..." },
    ...(props.secciones || []).map((s) => ({
        value: s.sec_id,
        label: `Sección ${s.sec_codigo}`,
        sublabel: s.trayecto?.tra_nombre || "",
    })),
]);

const opcionesEquiposDiag = computed(() => {
    const opts = [{ value: "", label: "Buscar equipo para ver ruta crítica..." }];
    Object.entries(props.diagnosticosEquipos || {}).forEach(([idEq, diag]) => {
        const ico = getIconoSemaforo(diag.semaforo).icon;
        const pct = diag.porcentaje_situacion !== undefined ? ` (${diag.porcentaje_situacion}%)` : "";
        opts.push({
            value: idEq,
            label: `${ico} ${diag.nombre_equipo || "Equipo #" + idEq}${pct}`,
        });
    });
    return opts;
});

const esConfirmado = (puc) => {
    const seg = puc?.seguimientos?.[0] || puc?.seguimiento;
    return Boolean(seg && (seg.seq_cumplido === true || seg.seq_cumplido === 1));
};

const totalPendientes = computed(() => {
    return listaPuntosControl.value.filter((p) => !esConfirmado(p)).length;
});

const totalArchivados = computed(() => {
    return listaPuntosControl.value.filter((p) => esConfirmado(p)).length;
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
        if (!d) return false;
        if (tipo === 'conforme' || tipo === 'en_ruta') return d.semaforo === 'conforme' || d.semaforo === 'en_ruta';
        if (tipo === 'observado' || tipo === 'acelerar') return d.semaforo === 'observado' || d.semaforo === 'acelerar';
        if (tipo === 'no_conforme' || tipo === 'riesgo_alto') return d.semaforo === 'no_conforme' || d.semaforo === 'riesgo_alto';
        if (tipo === 'no_viable' || tipo === 'inviable') return d.semaforo === 'no_viable' || d.semaforo === 'inviable';
        return d.semaforo === tipo;
    }).length;
};

const getIconoSemaforo = (tipo) => {
    switch (tipo) {
        case "en_ruta":
        case "conforme":
            return { icon: "🟢", class: "bg-success text-white", title: "Conforme: Cumple con los estándares y metas." };
        case "acelerar":
        case "observado":
            return { icon: "🟡", class: "bg-warning text-dark", title: "Observado: Tiene parametros menores que deben mejorarse." };
        case "riesgo_alto":
        case "no_conforme":
            return { icon: "🔴", class: "bg-danger text-white", title: "No conforme: Incumplimiento grave de los parámetros del proyecto." };
        case "inviable":
        case "no_viable":
            return { icon: "⚫", class: "bg-dark text-white", title: "No viable: Cierre administrativo por falta de factibilidad." };
        default:
            return { icon: "⚪", class: "bg-secondary text-white", title: "Pendiente" };
    }
};

const getEtiquetaSemaforo = (tipo) => {
    switch (tipo) {
        case "en_ruta":
        case "conforme":
            return "Conforme";
        case "acelerar":
        case "observado":
            return "Observado";
        case "riesgo_alto":
        case "no_conforme":
            return "No conforme";
        case "inviable":
        case "no_viable":
            return "No viable";
        default:
            return "Pendiente";
    }
};

const getSignificadoSemaforo = (tipo) => {
    switch (tipo) {
        case "en_ruta":
        case "conforme":
            return "Cumple con los estándares y metas.";
        case "acelerar":
        case "observado":
            return "Tiene parametros menores que deben mejorarse.";
        case "riesgo_alto":
        case "no_conforme":
            return "Incumplimiento grave de los parámetros del proyecto.";
        case "inviable":
        case "no_viable":
            return "Cierre administrativo por falta de factibilidad.";
        default:
            return "Pendiente de evaluación.";
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

const fechaMinimaReprog = computed(() => {
    const periMin = page.props.periodoActivo?.fecha_inicio;
    return periMin && periMin > fechaHoy.value ? periMin : fechaHoy.value;
});

const fechaMaximaReprog = computed(() => {
    const d = new Date();
    d.setMonth(d.getMonth() + 1);
    const y = d.getFullYear();
    const m = String(d.getMonth() + 1).padStart(2, "0");
    const day = String(d.getDate()).padStart(2, "0");
    const unMes = `${y}-${m}-${day}`;
    const periMax = page.props.periodoActivo?.fecha_fin;
    return periMax && periMax < unMes ? periMax : unMes;
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
    puc_es_ultimo_punto: false,
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
    seq_estado_semaforo: "conforme",
    seq_valoracion_avance: 50,
    es_ultimo_punto: false,
    desea_reprogramar: false,
    nueva_fecha: "",
    nueva_semana: "",
    acuerdos: [],
    asistencias: {},
});

const listaTiposEncuentro = ref([...(props.tiposEncuentro || [])]);
const creandoOtroTipoEncuentro = ref(false);
const nuevoTipoEncuentroNombre = ref("");
const guardandoTipoEncuentro = ref(false);

const verificarOtroTipoEncuentro = () => {
    if (formSeg.value.seq_id_tipo_encuentro === "__OTRO__") {
        creandoOtroTipoEncuentro.value = true;
        nuevoTipoEncuentroNombre.value = "";
    } else {
        creandoOtroTipoEncuentro.value = false;
    }
};

const guardarNuevoTipoEncuentro = async () => {
    if (!nuevoTipoEncuentroNombre.value.trim()) return;
    guardandoTipoEncuentro.value = true;
    try {
        const res = await axios.post("/catalogos/crear-opcion", {
            catalogo: "tipo_encuentro",
            nombre: nuevoTipoEncuentroNombre.value.trim(),
        });
        if (res.data.success) {
            listaTiposEncuentro.value.push({ tep_id: res.data.id, tep_nombre: res.data.nombre });
            formSeg.value.seq_id_tipo_encuentro = res.data.id;
            creandoOtroTipoEncuentro.value = false;
            nuevoTipoEncuentroNombre.value = "";
        }
    } catch (e) {
        console.error("Error al crear tipo de encuentro:", e);
    } finally {
        guardandoTipoEncuentro.value = false;
    }
};
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
    let list = listaPuntosControl.value;
    if (tabPuntos.value === 'pendientes') {
        list = list.filter((p) => !esConfirmado(p));
    } else {
        list = list.filter((p) => esConfirmado(p));
    }
    if (filtroSeccion.value) {
        list = list.filter((p) => String(p.equipo?.equ_id_sec) === String(filtroSeccion.value));
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
            if (!diag) return false;
            if (filtroSemaforo.value === 'conforme') return diag.semaforo === 'conforme' || diag.semaforo === 'en_ruta';
            if (filtroSemaforo.value === 'observado') return diag.semaforo === 'observado' || diag.semaforo === 'acelerar';
            if (filtroSemaforo.value === 'no_conforme') return diag.semaforo === 'no_conforme' || diag.semaforo === 'riesgo_alto';
            if (filtroSemaforo.value === 'no_viable') return diag.semaforo === 'no_viable' || diag.semaforo === 'inviable';
            return diag.semaforo === filtroSemaforo.value;
        });
    }
    return list;
});

const paginaActual = ref(1);
const porPagina = 3;
watch([busqueda, filtroSeccion, filtroSemaforo, tabPuntos], () => {
    paginaActual.value = 1;
});
const totalPaginas = computed(() => Math.ceil(filtrados.value.length / porPagina));
const desde = computed(() => (paginaActual.value - 1) * porPagina);
const hasta = computed(() => desde.value + porPagina);
const paginados = computed(() => filtrados.value.slice(desde.value, hasta.value));
const textoPaginacion = computed(() => {
    const total = filtrados.value?.length || 0;
    if (total === 0) return "0 registros";
    const start = desde.value + 1;
    const end = Math.min(hasta.value, total);
    return `Mostrando ${start}–${end} de ${total} (${porPagina} por página)`;
});
const paginasV = computed(() => {
    const t = totalPaginas.value,
        a = paginaActual.value,
        p = [],
        i = Math.max(1, a - 2),
        f = Math.min(t, a + 2);
    for (let x = i; x <= f; x++) p.push(x);
    return p;
});
const abrirModal = () => {
    if (filtroSeccion.value && seccionFiltroSinEsquema.value) {
        window.dispatchEvent(new CustomEvent("siscon:feedback", {
            detail: {
                type: "error",
                title: "Esquema de Evaluación Requerido",
                message: "Debes confirmar el Esquema de Evaluación de esta sección antes de crear un Punto de Control.",
            },
        }));
        return;
    }
    if (!filtroSeccion.value && (props.secciones || []).length > 0 && seccionesSinEsquema.value.length === (props.secciones || []).length) {
        window.dispatchEvent(new CustomEvent("siscon:feedback", {
            detail: {
                type: "error",
                title: "Esquema de Evaluación Requerido",
                message: "Tus secciones aún no tienen un Esquema de Evaluación confirmado. Haz clic en 'Confirmar Esquemas Oficiales' primero.",
            },
        }));
        return;
    }
    modalPuntoRef.value?.abrir();
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
    modalSegRef.value?.abrir(puc, soloLectura);
};

const _abrirSeguimientoLegacy = (puc, soloLectura = false) => {
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
            prevSeg?.seq_estado_semaforo
                ? (['positivo', 'conforme'].includes(prevSeg.seq_estado_semaforo) ? 'conforme' :
                   ['neutral', 'observado'].includes(prevSeg.seq_estado_semaforo) ? 'observado' :
                   ['negativo', 'no_conforme'].includes(prevSeg.seq_estado_semaforo) ? 'no_conforme' :
                   ['inviable', 'no_viable'].includes(prevSeg.seq_estado_semaforo) ? 'no_viable' : prevSeg.seq_estado_semaforo)
                : "conforme",
        seq_valoracion_avance:
            prevSeg?.seq_valoracion_avance !== undefined && prevSeg?.seq_valoracion_avance !== null
                ? Number(prevSeg.seq_valoracion_avance)
                : 50,
        es_ultimo_punto: Boolean(puc.puc_es_ultimo_punto),
        desea_reprogramar: false,
        nueva_fecha: puc.puc_fecha_reprogramada || "",
        nueva_semana: puc.puc_semana || "",
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

    if (formSeg.value.seq_cumplido === false) {
        if (!formSeg.value.seq_observaciones || formSeg.value.seq_observaciones.trim().length < 4) {
            erroresSeg.value = { seq_observaciones: "Debe justificar el motivo por el cual no se cumplió el punto de control." };
            return;
        }
        if (formSeg.value.desea_reprogramar) {
            if (!formSeg.value.nueva_fecha) {
                erroresSeg.value = { nueva_fecha: "Debe seleccionar la nueva fecha para reprogramar el punto de control." };
                return;
            }
            if (formSeg.value.nueva_fecha < fechaHoy.value) {
                erroresSeg.value = { nueva_fecha: "La fecha de reprogramación no puede ser en el pasado (debe ser a partir de hoy)." };
                return;
            }
            if (formSeg.value.nueva_fecha > fechaMaximaReprog.value) {
                erroresSeg.value = { nueva_fecha: "La fecha de reprogramación no puede exceder el límite de un mes a partir de hoy." };
                return;
            }
        }
    }

    loadingSeg.value = true;
    const idPunto = puntoActual.value?.puc_id;
    const idEquipo = puntoActual.value?.puc_id_equ;
    const codEquipo = puntoActual.value?.equipo?.equ_codigo || `Equipo #${idEquipo}`;
    const nomPunto = puntoActual.value?.puc_nombre || "Punto de control";
    const esConfirmadoGuardado = Boolean(formSeg.value.seq_cumplido);
    const fueReprogramado = Boolean(!esConfirmadoGuardado && formSeg.value.desea_reprogramar);

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
                } else if (!fueReprogramado) {
                    tabPuntos.value = "archivados";
                } else {
                    tabPuntos.value = "pendientes";
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
    if (!formReprog.value.nueva_fecha) {
        erroresReprog.value = { nueva_fecha: "Debe indicar la nueva fecha de encuentro." };
        return;
    }
    if (formReprog.value.nueva_fecha < fechaHoy.value) {
        erroresReprog.value = { nueva_fecha: "La fecha de reprogramación no puede ser en el pasado (debe ser a partir de hoy)." };
        return;
    }
    if (formReprog.value.nueva_fecha > fechaMaximaReprog.value) {
        erroresReprog.value = { nueva_fecha: "La fecha de reprogramación no puede exceder el límite de un mes a partir de hoy." };
        return;
    }
    if (!formReprog.value.motivo || formReprog.value.motivo.trim().length < 4) {
        erroresReprog.value = { motivo: "El motivo de la reprogramación es obligatorio (mínimo 4 caracteres)." };
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
.puntos-page {
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
.puntos-page[data-theme="dark"] {
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
