<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
        "
    >
        <SidebarProfesor
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
                            class="bi bi-mic me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Socializaciones
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button
                        class="btn btn-danger btn-sm fw-bold rounded-pill px-3"
                        @click="abrirModal()"
                    >
                        <i class="bi bi-plus-lg me-1"></i>Nueva
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
                            Socializaciones
                        </h4>
                        <p
                            style="
                                color: var(--text-muted);
                                font-size: 0.85rem;
                                margin-top: 2px;
                            "
                        >
                            Pre-socializaciones y socializaciones finales
                        </p>
                    </div>
                </div>

                <div v-if="$page.props.flash?.error" class="alert alert-danger py-2 px-3 small mb-3">
                    <i class="bi bi-exclamation-octagon-fill me-2"></i>{{ $page.props.flash.error }}
                </div>
                <div v-if="$page.props.flash?.success" class="alert alert-success py-2 px-3 small mb-3">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ $page.props.flash.success }}
                </div>
                <div v-if="Object.keys($page.props.errors || {}).length > 0" class="alert alert-danger py-2 px-3 small mb-3">
                    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i>Corrige los siguientes errores:</div>
                    <ul class="mb-0 ps-3">
                        <li v-for="(err, k) in $page.props.errors" :key="k">{{ err }}</li>
                    </ul>
                </div>
                <!-- MÓDULOS SEPARADOS: 1. PRE-SOCIALIZACIÓN VS 2. SOCIALIZACIÓN FINAL -->
                <div class="card border-0 shadow-sm p-2 mb-3 rounded-4" style="background: var(--bg-card); border: 1px solid var(--border) !important;">
                    <ul class="nav nav-pills gap-2 flex-wrap" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-bold d-flex align-items-center gap-2 rounded-pill px-4 py-2"
                                :class="moduloActivo === 'pre_socializacion' ? 'active bg-danger text-white shadow-sm' : 'text-body'"
                                type="button"
                                @click="moduloActivo = 'pre_socializacion'"
                            >
                                <i class="bi bi-person-check-fill fs-5"></i>
                                <span>1. Módulo Pre-Socialización (Pase a Defensa)</span>
                                <span
                                    class="badge rounded-pill"
                                    :class="moduloActivo === 'pre_socializacion' ? 'bg-white text-danger' : 'bg-secondary text-white'"
                                >
                                    {{ totalPreSoc }}
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-bold d-flex align-items-center gap-2 rounded-pill px-4 py-2"
                                :class="moduloActivo === 'socializacion_final' ? 'active bg-danger text-white shadow-sm' : 'text-body'"
                                type="button"
                                @click="moduloActivo = 'socializacion_final'"
                            >
                                <i class="bi bi-mortarboard-fill fs-5"></i>
                                <span>2. Módulo Socialización Final (Defensa con Jurados)</span>
                                <span
                                    class="badge rounded-pill"
                                    :class="moduloActivo === 'socializacion_final' ? 'bg-white text-danger' : 'bg-secondary text-white'"
                                >
                                    {{ totalSocFinal }}
                                </span>
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- TABS DE NAVEGACIÓN: EN CURSO / PENDIENTES VS FINALIZADAS / ARCHIVADAS -->
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                    <ul class="nav nav-pills gap-2" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-bold d-flex align-items-center gap-2 rounded-pill px-3 py-2"
                                :class="tabSocializaciones === 'pendientes' ? 'active bg-danger text-white shadow-sm' : 'btn-outline-secondary'"
                                type="button"
                                @click="tabSocializaciones = 'pendientes'"
                            >
                                <i class="bi bi-clock-history"></i>
                                <span>En Curso / Pendientes</span>
                                <span
                                    class="badge rounded-pill"
                                    :class="tabSocializaciones === 'pendientes' ? 'bg-white text-danger' : 'bg-secondary text-white'"
                                >
                                    {{ totalPendientes }}
                                </span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button
                                class="nav-link fw-bold d-flex align-items-center gap-2 rounded-pill px-3 py-2"
                                :class="tabSocializaciones === 'archivadas' ? 'active bg-danger text-white shadow-sm' : 'btn-outline-secondary'"
                                type="button"
                                @click="tabSocializaciones = 'archivadas'"
                            >
                                <i class="bi bi-archive-fill"></i>
                                <span>Finalizadas / Archivadas</span>
                                <span
                                    class="badge rounded-pill"
                                    :class="tabSocializaciones === 'archivadas' ? 'bg-white text-danger' : 'bg-secondary text-white'"
                                >
                                    {{ totalArchivadas }}
                                </span>
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="sc">
                    <div class="table-responsive">
                        <table class="ct">
                            <thead>
                                <tr>
                                    <th class="ps-4">Equipo</th>
                                    <th>Tipo</th>
                                    <th>Fecha</th>
                                    <th>Lugar</th>
                                    <th>Resultado</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!socializacionesFiltradas?.length">
                                    <td
                                        colspan="6"
                                        class="text-center py-5"
                                        style="color: var(--text-muted)"
                                    >
                                        {{ tabSocializaciones === 'pendientes' ? 'No hay socializaciones en curso o pendientes' : 'No hay socializaciones finalizadas / archivadas' }}
                                    </td>
                                </tr>
                                <tr
                                    v-for="soc in socializacionesPaginadas"
                                    :key="soc.soc_id"
                                >
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-1 flex-wrap">
                                            <button
                                                type="button"
                                                class="btn btn-link p-0 text-danger fw-bold text-decoration-none text-start d-flex align-items-center gap-1"
                                                @click="abrirDetalleEquipo(soc)"
                                                title="Ver todas las opciones del equipo"
                                            >
                                                <i class="bi bi-box-arrow-up-right small"></i>
                                                <span>{{ soc.equipo?.equ_codigo }}</span>
                                            </button>
                                            <Link
                                                v-if="soc.equipo?.equ_id"
                                                :href="`/profesor/equipos/${soc.equipo.equ_id}/expediente`"
                                                class="btn btn-xs btn-outline-info py-0 px-2 rounded-pill d-inline-flex align-items-center gap-1"
                                                style="font-size: 0.72rem;"
                                                title="Ver expediente del equipo"
                                            >
                                                <i class="bi bi-folder2-open"></i>
                                                <span>Expediente</span>
                                            </Link>
                                        </div>
                                        <div class="small text-muted text-truncate" style="max-width: 170px;" :title="soc.equipo?.equ_titulo">
                                            {{ soc.equipo?.equ_titulo || 'Proyecto PST' }}
                                        </div>
                                    </td>
                                    <td>
                                        <span
                                            class="bs"
                                            :class="
                                                soc.tipo_socializacion
                                                    ?.tso_orden === 1
                                                    ? 'bw'
                                                    : 'ba'
                                            "
                                            >{{
                                                soc.tipo_socializacion
                                                    ?.tso_nombre
                                            }}</span
                                        >
                                    </td>
                                    <td style="font-size: 0.88rem">
                                        <div v-if="soc.soc_fecha" class="fw-bold text-dark">
                                            {{ soc.soc_fecha }}
                                            <div v-if="soc.soc_hora" class="small text-muted">{{ soc.soc_hora }}</div>
                                        </div>
                                        <span v-else class="badge bg-warning text-dark">Sin fecha</span>
                                    </td>
                                    <td
                                        style="
                                            font-size: 0.85rem;
                                            color: var(--text-muted);
                                        "
                                    >
                                        {{ soc.soc_lugar || 'Por definir' }}
                                    </td>
                                    <td>
                                        <template v-if="soc.tipo_socializacion?.tso_orden === 1">
                                            <span
                                                v-if="soc.soc_apto === true || soc.soc_apto == 1"
                                                class="bs ba"
                                                >Apto</span
                                            >
                                            <span
                                                v-else-if="soc.soc_apto === false || soc.soc_apto === 0 || soc.soc_apto === '0'"
                                                class="bs bd"
                                                >No Apto</span
                                            >
                                            <span
                                                v-else
                                                class="bs bsec"
                                                >Pendiente</span
                                            >
                                        </template>
                                        <template v-else>
                                            <span
                                                v-if="soc.soc_aprobado === true || soc.soc_aprobado == 1"
                                                class="bs ba"
                                                >Aprobado</span
                                            >
                                            <span
                                                v-else-if="soc.soc_aprobado === false || soc.soc_aprobado === 0 || soc.soc_aprobado === '0'"
                                                class="bs bd"
                                                >No Aprobado</span
                                            >
                                            <span
                                                v-else
                                                class="bs bsec"
                                                >Pendiente</span
                                            >
                                        </template>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-dark fw-bold me-1"
                                            @click="abrirDetalleEquipo(soc)"
                                            title="Opciones completas del equipo, expediente y descargas"
                                        >
                                            <i class="bi bi-folder2-open me-1"></i>Opciones
                                        </button>
                                        <!-- Enlace al Módulo de Evaluación de los 4 Actores (Solo habilitado si ya tiene fecha de socialización final) -->
                                        <template v-if="soc.tipo_socializacion?.tso_orden === 2">
                                            <!-- Si tiene token de jurado activo -->
                                            <template v-if="soc.soc_fecha && (soc.equipo?.proyecto_comunidad?.token_jurado || soc.equipo?.proyectoComunidad?.token_jurado)">
                                                <a
                                                    :href="'/evaluacion-jurado/' + (soc.equipo?.proyecto_comunidad?.token_jurado || soc.equipo?.proyectoComunidad?.token_jurado)"
                                                    target="_blank"
                                                    class="btn btn-sm btn-dark text-white fw-bold me-1"
                                                    title="Abrir panel de jurado (12 horas)"
                                                >
                                                    <i class="bi bi-box-arrow-up-right me-1"></i>Ver Link
                                                </a>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-danger text-white fw-bold me-1"
                                                    @click="copiarLinkJuradoSoc(soc.equipo?.proyecto_comunidad?.token_jurado || soc.equipo?.proyectoComunidad?.token_jurado)"
                                                    title="Copiar URL de 12 horas para enviar al Jurado"
                                                >
                                                    <i class="bi bi-clipboard me-1"></i>Copiar Link
                                                </button>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-secondary me-1"
                                                    @click="generarLinkDirecto(soc.equipo.equ_id)"
                                                    title="Regenerar enlace de evaluación (12 horas nuevas)"
                                                    :disabled="generandoLinkId === soc.equipo.equ_id"
                                                >
                                                    <i class="bi bi-arrow-repeat"></i>
                                                </button>
                                            </template>
                                            <button
                                                v-else-if="soc.soc_fecha && soc.equipo?.equ_id"
                                                type="button"
                                                class="btn btn-sm btn-outline-dark fw-bold me-1"
                                                @click="generarLinkDirecto(soc.equipo.equ_id)"
                                                title="Generar enlace temporal de 12 horas para el jurado"
                                                :disabled="generandoLinkId === soc.equipo.equ_id"
                                            >
                                                <span v-if="generandoLinkId === soc.equipo.equ_id" class="spinner-border spinner-border-sm me-1"></span>
                                                <i v-else class="bi bi-link-45deg me-1"></i>Generar Link Jurado
                                            </button>
                                            <a
                                                v-if="soc.soc_fecha && (soc.equipo?.proyecto_comunidad?.pco_id || soc.equipo?.proyectoComunidad?.pco_id)"
                                                :href="'/evaluaciones/recorrido/' + (soc.equipo?.proyecto_comunidad?.pco_id || soc.equipo?.proyectoComunidad?.pco_id)"
                                                class="btn btn-sm btn-outline-danger me-1"
                                                title="Módulo de Evaluación de los 4 Actores y Rúbricas Oficiales"
                                            >
                                                <i class="bi bi-award-fill me-1"></i>Evaluación 4 Actores
                                            </a>
                                            <a
                                                v-if="soc.equipo?.equ_id"
                                                :href="'/evaluaciones/planilla-pdf/' + soc.equipo.equ_id"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-primary me-1"
                                                title="Exportar Planilla de Evaluación en blanco (PDF) para respaldo físico"
                                            >
                                                <i class="bi bi-file-earmark-pdf me-1"></i>Planilla PDF
                                            </a>
                                            <span
                                                v-else-if="!soc.soc_fecha"
                                                class="badge bg-warning text-dark border px-2 py-1 me-1"
                                                title="Debe programar la fecha de socialización para habilitar la evaluación final"
                                            >
                                                <i class="bi bi-calendar-x me-1"></i>Fecha requerida
                                            </span>
                                        </template>

                                        <!-- Si ya tiene resultado: botón ver y candado -->
                                        <template v-if="estaEvaluada(soc)">
                                            <button
                                                class="btn btn-sm btn-outline-primary me-1"
                                                @click="abrirResultado(soc, true)"
                                                title="Ver resultado registrado (Bloqueado)"
                                            >
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <span
                                                class="badge bg-light text-muted border px-2 py-1"
                                                title="Resultado registrado - No modificable"
                                                style="vertical-align: middle;"
                                            >
                                                <i class="bi bi-lock-fill text-secondary"></i>
                                            </span>
                                        </template>
                                        <!-- Si no tiene resultado: registrar resultado -->
                                        <template v-else>
                                            <button
                                                class="btn btn-sm btn-outline-success"
                                                @click="abrirResultado(soc, false)"
                                                title="Registrar resultado"
                                            >
                                                <i
                                                    class="bi bi-clipboard-check"
                                                ></i>
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

        <div class="modal fade" id="mSoc" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>Nueva Socialización</h5>
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
                                <label class="form-label fw-semibold">Sección</label>
                                <SelectBuscable
                                    v-model="seccionSeleccionadaModal"
                                    :options="opcionesSeccionesModal"
                                    size="sm"
                                    placeholder="Todas las secciones..."
                                    @change="onSeccionModalChange"
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    Equipo <span class="text-danger">*</span>
                                </label>
                                <SelectBuscableEquipo
                                    v-model="form.soc_id_equ"
                                    :equipos="equiposModalFiltrados"
                                    :has-error="!!errores.soc_id_equ"
                                    placeholder="🔍 Escribe código, título o sección del equipo..."
                                />
                                <div
                                    v-if="errores.soc_id_equ"
                                    class="text-danger small mt-1"
                                >
                                    {{ errores.soc_id_equ }}
                                </div>
                            </div>

                            <div v-if="esEquipoSemestreImpar" class="alert alert-warning py-2 px-3 small mb-3">
                                <i class="bi bi-info-circle-fill me-1"></i>
                                <strong>Semestre Impar (Semestre {{ obtenerSemestreSeccion(equipoSeleccionado?.seccion) }} - Evaluación Cualitativa):</strong> La sección de este equipo cursa semestre impar. Solo corresponde <strong>Pre-Socialización</strong>. La Socialización Final con jurados se evalúa en el semestre par.
                            </div>

                            <!-- Alerta de Bloqueo de Pre-Socialización sin Último Punto de Control cumplido -->
                            <div v-if="moduloActivo === 'pre_socializacion' && equipoSeleccionado && !equipoSeleccionado.tiene_ultimo_punto_cumplido" class="alert alert-danger py-2 px-3 small mb-3">
                                <i class="bi bi-lock-fill me-1"></i>
                                <strong>Bloqueado:</strong> Este equipo aún no tiene un Punto de Control cumplido marcado como <strong>"Último punto de control del semestre"</strong>. Regístralo primero en Puntos de Control para habilitar la Pre-Socialización.
                            </div>

                            <!-- Alerta de Bloqueo si no tiene Pre-Socialización aprobada -->
                            <div v-if="moduloActivo === 'socializacion_final' && equipoSeleccionado && !equipoSeleccionado.tiene_presoc_aprobada" class="alert alert-danger py-2 px-3 small mb-3">
                                <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                <strong>Bloqueado:</strong> Este equipo no cuenta con la <strong>Pre-Socialización aprobada (Apto)</strong>. Es requisito obligatorio aprobar la Pre-Socialización antes de poder registrar o programar la Socialización Final.
                            </div>

                            <!-- Retroalimentación del Último Punto de Control para Pre-Socialización -->
                            <div v-if="moduloActivo === 'pre_socializacion' && equipoSeleccionado?.ultimo_punto_observaciones" class="alert alert-info py-2 px-3 small mb-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <strong class="d-flex align-items-center gap-1">
                                        <i class="bi bi-chat-left-text-fill text-primary"></i>
                                        Observaciones del Último Punto de Control:
                                    </strong>
                                    <button type="button" class="btn btn-xs btn-outline-primary py-0 px-2" @click="copiarObservacionesUltimoPunto">
                                        <i class="bi bi-clipboard-plus me-1"></i>Copiar a observaciones
                                    </button>
                                </div>
                                <div class="text-secondary fst-italic">"{{ equipoSeleccionado.ultimo_punto_observaciones }}"</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Tipo
                                    <span class="text-danger">*</span></label
                                >
                                <SelectBuscable
                                    v-model="form.soc_id_tso"
                                    :options="opcionesTiposSocializacion"
                                    :has-error="!!errores.soc_id_tso"
                                    placeholder="Seleccionar..."
                                />
                                <div
                                    v-if="errores.soc_id_tso"
                                    class="invalid-feedback d-block"
                                >
                                    {{ errores.soc_id_tso }}
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Fecha
                                        <span class="text-danger"
                                            >*</span
                                        ></label
                                    >
                                    <input
                                        v-model="form.soc_fecha"
                                        type="date"
                                        :min="fechaMinima"
                                        :max="$page.props.periodoActivo?.fecha_fin"
                                        class="form-control"
                                        :class="{
                                            'is-invalid': errores.soc_fecha,
                                        }"
                                    />
                                    <small v-if="$page.props.periodoActivo?.lapso_academico" class="text-muted d-block mt-1" style="font-size: 0.72rem;">
                                        Lapso activo: {{ $page.props.periodoActivo.fecha_inicio_formato }} - {{ $page.props.periodoActivo.fecha_fin_formato }}
                                    </small>
                                    <div
                                        v-if="errores.soc_fecha"
                                        class="invalid-feedback"
                                    >
                                        {{ errores.soc_fecha }}
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold"
                                        >Hora</label
                                    >
                                    <input
                                        v-model="form.soc_hora"
                                        type="time"
                                        class="form-control"
                                    />
                                </div>
                            </div>
                            <div class="mt-3 mb-3">
                                <label class="form-label fw-semibold"
                                    >Lugar</label
                                >
                                <input
                                    v-model="form.soc_lugar"
                                    type="text"
                                    class="form-control"
                                    placeholder="Ej: Aula 3 UPTP"
                                />
                            </div>
                            <div>
                                <label class="form-label fw-semibold"
                                    >Observaciones</label
                                >
                                <textarea
                                    v-model="form.soc_observaciones"
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
                                :disabled="loading || (moduloActivo === 'socializacion_final' && equipoSeleccionado && !equipoSeleccionado.tiene_presoc_aprobada)"
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

        <div class="modal fade" id="mRes" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>
                            {{ modoSoloLecturaRes ? 'Resultado de Socialización — ' : 'Registrar Resultado — ' }}
                            {{ socActual?.equipo?.equ_codigo }}
                        </h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form @submit.prevent="guardarResultado">
                        <div class="modal-body">
                            <div v-if="modoSoloLecturaRes" class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-3">
                                <i class="bi bi-lock-fill fs-5 text-secondary"></i>
                                <div>
                                    <strong>Resultado Registrado:</strong> La calificación de esta socialización ya fue asentada y no puede ser modificada.
                                </div>
                            </div>
                            <div
                                v-if="
                                    socActual?.tipo_socializacion?.tso_orden ===
                                    1
                                "
                                class="mb-3"
                            >
                                <label class="form-label fw-semibold"
                                    >¿El equipo es APTO para la socialización
                                    final?</label
                                >
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input
                                            type="radio"
                                            class="form-check-input"
                                            v-model="formRes.soc_apto"
                                            :value="true"
                                            :disabled="modoSoloLecturaRes"
                                            id="apto"
                                        /><label
                                            class="form-check-label fw-semibold"
                                            for="apto"
                                            style="color: #198754"
                                            >Sí, apto</label
                                        >
                                    </div>
                                    <div class="form-check">
                                        <input
                                            type="radio"
                                            class="form-check-input"
                                            v-model="formRes.soc_apto"
                                            :value="false"
                                            :disabled="modoSoloLecturaRes"
                                            id="noApto"
                                        /><label
                                            class="form-check-label fw-semibold"
                                            for="noApto"
                                            style="color: #dc3545"
                                            >No apto</label
                                        >
                                    </div>
                                    <div
                                        v-if="formRes.soc_apto === false"
                                        class="mt-3 p-3 rounded border border-danger bg-danger-subtle"
                                    >
                                        <label class="form-label fw-bold text-danger small mb-1">
                                            <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                            Observaciones Obligatorias por Dictamen "No Apto" <span class="text-danger">*</span>
                                        </label>
                                        <textarea
                                            v-model="formRes.soc_observaciones_no_apto"
                                            :disabled="modoSoloLecturaRes"
                                            class="form-control form-control-sm"
                                            rows="3"
                                            minlength="5"
                                            required
                                            placeholder="Describa las debilidades o requisitos que el equipo debe subsanar antes de habilitar la Transición Semestral..."
                                        ></textarea>
                                    </div>
                                </div>
                            </div>
                            <div v-else class="mb-3">
                                <label class="form-label fw-semibold"
                                    >¿El proyecto fue APROBADO?</label
                                >
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input
                                            type="radio"
                                            class="form-check-input"
                                            v-model="formRes.soc_aprobado"
                                            :value="true"
                                            :disabled="modoSoloLecturaRes"
                                            id="aprobado"
                                        /><label
                                            class="form-check-label fw-semibold"
                                            for="aprobado"
                                            style="color: #198754"
                                            >Sí, aprobado</label
                                        >
                                    </div>
                                    <div class="form-check">
                                        <input
                                            type="radio"
                                            class="form-check-input"
                                            v-model="formRes.soc_aprobado"
                                            :value="false"
                                            :disabled="modoSoloLecturaRes"
                                            id="noAprobado"
                                        /><label
                                            class="form-check-label fw-semibold"
                                            for="noAprobado"
                                            style="color: #dc3545"
                                            >No aprobado</label
                                        >
                                    </div>
                                </div>
                            </div>

                            <!-- Control de Asistencia y Avance Individual -->
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Control de Asistencia y Veredicto Individual</label>
                                <p class="text-muted small mb-2">
                                    Nota oficial UPTP: Si un estudiante no asiste a la evaluación reprueba automáticamente. Puede seleccionar quiénes avanzan individualmente.
                                </p>
                                <div v-if="!formRes.integrantes?.length" class="text-muted small">
                                    Sin integrantes asociados
                                </div>
                                <div
                                    v-for="(int, idx) in formRes.integrantes"
                                    :key="int.usu_id"
                                    class="p-2 mb-2 rounded border"
                                    style="border-color: var(--border) !important; background: var(--bg-body);"
                                >
                                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                        <div>
                                            <strong style="font-size: 0.92rem;">{{ int.nombre }}</strong>
                                            <span class="badge bg-light text-dark border ms-2 small">C.I. {{ int.cedula }}</span>
                                            <span v-if="int.es_lider" class="badge bg-danger ms-1 small">Líder</span>
                                        </div>
                                        <div class="d-flex align-items-center gap-3">
                                            <!-- Asistencia -->
                                            <div class="form-check form-switch mb-0">
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    :id="'soc_asist_' + int.usu_id"
                                                    :disabled="modoSoloLecturaRes"
                                                    v-model="int.asistio"
                                                    @change="() => { if (!int.asistio) int.avanza = false; }"
                                                />
                                                <label class="form-check-label small fw-bold" :for="'soc_asist_' + int.usu_id">
                                                    {{ int.asistio ? 'Asistió' : 'Inasistencia' }}
                                                </label>
                                            </div>

                                            <!-- Avanza (solo si asistió) -->
                                            <div class="form-check form-switch mb-0">
                                                <input
                                                    type="checkbox"
                                                    class="form-check-input"
                                                    :id="'soc_avanza_' + int.usu_id"
                                                    :disabled="modoSoloLecturaRes || !int.asistio"
                                                    v-model="int.avanza"
                                                />
                                                <label class="form-check-label small fw-bold" :class="int.avanza ? 'text-success' : 'text-danger'" :for="'soc_avanza_' + int.usu_id">
                                                    {{ int.avanza ? 'Avanza' : 'No avanza' }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div v-if="!int.asistio" class="mt-1 small text-danger fw-bold">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Inasistencia registrada: el estudiante reprueba de acuerdo a la normativa académica.
                                    </div>

                                    <!-- Observación / justificación individual -->
                                    <div class="mt-2">
                                        <input
                                            type="text"
                                            v-model="int.justificacion"
                                            :disabled="modoSoloLecturaRes"
                                            class="form-control form-control-sm"
                                            placeholder="Observaciones de desempeño o motivo de no avance..."
                                        />
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="form-label fw-semibold"
                                    >Observaciones Generales</label
                                >
                                <textarea
                                    v-model="formRes.soc_observaciones"
                                    :disabled="modoSoloLecturaRes"
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
                                {{ modoSoloLecturaRes ? 'Cerrar' : 'Cancelar' }}
                            </button>
                            <button
                                v-if="!modoSoloLecturaRes"
                                type="submit"
                                class="btn btn-danger fw-bold"
                                :disabled="loading || (socActual?.tipo_socializacion?.tso_orden === 1 ? (formRes.soc_apto === null || formRes.soc_apto === undefined) : (formRes.soc_aprobado === null || formRes.soc_aprobado === undefined))"
                            >
                                <span v-if="loading" class="spinner-border spinner-border-sm me-1"></span>
                                Registrar Resultado
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL DETALLE DEL EQUIPO DENTRO DE SOCIALIZACIÓN -->
        <div class="modal fade" id="mDetalleEquipoSoc" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white p-3 border-bottom border-danger border-3">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-danger fs-6 font-monospace">{{ socSeleccionadaDetalle?.equipo?.equ_codigo }}</span>
                            <h5 class="modal-title fw-bold text-white m-0">Detalle y Opciones del Equipo</h5>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4 text-dark" v-if="socSeleccionadaDetalle">
                        <!-- Identidad del Proyecto -->
                        <div class="p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                <div>
                                    <span class="badge bg-dark me-1">Sección {{ socSeleccionadaDetalle.equipo?.seccion?.sec_codigo }}</span>
                                    <span class="badge bg-secondary me-1">{{ socSeleccionadaDetalle.trayecto?.tra_nombre || socSeleccionadaDetalle.equipo?.seccion?.trayecto?.tra_nombre || 'Trayecto' }}</span>
                                    <span class="badge bg-light text-dark border">{{ socSeleccionadaDetalle.tipo_socializacion?.tso_nombre }}</span>
                                </div>
                                <span v-if="socSeleccionadaDetalle.soc_entrega_cd_informe" class="badge bg-success">
                                    <i class="bi bi-disc-fill me-1"></i>CD / Informe Entregado
                                </span>
                                <span v-else class="badge bg-warning text-dark">
                                    <i class="bi bi-disc me-1"></i>CD / Informe Pendiente
                                </span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1">
                                {{ socSeleccionadaDetalle.equipo?.equ_titulo || 'Proyecto Sociotecnológico' }}
                            </h5>
                            <div class="small text-muted mt-2 d-flex flex-wrap gap-3">
                                <div>
                                    <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                                    <strong>Comunidad:</strong> {{ socSeleccionadaDetalle.equipo?.proyectoComunidad?.comunidad?.com_nombre || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.comunidad?.com_nombre || 'No asignada' }}
                                </div>
                                <div>
                                    <i class="bi bi-pin-map text-danger me-1"></i>
                                    {{ socSeleccionadaDetalle.equipo?.proyectoComunidad?.comunidad?.com_direccion || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.comunidad?.com_sector || 'Dirección en proceso' }}
                                </div>
                            </div>
                        </div>

                        <!-- Convocatoria y Estado de Socialización -->
                        <div class="card border mb-3">
                            <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                                <span><i class="bi bi-calendar-event text-danger me-1"></i> Estado del Acto de Socialización</span>
                                <span v-if="socSeleccionadaDetalle.tipo_socializacion?.tso_orden === 1" class="badge" :class="socSeleccionadaDetalle.soc_apto ? 'bg-success' : (socSeleccionadaDetalle.soc_apto === false ? 'bg-danger' : 'bg-secondary')">
                                    {{ socSeleccionadaDetalle.soc_apto ? 'Apto' : (socSeleccionadaDetalle.soc_apto === false ? 'No Apto' : 'Pendiente') }}
                                </span>
                                <span v-else class="badge" :class="socSeleccionadaDetalle.soc_aprobado ? 'bg-success' : (socSeleccionadaDetalle.soc_aprobado === false ? 'bg-danger' : 'bg-secondary')">
                                    {{ socSeleccionadaDetalle.soc_aprobado ? 'Aprobado' : (socSeleccionadaDetalle.soc_aprobado === false ? 'No Aprobado' : 'Pendiente') }}
                                </span>
                            </div>
                            <div class="card-body p-3 small">
                                <div class="row g-2">
                                    <div class="col-sm-4">
                                        <div class="text-muted">Fecha del Acto:</div>
                                        <div class="fw-bold" :class="socSeleccionadaDetalle.soc_fecha ? 'text-dark' : 'text-danger'">
                                            {{ socSeleccionadaDetalle.soc_fecha || 'Sin fecha programada' }}
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="text-muted">Hora:</div>
                                        <div class="fw-bold text-dark">{{ socSeleccionadaDetalle.soc_hora || 'Por definir' }}</div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="text-muted">Lugar:</div>
                                        <div class="fw-bold text-dark">{{ socSeleccionadaDetalle.soc_lugar || 'Por definir' }}</div>
                                    </div>
                                </div>
                                <div v-if="socSeleccionadaDetalle.soc_observaciones" class="mt-2 p-2 bg-light rounded border">
                                    <strong>Observaciones registradas:</strong> {{ socSeleccionadaDetalle.soc_observaciones }}
                                </div>
                            </div>
                        </div>

                        <!-- Link Temporal de 12 Horas para Jurados y Responsable de la Comunidad -->
                        <div class="card border mb-3">
                            <div class="card-header bg-dark text-white p-2 px-3 d-flex justify-content-between align-items-center">
                                <span class="fw-bold small text-uppercase">
                                    <i class="bi bi-link-45deg text-warning me-1"></i> Link Temporal para Jurado (Vigencia 12 Horas)
                                </span>
                                <span class="badge bg-warning text-dark small">Exclusivo Jurado / Resp. Comunidad</span>
                            </div>
                            <div class="card-body p-3">
                                <!-- Si es pre-socialización -->
                                <div v-if="socSeleccionadaDetalle.tipo_socializacion?.tso_orden === 1" class="text-muted small">
                                    <i class="bi bi-info-circle text-primary me-1"></i>
                                    Este equipo cursa Semestre Impar (Pre-Socialización cualitativa). Los enlaces temporales de 12 horas con rúbricas cuantitativas se generan durante la Socialización Final (Semestre Par).
                                </div>

                                <!-- Si es socialización final pero no tiene fecha programada -->
                                <div v-else-if="!socSeleccionadaDetalle.soc_fecha" class="alert alert-warning py-2 px-3 mb-0 small">
                                    <i class="bi bi-calendar-x-fill me-1"></i>
                                    <strong>Atención:</strong> Para generar y compartir el enlace de evaluación con los jurados, primero debe registrarse la fecha y lugar de la defensa final.
                                </div>

                                <!-- Si es socialización final y tiene fecha -->
                                <div v-else>
                                    <template v-if="tokenJuradoActivo(socSeleccionadaDetalle)">
                                        <label class="form-label fw-bold small text-muted mb-1">Enlace de Acceso Directo:</label>
                                        <div class="input-group input-group-sm mb-2">
                                            <input
                                                type="text"
                                                class="form-control font-monospace bg-light"
                                                readonly
                                                :value="obtenerUrlJurado(socSeleccionadaDetalle)"
                                            />
                                            <button
                                                class="btn fw-bold text-white"
                                                :class="copiadoExitoso ? 'btn-success' : 'btn-danger'"
                                                type="button"
                                                @click="copiarLinkModal(tokenJuradoActivo(socSeleccionadaDetalle))"
                                            >
                                                <i class="bi" :class="copiadoExitoso ? 'bi-check-lg' : 'bi-clipboard'"></i>
                                                {{ copiadoExitoso ? '¡Copiado!' : 'Copiar Link' }}
                                            </button>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <a
                                                :href="obtenerUrlJurado(socSeleccionadaDetalle)"
                                                target="_blank"
                                                class="btn btn-sm btn-dark text-white fw-bold"
                                            >
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Abrir Panel de Jurado
                                            </a>
                                            <a
                                                v-if="socSeleccionadaDetalle.equipo?.equ_id"
                                                :href="`/evaluaciones/planilla-pdf/${socSeleccionadaDetalle.equipo.equ_id}`"
                                                target="_blank"
                                                class="btn btn-sm btn-outline-primary fw-bold"
                                            >
                                                <i class="bi bi-file-earmark-pdf me-1"></i>Exportar Planilla de Evaluación (PDF)
                                            </a>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-secondary"
                                                @click="generarLinkDesdeModal(socSeleccionadaDetalle.equipo.equ_id)"
                                                :disabled="generandoLinkId === socSeleccionadaDetalle.equipo.equ_id"
                                            >
                                                <span v-if="generandoLinkId === socSeleccionadaDetalle.equipo.equ_id" class="spinner-border spinner-border-sm me-1"></span>
                                                <i v-else class="bi bi-arrow-repeat me-1"></i>Regenerar Link (Nuevas 12h)
                                            </button>
                                        </div>
                                    </template>
                                    <template v-else>
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                                            <span class="small text-muted">Aún no se ha emitido un enlace de 12 horas para este proyecto.</span>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-danger fw-bold"
                                                @click="generarLinkDesdeModal(socSeleccionadaDetalle.equipo.equ_id)"
                                                :disabled="generandoLinkId === socSeleccionadaDetalle.equipo.equ_id"
                                            >
                                                <span v-if="generandoLinkId === socSeleccionadaDetalle.equipo.equ_id" class="spinner-border spinner-border-sm me-1"></span>
                                                <i v-else class="bi bi-magic me-1"></i>Generar Enlace de 12 Horas
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Integrantes del Equipo -->
                        <div class="card border mb-3">
                            <div class="card-header bg-white fw-bold small text-muted text-uppercase">
                                <i class="bi bi-people-fill text-danger me-1"></i> Integrantes del Equipo ({{ socSeleccionadaDetalle.equipo?.integrantes?.length || 0 }})
                            </div>
                            <ul class="list-group list-group-flush small">
                                <li
                                    v-for="integ in socSeleccionadaDetalle.equipo?.integrantes"
                                    :key="integ.ein_id || integ.ein_id_usu"
                                    class="list-group-item d-flex justify-content-between align-items-center py-2"
                                >
                                    <div>
                                        <strong class="text-dark">{{ integ.usuario?.usu_primer_nombre }} {{ integ.usuario?.usu_primer_apellido }}</strong>
                                        <span class="text-muted font-monospace ms-2">C.I. {{ integ.usuario?.usu_cedula }}</span>
                                    </div>
                                    <span v-if="integ.ein_es_lider" class="badge bg-danger rounded-pill">Líder</span>
                                    <span v-else class="badge bg-light text-muted border rounded-pill">Integrante</span>
                                </li>
                            </ul>
                        </div>

                        <!-- Accesos y Descargas Oficiales -->
                        <div class="card border bg-light">
                            <div class="card-header bg-white fw-bold small text-dark">
                                <i class="bi bi-download text-danger me-1"></i> Navegación Directa y Descargas Oficiales UPTP
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-2">
                                    <!-- Expediente -->
                                    <div class="col-sm-6">
                                        <a
                                            :href="'/profesor/equipos/' + socSeleccionadaDetalle.equipo?.equ_id + '/expediente'"
                                            class="btn btn-outline-dark btn-sm w-100 fw-bold text-start d-flex align-items-center justify-content-between p-2"
                                        >
                                            <span><i class="bi bi-file-earmark-person-fill text-danger me-1"></i>Expediente de Calificaciones</span>
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </div>

                                    <!-- 4 Actores Rúbricas -->
                                    <div class="col-sm-6" v-if="socSeleccionadaDetalle.equipo?.proyectoComunidad?.pco_id || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.pco_id">
                                        <a
                                            :href="'/evaluaciones/recorrido/' + (socSeleccionadaDetalle.equipo?.proyectoComunidad?.pco_id || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.pco_id)"
                                            class="btn btn-outline-danger btn-sm w-100 fw-bold text-start d-flex align-items-center justify-content-between p-2"
                                        >
                                            <span><i class="bi bi-award-fill me-1"></i>Evaluación 4 Actores (Rúbricas)</span>
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </div>

                                    <!-- Acta Final PDF -->
                                    <div class="col-sm-4" v-if="socSeleccionadaDetalle.equipo?.proyectoComunidad?.pco_id || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.pco_id">
                                        <a
                                            :href="'/evaluaciones/pdf/' + (socSeleccionadaDetalle.equipo?.proyectoComunidad?.pco_id || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.pco_id)"
                                            target="_blank"
                                            class="btn btn-danger btn-sm w-100 fw-bold p-2 text-truncate"
                                            title="Descargar Acta Final Oficial PDF con Hash SHA-256"
                                        >
                                            <i class="bi bi-file-earmark-pdf-fill me-1"></i>Acta Final PDF
                                        </a>
                                    </div>

                                    <!-- Planilla Vinculación Excel -->
                                    <div class="col-sm-6" v-if="socSeleccionadaDetalle.equipo?.proyectoComunidad?.pco_id || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.pco_id">
                                        <a
                                            :href="'/excel/vinculacion/' + (socSeleccionadaDetalle.equipo?.proyectoComunidad?.pco_id || socSeleccionadaDetalle.equipo?.proyecto_comunidad?.pco_id)"
                                            target="_blank"
                                            class="btn btn-success btn-sm w-100 fw-bold p-2 text-truncate"
                                            title="Descargar Planilla de Vinculación Excel"
                                        >
                                            <i class="bi bi-file-earmark-excel-fill me-1"></i>Planilla Vinc. Excel
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light p-3 border-top">
                        <button type="button" class="btn btn-secondary fw-bold rounded-pill px-4" data-bs-dismiss="modal">
                            Cerrar
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
import { Link } from "@inertiajs/vue3";
import SelectBuscableEquipo from "@/Components/SelectBuscableEquipo.vue";
import SelectBuscable from "@/Components/SelectBuscable.vue";
import SidebarProfesor from "@/Components/SidebarProfesor.vue";

const props = defineProps({
    socializaciones: { type: [Array, Object], default: () => [] },
    equipos: { type: Array, default: () => [] },
    secciones: { type: Array, default: () => [] },
    tiposSocializacion: { type: Array, default: () => [] },
    tiposJurado: { type: Array, default: () => [] },
});

const listaSocializaciones = computed(() => {
    if (Array.isArray(props.socializaciones)) return props.socializaciones;
    if (props.socializaciones?.data && Array.isArray(props.socializaciones.data)) return props.socializaciones.data;
    return [];
});

const opcionesSeccionesModal = computed(() => [
    { value: "", label: "Todas las secciones..." },
    ...(props.secciones || []).map((sec) => ({
        value: sec.sec_id,
        label: `Sección ${sec.sec_codigo}`,
    })),
]);

const opcionesTiposSocializacion = computed(() => [
    { value: "", label: "Seleccionar..." },
    ...(props.tiposSocializacion || []).map((tipo) => ({
        value: tipo.tso_id,
        label: `${tipo.tso_nombre}${esEquipoSemestreImpar.value && tipo.tso_orden === 2 ? " (No aplica en semestre impar)" : ""}`,
        disabled: Boolean(esEquipoSemestreImpar.value && tipo.tso_orden === 2),
    })),
]);

const equipoSeleccionado = computed(() => {
    return props.equipos?.find(e => e.equ_id == form.value.soc_id_equ) || null;
});

const obtenerSemestreSeccion = (sec) => {
    if (!sec) return 1;
    // 1. Regla oficial UPTP: El primer número de la sección es el semestre del equipo (ej: 431 -> Semestre 4)
    if (sec.sec_codigo) {
        const codigo = String(sec.sec_codigo).trim();
        const match = codigo.match(/\d/);
        if (match) {
            const digito = parseInt(match[0]);
            if (digito >= 1 && digito <= 8) return digito;
        }
    }
    // 2. Si sec_semestre viene definido en BD
    if (sec.sec_semestre && !isNaN(parseInt(sec.sec_semestre))) {
        return parseInt(sec.sec_semestre);
    }
    // 3. Fallback
    return sec.sec_id_tra ? parseInt(sec.sec_id_tra) : 1;
};

const esEquipoSemestreImpar = computed(() => {
    const sec = equipoSeleccionado.value?.seccion;
    if (!sec) return false;
    const sem = obtenerSemestreSeccion(sec);
    return sem % 2 !== 0;
});

const generandoLinkId = ref(null);
const generarLinkDirecto = (equId) => {
    generandoLinkId.value = equId;
    router.post(`/profesor/equipos/${equId}/generar-link-jurado`, {}, {
        preserveScroll: true,
        onFinish: () => {
            generandoLinkId.value = null;
        }
    });
};

const copiarLinkJuradoSoc = (token) => {
    if (!token) return;
    const url = window.location.origin + '/evaluacion-jurado/' + token;
    navigator.clipboard.writeText(url);
    alert('¡Enlace copiado exitosamente al portapapeles!\n\n' + url + '\n\nComparte este link con el Jurado Evaluador y Responsable de la Comunidad (Vigencia: 12 Horas).');
};

let mDetalle = null;
const socSeleccionadaDetalle = ref(null);
const copiadoExitoso = ref(false);

const abrirDetalleEquipo = (soc) => {
    socSeleccionadaDetalle.value = soc;
    mDetalle = mDetalle || new Modal(document.getElementById("mDetalleEquipoSoc"));
    mDetalle.show();
};

const tokenJuradoActivo = (soc) => {
    return (
        soc?.equipo?.proyectoComunidad?.token_jurado ||
        soc?.equipo?.proyecto_comunidad?.token_jurado ||
        null
    );
};

const obtenerUrlJurado = (soc) => {
    const token = tokenJuradoActivo(soc);
    if (!token) return "";
    return window.location.origin + "/evaluacion-jurado/" + token;
};

const copiarLinkModal = (token) => {
    if (!token) return;
    const url = window.location.origin + "/evaluacion-jurado/" + token;
    navigator.clipboard.writeText(url);
    copiadoExitoso.value = true;
    setTimeout(() => {
        copiadoExitoso.value = false;
    }, 3000);
};

const generarLinkDesdeModal = (equId) => {
    generandoLinkId.value = equId;
    router.post(
        `/profesor/equipos/${equId}/generar-link-jurado`,
        {},
        {
            preserveScroll: true,
            onSuccess: (page) => {
                if (
                    page.props.flash?.link_jurado &&
                    socSeleccionadaDetalle.value?.equipo
                ) {
                    const parts = page.props.flash.link_jurado.split("/");
                    const tok = parts[parts.length - 1];
                    const p =
                        socSeleccionadaDetalle.value.equipo.proyectoComunidad ||
                        socSeleccionadaDetalle.value.equipo.proyecto_comunidad;
                    if (p) {
                        p.token_jurado = tok;
                    }
                }
            },
            onFinish: () => {
                generandoLinkId.value = null;
            },
        },
    );
};
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
const seccionSeleccionadaModal = ref("");
const moduloActivo = ref("pre_socializacion"); // "pre_socializacion" | "socializacion_final"

const totalPreSoc = computed(() =>
    (props.socializaciones || []).filter(
        (s) => s.tipo_socializacion?.tso_orden === 1 || s.soc_id_tso == 1
    ).length
);
const totalSocFinal = computed(() =>
    (props.socializaciones || []).filter(
        (s) => s.tipo_socializacion?.tso_orden === 2 || s.soc_id_tso == 2
    ).length
);

const fechaHoy = computed(() => new Date().toISOString().split("T")[0]);
const fechaMinima = computed(() => {
    const periMin = page.props.periodoActivo?.fecha_inicio;
    let base = fechaHoy.value;
    if (periMin && periMin > base) {
        base = periMin;
    }
    const tipo = props.tiposSocializacion?.find(
        (t) => t.tso_id == form.value.soc_id_tso
    );
    if (tipo && tipo.tso_orden === 2 && form.value.soc_id_equ) {
        const preSoc = props.socializaciones?.find(
            (s) =>
                s.soc_id_equ == form.value.soc_id_equ &&
                (s.tipo_socializacion?.tso_orden === 1 || s.soc_id_tso == 1) &&
                s.soc_apto
        );
        if (preSoc && preSoc.soc_fecha && preSoc.soc_fecha > base) {
            return preSoc.soc_fecha;
        }
    }
    return base;
});

const onSeccionModalChange = () => {
    if (seccionSeleccionadaModal.value && form.value.soc_id_equ) {
        const eq = props.equipos?.find((e) => e.equ_id == form.value.soc_id_equ);
        if (eq && eq.equ_id_sec != seccionSeleccionadaModal.value) {
            form.value.soc_id_equ = "";
        }
    }
};

const equiposModalFiltrados = computed(() => {
    let list = props.equipos || [];
    if (seccionSeleccionadaModal.value) {
        list = list.filter((e) => e.equ_id_sec == seccionSeleccionadaModal.value);
    }
    return list;
});

const form = ref({
    soc_id_equ: "",
    soc_id_tso: "",
    soc_fecha: "",
    soc_hora: "",
    soc_lugar: "",
    soc_observaciones: "",
});
const errores = ref({});
const loading = ref(false);
const formRes = ref({
    soc_apto: null,
    soc_aprobado: null,
    soc_observaciones: "",
    soc_observaciones_no_apto: "",
});
const socActual = ref(null);
let mSoc = null;
let mRes = null;

const abrirModal = () => {
    seccionSeleccionadaModal.value = "";
    const tsoId = moduloActivo.value === "pre_socializacion"
        ? (props.tiposSocializacion?.find(t => t.tso_orden === 1)?.tso_id || 1)
        : (props.tiposSocializacion?.find(t => t.tso_orden === 2)?.tso_id || 2);

    form.value = {
        soc_id_equ: "",
        soc_id_tso: tsoId,
        soc_fecha: "",
        soc_hora: "",
        soc_lugar: "",
        soc_observaciones: "",
    };
    errores.value = {};
    mSoc = mSoc || new Modal(document.getElementById("mSoc"));
    mSoc.show();
};

const copiarObservacionesUltimoPunto = () => {
    if (equipoSeleccionado.value?.ultimo_punto_observaciones) {
        const obs = "Obs. del último punto de control: " + equipoSeleccionado.value.ultimo_punto_observaciones;
        form.value.soc_observaciones = form.value.soc_observaciones
            ? form.value.soc_observaciones + "\n" + obs
            : obs;
    }
};

const guardar = () => {
    if (moduloActivo.value === "pre_socializacion" && equipoSeleccionado.value && !equipoSeleccionado.value.tiene_ultimo_punto_cumplido) {
        errores.value = { soc_id_equ: "Bloqueado: El equipo debe tener un punto de control cumplido marcado como último punto de control del semestre." };
        return;
    }
    if (moduloActivo.value === "socializacion_final" && equipoSeleccionado.value && !equipoSeleccionado.value.tiene_presoc_aprobada) {
        errores.value = { soc_id_equ: "Bloqueado: El equipo debe tener aprobada la Pre-Socialización (Apto) para programar la Socialización Final." };
        return;
    }
    loading.value = true;
    router.post("/profesor/socializaciones", form.value, {
        onError: (e) => {
            errores.value = e;
            loading.value = false;
        },
        onSuccess: () => {
            loading.value = false;
            mSoc?.hide();
        },
    });
};
const modoSoloLecturaRes = ref(false);
const tabSocializaciones = ref("pendientes"); // "pendientes" | "archivadas"

const estaEvaluada = (soc) => {
    if (!soc) return false;
    if (soc.tipo_socializacion?.tso_orden === 1 || soc.soc_id_tso == 1) {
        return soc.soc_apto !== null && soc.soc_apto !== undefined;
    }
    return soc.soc_aprobado !== null && soc.soc_aprobado !== undefined;
};

const listaPorModulo = computed(() => {
    let list = listaSocializaciones.value;
    if (moduloActivo.value === "pre_socializacion") {
        return list.filter((s) => s.tipo_socializacion?.tso_orden === 1 || s.soc_id_tso == 1);
    } else {
        return list.filter((s) => s.tipo_socializacion?.tso_orden === 2 || s.soc_id_tso == 2);
    }
});

const totalPendientes = computed(
    () => listaPorModulo.value.filter((s) => !estaEvaluada(s)).length,
);

const totalArchivadas = computed(
    () => listaPorModulo.value.filter((s) => estaEvaluada(s)).length,
);

const socializacionesFiltradas = computed(() => {
    let list = listaPorModulo.value;
    if (tabSocializaciones.value === "pendientes") {
        list = list.filter((s) => !estaEvaluada(s));
    } else {
        list = list.filter((s) => estaEvaluada(s));
    }
    return list;
});

const paginaActual = ref(1);
const porPagina = 3;
watch([tabSocializaciones, moduloActivo], () => {
    paginaActual.value = 1;
});
const totalPaginas = computed(() => Math.ceil(socializacionesFiltradas.value.length / porPagina));
const desde = computed(() => (paginaActual.value - 1) * porPagina);
const hasta = computed(() => desde.value + porPagina);
const socializacionesPaginadas = computed(() => socializacionesFiltradas.value.slice(desde.value, hasta.value));
const textoPaginacion = computed(() => {
    const total = socializacionesFiltradas.value?.length || 0;
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

const abrirResultado = (soc, soloLectura = false) => {
    socActual.value = soc;
    modoSoloLecturaRes.value = soloLectura || estaEvaluada(soc);

    const ints = soc.equipo?.integrantes || [];
    const prevInts = soc.integrantes || [];
    const prevMap = {};
    prevInts.forEach(pi => {
        prevMap[pi.iso_id_usu] = {
            asistio: pi.iso_asistio !== undefined && pi.iso_asistio !== null ? Boolean(pi.iso_asistio) : true,
            avanza: Boolean(pi.iso_avanza),
            justificacion: pi.iso_justificacion || ""
        };
    });

    formRes.value = {
        soc_apto: soc.soc_apto,
        soc_aprobado: soc.soc_aprobado,
        soc_observaciones: soc.soc_observaciones || "",
        soc_observaciones_no_apto: soc.soc_observaciones_no_apto || "",
        integrantes: ints.map(i => ({
            usu_id: i.ein_id_usu,
            nombre: `${i.usuario?.usu_primer_nombre || ''} ${i.usuario?.usu_primer_apellido || ''}`,
            cedula: i.usuario?.usu_cedula || '',
            es_lider: Boolean(i.ein_es_lider),
            asistio: prevMap[i.ein_id_usu]?.asistio ?? true,
            avanza: prevMap[i.ein_id_usu]?.avanza ?? true,
            justificacion: prevMap[i.ein_id_usu]?.justificacion ?? "",
        })),
    };
    mRes = mRes || new Modal(document.getElementById("mRes"));
    mRes.show();
};
const guardarResultado = () => {
    if (!socActual.value || modoSoloLecturaRes.value || estaEvaluada(socActual.value)) return;
    if (socActual.value?.tipo_socializacion?.tso_orden === 1 && (formRes.value.soc_apto === null || formRes.value.soc_apto === undefined)) {
        alert("Debes indicar si el equipo es APTO o NO APTO antes de registrar el resultado.");
        return;
    }
    if (socActual.value?.tipo_socializacion?.tso_orden === 1 && formRes.value.soc_apto === false && (!formRes.value.soc_observaciones_no_apto || formRes.value.soc_observaciones_no_apto.trim().length < 5)) {
        alert("Al dictaminar 'No Apto' es obligatorio ingresar observaciones de subsanación (mínimo 5 caracteres).");
        return;
    }
    if (socActual.value?.tipo_socializacion?.tso_orden !== 1 && (formRes.value.soc_aprobado === null || formRes.value.soc_aprobado === undefined)) {
        alert("Debes indicar si el proyecto fue APROBADO o NO APROBADO antes de registrar el resultado.");
        return;
    }
    loading.value = true;
    router.post(
        `/profesor/socializaciones/${socActual.value.soc_id}/resultado`,
        formRes.value,
        {
            preserveScroll: true,
            onSuccess: () => {
                loading.value = false;
                tabSocializaciones.value = "archivadas";
                mRes?.hide();
                socActual.value = null;
                formRes.value = {
                    soc_apto: null,
                    soc_aprobado: null,
                    soc_observaciones: "",
                };
            },
            onError: (e) => {
                loading.value = false;
                errores.value = e;
            },
        },
    );
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
