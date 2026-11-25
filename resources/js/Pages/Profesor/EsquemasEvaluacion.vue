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
                        Esquemas de Evaluación Configurables
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button
                        class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm"
                        @click="abrirModalCrear"
                    >
                        <i class="bi bi-plus-circle me-1"></i> Nuevo Esquema
                    </button>
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
                                <span class="badge border badge-themed">
                                    Mín: {{ esq.esq_escala_min }} / Máx: {{ esq.esq_escala_max }} (Aprueba: {{ esq.esq_nota_aprobatoria }})
                                </span>
                            </div>
                            <div class="card-body">
                                <h5 class="fw-bold text-danger mb-1">{{ esq.esq_nombre }}</h5>
                                <p class="text-muted small mb-3">{{ esq.esq_descripcion || 'Sin descripción adicional.' }}</p>

                                <!-- Criterios -->
                                <div v-if="esq.esq_criterios && esq.esq_criterios.length" class="mb-3">
                                    <div class="small fw-semibold text-muted mb-2">Criterios de evaluación y pesos:</div>
                                    <div class="d-flex flex-column gap-1">
                                        <div
                                            v-for="(crit, idx) in esq.esq_criterios"
                                            :key="idx"
                                            class="d-flex justify-content-between align-items-center p-2 rounded small criterio-item"
                                        >
                                            <span>{{ crit.nombre }}</span>
                                            <span class="badge bg-dark">{{ crit.peso }}%</span>
                                        </div>
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
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Modo de Evaluación</label>
                                    <select v-model="form.esq_modo" class="form-select" required>
                                        <option value="cuantitativo">Cuantitativo (Escala Numérica)</option>
                                        <option value="cualitativo_respaldado">Cualitativo Respaldado</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Escala Mín / Máx</label>
                                    <div class="input-group">
                                        <input v-model.number="form.esq_escala_min" type="number" step="0.5" class="form-control" placeholder="1" required />
                                        <span class="input-group-text">-</span>
                                        <input v-model.number="form.esq_escala_max" type="number" step="0.5" class="form-control" placeholder="20" required />
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Nota Aprobatoria</label>
                                    <input v-model.number="form.esq_nota_aprobatoria" type="number" step="0.5" class="form-control" placeholder="16" required />
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
                                <div v-for="(crit, idx) in form.esq_criterios" :key="idx" class="row g-2 mb-2 align-items-center">
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
                                <select v-model="formAsignar.esq_sec_id_sec" class="form-select" required>
                                    <option value="" disabled>Seleccione una sección...</option>
                                    <option v-for="sec in secciones" :key="sec.sec_id" :value="sec.sec_id">
                                        {{ sec.sec_nombre || ('Sección ' + sec.sec_codigo + (sec.trayecto ? ' - ' + sec.trayecto.tra_nombre : '') + (sec.turno ? ' (' + sec.turno.tur_nombre + ')' : '')) }}
                                    </option>
                                </select>
                                <div v-if="secciones.length === 0" class="text-danger small mt-1">
                                    <i class="bi bi-exclamation-triangle me-1"></i> No hay secciones disponibles para asignar.
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Lapso / Semestre</label>
                                <select v-model="formAsignar.esq_sec_lapso" class="form-select">
                                    <option value="semestre_1">Semestre 1</option>
                                    <option value="semestre_2">Semestre 2</option>
                                    <option value="anual">Completo / Anual</option>
                                </select>
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
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link, useForm, usePage } from '@inertiajs/vue3'
import bootstrap from 'bootstrap/dist/js/bootstrap.bundle.min.js'
import SidebarProfesor from "@/Components/SidebarProfesor.vue"

const props = defineProps({
    esquemas: { type: Array, default: () => [] },
    secciones: { type: Array, default: () => [] },
    esCoordinador: { type: Boolean, default: false },
    usuarioActual: { type: Object, default: () => ({}) },
})

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

const form = useForm({
    esq_nombre: '',
    esq_descripcion: '',
    esq_modo: 'cuantitativo',
    esq_escala_min: 1,
    esq_escala_max: 20,
    esq_nota_aprobatoria: 16,
    esq_criterios: [
        { nombre: 'Informe Técnico', peso: 40 },
        { nombre: 'Avance Práctico', peso: 30 },
        { nombre: 'Participación y Puntualidad', peso: 30 },
    ],
})

const formAsignar = useForm({
    esq_sec_id_esq: null,
    esq_sec_id_sec: '',
    esq_sec_lapso: 'semestre_1',
    accion: 'asignar',
})

const totalPeso = computed(() => {
    return (form.esq_criterios || []).reduce((acc, curr) => acc + (Number(curr.peso) || 0), 0)
})

const agregarCriterio = () => {
    form.esq_criterios.push({ nombre: '', peso: 0 })
}

const eliminarCriterio = (idx) => {
    form.esq_criterios.splice(idx, 1)
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
    form.esq_criterios = [
        { nombre: 'Informe Técnico', peso: 40 },
        { nombre: 'Avance Práctico', peso: 30 },
        { nombre: 'Participación y Puntualidad', peso: 30 },
    ]
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
    form.esq_criterios = esq.esq_criterios ? JSON.parse(JSON.stringify(esq.esq_criterios)) : []
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
    form.esq_criterios = esq.esq_criterios ? JSON.parse(JSON.stringify(esq.esq_criterios)) : []
    modalEsquemaInstance?.show()
}

const guardarEsquema = () => {
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
