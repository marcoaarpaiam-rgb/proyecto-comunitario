<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body, #f8f9fa);
            color: var(--text-main, #212529);
        "
    >
        <SidebarProfesor
            v-if="esProfesor"
            :sidebar-open="sidebarOpen"
            @close="sidebarOpen = false"
        />
        <SidebarLider
            v-else-if="esLider"
            :sidebar-open="sidebarOpen"
            @close="sidebarOpen = false"
        />
        <SidebarCoordinador
            v-else
            :sidebar-open="sidebarOpen"
            @close="sidebarOpen = false"
        />

        <div class="main">
            <!-- Topbar -->
            <div class="topbar">
                <div class="d-flex align-items-center gap-3">
                    <div class="tb-title">
                        <i class="bi bi-award-fill me-2 text-danger d-none d-sm-inline"></i>
                        Recorrido de Evaluación — {{ proyecto.codigo_equipo }}
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a
                        v-if="!esImpar && proyecto?.id_equipo"
                        :href="`/evaluaciones/planilla-pdf/${proyecto.id_equipo}`"
                        target="_blank"
                        class="btn btn-outline-primary btn-sm rounded-pill px-3"
                    >
                        <i class="bi bi-file-earmark-pdf me-1"></i>Exportar Planilla de Evaluación (PDF)
                    </a>
                    <Link
                        href="/evaluaciones"
                        class="btn btn-outline-secondary btn-sm rounded-pill px-3"
                    >
                        <i class="bi bi-arrow-left me-1"></i> Volver al Listado
                    </Link>
                    <button class="tb-btn" @click="toggleTema" title="Cambiar tema">
                        <i class="bi" :class="tema === 'dark' ? 'bi-sun' : 'bi-moon-stars'"></i>
                    </button>
                </div>
            </div>

            <div class="content p-3 p-md-4">
                <!-- Alertas Flash -->
                <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ $page.props.flash.success }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $page.props.flash.error }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb" class="mb-3">
                    <ol class="breadcrumb small">
                        <li class="breadcrumb-item">
                            <Link href="/evaluaciones" class="text-decoration-none text-muted">Evaluaciones</Link>
                        </li>
                        <li class="breadcrumb-item active text-danger fw-bold" aria-current="page">
                            {{ proyecto.codigo_equipo }}
                        </li>
                    </ol>
                </nav>

                <!-- Ficha Informativa del Proyecto -->
                <div class="card border-0 shadow-sm mb-4 border-start border-danger border-4 rounded-3">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                    <span class="badge bg-danger fs-6 px-3 py-1 rounded-pill">{{ proyecto.codigo_equipo }}</span>
                                    <span class="badge bg-secondary-subtle text-secondary fs-6 px-3 py-1 rounded-pill">{{ proyecto.trayecto_nombre || 'Trayecto' }}</span>
                                    <span class="badge bg-light text-dark border fs-6 px-3 py-1 rounded-pill">Sección {{ proyecto.seccion_codigo || '—' }}</span>
                                    <span
                                        v-if="esImpar"
                                        class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill"
                                    >
                                        Semestre Impar
                                    </span>
                                    <span
                                        v-else
                                        class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill"
                                    >
                                        Semestre Par
                                    </span>
                                </div>
                                <h4 class="fw-bold text-dark mb-1">{{ proyecto.titulo_proyecto }}</h4>
                                <p class="text-muted small mb-3" style="max-width: 850px;">
                                    {{ proyecto.resumen || 'Sin resumen registrado.' }}
                                </p>

                                <div class="d-flex align-items-center gap-4 flex-wrap text-muted small">
                                    <div><i class="bi bi-building text-danger me-1"></i> Comunidad: <strong>{{ proyecto.comunidad_nombre || 'N/A' }}</strong></div>
                                    <div><i class="bi bi-geo-alt text-danger me-1"></i> Ubicación: {{ proyecto.comunidad_ubicacion || 'N/A' }}</div>
                                    <div><i class="bi bi-person-badge text-danger me-1"></i> Docente/Tutor: {{ proyecto.tutor_nombre || 'Docente Asignado' }} {{ proyecto.tutor_apellido || '' }}</div>
                                </div>
                            </div>

                            <!-- Integrantes del Equipo -->
                            <div class="bg-light p-3 rounded border text-start" style="min-width: 260px;">
                                <div class="fw-bold small text-muted text-uppercase mb-2">
                                    <i class="bi bi-people-fill text-danger me-1"></i> Integrantes del Equipo
                                </div>
                                <ul class="list-unstyled mb-0 small">
                                    <li
                                        v-for="integ in integrantes"
                                        :key="integ.cedula || integ.primer_nombre"
                                        class="py-1 border-bottom border-light-subtle d-flex justify-content-between align-items-center gap-2"
                                    >
                                        <span>{{ integ.primer_nombre }} {{ integ.primer_apellido }}</span>
                                        <span
                                            class="badge rounded-pill"
                                            :class="integ.es_lider ? 'bg-danger' : 'bg-secondary'"
                                            style="font-size: 0.65rem;"
                                        >
                                            {{ integ.es_lider ? 'Líder' : 'C.I. ' + integ.cedula }}
                                        </span>
                                    </li>
                                    <li v-if="!integrantes || integrantes.length === 0" class="text-muted small">
                                        Sin integrantes asignados
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Programación de Socialización Final (Defensa) -->
                <div v-if="tieneFechaSocializacion" class="card shadow-sm border-0 mb-3 bg-light border-start border-success border-4 rounded-3">
                    <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                <span class="badge bg-success px-3 py-1 text-uppercase fw-bold">
                                    <i class="bi bi-calendar-check-fill me-1"></i> Fecha de Socialización Final Programada
                                </span>
                                <span v-if="cdEntregado" class="badge bg-dark text-white px-3 py-1 fw-bold">
                                    <i class="bi bi-disc-fill me-1"></i> CD / Informe Entregado
                                </span>
                                <span v-else class="badge bg-warning text-dark px-3 py-1 fw-bold">
                                    <i class="bi bi-exclamation-circle me-1"></i> CD / Informe Pendiente
                                </span>
                            </div>
                            <div class="fs-6 fw-bold text-dark mt-1">
                                <i class="bi bi-calendar-event text-danger me-1"></i> {{ formatearFecha(socializacionFinal?.soc_fecha) }}
                                <span v-if="socializacionFinal?.soc_hora">
                                    &bull; <i class="bi bi-clock text-danger me-1"></i> {{ socializacionFinal.soc_hora }}
                                </span>
                                &bull; <i class="bi bi-geo-alt text-danger me-1"></i> {{ socializacionFinal?.soc_lugar }}
                            </div>
                        </div>
                        <div>
                            <button
                                class="btn btn-sm btn-outline-secondary rounded-pill"
                                type="button"
                                @click="mostrarReprogramar = !mostrarReprogramar"
                            >
                                <i class="bi bi-pencil me-1"></i> Reprogramar Fecha / Lugar / CD
                            </button>
                        </div>
                    </div>

                    <!-- Formulario de Reprogramación -->
                    <div v-if="mostrarReprogramar" class="px-3 pb-3">
                        <form @submit.prevent="guardarSocializacion" class="p-3 bg-white border rounded">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-3">
                                    <label class="form-label fw-bold small">Nueva Fecha <span class="text-danger">*</span></label>
                                    <input type="date" v-model="formSocializacion.fecha" class="form-control form-control-sm" required />
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label fw-bold small">Hora</label>
                                    <input type="time" v-model="formSocializacion.hora" class="form-control form-control-sm" />
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold small">Lugar <span class="text-danger">*</span></label>
                                    <input type="text" v-model="formSocializacion.lugar" class="form-control form-control-sm" required />
                                </div>
                                <div class="col-md-3">
                                    <div class="form-check p-2 border rounded bg-light" style="padding-left: 2rem !important;">
                                        <input class="form-check-input" type="checkbox" v-model="formSocializacion.entrega_cd_informe" id="chkCdEditRecorrido" />
                                        <label class="form-check-label fw-bold small text-dark" for="chkCdEditRecorrido">
                                            Entregó CD / Informe
                                        </label>
                                    </div>
                                </div>
                                <div class="col-12 text-end mt-2">
                                    <button type="submit" class="btn btn-sm btn-danger fw-bold rounded-pill px-3" :disabled="formSocializacion.processing">
                                        <i class="bi bi-save me-1"></i> Guardar Cambios de Programación
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Alerta Bloqueante si Fecha de Socialización Final está Pendiente -->
                <div v-else class="card border-warning shadow-sm mb-4 rounded-3" style="background: #fffdf5; border-left: 6px solid #ffc107 !important;">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start gap-3 flex-wrap">
                            <div class="p-3 bg-warning text-dark rounded-circle fs-3 d-none d-sm-block">
                                <i class="bi bi-calendar-x-fill"></i>
                            </div>
                            <div style="flex: 1; min-width: 280px;">
                                <span class="badge bg-warning text-dark px-3 py-1 mb-2 text-uppercase fw-bold">
                                    Fecha de Socialización Final Pendiente
                                </span>
                                <h5 class="fw-bold text-dark mb-1">
                                    El PIN de Defensa y la Evaluación Final están temporalmente inactivos
                                </h5>
                                <p class="text-muted small mb-3" style="line-height: 1.6;">
                                    Por directriz institucional del PNFI y control estricto de auditoría académica, el <strong>PIN de defensa</strong> para jurados y responsable de la comunidad, así como el <strong>registro de notas de la Socialización Final</strong> (Semestre 2), <strong>solo se habilitan cuando se haya programado y guardado la Fecha y Lugar</strong> del acto de defensa.
                                </p>

                                <!-- Formulario Contextual para Registrar Fecha y Lugar -->
                                <form @submit.prevent="guardarSocializacion" class="p-3 bg-white border rounded shadow-sm">
                                    <div class="row g-3 align-items-end">
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold small text-dark">Fecha de la Socialización Final <span class="text-danger">*</span></label>
                                            <input type="date" v-model="formSocializacion.fecha" class="form-control" required :min="fechaHoy" />
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label fw-bold small text-dark">Hora del Acto</label>
                                            <input type="time" v-model="formSocializacion.hora" class="form-control" />
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold small text-dark">Lugar del Acto <span class="text-danger">*</span></label>
                                            <input type="text" v-model="formSocializacion.lugar" class="form-control" placeholder="Ej: Aula 4 UPTP, Auditorio, Casa Comunal" required />
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-check p-2 border rounded bg-light" style="padding-left: 2rem !important;">
                                                <input class="form-check-input" type="checkbox" v-model="formSocializacion.entrega_cd_informe" id="chkCdNewRecorrido" />
                                                <label class="form-check-label fw-bold small text-dark" for="chkCdNewRecorrido">
                                                    Entregó CD / Informe
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12 text-end mt-3">
                                            <button type="submit" class="btn btn-danger fw-bold px-4 py-2 rounded-pill shadow-sm" :disabled="formSocializacion.processing">
                                                <i class="bi bi-calendar-check-fill me-1"></i>
                                                Guardar Fecha y Habilitar Evaluación Final
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card de Acceso Rápido con PIN para Defensa en Vivo (Solo con fecha guardada) -->
                <div v-if="tieneFechaSocializacion" class="card shadow-sm border-0 mb-4 bg-dark text-white rounded-3 overflow-hidden">
                    <div class="card-body p-3 p-md-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="p-3 bg-danger bg-opacity-25 rounded-circle text-danger fs-2 d-none d-sm-block">
                                <i class="bi bi-key-fill"></i>
                            </div>
                            <div>
                                <span class="badge bg-danger px-2 py-1 small text-uppercase">Acceso Rápido con PIN — Socialización Final</span>
                                <div class="d-flex align-items-baseline gap-2 mt-1">
                                    <span class="fs-1 fw-bold text-warning font-monospace" style="letter-spacing: 4px;">
                                        {{ proyecto.pin_defensa || '123456' }}
                                    </span>
                                    <button class="btn btn-sm btn-outline-light border-0 text-white-50" @click="copiarPin" title="Copiar PIN">
                                        <i class="bi bi-clipboard"></i>
                                    </button>
                                </div>
                                <div class="text-white-50 small">
                                    <template v-if="proyecto.token_jurado">
                                        Enlace temporal de 12 horas activo para Jurados: 
                                        <a :href="`/evaluacion-jurado/${proyecto.token_jurado}`" target="_blank" class="text-warning text-decoration-underline fw-bold">
                                            /evaluacion-jurado/{{ proyecto.token_jurado.substring(0, 12) }}...
                                        </a>
                                    </template>
                                    <template v-else>
                                        Comparta este PIN con los jurados para calificar en tiempo real durante la socialización.
                                    </template>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button
                                type="button"
                                class="btn btn-sm btn-outline-light px-3 rounded-pill"
                                @click="regenerarPin"
                            >
                                <i class="bi bi-arrow-repeat me-1"></i> Regenerar PIN
                            </button>
                            <a
                                v-if="proyecto.token_jurado"
                                :href="`/evaluacion-jurado/${proyecto.token_jurado}`"
                                target="_blank"
                                class="btn btn-sm btn-danger px-3 fw-bold rounded-pill"
                            >
                                <i class="bi bi-box-arrow-up-right me-1"></i> Abrir Panel Jurado (12h)
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Línea de Tiempo y Recorrido de los 2 Semestres -->
                <div class="row g-4 mb-4">
                    <div v-for="sem in semestres" :key="sem.id_recorrido" class="col-lg-6">
                        <div
                            class="card border-0 shadow-sm h-100 rounded-3 border-top border-4"
                            :class="sem.estado_semestre === 'Culminado' ? 'border-success' : 'border-danger'"
                        >
                            <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="badge bg-danger rounded-pill px-3 py-1 mb-1">{{ sem.semestre }}</span>
                                    <h5 class="fw-bold m-0 text-dark">
                                        Ponderación en Trayecto: <span class="text-danger">{{ sem.semestre === 'Semestre 1' ? '40%' : '60%' }}</span>
                                    </h5>
                                </div>
                                <div>
                                    <span
                                        v-if="sem.estado_semestre === 'Culminado'"
                                        class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold"
                                    >
                                        <i class="bi bi-check-circle-fill me-1"></i> Culminado
                                    </span>
                                    <span
                                        v-else-if="sem.estado_semestre === 'En Evaluacion'"
                                        class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-bold"
                                    >
                                        <i class="bi bi-hourglass-split me-1"></i> En Evaluación
                                    </span>
                                    <span
                                        v-else
                                        class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-bold"
                                    >
                                        <i class="bi bi-clock me-1"></i> En Curso
                                    </span>
                                </div>
                            </div>

                            <div class="card-body p-4">
                                <!-- Calificación Semestral Cuantitativa -->
                                <div class="bg-light p-3 rounded mb-4 border d-flex justify-content-between align-items-center">
                                    <div>
                                        <div class="small text-muted text-uppercase fw-bold">Nota Cuantitativa del {{ sem.semestre }}</div>
                                        <div class="text-muted small">
                                            {{ sem.semestre === 'Semestre 1' ? 'Docente (60%) + Comunidad (40%)' : 'Docente (40%) + Jurado (30%) + Comunidad (20%) + Estudiante (10%)' }}
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <span
                                            class="fs-3 fw-bold"
                                            :class="Number(sem.nota_cuantitativa_semestre) >= 16 ? 'text-success' : (Number(sem.nota_cuantitativa_semestre) > 0 ? 'text-danger' : 'text-muted')"
                                        >
                                            {{ formatearNota(sem.nota_cuantitativa_semestre) }}
                                        </span>
                                        <span class="text-muted small">/ 20.00 pts</span>
                                    </div>
                                </div>

                                <!-- Roles Evaluadores según Semestre -->
                                <h6 class="fw-bold small text-muted text-uppercase mb-3">
                                    <i class="bi bi-person-check-fill text-danger me-1"></i> Actores Evaluadores ({{ sem.semestre === 'Semestre 1' ? '2 Actores' : '4 Actores Oficiales UPTP' }})
                                </h6>

                                <!-- SEMESTRE 1 -->
                                <div v-if="sem.semestre === 'Semestre 1'" class="d-flex flex-column gap-3 mb-4">
                                    <!-- 1. Docente / Tutor S1 -->
                                    <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <div class="fw-bold text-dark">
                                                <i class="bi bi-mortarboard text-danger me-2"></i>Docente de Proyecto / Tutor
                                                <span class="badge bg-light text-dark border ms-1 small">Peso: 60%</span>
                                            </div>
                                            <div class="small text-muted mt-1">
                                                <template v-if="getEval(sem, 'docente_proyecto') || getEval(sem, 'tutor')">
                                                    <span class="badge bg-success-subtle text-success me-1">✓ Evaluado</span>
                                                    Calificación: <strong>{{ formatearNota((getEval(sem, 'docente_proyecto') || getEval(sem, 'tutor')).subtotal_cuantitativo) }} pts</strong>
                                                    &bull; Nivel: <em>{{ (getEval(sem, 'docente_proyecto') || getEval(sem, 'tutor')).nivel_logro_cualitativo }}</em>
                                                </template>
                                                <template v-else>
                                                    <span class="badge bg-secondary-subtle text-secondary">Pendiente de evaluación</span>
                                                </template>
                                            </div>
                                        </div>
                                        <div v-if="!esCoordinador">
                                            <Link
                                                :href="`/evaluaciones/evaluar/${sem.id_recorrido}/docente_proyecto`"
                                                class="btn btn-sm rounded-pill px-3"
                                                :class="(getEval(sem, 'docente_proyecto') || getEval(sem, 'tutor')) ? 'btn-outline-warning' : 'btn-danger'"
                                            >
                                                <i class="bi" :class="(getEval(sem, 'docente_proyecto') || getEval(sem, 'tutor')) ? 'bi-pencil' : 'bi-clipboard-check'"></i>
                                                {{ (getEval(sem, 'docente_proyecto') || getEval(sem, 'tutor')) ? ' Modificar' : ' Evaluar Rúbrica' }}
                                            </Link>
                                        </div>
                                    </div>

                                    <!-- 2. Comunidad S1 -->
                                    <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                                        <div>
                                            <div class="fw-bold text-dark">
                                                <i class="bi bi-people text-danger me-2"></i>Responsable de la Comunidad
                                                <span class="badge bg-light text-dark border ms-1 small">Peso: 40%</span>
                                            </div>
                                            <div class="small text-muted mt-1">
                                                <template v-if="getEval(sem, 'comunidad')">
                                                    <span class="badge bg-success-subtle text-success me-1">✓ Evaluado</span>
                                                    Calificación: <strong>{{ formatearNota(getEval(sem, 'comunidad').subtotal_cuantitativo) }} pts</strong>
                                                    &bull; Nivel: <em>{{ getEval(sem, 'comunidad').nivel_logro_cualitativo }}</em>
                                                </template>
                                                <template v-else>
                                                    <span class="badge bg-secondary-subtle text-secondary">Pendiente de evaluación</span>
                                                </template>
                                            </div>
                                        </div>
                                        <div v-if="!esCoordinador">
                                            <Link
                                                :href="`/evaluaciones/evaluar/${sem.id_recorrido}/comunidad`"
                                                class="btn btn-sm rounded-pill px-3"
                                                :class="getEval(sem, 'comunidad') ? 'btn-outline-warning' : 'btn-danger'"
                                            >
                                                <i class="bi" :class="getEval(sem, 'comunidad') ? 'bi-pencil' : 'bi-clipboard-check'"></i>
                                                {{ getEval(sem, 'comunidad') ? ' Modificar' : ' Evaluar Rúbrica' }}
                                            </Link>
                                        </div>
                                    </div>

                                    <!-- Botón de Transición Semestral al culminar Semestre 1 -->
                                    <div v-if="sem.estado_semestre === 'Culminado' && esImpar" class="p-3 bg-primary-subtle rounded border border-primary mt-2 text-center">
                                        <div class="fw-bold text-primary mb-1">
                                            <i class="bi bi-arrow-right-circle me-1"></i> ¡Semestre 1 Culminado Exitosamente!
                                        </div>
                                        <p class="small text-muted mb-2">
                                            El equipo ha completado sus evaluaciones del Semestre 1. Puede promover automáticamente al equipo a la sección del Semestre Par (+1 al código).
                                        </p>
                                        <button
                                            type="button"
                                            class="btn btn-primary btn-sm fw-bold px-3 rounded-pill"
                                            @click="promoverSemestrePar"
                                        >
                                            <i class="bi bi-rocket-takeoff-fill me-1"></i> Promover a Semestre Par (Transición Semestral)
                                        </button>
                                    </div>
                                </div>

                                <!-- SEMESTRE 2 -->
                                <div v-else class="d-flex flex-column gap-3 mb-4">
                                    <div v-if="!tieneFechaSocializacion" class="p-4 rounded border bg-light text-center my-3">
                                        <i class="bi bi-lock-fill text-warning fs-1 d-block mb-2"></i>
                                        <h6 class="fw-bold text-dark">Evaluación Final del Semestre 2 Inactiva</h6>
                                        <p class="small text-muted mb-0">
                                            Para habilitar el registro de notas por rúbrica de los 4 actores y el PIN de jurado/comunidad, debe programar y guardar la <strong>Fecha y Lugar de la Socialización Final</strong> en el formulario superior.
                                        </p>
                                    </div>

                                    <template v-else>
                                        <!-- 1. Docente de Proyecto (40%) -->
                                        <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    <i class="bi bi-mortarboard text-danger me-2"></i>Docente de Proyecto
                                                    <span class="badge bg-light text-dark border ms-1 small">Peso: 40%</span>
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    <template v-if="getEval(sem, 'docente_proyecto') || getEval(sem, 'tutor')">
                                                        <span class="badge bg-success-subtle text-success me-1">✓ Evaluado</span>
                                                        Calificación: <strong>{{ formatearNota((getEval(sem, 'docente_proyecto') || getEval(sem, 'tutor')).subtotal_cuantitativo) }} pts</strong>
                                                        &bull; Nivel: <em>{{ (getEval(sem, 'docente_proyecto') || getEval(sem, 'tutor')).nivel_logro_cualitativo }}</em>
                                                    </template>
                                                    <template v-else>
                                                        <span class="badge bg-secondary-subtle text-secondary">Pendiente de evaluación</span>
                                                    </template>
                                                </div>
                                            </div>
                                            <div v-if="!esCoordinador">
                                                <Link
                                                    :href="`/evaluaciones/evaluar/${sem.id_recorrido}/docente_proyecto`"
                                                    class="btn btn-sm rounded-pill px-3"
                                                    :class="(getEval(sem, 'docente_proyecto') || getEval(sem, 'tutor')) ? 'btn-outline-warning' : 'btn-danger'"
                                                >
                                                    <i class="bi" :class="(getEval(sem, 'docente_proyecto') || getEval(sem, 'tutor')) ? 'bi-pencil' : 'bi-clipboard-check'"></i>
                                                    {{ (getEval(sem, 'docente_proyecto') || getEval(sem, 'tutor')) ? ' Modificar' : ' Evaluar Rúbrica' }}
                                                </Link>
                                            </div>
                                        </div>

                                        <!-- 2. Jurado Evaluador (30%) -->
                                        <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    <i class="bi bi-award text-danger me-2"></i>Jurado Evaluador (Socialización Final)
                                                    <span class="badge bg-light text-dark border ms-1 small">Peso: 30%</span>
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    <template v-if="getEval(sem, 'jurado')">
                                                        <span class="badge bg-success-subtle text-success me-1">✓ Evaluado</span>
                                                        Calificación: <strong>{{ formatearNota(sem.nota_jurado || getEval(sem, 'jurado').subtotal_cuantitativo) }} pts</strong>
                                                        &bull; Nivel: <em>{{ getEval(sem, 'jurado').nivel_logro_cualitativo }}</em>
                                                    </template>
                                                    <template v-else>
                                                        <span class="badge bg-secondary-subtle text-secondary">Pendiente de evaluación</span>
                                                    </template>
                                                </div>
                                            </div>
                                            <div v-if="!esCoordinador">
                                                <Link
                                                    :href="`/evaluaciones/evaluar/${sem.id_recorrido}/jurado`"
                                                    class="btn btn-sm rounded-pill px-3"
                                                    :class="getEval(sem, 'jurado') ? 'btn-outline-warning' : 'btn-danger'"
                                                >
                                                    <i class="bi" :class="getEval(sem, 'jurado') ? 'bi-pencil' : 'bi-clipboard-check'"></i>
                                                    {{ getEval(sem, 'jurado') ? ' Modificar' : ' Evaluar Rúbrica' }}
                                                </Link>
                                            </div>
                                        </div>

                                        <!-- 3. Representante de la Comunidad (20%) -->
                                        <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    <i class="bi bi-people text-danger me-2"></i>Representante de la Comunidad
                                                    <span class="badge bg-light text-dark border ms-1 small">Peso: 20%</span>
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    <template v-if="getEval(sem, 'comunidad')">
                                                        <span class="badge bg-success-subtle text-success me-1">✓ Evaluado</span>
                                                        Calificación: <strong>{{ formatearNota(getEval(sem, 'comunidad').subtotal_cuantitativo) }} pts</strong>
                                                        &bull; Nivel: <em>{{ getEval(sem, 'comunidad').nivel_logro_cualitativo }}</em>
                                                    </template>
                                                    <template v-else>
                                                        <span class="badge bg-secondary-subtle text-secondary">Pendiente de evaluación</span>
                                                    </template>
                                                </div>
                                            </div>
                                            <div v-if="!esCoordinador">
                                                <Link
                                                    :href="`/evaluaciones/evaluar/${sem.id_recorrido}/comunidad`"
                                                    class="btn btn-sm rounded-pill px-3"
                                                    :class="getEval(sem, 'comunidad') ? 'btn-outline-warning' : 'btn-danger'"
                                                >
                                                    <i class="bi" :class="getEval(sem, 'comunidad') ? 'bi-pencil' : 'bi-clipboard-check'"></i>
                                                    {{ getEval(sem, 'comunidad') ? ' Modificar' : ' Evaluar Rúbrica' }}
                                                </Link>
                                            </div>
                                        </div>

                                        <!-- 4. Autoevaluación Estudiantil (10%) -->
                                        <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div>
                                                <div class="fw-bold text-dark">
                                                    <i class="bi bi-person-check text-success me-2"></i>Autoevaluación y Coevaluación Estudiantil
                                                    <span class="badge bg-light text-dark border ms-1 small">Peso: 10%</span>
                                                </div>
                                                <div class="small text-muted mt-1">
                                                    <template v-if="getEval(sem, 'estudiante')">
                                                        <span class="badge bg-success-subtle text-success me-1">✓ Evaluado</span>
                                                        Calificación: <strong>{{ formatearNota(getEval(sem, 'estudiante').subtotal_cuantitativo) }} pts</strong>
                                                        &bull; Nivel: <em>{{ getEval(sem, 'estudiante').nivel_logro_cualitativo }}</em>
                                                    </template>
                                                    <template v-else>
                                                        <span class="badge bg-secondary-subtle text-secondary">Pendiente de registro grupal</span>
                                                    </template>
                                                </div>
                                            </div>
                                            <div v-if="!esCoordinador">
                                                <Link
                                                    :href="`/evaluaciones/evaluar/${sem.id_recorrido}/estudiante`"
                                                    class="btn btn-sm rounded-pill px-3"
                                                    :class="getEval(sem, 'estudiante') ? 'btn-outline-warning' : 'btn-success'"
                                                >
                                                    <i class="bi" :class="getEval(sem, 'estudiante') ? 'bi-pencil' : 'bi-clipboard-check'"></i>
                                                    {{ getEval(sem, 'estudiante') ? ' Modificar' : ' Cargar Autoevaluación' }}
                                                </Link>
                                            </div>
                                        </div>
                                    </template>
                                </div>

                                <!-- Síntesis Cualitativa del Semestre -->
                                <div class="border rounded p-3 bg-light">
                                    <div class="small fw-bold text-dark mb-1">
                                        <i class="bi bi-journal-text text-danger me-1"></i> Síntesis Cualitativa del {{ sem.semestre }}
                                    </div>
                                    <p class="small text-muted m-0" style="white-space: pre-wrap;">
                                        {{ sem.valoracion_cualitativa_semestre || 'La valoración cualitativa se generará automáticamente en cuanto los evaluadores consoliden sus rúbricas.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Expediente Individual por Estudiante (Reglamento PNF) -->
                <div v-if="metricasIndividuales && metricasIndividuales.length" class="card border-0 shadow-sm mb-4 rounded-3 border-dark">
                    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-person-lines-fill text-danger fs-5"></i>
                            <h5 class="fw-bold m-0 text-white">Expediente Individual por Estudiante (Reglamento PNF)</h5>
                        </div>
                        <span class="badge bg-danger px-3 py-2 rounded-pill">
                            Mínimo Aprobatorio: 16.00 pts &bull; Límite de Inasistencias: 25%
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light small text-uppercase">
                                    <tr>
                                        <th class="ps-4">Cédula</th>
                                        <th>Estudiante</th>
                                        <th class="text-center">Asistencia Puntos de Control</th>
                                        <th class="text-center">Nota Individual</th>
                                        <th class="pe-4">Condición Reglamentaria PNF</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="m in metricasIndividuales"
                                        :key="m.cedula"
                                        :class="{ 'table-danger': m.reprobado_inasistencia }"
                                    >
                                        <td class="ps-4 font-monospace fw-bold">V-{{ m.cedula }}</td>
                                        <td>
                                            <span class="fw-bold text-dark">{{ m.nombre_completo }}</span>
                                            <span v-if="m.es_lider" class="badge bg-danger ms-1" style="font-size: 0.7rem;">Líder</span>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                class="badge px-3 py-1"
                                                :class="m.reprobado_inasistencia ? 'bg-danger' : 'bg-success'"
                                            >
                                                {{ Number(m.porcentaje_asistencia || 100).toFixed(1) }}% Asistencia
                                            </span>
                                            <div class="small text-muted mt-1">
                                                Faltas injustificadas: {{ Number(m.porcentaje_inasistencia || 0).toFixed(1) }}%
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span
                                                v-if="m.nota_individual !== null && m.nota_individual !== undefined"
                                                class="fs-5 fw-bold"
                                                :class="(Number(m.nota_individual) >= 16 && !m.reprobado_inasistencia) ? 'text-success' : 'text-danger'"
                                            >
                                                {{ formatearNota(m.nota_individual) }} / 20
                                            </span>
                                            <span v-else class="text-muted small">Pendiente</span>
                                        </td>
                                        <td class="pe-4">
                                            <div v-if="m.reprobado_inasistencia" class="text-danger fw-bold small">
                                                <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                                ALERTA PNF: Reprobado individualmente por inasistencia (&gt;25% faltas injustificadas). No afecta la nota ni aprobación de sus compañeros.
                                            </div>
                                            <span
                                                v-else-if="m.aprobado_individual === true"
                                                class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-bold"
                                            >
                                                <i class="bi bi-check-circle-fill me-1"></i> {{ m.condicion_pnf }}
                                            </span>
                                            <span
                                                v-else-if="m.aprobado_individual === false"
                                                class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 fw-bold"
                                            >
                                                <i class="bi bi-x-circle-fill me-1"></i> {{ m.condicion_pnf }}
                                            </span>
                                            <span v-else class="badge bg-light text-dark border px-3 py-1">
                                                {{ m.condicion_pnf || 'En Curso' }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Consolidación Definitiva y Acta Final del Trayecto -->
                <div class="card border-0 shadow-sm border-top border-danger border-4 rounded-3 mb-4">
                    <div class="card-header bg-danger text-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-patch-check-fill fs-5"></i>
                            <h5 class="fw-bold m-0 text-white">Consolidación Definitiva y Acta Final del Trayecto</h5>
                        </div>
                        <div>
                            <span v-if="acta && acta.cerrada" class="badge bg-white text-danger px-3 py-2 rounded-pill fw-bold">
                                <i class="bi bi-shield-lock-fill me-1"></i> ACTA CERRADA Y REGISTRADA
                            </span>
                            <span v-else class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                                <i class="bi bi-hourglass-split me-1"></i> En Espera de Culminación de Semestres
                            </span>
                        </div>
                    </div>

                    <div class="card-body p-4">
                        <template v-if="acta && acta.cerrada">
                            <div class="row align-items-center g-4">
                                <div class="col-md-3 text-center border-end">
                                    <div class="small text-muted text-uppercase fw-bold mb-1">Calificación Definitiva</div>
                                    <div class="display-4 fw-bold text-danger">
                                        {{ formatearNota(acta.calificacion_definitiva_cuantitativa) }}
                                    </div>
                                    <div class="text-muted small">sobre 20.00 pts (Aprobatorio &ge; 16.00)</div>
                                </div>

                                <div class="col-md-5">
                                    <div class="small text-muted text-uppercase fw-bold mb-1">Veredicto y Mención Oficial</div>
                                    <div class="fs-5 fw-bold text-dark mb-2">
                                        <span
                                            class="badge px-3 py-2 rounded-pill"
                                            :class="Number(acta.calificacion_definitiva_cuantitativa) >= 16 ? 'bg-success' : 'bg-danger'"
                                        >
                                            {{ acta.calificacion_definitiva_cualitativa }}
                                        </span>
                                    </div>
                                    <div class="small text-muted">
                                        Fórmula institucional: <strong>(S1 &times; 40%) + (S2 &times; 60%)</strong>
                                        <br />
                                        S1: {{ formatearNota(acta.nota_semestre_1) }} pts &bull; S2: {{ formatearNota(acta.nota_semestre_2) }} pts
                                    </div>
                                </div>

                                <div class="col-md-4 text-md-end d-flex flex-column gap-2">
                                    <a
                                        :href="`/evaluaciones/pdf/${proyecto.id_proyecto}`"
                                        target="_blank"
                                        class="btn btn-danger shadow-sm fw-bold rounded-pill"
                                    >
                                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Descargar Acta Oficial PDF
                                    </a>

                                    <!-- Enviar a Repositorio Institucional -->
                                    <button
                                        type="button"
                                        class="btn btn-outline-dark fw-bold rounded-pill"
                                        @click="enviarRepositorio"
                                    >
                                        <i class="bi bi-archive-fill me-1"></i> Enviar a Repositorio Institucional
                                    </button>

                                    <!-- Exportar Planilla de Vinculación -->
                                    <a
                                        :href="`/excel/vinculacion/${proyecto.id_proyecto}`"
                                        class="btn btn-outline-success fw-bold rounded-pill"
                                    >
                                        <i class="bi bi-file-earmark-excel-fill me-1"></i> Exportar Planilla de Vinculación (.xlsx)
                                    </a>
                                </div>
                            </div>

                            <!-- Código Hash de Verificación -->
                            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="small text-muted font-monospace text-break">
                                    <i class="bi bi-fingerprint me-1"></i> Hash SHA-256: <strong>{{ acta.hash_verificacion }}</strong>
                                </div>
                                <div>
                                    <a
                                        :href="`/evaluaciones/verificar/${acta.hash_verificacion}`"
                                        target="_blank"
                                        class="btn btn-sm btn-outline-secondary rounded-pill"
                                    >
                                        <i class="bi bi-search me-1"></i> Verificar Autenticidad Online
                                    </a>
                                </div>
                            </div>
                        </template>

                        <div v-else class="text-center py-4 text-muted">
                            <i class="bi bi-info-circle fs-1 text-danger d-block mb-3"></i>
                            <h6 class="fw-bold text-dark">El Acta Definitiva se generará automáticamente</h6>
                            <p class="small mx-auto mb-3" style="max-width: 600px;">
                                Para realizar el cierre institucional del trayecto, el <strong>Semestre 1 (40%)</strong> y el <strong>Semestre 2 (60%)</strong> deben completar todas sus evaluaciones oficiales.
                            </p>
                            <div class="d-inline-flex gap-2">
                                <a
                                    :href="`/excel/vinculacion/${proyecto.id_proyecto}`"
                                    class="btn btn-sm btn-outline-success rounded-pill"
                                >
                                    <i class="bi bi-file-earmark-excel me-1"></i> Descargar Planilla de Vinculación Preliminar
                                </a>
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
import { Link, usePage, useForm, router } from "@inertiajs/vue3";
import SidebarProfesor from "@/Components/SidebarProfesor.vue";
import SidebarCoordinador from "@/Components/SidebarCoordinador.vue";
import SidebarLider from "@/Components/SidebarLider.vue";

const props = defineProps({
    proyecto: Object,
    semestres: Array,
    acta: Object,
    integrantes: Array,
    rubricas: Object,
    socializacionFinal: Object,
    tieneFechaSocializacion: Boolean,
    metricasIndividuales: Array,
});

const page = usePage();
const sidebarOpen = ref(false);

const rolUsuario = computed(() => {
    const r =
        page.props.auth?.rol ||
        page.props.auth?.user?.usu_rol ||
        page.props.auth?.user?.rol ||
        page.props.auth?.user?.roles?.[0] ||
        "";
    return String(r).toLowerCase();
});

const esProfesor = computed(() => {
    return (
        rolUsuario.value.includes("profesor") ||
        rolUsuario.value.includes("docente") ||
        window.location.pathname.startsWith("/profesor")
    );
});

const esLider = computed(() => {
    return (
        rolUsuario.value.includes("lider") ||
        rolUsuario.value.includes("estudiante") ||
        window.location.pathname.startsWith("/lider")
    );
});

const esCoordinador = computed(() => {
    return !esProfesor.value && !esLider.value;
});

const tema = ref(
    localStorage.getItem(esProfesor.value ? "prof-theme" : "coord-theme") || "light"
);

const toggleTema = () => {
    const k = esProfesor.value ? "prof-theme" : "coord-theme";
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem(k, tema.value);
};

const esImpar = computed(() => {
    const primerDigito = parseInt(String(props.proyecto?.seccion_codigo || "0").charAt(0), 10);
    return primerDigito % 2 !== 0;
});

const fechaHoy = new Date().toISOString().split("T")[0];

const cdEntregado = computed(() => {
    return (props.socializacionFinal?.soc_observaciones || "").includes("[CD/INFORME ENTREGADO]");
});

const mostrarReprogramar = ref(false);

const formSocializacion = useForm({
    fecha: props.socializacionFinal?.soc_fecha || "",
    hora: props.socializacionFinal?.soc_hora || "",
    lugar: props.socializacionFinal?.soc_lugar || "",
    entrega_cd_informe: cdEntregado.value,
});

const guardarSocializacion = () => {
    formSocializacion.post(`/evaluaciones/socializacion-final/${props.proyecto.id_proyecto}`, {
        onSuccess: () => {
            mostrarReprogramar.value = false;
        },
    });
};

const regenerarPin = () => {
    if (confirm("¿Desea regenerar el PIN de defensa para este proyecto?")) {
        router.post(`/evaluaciones/pin/${props.proyecto.id_proyecto}`);
    }
};

const promoverSemestrePar = () => {
    if (confirm("¿Promover el equipo a la sección par y congelar el historial del Semestre 1?")) {
        router.post(`/evaluaciones/transicion/${props.proyecto.id_equipo}`);
    }
};

const enviarRepositorio = () => {
    if (confirm("¿Archivar y enviar este proyecto al Repositorio Institucional?")) {
        router.post(`/evaluaciones/repositorio/${props.proyecto.id_equipo}`);
    }
};

const copiarPin = () => {
    if (props.proyecto?.pin_defensa) {
        navigator.clipboard.writeText(String(props.proyecto.pin_defensa));
        alert("PIN copiado al portapapeles: " + props.proyecto.pin_defensa);
    }
};

const getEval = (semestre, rol) => {
    if (!semestre || !semestre.evaluaciones) return null;
    if (Array.isArray(semestre.evaluaciones)) {
        return semestre.evaluaciones.find((e) => e.rol_evaluador === rol) || null;
    }
    return semestre.evaluaciones[rol] || null;
};

const formatearNota = (val) => {
    if (val === null || val === undefined || isNaN(val)) return "0.00";
    return Number(val).toFixed(2);
};

const formatearFecha = (val) => {
    if (!val) return "—";
    const parts = String(val).split("T")[0].split("-");
    if (parts.length === 3) {
        return `${parts[2]}/${parts[1]}/${parts[0]}`;
    }
    return val;
};
</script>

<style scoped>
.main {
    margin-left: 250px;
    min-height: 100vh;
    background: var(--bg-body, #f8f9fa);
    color: var(--text-main, #212529);
    transition: background 0.2s, color 0.2s;
}
.topbar {
    height: 65px;
    background: var(--bg-topbar, #fff);
    border-bottom: 1px solid var(--border, #dee2e6);
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1.5rem;
    position: sticky;
    top: 0;
    z-index: 1020;
    transition: background 0.2s, border-color 0.2s;
}
.tb-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--text-main, #343a40);
}
.tb-btn {
    background: none;
    border: none;
    color: var(--text-muted, #6c757d);
    font-size: 1.2rem;
    cursor: pointer;
    padding: 4px;
    transition: color 0.2s;
}
.tb-btn:hover {
    color: #dc3545;
}
@media (max-width: 991.98px) {
    .main {
        margin-left: 0;
    }
}
</style>
