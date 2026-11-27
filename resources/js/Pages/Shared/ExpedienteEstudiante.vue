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
            v-if="esCoordinador"
            :sidebar-open="sidebarOpen"
            @close="sidebarOpen = false"
        />
        <SidebarProfesor
            v-else
            :sidebar-open="sidebarOpen"
            @close="sidebarOpen = false"
        />

        <!-- MAIN CONTAINER -->
        <div class="main-prof">
            <!-- TOPBAR -->
            <div class="topbar-prof">
                <div class="d-flex align-items-center gap-3">
                    <button
                        class="btn btn-outline-danger btn-sm toggle-btn px-2"
                        @click="sidebarOpen = true"
                    >
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div class="topbar-title">
                        <i class="bi bi-person-badge me-2 text-danger d-none d-sm-inline"></i>
                        <span>Expediente del Estudiante</span>
                        <span v-if="estudiante" class="text-muted fw-normal fs-6 ms-2 d-none d-md-inline">
                            &bull; {{ estudiante.usu_primer_nombre }} {{ estudiante.usu_primer_apellido }}
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <Link
                        v-if="estudiante"
                        :href="`${baseUrl}/estudiantes/buscar`"
                        class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-none d-sm-inline-flex align-items-center gap-1"
                        title="Ver lista de estudiantes"
                    >
                        <i class="bi bi-grid me-1"></i> Directorio
                    </Link>
                    <button class="topbar-btn" @click="toggleTema" title="Cambiar tema">
                        <i
                            class="bi"
                            :class="tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'"
                        ></i>
                    </button>
                </div>
            </div>

            <!-- CONTENIDO PRINCIPAL -->
            <div class="content-prof">

                <!-- CASO 1: DIRECTORIO DE ESTUDIANTES (SIN ESTUDIANTE SELECCIONADO) -->
                <div v-if="!estudiante">
                    <!-- ENCABEZADO Y FILTROS -->
                    <div class="card border-0 shadow-sm mb-4" style="background: var(--bg-card); border-radius: 16px;">
                        <div class="card-body p-3 p-md-4">
                            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                                <div>
                                    <h4 class="fw-bold mb-1" style="color: var(--text-main);">
                                        <i class="bi bi-people-fill text-danger me-2"></i>
                                        Directorio de Estudiantes
                                    </h4>
                                    <p class="text-muted small mb-0">
                                        {{ esCoordinador ? 'Todos los estudiantes registrados en el sistema.' : 'Estudiantes inscritos en tus secciones asignadas de Proyecto.' }}
                                    </p>
                                </div>
                                <div class="badge bg-danger fs-6 px-3 py-2 rounded-pill shadow-sm">
                                    {{ estudiantesFiltrados.length }} estudiante{{ estudiantesFiltrados.length === 1 ? '' : 's' }}
                                </div>
                            </div>

                            <!-- BARRA DE BÚSQUEDA Y FILTROS -->
                            <div class="row g-2 align-items-center">
                                <div class="col-12 col-md-5">
                                    <div class="input-group">
                                        <span class="input-group-text border-end-0" style="background: var(--bg-th); color: var(--text-muted); border-color: var(--border);">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input
                                            v-model="filtroTexto"
                                            type="text"
                                            class="form-control border-start-0"
                                            style="background: var(--bg-card); color: var(--text-main); border-color: var(--border);"
                                            placeholder="Buscar por Cédula, Nombre o Apellido..."
                                        />
                                        <button
                                            v-if="filtroTexto"
                                            class="btn btn-outline-secondary border-start-0"
                                            type="button"
                                            @click="filtroTexto = ''"
                                        >
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </div>
                                </div>

                                <!-- Filtro por Sección -->
                                <div class="col-12 col-sm-6 col-md-4">
                                    <SelectBuscable
                                        v-model="filtroSeccion"
                                        :options="opcionesSeccionesExpEst"
                                        placeholder="Todas las secciones asignadas"
                                    />
                                </div>

                                <!-- Filtro por Estado de Equipo -->
                                <div class="col-12 col-sm-6 col-md-3">
                                    <SelectBuscable
                                        v-model="filtroEquipo"
                                        :options="opcionesFiltroEquipoEst"
                                        placeholder="Todos los equipos"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- GRILLA DE ESTUDIANTES -->
                    <div v-if="estudiantesFiltrados.length > 0" class="row g-3">
                        <div
                            v-for="est in estudiantesFiltrados"
                            :key="est.usu_id"
                            class="col-12 col-md-6 col-xl-4"
                        >
                            <div class="card h-100 border-0 shadow-sm student-card" style="background: var(--bg-card); border-radius: 14px; transition: transform 0.2s, box-shadow 0.2s;">
                                <div class="card-body p-3 d-flex flex-column justify-content-between">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div
                                            style="
                                                width: 52px;
                                                height: 52px;
                                                border-radius: 50%;
                                                background: linear-gradient(135deg, #dc3545, #a71d2a);
                                                color: #fff;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                font-size: 1.25rem;
                                                font-weight: 800;
                                                flex-shrink: 0;
                                                box-shadow: 0 4px 10px rgba(220, 53, 69, 0.25);
                                            "
                                        >
                                            {{ obtenerIniciales(est.usu_primer_nombre, est.usu_primer_apellido) }}
                                        </div>
                                        <div class="flex-grow-1 overflow-hidden">
                                            <h6 class="fw-bold mb-1 text-truncate" style="color: var(--text-main);" :title="`${est.usu_primer_nombre} ${est.usu_primer_apellido}`">
                                                {{ est.usu_primer_nombre }} {{ est.usu_primer_apellido }}
                                            </h6>
                                            <div class="d-flex align-items-center gap-1 flex-wrap">
                                                <span class="badge bg-light text-dark border small">
                                                    C.I. {{ est.usu_cedula }}
                                                </span>
                                                <span v-if="est.seccion_codigo" class="badge bg-danger-subtle text-danger border border-danger-subtle small">
                                                    Sec. {{ est.seccion_codigo }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-2 rounded mb-3" style="background: var(--bg-th); font-size: 0.8rem; border: 1px solid var(--border);">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-muted"><i class="bi bi-diagram-3 me-1"></i> Equipo:</span>
                                            <span v-if="est.equipo_codigo" class="fw-bold text-danger">
                                                {{ est.equipo_codigo }}
                                            </span>
                                            <span v-else class="badge bg-warning-subtle text-warning border border-warning-subtle py-0">
                                                Sin equipo
                                            </span>
                                        </div>
                                        <div v-if="est.trayecto_nombre" class="d-flex justify-content-between align-items-center text-muted">
                                            <span><i class="bi bi-mortarboard me-1"></i> Trayecto:</span>
                                            <span>{{ est.trayecto_nombre }}</span>
                                        </div>
                                    </div>

                                    <Link
                                        :href="`${baseUrl}/estudiantes/${est.usu_id}/expediente`"
                                        class="btn btn-sm btn-outline-danger w-100 fw-bold rounded-pill py-2 d-flex align-items-center justify-content-center gap-1"
                                    >
                                        <i class="bi bi-person-badge"></i>
                                        <span>Ver expediente completo</span>
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ESTADO VACÍO -->
                    <div v-else class="card border-0 shadow-sm p-5 text-center my-4" style="background: var(--bg-card); border-radius: 16px;">
                        <i class="bi bi-search text-danger" style="font-size: 3rem;"></i>
                        <h5 class="fw-bold mt-3" style="color: var(--text-main);">No se encontraron estudiantes</h5>
                        <p class="text-muted small mx-auto" style="max-width: 480px;">
                            No hay ningún estudiante que coincida con los criterios de búsqueda o filtros seleccionados.
                        </p>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-outline-danger rounded-pill px-4" @click="limpiarFiltros">
                                Limpiar filtros de búsqueda
                            </button>
                        </div>
                    </div>
                </div>

                <!-- CASO 2: EXPEDIENTE COMPLETO DEL ESTUDIANTE SELECCIONADO -->
                <div v-else>
                    <!-- BARRA SUPERIOR DE ACCIONES Y SELECTOR RÁPIDO -->
                    <div class="card border-0 shadow-sm mb-3" style="background: var(--bg-card); border-radius: 14px;">
                        <div class="card-body p-2 p-md-3">
                            <div class="row align-items-center g-2">
                                <div class="col-12 col-md-auto">
                                    <Link
                                        :href="`${baseUrl}/estudiantes/buscar`"
                                        class="btn btn-sm btn-outline-secondary rounded-pill px-3 d-inline-flex align-items-center gap-1 w-100 w-md-auto"
                                    >
                                        <i class="bi bi-arrow-left"></i>
                                        <span>Directorio de Estudiantes</span>
                                    </Link>
                                </div>
                                <div class="col-12 col-md">
                                    <div class="position-relative">
                                        <div class="input-group">
                                            <span class="input-group-text border-end-0" style="background: var(--bg-th); color: var(--text-muted); border-color: var(--border);">
                                                <i class="bi bi-search"></i>
                                            </span>
                                            <input
                                                v-model="busquedaInput"
                                                type="text"
                                                class="form-control border-start-0"
                                                style="background: var(--bg-card); color: var(--text-main); border-color: var(--border);"
                                                placeholder="Buscar y cambiar a otro estudiante por Cédula o Nombre..."
                                                @input="buscarEstudiantes"
                                                @focus="mostrarDropdownResultados = true"
                                            />
                                            <button
                                                v-if="busquedaInput"
                                                class="btn btn-outline-secondary border-start-0"
                                                type="button"
                                                @click="busquedaInput = ''; resultadosBusqueda = []"
                                            >
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        </div>

                                        <!-- Resultados flotantes de búsqueda rápida -->
                                        <div
                                            v-if="resultadosBusqueda.length > 0 && mostrarDropdownResultados"
                                            class="list-group position-absolute w-100 shadow-lg mt-1 border rounded-3 overflow-hidden"
                                            style="z-index: 1050; max-height: 280px; overflow-y: auto; background: var(--bg-card);"
                                        >
                                            <Link
                                                v-for="est in resultadosBusqueda"
                                                :key="est.usu_id"
                                                :href="`${baseUrl}/estudiantes/${est.usu_id}/expediente`"
                                                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center p-2 px-3"
                                                style="background: var(--bg-card); color: var(--text-main); border-color: var(--border);"
                                                @click="mostrarDropdownResultados = false"
                                            >
                                                <div>
                                                    <strong class="d-block">{{ est.usu_primer_nombre }} {{ est.usu_primer_apellido }}</strong>
                                                    <span class="badge bg-light text-dark border small me-2">C.I. {{ est.usu_cedula }}</span>
                                                    <span v-if="est.seccion_codigo" class="badge bg-danger-subtle text-danger small">Sec. {{ est.seccion_codigo }}</span>
                                                </div>
                                                <span class="btn btn-sm btn-outline-danger py-0 px-2 rounded-pill small">
                                                    Abrir expediente
                                                </span>
                                            </Link>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- FICHA PRINCIPAL DEL ESTUDIANTE -->
                    <div class="card border-0 shadow-sm mb-4" style="background: var(--bg-card); border-radius: 16px;">
                        <div class="card-body p-3 p-md-4">
                            <div class="row align-items-center g-3">
                                <div class="col-auto">
                                    <div
                                        style="
                                            width: 72px;
                                            height: 72px;
                                            border-radius: 50%;
                                            background: linear-gradient(135deg, #dc3545, #b02a37);
                                            color: #fff;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            font-size: 1.8rem;
                                            font-weight: 800;
                                            box-shadow: 0 4px 14px rgba(220, 53, 69, 0.35);
                                        "
                                    >
                                        {{ inicialesEstudiante }}
                                    </div>
                                </div>
                                <div class="col">
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <h3 class="m-0 fw-bold" style="color: var(--text-main);">
                                            {{ estudiante.usu_primer_nombre }} {{ estudiante.usu_primer_apellido }}
                                        </h3>
                                        <span class="badge bg-danger rounded-pill px-3 py-1 fs-6">
                                            C.I. {{ estudiante.usu_cedula }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3 flex-wrap text-muted small mt-2">
                                        <div><i class="bi bi-envelope me-1 text-danger"></i> {{ (estudiante.usu_email || estudiante.usu_correo) || 'Sin correo registrado' }}</div>
                                        <div><i class="bi bi-telephone me-1 text-danger"></i> {{ estudiante.usu_telefono || 'Sin teléfono' }}</div>
                                        <div v-if="seccionActual">
                                            <i class="bi bi-collection me-1 text-danger"></i>
                                            Sección <strong>{{ seccionActual.sec_codigo }}</strong> &bull;
                                            Trayecto {{ seccionActual.trayecto?.tra_nombre || 'N/A' }}
                                            (Semestre {{ seccionActual.sec_semestre || 'N/A' }})
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-md-auto text-md-end">
                                    <div class="p-2 px-3 rounded text-center border" style="background: rgba(220, 53, 69, 0.05); border-color: rgba(220, 53, 69, 0.2) !important;">
                                        <div class="small text-muted fw-bold">EQUIPO ACTUAL</div>
                                        <div class="fw-bold text-danger fs-6">
                                            {{ equipoActual ? equipoActual.equ_codigo : 'Sin equipo asignado' }}
                                        </div>
                                        <div v-if="equipoActual" class="text-truncate small text-muted" style="max-width: 220px;" :title="equipoActual.equ_titulo">
                                            {{ equipoActual.equ_titulo }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RESUMEN DE MÉTRICAS RÁPIDAS -->
                    <div class="row g-3 mb-4">
                        <div class="col-12 col-sm-4">
                            <div class="card border-0 shadow-sm p-3 h-100" style="background: var(--bg-card); border-radius: 12px; border-left: 4px solid #dc3545 !important;">
                                <div class="text-muted small fw-bold">HISTORIAL EN EQUIPOS</div>
                                <div class="fs-4 fw-bold text-danger mt-1">{{ historialEventos.length }}</div>
                                <div class="small text-muted">Eventos y movimientos registrados</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="card border-0 shadow-sm p-3 h-100" style="background: var(--bg-card); border-radius: 12px; border-left: 4px solid #198754 !important;">
                                <div class="text-muted small fw-bold">PUNTOS DE CONTROL</div>
                                <div class="fs-4 fw-bold text-success mt-1">{{ totalAsistenciasPuntos }}</div>
                                <div class="small text-muted">Asistencias con evaluación docente</div>
                            </div>
                        </div>
                        <div class="col-12 col-sm-4">
                            <div class="card border-0 shadow-sm p-3 h-100" style="background: var(--bg-card); border-radius: 12px; border-left: 4px solid #0d6efd !important;">
                                <div class="text-muted small fw-bold">SOCIALIZACIONES</div>
                                <div class="fs-4 fw-bold text-primary mt-1">{{ socializaciones.length }}</div>
                                <div class="small text-muted">Presentaciones reglamentarias</div>
                            </div>
                        </div>
                    </div>

                    <!-- TABS DE NAVEGACIÓN DEL EXPEDIENTE -->
                    <ul class="nav nav-pills gap-2 mb-3 border-bottom pb-2 flex-nowrap overflow-auto" role="tablist">
                        <li class="nav-item">
                            <button
                                class="nav-link rounded-pill fw-bold text-nowrap px-3 py-2"
                                :class="{ active: tabActiva === 'equipos' }"
                                @click="tabActiva = 'equipos'"
                            >
                                <i class="bi bi-people me-1"></i> Equipos e Historial ({{ historialEventos.length }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                                class="nav-link rounded-pill fw-bold text-nowrap px-3 py-2"
                                :class="{ active: tabActiva === 'puntos' }"
                                @click="tabActiva = 'puntos'"
                            >
                                <i class="bi bi-check2-circle me-1"></i> Puntos de Control y Asistencia ({{ asistenciasPuntos.length }})
                            </button>
                        </li>
                        <li class="nav-item">
                            <button
                                class="nav-link rounded-pill fw-bold text-nowrap px-3 py-2"
                                :class="{ active: tabActiva === 'socializaciones' }"
                                @click="tabActiva = 'socializaciones'"
                            >
                                <i class="bi bi-mic me-1"></i> Socializaciones y Avance ({{ socializaciones.length }})
                            </button>
                        </li>
                    </ul>

                    <!-- CONTENIDO TAB 1: EQUIPOS E HISTORIAL DE CAMBIOS Y EVIDENCIAS -->
                    <div v-show="tabActiva === 'equipos'">
                        <div class="card border-0 shadow-sm" style="background: var(--bg-card); border-radius: 14px;">
                            <div class="card-header bg-transparent border-0 pt-3 px-3 px-md-4">
                                <h6 class="fw-bold m-0 text-danger">
                                    <i class="bi bi-clock-history me-1"></i> Registro Histórico de Desvinculaciones, Traslados y Modificaciones
                                </h6>
                                <p class="text-muted small m-0 mt-1">
                                    Evidencias formales registradas ante cualquier salida o traslado del estudiante, con fecha, motivo y responsable.
                                </p>
                            </div>
                            <div class="card-body p-0 p-md-3">
                                <div v-if="historialEventos.length === 0" class="text-center py-5 text-muted">
                                    <i class="bi bi-info-circle fs-2 d-block mb-2 text-danger opacity-50"></i>
                                    El estudiante no posee eventos disciplinarios, traslados ni desvinculaciones registradas.
                                </div>
                                <div v-else class="table-responsive">
                                    <table class="table align-middle mb-0" style="color: var(--text-main);">
                                        <thead class="small text-uppercase" style="background: var(--bg-th); color: var(--text-muted);">
                                            <tr>
                                                <th class="ps-3">Fecha y Hora</th>
                                                <th>Tipo de Evento</th>
                                                <th>Equipo Afectado</th>
                                                <th>Motivo / Evidencia Registrada</th>
                                                <th>Responsable</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="ev in historialEventos" :key="ev.heq_id">
                                                <td class="ps-3 small text-muted text-nowrap">
                                                    {{ formatearFecha(ev.heq_fecha_evento) }}
                                                </td>
                                                <td>
                                                    <span class="badge bg-danger rounded-pill px-2 py-1">
                                                        {{ ev.tipo_evento?.tee_nombre || ev.tipo_evento?.tev_nombre || 'Evento de Equipo' }}
                                                    </span>
                                                </td>
                                                <td class="fw-semibold">
                                                    {{ ev.equipo?.equ_codigo || 'Equipo ' + ev.heq_id_equ }}
                                                </td>
                                                <td style="max-width: 320px;">
                                                    <div class="p-2 rounded bg-light border text-dark small" style="white-space: pre-wrap;">
                                                        {{ ev.heq_detalles || ev.heq_motivo || 'Sin detalles' }}
                                                    </div>
                                                </td>
                                                <td class="small text-muted">
                                                    {{ ev.usuario_modifico ? `${ev.usuario_modifico.usu_primer_nombre} ${ev.usuario_modifico.usu_primer_apellido}` : 'Sistema' }}
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CONTENIDO TAB 2: PUNTOS DE CONTROL Y ASISTENCIA -->
                    <div v-show="tabActiva === 'puntos'">
                        <div class="card border-0 shadow-sm" style="background: var(--bg-card); border-radius: 14px;">
                            <div class="card-header bg-transparent border-0 pt-3 px-3 px-md-4">
                                <h6 class="fw-bold m-0 text-danger">
                                    <i class="bi bi-calendar-check me-1"></i> Control de Asistencia y Observaciones Docente (Semanas 6, 10, 14)
                                </h6>
                                <p class="text-muted small m-0 mt-1">
                                    Historial punto a punto con justificaciones de inasistencia y anotaciones individuales sobre el desempeño del estudiante.
                                </p>
                            </div>
                            <div class="card-body p-0 p-md-3">
                                <div v-if="asistenciasPuntos.length === 0" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-2 d-block mb-2 text-danger opacity-50"></i>
                                    No hay registros de puntos de control evaluados para este estudiante todavía.
                                </div>
                                <div v-else class="table-responsive">
                                    <table class="table align-middle mb-0" style="color: var(--text-main);">
                                        <thead class="small text-uppercase" style="background: var(--bg-th); color: var(--text-muted);">
                                            <tr>
                                                <th class="ps-3">Punto / Semana</th>
                                                <th>Equipo</th>
                                                <th>Asistencia</th>
                                                <th>Cumplimiento Previo</th>
                                                <th>Observaciones Individuales</th>
                                                <th>Observaciones Generales</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="asist in asistenciasPuntos" :key="asist.apc_id">
                                                <td class="ps-3">
                                                    <div class="fw-bold">{{ asist.puc_nombre }}</div>
                                                    <div class="small text-muted">
                                                        Semana {{ asist.puc_semana }} &bull; Límite: {{ asist.fecha_limite || 'N/A' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary rounded-pill px-2 py-1">
                                                        {{ asist.equipo_codigo || 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div v-if="asist.asistio" class="badge bg-success rounded-pill px-2 py-1">
                                                        <i class="bi bi-check-circle me-1"></i> Presente
                                                    </div>
                                                    <div v-else-if="asist.justificado" class="badge bg-warning text-dark rounded-pill px-2 py-1">
                                                        <i class="bi bi-exclamation-triangle me-1"></i> Inasistencia Justificada
                                                    </div>
                                                    <div v-else class="badge bg-danger rounded-pill px-2 py-1">
                                                        <i class="bi bi-x-circle me-1"></i> Inasistencia Injustificada
                                                    </div>
                                                    <div v-if="asist.justificativo" class="small text-muted mt-1 fst-italic">
                                                        "{{ asist.justificativo }}"
                                                    </div>
                                                </td>
                                                <td>
                                                    <span v-if="asist.cumplio_previo === true" class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2">
                                                        Cumplió acuerdos
                                                    </span>
                                                    <span v-else-if="asist.cumplio_previo === false" class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2">
                                                        Incumplió acuerdos
                                                    </span>
                                                    <span v-else class="text-muted small">No evaluado</span>
                                                </td>
                                                <td style="max-width: 280px;">
                                                    <div v-if="asist.observacion_indiv" class="p-2 rounded bg-light border text-dark small">
                                                        {{ asist.observacion_indiv }}
                                                    </div>
                                                    <span v-else class="text-muted small">Sin anotaciones individuales</span>
                                                </td>
                                                <td style="max-width: 280px;">
                                                    <div v-if="asist.obs_generales" class="small text-muted">
                                                        {{ asist.obs_generales }}
                                                    </div>
                                                    <span v-else class="text-muted small">-</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- CONTENIDO TAB 3: SOCIALIZACIONES Y AVANCE -->
                    <div v-show="tabActiva === 'socializaciones'">
                        <div class="card border-0 shadow-sm" style="background: var(--bg-card); border-radius: 14px;">
                            <div class="card-header bg-transparent border-0 pt-3 px-3 px-md-4">
                                <h6 class="fw-bold m-0 text-danger">
                                    <i class="bi bi-mic me-1"></i> Presentaciones y Defensa ante Jurado Evaluador
                                </h6>
                                <p class="text-muted small m-0 mt-1">
                                    Registro de asistencia del estudiante a las jornadas de pre-socialización y socialización final de proyecto.
                                </p>
                            </div>
                            <div class="card-body p-0 p-md-3">
                                <div v-if="socializaciones.length === 0" class="text-center py-5 text-muted">
                                    <i class="bi bi-mic-mute fs-2 d-block mb-2 text-danger opacity-50"></i>
                                    No hay presentaciones ni socializaciones registradas para este estudiante.
                                </div>
                                <div v-else class="table-responsive">
                                    <table class="table align-middle mb-0" style="color: var(--text-main);">
                                        <thead class="small text-uppercase" style="background: var(--bg-th); color: var(--text-muted);">
                                            <tr>
                                                <th class="ps-3">Tipo de Socialización</th>
                                                <th>Fecha y Lugar</th>
                                                <th>Equipo</th>
                                                <th>Asistencia Estudiante</th>
                                                <th>Condición / Veredicto</th>
                                                <th>Justificación / Observaciones</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="soc in socializaciones" :key="soc.iso_id">
                                                <td class="ps-3 fw-bold">
                                                    {{ soc.tipo }}
                                                </td>
                                                <td class="small">
                                                    <div>{{ soc.fecha || 'Fecha por definir' }}</div>
                                                    <div class="text-muted">{{ soc.lugar }}</div>
                                                </td>
                                                <td class="small">
                                                    <span class="badge bg-secondary rounded-pill px-2">
                                                        {{ soc.equipo_codigo }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span v-if="soc.asistio" class="badge bg-success rounded-pill px-2 py-1">
                                                        <i class="bi bi-check-circle me-1"></i> Asistió
                                                    </span>
                                                    <span v-else class="badge bg-danger rounded-pill px-2 py-1">
                                                        <i class="bi bi-x-circle me-1"></i> Inasistencia
                                                    </span>
                                                </td>
                                                <td>
                                                    <span v-if="soc.avanza" class="badge bg-success rounded-pill px-2 py-1">
                                                        <i class="bi bi-arrow-right-circle me-1"></i> Avanza / Apto
                                                    </span>
                                                    <span v-else class="badge bg-danger rounded-pill px-2 py-1">
                                                        <i class="bi bi-x-octagon me-1"></i> Reprobado / No Avanza
                                                    </span>
                                                </td>
                                                <td style="max-width: 320px;">
                                                    <div v-if="soc.justificacion" class="p-2 rounded bg-light border text-dark small">
                                                        {{ soc.justificacion }}
                                                    </div>
                                                    <span v-else class="text-muted small">Sin observaciones</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import axios from "axios";
import SidebarCoordinador from "@/Components/SidebarCoordinador.vue";
import SidebarProfesor from "@/Components/SidebarProfesor.vue";
import SelectBuscable from "@/Components/SelectBuscable.vue";

const props = defineProps({
    estudiante: Object,
    historialEventos: { type: Array, default: () => [] },
    asistenciasPuntos: { type: Array, default: () => [] },
    socializaciones: { type: Array, default: () => [] },
    backUrl: String,
    esCoordinador: Boolean,
    estudiantesLista: { type: Array, default: () => [] },
    secciones: { type: Array, default: () => [] },
});

const page = usePage();
const sidebarOpen = ref(false);
const tabActiva = ref("equipos");

// Filtros para la vista del directorio
const filtroTexto = ref("");
const filtroSeccion = ref("");
const filtroEquipo = ref("todos");

const opcionesSeccionesExpEst = computed(() => [
    { value: "", label: "Todas las secciones asignadas" },
    ...(props.secciones || []).map((sec) => ({
        value: sec.sec_id,
        label: `Sección ${sec.sec_codigo}${sec.trayecto?.tra_nombre ? " (" + sec.trayecto.tra_nombre + ")" : ""}`,
    })),
]);
const opcionesFiltroEquipoEst = [
    { value: "todos", label: "Todos los equipos" },
    { value: "con_equipo", label: "Con equipo asignado" },
    { value: "sin_equipo", label: "Sin equipo asignado" },
];

// Búsqueda rápida superior cuando hay un estudiante seleccionado
const busquedaInput = ref("");
const resultadosBusqueda = ref([]);
const mostrarDropdownResultados = ref(false);

const temaKey = computed(() => props.esCoordinador ? 'coord-theme' : 'prof-theme');
const tema = ref(localStorage.getItem(temaKey.value) || "light");

const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem(temaKey.value, tema.value);
};

const baseUrl = computed(() => props.esCoordinador ? '/coordinador' : '/profesor');

const inicialesDocente = computed(() => {
    const u = page.props.auth?.user;
    const n = u?.usu_primer_nombre?.[0] || "";
    const a = u?.usu_primer_apellido?.[0] || "";
    return (n + a).toUpperCase() || "UP";
});

const inicialesEstudiante = computed(() => {
    if (!props.estudiante) return "UP";
    const nom = props.estudiante.usu_primer_nombre || "";
    const ape = props.estudiante.usu_primer_apellido || "";
    return `${nom[0] || ""}${ape[0] || ""}`.toUpperCase();
});

const obtenerIniciales = (nombre, apellido) => {
    const n = (nombre || "")[0] || "";
    const a = (apellido || "")[0] || "";
    return (n + a).toUpperCase() || "ES";
};

const seccionActual = computed(() => {
    return props.estudiante?.secciones_estudiante?.[0]?.seccion || null;
});

const equipoActual = computed(() => {
    return props.estudiante?.equipos_integrante?.[0]?.equipo || null;
});

const totalAsistenciasPuntos = computed(() => {
    return props.asistenciasPuntos.filter(a => a.asistio).length;
});

const estudiantesFiltrados = computed(() => {
    let list = props.estudiantesLista || [];

    if (filtroSeccion.value) {
        list = list.filter(e => String(e.seccion_id) === String(filtroSeccion.value));
    }

    if (filtroEquipo.value === 'con_equipo') {
        list = list.filter(e => !!e.equipo_codigo);
    } else if (filtroEquipo.value === 'sin_equipo') {
        list = list.filter(e => !e.equipo_codigo);
    }

    if (filtroTexto.value.trim()) {
        const q = filtroTexto.value.trim().toLowerCase();
        list = list.filter(e => {
            const nom = `${e.usu_primer_nombre || ''} ${e.usu_primer_apellido || ''}`.toLowerCase();
            const ci = String(e.usu_cedula || '').toLowerCase();
            const sec = String(e.seccion_codigo || '').toLowerCase();
            const eq = String(e.equipo_codigo || '').toLowerCase();
            return nom.includes(q) || ci.includes(q) || sec.includes(q) || eq.includes(q);
        });
    }

    return list;
});

const limpiarFiltros = () => {
    filtroTexto.value = "";
    filtroSeccion.value = "";
    filtroEquipo.value = "todos";
};

const formatearFecha = (fechaStr) => {
    if (!fechaStr) return "N/A";
    const d = new Date(fechaStr);
    return isNaN(d) ? fechaStr : d.toLocaleString("es-VE", { dateStyle: "short", timeStyle: "short" });
};

let timerBusqueda = null;
const buscarEstudiantes = () => {
    clearTimeout(timerBusqueda);
    const q = busquedaInput.value.trim();
    if (q.length < 1) {
        resultadosBusqueda.value = [];
        return;
    }
    timerBusqueda = setTimeout(async () => {
        try {
            const url = `${baseUrl.value}/estudiantes/buscar?q=${encodeURIComponent(q)}`;
            const res = await axios.get(url);
            resultadosBusqueda.value = res.data;
            mostrarDropdownResultados.value = true;
        } catch (e) {
            console.error(e);
        }
    }, 250);
};

const logout = () => {
    router.post("/logout");
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

.sb-prof {
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
.sb-avatar {
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
.sb-link {
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
.sb-link:hover {
    background: rgba(255, 255, 255, 0.06);
    color: #fff;
}
.sb-link.active {
    background: rgba(220, 53, 69, 0.18);
    color: #fff;
    border-left-color: #dc3545;
    font-weight: 700;
}
.sb-link i {
    font-size: 1rem;
    width: 20px;
    text-align: center;
}

.main-prof {
    transition: margin-left 0.3s;
}
.topbar-prof {
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
.topbar-title {
    font-weight: 800;
    color: var(--text-main);
    font-size: 1.15rem;
}
.topbar-btn {
    background: none;
    border: none;
    color: var(--text-muted);
    font-size: 1.3rem;
    cursor: pointer;
    padding: 4px;
    transition: color 0.2s;
}
.topbar-btn:hover {
    color: #dc3545;
}
.content-prof {
    padding: 1.4rem 1.25rem;
}

.student-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08) !important;
}

.nav-pills .nav-link {
    color: var(--text-muted);
    background: var(--bg-card);
    border: 1px solid var(--border);
    transition: all 0.2s;
}
.nav-pills .nav-link.active {
    background: #dc3545 !important;
    color: #fff !important;
    border-color: #dc3545 !important;
}

.sb-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1035;
    display: none;
}

@media (min-width: 992px) {
    .sb-prof {
        transform: translateX(0) !important;
    }
    .main-prof {
        margin-left: 250px;
    }
    .toggle-btn {
        display: none !important;
    }
}
@media (max-width: 991.98px) {
    .sb-prof {
        transform: translateX(-100%);
    }
    .main-prof {
        margin-left: 0;
    }
    .sb-prof.show {
        transform: translateX(0);
    }
    .sb-overlay.show {
        display: block !important;
    }
    .content-prof {
        padding: 1rem;
    }
}
</style>
