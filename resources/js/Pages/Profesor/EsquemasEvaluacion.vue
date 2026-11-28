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
            :sidebar-open="sidebarOpen"
            @close="sidebarOpen = false"
        />

        <!-- CONTENIDO PRINCIPAL -->
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
                        <i class="bi bi-sliders me-2 text-danger d-none d-sm-inline"></i>
                        Evaluación y Rúbricas
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button
                        class="btn btn-outline-danger btn-sm fw-bold rounded-pill px-3 shadow-sm"
                        @click="abrirModalNuevoEntregable"
                    >
                        <i class="bi bi-file-earmark-plus me-1"></i>+ Nuevo Entregable
                    </button>
                    <button
                        v-if="tabEvaluacion === 'esquemas'"
                        class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm"
                        @click="abrirModalCrear"
                    >
                        <i class="bi bi-plus-circle me-1"></i> Nuevo Esquema
                    </button>
                    <Link
                        v-else
                        href="/evaluaciones"
                        class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm"
                    >
                        <i class="bi bi-award me-1"></i> Calificar Proyectos
                    </Link>
                </div>
            </div>

            <div class="content p-3 p-md-4">
                <!-- Alertas -->
                <div v-if="$page.props.flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i> {{ $page.props.flash.success }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                <div v-if="$page.props.flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ $page.props.flash.error }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>

                <!-- PESTAÑAS UNIFICADAS: ESQUEMA DEL SEMESTRE VS RÚBRICAS DE SOCIALIZACIÓN -->
                <ul class="nav nav-pills gap-2 mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button
                            type="button"
                            class="nav-link fw-bold d-flex align-items-center gap-2 rounded-pill px-3 py-2"
                            :class="tabEvaluacion === 'esquemas' ? 'active bg-danger text-white shadow-sm' : 'btn-outline-secondary'"
                            @click="tabEvaluacion = 'esquemas'"
                        >
                            <i class="bi bi-sliders"></i>
                            <span>1. Esquema del Semestre</span>
                            <span class="badge rounded-pill" :class="tabEvaluacion === 'esquemas' ? 'bg-white text-danger' : 'bg-secondary text-white'">
                                {{ esquemas.length }}
                            </span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button
                            type="button"
                            class="nav-link fw-bold d-flex align-items-center gap-2 rounded-pill px-3 py-2"
                            :class="tabEvaluacion === 'rubricas' ? 'active bg-danger text-white shadow-sm' : 'btn-outline-secondary'"
                            @click="tabEvaluacion = 'rubricas'"
                        >
                            <i class="bi bi-journal-check"></i>
                            <span>2. Rúbricas de Socialización</span>
                            <span class="badge rounded-pill" :class="tabEvaluacion === 'rubricas' ? 'bg-white text-danger' : 'bg-secondary text-white'">
                                {{ rubricas.length }}
                            </span>
                        </button>
                    </li>
                </ul>

                <div v-if="tabEvaluacion === 'esquemas'">
                <!-- ALERTA DE SECCIONES SIN ESQUEMA ASIGNADO -->
                <div v-if="seccionesSinEsquema.length > 0" class="alert alert-warning border-warning shadow-sm rounded-3 p-3 mb-4">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                        <div>
                            <div class="fw-bold text-dark fs-6 d-flex align-items-center gap-2 mb-1">
                                <i class="bi bi-exclamation-octagon-fill text-warning fs-5"></i>
                                Tienes {{ seccionesSinEsquema.length }} sección(es) sin esquema de evaluación asignado
                            </div>
                            <div class="text-muted small">
                                Secciones pendientes:
                                <span v-for="sec in seccionesSinEsquema" :key="sec.sec_id" class="badge bg-white text-dark border me-1">
                                    {{ sec.sec_codigo }} (Semestre {{ sec.sec_semestre || '—' }})
                                </span>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="btn btn-danger btn-sm fw-bold px-3 py-2 text-nowrap shadow-sm"
                            @click="autoAsignarEsquemas"
                            :disabled="asignandoAuto"
                        >
                            <span v-if="asignandoAuto" class="spinner-border spinner-border-sm me-1"></span>
                            <i v-else class="bi bi-magic me-1"></i>
                            Asignar Esquemas Oficiales Automáticamente
                        </button>
                    </div>
                </div>

                <!-- Info institucional -->
                <div class="card border-0 shadow-sm mb-4 card-themed">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fs-1 text-danger">⚖️</span>
                            <div>
                                <h5 class="fw-bold mb-1">Flexibilidad y Rigor Metodológico</h5>
                                <p class="text-muted small mb-0">
                                    Configura esquemas cuantitativos o cualitativos respaldados por evidencias. Puedes asociar esquemas a tus secciones para evaluar con criterios claros y objetivos, o adaptar las plantillas institucionales a tu manera.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Lista de Esquemas -->
                <div class="row g-3">
                    <div v-for="esq in esquemas" :key="esq.esq_id" class="col-12 col-lg-6">
                        <div class="card border-0 shadow-sm h-100 card-themed">
                            <div class="card-header border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center bg-transparent">
                                <div class="d-flex align-items-center gap-2">
                                    <span v-if="esq.esq_es_plantilla" class="badge bg-primary">Plantilla UPTP</span>
                                    <span v-else class="badge bg-secondary">Docente</span>
                                    <span :class="esq.esq_modo === 'cuantitativo' ? 'badge bg-info text-dark' : 'badge bg-success'">
                                        {{ esq.esq_modo === 'cuantitativo' ? 'Cuantitativo (1-20)' : 'Cualitativo Respaldado' }}
                                    </span>
                                </div>
                                <span v-if="esq.esq_modo === 'cuantitativo'" class="badge border badge-themed">
                                    Mín: {{ esq.esq_escala_min }} / Máx: {{ esq.esq_escala_max }} (Aprueba: {{ esq.esq_nota_aprobatoria }})
                                </span>
                                <span v-else class="badge border badge-themed">
                                    Baremo Literal (A / B / C / D)
                                </span>
                            </div>
                            <div class="card-body">
                                <h5 class="fw-bold text-danger mb-1">{{ esq.esq_nombre }}</h5>
                                <p class="text-muted small mb-3">{{ esq.esq_descripcion || 'Sin descripción adicional.' }}</p>

                                <!-- Criterios -->
                                <div v-if="extraerCriterios(esq.esq_criterios).length" class="mb-3">
                                    <div class="small fw-semibold text-muted mb-2">Criterios de evaluación y pesos:</div>
                                    <div class="d-flex flex-column gap-1">
                                        <div
                                            v-for="(crit, idx) in extraerCriterios(esq.esq_criterios)"
                                            :key="idx"
                                            class="d-flex justify-content-between align-items-center p-2 rounded small criterio-item"
                                        >
                                            <span>{{ crit.nombre }}</span>
                                            <span class="badge bg-dark">{{ crit.peso }}%</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Baremo Literal (Semestre Impar) -->
                                <div v-if="esq.esq_modo === 'cualitativo_respaldado' && extraerBaremo(esq.esq_criterios).length" class="mb-3">
                                    <div class="small fw-semibold text-primary mb-2">
                                        <i class="bi bi-table me-1"></i>Baremo Literal (Semestre Impar):
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0" style="font-size: 0.78rem;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Letra</th>
                                                    <th>Nivel de Logro</th>
                                                    <th>Ref.</th>
                                                    <th>Condición</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(b, bIdx) in extraerBaremo(esq.esq_criterios)" :key="bIdx">
                                                    <td class="fw-bold text-center">{{ b.letra }}</td>
                                                    <td>{{ b.nivel }}</td>
                                                    <td class="font-monospace">{{ b.rango }}</td>
                                                    <td>
                                                        <span class="badge" :class="b.aprobado ? 'bg-success' : 'bg-danger'">
                                                            {{ b.aprobado ? 'Aprobado' : 'No Aprobado' }}
                                                        </span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <!-- Secciones actualmente asignadas -->
                                <div v-if="esq.secciones && esq.secciones.length" class="mb-3">
                                    <div class="small fw-semibold text-muted mb-1">
                                        <i class="bi bi-diagram-3 me-1 text-primary"></i> Secciones asignadas:
                                    </div>
                                    <div class="d-flex flex-wrap gap-1">
                                        <span
                                            v-for="sec in esq.secciones"
                                            :key="sec.sec_id"
                                            class="badge border d-flex align-items-center gap-1 py-1 px-2 badge-seccion"
                                        >
                                            <i class="bi bi-check-circle-fill text-success" style="font-size: 0.75rem;"></i>
                                            <span>{{ sec.sec_nombre || ('Sección ' + sec.sec_codigo) }}</span>
                                            <span v-if="sec.pivot?.esq_sec_lapso" class="text-muted" style="font-size: 0.7rem;">
                                                ({{ sec.pivot.esq_sec_lapso === 'semestre_1' ? 'Sem. 1' : (sec.pivot.esq_sec_lapso === 'semestre_2' ? 'Sem. 2' : 'Anual') }})
                                            </span>
                                            <button
                                                type="button"
                                                class="btn-close ms-1"
                                                style="font-size: 0.5rem;"
                                                title="Desasignar de esta sección"
                                                @click.stop="desasignarSeccion(esq.esq_id, sec.sec_id, sec.pivot?.esq_sec_lapso)"
                                            ></button>
                                        </span>
                                    </div>
                                </div>
                                <div v-else class="small text-muted mb-3 fst-italic">
                                    <i class="bi bi-info-circle me-1"></i> Sin secciones asignadas actualmente.
                                </div>

                                <div class="small text-muted mb-1" v-if="esq.creador">
                                    <i class="bi bi-person me-1"></i> Creado por: {{ esq.creador.usu_primer_nombre }} {{ esq.creador.usu_primer_apellido }}
                                </div>
                            </div>

                            <div class="card-footer border-top d-flex justify-content-between align-items-center py-2 flex-wrap gap-2 bg-transparent">
                                <div class="d-flex align-items-center gap-2">
                                    <button
                                        class="btn btn-outline-primary btn-sm rounded-pill"
                                        @click="abrirModalAsignar(esq)"
                                    >
                                        <i class="bi bi-link-45deg me-1"></i> Asignar a Sección
                                    </button>

                                    <!-- Personalizar plantilla institucional a su manera -->
                                    <button
                                        v-if="esq.esq_es_plantilla && !esCoordinador"
                                        class="btn btn-outline-success btn-sm rounded-pill"
                                        @click="personalizarPlantilla(esq)"
                                        title="Crear una copia personalizada de este esquema para adaptarla a tus secciones"
                                    >
                                        <i class="bi bi-sliders me-1"></i> Adaptar a mi manera
                                    </button>
                                </div>

                                <div class="d-flex gap-1" v-if="!esq.esq_es_plantilla || esCoordinador || esq.esq_id_usu_creador === usuarioActual?.usu_id">
                                    <button
                                        class="btn btn-sm btn-outline-secondary"
                                        @click="abrirModalEditar(esq)"
                                        title="Modificar esquema"
                                    >
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button
                                        class="btn btn-sm btn-outline-danger"
                                        @click="eliminarEsquema(esq.esq_id)"
                                        title="Desactivar esquema"
                                        v-if="!esq.esq_es_plantilla || esCoordinador"
                                    >
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="esquemas.length === 0" class="text-center py-5">
                    <p class="text-muted">No hay esquemas de evaluación registrados.</p>
                </div>
                </div>

                <!-- PESTAÑA 2: RÚBRICAS DE SOCIALIZACIÓN -->
                <div v-if="tabEvaluacion === 'rubricas'">
                    <div class="card border-0 shadow-sm mb-4 card-themed">
                        <div class="card-body d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                            <div class="d-flex align-items-center gap-3">
                                <span class="fs-1 text-danger">📋</span>
                                <div>
                                    <h5 class="fw-bold mb-1">Rúbricas Oficiales de Socialización (PNFI)</h5>
                                    <p class="text-muted small mb-0">
                                        Opcional al inicio del semestre, <strong>obligatorio antes de programar la defensa</strong>. Las rúbricas base del PNFI vienen precargadas y puedes ajustar sus criterios según tus secciones.
                                    </p>
                                </div>
                            </div>
                            <Link href="/evaluaciones" class="btn btn-danger btn-sm fw-bold rounded-pill px-3 text-nowrap">
                                <i class="bi bi-award me-1"></i> Ir a Evaluar Proyectos
                            </Link>
                        </div>
                    </div>

                    <div v-if="rubricas.length === 0" class="card border-0 shadow-sm p-5 text-center text-muted card-themed">
                        <i class="bi bi-journal-x fs-1 d-block mb-2"></i>
                        No hay rúbricas cargadas para tus trayectos.
                    </div>

                    <div class="row g-3">
                        <div v-for="rub in rubricas" :key="rub.id_rubrica" class="col-12 col-lg-6">
                            <div class="card border-0 shadow-sm h-100 card-themed">
                                <div class="card-header border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center bg-transparent flex-wrap gap-2">
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-danger">{{ rub.tra_nombre || ('Trayecto ' + rub.id_trayecto) }}</span>
                                        <span class="badge bg-dark">{{ rub.semestre }}</span>
                                        <span class="badge bg-primary text-capitalize">{{ formatearRolRubrica(rub.rol_evaluador) }}</span>
                                    </div>
                                    <span class="badge border badge-themed">
                                        Peso: {{ Number(rub.peso_porcentual).toFixed(0) }}%
                                    </span>
                                </div>
                                <div class="card-body">
                                    <h6 class="fw-bold text-danger mb-3">{{ rub.titulo }}</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0" style="font-size: 0.8rem;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Criterio de Evaluación</th>
                                                    <th class="text-center">Tipo</th>
                                                    <th class="text-end">Pts Máx</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="c in (rub.criterios || [])" :key="c.id_criterio">
                                                    <td>{{ c.nombre_criterio }}</td>
                                                    <td class="text-center">
                                                        <span class="badge" :class="c.tipo_criterio === 'cuantitativo' ? 'bg-info text-dark' : 'bg-success'">
                                                            {{ c.tipo_criterio }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end fw-bold">
                                                        {{ c.tipo_criterio === 'cuantitativo' ? Number(c.puntaje_maximo).toFixed(1) : 'Cualit.' }}
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="card-footer border-top bg-transparent d-flex justify-content-end py-2">
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3" @click="abrirModalEditarRubrica(rub)">
                                        <i class="bi bi-pencil-square me-1"></i> Editar Rúbrica
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- MODAL CREAR / EDITAR ESQUEMA -->
        <div class="modal fade" id="modalEsquema" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content modal-themed">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">
                            {{ editando ? 'Editar Esquema de Evaluación' : 'Configurar Esquema de Evaluación' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form @submit.prevent="guardarEsquema">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nombre del Esquema</label>
                                <input v-model="form.esq_nombre" type="text" class="form-control" required placeholder="Ej: Rúbrica de Fase 1 - T-II" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Descripción / Propósito</label>
                                <textarea v-model="form.esq_descripcion" class="form-control" rows="2" placeholder="Describe los alcances pedagógicos del esquema..."></textarea>
                            </div>
                            <div class="row g-3 mb-3">
                                <div :class="form.esq_modo === 'cuantitativo' ? 'col-md-4' : 'col-md-6'">
                                    <label class="form-label fw-semibold">Modo de Evaluación</label>
                                    <SelectBuscable v-model="form.esq_modo" :options="opcionesModoEsq" placeholder="Modo..." />
                                </div>
                                <div :class="form.esq_modo === 'cuantitativo' ? 'col-md-4' : 'col-md-6'">
                                    <label class="form-label fw-semibold">
                                        {{ form.esq_modo === 'cuantitativo' ? 'Escala Mín / Máx' : 'Equivalencia Referencial (Baremo)' }}
                                    </label>
                                    <div v-if="form.esq_modo === 'cuantitativo'" class="input-group">
                                        <input v-model.number="form.esq_escala_min" type="number" step="0.5" class="form-control" :class="{ 'is-invalid': form.errors.esq_escala_min }" placeholder="1" required />
                                        <span class="input-group-text">-</span>
                                        <input v-model.number="form.esq_escala_max" type="number" step="0.5" class="form-control" :class="{ 'is-invalid': form.errors.esq_escala_max }" placeholder="20" required />
                                    </div>
                                    <div v-else class="form-control bg-light text-primary fw-semibold d-flex align-items-center">
                                        <i class="bi bi-award me-2"></i>Literal A / B / C / D (Sin nota numérica mínima)
                                    </div>
                                </div>
                                <div v-if="form.esq_modo === 'cuantitativo'" class="col-md-4">
                                    <label class="form-label fw-semibold">Nota Mínima Aprobatoria</label>
                                    <input
                                        v-model.number="form.esq_nota_aprobatoria"
                                        type="number"
                                        step="0.5"
                                        class="form-control"
                                        :class="{ 'is-invalid': form.errors.esq_nota_aprobatoria }"
                                        placeholder="16"
                                        required
                                    />
                                    <div v-if="form.errors.esq_nota_aprobatoria" class="invalid-feedback">
                                        {{ form.errors.esq_nota_aprobatoria }}
                                    </div>
                                </div>
                            </div>

                            <!-- Criterios dinámicos -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-semibold mb-0">Criterios de Evaluación y Ponderación (%)</label>
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="agregarCriterio">
                                        <i class="bi bi-plus-circle me-1"></i> Agregar Criterio
                                    </button>
                                </div>
                                <div v-for="(crit, idx) in criteriosList" :key="idx" class="row g-2 mb-2 align-items-center">
                                    <div class="col-8">
                                        <input v-model="crit.nombre" type="text" class="form-control form-control-sm" placeholder="Nombre del criterio..." required />
                                    </div>
                                    <div class="col-3">
                                        <div class="input-group input-group-sm">
                                            <input v-model.number="crit.peso" type="number" class="form-control" placeholder="Peso %" min="1" max="100" required />
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                    <div class="col-1 text-end">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" @click="eliminarCriterio(idx)">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="small fw-semibold mt-1" :class="totalPeso === 100 ? 'text-success' : 'text-danger'">
                                    Total suma de pesos: {{ totalPeso }}% {{ totalPeso !== 100 ? '(Debe sumar 100%)' : '✅' }}
                                </div>
                            </div>

                            <!-- Baremo Literal Configurable (Semestre Impar) -->
                            <div v-if="form.esq_modo === 'cualitativo_respaldado'" class="border rounded p-3 bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <label class="form-label fw-bold text-primary mb-0">
                                            <i class="bi bi-table me-1"></i>Tabla de Equivalencias / Baremo Literal (Semestre Impar)
                                        </label>
                                        <div class="small text-muted">Configura las letras, niveles de logro, rangos referenciales y si aprueba la evaluación formativa.</div>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" @click="agregarBaremoFila">
                                        <i class="bi bi-plus-circle me-1"></i>Agregar Letra
                                    </button>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0 bg-white">
                                        <thead class="small text-muted">
                                            <tr>
                                                <th style="width: 75px;">Letra</th>
                                                <th>Nivel de Logro Descriptivo</th>
                                                <th style="width: 115px;">Rango Ref.</th>
                                                <th style="width: 135px;">Condición</th>
                                                <th style="width: 40px;"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(fila, idx) in baremoList" :key="idx">
                                                <td>
                                                    <input v-model="fila.letra" type="text" maxlength="4" class="form-control form-control-sm text-center fw-bold text-uppercase" placeholder="A" required />
                                                </td>
                                                <td>
                                                    <input v-model="fila.nivel" type="text" class="form-control form-control-sm" placeholder="Ej: Excelente" required />
                                                </td>
                                                <td>
                                                    <input v-model="fila.rango" type="text" class="form-control form-control-sm font-monospace" placeholder="19-20" required />
                                                </td>
                                                <td>
                                                    <SelectBuscable v-model="fila.aprobado" :options="opcionesCondicionBaremo" size="sm" />
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-sm btn-outline-danger py-0 px-1" @click="eliminarBaremoFila(idx)" :disabled="baremoList.length <= 2">
                                                        <i class="bi bi-x"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger" :disabled="form.processing || totalPeso !== 100">
                                {{ editando ? 'Actualizar Esquema' : 'Guardar Esquema' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL ASIGNAR A SECCIÓN -->
        <div class="modal fade" id="modalAsignar" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content modal-themed">
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Asignar Esquema a Sección</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form @submit.prevent="guardarAsignacion">
                        <div class="modal-body">
                            <p class="small text-muted">
                                Selecciona la sección a la que se aplicará el esquema <strong>{{ esquemaSeleccionado?.esq_nombre }}</strong>.
                            </p>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Sección</label>
                                <SelectBuscable
                                    v-model="formAsignar.esq_sec_id_sec"
                                    :options="opcionesSeccionesAsignar"
                                    placeholder="Seleccione una sección..."
                                    search-placeholder="Buscar sección..."
                                />
                                <div v-if="secciones.length === 0" class="text-danger small mt-1">
                                    <i class="bi bi-exclamation-triangle me-1"></i> No tienes secciones asignadas disponibles.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Lapso / Semestre</label>
                                <SelectBuscable v-model="formAsignar.esq_sec_lapso" :options="opcionesLapsoAsignar" placeholder="Seleccione lapso..." />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" :disabled="formAsignar.processing || !formAsignar.esq_sec_id_sec">
                                Confirmar Asignación
                            </button>
                        </div>
                    </form>
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
                                    <i class="bi bi-lock-fill me-1"></i>Trayecto asignado automáticamente según tu sección en curso.
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

        <!-- MODAL EDITAR RÚBRICA DE SOCIALIZACIÓN -->
        <div class="modal fade" id="modalEditarRubrica" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content modal-themed border-0 shadow-lg">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-journal-check text-danger me-2"></i>Editar Rúbrica de Socialización
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form @submit.prevent="guardarRubrica">
                        <div class="modal-body p-4">
                            <div v-if="errorRubrica" class="alert alert-danger py-2 small mb-3">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>{{ errorRubrica }}
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-md-8">
                                    <label class="form-label fw-semibold">Título de la Rúbrica <span class="text-danger">*</span></label>
                                    <input
                                        v-model="formRubrica.titulo"
                                        type="text"
                                        class="form-control"
                                        :class="{ 'is-invalid': formRubrica.errors.titulo }"
                                        required
                                    />
                                    <div v-if="formRubrica.errors.titulo" class="invalid-feedback">{{ formRubrica.errors.titulo }}</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Peso del Actor (%) <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input
                                            v-model.number="formRubrica.peso_porcentual"
                                            type="number"
                                            min="1"
                                            max="100"
                                            class="form-control"
                                            :class="{ 'is-invalid': formRubrica.errors.peso_porcentual }"
                                            required
                                        />
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label fw-semibold mb-0">Criterios de la Rúbrica</label>
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" @click="agregarCriterioRubrica">
                                    <i class="bi bi-plus-circle me-1"></i> Agregar Criterio
                                </button>
                            </div>

                            <div v-for="(c, idx) in formRubrica.criterios" :key="idx" class="row g-2 mb-2 align-items-center">
                                <div class="col-md-6">
                                    <input
                                        v-model="c.nombre_criterio"
                                        type="text"
                                        class="form-control form-control-sm"
                                        placeholder="Descripción del criterio..."
                                        required
                                    />
                                </div>
                                <div class="col-md-3">
                                    <select v-model="c.tipo_criterio" class="form-select form-select-sm" @change="onTipoCriterioChange(c)">
                                        <option value="cuantitativo">Cuantitativo</option>
                                        <option value="cualitativo">Cualitativo</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <div class="input-group input-group-sm">
                                        <input
                                            v-model.number="c.puntaje_maximo"
                                            type="number"
                                            step="0.5"
                                            min="0"
                                            max="20"
                                            class="form-control"
                                            :disabled="c.tipo_criterio === 'cualitativo'"
                                            required
                                        />
                                        <span class="input-group-text">pts</span>
                                    </div>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" @click="eliminarCriterioRubrica(idx)" :disabled="formRubrica.criterios.length <= 1">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="small fw-semibold mt-2" :class="sumaPuntajesRubrica === 20 || !tieneCriterioCuantitativoRubrica ? 'text-success' : 'text-danger'">
                                Suma de puntajes cuantitativos: {{ sumaPuntajesRubrica }} / 20 pts
                                <span v-if="tieneCriterioCuantitativoRubrica && sumaPuntajesRubrica !== 20">(Debe sumar exactamente 20 pts)</span>
                            </div>
                        </div>
                        <div class="modal-footer border-top">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-danger btn-sm fw-bold px-3" :disabled="formRubrica.processing">
                                <span v-if="formRubrica.processing" class="spinner-border spinner-border-sm me-1"></span>
                                Guardar Rúbrica
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js'
import SidebarProfesor from "@/Components/SidebarProfesor.vue"
import SelectBuscable from "@/Components/SelectBuscable.vue"

const props = defineProps({
    esquemas: { type: Array, default: () => [] },
    rubricas: { type: Array, default: () => [] },
    secciones: { type: Array, default: () => [] },
    esCoordinador: { type: Boolean, default: false },
    usuarioActual: { type: Object, default: () => ({}) },
    tiposEntregable: { type: Array, default: () => [] },
})

const tabEvaluacion = ref('esquemas');

const seccionesSinEsquema = computed(() => {
    const assignedSecIds = new Set();
    (props.esquemas || []).forEach(esq => {
        (esq.secciones || []).forEach(sec => {
            assignedSecIds.add(sec.sec_id);
        });
    });
    return (props.secciones || []).filter(s => !assignedSecIds.has(s.sec_id));
});

const asignandoAuto = ref(false);
const autoAsignarEsquemas = () => {
    asignandoAuto.value = true;
    useForm({}).post("/profesor/esquemas-evaluacion/auto-asignar", {
        preserveScroll: true,
        onFinish: () => { asignandoAuto.value = false; }
    });
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
    const primeraSec = props.secciones?.[0];
    const trayectoAuto = Number(primeraSec?.sec_id_tra || primeraSec?.trayecto?.tra_numero || 1);
    formNuevoEntregable.value = {
        nombre: "",
        trayecto: trayectoAuto,
    };
    errorNuevoEntregable.value = "";
    const el = document.getElementById("modalNuevoEntregableMaestro");
    if (el) {
        modalNuevoEntregable.value = modalNuevoEntregable.value || new bootstrap.Modal(el);
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

// Gestión y Edición de Rúbricas de Socialización
const rubricaEditId = ref(null);
const errorRubrica = ref("");
let modalRubricaInstance = null;
const formRubrica = useForm({
    titulo: "",
    peso_porcentual: 20,
    criterios: [],
});

const formatearRolRubrica = (rol) => {
    const mapa = {
        tutor: "Tutor Académico",
        docente_proyecto: "Docente de Proyecto",
        comunidad: "Vocero Comunitario",
        jurado: "Jurado Evaluador",
        estudiante: "Coevaluación Estudiantil",
    };
    return mapa[rol] || rol;
};

const abrirModalEditarRubrica = (rub) => {
    rubricaEditId.value = rub.id_rubrica;
    errorRubrica.value = "";
    formRubrica.clearErrors();
    formRubrica.titulo = rub.titulo || "";
    formRubrica.peso_porcentual = Number(rub.peso_porcentual || 20);
    formRubrica.criterios = (rub.criterios || []).map((c) => ({
        id_criterio: c.id_criterio,
        nombre_criterio: c.nombre_criterio,
        tipo_criterio: c.tipo_criterio || "cuantitativo",
        puntaje_maximo: Number(c.puntaje_maximo || 0),
    }));
    if (formRubrica.criterios.length === 0) {
        formRubrica.criterios.push({ id_criterio: null, nombre_criterio: "", tipo_criterio: "cuantitativo", puntaje_maximo: 20 });
    }
    const el = document.getElementById("modalEditarRubrica");
    if (el) {
        modalRubricaInstance = modalRubricaInstance || new bootstrap.Modal(el);
        modalRubricaInstance.show();
    }
};

const agregarCriterioRubrica = () => {
    formRubrica.criterios.push({ id_criterio: null, nombre_criterio: "", tipo_criterio: "cuantitativo", puntaje_maximo: 0 });
};

const eliminarCriterioRubrica = (idx) => {
    if (formRubrica.criterios.length > 1) {
        formRubrica.criterios.splice(idx, 1);
    }
};

const tieneCriterioCuantitativoRubrica = computed(() =>
    (formRubrica.criterios || []).some((c) => c.tipo_criterio === "cuantitativo")
);

const sumaPuntajesRubrica = computed(() =>
    (formRubrica.criterios || []).reduce(
        (acc, c) => acc + (c.tipo_criterio === "cuantitativo" ? Number(c.puntaje_maximo || 0) : 0),
        0
    )
);

const onTipoCriterioChange = (criterio) => {
    if (criterio.tipo_criterio === 'cualitativo') {
        criterio.puntaje_maximo = 0;
    }
};

const guardarRubrica = () => {
    errorRubrica.value = "";
    if (!formRubrica.titulo.trim()) {
        errorRubrica.value = "El título de la rúbrica es obligatorio.";
        return;
    }
    if (tieneCriterioCuantitativoRubrica.value && Math.abs(sumaPuntajesRubrica.value - 20) > 0.01) {
        errorRubrica.value = `La suma de los puntajes cuantitativos debe ser exactamente 20 pts (actual: ${sumaPuntajesRubrica.value} pts).`;
        return;
    }
    formRubrica.put(`/profesor/rubricas/${rubricaEditId.value}`, {
        preserveScroll: true,
        onSuccess: () => {
            modalRubricaInstance?.hide();
        },
        onError: (errs) => {
            const msgs = Object.values(errs || {}).flat();
            errorRubrica.value = msgs[0] || "Verifique los campos de la rúbrica.";
        },
    });
};

const opcionesModoEsq = [
    { value: "cuantitativo", label: "Cuantitativo (Escala Numérica)" },
    { value: "cualitativo_respaldado", label: "Cualitativo Respaldado" },
]

const opcionesCondicionBaremo = [
    { value: true, label: "Aprobado" },
    { value: false, label: "No Aprobado" },
]

const opcionesLapsoAsignar = [
    { value: "semestre_1", label: "Semestre 1" },
    { value: "semestre_2", label: "Semestre 2" },
    { value: "anual", label: "Completo / Anual" },
]

const opcionesSeccionesAsignar = computed(() => [
    { value: "", label: "Seleccione una sección..." },
    ...(props.secciones || []).map((sec) => ({
        value: sec.sec_id,
        label:
            sec.sec_nombre ||
            `Sección ${sec.sec_codigo}${sec.trayecto ? " - " + sec.trayecto.tra_nombre : ""}${sec.turno ? " (" + sec.turno.tur_nombre + ")" : ""}`,
    })),
])

const page = usePage()
const sidebarOpen = ref(false)
const tema = ref('light')

const iniciales = computed(() => {
    const u = page.props.auth?.user
    if (!u) return 'U'
    return ((u.usu_primer_nombre?.[0] || '') + (u.usu_primer_apellido?.[0] || '')).toUpperCase()
})

const esCoordinador = computed(() => {
    if (props.esCoordinador) return true
    const roles = page.props.auth?.roles || []
    return roles.includes('coordinador') || roles.includes('admin')
})

// Formularios
const editando = ref(false)
const esquemaEditId = ref(null)
const esquemaSeleccionado = ref(null)

const BAREMO_INICIAL = () => [
    { letra: 'A', nivel: 'Excelente', rango: '19-20', aprobado: true },
    { letra: 'B', nivel: 'Bueno / Aprobado', rango: '16-18', aprobado: true },
    { letra: 'C', nivel: 'En Proceso / Nivelación', rango: '12-15', aprobado: false },
    { letra: 'D', nivel: 'Deficiente', rango: '01-11', aprobado: false },
]

const CRITERIOS_INICIAL = () => [
    { nombre: 'Informe Técnico', peso: 40 },
    { nombre: 'Avance Práctico', peso: 30 },
    { nombre: 'Participación y Puntualidad', peso: 30 },
]

const extraerCriterios = (raw) => {
    if (Array.isArray(raw)) return raw
    if (raw && Array.isArray(raw.criterios)) return raw.criterios
    return []
}

const extraerBaremo = (raw) => {
    if (raw && Array.isArray(raw.baremo)) return raw.baremo
    return []
}

const criteriosList = ref(CRITERIOS_INICIAL())
const baremoList = ref(BAREMO_INICIAL())

const form = useForm({
    esq_nombre: '',
    esq_descripcion: '',
    esq_modo: 'cuantitativo',
    esq_escala_min: 1,
    esq_escala_max: 20,
    esq_nota_aprobatoria: 16,
    esq_criterios: CRITERIOS_INICIAL(),
})

const formAsignar = useForm({
    esq_sec_id_esq: null,
    esq_sec_id_sec: '',
    esq_sec_lapso: 'semestre_1',
    accion: 'asignar',
})

const totalPeso = computed(() => {
    return (criteriosList.value || []).reduce((acc, curr) => acc + (Number(curr.peso) || 0), 0)
})

const agregarCriterio = () => {
    criteriosList.value.push({ nombre: '', peso: 0 })
}

const eliminarCriterio = (idx) => {
    criteriosList.value.splice(idx, 1)
}

const agregarBaremoFila = () => {
    baremoList.value.push({ letra: '', nivel: '', rango: '', aprobado: false })
}

const eliminarBaremoFila = (idx) => {
    if (baremoList.value.length > 2) {
        baremoList.value.splice(idx, 1)
    }
}

let modalEsquemaInstance = null
let modalAsignarInstance = null

onMounted(() => {
    const savedTheme = localStorage.getItem('theme') || 'light'
    tema.value = savedTheme

    const observer = new MutationObserver(() => {
        const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 
                             document.documentElement.getAttribute('data-theme') || 'light'
        tema.value = currentTheme
    })
    observer.observe(document.documentElement, { attributes: true, attributeFilter: ['data-bs-theme', 'data-theme'] })

    const elEsq = document.getElementById('modalEsquema')
    if (elEsq) modalEsquemaInstance = new bootstrap.Modal(elEsq)
    const elAsig = document.getElementById('modalAsignar')
    if (elAsig) modalAsignarInstance = new bootstrap.Modal(elAsig)
})

const abrirModalCrear = () => {
    editando.value = false
    esquemaEditId.value = null
    form.reset()
    criteriosList.value = CRITERIOS_INICIAL()
    baremoList.value = BAREMO_INICIAL()
    modalEsquemaInstance?.show()
}

const abrirModalEditar = (esq) => {
    editando.value = true
    esquemaEditId.value = esq.esq_id
    form.esq_nombre = esq.esq_nombre
    form.esq_descripcion = esq.esq_descripcion || ''
    form.esq_modo = esq.esq_modo
    form.esq_escala_min = Number(esq.esq_escala_min)
    form.esq_escala_max = Number(esq.esq_escala_max)
    form.esq_nota_aprobatoria = Number(esq.esq_nota_aprobatoria)
    const crits = extraerCriterios(esq.esq_criterios)
    criteriosList.value = crits.length ? JSON.parse(JSON.stringify(crits)) : CRITERIOS_INICIAL()
    const bar = extraerBaremo(esq.esq_criterios)
    baremoList.value = bar.length ? JSON.parse(JSON.stringify(bar)) : BAREMO_INICIAL()
    modalEsquemaInstance?.show()
}

const personalizarPlantilla = (esq) => {
    editando.value = false
    esquemaEditId.value = null
    form.esq_nombre = `${esq.esq_nombre} (Mi Versión)`
    form.esq_descripcion = esq.esq_descripcion ? `Adaptación de ${esq.esq_nombre}: ${esq.esq_descripcion}` : 'Esquema adaptado por el docente.'
    form.esq_modo = esq.esq_modo
    form.esq_escala_min = Number(esq.esq_escala_min)
    form.esq_escala_max = Number(esq.esq_escala_max)
    form.esq_nota_aprobatoria = Number(esq.esq_nota_aprobatoria)
    const crits = extraerCriterios(esq.esq_criterios)
    criteriosList.value = crits.length ? JSON.parse(JSON.stringify(crits)) : CRITERIOS_INICIAL()
    const bar = extraerBaremo(esq.esq_criterios)
    baremoList.value = bar.length ? JSON.parse(JSON.stringify(bar)) : BAREMO_INICIAL()
    modalEsquemaInstance?.show()
}

const guardarEsquema = () => {
    form.esq_criterios = form.esq_modo === 'cualitativo_respaldado'
        ? { criterios: criteriosList.value, baremo: baremoList.value }
        : criteriosList.value

    if (editando.value) {
        form.put(`/profesor/esquemas-evaluacion/${esquemaEditId.value}`, {
            onSuccess: () => modalEsquemaInstance?.hide(),
        })
    } else {
        form.post('/profesor/esquemas-evaluacion', {
            onSuccess: () => modalEsquemaInstance?.hide(),
        })
    }
}

const eliminarEsquema = (id) => {
    if (confirm('¿Deseas desactivar este esquema de evaluación?')) {
        form.delete(`/profesor/esquemas-evaluacion/${id}`)
    }
}

const abrirModalAsignar = (esq) => {
    esquemaSeleccionado.value = esq
    formAsignar.esq_sec_id_esq = esq.esq_id
    formAsignar.esq_sec_id_sec = props.secciones?.[0]?.sec_id || ''
    formAsignar.accion = 'asignar'
    modalAsignarInstance?.show()
}

const guardarAsignacion = () => {
    formAsignar.post('/profesor/esquemas-evaluacion/asignar', {
        onSuccess: () => modalAsignarInstance?.hide(),
    })
}

const desasignarSeccion = (esqId, secId, lapso) => {
    if (confirm('¿Deseas desasignar esta sección del esquema?')) {
        formAsignar.esq_sec_id_esq = esqId
        formAsignar.esq_sec_id_sec = secId
        formAsignar.esq_sec_lapso = lapso || 'semestre_1'
        formAsignar.accion = 'desasignar'
        formAsignar.post('/profesor/esquemas-evaluacion/asignar', {
            onSuccess: () => {
                formAsignar.accion = 'asignar'
            }
        })
    }
}
</script>

<style scoped>
.main {
    margin-left: 250px;
    min-height: 100vh;
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
}
.tb-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: var(--text-main, #343a40);
}
.card-themed {
    background: var(--bg-card, #ffffff);
    color: var(--text-main, #212529);
    border: 1px solid var(--border, rgba(0, 0, 0, 0.08)) !important;
}
.badge-themed {
    background: var(--bg-quick, #f8f9fa);
    color: var(--text-main, #212529);
}
.criterio-item {
    background: var(--bg-th, #f8f9fa);
    color: var(--text-main, #212529);
    border: 1px solid var(--border, rgba(0, 0, 0, 0.05));
}
.badge-seccion {
    background: var(--bg-quick, #f8f9fa);
    color: var(--text-main, #212529);
}
.modal-themed {
    background: var(--bg-card, #ffffff);
    color: var(--text-main, #212529);
}
@media (max-width: 991.98px) {
    .main {
        margin-left: 0;
    }
}
</style>
