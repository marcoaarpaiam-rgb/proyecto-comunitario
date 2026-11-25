<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
        "
    >
        <!-- SIDEBAR LIDER UNIFICADO -->
        <SidebarLider :sidebar-open="sidebarOpen" @close="sidebarOpen = false" />

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
                            class="bi bi-people me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Mi Equipo
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
                <div style="margin-bottom: 1.25rem">
                    <h4
                        style="
                            font-weight: 800;
                            font-size: 1.25rem;
                            color: var(--text-main);
                            margin: 0;
                        "
                    >
                        Mi Equipo
                    </h4>
                    <p
                        style="
                            color: var(--text-muted);
                            font-size: 0.85rem;
                            margin-top: 2px;
                        "
                    >
                        Integrantes del equipo de proyecto
                    </p>
                </div>

                <!-- Alertas de errores y mensajes flash -->
                <div v-if="$page.props.flash?.error || Object.keys($page.props.errors || {}).length" class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span v-if="$page.props.flash?.error">{{ $page.props.flash.error }}</span>
                    <ul v-else class="mb-0 ps-3">
                        <li v-for="(err, k) in $page.props.errors" :key="k">{{ err }}</li>
                    </ul>
                </div>
                <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ $page.props.flash.success }}
                </div>

                <div v-if="!equipo" class="sc">
                    <div
                        class="p-5 text-center"
                        style="color: var(--text-muted)"
                    >
                        <i class="bi bi-people fs-1 d-block mb-3"></i>No tienes
                        equipo asignado
                    </div>
                </div>

                <div v-else>
                    <!-- BLOQUE 6: Panel de Acompañamiento, Semáforo y Transparencia del Líder -->
                    <div v-if="diagnostico" class="mb-4">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h5 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                                <span>📌</span> Estado del Proyecto y Ruta Crítica
                            </h5>
                        </div>
                        <SemaforoEquipo :diagnostico="diagnostico" />

                        <!-- Acuerdos y compromisos pendientes -->
                        <div v-if="acuerdosPendientes && acuerdosPendientes.length > 0" class="card border-0 shadow-sm mb-3">
                            <div class="card-header bg-warning bg-opacity-10 border-0 py-2 d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-dark small">
                                    <i class="bi bi-list-check me-1 text-warning"></i> Compromisos y Correcciones Pendientes ({{ acuerdosPendientes.length }})
                                </span>
                            </div>
                            <div class="card-body p-2">
                                <ul class="list-group list-group-flush">
                                    <li
                                        v-for="ac in acuerdosPendientes"
                                        :key="ac.acue_id"
                                        class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center border-0 border-bottom"
                                    >
                                        <div>
                                            <div class="small fw-semibold text-dark">{{ ac.acue_descripcion }}</div>
                                            <small v-if="ac.acue_fecha_limite" class="text-muted">
                                                <i class="bi bi-clock me-1"></i> Límite: {{ ac.acue_fecha_limite }}
                                            </small>
                                        </div>
                                        <span :class="ac.acue_estado === 'en_proceso' ? 'badge bg-info text-dark' : 'badge bg-warning text-dark'">
                                            {{ ac.acue_estado === 'en_proceso' ? 'En proceso' : 'Pendiente' }}
                                        </span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <!-- SECCIÓN DE DETALLES POR PUNTO DE CONTROL -->
                        <div class="card border-0 shadow-sm mb-4" style="background: var(--bg-card); border: 1px solid var(--border) !important;">
                            <div class="card-header py-3 d-flex flex-wrap align-items-center justify-content-between gap-3" style="background: var(--bg-card); border-bottom: 1px solid var(--border);">
                                <div>
                                    <h6 class="fw-bold mb-0 d-flex align-items-center gap-2" style="color: var(--text-main);">
                                        <i class="bi bi-geo-alt-fill text-danger fs-5"></i>
                                        <span>Detalle de Puntos de Control y Evaluación</span>
                                    </h6>
                                    <small style="color: var(--text-muted);">
                                        Selecciona un punto de control para consultar sus detalles, retroalimentación docente y tabla de asistencia.
                                    </small>
                                </div>
                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button
                                            type="button"
                                            class="btn btn-sm fw-bold"
                                            :class="tabPuntosLider === 'activos' ? 'btn-danger text-white' : 'btn-outline-secondary'"
                                            @click="cambiarTabPuntos('activos')"
                                        >
                                            <i class="bi bi-clock-history me-1"></i>En Curso / Próximos ({{ puntosActivos.length }})
                                        </button>
                                        <button
                                            type="button"
                                            class="btn btn-sm fw-bold"
                                            :class="tabPuntosLider === 'historial' ? 'btn-danger text-white' : 'btn-outline-secondary'"
                                            @click="cambiarTabPuntos('historial')"
                                        >
                                            <i class="bi bi-check-circle me-1"></i>Historial Cumplido ({{ puntosCumplidos.length }})
                                        </button>
                                    </div>
                                    <select
                                        v-model="puntoControlSeleccionado"
                                        class="form-select form-select-sm fw-semibold"
                                        style="min-width: 250px; background: var(--bg-card); color: var(--text-main); border-color: var(--border);"
                                    >
                                        <option v-if="!puntosVisibles || puntosVisibles.length === 0" value="">
                                            {{ tabPuntosLider === 'activos' ? 'Sin puntos pendientes en curso' : 'Sin puntos cumplidos en historial' }}
                                        </option>
                                        <option v-for="puc in puntosVisibles" :key="puc.puc_id" :value="puc.puc_id">
                                            {{ puc.puc_nombre }} (Semana {{ puc.puc_semana }})
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <!-- Estado cuando no hay puntos en la pestaña activa -->
                            <div v-if="tabPuntosLider === 'activos' && puntosActivos.length === 0" class="p-4 text-center text-muted small">
                                <i class="bi bi-patch-check-fill text-success fs-2 d-block mb-2"></i>
                                <div class="fw-bold fs-6 text-success">¡Excelente trabajo! Todos los puntos de control asignados han sido cumplidos y evaluados.</div>
                                <div class="mt-1">Para revisar retroalimentaciones, observaciones y asistencias de encuentros anteriores, pulsa en el botón <strong>Historial Cumplido</strong> arriba.</div>
                            </div>

                            <!-- Si no hay puntos de control en absoluto -->
                            <div v-else-if="!puntoActual" class="p-4 text-center text-muted small">
                                <i class="bi bi-geo-alt fs-3 d-block mb-2"></i>
                                No hay puntos de control registrados para este equipo en este lapso.
                            </div>

                            <div v-else>
                                <!-- Ficha Resumen del Punto de Control Seleccionado -->
                                <div class="p-3 border-bottom" style="background: rgba(220, 53, 69, 0.03); border-color: var(--border) !important;">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-0" style="color: var(--text-main);">
                                                {{ puntoActual.puc_nombre }}
                                            </h6>
                                            <div class="d-flex flex-wrap align-items-center gap-2 mt-1">
                                                <span class="badge bg-secondary-subtle text-secondary">
                                                    <i class="bi bi-calendar3 me-1"></i>Semana {{ puntoActual.puc_semana }}
                                                </span>
                                                <span class="badge bg-light text-muted border">
                                                    <i class="bi bi-clock me-1"></i>Límite: {{ puntoActual.puc_fecha_limite }}
                                                </span>
                                                <span v-if="puntoActual.puc_fecha_reprogramada" class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                                    <i class="bi bi-arrow-repeat me-1"></i>Reprogramado: {{ puntoActual.puc_fecha_reprogramada }}
                                                </span>
                                            </div>
                                        </div>
                                        <div class="d-flex flex-wrap align-items-center gap-2">
                                            <!-- Estado de cumplimiento -->
                                            <span v-if="puntoActual.seguimiento?.seq_cumplido" class="badge bg-success py-1 px-2">
                                                <i class="bi bi-check-circle-fill me-1"></i>Cumplido {{ puntoActual.seguimiento.seq_cumplido_a_tiempo ? '(A tiempo)' : '(Con retraso)' }}
                                            </span>
                                            <span v-else class="badge bg-warning text-dark py-1 px-2">
                                                <i class="bi bi-hourglass-split me-1"></i>Pendiente de evaluación
                                            </span>

                                            <!-- Enfoque de Gestión de Proyectos y Procesos -->
                                            <span
                                                v-if="puntoActual.seguimiento?.seq_estado_semaforo"
                                                :class="[
                                                    'badge py-1 px-2',
                                                    ['positivo', 'conforme', 'en_ruta'].includes(puntoActual.seguimiento.seq_estado_semaforo) ? 'bg-success' :
                                                    ['alerta', 'neutral', 'observado', 'acelerar'].includes(puntoActual.seguimiento.seq_estado_semaforo) ? 'bg-warning text-dark' :
                                                    ['no_viable', 'inviable'].includes(puntoActual.seguimiento.seq_estado_semaforo) ? 'bg-dark' : 'bg-danger'
                                                ]"
                                                :title="getSignificadoEstado(puntoActual.seguimiento.seq_estado_semaforo)"
                                                style="cursor: help;"
                                            >
                                                <i class="bi bi-circle-fill me-1" style="font-size: 0.6rem;"></i>
                                                Enfoque de Gestión de Proyectos y Procesos: {{ getEtiquetaEstado(puntoActual.seguimiento.seq_estado_semaforo) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Descripción del punto de control -->
                                    <div v-if="puntoActual.puc_descripcion" class="small mb-2" style="color: var(--text-muted);">
                                        {{ puntoActual.puc_descripcion }}
                                    </div>

                                    <!-- Observaciones del docente -->
                                    <div v-if="puntoActual.seguimiento?.seq_observaciones" class="p-2 rounded mt-2" style="background: rgba(13, 110, 253, 0.08); border-left: 3px solid #0d6efd;">
                                        <div class="small fw-bold text-primary mb-1 d-flex align-items-center gap-1">
                                            <i class="bi bi-chat-left-dots-fill"></i>
                                            <span>Observaciones y Retroalimentación Docente:</span>
                                        </div>
                                        <div class="small" style="color: var(--text-main);">
                                            {{ puntoActual.seguimiento.seq_observaciones }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Tabla detallada de asistencias y participación del punto de control -->
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0" style="font-size: 0.83rem; --bs-table-bg: transparent; --bs-table-color: var(--text-main);">
                                        <thead>
                                            <tr style="background: var(--bg-th); color: var(--text-main);">
                                                <th class="ps-3" style="color: var(--text-muted); font-size: 0.78rem;">Integrante</th>
                                                <th style="color: var(--text-muted); font-size: 0.78rem;">Rol</th>
                                                <th style="color: var(--text-muted); font-size: 0.78rem;">Asistencia al Encuentro</th>
                                                <th style="color: var(--text-muted); font-size: 0.78rem;">Participación Activa</th>
                                                <th class="pe-3" style="min-width: 220px; color: var(--text-muted); font-size: 0.78rem;">Traza de Auditoría / Modificación</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="asist in detallesAsistenciaPunto" :key="asist.id" style="border-bottom: 1px solid var(--border);">
                                                <td class="ps-3">
                                                    <div style="font-weight: 700; color: var(--text-main);">{{ asist.nombre }}</div>
                                                    <small style="color: var(--text-muted); font-size: 0.72rem;">C.I. {{ asist.cedula }}</small>
                                                </td>
                                                <td>
                                                    <span v-if="asist.es_lider" class="badge bg-danger-subtle text-danger border border-danger-subtle" style="font-size: 0.68rem;">Líder</span>
                                                    <span v-else class="badge bg-light text-muted border" style="font-size: 0.68rem;">Integrante</span>
                                                </td>
                                                <td>
                                                    <span v-if="!asist.tiene_registro" class="badge bg-secondary-subtle text-secondary">
                                                        <i class="bi bi-hourglass me-1"></i>Pendiente de encuentro
                                                    </span>
                                                    <span v-else-if="asist.asistio" class="badge bg-success">
                                                        <i class="bi bi-check-lg me-1"></i>Presente
                                                    </span>
                                                    <span v-else-if="asist.justificado" class="badge bg-warning text-dark">
                                                        <i class="bi bi-file-earmark-medical me-1"></i>Justificado
                                                    </span>
                                                    <span v-else class="badge bg-danger">
                                                        <i class="bi bi-x-lg me-1"></i>Ausente
                                                    </span>
                                                </td>
                                                <td>
                                                    <span v-if="!asist.tiene_registro" class="text-muted small">—</span>
                                                    <span v-else-if="asist.participo" class="badge bg-primary">
                                                        <i class="bi bi-hand-thumbs-up-fill me-1"></i>Participó
                                                    </span>
                                                    <span v-else class="text-muted small">—</span>
                                                </td>
                                                <td class="pe-3">
                                                    <div v-if="asist.updated_at || asist.motivo_edicion" class="p-1 px-2 rounded bg-warning bg-opacity-10 border border-warning-subtle text-dark" style="font-size: 0.72rem;">
                                                        <div class="d-flex align-items-center gap-1 text-warning-emphasis fw-bold">
                                                            <i class="bi bi-pencil-square"></i>
                                                            <span>Modificada el {{ asist.updated_at }}</span>
                                                        </div>
                                                        <div v-if="asist.motivo_edicion" class="mt-1 small fst-italic">
                                                            {{ asist.motivo_edicion }}
                                                        </div>
                                                    </div>
                                                    <span v-else-if="asist.tiene_registro" class="text-muted small" style="font-size: 0.72rem;">
                                                        <i class="bi bi-check2 text-success me-1"></i>Registro original sin cambios
                                                    </span>
                                                    <span v-else class="text-muted small" style="font-size: 0.72rem;">
                                                        Sin modificaciones
                                                    </span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Acuerdos específicos de este punto de control -->
                                <div v-if="puntoActual.seguimiento?.acuerdos?.length > 0" class="p-3 border-top" style="border-color: var(--border) !important;">
                                    <h6 class="small fw-bold mb-2 d-flex align-items-center gap-1" style="color: var(--text-main);">
                                        <i class="bi bi-list-check text-warning"></i> Compromisos y Acuerdos de este Encuentro ({{ puntoActual.seguimiento.acuerdos.length }})
                                    </h6>
                                    <ul class="list-group list-group-flush">
                                        <li v-for="ac in puntoActual.seguimiento.acuerdos" :key="ac.acue_id" class="list-group-item px-2 py-2 d-flex justify-content-between align-items-center" style="background: transparent; border-color: var(--border);">
                                            <div>
                                                <div class="small fw-semibold" style="color: var(--text-main);">{{ ac.acue_descripcion }}</div>
                                                <small v-if="ac.acue_fecha_limite" style="color: var(--text-muted);">
                                                    <i class="bi bi-clock me-1"></i> Límite: {{ ac.acue_fecha_limite }}
                                                </small>
                                            </div>
                                            <span :class="ac.acue_estado === 'cumplido' ? 'badge bg-success' : (ac.acue_estado === 'en_proceso' ? 'badge bg-info text-dark' : 'badge bg-warning text-dark')">
                                                {{ ac.acue_estado === 'cumplido' ? 'Cumplido' : (ac.acue_estado === 'en_proceso' ? 'En proceso' : 'Pendiente') }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Resumen Acumulado de Asistencias del Equipo -->
                        <div v-if="resumenIntegrantes && resumenIntegrantes.length > 0" class="card border-0 shadow-sm mb-4" style="background: var(--bg-card); border: 1px solid var(--border) !important;">
                            <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between" style="background: var(--bg-card); border-bottom: 1px solid var(--border);">
                                <span class="small fw-bold" style="color: var(--text-main);">
                                    <i class="bi bi-pie-chart-fill text-danger me-1"></i> Asistencia Acumulada por Integrante
                                </span>
                            </div>
                            <div class="card-body p-3" style="background: var(--bg-th);">
                                <div class="row g-2">
                                    <div v-for="int in resumenIntegrantes" :key="int.usu_id" class="col-md-4 col-sm-6">
                                        <div class="p-2 rounded border shadow-sm h-100" style="background: var(--bg-card); border-color: var(--border) !important;">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <div>
                                                    <span class="fw-bold small d-block text-truncate" style="max-width: 140px; color: var(--text-main);">{{ int.nombre }}</span>
                                                    <span style="font-size: 0.72rem; color: var(--text-muted);">C.I. {{ int.cedula }}</span>
                                                </div>
                                                <span v-if="int.es_lider" class="badge bg-danger bg-opacity-10 text-danger border border-danger-subtle" style="font-size: 0.65rem;">Líder</span>
                                                <span v-else class="badge bg-light text-muted border" style="font-size: 0.65rem;">Integrante</span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between mt-2 pt-1 border-top" style="font-size: 0.75rem; border-color: var(--border) !important;">
                                                <span style="color: var(--text-muted);">Asistencia:</span>
                                                <span :class="int.porcentaje_asistencia >= 85 ? 'fw-bold text-success' : (int.porcentaje_asistencia >= 75 ? 'fw-bold text-warning' : 'fw-bold text-danger')">
                                                    {{ int.porcentaje_asistencia }}% ({{ int.asistencias + int.justificados }}/{{ int.total_encuentros }})
                                                </span>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between" style="font-size: 0.72rem; color: var(--text-muted);">
                                                <span>Participaciones:</span>
                                                <span class="fw-semibold text-primary">{{ int.participaciones }} activa(s)</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sc mb-3">
                        <div class="sh">
                            <h6>
                                <i
                                    class="bi bi-info-circle text-danger me-2"
                                ></i
                                >Datos del Equipo
                            </h6>
                        </div>
                        <div class="p-3">
                            <div class="row g-2">
                                <div class="col-md-3">
                                    <div
                                        style="
                                            font-size: 0.78rem;
                                            color: var(--text-muted);
                                            font-weight: 700;
                                        "
                                    >
                                        Código
                                    </div>
                                    <div
                                        class="fw-bold"
                                        style="color: var(--rojo)"
                                    >
                                        {{ equipo.equ_codigo }}
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div
                                        style="
                                            font-size: 0.78rem;
                                            color: var(--text-muted);
                                            font-weight: 700;
                                        "
                                    >
                                        Título
                                    </div>
                                    <div
                                        class="fw-semibold"
                                        style="font-size: 0.9rem"
                                    >
                                        {{ equipo.equ_titulo }}
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div
                                        style="
                                            font-size: 0.78rem;
                                            color: var(--text-muted);
                                            font-weight: 700;
                                        "
                                    >
                                        Sección
                                    </div>
                                    <div>
                                        <span
                                            class="bs"
                                            style="
                                                background: rgba(
                                                    13,
                                                    110,
                                                    253,
                                                    0.1
                                                );
                                                color: #055160;
                                                border: 1px solid
                                                    rgba(13, 110, 253, 0.2);
                                            "
                                            >{{
                                                equipo.seccion?.sec_codigo
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div
                                        style="
                                            font-size: 0.78rem;
                                            color: var(--text-muted);
                                            font-weight: 700;
                                        "
                                    >
                                        Trayecto
                                    </div>
                                    <div style="font-size: 0.88rem">
                                        {{ equipo.trayecto?.tra_nombre }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="sc">
                        <div class="sh">
                            <h6>
                                <i
                                    class="bi bi-people-fill text-danger me-2"
                                ></i
                                >Integrantes
                                <span
                                    class="ms-2"
                                    style="
                                        font-size: 0.82rem;
                                        color: var(--text-muted);
                                        font-weight: 500;
                                    "
                                    >({{ (equipo.integrantes || []).filter(i => i.ein_status !== false).length }} /
                                    6)</span
                                >
                            </h6>
                        </div>
                        <div
                            v-for="int in (equipo.integrantes || []).filter(i => i.ein_status !== false)"
                            :key="int.ein_id"
                            class="miembro-card"
                        >
                            <div
                                class="av"
                                :style="{
                                    background: int.ein_es_lider
                                        ? '#dc3545'
                                        : '#495057',
                                }"
                            >
                                {{ int.usuario?.usu_primer_nombre?.[0]
                                }}{{ int.usuario?.usu_primer_apellido?.[0] }}
                            </div>
                            <div class="flex-grow-1">
                                <div
                                    class="fw-semibold"
                                    style="font-size: 0.92rem"
                                >
                                    {{ int.usuario?.usu_primer_nombre }}
                                    {{ int.usuario?.usu_primer_apellido }}
                                </div>
                                <div
                                    style="
                                        font-size: 0.78rem;
                                        color: var(--text-muted);
                                    "
                                >
                                    CI: {{ int.usuario?.usu_cedula }}
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span v-if="int.ein_es_lider" class="bs bd"
                                    ><i
                                        class="bi bi-star-fill me-1"
                                        style="font-size: 0.65rem"
                                    ></i
                                    >Líder</span
                                >
                                <span v-else class="bs bsec">Integrante</span>
                                <span
                                    v-if="int.usuario?.usu_tiene_acceso"
                                    class="bs ba"
                                    style="font-size: 0.68rem"
                                    ><i class="bi bi-check me-1"></i>Con
                                    acceso</span
                                >
                            </div>
                        </div>
                    </div>
                    <!-- Tutor del proyecto -->
                    <div class="sc mt-3">
                        <div
                            class="sh d-flex align-items-center justify-content-between"
                        >
                            <span
                                ><i
                                    class="bi bi-person-badge me-2 text-danger"
                                ></i
                                >Tutor del Proyecto</span
                            >
                            <button
                                class="btn btn-sm btn-danger rounded-pill fw-bold"
                                @click="mostrarFormTutor = !mostrarFormTutor"
                            >
                                <i class="bi bi-pencil me-1"></i>
                                {{ tutor ? "Cambiar" : "Asignar" }}
                            </button>
                        </div>
                        <div class="p-3">
                            <!-- Tutor actual -->
                            <div v-if="tutor && !mostrarFormTutor">
                                <div class="d-flex align-items-center gap-3">
                                    <div
                                        style="
                                            width: 42px;
                                            height: 42px;
                                            background: #dc3545;
                                            border-radius: 50%;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            color: #fff;
                                            font-weight: 800;
                                            font-size: 1rem;
                                            flex-shrink: 0;
                                        "
                                    >
                                        {{
                                            tutor.usuario
                                                ?.usu_primer_nombre?.[0]
                                        }}{{
                                            tutor.usuario
                                                ?.usu_primer_apellido?.[0]
                                        }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">
                                            {{
                                                tutor.usuario?.usu_primer_nombre
                                            }}
                                            {{
                                                tutor.usuario
                                                    ?.usu_primer_apellido
                                            }}
                                        </div>
                                        <div
                                            class="small"
                                            style="color: var(--text-muted)"
                                        >
                                            C.I.
                                            {{ tutor.usuario?.usu_cedula }} —
                                            Profesor de Proyecto
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Sin tutor -->
                            <div
                                v-else-if="!tutor && !mostrarFormTutor"
                                class="text-center py-3"
                                style="color: var(--text-muted)"
                            >
                                <i
                                    class="bi bi-person-badge fs-3 d-block mb-1"
                                ></i>
                                No hay tutor asignado
                            </div>

                            <!-- Formulario -->
                            <div v-if="mostrarFormTutor">
                                <label class="form-label small fw-bold"
                                    >Selecciona el tutor</label
                                >
                                <select
                                    class="form-select form-select-sm mb-3"
                                    v-model="formTutor.tut_id_usu"
                                >
                                    <option value="">
                                        — Seleccionar profesor —
                                    </option>
                                    <option
                                        v-for="prof in profesoresDisponibles"
                                        :key="prof.usu_id"
                                        :value="prof.usu_id"
                                    >
                                        {{ prof.usu_primer_nombre }}
                                        {{ prof.usu_primer_apellido }} (C.I.
                                        {{ prof.usu_cedula }})
                                    </option>
                                </select>
                                <p
                                    class="small mb-3"
                                    style="color: var(--text-muted)"
                                >
                                    <i class="bi bi-info-circle me-1"></i>
                                    Solo se muestran los profesores del PNF
                                    Informática registrados en el sistema.
                                    Cuando se integre el SOGAC, esta lista se
                                    completará automáticamente.
                                </p>
                                <div class="d-flex gap-2">
                                    <button
                                        class="btn btn-sm btn-danger fw-bold"
                                        @click="guardarTutor"
                                    >
                                        <i class="bi bi-check2 me-1"></i>
                                        Guardar
                                    </button>
                                    <button
                                        class="btn btn-sm btn-outline-secondary"
                                        @click="mostrarFormTutor = false"
                                    >
                                        Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Confirmación Cerrar Sesión -->
        <ModalConfirmar
            ref="modalLogout"
            titulo="Cerrar Sesión"
            mensaje="¿Estás seguro de que deseas cerrar sesión en el sistema?"
            icono="🚪"
            textoConfirmar="Sí, cerrar sesión"
            textoCancelar="Cancelar"
            colorConfirmar="#dc3545"
            @confirmar="ejecutarLogout"
        />
    </div>
</template>
<script setup>
import { ref, computed, onMounted } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import ModalConfirmar from "@/Components/ModalConfirmar.vue";
import SemaforoEquipo from "@/Components/SemaforoEquipo.vue";
import SidebarLider from "@/Components/SidebarLider.vue";

const props = defineProps({
    equipo: Object,
    puntosControl: { type: Array, default: () => [] },
    tutor: Object,
    profesoresDisponibles: Array,
    diagnostico: { type: Object, default: null },
    acuerdosPendientes: { type: Array, default: () => [] },
    misAsistencias: { type: Array, default: () => [] },
    asistenciasGrupo: { type: Array, default: () => [] },
    resumenIntegrantes: { type: Array, default: () => [] },
});
const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("lid-theme") || "light");
const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("lid-theme", tema.value);
};

const tabPuntosLider = ref("activos"); // "activos" | "historial"

const puntosCumplidos = computed(() => {
    return (props.puntosControl || []).filter(
        (p) => p.seguimiento?.seq_cumplido === true
    );
});

const puntosActivos = computed(() => {
    return (props.puntosControl || []).filter(
        (p) => p.seguimiento?.seq_cumplido !== true
    );
});

const puntosVisibles = computed(() => {
    return tabPuntosLider.value === "activos"
        ? puntosActivos.value
        : puntosCumplidos.value;
});

const puntoControlSeleccionado = ref(
    props.puntosControl && props.puntosControl.length > 0
        ? (props.puntosControl.find((p) => p.seguimiento?.seq_cumplido !== true)?.puc_id || props.puntosControl[0].puc_id)
        : ""
);

const cambiarTabPuntos = (tab) => {
    tabPuntosLider.value = tab;
    const lista = tab === "activos" ? puntosActivos.value : puntosCumplidos.value;
    puntoControlSeleccionado.value = lista.length > 0 ? lista[0].puc_id : "";
};

const puntoActual = computed(() => {
    if (!props.puntosControl || props.puntosControl.length === 0) return null;
    return (
        props.puntosControl.find(
            (p) => String(p.puc_id) === String(puntoControlSeleccionado.value)
        ) || (puntosVisibles.value.length > 0 ? puntosVisibles.value[0] : null)
    );
});

const detallesAsistenciaPunto = computed(() => {
    if (!puntoActual.value) return [];

    const seq = puntoActual.value.seguimiento;
    const asistenciasSeq = seq?.asistencias || [];

    const integrantes = (props.equipo?.integrantes || []).filter(
        (i) => i.ein_status !== false
    );

    return integrantes.map((int) => {
        const registro = asistenciasSeq.find(
            (a) => a.apc_id_usu === int.ein_id_usu
        );
        const u = int.usuario;
        return {
            id: registro?.apc_id || `int-${int.ein_id}`,
            usu_id: int.ein_id_usu,
            nombre: u ? `${u.usu_primer_nombre} ${u.usu_primer_apellido}` : "Estudiante",
            cedula: u?.usu_cedula || "S/C",
            es_lider: Boolean(int.ein_es_lider),
            tiene_registro: Boolean(registro),
            asistio: registro ? Boolean(registro.apc_asistio) : null,
            justificado: registro ? Boolean(registro.apc_justificado) : null,
            participo: registro ? Boolean(registro.apc_participo) : null,
            motivo_edicion: registro?.apc_motivo_edicion || null,
            updated_at: registro?.apc_updated_at || null,
        };
    });
});

const modalLogout = ref(null);
const logout = () => {
    modalLogout.value?.abrir();
};
const ejecutarLogout = () => {
    router.post("/logout");
};
const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});
const mostrarFormTutor = ref(false);
const formTutor = ref({ tut_id_usu: props.tutor?.tut_id_usu ?? "" });

const guardarTutor = () => {
    router.post("/lider/tutor", formTutor.value, {
        preserveScroll: true,
        onSuccess: () => {
            mostrarFormTutor.value = false;
        },
    });
};
const getEtiquetaEstado = (tipo) => {
    switch (tipo) {
        case "en_ruta":
        case "conforme":
        case "positivo":
            return "Conforme";
        case "acelerar":
        case "observado":
        case "neutral":
        case "alerta":
            return "Observado";
        case "riesgo_alto":
        case "no_conforme":
        case "negativo":
            return "No conforme";
        case "inviable":
        case "no_viable":
            return "No viable";
        default:
            return tipo;
    }
};

const getSignificadoEstado = (tipo) => {
    switch (tipo) {
        case "en_ruta":
        case "conforme":
        case "positivo":
            return "Cumple con los estándares y metas.";
        case "acelerar":
        case "observado":
        case "neutral":
        case "alerta":
            return "Tiene parametros menores que deben mejorarse.";
        case "riesgo_alto":
        case "no_conforme":
        case "negativo":
            return "Incumplimiento grave de los parámetros del proyecto.";
        case "inviable":
        case "no_viable":
            return "Cierre administrativo por falta de factibilidad.";
        default:
            return "Enfoque de Gestión de Proyectos y Procesos";
    }
};

const notifNoLeidas = ref(0);
onMounted(() => {
    fetch("/notificaciones/lista")
        .then((r) => r.json())
        .then((d) => {
            notifNoLeidas.value = d.filter((n) => !n.not_leida).length;
        })
        .catch(() => {});
});
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
.sh {
    padding: 1rem 1.3rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    gap: 8px;
}
.sh h6 {
    margin: 0;
    font-weight: 800;
    color: var(--text-main);
    font-size: 0.97rem;
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
[data-theme="dark"] .bsec {
    background: #252830;
    color: #f1f3f5;
    border-color: #2d3139;
}
.miembro-card {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 0.85rem 1.3rem;
    border-bottom: 1px solid var(--border);
}
.miembro-card:last-child {
    border-bottom: none;
}
.av {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.85rem;
    color: #fff;
    flex-shrink: 0;
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
