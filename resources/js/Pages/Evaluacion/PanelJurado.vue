<template>
    <div class="panel-jurado-page min-vh-100 d-flex flex-column bg-light text-dark">
        <!-- ── HEADER INSTITUCIONAL CON BANNER DE VIGENCIA DE 12 HORAS ── -->
        <header class="bg-dark text-white py-3 border-bottom border-danger border-4 sticky-top shadow z-3">
            <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
                <div class="d-flex align-items-center gap-3">
                    <img :src="'/images/images.png'" alt="Logo UPTP" class="logo-uptp" />
                    <div>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-danger text-uppercase fw-bold px-2 py-1 small">
                                <i class="bi bi-shield-check me-1"></i>Portal de Evaluación Oficial
                            </span>
                            <span class="badge bg-secondary-subtle text-white-50 border border-secondary px-2 py-1 small">
                                Socialización Final PST
                            </span>
                        </div>
                        <h1 class="h5 m-0 fw-bold text-white lh-1 mt-1">
                            <span class="text-danger font-monospace">{{ equipo.equ_codigo }}</span> &bull; {{ equipo.equ_titulo || 'Proyecto Sociotecnológico' }}
                        </h1>
                        <small class="text-white-50">
                            UPTP "Juan de Jesús Montilla" &bull; {{ equipo.trayecto?.tra_nombre || 'PNFI' }} &bull; Sección {{ equipo.seccion?.sec_codigo || '—' }}
                        </small>
                    </div>
                </div>

                <!-- Temporizador de Vigencia (12 Horas) -->
                <div class="timer-box d-flex align-items-center gap-3 bg-black px-3 py-2 rounded-3 border border-secondary shadow-sm">
                    <div class="d-flex align-items-center justify-content-center bg-dark rounded-circle p-2 border border-secondary">
                        <i class="bi bi-clock-history text-warning fs-4"></i>
                    </div>
                    <div class="text-end">
                        <div class="text-white-50 small fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px;">
                            Vigencia del Enlace
                        </div>
                        <div class="fw-bold font-monospace text-warning fs-5 lh-1">
                            {{ tiempoRestanteLocal }}
                        </div>
                        <small class="text-white-50" style="font-size: 0.68rem;" v-if="expira_formateado">
                            Expira: {{ expira_formateado }}
                        </small>
                    </div>
                    <span v-if="minutosRestantesCalc > 0" class="badge bg-success-subtle text-success border border-success px-2 py-1 text-uppercase fw-bold small">
                        <i class="bi bi-broadcast me-1 pulse-dot"></i>Activo
                    </span>
                    <span v-else class="badge bg-danger text-white px-2 py-1 text-uppercase fw-bold small">
                        <i class="bi bi-x-octagon-fill me-1"></i>Expirado
                    </span>
                </div>
            </div>
        </header>

        <!-- ── BARRA DE PESTAÑAS PRINCIPALES (ACCESIBLES, GRANDES Y MODERNAS) ── -->
        <nav class="bg-white border-bottom shadow-sm">
            <div class="container">
                <ul class="nav nav-pills nav-fill py-2 gap-2 flex-column flex-md-row" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link py-3 px-3 fw-bold fs-6 d-flex align-items-center justify-content-center gap-2 border rounded-3 transition-all"
                            :class="{ 'active bg-dark text-white border-dark shadow-sm': tabActiva === 'datos', 'text-dark bg-light': tabActiva !== 'datos' }"
                            @click="tabActiva = 'datos'"
                            type="button"
                            role="tab"
                        >
                            <span class="badge" :class="tabActiva === 'datos' ? 'bg-danger text-white' : 'bg-secondary text-white'">1</span>
                            <i class="bi bi-journal-text fs-5"></i>
                            <span>Ficha del Proyecto</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link py-3 px-3 fw-bold fs-6 d-flex align-items-center justify-content-center gap-2 border rounded-3 transition-all"
                            :class="{ 'active bg-dark text-white border-dark shadow-sm': tabActiva === 'historial', 'text-dark bg-light': tabActiva !== 'historial' }"
                            @click="tabActiva = 'historial'"
                            type="button"
                            role="tab"
                        >
                            <span class="badge" :class="tabActiva === 'historial' ? 'bg-danger text-white' : 'bg-secondary text-white'">2</span>
                            <i class="bi bi-calendar-check fs-5"></i>
                            <span>Historial Académico</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link py-3 px-3 fw-bold fs-6 d-flex align-items-center justify-content-center gap-2 border rounded-3 transition-all"
                            :class="{ 'active bg-dark text-white border-dark shadow-sm': tabActiva === 'expediente', 'text-dark bg-light': tabActiva !== 'expediente' }"
                            @click="tabActiva = 'expediente'"
                            type="button"
                            role="tab"
                        >
                            <span class="badge" :class="tabActiva === 'expediente' ? 'bg-danger text-white' : 'bg-secondary text-white'">3</span>
                            <i class="bi bi-person-vcard fs-5"></i>
                            <span>Expedientes por Integrante</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            class="nav-link py-3 px-3 fw-bold fs-6 d-flex align-items-center justify-content-center gap-2 border rounded-3 transition-all"
                            :class="{ 'active bg-danger text-white border-danger shadow': tabActiva === 'evaluacion', 'text-danger bg-danger-subtle border-danger': tabActiva !== 'evaluacion' }"
                            @click="tabActiva = 'evaluacion'"
                            type="button"
                            role="tab"
                        >
                            <span class="badge" :class="tabActiva === 'evaluacion' ? 'bg-white text-danger' : 'bg-danger text-white'">4</span>
                            <i class="bi bi-award-fill fs-5"></i>
                            <span>Scorecard de Evaluación</span>
                        </button>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- ── CONTENIDO PRINCIPAL ── -->
        <main class="container py-4 flex-grow-1">
            <!-- Alertas Flash -->
            <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show p-3 rounded-4 shadow-sm mb-4 border-2 border-success" role="alert">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <span class="fw-bold">{{ $page.props.flash.success }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show p-3 rounded-4 shadow-sm mb-4 border-2 border-danger" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <span class="fw-bold">{{ $page.props.flash.error }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>

            <!-- ════════════════════════════════════════════════════════════ -->
            <!-- PESTAÑA 1: DATOS DEL GRUPO Y PROYECTO EVALUADO -->
            <!-- ════════════════════════════════════════════════════════════ -->
            <section v-if="tabActiva === 'datos'" class="animate-fadeIn">
                <div class="row g-4">
                    <!-- Ficha Técnica Principal -->
                    <div class="col-lg-8">
                        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                            <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center border-bottom border-danger border-3">
                                <h5 class="m-0 fw-bold d-flex align-items-center gap-2 text-white">
                                    <i class="bi bi-journal-bookmark-fill text-danger"></i> Ficha Técnica del Proyecto
                                </h5>
                                <span class="badge bg-danger fs-6 px-3 py-1 font-monospace">{{ equipo.equ_codigo }}</span>
                            </div>
                            <div class="card-body p-4 text-dark" style="font-size: 1.05rem; line-height: 1.6;">
                                <div class="mb-3">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Título Oficial del Proyecto</label>
                                    <h4 class="fw-bold text-dark mb-0">{{ equipo.equ_titulo || 'Sin título asignado' }}</h4>
                                </div>

                                <div class="mb-4">
                                    <label class="text-muted small fw-bold text-uppercase d-block mb-1">Resumen Ejecutivo / Alcance Técnico</label>
                                    <div class="p-3 bg-light rounded-3 border border-start border-4 border-danger text-justify">
                                        {{ proyecto.pco_resumen || 'No se ha registrado un resumen técnico para este proyecto.' }}
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3 bg-light border">
                                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Línea de Investigación</label>
                                            <div class="fw-bold text-dark">
                                                <i class="bi bi-diagram-3-fill text-danger me-1"></i>
                                                {{ proyecto.pco_linea_investigacion || 'Sistemas de Información y Desarrollo de Software' }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded-3 bg-light border">
                                            <label class="text-muted small fw-bold text-uppercase d-block mb-1">Ubicación Académica</label>
                                            <div class="fw-bold text-dark">
                                                <i class="bi bi-mortarboard-fill text-danger me-1"></i>
                                                {{ equipo.trayecto?.tra_nombre || 'Trayecto' }} &bull; Sección {{ equipo.seccion?.sec_codigo }} ({{ equipo.seccion?.turno?.tur_nombre || 'Turno Regular' }})
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comunidad Beneficiada -->
                        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                            <div class="card-header bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
                                <h5 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-geo-alt-fill text-danger"></i> Comunidad Beneficiada y Ámbito
                                </h5>
                                <span class="badge bg-dark">Impacto Territorial</span>
                            </div>
                            <div class="card-body p-4 text-dark">
                                <h5 class="fw-bold text-dark mb-2">
                                    {{ equipo.proyecto_comunidad?.comunidad?.com_nombre || 'Comunidad no registrada' }}
                                </h5>
                                <p class="text-muted mb-3">
                                    <strong><i class="bi bi-pin-map text-danger me-1"></i>Dirección / Sector:</strong>
                                    {{ equipo.proyecto_comunidad?.comunidad?.com_direccion || equipo.proyecto_comunidad?.comunidad?.com_sector || 'Sector no especificado' }}
                                </p>
                                <div v-if="responsable_comunidad" class="alert alert-light border rounded-3 p-3 mb-0">
                                    <strong class="text-dark d-block mb-1">
                                        <i class="bi bi-person-badge-fill text-danger me-1"></i> Vocero / Contacto Comunitario:
                                    </strong>
                                    <div>{{ responsable_comunidad.rco_nombre }} &bull; C.I. {{ responsable_comunidad.rco_cedula }}</div>
                                    <div class="small text-muted" v-if="responsable_comunidad.rco_telefono">Teléfono de contacto: {{ responsable_comunidad.rco_telefono }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Columna Lateral: Asesores e Integrantes -->
                    <div class="col-lg-4">
                        <!-- Docente y Tutor -->
                        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                            <div class="card-header bg-dark text-white p-3 border-bottom border-danger border-3">
                                <h6 class="m-0 fw-bold text-white"><i class="bi bi-people-fill text-danger me-1"></i> Docente y Tutor Asesor</h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="mb-3 pb-3 border-bottom">
                                    <span class="badge bg-danger-subtle text-danger fw-bold mb-1">Docente de Proyecto</span>
                                    <div class="fw-bold text-dark fs-6" v-if="docente">
                                        {{ docente.usu_primer_nombre }} {{ docente.usu_primer_apellido }}
                                    </div>
                                    <div class="small text-muted" v-if="docente">C.I. {{ docente.usu_cedula }} &bull; {{ docente.usu_email }}</div>
                                    <div class="text-muted small" v-else>No asignado a la sección</div>
                                </div>
                                <div>
                                    <span class="badge bg-dark-subtle text-dark fw-bold mb-1">Tutor de Proyecto</span>
                                    <div class="fw-bold text-dark fs-6" v-if="tutor">
                                        {{ tutor.usu_primer_nombre }} {{ tutor.usu_primer_apellido }}
                                    </div>
                                    <div class="small text-muted" v-if="tutor">C.I. {{ tutor.usu_cedula }} &bull; {{ tutor.usu_email }}</div>
                                    <div class="text-muted small" v-else>No asignado</div>
                                </div>
                            </div>
                        </div>

                        <!-- Integrantes del Equipo -->
                        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                            <div class="card-header bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="m-0 fw-bold text-dark">
                                    <i class="bi bi-mortarboard text-danger me-1"></i> Integrantes ({{ integrantes_detalles.length }})
                                </h6>
                                <span class="badge bg-danger rounded-pill">{{ integrantes_detalles.length }} estudiantes</span>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li
                                    v-for="integ in integrantes_detalles"
                                    :key="integ.usu_id"
                                    class="list-group-item p-3 d-flex align-items-center justify-content-between"
                                >
                                    <div>
                                        <div class="fw-bold text-dark">{{ integ.nombre_completo }}</div>
                                        <div class="small text-muted font-monospace">C.I. {{ integ.cedula }}</div>
                                    </div>
                                    <span v-if="integ.es_lider" class="badge bg-danger text-uppercase px-2 py-1">Líder</span>
                                    <span v-else class="badge bg-light text-dark border px-2 py-1">Integrante</span>
                                </li>
                            </ul>
                            <div class="card-footer bg-light text-center p-3">
                                <button class="btn btn-outline-danger btn-sm w-100 fw-bold" @click="tabActiva = 'expediente'">
                                    Ver Expediente Detallado de Cada Uno &rarr;
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ════════════════════════════════════════════════════════════ -->
            <!-- PESTAÑA 2: HISTORIAL ACADÉMICO DEL GRUPO -->
            <!-- ════════════════════════════════════════════════════════════ -->
            <section v-if="tabActiva === 'historial'" class="animate-fadeIn">
                <div class="row g-4">
                    <!-- Calificaciones Acumuladas Semestre 1 y 2 -->
                    <div class="col-12">
                        <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 overflow-hidden">
                            <div class="card-header bg-dark text-white p-3 border-bottom border-danger border-3">
                                <h5 class="m-0 fw-bold d-flex align-items-center gap-2 text-white">
                                    <i class="bi bi-bar-chart-steps text-danger"></i> Calificaciones Acumuladas y Trayectoria
                                </h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <!-- Semestre Impar (Cualitativo) -->
                                    <div class="col-md-6">
                                        <div class="p-3 border-2 border-danger-subtle rounded-4 bg-light h-100">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-dark text-white px-2 py-1">Semestre Impar (Cualitativo)</span>
                                                <span class="badge bg-secondary">Pre-Socialización</span>
                                            </div>
                                            <div class="fw-bold fs-5 text-dark mb-1">
                                                {{ preSocGeneral ? (preSocGeneral.soc_apto ? 'CALIFICADO COMO APTO' : 'NO APTO') : 'Sin registro de Pre-Socialización' }}
                                            </div>
                                            <p class="small text-muted mb-2">
                                                {{ preSocGeneral?.soc_observaciones || 'No se registraron observaciones adicionales en la Pre-Socialización.' }}
                                            </p>
                                            <div class="small text-dark" v-if="preSocGeneral?.soc_fecha">
                                                <strong>Fecha del acto:</strong> {{ preSocGeneral.soc_fecha }}
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Semestre Par (Cuantitativo) -->
                                    <div class="col-md-6">
                                        <div class="p-3 border-2 border-success-subtle rounded-4 bg-light h-100">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="badge bg-success text-white px-2 py-1">Semestre Par (Cuantitativo)</span>
                                                <span class="badge bg-dark">Socialización Final (100%)</span>
                                            </div>
                                            <div class="fw-bold fs-5 text-dark mb-1">
                                                Puntos de Control: {{ puntosControl.length }} evaluados
                                            </div>
                                            <p class="small text-muted mb-2">
                                                Ponderación oficial: Docente (40%) + Jurado Evaluador (30%) + Comunidad (20%) + Autoevaluación Estudiantil (10%).
                                            </p>
                                            <div class="small text-dark" v-if="socFinalGeneral?.soc_fecha">
                                                <strong>Fecha programada de defensa:</strong> {{ socFinalGeneral.soc_fecha }} &bull; {{ socFinalGeneral.soc_lugar }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Puntos de Control Evaluados -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 bg-white h-100 overflow-hidden">
                            <div class="card-header bg-light p-3 border-bottom">
                                <h5 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-check2-circle text-danger"></i> Puntos de Control y Seguimiento
                                </h5>
                            </div>
                            <div class="card-body p-3">
                                <div v-if="puntosControl.length === 0" class="text-center text-muted py-5">
                                    <i class="bi bi-clipboard-x fs-1 text-muted d-block mb-2"></i>
                                    No hay puntos de control registrados aún para esta sección.
                                </div>
                                <div v-else class="list-group list-group-flush">
                                    <div
                                        v-for="puc in puntosControl"
                                        :key="puc.puc_id"
                                        class="list-group-item p-3 border rounded-3 mb-2 bg-light"
                                    >
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-dark">Corte Semana {{ puc.puc_semana_corte }}</span>
                                            <span class="badge bg-dark">{{ puc.puc_fecha || 'Fecha programada' }}</span>
                                        </div>
                                        <div class="small text-muted mb-2">
                                            <strong>Instrumento:</strong> {{ puc.puc_tipo_instrumento || 'Pauta oficial' }}
                                        </div>
                                        <div v-if="puc.seq_observaciones" class="alert alert-white bg-white p-2 border rounded small mb-1">
                                            <strong>Observaciones del docente:</strong> {{ puc.seq_observaciones }}
                                        </div>
                                        <div v-if="puc.seq_cumplio_observaciones_anteriores !== null" class="small mt-1">
                                            <span v-if="puc.seq_cumplio_observaciones_anteriores" class="text-success fw-bold">
                                                <i class="bi bi-check-circle-fill me-1"></i> Cumplió observaciones previas
                                            </span>
                                            <span v-else class="text-danger fw-bold">
                                                <i class="bi bi-x-circle-fill me-1"></i> No cumplió observaciones previas
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Línea de Tiempo de Eventos del Equipo -->
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm rounded-4 bg-white h-100 overflow-hidden">
                            <div class="card-header bg-light p-3 border-bottom">
                                <h5 class="m-0 fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-clock-history text-danger"></i> Línea de Tiempo de Eventos
                                </h5>
                            </div>
                            <div class="card-body p-3" style="max-height: 480px; overflow-y: auto;">
                                <div v-if="historial_eventos.length === 0" class="text-center text-muted py-5">
                                    <i class="bi bi-clock fs-1 text-muted d-block mb-2"></i>
                                    No hay eventos registrados en la bitácora del equipo.
                                </div>
                                <div v-else class="timeline">
                                    <div
                                        v-for="(ev, idx) in historial_eventos"
                                        :key="idx"
                                        class="p-3 mb-2 border rounded-3 bg-white border-start border-4 border-danger shadow-xs"
                                    >
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="fw-bold text-dark">{{ ev.teq_nombre || 'Evento del Equipo' }}</span>
                                            <small class="text-muted font-monospace">{{ ev.heq_fecha_evento }}</small>
                                        </div>
                                        <p class="small text-muted mb-0">{{ ev.heq_motivo || 'Sin detalles' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ════════════════════════════════════════════════════════════ -->
            <!-- PESTAÑA 3: EXPEDIENTE INDIVIDUAL POR ESTUDIANTE -->
            <!-- ════════════════════════════════════════════════════════════ -->
            <section v-if="tabActiva === 'expediente'" class="animate-fadeIn">
                <div class="alert alert-dark p-3 rounded-4 mb-4 d-flex align-items-center justify-content-between border-0 shadow-sm">
                    <div>
                        <i class="bi bi-person-lines-fill text-danger me-2 fs-5"></i>
                        <strong>Expediente Individual de Integrantes:</strong> Seleccione a cada estudiante para consultar su récord de asistencia, estatus reglamentario y antecedentes académicos.
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Lista de Integrantes (Selector) -->
                    <div class="col-lg-4">
                        <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                            <div class="card-header bg-dark text-white p-3 border-bottom border-danger border-3">
                                <h6 class="m-0 fw-bold text-white">Seleccione un Estudiante:</h6>
                            </div>
                            <div class="list-group list-group-flush p-2">
                                <button
                                    v-for="integ in integrantes_detalles"
                                    :key="integ.usu_id"
                                    type="button"
                                    class="list-group-item list-group-item-action p-3 rounded-3 mb-2 border d-flex align-items-center justify-content-between text-start transition-all"
                                    :class="{ 'bg-danger text-white border-danger shadow': estudianteSeleccionado?.usu_id === integ.usu_id, 'bg-light': estudianteSeleccionado?.usu_id !== integ.usu_id }"
                                    @click="estudianteSeleccionado = integ"
                                >
                                    <div>
                                        <div class="fw-bold fs-6" :class="{ 'text-white': estudianteSeleccionado?.usu_id === integ.usu_id, 'text-dark': estudianteSeleccionado?.usu_id !== integ.usu_id }">
                                            {{ integ.nombre_completo }}
                                        </div>
                                        <div class="small font-monospace" :class="{ 'text-white-50': estudianteSeleccionado?.usu_id === integ.usu_id, 'text-muted': estudianteSeleccionado?.usu_id !== integ.usu_id }">
                                            C.I. {{ integ.cedula }} &bull; Asist.: {{ integ.porcentaje_asistencia ?? 100 }}%
                                        </div>
                                        <span
                                            v-if="integ.reprobado_inasistencia"
                                            class="badge mt-1"
                                            :class="estudianteSeleccionado?.usu_id === integ.usu_id ? 'bg-warning text-dark' : 'bg-danger text-white'"
                                        >
                                            <i class="bi bi-exclamation-octagon-fill me-1"></i>&gt;25% Inasistencia
                                        </span>
                                    </div>
                                    <i class="bi bi-chevron-right fs-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Ficha Detallada del Estudiante Seleccionado -->
                    <div class="col-lg-8">
                        <div v-if="estudianteSeleccionado" class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
                            <div class="card-header bg-light p-3 border-bottom d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="fw-bold text-dark m-0">{{ estudianteSeleccionado.nombre_completo }}</h5>
                                    <span class="small text-muted font-monospace">Cédula de Identidad: V-{{ estudianteSeleccionado.cedula }}</span>
                                </div>
                                <span v-if="estudianteSeleccionado.es_lider" class="badge bg-danger fs-6 px-3 py-1">Líder del Equipo</span>
                                <span v-else class="badge bg-secondary fs-6 px-3 py-1">Integrante</span>
                            </div>

                            <div class="card-body p-4 text-dark" style="font-size: 1.05rem;">
                                <!-- Resumen Individual PNF de Asistencia y Alerta de Inasistencia (>25%) -->
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 p-3 rounded-3 border bg-light mb-3">
                                    <div>
                                        <span class="small text-muted fw-bold text-uppercase d-block">Asistencia Acumulada en Puntos de Control</span>
                                        <span class="fs-5 fw-bold" :class="estudianteSeleccionado.reprobado_inasistencia ? 'text-danger' : 'text-success'">
                                            {{ estudianteSeleccionado.porcentaje_asistencia ?? 100 }}% Asistencia
                                        </span>
                                        <span class="small text-muted ms-2">
                                            (Inasistencias injustificadas: {{ estudianteSeleccionado.porcentaje_inasistencia ?? 0 }}%)
                                        </span>
                                    </div>
                                    <span
                                        class="badge px-3 py-2 fs-6"
                                        :class="estudianteSeleccionado.reprobado_inasistencia ? 'bg-danger text-white' : 'bg-dark text-white'"
                                    >
                                        {{ estudianteSeleccionado.condicion_pnf || 'En Curso' }}
                                    </span>
                                </div>

                                <div
                                    v-if="estudianteSeleccionado.reprobado_inasistencia"
                                    class="alert alert-danger border-2 border-danger rounded-3 p-3 mb-4"
                                    role="alert"
                                >
                                    <div class="fw-bold d-flex align-items-center gap-2 mb-1">
                                        <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                                        ALERTA REGLAMENTO PNF: Reprobación Individual por Inasistencia (&gt;25%)
                                    </div>
                                    <p class="small mb-0">
                                        Este estudiante acumula <strong>{{ estudianteSeleccionado.porcentaje_inasistencia }}% de inasistencias injustificadas</strong> (asistencia inferior al 75% reglamentario). Esta penalización aplica <strong>exclusivamente a su expediente individual</strong> y no afecta la calificación ni aprobación de sus compañeros de equipo.
                                    </p>
                                </div>

                                <!-- Récord de Asistencia en Puntos de Control -->
                                <h6 class="fw-bold text-dark mb-3">
                                    <i class="bi bi-calendar-check text-danger me-1"></i> Récord de Asistencia en Puntos de Control:
                                </h6>
                                <div v-if="estudianteSeleccionado.asistencias.length === 0" class="text-muted small mb-4 p-3 bg-light rounded text-center">
                                    No se registran asistencias en los cortes de puntos de control.
                                </div>
                                <div v-else class="table-responsive mb-4">
                                    <table class="table table-bordered table-sm align-middle text-center">
                                        <thead class="table-dark">
                                            <tr>
                                                <th>Corte</th>
                                                <th>Fecha</th>
                                                <th>Asistencia</th>
                                                <th>Observación / Justificativo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(asist, i) in estudianteSeleccionado.asistencias" :key="i">
                                                <td class="fw-bold">Semana {{ asist.puc_semana_corte }}</td>
                                                <td>{{ asist.puc_fecha || 'S/F' }}</td>
                                                <td>
                                                    <span v-if="asist.apc_asistio" class="badge bg-success">Asistió</span>
                                                    <span v-else-if="asist.apc_justificacion" class="badge bg-warning text-dark">Justificado</span>
                                                    <span v-else class="badge bg-danger">Inasistente</span>
                                                </td>
                                                <td class="text-start small">{{ asist.apc_observacion || asist.apc_justificacion || 'Sin observaciones' }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Estatus en Pre-Socialización -->
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="bi bi-signpost-split text-danger me-1"></i> Estatus en Pre-Socialización (Semestre Impar):
                                </h6>
                                <div class="p-3 bg-light rounded-3 border mb-4">
                                    <div v-if="estudianteSeleccionado.pre_socializacion">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <strong class="text-dark">Avanza al Semestre Par:</strong>
                                            <span v-if="estudianteSeleccionado.pre_socializacion.iso_avanza" class="badge bg-success fs-6">SÍ AVANZA</span>
                                            <span v-else class="badge bg-danger fs-6">NO AVANZA</span>
                                        </div>
                                        <div class="small text-muted">
                                            {{ estudianteSeleccionado.pre_socializacion.iso_justificacion || 'Cumplió satisfactoriamente con la pre-socialización del proyecto.' }}
                                        </div>
                                    </div>
                                    <div v-else class="text-muted small">
                                        No se registra evaluación previa en pre-socialización para este integrante.
                                    </div>
                                </div>

                                <!-- Calificaciones Previas Registradas -->
                                <h6 class="fw-bold text-dark mb-2">
                                    <i class="bi bi-award text-danger me-1"></i> Calificaciones Previas Asignadas:
                                </h6>
                                <div v-if="estudianteSeleccionado.calificaciones.length === 0" class="text-muted small p-3 bg-light rounded text-center">
                                    Aún no se han registrado calificaciones para este estudiante en este acto.
                                </div>
                                <div v-else class="list-group list-group-flush border rounded-3">
                                    <div
                                        v-for="(calif, idx) in estudianteSeleccionado.calificaciones"
                                        :key="idx"
                                        class="list-group-item p-3 d-flex justify-content-between align-items-center"
                                    >
                                        <div>
                                            <span class="badge bg-dark text-uppercase mb-1">{{ calif.rol_evaluador }}</span>
                                            <div class="fw-bold text-dark">{{ calif.nombre_evaluador }}</div>
                                            <small class="text-muted">{{ calif.observacion_individual || 'Sin nota individual' }}</small>
                                        </div>
                                        <div class="fs-4 fw-bold text-danger font-monospace">
                                            {{ calif.calificacion }} pts
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-else class="card border-0 shadow-sm rounded-4 bg-white p-5 text-center text-muted">
                            <i class="bi bi-person-bounding-box text-secondary fs-1 mb-2"></i>
                            <h5 class="fw-bold text-dark">Seleccione un estudiante</h5>
                            <p class="small mb-0">Haga clic en la lista de la izquierda para visualizar el expediente completo.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- ════════════════════════════════════════════════════════════ -->
            <!-- PESTAÑA 4: FORMULARIO DE CALIFICACIÓN DE SOCIALIZACIÓN -->
            <!-- ════════════════════════════════════════════════════════════ -->
            <section v-if="tabActiva === 'evaluacion'" class="animate-fadeIn">
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div class="card border-0 shadow-lg rounded-4 bg-white overflow-hidden">
                            <!-- Encabezado del Formulario -->
                            <div class="card-header bg-dark text-white p-4 border-bottom border-danger border-4">
                                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                    <div>
                                        <span class="badge bg-danger text-uppercase px-2 py-1 small">Scorecard Oficial UPTP</span>
                                        <h3 class="h4 fw-bold text-white mb-0 mt-1">Calificación de la Socialización Final</h3>
                                        <p class="text-white-50 small mb-0">Escala Oficial PNF: 1 a 20 puntos &bull; Calificación mínima aprobatoria: <strong>16.00 pts</strong></p>
                                    </div>
                                    <div class="bg-black p-3 rounded-4 border border-secondary text-end font-monospace text-warning shadow-sm">
                                        <small class="d-block text-white-50" style="font-size: 0.72rem; letter-spacing: 0.5px;">PROMEDIO DEL EQUIPO</small>
                                        <span class="fs-3 fw-bold">{{ promedioCalculado }} <small class="fs-6 text-white-50">pts</small></span>
                                    </div>
                                </div>
                            </div>

                            <form @submit.prevent="consignarCalificacion" class="p-4 p-md-5">
                                <!-- 1. Identificación del Evaluador -->
                                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                                    <i class="bi bi-person-badge-fill text-danger"></i> 1. Identificación del Evaluador
                                </h5>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark small">Rol del Evaluador <span class="text-danger">*</span></label>
                                        <select v-model="formCalif.rol_evaluador" class="form-select form-select-lg" required>
                                            <option value="jurado">Jurado Evaluador (Ponderación 30%)</option>
                                            <option value="comunidad">Vocero Comunitario (Ponderación 20%)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark small">Nombre y Apellido <span class="text-danger">*</span></label>
                                        <input
                                            v-model="formCalif.nombre_evaluador"
                                            type="text"
                                            class="form-control form-control-lg"
                                            placeholder="Ej: Ing. Pedro Pérez"
                                            required
                                        />
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label fw-bold text-dark small">Cédula de Identidad <span class="text-danger">*</span></label>
                                        <input
                                            v-model="formCalif.cedula_evaluador"
                                            type="text"
                                            class="form-control form-control-lg"
                                            placeholder="Ej: 12345678"
                                            required
                                        />
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark small">Institución / Comunidad Representada</label>
                                        <input
                                            v-model="formCalif.institucion_evaluador"
                                            type="text"
                                            class="form-control form-control-lg"
                                            placeholder="Ej: Dpto. de Informática UPTP / Consejo Comunal La Victoria"
                                        />
                                    </div>
                                </div>

                                <!-- 2. Calificación Individualizada por Estudiante -->
                                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                                    <i class="bi bi-mortarboard-fill text-danger"></i> 2. Calificación Individualizada por Estudiante (1 a 20 pts)
                                </h5>
                                <p class="text-muted small mb-3">
                                    Asigne la nota cuantitativa individual a cada estudiante en función de su defensa oral, dominio del tema y respuestas a las preguntas:
                                </p>

                                <div class="table-responsive mb-4">
                                    <table class="table table-hover table-bordered align-middle">
                                        <thead class="table-dark">
                                            <tr>
                                                <th style="width: 35%;">Estudiante</th>
                                                <th style="width: 25%;" class="text-center">Calificación (1 - 20) <span class="text-danger">*</span></th>
                                                <th style="width: 40%;">Observación Individual (Opcional)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="integ in integrantes_detalles" :key="integ.usu_id">
                                                <td>
                                                    <div class="fw-bold text-dark fs-6">{{ integ.nombre_completo }}</div>
                                                    <div class="small text-muted font-monospace">C.I. {{ integ.cedula }} &bull; Asist.: {{ integ.porcentaje_asistencia ?? 100 }}%</div>
                                                    <span v-if="integ.es_lider" class="badge bg-danger mt-1 me-1">Líder</span>
                                                    <div v-if="integ.reprobado_inasistencia" class="alert alert-danger py-1 px-2 mt-2 mb-0 small fw-bold border-danger">
                                                        <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                        ALERTA PNF: &gt;25% inasistencias (Reprobación individual; no afecta al grupo).
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="input-group input-group-lg justify-content-center">
                                                        <input
                                                            v-model.number="formCalif.calificaciones[integ.usu_id]"
                                                            type="number"
                                                            step="0.5"
                                                            min="1"
                                                            max="20"
                                                            class="form-control text-center fw-bold fs-4 border-2"
                                                            :class="{ 'border-danger text-danger bg-danger-subtle': (formCalif.calificaciones[integ.usu_id] || 0) < 16 || integ.reprobado_inasistencia, 'border-success text-success bg-success-subtle': (formCalif.calificaciones[integ.usu_id] || 0) >= 16 && !integ.reprobado_inasistencia }"
                                                            placeholder="20"
                                                            required
                                                            style="max-width: 140px;"
                                                        />
                                                    </div>
                                                    <div class="small mt-1 fw-bold">
                                                        <span v-if="integ.reprobado_inasistencia" class="badge bg-danger">
                                                            Reprobado por Inasistencia
                                                        </span>
                                                        <span v-else-if="(formCalif.calificaciones[integ.usu_id] || 0) >= 16" class="badge bg-success">
                                                            <i class="bi bi-check-circle-fill me-1"></i>Aprobado (≥ 16 pts)
                                                        </span>
                                                        <span v-else class="badge bg-danger">
                                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>Reprobado (&lt; 16 pts)
                                                        </span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <input
                                                        v-model="formCalif.observaciones_individuales[integ.usu_id]"
                                                        type="text"
                                                        class="form-control"
                                                        placeholder="Comentarios sobre el desempeño individual del estudiante..."
                                                    />
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- 3. Síntesis Cualitativa y Nivel de Logro General -->
                                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom d-flex align-items-center gap-2">
                                    <i class="bi bi-chat-square-quote-fill text-danger"></i> 3. Síntesis y Nivel de Logro Cualitativo de la Socialización
                                </h5>

                                <div class="row g-3 mb-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold text-dark small">Nivel de Logro Institucional <span class="text-danger">*</span></label>
                                        <select v-model="formCalif.nivel_logro_cualitativo" class="form-select form-select-lg" required>
                                            <option value="Logrado con Excelencia">Logrado con Excelencia (18 - 20 pts)</option>
                                            <option value="Logrado">Logrado (16 - 17.99 pts)</option>
                                            <option value="En Proceso">En Proceso (10 - 15.99 pts)</option>
                                            <option value="No Logrado">No Logrado (&lt; 10 pts)</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-dark small">
                                            Observación y Valoración Cualitativa General <span class="text-danger">* (mínimo 15 caracteres)</span>
                                        </label>
                                        <textarea
                                            v-model="formCalif.observacion_cualitativa"
                                            rows="4"
                                            class="form-control form-control-lg"
                                            placeholder="Describa el desenvolvimiento general del equipo, calidad técnica del producto o software, aplicabilidad en la comunidad y solvencia en las respuestas..."
                                            required
                                        ></textarea>
                                        <div class="form-text small" :class="(formCalif.observacion_cualitativa || '').length >= 15 ? 'text-success' : 'text-danger'">
                                            Mínimo 15 caracteres (actual: {{ (formCalif.observacion_cualitativa || '').length }}). Esta valoración se registrará en el Acta Final del Trayecto.
                                        </div>
                                    </div>
                                </div>

                                <!-- Botón de Consignación -->
                                <div class="d-grid mt-4">
                                    <button
                                        type="submit"
                                        class="btn btn-danger btn-lg py-3 fw-bold rounded-pill shadow fs-5"
                                        :disabled="loadingSubmit || minutosRestantesCalc <= 0"
                                    >
                                        <span v-if="loadingSubmit" class="spinner-border spinner-border-sm me-2"></span>
                                        <i v-else class="bi bi-check2-circle me-2"></i>
                                        Consignar Calificación de Socialización
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="bg-white border-top py-3 text-center text-muted small mt-auto">
            <div class="container">
                &copy; {{ new Date().getFullYear() }} SISCON-PST &bull; UPTP "Juan de Jesús Montilla". Enlace temporal emitido para evaluación oficial.
            </div>
        </footer>
    </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    token: String,
    tiempo_restante_texto: String,
    minutos_restantes: Number,
    expira_formateado: String,
    equipo: Object,
    proyecto: Object,
    tutor: Object,
    docente: Object,
    responsable_comunidad: Object,
    historial_eventos: Array,
    puntos_control: Array,
    evaluaciones_semestrales: Array,
    socializaciones: Array,
    integrantes_detalles: Array,
    evaluaciones_token: Array,
});

const tabActiva = ref('evaluacion');
const estudianteSeleccionado = ref(props.integrantes_detalles?.[0] || null);
const loadingSubmit = ref(false);

// Cuenta regresiva de 12 horas en vivo
const segundosRestantes = ref(props.minutos_restantes * 60);
let timerInterval = null;

onMounted(() => {
    timerInterval = setInterval(() => {
        if (segundosRestantes.value > 0) {
            segundosRestantes.value--;
        }
    }, 1000);
});

onUnmounted(() => {
    if (timerInterval) clearInterval(timerInterval);
});

const minutosRestantesCalc = computed(() => {
    return Math.floor(segundosRestantes.value / 60);
});

const tiempoRestanteLocal = computed(() => {
    if (segundosRestantes.value <= 0) return 'Expirado';
    const hrs = Math.floor(segundosRestantes.value / 3600);
    const mins = Math.floor((segundosRestantes.value % 3600) / 60);
    const secs = segundosRestantes.value % 60;
    return `${hrs}h ${mins < 10 ? '0' : ''}${mins}m ${secs < 10 ? '0' : ''}${secs}s`;
});

// Resumen de pre-socialización y socialización final
const preSocGeneral = computed(() => {
    return (props.socializaciones || []).find(s => s.tso_orden === 1);
});

const socFinalGeneral = computed(() => {
    return (props.socializaciones || []).find(s => s.tso_orden === 2);
});

// Formulario de Calificación
const califIniciales = {};
const obsIniciales = {};
(props.integrantes_detalles || []).forEach(integ => {
    // Si ya tiene calificación en este token, precargarla
    const evalPrevia = (props.evaluaciones_token || []).find(e => e.id_estudiante === integ.usu_id);
    califIniciales[integ.usu_id] = evalPrevia ? parseFloat(evalPrevia.calificacion) : 20;
    obsIniciales[integ.usu_id] = evalPrevia ? evalPrevia.observacion_individual : '';
});

const primerEvalToken = props.evaluaciones_token?.[0] || null;

const formCalif = ref({
    rol_evaluador: primerEvalToken?.rol_evaluador || 'jurado',
    nombre_evaluador: primerEvalToken?.nombre_evaluador || '',
    cedula_evaluador: primerEvalToken?.cedula_evaluador || '',
    institucion_evaluador: primerEvalToken?.institucion_evaluador || '',
    calificaciones: califIniciales,
    observaciones_individuales: obsIniciales,
    observacion_cualitativa: primerEvalToken?.observacion_general || '',
    nivel_logro_cualitativo: primerEvalToken?.nivel_logro || 'Logrado con Excelencia',
});

const promedioCalculado = computed(() => {
    const notas = Object.values(formCalif.value.calificaciones);
    if (!notas.length) return '0.00';
    const suma = notas.reduce((acc, curr) => acc + (parseFloat(curr) || 0), 0);
    return (suma / notas.length).toFixed(2);
});

const consignarCalificacion = () => {
    if (segundosRestantes.value <= 0) {
        alert('Este enlace de evaluación ha superado las 12 horas de vigencia y ha expirado.');
        return;
    }

    if (!formCalif.value.observacion_cualitativa || formCalif.value.observacion_cualitativa.trim().length < 15) {
        alert('Por favor ingrese una valoración cualitativa de al menos 15 caracteres.');
        return;
    }

    loadingSubmit.value = true;
    router.post(`/evaluacion-jurado/${props.token}/calificar`, formCalif.value, {
        onSuccess: () => {
            loadingSubmit.value = false;
        },
        onError: () => {
            loadingSubmit.value = false;
        }
    });
};
</script>

<style scoped>
.panel-jurado-page {
    font-family: system-ui, -apple-system, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
}

.logo-uptp {
    height: 52px;
    object-fit: contain;
}

.transition-all {
    transition: all 0.2s ease-in-out;
}

.timer-box {
    min-width: 220px;
}

.pulse-dot {
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.3;
        transform: scale(0.85);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}

.animate-fadeIn {
    animation: fadeIn 0.22s ease-in-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(5px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
