<template>
    <div class="modal fade" id="mSeg" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form @submit.prevent="guardarSeguimiento" class="modal-content border-0 shadow-lg">
                <div class="mh">
                    <h5>
                        <i class="bi bi-clipboard2-check text-danger me-2"></i>
                        {{ modoSoloLecturaSeg ? "Evaluación del Punto — " : "Registrar Seguimiento — " }}
                        {{ puntoActual?.puc_nombre }}
                    </h5>
                    <button type="button" class="btn btn-sm text-white p-0 fs-5" data-bs-dismiss="modal" aria-label="Cerrar">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>

                <div class="modal-body">
                        <div
                            v-if="modoSoloLecturaSeg"
                            class="alert alert-info py-2 px-3 small d-flex align-items-center gap-2 mb-3"
                        >
                            <i class="bi bi-lock-fill fs-5 text-secondary"></i>
                            <div>
                                <strong>Evaluación Registrada:</strong> Este punto de control ya fue evaluado como
                                <span class="badge" :class="formSeg.seq_cumplido ? 'bg-success' : 'bg-danger'">
                                    {{ formSeg.seq_cumplido ? "Cumplido" : "No cumplido" }}
                                </span>. No es posible modificarlo.
                            </div>
                        </div>

                        <!-- Pregunta principal -->
                        <div class="card border p-3 mb-3" style="background: var(--bg-card); border-color: var(--border) !important;">
                            <label class="form-label fw-bold d-flex align-items-center gap-2 mb-2" style="font-size: 0.95rem;">
                                <i class="bi bi-question-circle-fill text-danger fs-5"></i>
                                <span>¿Se cumplió el punto de control?</span>
                                <span class="text-danger">*</span>
                            </label>
                            <div class="d-flex gap-4">
                                <div class="form-check">
                                    <input
                                        type="radio"
                                        class="form-check-input"
                                        v-model="formSeg.seq_cumplido"
                                        :value="true"
                                        :disabled="modoSoloLecturaSeg"
                                        id="segCumplidoSi"
                                    />
                                    <label class="form-check-label fw-bold text-success" for="segCumplidoSi" style="cursor: pointer;">
                                        <i class="bi bi-check-circle-fill me-1"></i>Sí
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input
                                        type="radio"
                                        class="form-check-input"
                                        v-model="formSeg.seq_cumplido"
                                        :value="false"
                                        :disabled="modoSoloLecturaSeg"
                                        id="segCumplidoNo"
                                    />
                                    <label class="form-check-label fw-bold text-danger" for="segCumplidoNo" style="cursor: pointer;">
                                        <i class="bi bi-x-circle-fill me-1"></i>No
                                    </label>
                                </div>
                            </div>
                            <div v-if="erroresSeg.seq_cumplido" class="text-danger small mt-1">
                                {{ erroresSeg.seq_cumplido }}
                            </div>
                        </div>

                        <div v-if="formSeg.seq_cumplido === null && !modoSoloLecturaSeg" class="alert alert-light border py-4 px-3 text-center text-muted small mb-3">
                            <i class="bi bi-hand-index-thumb fs-3 d-block mb-2 text-danger"></i>
                            Indique si el equipo cumplió o no con el punto de control para desplegar las opciones.
                        </div>

                        <!-- NO CUMPLIDO -->
                        <div v-if="formSeg.seq_cumplido === false">
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    Justificación del No Cumplimiento <span class="text-danger">*</span>
                                </label>
                                <textarea
                                    v-model="formSeg.seq_observaciones"
                                    :disabled="modoSoloLecturaSeg"
                                    class="form-control"
                                    :class="{ 'is-invalid': erroresSeg.seq_observaciones }"
                                    rows="3"
                                    placeholder="Explique los motivos..."
                                    required
                                ></textarea>
                                <div v-if="erroresSeg.seq_observaciones" class="invalid-feedback">
                                    {{ erroresSeg.seq_observaciones }}
                                </div>
                            </div>

                            <div class="card border p-3 mb-3 rounded" style="background: rgba(220, 53, 69, 0.04);">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="form-check form-switch m-0 p-0 d-flex align-items-center flex-shrink-0">
                                        <input
                                            class="form-check-input m-0"
                                            type="checkbox"
                                            id="checkDeseaReprog"
                                            v-model="formSeg.desea_reprogramar"
                                            :disabled="modoSoloLecturaSeg"
                                            style="cursor: pointer; width: 2.2em; height: 1.2em;"
                                        />
                                    </div>
                                    <label class="form-check-label fw-bold m-0" for="checkDeseaReprog" style="cursor: pointer;">
                                        <i class="bi bi-calendar-plus text-danger me-1"></i>¿Desea reprogramar el punto de control?
                                    </label>
                                </div>
                                <div v-if="formSeg.desea_reprogramar" class="mt-3 pt-3 border-top row g-3">
                                    <div class="col-md-7">
                                        <label class="form-label fw-semibold small">Nueva Fecha <span class="text-danger">*</span></label>
                                        <input
                                            v-model="formSeg.nueva_fecha"
                                            type="date"
                                            :min="fechaHoy"
                                            :max="fechaMaximaReprog"
                                            class="form-control form-control-sm"
                                            :class="{ 'is-invalid': erroresSeg.nueva_fecha }"
                                            :disabled="modoSoloLecturaSeg"
                                            required
                                        />
                                        <div v-if="erroresSeg.nueva_fecha" class="invalid-feedback">
                                            {{ erroresSeg.nueva_fecha }}
                                        </div>
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label fw-semibold small">Nueva Semana (1 a 18)</label>
                                        <input
                                            v-model.number="formSeg.nueva_semana"
                                            type="number"
                                            min="1"
                                            max="18"
                                            class="form-control form-control-sm"
                                            :disabled="modoSoloLecturaSeg"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SÍ CUMPLIDO -->
                        <div v-if="formSeg.seq_cumplido === true">
                            <!-- Observaciones previas -->
                            <div v-if="puntoActual?.punto_previo_observaciones" class="alert alert-warning py-2 px-3 mb-3 small border-warning">
                                <div class="fw-bold text-dark mb-1">
                                    <i class="bi bi-clock-history text-warning me-1"></i>
                                    Observaciones del Punto Previo ({{ puntoActual.punto_previo_nombre }}):
                                </div>
                                <div class="p-2 rounded bg-white text-dark border mb-2">{{ puntoActual.punto_previo_observaciones }}</div>
                                <label class="form-label fw-bold mb-1">¿Subsanó las observaciones anteriores?</label>
                                <div class="d-flex gap-3 mb-2">
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" v-model="formSeg.seq_cumplio_previo" :value="true" :disabled="modoSoloLecturaSeg" id="cPrevSi" />
                                        <label class="form-check-label fw-semibold text-success" for="cPrevSi">Sí</label>
                                    </div>
                                    <div class="form-check">
                                        <input type="radio" class="form-check-input" v-model="formSeg.seq_cumplio_previo" :value="false" :disabled="modoSoloLecturaSeg" id="cPrevNo" />
                                        <label class="form-check-label fw-semibold text-danger" for="cPrevNo">No</label>
                                    </div>
                                </div>
                                <input type="text" v-model="formSeg.seq_obs_previas_verificacion" :disabled="modoSoloLecturaSeg" class="form-control form-control-sm bg-white" placeholder="Detalle de verificación..." />
                            </div>

                            <!-- Tipo de Encuentro -->
                            <div class="card border p-3 mb-3" style="background: var(--bg-th); border-color: var(--border) !important;">
                                <label class="form-label fw-bold mb-1">
                                    <i class="bi bi-tag-fill text-danger me-1"></i>Tipo de Encuentro <span class="text-danger">*</span>
                                </label>
                                <SelectBuscable
                                    v-model="formSeg.seq_id_tipo_encuentro"
                                    :options="opcionesTipoEncuentro"
                                    size="sm"
                                    :disabled="modoSoloLecturaSeg"
                                    placeholder="Seleccione tipo de encuentro..."
                                />
                            </div>

                            <!-- MODALIDAD CUALITATIVA (SEMESTRE IMPAR): BAREMO LITERAL -->
                            <div v-if="esSemestreImpar" class="card border-2 border-primary p-3 mb-3" style="background: var(--bg-card);">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label class="form-label fw-bold mb-0 text-primary">
                                        <i class="bi bi-award-fill me-1"></i>
                                        Valoración Formativa Cualitativa (Baremo Literal — Semestre Impar)
                                    </label>
                                    <span class="badge bg-primary">Semestre {{ esquemaActual?.semestre_numero || 'Impar' }}</span>
                                </div>
                                <p class="small text-muted mb-2">
                                    Seleccione la letra del baremo configurado; el sistema traducirá automáticamente el resultado al porcentaje y Semáforo de Ruta Crítica.
                                </p>
                                <SelectBuscable
                                    v-model="formSeg.seq_valoracion_literal"
                                    :options="opcionesBaremoLiteral"
                                    :disabled="modoSoloLecturaSeg"
                                    placeholder="Seleccionar calificación literal..."
                                    @change="onLiteralChange"
                                />
                                <div v-if="baremoSeleccionado" class="mt-2 p-2 rounded small border d-flex justify-content-between align-items-center" :class="baremoSeleccionado.aprobado ? 'bg-success-subtle border-success text-success' : 'bg-warning-subtle border-warning text-dark'">
                                    <span>
                                        <strong>Nivel:</strong> {{ baremoSeleccionado.nivel }} &bull;
                                        <strong>Referencia:</strong> {{ baremoSeleccionado.rango }}
                                    </span>
                                    <span class="badge" :class="baremoSeleccionado.aprobado ? 'bg-success' : 'bg-danger'">
                                        {{ baremoSeleccionado.aprobado ? 'Aprobado' : 'No Aprobado / Nivelación' }}
                                    </span>
                                </div>
                            </div>

                            <!-- MODALIDAD CUANTITATIVA (SEMESTRE PAR) -->
                            <div v-else class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <div class="card h-100 border p-3" style="background: var(--bg-th); border-color: var(--border) !important;">
                                        <div class="d-flex align-items-center justify-content-between mb-1">
                                            <label class="form-label fw-bold mb-0">Porcentaje de Avance</label>
                                            <span class="badge bg-dark">{{ formSeg.seq_valoracion_avance || 0 }}%</span>
                                        </div>
                                        <div class="input-group input-group-sm mb-2">
                                            <input
                                                type="number"
                                                min="0"
                                                max="100"
                                                v-model.number="formSeg.seq_valoracion_avance"
                                                class="form-control"
                                                :disabled="modoSoloLecturaSeg"
                                                @input="syncSemaforoDesdePorcentaje"
                                            />
                                            <span class="input-group-text">%</span>
                                        </div>
                                        <div class="progress mb-2" style="height: 8px;">
                                            <div
                                                class="progress-bar"
                                                :class="`bg-${estadoPorcentajeBadge.color}`"
                                                :style="{ width: `${Math.min(100, Math.max(0, formSeg.seq_valoracion_avance || 0))}%` }"
                                            ></div>
                                        </div>
                                        <small class="text-muted" style="font-size: 0.72rem;">
                                            Escala automática: ≥75% Conforme · 50–74% Observado · 25–49% No conforme · &lt;25% No viable
                                        </small>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card h-100 border p-3" style="background: var(--bg-th); border-color: var(--border) !important;">
                                        <label class="form-label fw-bold mb-2">
                                            <i class="bi bi-diagram-3-fill text-danger me-1"></i>Situación del Proyecto (por %)
                                        </label>
                                        <div class="p-2 rounded border mb-2 d-flex align-items-center justify-content-between" style="background: var(--bg-card);">
                                            <span class="small fw-semibold">Clasificación según {{ formSeg.seq_valoracion_avance || 0 }}%:</span>
                                            <span class="badge" :class="`bg-${estadoPorcentajeBadge.color}`">{{ estadoPorcentajeBadge.texto }}</span>
                                        </div>
                                        <div class="d-flex flex-column gap-1 small">
                                            <label class="form-check-label text-success fw-bold">
                                                <input type="radio" class="form-check-input me-1" v-model="formSeg.seq_estado_semaforo" value="conforme" :disabled="modoSoloLecturaSeg" />
                                                🟢 Conforme (75% – 100%)
                                            </label>
                                            <label class="form-check-label text-warning fw-bold">
                                                <input type="radio" class="form-check-input me-1" v-model="formSeg.seq_estado_semaforo" value="observado" :disabled="modoSoloLecturaSeg" />
                                                🟡 Observado (50% – 74%)
                                            </label>
                                            <label class="form-check-label text-danger fw-bold">
                                                <input type="radio" class="form-check-input me-1" v-model="formSeg.seq_estado_semaforo" value="no_conforme" :disabled="modoSoloLecturaSeg" />
                                                🔴 No conforme (25% – 49%)
                                            </label>
                                            <label class="form-check-label text-secondary fw-bold">
                                                <input type="radio" class="form-check-input me-1" v-model="formSeg.seq_estado_semaforo" value="no_viable" :disabled="modoSoloLecturaSeg" />
                                                ⚫ No viable (&lt; 25%)
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Acuerdos -->
                            <div class="card border p-3 mb-3" style="background: var(--bg-th); border-color: var(--border) !important;">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label fw-bold mb-0">Acuerdos y Compromisos</label>
                                    <button v-if="!modoSoloLecturaSeg" type="button" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill" @click="agregarAcuerdo">
                                        <i class="bi bi-plus-circle me-1"></i>Agregar
                                    </button>
                                </div>
                                <div class="form-text small mb-2">
                                    <i class="bi bi-info-circle me-1 text-primary"></i>Registra las tareas o correcciones pendientes con fecha límite para el próximo encuentro (alimenta el Semáforo de Ruta Crítica).
                                </div>
                                <div v-if="!formSeg.acuerdos?.length" class="text-muted small">Sin acuerdos registrados.</div>
                                <div v-for="(acue, aIdx) in formSeg.acuerdos" :key="aIdx" class="row g-2 mb-2 align-items-center p-2 rounded border" style="background: var(--bg-card);">
                                    <div class="col-md-5">
                                        <input v-model="acue.descripcion" type="text" class="form-control form-control-sm" placeholder="Compromiso..." :disabled="modoSoloLecturaSeg" />
                                    </div>
                                    <div class="col-md-3">
                                        <input v-model="acue.fecha_limite" type="date" class="form-control form-control-sm" :disabled="modoSoloLecturaSeg" />
                                    </div>
                                    <div class="col-md-3">
                                        <SelectBuscable
                                            v-model="acue.estado"
                                            :options="opcionesEstadoAcuerdo"
                                            size="sm"
                                            :disabled="modoSoloLecturaSeg"
                                        />
                                    </div>
                                    <div class="col-md-1 text-end" v-if="!modoSoloLecturaSeg">
                                        <button type="button" class="btn btn-sm btn-outline-danger p-0 px-1" @click="formSeg.acuerdos.splice(aIdx, 1)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Observaciones para este punto</label>
                                <textarea v-model="formSeg.seq_observaciones" :disabled="modoSoloLecturaSeg" class="form-control" rows="2"></textarea>
                            </div>

                            <div class="mb-3">
                                <div class="p-3 rounded border" style="background: var(--bg-th); border-color: var(--border) !important;">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="form-check form-switch m-0 p-0 d-flex align-items-center flex-shrink-0">
                                            <input class="form-check-input m-0" type="checkbox" id="chkSegUltimo" v-model="formSeg.es_ultimo_punto" :disabled="modoSoloLecturaSeg" style="cursor: pointer; width: 2.2em; height: 1.2em;" />
                                        </div>
                                        <div>
                                            <label class="form-check-label fw-bold m-0" for="chkSegUltimo" style="cursor: pointer;">
                                                ¿Es el último punto de control del semestre?
                                            </label>
                                            <div class="text-muted small">
                                                Al marcarlo como cumplido y último punto, se transfieren las observaciones y se habilita la Pre-Socialización.
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Programación directa del siguiente punto de control -->
                                    <div v-if="!formSeg.es_ultimo_punto && !modoSoloLecturaSeg" class="mt-3 pt-3 border-top">
                                        <div class="fw-bold small text-primary mb-2">
                                            <i class="bi bi-calendar-plus me-1"></i>Siguiente Punto de Control (Opcional — se creará automáticamente al guardar)
                                        </div>
                                        <div class="row g-2">
                                            <div class="col-md-7">
                                                <label class="form-label small fw-semibold mb-1">Nombre del siguiente punto</label>
                                                <input
                                                    v-model="formSeg.siguiente_puc_nombre"
                                                    type="text"
                                                    class="form-control form-control-sm"
                                                    :class="{ 'is-invalid': erroresSeg.siguiente_puc_nombre }"
                                                    placeholder="Ej: Revisión Avance Metodológico / Prototipo"
                                                />
                                                <div v-if="erroresSeg.siguiente_puc_nombre" class="invalid-feedback">{{ erroresSeg.siguiente_puc_nombre }}</div>
                                            </div>
                                            <div class="col-md-5">
                                                <label class="form-label small fw-semibold mb-1">Fecha límite</label>
                                                <input
                                                    v-model="formSeg.siguiente_puc_fecha"
                                                    type="date"
                                                    :min="fechaHoy"
                                                    :max="$page?.props?.periodoActivo?.fecha_fin"
                                                    class="form-control form-control-sm"
                                                    :class="{ 'is-invalid': erroresSeg.siguiente_puc_fecha }"
                                                />
                                                <div v-if="erroresSeg.siguiente_puc_fecha" class="invalid-feedback">{{ erroresSeg.siguiente_puc_fecha }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Asistencia y Participación Individual -->
                            <div class="card border p-3" style="background: var(--bg-th); border-color: var(--border) !important;">
                                <label class="form-label fw-bold mb-2">Control Individual de Asistencia y Valoración</label>
                                <div v-for="int in puntoActual?.equipo?.integrantes" :key="int.ein_id" class="p-2 mb-2 rounded border" style="background: var(--bg-card);">
                                    <template v-if="formSeg.asistencias?.[int.ein_id_usu]">
                                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                            <div>
                                                <strong class="small">{{ int.usuario?.usu_primer_nombre }} {{ int.usuario?.usu_primer_apellido }}</strong>
                                                <span class="text-muted small ms-2">CI: {{ int.usuario?.usu_cedula }}</span>
                                            </div>
                                            <div class="d-flex align-items-center gap-3 flex-wrap">
                                                <label class="form-check-label small" :class="{ 'text-muted': formSeg.asistencias[int.ein_id_usu].justificado }">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input me-1"
                                                        v-model="formSeg.asistencias[int.ein_id_usu].asistio"
                                                        :disabled="modoSoloLecturaSeg || formSeg.asistencias[int.ein_id_usu].justificado"
                                                        @change="onToggleAsistio(int.ein_id_usu)"
                                                    />
                                                    Asistió
                                                </label>
                                                <label class="form-check-label small" :class="{ 'text-muted': !formSeg.asistencias[int.ein_id_usu].asistio || formSeg.asistencias[int.ein_id_usu].justificado }">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input me-1"
                                                        v-model="formSeg.asistencias[int.ein_id_usu].participo"
                                                        :disabled="modoSoloLecturaSeg || !formSeg.asistencias[int.ein_id_usu].asistio || formSeg.asistencias[int.ein_id_usu].justificado"
                                                    />
                                                    Participó
                                                </label>
                                                <label class="form-check-label small">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input me-1"
                                                        v-model="formSeg.asistencias[int.ein_id_usu].justificado"
                                                        :disabled="modoSoloLecturaSeg"
                                                        @change="onToggleJustificado(int.ein_id_usu)"
                                                    />
                                                    Justificado
                                                </label>
                                                <div v-if="esSemestreImpar" style="min-width: 170px;">
                                                    <SelectBuscable
                                                        v-model="formSeg.asistencias[int.ein_id_usu].valoracion_literal"
                                                        :options="opcionesLiteralIndividual"
                                                        size="sm"
                                                        :disabled="modoSoloLecturaSeg"
                                                        placeholder="Literal..."
                                                        @change="syncLiteralEstudiante(int.ein_id_usu)"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                        <input
                                            v-model="formSeg.asistencias[int.ein_id_usu].observacion"
                                            type="text"
                                            class="form-control form-control-sm mt-2"
                                            placeholder="Observación individual (opcional)..."
                                            :disabled="modoSoloLecturaSeg"
                                        />
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            {{ modoSoloLecturaSeg ? "Cerrar" : "Cancelar" }}
                        </button>
                        <button v-if="!modoSoloLecturaSeg" type="submit" class="btn btn-danger fw-bold" :disabled="loadingSeg">
                            <span v-if="loadingSeg" class="spinner-border spinner-border-sm me-2"></span>
                            Guardar Seguimiento
                        </button>
                    </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { router } from "@inertiajs/vue3";
import { Modal } from "bootstrap";
import SelectBuscable from "@/Components/SelectBuscable.vue";

const props = defineProps({
    tiposEncuentro: { type: Array, default: () => [] },
    esquemasPorSeccion: { type: Object, default: () => ({}) },
});

const emit = defineEmits(["guardado"]);

const puntoActual = ref(null);
const modoSoloLecturaSeg = ref(false);
const loadingSeg = ref(false);
const erroresSeg = ref({});
let mSeg = null;

const BAREMO_DEFECTO = [
    { letra: "A", nivel: "Excelente", rango: "19-20", aprobado: true },
    { letra: "B", nivel: "Bueno / Aprobado", rango: "16-18", aprobado: true },
    { letra: "C", nivel: "En Proceso / Nivelación", rango: "12-15", aprobado: false },
    { letra: "D", nivel: "Deficiente", rango: "01-11", aprobado: false },
];

const fechaHoy = computed(() => new Date().toISOString().split("T")[0]);
const fechaMaximaReprog = computed(() => {
    const d = new Date();
    d.setMonth(d.getMonth() + 1);
    return d.toISOString().split("T")[0];
});

const esquemaActual = computed(() => {
    const secId = puntoActual.value?.equipo?.equ_id_sec;
    return secId ? props.esquemasPorSeccion?.[secId] || null : null;
});

const esSemestreImpar = computed(() => {
    if (esquemaActual.value?.es_semestre_impar !== undefined) {
        return Boolean(esquemaActual.value.es_semestre_impar);
    }
    const cod = String(puntoActual.value?.equipo?.seccion?.sec_codigo || "");
    const d = parseInt(cod.charAt(0));
    return !isNaN(d) && d % 2 !== 0;
});

const baremoActivo = computed(() => {
    const b = esquemaActual.value?.baremo;
    return Array.isArray(b) && b.length > 0 ? b : BAREMO_DEFECTO;
});

const opcionesTipoEncuentro = computed(() => [
    { value: null, label: "Seleccione tipo de encuentro..." },
    ...(props.tiposEncuentro || []).map((te) => ({
        value: te.tep_id,
        label: te.tep_nombre,
    })),
]);

const opcionesBaremoLiteral = computed(() => [
    { value: "", label: "Seleccionar calificación literal..." },
    ...baremoActivo.value.map((item) => ({
        value: item.letra,
        label: `${item.letra} — ${item.nivel} (Ref: ${item.rango})`,
        sublabel: item.aprobado ? "Aprobado" : "Requiere Nivelación",
    })),
]);

const opcionesLiteralIndividual = computed(() => [
    { value: "", label: "Literal..." },
    ...baremoActivo.value.map((item) => ({
        value: item.letra,
        label: `${item.letra} (${item.nivel})`,
    })),
]);

const opcionesEstadoAcuerdo = [
    { value: "pendiente", label: "Pendiente" },
    { value: "en_proceso", label: "En Proceso" },
    { value: "rectificado", label: "Rectificado" },
];

const estadoPorcentajeBadge = computed(() => {
    const p = Number(formSeg.value.seq_valoracion_avance || 0);
    if (p >= 75) return { color: "success", texto: "🟢 Conforme", semaforo: "conforme" };
    if (p >= 50) return { color: "warning", texto: "🟡 Observado", semaforo: "observado" };
    if (p >= 25) return { color: "danger", texto: "🔴 No conforme", semaforo: "no_conforme" };
    return { color: "dark", texto: "⚫ No viable", semaforo: "no_viable" };
});

const syncSemaforoDesdePorcentaje = () => {
    formSeg.value.seq_estado_semaforo = estadoPorcentajeBadge.value.semaforo;
};

const formSeg = ref({
    seq_cumplido: null,
    seq_cumplio_previo: null,
    seq_obs_previas_verificacion: "",
    seq_observaciones: "",
    seq_id_tipo_encuentro: null,
    seq_estado_semaforo: "conforme",
    seq_valoracion_avance: 50,
    seq_valoracion_literal: "",
    seq_nivel_logro: "",
    seq_equivalencia_referencial: "",
    seq_aprobado_cualitativo: null,
    es_ultimo_punto: false,
    siguiente_puc_nombre: "",
    siguiente_puc_fecha: "",
    desea_reprogramar: false,
    nueva_fecha: "",
    nueva_semana: "",
    acuerdos: [],
    asistencias: {},
});

const baremoSeleccionado = computed(() =>
    baremoActivo.value.find((b) => b.letra === formSeg.value.seq_valoracion_literal) || null,
);

const onLiteralChange = () => {
    const b = baremoSeleccionado.value;
    if (!b) return;
    const mapaLitAvance = { A: 95, B: 80, C: 60, D: 35 };
    formSeg.value.seq_valoracion_avance = mapaLitAvance[String(b.letra || "").toUpperCase()] ?? (b.aprobado ? 80 : 40);
    formSeg.value.seq_nivel_logro = b.nivel;
    formSeg.value.seq_equivalencia_referencial = b.rango;
    formSeg.value.seq_aprobado_cualitativo = Boolean(b.aprobado);
    formSeg.value.seq_estado_semaforo = b.letra === "D" ? "riesgo_alto" : b.letra === "C" ? "acelerar" : "en_ruta";
};

const syncLiteralEstudiante = (usuId) => {
    const letra = formSeg.value.asistencias[usuId]?.valoracion_literal;
    const item = baremoActivo.value.find((b) => b.letra === letra);
    formSeg.value.asistencias[usuId].nivel_logro = item ? item.nivel : "";
};

const onToggleAsistio = (usuId) => {
    const a = formSeg.value.asistencias?.[usuId];
    if (!a) return;
    if (a.asistio) {
        a.justificado = false;
    } else {
        a.participo = false;
    }
};

const onToggleJustificado = (usuId) => {
    const a = formSeg.value.asistencias?.[usuId];
    if (!a) return;
    if (a.justificado) {
        a.asistio = false;
        a.participo = false;
    }
};

const agregarAcuerdo = () => {
    formSeg.value.acuerdos.push({ descripcion: "", fecha_limite: "", estado: "pendiente" });
};

const abrir = (puc, soloLectura = false) => {
    puntoActual.value = puc;
    modoSoloLecturaSeg.value = soloLectura;
    const initialAsist = {};
    const prevSeg = puc.seguimientos?.[0];
    const prevMap = {};
    (prevSeg?.asistencias || []).forEach((a) => {
        prevMap[a.apc_id_usu] = a;
    });

    (puc.equipo?.integrantes || []).forEach((i) => {
        const prev = prevMap[i.ein_id_usu];
        const justificadoInit = prev ? Boolean(prev.apc_justificado) : false;
        const asistioInit = justificadoInit ? false : (prev ? Boolean(prev.apc_asistio) : true);
        const participoInit = (asistioInit && !justificadoInit && prev) ? Boolean(prev.apc_participo) : false;
        initialAsist[i.ein_id_usu] = {
            asistio: asistioInit,
            participo: participoInit,
            justificado: justificadoInit,
            valoracion_literal: prev?.apc_valoracion_literal || "",
            nivel_logro: prev?.apc_nivel_logro || "",
            observacion: prev?.apc_observaciones || "",
        };
    });

    formSeg.value = {
        seq_cumplido: prevSeg?.seq_cumplido !== undefined && prevSeg?.seq_cumplido !== null ? Boolean(prevSeg.seq_cumplido) : null,
        seq_cumplio_previo: prevSeg?.seq_cumplio_previo !== undefined && prevSeg?.seq_cumplio_previo !== null ? Boolean(prevSeg.seq_cumplio_previo) : null,
        seq_obs_previas_verificacion: prevSeg?.seq_obs_previas_verificacion || "",
        seq_observaciones: prevSeg?.seq_observaciones || "",
        seq_id_tipo_encuentro: prevSeg?.seq_id_tipo_encuentro || (props.tiposEncuentro?.[0]?.tep_id || null),
        seq_estado_semaforo: prevSeg?.seq_estado_semaforo || "conforme",
        seq_valoracion_avance: prevSeg?.seq_valoracion_avance ?? 50,
        seq_valoracion_literal: prevSeg?.seq_valoracion_literal || "",
        seq_nivel_logro: prevSeg?.seq_nivel_logro || "",
        seq_equivalencia_referencial: prevSeg?.seq_equivalencia_referencial || "",
        seq_aprobado_cualitativo: prevSeg?.seq_aprobado_cualitativo ?? null,
        es_ultimo_punto: Boolean(puc.puc_es_ultimo_punto),
        siguiente_puc_nombre: "",
        siguiente_puc_fecha: "",
        desea_reprogramar: false,
        nueva_fecha: puc.puc_fecha_reprogramada || "",
        nueva_semana: puc.puc_semana || "",
        acuerdos: (prevSeg?.acuerdos || []).map((a) => ({
            acue_id: a.acue_id,
            descripcion: a.acue_descripcion,
            fecha_limite: a.acue_fecha_limite ? a.acue_fecha_limite.split("T")[0] : "",
            estado: a.acue_estado || "pendiente",
        })),
        asistencias: initialAsist,
    };

    erroresSeg.value = {};
    const el = document.getElementById("mSeg");
    if (el) {
        mSeg = Modal.getInstance(el) || new Modal(el);
        mSeg.show();
    }
};

const guardarSeguimiento = () => {
    if (modoSoloLecturaSeg.value) return;
    if (formSeg.value.seq_cumplido === null) {
        erroresSeg.value = { seq_cumplido: "Debe indicar si el equipo cumplió o no." };
        return;
    }

    loadingSeg.value = true;
    router.post(`/profesor/puntos-control/${puntoActual.value.puc_id}/seguimiento`, formSeg.value, {
        preserveScroll: true,
        onSuccess: () => {
            loadingSeg.value = false;
            mSeg?.hide();
            emit("guardado");
        },
        onError: (e) => {
            erroresSeg.value = e;
            loadingSeg.value = false;
        },
    });
};

defineExpose({ abrir });
</script>

<style scoped>
.mh {
    background: #1a1d23;
    border-bottom: 2px solid #dc3545;
    padding: 1rem 1.3rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}
.mh h5 {
    color: #fff;
    font-weight: 800;
    margin: 0;
    font-size: 1rem;
    display: flex;
    align-items: center;
}
.modal-content {
    background: var(--bg-card, #ffffff);
    color: var(--text-main, #1a1a1a);
    border: 1px solid var(--border, #dee2e6) !important;
    overflow: hidden;
}
.modal-body,
.modal-footer {
    background: var(--bg-card, #ffffff);
    color: var(--text-main, #1a1a1a);
    border-color: var(--border, #dee2e6) !important;
}
.form-control,
.form-select {
    background: var(--bg-card, #ffffff);
    color: var(--text-main, #1a1a1a);
    border-color: var(--border, #dee2e6);
}
</style>
