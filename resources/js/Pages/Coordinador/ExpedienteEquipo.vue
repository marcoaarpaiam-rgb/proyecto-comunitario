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
            v-if="esProfesor"
            :sidebar-open="sidebarOpen"
            @close="sidebarOpen = false"
        />
        <SidebarCoordinador
            v-else
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
                            class="bi bi-folder2-open me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Expediente — {{ equipo.equ_codigo }}
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button
                        type="button"
                        @click="volver"
                        class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                    >
                        <i class="bi bi-arrow-left me-1"></i>Volver
                    </button>
                    <a
                        v-if="equipo.resultado_proyecto"
                        :href="`${rutaBase}/equipos/${equipo.equ_id}/paquete-cierre`"
                        class="btn btn-sm btn-danger fw-bold rounded-pill px-3"
                        title="Descargar ZIP con todos los documentos del proyecto"
                    >
                        <i class="bi bi-file-zip me-1"></i>
                        <span class="d-none d-sm-inline">Paquete Cierre</span>
                    </a>
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
                <!-- Header del equipo -->
                <div
                    class="sc mb-3"
                    style="
                        background: linear-gradient(135deg, #1a1d23, #0d0e11);
                        border-color: #dc3545;
                    "
                >
                    <div class="p-3 p-md-4">
                        <div
                            class="d-flex align-items-center justify-content-between flex-wrap gap-3"
                        >
                            <div>
                                <div
                                    style="
                                        font-size: 1.5rem;
                                        font-weight: 900;
                                        color: #fff;
                                        letter-spacing: -0.5px;
                                    "
                                >
                                    {{ equipo.equ_codigo }}
                                </div>
                                <div
                                    style="
                                        font-size: 0.95rem;
                                        color: #94a3b8;
                                        margin-top: 2px;
                                    "
                                >
                                    {{ equipo.equ_titulo }}
                                </div>
                                <div
                                    style="
                                        font-size: 0.82rem;
                                        color: #64748b;
                                        margin-top: 4px;
                                    "
                                >
                                    <i class="bi bi-collection me-1"></i
                                    >{{ equipo.seccion?.sec_codigo }}
                                    &nbsp;·&nbsp;
                                    <i class="bi bi-layers me-1"></i
                                    >{{ equipo.trayecto?.tra_nombre }}
                                </div>
                            </div>
                            <div class="d-flex gap-2 flex-wrap">
                                <span
                                    v-if="equipo.resultado_proyecto"
                                    class="bs"
                                    :class="
                                        badgeRes(
                                            equipo.resultado_proyecto
                                                ?.estado_proyecto?.epr_nombre,
                                        )
                                    "
                                    style="
                                        font-size: 0.85rem;
                                        padding: 6px 14px;
                                    "
                                >
                                    {{
                                        equipo.resultado_proyecto
                                            ?.estado_proyecto?.epr_nombre
                                    }}
                                </span>
                                <span
                                    v-else
                                    class="bs bsec"
                                    style="
                                        font-size: 0.85rem;
                                        padding: 6px 14px;
                                    "
                                    >Sin resultado</span
                                >
                                <span
                                    v-if="equipo.equ_status"
                                    class="bs ba"
                                    style="
                                        font-size: 0.85rem;
                                        padding: 6px 14px;
                                    "
                                    ><i
                                        class="bi bi-circle-fill me-1"
                                        style="font-size: 0.5rem"
                                    ></i
                                    >Activo</span
                                >
                                <span
                                    v-else
                                    class="bs bd"
                                    style="
                                        font-size: 0.85rem;
                                        padding: 6px 14px;
                                    "
                                    ><i
                                        class="bi bi-circle-fill me-1"
                                        style="font-size: 0.5rem"
                                    ></i
                                    >Disuelto</span
                                >
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Diagnóstico Integral y Ruta Crítica -->
                <div v-if="props.diagnostico" class="mb-4">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <h6 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <span>🧭</span> Diagnóstico de Ruta Crítica y Acompañamiento
                        </h6>
                    </div>
                    <SemaforoEquipo :diagnostico="props.diagnostico" />
                </div>

                <div class="row g-3">
                    <!-- Integrantes -->
                    <div class="col-md-5">
                        <div class="sc h-100">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-people-fill text-danger me-2"
                                    ></i
                                    >Integrantes
                                </h6>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li
                                    v-for="int in equipo.integrantes"
                                    :key="int.ein_id"
                                    class="list-group-item d-flex align-items-center gap-3 px-3 py-2"
                                    style="
                                        background: transparent;
                                        border-color: var(--border);
                                    "
                                    :class="!int.ein_status ? 'opacity-50' : ''"
                                >
                                    <div
                                        style="
                                            width: 34px;
                                            height: 34px;
                                            border-radius: 50%;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            font-weight: 800;
                                            font-size: 0.8rem;
                                            color: #fff;
                                            flex-shrink: 0;
                                        "
                                        :style="{
                                            background: int.ein_es_lider
                                                ? '#dc3545'
                                                : '#495057',
                                        }"
                                    >
                                        {{ int.usuario?.usu_primer_nombre?.[0]
                                        }}{{
                                            int.usuario
                                                ?.usu_primer_apellido?.[0]
                                        }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div
                                            class="fw-semibold"
                                            style="font-size: 0.9rem"
                                        >
                                            {{ int.usuario?.usu_primer_nombre }}
                                            {{
                                                int.usuario?.usu_primer_apellido
                                            }}
                                        </div>
                                        <div
                                            style="
                                                font-size: 0.75rem;
                                                color: var(--text-muted);
                                            "
                                        >
                                            CI: {{ int.usuario?.usu_cedula }}
                                            <span
                                                v-if="metricasIndividuales?.[int.usuario?.usu_id]"
                                                class="ms-2 fw-bold"
                                                :class="metricasIndividuales[int.usuario.usu_id].reprobado_inasistencia ? 'text-danger' : 'text-success'"
                                            >
                                                &bull; Asistencia: {{ metricasIndividuales[int.usuario.usu_id].porcentaje_asistencia }}%
                                            </span>
                                        </div>
                                        <!-- Alerta individual por inasistencia (>25% faltas PNF) sin afectar al resto del equipo -->
                                        <div
                                            v-if="metricasIndividuales?.[int.usuario?.usu_id]?.reprobado_inasistencia"
                                            class="alert alert-danger py-1 px-2 mt-1 mb-0 small fw-bold"
                                            style="font-size: 0.74rem;"
                                        >
                                            <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                            ALERTA PNF: Reprobación individual por inasistencia ({{ metricasIndividuales[int.usuario.usu_id].porcentaje_inasistencia }}% faltas &gt; 25% máx.). No afecta a sus compañeros.
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex flex-column align-items-end gap-1"
                                    >
                                        <span
                                            class="bs"
                                            :class="
                                                int.ein_es_lider ? 'bd' : 'bsec'
                                            "
                                            style="font-size: 0.7rem"
                                            >{{
                                                int.ein_es_lider
                                                    ? "Líder"
                                                    : "Integrante"
                                            }}</span
                                        >
                                        <span
                                            v-if="metricasIndividuales?.[int.usuario?.usu_id]?.nota_individual !== null && metricasIndividuales?.[int.usuario?.usu_id]?.nota_individual !== undefined"
                                            class="badge"
                                            :class="metricasIndividuales[int.usuario.usu_id].aprobado_individual ? 'bg-success' : 'bg-danger'"
                                            style="font-size: 0.7rem"
                                        >
                                            {{ Number(metricasIndividuales[int.usuario.usu_id].nota_individual).toFixed(2) }} pts ({{ metricasIndividuales[int.usuario.usu_id].aprobado_individual ? 'Aprob.' : 'Reprob.' }})
                                        </span>
                                        <span
                                            v-if="!int.ein_status"
                                            style="
                                                font-size: 0.7rem;
                                                color: #dc3545;
                                            "
                                            >Removido</span
                                        >
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Proyecto y comunidad -->
                    <div class="col-md-7">
                        <div class="sc mb-3" v-if="equipo.proyecto_comunidad">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-building text-danger me-2"
                                    ></i
                                    >Proyecto y Comunidad
                                </h6>
                            </div>
                            <div class="p-3">
                                <div class="info-row">
                                    <span class="info-label">Comunidad</span
                                    ><span>{{
                                        equipo.proyecto_comunidad.comunidad
                                            ?.com_nombre
                                    }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Ubicación</span
                                    ><span
                                        style="
                                            color: var(--text-muted);
                                            font-size: 0.85rem;
                                        "
                                        >{{
                                            equipo.proyecto_comunidad.comunidad
                                                ?.com_ubicacion
                                        }}</span
                                    >
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Línea de Investigación</span
                                    ><span>{{
                                        equipo.proyecto_comunidad.tipo_proyecto
                                            ?.tpr_nombre
                                    }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Modalidad</span
                                    ><span>{{
                                        equipo.proyecto_comunidad.modalidad
                                            ?.mpr_nombre
                                    }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Fecha Límite</span
                                    ><span
                                        class="fw-bold"
                                        style="color: var(--rojo)"
                                        >{{
                                            equipo.proyecto_comunidad
                                                .pco_fecha_limite
                                        }}</span
                                    >
                                </div>
                                <div class="info-row">
                                    <span class="info-label"
                                        >Comunidad Aprobada</span
                                    >
                                    <span
                                        v-if="
                                            equipo.proyecto_comunidad
                                                .pco_fecha_aprobacion
                                        "
                                        class="bs ba"
                                        style="font-size: 0.7rem"
                                        >✓
                                        {{
                                            equipo.proyecto_comunidad
                                                .pco_fecha_aprobacion
                                        }}</span
                                    >
                                    <span
                                        v-else
                                        class="bs bw"
                                        style="font-size: 0.7rem"
                                        >Pendiente</span
                                    >
                                </div>
                            </div>
                        </div>
                        <div class="sc" v-else>
                            <div
                                class="p-4 text-center"
                                style="color: var(--text-muted)"
                            >
                                <i class="bi bi-building fs-2 d-block mb-2"></i
                                ><small>Sin proyecto asignado</small>
                            </div>
                        </div>

                        <!-- Resultado -->
                        <div v-if="equipo.resultado_proyecto" class="sc">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-trophy-fill text-danger me-2"
                                    ></i
                                    >Resultado Final
                                </h6>
                            </div>
                            <div class="p-3">
                                <div class="info-row">
                                    <span class="info-label">Estado</span
                                    ><span
                                        class="bs"
                                        :class="
                                            badgeRes(
                                                equipo.resultado_proyecto
                                                    .estado_proyecto
                                                    ?.epr_nombre,
                                            )
                                        "
                                        >{{
                                            equipo.resultado_proyecto
                                                .estado_proyecto?.epr_nombre
                                        }}</span
                                    >
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Trayecto</span
                                    ><span>{{
                                        equipo.resultado_proyecto.trayecto
                                            ?.tra_nombre
                                    }}</span>
                                </div>
                                <div class="info-row">
                                    <span class="info-label">Fecha</span
                                    ><span>{{
                                        equipo.resultado_proyecto
                                            .rpr_fecha_registro
                                    }}</span>
                                </div>
                                <div
                                    v-if="
                                        equipo.resultado_proyecto
                                            .rpr_observaciones
                                    "
                                    class="info-row"
                                >
                                    <span class="info-label">Observaciones</span
                                    ><span
                                        style="
                                            font-size: 0.85rem;
                                            max-width: 200px;
                                            text-align: right;
                                        "
                                        >{{
                                            equipo.resultado_proyecto
                                                .rpr_observaciones
                                        }}</span
                                    >
                                </div>
                                <div class="info-row">
                                    <span class="info-label"
                                        >Entrega Tardía</span
                                    ><span
                                        v-if="
                                            equipo.resultado_proyecto
                                                .rpr_habilitado_tardio
                                        "
                                        class="bs bw"
                                        style="font-size: 0.7rem"
                                        >Habilitada</span
                                    ><span
                                        v-else
                                        style="
                                            color: var(--text-muted);
                                            font-size: 0.85rem;
                                        "
                                        >No habilitada</span
                                    >
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Calificaciones y Recorrido de Evaluación por Semestre (Motor Oficial UPTP) -->
                    <div class="col-12" v-if="evaluaciones?.length || actaFinal || equipo.proyecto_comunidad">
                        <div class="sc mb-2">
                            <div class="sh d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <h6 class="m-0">
                                    <i class="bi bi-award-fill text-danger me-2"></i>Socialización Final y Evaluación del Trayecto (UPTP)
                                </h6>
                                <div class="d-flex gap-2 align-items-center flex-wrap">
                                    <a
                                        v-if="socFinalProgramada && equipo.proyecto_comunidad?.token_jurado"
                                        :href="`/evaluacion-jurado/${equipo.proyecto_comunidad.token_jurado}`"
                                        target="_blank"
                                        class="btn btn-sm btn-warning text-dark fw-bold"
                                        style="font-size: 0.82rem; min-height: 44px; display: inline-flex; align-items: center;"
                                        title="Abrir panel temporal de 12 horas para jurados y vocero comunitario"
                                    >
                                        <i class="bi bi-link-45deg me-1"></i>Panel Jurado (12h)
                                    </a>
                                    <a
                                        v-if="socFinalProgramada && equipo.proyecto_comunidad"
                                        :href="`/evaluaciones/recorrido/${equipo.proyecto_comunidad.pco_id}`"
                                        class="btn btn-sm btn-outline-danger"
                                        style="font-size: 0.82rem; font-weight: 600; min-height: 44px; display: inline-flex; align-items: center;"
                                    >
                                        <i class="bi bi-box-arrow-up-right me-1"></i>Ver Rúbricas 4 Actores
                                    </a>
                                    <a
                                        v-if="actaFinal"
                                        :href="`/evaluaciones/pdf/${actaFinal.id_proyecto}`"
                                        class="btn btn-sm btn-danger"
                                        style="font-size: 0.82rem; font-weight: 600; min-height: 44px; display: inline-flex; align-items: center;"
                                        target="_blank"
                                    >
                                        <i class="bi bi-file-earmark-pdf-fill me-1"></i>Acta Final PDF
                                    </a>
                                </div>
                            </div>

                            <div class="p-3">
                                <div class="row g-3">
                                    <!-- Control de Fecha de Socialización Final y Entrega de CD/Informe -->
                                    <div class="col-12" v-if="socFinalProgramada">
                                        <div class="p-3 rounded border d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: #f0fdf4; border-color: #198754 !important; border-left: 5px solid #198754 !important;">
                                            <div>
                                                <div class="fw-bold text-dark d-flex align-items-center flex-wrap gap-2">
                                                    <span class="badge bg-success">Socialización Final Programada</span>
                                                    <span v-if="cdEntregado" class="badge bg-dark text-white">
                                                        <i class="bi bi-disc-fill me-1"></i>CD / Informe Entregado
                                                    </span>
                                                    <span v-else class="badge bg-warning text-dark">
                                                        <i class="bi bi-exclamation-circle me-1"></i>CD / Informe Pendiente
                                                    </span>
                                                </div>
                                                <div class="small text-dark mt-1">
                                                    <strong>Fecha:</strong> {{ socFinalProgramada.soc_fecha }}
                                                    <span v-if="socFinalProgramada.soc_hora"> &bull; <strong>Hora:</strong> {{ socFinalProgramada.soc_hora }}</span>
                                                    &bull; <strong>Lugar:</strong> {{ socFinalProgramada.soc_lugar || 'Por definir' }}
                                                </div>
                                            </div>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-dark fw-bold"
                                                style="min-height: 44px;"
                                                @click="abrirEditorSocExp"
                                            >
                                                <i class="bi bi-calendar2-event me-1"></i>{{ mostrarFormSocExp ? 'Ocultar Formulario' : 'Editar Fecha / CD' }}
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Candado Estricto: PIN y Evaluación Final Ocultos si no hay Fecha Programada -->
                                    <div class="col-12" v-if="!socFinalProgramada">
                                        <div class="p-3 rounded border" style="background: #fffdf5; border-color: #d97706 !important; border-left: 5px solid #d97706 !important;">
                                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-2">
                                                <div>
                                                    <span class="badge bg-warning text-dark fw-bold text-uppercase mb-1">
                                                        <i class="bi bi-lock-fill me-1"></i>Módulo de Evaluación Final y PIN Bloqueados
                                                    </span>
                                                    <div class="fw-bold text-dark">
                                                        Debe programar la Fecha de Socialización Final para habilitar el PIN de Defensa y la Evaluación de 4 Actores
                                                    </div>
                                                    <div class="small text-muted">
                                                        Regla institucional PNF: El PIN de 6 dígitos (Jurado 30% y Comunidad 20%), la ponderación de 4 actores y la entrega de CD/Informe solo se habilitan una vez guardada la fecha de defensa.
                                                    </div>
                                                </div>
                                            </div>

                                            <form @submit.prevent="guardarSocFinalExpediente" class="mt-3 p-3 bg-white rounded border">
                                                <div class="row g-3 align-items-end">
                                                    <div class="col-md-3">
                                                        <label class="form-label fw-bold text-dark small">Fecha de Socialización *</label>
                                                        <input v-model="formSocFinalExp.fecha" type="date" class="form-control" style="min-height: 46px;" required />
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label fw-bold text-dark small">Hora</label>
                                                        <input v-model="formSocFinalExp.hora" type="time" class="form-control" style="min-height: 46px;" />
                                                    </div>
                                                    <div class="col-md-4">
                                                        <label class="form-label fw-bold text-dark small">Lugar del Acto *</label>
                                                        <input v-model="formSocFinalExp.lugar" type="text" class="form-control" style="min-height: 46px;" placeholder="Ej: Auditorio UPTP / Aula 4" required />
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="form-check p-2 border rounded bg-light" style="min-height: 46px; display: flex; align-items: center; gap: 8px; padding-left: 2rem !important;">
                                                            <input v-model="formSocFinalExp.entrega_cd_informe" class="form-check-input" type="checkbox" id="chkCdExpLock" />
                                                            <label class="form-check-label fw-bold small text-dark" for="chkCdExpLock">Entregó CD / Informe</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 text-end">
                                                        <button type="submit" class="btn btn-danger fw-bold px-4" style="min-height: 46px;" :disabled="loadingSocExp">
                                                            <i class="bi bi-calendar-check-fill me-1"></i>
                                                            {{ loadingSocExp ? 'Guardando...' : 'Guardar Fecha y Habilitar Evaluación Final' }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    <!-- Formulario inline para editar fecha/CD cuando ya está programada -->
                                    <div class="col-12" v-if="socFinalProgramada && mostrarFormSocExp">
                                        <form @submit.prevent="guardarSocFinalExpediente" class="p-3 bg-light rounded border">
                                            <div class="row g-3 align-items-end">
                                                <div class="col-md-3">
                                                    <label class="form-label fw-bold text-dark small">Fecha de Socialización *</label>
                                                    <input v-model="formSocFinalExp.fecha" type="date" class="form-control" style="min-height: 46px;" required />
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label fw-bold text-dark small">Hora</label>
                                                    <input v-model="formSocFinalExp.hora" type="time" class="form-control" style="min-height: 46px;" />
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label fw-bold text-dark small">Lugar del Acto *</label>
                                                    <input v-model="formSocFinalExp.lugar" type="text" class="form-control" style="min-height: 46px;" required />
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="form-check p-2 border rounded bg-white" style="min-height: 46px; display: flex; align-items: center; gap: 8px; padding-left: 2rem !important;">
                                                        <input v-model="formSocFinalExp.entrega_cd_informe" class="form-check-input" type="checkbox" id="chkCdExpEdit" />
                                                        <label class="form-check-label fw-bold small text-dark" for="chkCdExpEdit">Entregó CD / Informe</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-end">
                                                    <button type="submit" class="btn btn-danger fw-bold px-4" style="min-height: 46px;" :disabled="loadingSocExp">
                                                        <i class="bi bi-save me-1"></i>
                                                        {{ loadingSocExp ? 'Actualizando...' : 'Actualizar Programación y CD' }}
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <!-- Banner de PIN de defensa para Jurado y Comunidad (SOLO visible si hay fecha guardada) -->
                                    <div class="col-12" v-if="socFinalProgramada && (equipo.proyecto_comunidad?.pin_defensa || evaluaciones?.[0]?.pin_defensa)">
                                        <div class="p-2 px-3 rounded border d-flex justify-content-between align-items-center flex-wrap gap-2" style="background: #fff9db; border-color: #ffe066 !important;">
                                            <div class="d-flex align-items-center gap-2">
                                                <i class="bi bi-key-fill text-danger fs-5"></i>
                                                <div>
                                                    <span class="small text-muted fw-bold d-block">PIN de Defensa Oficial (Jurado y Vocero Comunitario):</span>
                                                    <span class="fs-5 fw-bold font-monospace text-dark tracking-wider">
                                                        {{ equipo.proyecto_comunidad?.pin_defensa || evaluaciones?.[0]?.pin_defensa }}
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="small text-muted">
                                                <span v-if="equipo.proyecto_comunidad?.token_jurado">
                                                    Acceso activo: <a :href="'/evaluacion-jurado/' + equipo.proyecto_comunidad.token_jurado" target="_blank" class="fw-bold text-danger text-decoration-underline">Panel Temporal de Evaluación</a>
                                                </span>
                                                <span v-else>
                                                    Genere el enlace temporal de 12 horas en la gestión de Socializaciones.
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Semestres -->
                                    <div class="col-md-6" v-for="sem in evaluaciones" :key="sem.id_recorrido">
                                        <div class="p-3 rounded border" :style="sem.estado_semestre === 'Culminado' ? 'background: #f8fff9; border-color: #badbcc !important;' : 'background: #fff;'">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <div>
                                                    <span class="badge bg-danger rounded-pill px-2 py-1 mb-1" style="font-size: 0.72rem;">{{ sem.semestre }}</span>
                                                    <h6 class="fw-bold m-0 text-dark">
                                                        {{ sem.semestre === 'Semestre 1' ? 'Semestre Impar (40% Trayecto)' : 'Semestre Par (60% Trayecto)' }}
                                                    </h6>
                                                </div>
                                                <span class="bs" :class="sem.estado_semestre === 'Culminado' ? 'ba' : 'bw'" style="font-size: 0.75rem;">
                                                    {{ sem.estado_semestre }}
                                                </span>
                                            </div>

                                            <div class="d-flex justify-content-between align-items-baseline p-2 rounded mb-2" style="background: rgba(0,0,0,0.03);">
                                                <span class="text-muted" style="font-size: 0.85rem; font-weight: 600;">Calificación Semestral (Mín. 16.00):</span>
                                                <span class="fw-bold" style="font-size: 1.25rem;" :style="Number(sem.nota_cuantitativa_semestre) >= 16 ? 'color: #198754;' : 'color: #dc3545;'">
                                                    {{ Number(sem.nota_cuantitativa_semestre).toFixed(2) }} <small class="text-muted" style="font-size: 0.75rem;">/ 20 pts</small>
                                                </span>
                                            </div>

                                            <!-- Desglose de Actores si es Semestre 2 (Condicionado a fecha guardada) -->
                                            <div v-if="sem.semestre === 'Semestre 2'" class="mt-2 pt-2 border-top">
                                                <template v-if="socFinalProgramada">
                                                    <div class="small fw-bold text-muted mb-1" style="font-size: 0.74rem;">Ponderación 4 Actores UPTP:</div>
                                                    <div class="d-flex justify-content-between small text-muted py-1 border-bottom" style="font-size: 0.78rem;">
                                                        <span>Docente de Proyecto (40%):</span>
                                                        <strong class="text-dark">{{ Number(sem.nota_docente || 0).toFixed(2) }} pts</strong>
                                                    </div>
                                                    <div class="d-flex justify-content-between small text-muted py-1 border-bottom" style="font-size: 0.78rem;">
                                                        <span>Jurado Evaluador (30%):</span>
                                                        <strong class="text-dark">{{ Number(sem.nota_jurado || 0).toFixed(2) }} pts</strong>
                                                    </div>
                                                    <div class="d-flex justify-content-between small text-muted py-1 border-bottom" style="font-size: 0.78rem;">
                                                        <span>Comunidad Beneficiaria (20%):</span>
                                                        <strong class="text-dark">{{ Number(sem.nota_comunidad || 0).toFixed(2) }} pts</strong>
                                                    </div>
                                                    <div class="d-flex justify-content-between small text-muted py-1" style="font-size: 0.78rem;">
                                                        <span>Autoevaluación Estudiante (10%):</span>
                                                        <strong class="text-dark">{{ Number(sem.nota_estudiante || 0).toFixed(2) }} pts</strong>
                                                    </div>
                                                </template>
                                                <div v-else class="small text-muted p-2 bg-light rounded border">
                                                    <i class="bi bi-lock-fill text-warning me-1"></i>
                                                    Evaluación de 4 Actores oculta hasta programar la Fecha de Socialización Final.
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Acta Final Resumen -->
                                    <div class="col-12" v-if="actaFinal">
                                        <div class="p-3 rounded border" style="background: #eef7ff; border-color: #b6d4fe !important;">
                                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                <div>
                                                    <div class="fw-bold text-primary" style="font-size: 0.95rem;">
                                                        <i class="bi bi-shield-check me-1"></i>Acta Final de Trayecto Cerrada
                                                    </div>
                                                    <div class="small text-muted">
                                                        Hash Oficial SHA-256: <code>{{ actaFinal.hash_verificacion }}</code>
                                                    </div>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-primary px-3 py-2 rounded-pill fw-bold" style="font-size: 0.88rem;">
                                                        Definitiva: {{ Number(actaFinal.calificacion_definitiva_cuantitativa).toFixed(2) }} / 20 pts ({{ actaFinal.calificacion_definitiva_cualitativa }})
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Puntos de Control -->
                    <div class="col-12" v-if="equipo.puntos_control?.length">
                        <div class="sc">
                            <div class="sh d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <h6>
                                    <i
                                        class="bi bi-flag-fill text-danger me-2"
                                    ></i
                                    >Puntos de Control
                                    <span
                                        style="
                                            font-size: 0.8rem;
                                            color: var(--text-muted);
                                            font-weight: 500;
                                        "
                                        >{{ cumplidos }} /
                                        {{ equipo.puntos_control.length }}
                                        confirmados</span
                                    >
                                </h6>
                            </div>
                            <div class="table-responsive">
                                <table class="ct">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Punto</th>
                                            <th>Fecha Límite</th>
                                            <th>Estado / Enfoque de Gestión de Proyectos y Procesos</th>
                                            <th>Avance</th>
                                            <th>Asistencias</th>
                                            <th>Acuerdos</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="puc in equipo.puntos_control"
                                            :key="puc.puc_id"
                                        >
                                            <td class="ps-4">
                                                <div class="fw-semibold">{{ puc.puc_nombre }}</div>
                                                <div v-if="puc.puc_resumen" class="small text-muted">{{ puc.puc_resumen }}</div>
                                                <div v-if="obtenerSeguimiento(puc)?.tipo_encuentro" class="badge bg-light text-dark border mt-1">
                                                    {{ obtenerSeguimiento(puc).tipo_encuentro?.ten_nombre }}
                                                </div>
                                            </td>
                                            <td>
                                                <span
                                                    :style="
                                                        !esCumplido(puc) &&
                                                        estaVencido(
                                                            puc.puc_fecha_limite,
                                                        )
                                                            ? 'color:#dc3545;font-weight:700'
                                                            : ''
                                                    "
                                                    >{{
                                                        puc.puc_fecha_reprogramada ||
                                                        puc.puc_fecha_limite
                                                    }}</span
                                                >
                                                <span
                                                    v-if="
                                                        puc.puc_fecha_reprogramada
                                                    "
                                                    class="badge bg-warning text-dark ms-1"
                                                    style="font-size: 0.68rem;"
                                                    >reprog.</span
                                                >
                                            </td>
                                            <td>
                                                <span
                                                    v-if="esCumplido(puc)"
                                                    class="bs ba d-inline-flex align-items-center gap-1 mb-1"
                                                    style="font-size: 0.72rem"
                                                    ><i class="bi bi-check-circle-fill"></i> Confirmado</span
                                                >
                                                <span
                                                    v-else-if="
                                                        estaVencido(
                                                            puc.puc_fecha_limite,
                                                        )
                                                    "
                                                    class="bs bd"
                                                    style="font-size: 0.72rem"
                                                    >Vencido</span
                                                >
                                                <span
                                                    v-else
                                                    class="bs bw"
                                                    style="font-size: 0.72rem"
                                                    >Pendiente</span
                                                >
                                                <div v-if="obtenerSeguimiento(puc)?.seq_estado_semaforo">
                                                    <span
                                                        class="badge"
                                                        :class="{
                                                            'bg-success': ['positivo', 'conforme', 'en_ruta'].includes(obtenerSeguimiento(puc).seq_estado_semaforo),
                                                            'bg-warning text-dark': ['neutral', 'observado', 'acelerar'].includes(obtenerSeguimiento(puc).seq_estado_semaforo),
                                                            'bg-danger': ['negativo', 'no_conforme', 'riesgo_alto'].includes(obtenerSeguimiento(puc).seq_estado_semaforo),
                                                            'bg-dark': ['inviable', 'no_viable'].includes(obtenerSeguimiento(puc).seq_estado_semaforo),
                                                        }"
                                                        :title="getSignificadoSemaforo(obtenerSeguimiento(puc).seq_estado_semaforo)"
                                                        style="font-size: 0.68rem; cursor: help;"
                                                    >
                                                        {{ getEtiquetaSemaforo(obtenerSeguimiento(puc).seq_estado_semaforo) }}
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <span v-if="obtenerSeguimiento(puc)?.seq_valoracion_avance !== null && obtenerSeguimiento(puc)?.seq_valoracion_avance !== undefined" class="fw-bold text-dark">
                                                    {{ obtenerSeguimiento(puc).seq_valoracion_avance }}%
                                                </span>
                                                <span v-else class="text-muted">—</span>
                                            </td>
                                            <td>
                                                <div v-if="obtenerAsistencias(puc)?.length" class="small">
                                                    <span class="badge bg-secondary">
                                                        {{ obtenerAsistencias(puc).filter(a => a.apc_asistio).length }} / {{ obtenerAsistencias(puc).length }} presentes
                                                    </span>
                                                </div>
                                                <span v-else class="text-muted small">—</span>
                                            </td>
                                            <td>
                                                <div v-if="obtenerAcuerdos(puc)?.length" class="small">
                                                    <span class="badge bg-info text-dark">
                                                        {{ obtenerAcuerdos(puc).length }} acuerdos
                                                    </span>
                                                    <div v-for="ac in obtenerAcuerdos(puc)" :key="ac.acue_id" class="text-muted small mt-1" style="max-width: 180px;">
                                                        &bull; {{ ac.acue_descripcion }}
                                                        <span class="badge ms-1" :class="ac.acue_estado === 'rectificado' ? 'bg-success' : ac.acue_estado === 'en_proceso' ? 'bg-warning text-dark' : 'bg-secondary'" style="font-size: 0.65rem;">
                                                            {{ ac.acue_estado }}
                                                        </span>
                                                    </div>
                                                </div>
                                                <span v-else class="text-muted small">—</span>
                                            </td>
                                            <td
                                                style="
                                                    font-size: 0.83rem;
                                                    color: var(--text-muted);
                                                    max-width: 200px;
                                                "
                                            >
                                                {{ obtenerObservacion(puc) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Entregables -->
                    <div class="col-md-6" v-if="equipo.entregables?.length">
                        <div class="sc">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-file-earmark-check-fill text-danger me-2"
                                    ></i
                                    >Entregables
                                </h6>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li
                                    v-for="ent in equipo.entregables"
                                    :key="ent.entr_id"
                                    class="list-group-item d-flex align-items-center justify-content-between px-3 py-2"
                                    style="
                                        background: transparent;
                                        border-color: var(--border);
                                    "
                                >
                                    <div>
                                        <div
                                            class="fw-semibold"
                                            style="font-size: 0.88rem"
                                        >
                                            {{ ent.entr_nombre_archivo }}
                                        </div>
                                        <div
                                            style="
                                                font-size: 0.75rem;
                                                color: var(--text-muted);
                                            "
                                        >
                                            <span v-if="ent.catalogo_pnfi" class="badge bg-secondary me-1">
                                                {{ ent.catalogo_pnfi.cep_nombre }}
                                            </span>
                                            {{
                                                ent.tipo_entregable?.tet_nombre
                                            }}
                                            · v{{ ent.entr_version || "1" }}
                                            <span v-if="ent.entr_fecha_revision"> · Rev: {{ ent.entr_fecha_revision }}</span>
                                        </div>
                                        <div v-if="ent.entr_observacion_rechazo" class="small text-danger mt-1">
                                            Obs: {{ ent.entr_observacion_rechazo }}
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <a v-if="ent.entr_ruta" :href="'/storage/' + ent.entr_ruta" target="_blank" class="btn btn-sm btn-outline-secondary py-0 px-2" title="Descargar archivo">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <span
                                            class="bs"
                                            :class="
                                                ent.entr_aprobado === null
                                                    ? 'bw'
                                                    : ent.entr_aprobado
                                                      ? 'ba'
                                                      : 'bd'
                                            "
                                            style="font-size: 0.7rem"
                                        >
                                            {{
                                                ent.entr_aprobado === null
                                                    ? "Pendiente"
                                                    : ent.entr_aprobado
                                                      ? "Aprobado"
                                                      : "Rechazado"
                                            }}
                                        </span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Socializaciones -->
                    <div class="col-md-6" v-if="equipo.socializaciones?.length">
                        <div class="sc">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-mic-fill text-danger me-2"
                                    ></i
                                    >Socializaciones
                                </h6>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li
                                    v-for="soc in equipo.socializaciones"
                                    :key="soc.soc_id"
                                    class="list-group-item px-3 py-2"
                                    style="
                                        background: transparent;
                                        border-color: var(--border);
                                    "
                                >
                                    <div
                                        class="d-flex align-items-center justify-content-between"
                                    >
                                        <div>
                                            <span
                                                class="bs"
                                                :class="
                                                    soc.tipo_socializacion
                                                        ?.tso_orden === 1
                                                        ? 'bw'
                                                        : 'bp'
                                                "
                                                style="font-size: 0.72rem"
                                                >{{
                                                    soc.tipo_socializacion
                                                        ?.tso_nombre
                                                }}</span
                                            >
                                            <div
                                                style="
                                                    font-size: 0.8rem;
                                                    color: var(--text-muted);
                                                    margin-top: 3px;
                                                "
                                            >
                                                {{ soc.soc_fecha }} ·
                                                {{ soc.soc_lugar }}
                                            </div>
                                        </div>
                                        <div class="d-flex gap-1">
                                            <span
                                                v-if="soc.soc_apto === true"
                                                class="bs ba"
                                                style="font-size: 0.7rem"
                                                >Apto</span
                                            >
                                            <span
                                                v-else-if="
                                                    soc.soc_apto === false
                                                "
                                                class="bs bd"
                                                style="font-size: 0.7rem"
                                                >No Apto</span
                                            >
                                            <span
                                                v-if="soc.soc_aprobado === true"
                                                class="bs ba"
                                                style="font-size: 0.7rem"
                                                >Aprobado</span
                                            >
                                            <span
                                                v-else-if="
                                                    soc.soc_aprobado === false
                                                "
                                                class="bs bd"
                                                style="font-size: 0.7rem"
                                                >No Aprobado</span
                                            >
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <!-- Carta de presentación -->
                    <div class="col-12" v-if="equipo.carta_presentacion">
                        <div class="sc">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-file-earmark-text-fill text-danger me-2"
                                    ></i
                                    >Carta de Presentación
                                </h6>
                            </div>
                            <div class="p-3 d-flex align-items-center gap-3">
                                <i
                                    class="bi bi-file-earmark-pdf fs-2"
                                    style="color: #dc3545"
                                ></i>
                                <div>
                                    <div class="fw-semibold">
                                        {{
                                            equipo.proyecto_comunidad?.comunidad
                                                ?.com_nombre
                                        }}
                                    </div>
                                    <div
                                        style="
                                            font-size: 0.82rem;
                                            color: var(--text-muted);
                                        "
                                    >
                                        Solicitada:
                                        {{
                                            equipo.carta_presentacion.cpr_fecha_generacion?.split(
                                                "T",
                                            )[0]
                                        }}
                                        <span
                                            v-if="
                                                equipo.carta_presentacion
                                                    .cpr_fecha_aprobacion
                                            "
                                        >
                                            · Aprobada:
                                            {{
                                                equipo.carta_presentacion.cpr_fecha_aprobacion?.split(
                                                    "T",
                                                )[0]
                                            }}</span
                                        >
                                    </div>
                                </div>
                                <div class="ms-auto d-flex gap-2">
                                    <span
                                        v-if="
                                            equipo.carta_presentacion
                                                .cpr_fecha_aprobacion
                                        "
                                        class="bs ba"
                                        >Generada</span
                                    >
                                    <span v-else class="bs bw">Pendiente</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historial de eventos -->
                    <div class="col-12" v-if="equipo.historial?.length">
                        <div class="sc">
                            <div class="sh">
                                <h6>
                                    <i
                                        class="bi bi-clock-history text-danger me-2"
                                    ></i
                                    >Historial de Eventos
                                </h6>
                            </div>
                            <ul class="list-group list-group-flush">
                                <li
                                    v-for="evt in equipo.historial"
                                    :key="evt.heq_id"
                                    class="list-group-item d-flex align-items-start gap-3 px-3 py-2"
                                    style="
                                        background: transparent;
                                        border-color: var(--border);
                                    "
                                >
                                    <div
                                        style="
                                            width: 30px;
                                            height: 30px;
                                            border-radius: 50%;
                                            display: flex;
                                            align-items: center;
                                            justify-content: center;
                                            font-size: 0.8rem;
                                            flex-shrink: 0;
                                        "
                                        :style="
                                            colorEvt(
                                                evt.tipo_evento?.tee_nombre,
                                            )
                                        "
                                    >
                                        <i
                                            class="bi"
                                            :class="
                                                iconEvt(
                                                    evt.tipo_evento?.tee_nombre,
                                                )
                                            "
                                        ></i>
                                    </div>
                                    <div>
                                        <div
                                            class="fw-semibold"
                                            style="font-size: 0.88rem"
                                        >
                                            {{ evt.tipo_evento?.tee_nombre }}
                                        </div>
                                        <div
                                            style="
                                                font-size: 0.8rem;
                                                color: var(--text-muted);
                                            "
                                        >
                                            {{ evt.heq_motivo }}
                                        </div>
                                        <div
                                            style="
                                                font-size: 0.75rem;
                                                color: var(--text-muted);
                                            "
                                        >
                                            {{ evt.heq_fecha_evento }}
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script setup>
import { ref, computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Link } from "@inertiajs/vue3";
import SemaforoEquipo from "@/Components/SemaforoEquipo.vue";
import SidebarCoordinador from "@/Components/SidebarCoordinador.vue";
import SidebarProfesor from "@/Components/SidebarProfesor.vue";

const page = usePage();
const esProfesor = computed(() => {
    return (
        page.props.auth.user?.rol?.rol_nombre === "profesor_proyecto" ||
        page.props.auth.user?.id_rol === 2 ||
        window.location.pathname.startsWith("/profesor")
    );
});

// Detectar si viene del profesor o del coordinador
const rutaBase = computed(() => {
    return esProfesor.value ? "/profesor" : "/coordinador";
});

const menuMaestras = ref(false);
const menuAcademico = ref(false);
const menuSistema = ref(false);
const props = defineProps({
    equipo: Object,
    evaluaciones: {
        type: Array,
        default: () => [],
    },
    actaFinal: {
        type: Object,
        default: () => null,
    },
    metricasIndividuales: {
        type: Object,
        default: () => ({}),
    },
    diagnostico: {
        type: Object,
        default: () => null,
    },
    semanaActual: {
        type: Number,
        default: 1,
    },
});

const socFinalProgramada = computed(() => {
    return (props.equipo?.socializaciones || []).find(
        (s) =>
            (s.tipo_socializacion?.tso_orden === 2 || Number(s.soc_id_tso) === 2) &&
            Boolean(s.soc_fecha)
    ) || null;
});

const cdEntregado = computed(() => {
    const obs = socFinalProgramada.value?.soc_observaciones || "";
    return obs.includes("[CD/INFORME ENTREGADO]");
});

const mostrarFormSocExp = ref(false);
const loadingSocExp = ref(false);
const formSocFinalExp = ref({
    fecha: "",
    hora: "",
    lugar: "",
    entrega_cd_informe: false,
});

const abrirEditorSocExp = () => {
    mostrarFormSocExp.value = !mostrarFormSocExp.value;
    if (mostrarFormSocExp.value && socFinalProgramada.value) {
        formSocFinalExp.value = {
            fecha: socFinalProgramada.value.soc_fecha || "",
            hora: socFinalProgramada.value.soc_hora || "",
            lugar: socFinalProgramada.value.soc_lugar || "",
            entrega_cd_informe: cdEntregado.value,
        };
    }
};

const guardarSocFinalExpediente = () => {
    if (!formSocFinalExp.value.fecha || !formSocFinalExp.value.lugar) return;
    loadingSocExp.value = true;
    router.post(
        `${rutaBase.value}/socializaciones/programar-final`,
        {
            id_equipo: props.equipo.equ_id,
            fecha: formSocFinalExp.value.fecha,
            hora: formSocFinalExp.value.hora,
            lugar: formSocFinalExp.value.lugar,
            entrega_cd_informe: formSocFinalExp.value.entrega_cd_informe,
        },
        {
            preserveScroll: true,
            onFinish: () => {
                loadingSocExp.value = false;
                mostrarFormSocExp.value = false;
            },
        }
    );
};

const sidebarOpen = ref(false);
const temaKey = computed(() => (esProfesor.value ? "prof-theme" : "coord-theme"));
const tema = ref(localStorage.getItem(temaKey.value) || "light");
const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem(temaKey.value, tema.value);
};
const logout = () => router.post("/logout");
const volver = () => {
    if (window.history.length > 1) {
        window.history.back();
    } else {
        router.visit(`${rutaBase.value}/equipos`);
    }
};
const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});
const estaVencido = (f) => f && new Date(f) < new Date();
const obtenerSeguimiento = (puc) => puc.seguimiento || (puc.seguimientos && puc.seguimientos[0]) || null;
const esCumplido = (puc) => Boolean(obtenerSeguimiento(puc)?.seq_cumplido);
const obtenerObservacion = (puc) => obtenerSeguimiento(puc)?.seq_observaciones || "—";
const obtenerAcuerdos = (puc) => obtenerSeguimiento(puc)?.acuerdos || [];
const obtenerAsistencias = (puc) => obtenerSeguimiento(puc)?.asistencias || [];
const cumplidos = computed(
    () =>
        (props.equipo.puntos_control || []).filter(esCumplido).length,
);
const badgeRes = (n) => {
    if (n === "Aprobado") return "ba";
    if (n === "Aplazado") return "bw";
    return "bd";
};
const colorEvt = (n) => {
    if (n === "Disuelto") return "background:#f8d7da;color:#842029";
    if (n === "Líder Cambiado") return "background:#fff3cd;color:#664d03";
    if (n === "Integrante Removido") return "background:#ffe5d0;color:#984c0c";
    if (n === "Transición Semestral") return "background:#d1e7dd;color:#0f5132";
    if (n === "Envío a Repositorio") return "background:#cff4fc;color:#055160";
    return "background:#e2e3e5;color:#41464b";
};
const iconEvt = (n) => {
    if (n === "Disuelto") return "bi-x-circle";
    if (n === "Líder Cambiado") return "bi-arrow-repeat";
    if (n === "Integrante Removido") return "bi-person-dash";
    if (n === "Transición Semestral") return "bi-arrow-right-circle-fill";
    if (n === "Envío a Repositorio") return "bi-archive-fill";
    return "bi-circle";
};

const getEtiquetaSemaforo = (tipo) => {
    switch (tipo) {
        case "en_ruta":
        case "conforme":
        case "positivo":
            return "🟢 Conforme";
        case "acelerar":
        case "observado":
        case "neutral":
            return "🟡 Observado";
        case "riesgo_alto":
        case "no_conforme":
        case "negativo":
            return "🔴 No conforme";
        case "inviable":
        case "no_viable":
            return "⚫ No viable";
        default:
            return tipo;
    }
};

const getSignificadoSemaforo = (tipo) => {
    switch (tipo) {
        case "en_ruta":
        case "conforme":
        case "positivo":
            return "Cumple con los estándares y metas.";
        case "acelerar":
        case "observado":
        case "neutral":
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
    transition:
        background 0.3s,
        border-color 0.3s;
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
.ct {
    width: 100%;
    border-collapse: collapse;
}
.ct thead th {
    padding: 0.65rem 1rem;
    background: var(--bg-th);
    color: var(--text-muted);
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 2px solid var(--border);
}
.ct tbody td {
    padding: 0.65rem 1rem;
    border-bottom: 1px solid var(--border);
    color: var(--text-main);
    font-size: 0.87rem;
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
.info-row {
    display: flex;
    justify-content: space-between;
    padding: 0.6rem 0;
    border-bottom: 1px solid var(--border);
    font-size: 0.88rem;
}
.info-row:last-child {
    border-bottom: none;
}
.info-label {
    color: var(--text-muted);
    font-weight: 700;
    font-size: 0.82rem;
}
.step-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    flex-shrink: 0;
    margin-top: 4px;
}
.timeline-line {
    width: 2px;
    background: var(--border);
    margin: 0 auto;
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
