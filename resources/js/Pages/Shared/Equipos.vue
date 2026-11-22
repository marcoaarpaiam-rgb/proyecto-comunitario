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
                            class="bi bi-people me-2 text-danger d-none d-sm-inline"
                        ></i
                        >{{ esProfesor ? "Mis Equipos" : "Equipos" }}
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button
                        v-if="puedeCrear"
                        class="btn btn-danger btn-sm fw-bold rounded-pill px-3"
                        @click="abrirModalCrear"
                    >
                        <i class="bi bi-plus-lg me-1"></i>Nuevo Equipo
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
                <!-- Banner si viene filtrado por Sección -->
                <div
                    v-if="seccion"
                    class="mb-3 d-flex align-items-center justify-content-between p-3 rounded"
                    style="
                        background: rgba(220, 53, 69, 0.08);
                        border: 1px solid rgba(220, 53, 69, 0.2);
                    "
                >
                    <div class="d-flex align-items-center flex-wrap gap-2">
                        <span class="badge bg-danger fs-6"
                            >Sección {{ seccion.sec_codigo }}</span
                        >
                        <span class="fw-bold" style="color: var(--text-main)">{{
                            seccion.trayecto
                        }}</span>
                        <span class="badge bg-secondary"
                            >Turno {{ seccion.turno }}</span
                        >
                    </div>
                    <Link
                        v-if="modoProfesor || esProfesor"
                        href="/profesor/secciones"
                        class="btn btn-sm btn-outline-danger fw-bold"
                    >
                        <i class="bi bi-arrow-left me-1"></i>Volver a Mis
                        Secciones
                    </Link>
                </div>

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
                            {{
                                verDisueltos
                                    ? "Equipos Disueltos"
                                    : "Equipos Activos"
                            }}
                        </h4>
                        <p
                            style="
                                color: var(--text-muted);
                                font-size: 0.85rem;
                                margin-top: 2px;
                            "
                        >
                            Gestión de equipos e integrantes
                        </p>
                    </div>
                    <div class="d-flex gap-2 flex-wrap">
                        <button
                            class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                            @click="verDisueltos = !verDisueltos"
                        >
                            <i class="bi bi-arrow-counterclockwise me-1"></i
                            >{{
                                verDisueltos ? "Ver Activos" : "Ver Disueltos"
                            }}
                        </button>
                    </div>
                </div>

                <!-- Barra compacta de filtros de semáforo -->
                <div v-if="Object.keys(props.diagnosticosEquipos || {}).length > 0" class="mb-3 d-flex flex-wrap gap-2 align-items-center">
                    <span class="small fw-bold text-muted me-1"><i class="bi bi-funnel"></i> Semáforo:</span>
                    <button
                        class="btn btn-sm py-1 px-2"
                        :class="filtroSemaforo === '' ? 'btn-danger' : 'btn-outline-secondary'"
                        @click="filtroSemaforo = ''; pagina = 1"
                    >
                        Todos ({{ listaActual.length }})
                    </button>
                    <button
                        class="btn btn-sm py-1 px-2"
                        :class="filtroSemaforo === 'en_ruta' ? 'btn-success' : 'btn-outline-success'"
                        @click="filtroSemaforo = filtroSemaforo === 'en_ruta' ? '' : 'en_ruta'; pagina = 1"
                    >
                        🟢 En ruta ({{ contarPorSemaforo('en_ruta') }})
                    </button>
                    <button
                        class="btn btn-sm py-1 px-2"
                        :class="filtroSemaforo === 'acelerar' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark'"
                        @click="filtroSemaforo = filtroSemaforo === 'acelerar' ? '' : 'acelerar'; pagina = 1"
                    >
                        🟡 Acelerar ({{ contarPorSemaforo('acelerar') }})
                    </button>
                    <button
                        class="btn btn-sm py-1 px-2"
                        :class="filtroSemaforo === 'riesgo_alto' ? 'btn-danger' : 'btn-outline-danger'"
                        @click="filtroSemaforo = filtroSemaforo === 'riesgo_alto' ? '' : 'riesgo_alto'; pagina = 1"
                    >
                        🔴 Riesgo alto ({{ contarPorSemaforo('riesgo_alto') }})
                    </button>
                    <button
                        class="btn btn-sm py-1 px-2"
                        :class="filtroSemaforo === 'inviable' ? 'btn-dark' : 'btn-outline-dark'"
                        @click="filtroSemaforo = filtroSemaforo === 'inviable' ? '' : 'inviable'; pagina = 1"
                    >
                        ⛔ Inviable ({{ contarPorSemaforo('inviable') }})
                    </button>
                </div>

                <div class="sc">
                    <div
                        style="
                            padding: 0.9rem 1rem;
                            border-bottom: 1px solid var(--border);
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            flex-wrap: wrap;
                        "
                    >
                        <div class="srch">
                            <i class="bi bi-search"></i
                            ><input
                                v-model="busqueda"
                                type="text"
                                placeholder="Buscar por código o título..."
                            />
                        </div>
                        <span
                            class="ms-auto"
                            style="
                                font-size: 0.82rem;
                                color: var(--text-muted);
                                font-weight: 600;
                            "
                            >{{ equiposFiltrados.length }} equipo(s)</span
                        >
                    </div>
                    <div class="table-responsive">
                        <table class="ct">
                            <thead>
                                <tr>
                                    <th class="ps-4">Código</th>
                                    <th>Título</th>
                                    <th>Semáforo</th>
                                    <th>Sección</th>
                                    <th>Trayecto</th>
                                    <th>Integrantes</th>
                                    <th>Comunidad</th>
                                    <th class="text-end pe-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="equiposPaginados.length === 0">
                                    <td
                                        colspan="8"
                                        class="text-center py-5"
                                        style="color: var(--text-muted)"
                                    >
                                        Sin equipos
                                    </td>
                                </tr>
                                <tr
                                    v-for="equ in equiposPaginados"
                                    :key="equ.equ_id"
                                >
                                    <td
                                        class="ps-4 fw-bold"
                                        style="color: var(--rojo)"
                                    >
                                        {{ codigoLimpio(equ.equ_codigo) }}
                                    </td>
                                    <td
                                        style="
                                            max-width: 180px;
                                            white-space: nowrap;
                                            overflow: hidden;
                                            text-overflow: ellipsis;
                                            font-size: 0.88rem;
                                        "
                                    >
                                        {{ equ.equ_titulo }}
                                    </td>
                                    <td>
                                        <span
                                            v-if="props.diagnosticosEquipos?.[equ.equ_id]"
                                            class="badge"
                                            :class="getIconoSemaforo(props.diagnosticosEquipos[equ.equ_id]?.semaforo)?.class"
                                        >
                                            {{ getIconoSemaforo(props.diagnosticosEquipos[equ.equ_id]?.semaforo)?.icon }}
                                            {{ getEtiquetaSemaforo(props.diagnosticosEquipos[equ.equ_id]?.semaforo) }}
                                        </span>
                                        <span v-else class="text-muted small">—</span>
                                    </td>
                                    <td>
                                        <span class="bs bp">{{
                                            equ.seccion?.sec_codigo
                                        }}</span>
                                    </td>
                                    <td
                                        style="
                                            font-size: 0.85rem;
                                            color: var(--text-muted);
                                        "
                                    >
                                        {{ equ.trayecto?.tra_nombre }}
                                    </td>
                                    <td>
                                        <button
                                            class="btn btn-sm btn-outline-secondary"
                                            style="font-size: 0.78rem"
                                            @click="verIntegrantes(equ)"
                                        >
                                            <i class="bi bi-people me-1"></i
                                            >{{
                                                (equ.integrantes || []).filter(
                                                    (i) =>
                                                        i.ein_status !== false,
                                                ).length
                                            }}
                                        </button>
                                    </td>
                                    <td
                                        style="
                                            font-size: 0.85rem;
                                            color: var(--text-muted);
                                        "
                                    >
                                        {{
                                            equ.proyecto_comunidad?.comunidad
                                                ?.com_nombre || "—"
                                        }}
                                    </td>
                                    <td class="text-end pe-4">
                                        <template v-if="!verDisueltos">
                                            <Link
                                                :href="`${base}/equipos/${equ.equ_id}/expediente`"
                                                class="btn btn-sm btn-outline-info me-1"
                                                title="Expediente"
                                                ><i
                                                    class="bi bi-folder2-open"
                                                ></i
                                            ></Link>
                                            <button
                                                class="btn btn-sm btn-outline-warning me-1"
                                                @click="
                                                    abrirHistorialAcademico(equ)
                                                "
                                                title="Historial Académico"
                                            >
                                                <i
                                                    class="bi bi-mortarboard-fill"
                                                ></i>
                                            </button>
                                            <!-- Botón Socialización Final y Evaluación (4 Actores y PIN) -->
                                            <button
                                                v-if="equ.proyecto_comunidad || equ.proyectoComunidad || !esEquipoImpar(equ)"
                                                class="btn btn-sm btn-outline-danger me-1"
                                                @click="abrirModalSocializacion(equ)"
                                                title="Socialización Final y Evaluación (PIN y 4 Actores)"
                                            >
                                                <i class="bi bi-award-fill"></i>
                                            </button>
                                            <!-- Botón Promoción Semestral a Par (Coordinador) -->
                                            <button
                                                v-if="!esProfesor && esEquipoImpar(equ)"
                                                class="btn btn-sm btn-outline-primary me-1"
                                                @click="promoverEquipoDirecto(equ)"
                                                title="Promover a Semestre Par (Transición Semestral)"
                                            >
                                                <i class="bi bi-rocket-takeoff-fill"></i>
                                            </button>
                                            <a
                                                v-if="equ.resultado_proyecto"
                                                :href="`${base}/equipos/${equ.equ_id}/paquete-cierre`"
                                                class="btn btn-sm btn-outline-secondary me-1"
                                                title="Paquete de cierre (ZIP)"
                                            >
                                                <i class="bi bi-file-zip"></i>
                                            </a>
                                            <button
                                                class="btn btn-sm btn-outline-primary me-1"
                                                @click="abrirModalEditar(equ)"
                                                title="Editar título"
                                            >
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <button
                                                class="btn btn-sm btn-outline-danger"
                                                @click="abrirDisolver(equ)"
                                                title="Disolver"
                                            >
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </template>
                                        <template v-else>
                                            <button
                                                class="btn btn-sm btn-outline-success"
                                                @click="reactivar(equ)"
                                                title="Reactivar equipo"
                                            >
                                                <i
                                                    class="bi bi-arrow-counterclockwise me-1"
                                                ></i
                                                >Reactivar
                                            </button>
                                            <button
                                                class="btn btn-sm btn-outline-danger ms-1"
                                                @click="
                                                    eliminarDefinitivamente(equ)
                                                "
                                                title="Eliminar definitivamente"
                                            >
                                                <i class="bi bi-trash me-1"></i
                                                >Eliminar
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
                        <span
                            style="font-size: 0.82rem; color: var(--text-muted)"
                            >{{ desde + 1 }}–{{
                                Math.min(hasta, equiposFiltrados.length)
                            }}
                            de {{ equiposFiltrados.length }}</span
                        >
                        <div class="d-flex gap-1">
                            <button
                                class="btn btn-sm btn-outline-secondary"
                                :disabled="pagina === 1"
                                @click="pagina--"
                            >
                                <i class="bi bi-chevron-left"></i>
                            </button>
                            <button
                                v-for="p in paginasV"
                                :key="p"
                                class="btn btn-sm"
                                :class="
                                    p === pagina
                                        ? 'btn-danger'
                                        : 'btn-outline-secondary'
                                "
                                @click="pagina = p"
                            >
                                {{ p }}
                            </button>
                            <button
                                class="btn btn-sm btn-outline-secondary"
                                :disabled="pagina === totalPaginas"
                                @click="pagina++"
                            >
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Crear (2 pasos) -->
        <div class="modal fade" id="mCrear" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>Nuevo Equipo — Paso {{ paso }}/2</h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form
                        @submit.prevent="
                            paso === 1 ? irPaso2() : guardarNuevo()
                        "
                    >
                        <div class="modal-body">
                            <!-- Indicador pasos -->
                            <div class="step-ind">
                                <div
                                    class="step-circle"
                                    :class="
                                        paso >= 1 ? 'sc-active' : 'sc-pending'
                                    "
                                >
                                    1
                                </div>
                                <span
                                    style="font-size: 0.82rem; font-weight: 700"
                                    :style="
                                        paso >= 1
                                            ? 'color:var(--rojo)'
                                            : 'color:var(--text-muted)'
                                    "
                                    >Equipo e Integrantes</span
                                >
                                <div
                                    class="step-line"
                                    :class="paso >= 2 ? 'done' : ''"
                                ></div>
                                <div
                                    class="step-circle"
                                    :class="
                                        paso >= 2 ? 'sc-active' : 'sc-pending'
                                    "
                                >
                                    2
                                </div>
                                <span
                                    style="font-size: 0.82rem; font-weight: 700"
                                    :style="
                                        paso >= 2
                                            ? 'color:var(--rojo)'
                                            : 'color:var(--text-muted)'
                                    "
                                    >Proyecto (opcional)</span
                                >
                            </div>

                            <!-- PASO 1 -->
                            <div v-if="paso === 1">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                            >Sección
                                            <span class="text-danger"
                                                >*</span
                                            ></label
                                        >
                                        <select
                                            v-model="formNuevo.equ_id_sec"
                                            class="form-select"
                                            :class="{
                                                'is-invalid':
                                                    errores.equ_id_sec,
                                            }"
                                            @change="onSeccionChange"
                                        >
                                            <option value="">
                                                Seleccionar...
                                            </option>
                                            <option
                                                v-for="sec in secciones"
                                                :key="sec.sec_id"
                                                :value="sec.sec_id"
                                            >
                                                {{ sec.sec_codigo }} —
                                                {{ sec.trayecto?.tra_nombre }}
                                                ({{ sec.turno?.tur_nombre }})
                                            </option>
                                        </select>
                                        <div
                                            v-if="errores.equ_id_sec"
                                            class="invalid-feedback"
                                        >
                                            {{ errores.equ_id_sec }}
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                            >Trayecto (automático)</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control"
                                            :value="
                                                trayectoSeleccionado ||
                                                'Se asigna al seleccionar sección'
                                            "
                                            readonly
                                            style="
                                                background: var(
                                                    --bg-th
                                                ) !important;
                                            "
                                        />
                                    </div>
                                    <!-- Banner informativo según sea primer equipo o segundo en adelante -->
                                    <div
                                        class="col-12"
                                        v-if="
                                            formNuevo.equ_id_sec &&
                                            esAutoCompletado
                                        "
                                    >
                                        <div
                                            class="py-2 px-3 rounded small d-flex align-items-center justify-content-between"
                                            style="
                                                background: rgba(
                                                    13,
                                                    110,
                                                    253,
                                                    0.08
                                                );
                                                border-left: 3px solid #0d6efd;
                                                color: var(--text-main);
                                            "
                                        >
                                            <div>
                                                <i
                                                    class="bi bi-magic text-primary me-1"
                                                ></i>
                                                <strong
                                                    >Datos
                                                    auto-completados:</strong
                                                >
                                                Semestre ({{
                                                    formNuevo.equ_semestre
                                                }}) y Total de Equipos ({{
                                                    formNuevo.equ_total_seccion
                                                }}) configurados según el equipo
                                                previo de este trayecto. Solo
                                                debes indicar el
                                                <strong>N° de Equipo</strong>.
                                            </div>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-link p-0 text-decoration-none fw-semibold text-primary ms-2 flex-shrink-0"
                                                @click="
                                                    editarParametrosManual =
                                                        !editarParametrosManual
                                                "
                                            >
                                                <i
                                                    class="bi bi-pencil-square me-1"
                                                ></i
                                                >{{
                                                    editarParametrosManual
                                                        ? "Bloquear"
                                                        : "Modificar"
                                                }}
                                            </button>
                                        </div>
                                    </div>
                                    <div
                                        class="col-12"
                                        v-else-if="formNuevo.equ_id_sec"
                                    >
                                        <div
                                            class="py-2 px-3 rounded small"
                                            style="
                                                background: rgba(
                                                    255,
                                                    193,
                                                    7,
                                                    0.12
                                                );
                                                border-left: 3px solid #ffc107;
                                                color: var(--text-main);
                                            "
                                        >
                                            <i
                                                class="bi bi-flag-fill text-warning me-1"
                                            ></i>
                                            <strong
                                                >Primer equipo de este
                                                trayecto:</strong
                                            >
                                            Define el Semestre y Total de
                                            Equipos para la sección. A partir
                                            del 2do equipo, estos datos se
                                            auto-completarán solos.
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div
                                            class="d-flex align-items-end mb-1"
                                            style="min-height: 22px"
                                        >
                                            <label
                                                class="form-label fw-semibold small mb-0"
                                                :class="{
                                                    'text-danger':
                                                        esAutoCompletado,
                                                }"
                                            >
                                                N° Equipo
                                                <span class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                        </div>
                                        <input
                                            type="number"
                                            class="form-control"
                                            :class="{
                                                'border-danger fw-bold shadow-sm':
                                                    esAutoCompletado,
                                                'is-invalid':
                                                    errores.equ_numero_grupo,
                                            }"
                                            v-model="formNuevo.equ_numero_grupo"
                                            min="1"
                                            :max="
                                                formNuevo.equ_total_seccion ||
                                                99
                                            "
                                            @input="validarNumeroVsTotal"
                                            @keypress="
                                                (e) =>
                                                    !/[0-9]/.test(e.key) &&
                                                    e.preventDefault()
                                            "
                                        />
                                        <div
                                            v-if="errores.equ_numero_grupo"
                                            class="text-danger small mt-1"
                                        >
                                            {{ errores.equ_numero_grupo }}
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div
                                            class="d-flex align-items-end justify-content-between mb-1"
                                            style="min-height: 22px"
                                        >
                                            <label
                                                class="form-label fw-semibold small mb-0"
                                            >
                                                Semestre
                                                <span class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <span
                                                class="badge bg-secondary-subtle text-secondary border small"
                                                style="font-size: 0.65rem"
                                                title="El primer número de la sección es el semestre"
                                            ></span>
                                        </div>
                                        <input
                                            type="number"
                                            class="form-control"
                                            v-model="formNuevo.equ_semestre"
                                            min="1"
                                            max="9"
                                            readonly
                                            style="
                                                background: var(
                                                    --bg-th
                                                ) !important;
                                                cursor: not-allowed;
                                            "
                                            title="El semestre se define automáticamente por el primer número de la sección"
                                        />

                                        <div
                                            v-if="errores.equ_semestre"
                                            class="text-danger small mt-1"
                                        >
                                            {{ errores.equ_semestre }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div
                                            class="d-flex align-items-end justify-content-between mb-1"
                                            style="min-height: 22px"
                                        >
                                            <label
                                                class="form-label fw-semibold small mb-0 text-truncate"
                                            >
                                                Total Equipos
                                                <span class="text-danger"
                                                    >*</span
                                                >
                                            </label>
                                            <button
                                                v-if="esAutoCompletado"
                                                type="button"
                                                class="btn btn-sm btn-link p-0 text-decoration-none fw-semibold"
                                                style="font-size: 0.75rem"
                                                @click="
                                                    editarParametrosManual =
                                                        !editarParametrosManual
                                                "
                                                :title="
                                                    editarParametrosManual
                                                        ? 'Fijar total'
                                                        : 'Modificar total de equipos de la sección'
                                                "
                                            >
                                                <i
                                                    class="bi"
                                                    :class="
                                                        editarParametrosManual
                                                            ? 'bi-lock-fill text-success'
                                                            : 'bi-pencil-square text-primary'
                                                    "
                                                ></i>
                                                {{
                                                    editarParametrosManual
                                                        ? "Fijar"
                                                        : "Modificar"
                                                }}
                                            </button>
                                        </div>
                                        <input
                                            type="number"
                                            class="form-control"
                                            :class="{
                                                'border-primary fw-bold':
                                                    editarParametrosManual,
                                                'is-invalid':
                                                    errores.equ_total_seccion,
                                            }"
                                            v-model="
                                                formNuevo.equ_total_seccion
                                            "
                                            min="1"
                                            max="99"
                                            :readonly="
                                                esAutoCompletado &&
                                                !editarParametrosManual
                                            "
                                            :style="
                                                esAutoCompletado &&
                                                !editarParametrosManual
                                                    ? 'background: var(--bg-th) !important;'
                                                    : ''
                                            "
                                            @input="onTotalSeccionInput"
                                            @keypress="
                                                (e) =>
                                                    !/[0-9]/.test(e.key) &&
                                                    e.preventDefault()
                                            "
                                        />
                                        <div
                                            v-if="errores.equ_total_seccion"
                                            class="text-danger small mt-1"
                                        >
                                            {{ errores.equ_total_seccion }}
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div
                                            class="d-flex align-items-end mb-1"
                                            style="min-height: 22px"
                                        >
                                            <label
                                                class="form-label fw-semibold small mb-0 text-truncate"
                                            >
                                                Código generado
                                            </label>
                                        </div>
                                        <input
                                            type="text"
                                            class="form-control fw-bold text-center"
                                            :value="codigoPreview"
                                            readonly
                                            style="
                                                background: var(
                                                    --bg-th
                                                ) !important;
                                                color: var(--rojo) !important;
                                                letter-spacing: 0.5px;
                                            "
                                        />
                                    </div>

                                    <!-- Aviso de sincronización cuando se modifica el Total de Equipos -->
                                    <div class="col-12" v-if="totalModificado">
                                        <div
                                            class="py-2 px-3 rounded small d-flex align-items-center gap-2"
                                            style="
                                                background: rgba(
                                                    25,
                                                    135,
                                                    84,
                                                    0.08
                                                );
                                                border-left: 3px solid #198754;
                                                color: var(--text-main);
                                            "
                                        >
                                            <i
                                                class="bi bi-arrow-repeat text-success fs-6 flex-shrink-0"
                                            ></i>
                                            <span>
                                                <strong
                                                    >Actualización automática de
                                                    códigos:</strong
                                                >
                                                El total de la sección cambiará
                                                de
                                                <strong>{{
                                                    totalOriginalSeccion
                                                }}</strong>
                                                a
                                                <strong>{{
                                                    formNuevo.equ_total_seccion
                                                }}</strong
                                                >. Al guardar este equipo, los
                                                códigos de todos los equipos de
                                                esta sección se actualizarán
                                                automáticamente al nuevo total
                                                (ej:
                                                <strong
                                                    >/{{
                                                        formNuevo.equ_total_seccion
                                                    }}</strong
                                                >).
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold"
                                            >Título del Proyecto
                                            <span class="text-danger"
                                                >*</span
                                            ></label
                                        >
                                        <input
                                            v-model="formNuevo.equ_titulo"
                                            type="text"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    errores.equ_titulo,
                                            }"
                                            placeholder="Ej: Sistema de Control de Inventario..."
                                        />
                                        <div
                                            v-if="errores.equ_titulo"
                                            class="invalid-feedback"
                                        >
                                            {{ errores.equ_titulo }}
                                        </div>
                                    </div>
                                </div>
                                <hr
                                    style="
                                        border-color: var(--border);
                                        margin: 1rem 0;
                                    "
                                />
                                <div
                                    class="d-flex justify-content-between align-items-center mb-2"
                                >
                                    <h6
                                        style="
                                            font-weight: 800;
                                            color: var(--text-main);
                                            margin: 0;
                                        "
                                    >
                                        <i
                                            class="bi bi-people-fill me-2 text-danger"
                                        ></i
                                        >Integrantes ({{
                                            formNuevo.integrantes.length
                                        }}/6)
                                    </h6>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                        @click="agregarIntegrante"
                                        :disabled="
                                            formNuevo.integrantes.length >= 6
                                        "
                                    >
                                        <i class="bi bi-plus me-1"></i>Agregar
                                    </button>
                                </div>
                                <div
                                    v-if="errores.integrantes"
                                    class="alert alert-danger py-2 small mb-2"
                                >
                                    {{ errores.integrantes }}
                                </div>
                                <!-- INFO: Una sola vez, destacando el rol del líder -->
                                <div
                                    class="py-2 px-3 mb-2 small rounded"
                                    style="
                                        background: rgba(220, 53, 69, 0.08);
                                        border-left: 3px solid #dc3545;
                                        color: var(--text-main);
                                    "
                                >
                                    <i
                                        class="bi bi-star-fill text-warning me-1"
                                    ></i>
                                    <strong>Líder de Equipo:</strong> El primer
                                    estudiante de la lista es asignado
                                    automáticamente como líder del equipo.
                                    Recibirá credenciales de acceso con su
                                    cédula.
                                </div>
                                <!-- Encabezado de columnas ANTES del v-for -->
                                <div
                                    class="row mb-1 small fw-bold"
                                    style="color: var(--text-muted)"
                                >
                                    <div class="col-8">Estudiante</div>
                                    <div class="col-3 text-center">
                                        Rol en el Equipo
                                    </div>
                                    <div class="col-1 text-center"></div>
                                </div>
                                <div
                                    v-for="(item, idx) in formNuevo.integrantes"
                                    :key="idx"
                                    class="row g-2 mb-2 align-items-center"
                                >
                                    <div class="col-8">
                                        <SelectBuscableEstudiante
                                            v-model="item.usu_id"
                                            :estudiantes="
                                                estudiantesDisponibles(
                                                    item.usu_id,
                                                )
                                            "
                                            :todos-estudiantes="
                                                props.estudiantes
                                            "
                                            :is-lider="idx === 0"
                                            :disabled="!formNuevo.equ_id_sec"
                                            :placeholder="
                                                !formNuevo.equ_id_sec
                                                    ? 'Seleccione primero una sección...'
                                                    : idx === 0
                                                      ? 'Escribe para buscar Líder...'
                                                      : 'Escribe para buscar estudiante...'
                                            "
                                        />
                                    </div>
                                    <div class="col-3 text-center">
                                        <span
                                            v-if="idx === 0"
                                            class="badge bg-danger py-2 px-2 shadow-sm d-inline-flex align-items-center justify-content-center w-100"
                                            style="
                                                font-size: 0.78rem;
                                                letter-spacing: 0.2px;
                                            "
                                        >
                                            <i
                                                class="bi bi-star-fill text-warning me-1"
                                            ></i>
                                            Líder de Equipo
                                        </span>
                                        <span
                                            v-else
                                            class="badge bg-secondary py-2 px-2 text-white d-inline-flex align-items-center justify-content-center w-100"
                                            style="font-size: 0.78rem"
                                        >
                                            <i class="bi bi-person me-1"></i>
                                            Integrante
                                        </span>
                                    </div>
                                    <div class="col-1 text-center">
                                        <button
                                            v-if="idx > 0"
                                            type="button"
                                            class="btn btn-sm btn-outline-danger p-1 w-100"
                                            @click="quitarIntegrante(idx)"
                                            title="Quitar integrante"
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <span
                                            v-else
                                            class="text-muted d-inline-block pt-1"
                                            title="El líder es obligatorio y no puede eliminarse"
                                        >
                                            <i class="bi bi-lock-fill"></i>
                                        </span>
                                    </div>
                                </div>

                                <!-- Justificación si hay menos de 3 integrantes -->
                                <div
                                    v-if="
                                        integrantesAgregados < 3 &&
                                        integrantesAgregados > 0
                                    "
                                    class="alert py-2 small mb-2 mt-3"
                                    style="
                                        background: rgba(255, 193, 7, 0.1);
                                        border: 1px solid rgba(255, 193, 7, 0.4);
                                    "
                                >
                                    <i
                                        class="bi bi-exclamation-triangle me-1"
                                        style="color: #ffc107"
                                    ></i>
                                    <strong>Atención:</strong> El equipo tiene
                                    {{ integrantesAgregados }} integrante(s). El
                                    mínimo recomendado es 3. Si deseas
                                    continuar, escribe la justificación:
                                    <textarea
                                        v-model="
                                            formNuevo.justificacion_integrantes
                                        "
                                        class="form-control form-control-sm mt-2"
                                        rows="2"
                                        placeholder="Razón por la que el equipo tiene menos de 3 integrantes..."
                                    ></textarea>
                                    <div
                                        v-if="errores.justificacion_integrantes"
                                        class="text-danger small mt-1"
                                    >
                                        {{ errores.justificacion_integrantes }}
                                    </div>
                                </div>
                            </div>

                            <!-- PASO 2 -->
                            <div v-if="paso === 2">
                                <div
                                    class="alert py-2 small mb-3"
                                    style="
                                        background: rgba(13, 110, 253, 0.08);
                                        border: 1px solid
                                            rgba(13, 110, 253, 0.2);
                                        color: var(--text-main);
                                    "
                                >
                                    <i
                                        class="bi bi-info-circle me-1 text-primary"
                                    ></i
                                    >Este paso es opcional. Puedes asignar el
                                    proyecto ahora o después desde el módulo
                                    Proyectos.
                                </div>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                            >Comunidad</label
                                        >
                                        <div
                                            class="d-flex gap-2 align-items-center w-100"
                                            style="min-width: 0"
                                        >
                                            <SelectBuscableComunidad
                                                v-model="
                                                    formProyecto.pco_id_com
                                                "
                                                :comunidades="comunidades"
                                                size="sm"
                                                placeholder="Escribe para buscar comunidad..."
                                            />
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger flex-shrink-0 fw-semibold d-inline-flex align-items-center"
                                                style="white-space: nowrap"
                                                @click="
                                                    abrirModalNuevaComunidad(
                                                        'crear',
                                                    )
                                                "
                                                title="Registrar nueva comunidad"
                                            >
                                                <i
                                                    class="bi bi-plus-lg me-1"
                                                ></i
                                                >Nueva
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Radar de Comunidad en Vivo (Crear Equipo) -->
                                    <div class="col-12" v-if="formProyecto.pco_id_com">
                                        <div class="p-3 rounded-3 border bg-light small">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <span class="fw-bold text-dark text-uppercase">
                                                    <i class="bi bi-radar text-danger me-1"></i>
                                                    Radar de Comunidad: {{ radarCrear?.comunidad?.com_nombre || 'Consultando...' }}
                                                </span>
                                                <span v-if="loadingRadarCrear" class="spinner-border spinner-border-sm text-danger"></span>
                                                <span v-else-if="radarCrear" class="badge bg-dark">
                                                    {{ radarCrear.proyectos_activos?.length || 0 }} activos &bull; {{ radarCrear.proyectos_culminados?.length || 0 }} culminados
                                                </span>
                                            </div>
                                            <div
                                                v-if="radarCrear?.alerta_similitud?.existe"
                                                class="alert p-2 mb-2 rounded-3"
                                                :class="radarCrear.alerta_similitud.es_critico ? 'alert-danger border-danger' : 'alert-warning border-warning'"
                                            >
                                                <div class="fw-bold mb-1">
                                                    <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                                    Posible Proyecto Repetido (Similitud del {{ radarCrear.alerta_similitud.porcentaje }}%)
                                                </div>
                                                <p class="mb-2">{{ radarCrear.alerta_similitud.mensaje }}</p>
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-dark fw-bold mb-2"
                                                    @click="marcarContinuacionEqu('crear')"
                                                >
                                                    <i class="bi bi-diagram-2-fill text-danger me-1"></i>
                                                    Marcar como Continuación / Escalamiento
                                                </button>

                                                <div class="mt-2 pt-2 border-top">
                                                    <label class="form-label fw-bold text-dark small mb-1">
                                                        <i class="bi bi-shield-fill-exclamation text-danger me-1"></i>
                                                        Justificación Obligatoria contra Duplicidad <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea
                                                        class="form-control form-control-sm bg-white"
                                                        rows="2"
                                                        v-model="formNuevo.justificacion_duplicidad"
                                                        placeholder="Explique claramente el motivo (ej. Fase II, módulo complementario, nuevo alcance o usuarios distintos)..."
                                                    ></textarea>
                                                    <div class="form-text small" :class="(formNuevo.justificacion_duplicidad || '').length >= 15 ? 'text-success' : 'text-danger'">
                                                        Mínimo 15 caracteres (actual: {{ (formNuevo.justificacion_duplicidad || '').length }})
                                                    </div>
                                                    <div v-if="errores.justificacion_duplicidad" class="text-danger small mt-1 fw-semibold">
                                                        {{ errores.justificacion_duplicidad }}
                                                    </div>
                                                </div>
                                            </div>
                                            <div v-if="radarCrear && radarCrear.total_historico > 0" class="row g-2">
                                                <div class="col-md-6" v-if="radarCrear.proyectos_activos?.length">
                                                    <div class="fw-bold text-danger mb-1">Proyectos en desarrollo:</div>
                                                    <ul class="list-unstyled mb-0">
                                                        <li v-for="pa in radarCrear.proyectos_activos" :key="pa.pco_id" class="mb-1 p-1 px-2 bg-white border rounded">
                                                            <strong>{{ pa.equ_codigo }}</strong>: {{ pa.equ_titulo }}
                                                        </li>
                                                    </ul>
                                                </div>
                                                <div class="col-md-6" v-if="radarCrear.proyectos_culminados?.length">
                                                    <div class="fw-bold text-success mb-1">Culminados previamente:</div>
                                                    <ul class="list-unstyled mb-0">
                                                        <li v-for="pc in radarCrear.proyectos_culminados" :key="pc.pco_id" class="mb-1 p-1 px-2 bg-white border rounded">
                                                            <strong>{{ pc.equ_codigo }}</strong>: {{ pc.equ_titulo }}
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div v-else-if="radarCrear && !loadingRadarCrear" class="text-success fw-semibold">
                                                <i class="bi bi-check-circle-fill me-1"></i> Comunidad sin proyectos previos registrados.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                            >Tipo de Proyecto</label
                                        >
                                        <div
                                            class="d-flex gap-2 align-items-center w-100"
                                            style="min-width: 0"
                                        >
                                            <select
                                                class="form-select form-select-sm flex-grow-1"
                                                style="min-width: 0"
                                                v-model="
                                                    formProyecto.pco_id_tpr
                                                "
                                            >
                                                <option value="">
                                                    Seleccionar tipo...
                                                </option>
                                                <option
                                                    v-for="t in tiposProyecto"
                                                    :key="t.tpr_id"
                                                    :value="t.tpr_id"
                                                >
                                                    {{ t.tpr_nombre }}
                                                </option>
                                            </select>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger flex-shrink-0 fw-semibold d-inline-flex align-items-center"
                                                style="white-space: nowrap"
                                                @click="
                                                    abrirModalNuevoTipo('crear')
                                                "
                                                title="Registrar nuevo tipo de proyecto"
                                            >
                                                <i
                                                    class="bi bi-plus-lg me-1"
                                                ></i
                                                >Nuevo
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                            >Modalidad</label
                                        >
                                        <select
                                            v-model="formProyecto.pco_id_mpr"
                                            class="form-select"
                                        >
                                            <option value="">
                                                Seleccionar modalidad...
                                            </option>
                                            <option
                                                v-for="m in modalidades"
                                                :key="m.mpr_id"
                                                :value="m.mpr_id"
                                            >
                                                {{ m.mpr_nombre }}
                                            </option>
                                        </select>
                                    </div>
                                    <!-- Buscador de proyectos anteriores si la modalidad lo requiere -->
                                    <div
                                        v-if="requiereProyectoPrevio"
                                        class="col-12"
                                    >
                                        <div
                                            class="p-2 rounded border"
                                            style="
                                                background: rgba(
                                                    13,
                                                    110,
                                                    253,
                                                    0.05
                                                );
                                                border-color: rgba(
                                                    13,
                                                    110,
                                                    253,
                                                    0.2
                                                ) !important;
                                            "
                                        >
                                            <label
                                                class="form-label fw-semibold small mb-1"
                                            >
                                                <i
                                                    class="bi bi-search me-1 text-primary"
                                                ></i
                                                >Proyecto previo en esta
                                                comunidad:
                                            </label>
                                            <select
                                                v-model="
                                                    formProyecto.pco_id_pco_previo
                                                "
                                                class="form-select form-select-sm"
                                            >
                                                <option value="">
                                                    Seleccionar proyecto
                                                    anterior...
                                                </option>
                                                <option
                                                    v-for="prev in proyectosPreviosComunidad"
                                                    :key="prev.pco_id"
                                                    :value="prev.pco_id"
                                                >
                                                    {{
                                                        prev.equipo
                                                            ?.equ_titulo ||
                                                        prev.pco_resumen ||
                                                        "Proyecto #" +
                                                            prev.pco_id
                                                    }}
                                                    ({{
                                                        prev.equipo
                                                            ?.equ_codigo ||
                                                        "Sin código"
                                                    }})
                                                </option>
                                            </select>
                                            <small
                                                v-if="
                                                    proyectosPreviosComunidad.length ===
                                                    0
                                                "
                                                class="text-muted d-block mt-1"
                                            >
                                                No se encontraron proyectos
                                                previos en esta comunidad.
                                            </small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                            >Fecha Límite</label
                                        >
                                        <input
                                            type="date"
                                            class="form-control"
                                            v-model="
                                                formProyecto.pco_fecha_limite
                                            "
                                            :min="fechaMinima"
                                            :max="fechaMaxima"
                                        />
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label fw-semibold"
                                            >Resumen
                                            <small
                                                style="color: var(--text-muted)"
                                                >(máx. 150 caracteres)</small
                                            ></label
                                        >
                                        <textarea
                                            v-model="formProyecto.pco_resumen"
                                            class="form-control"
                                            rows="2"
                                            maxlength="150"
                                        ></textarea>
                                        <small style="color: var(--text-muted)"
                                            >{{
                                                formProyecto.pco_resumen.length
                                            }}/150</small
                                        >
                                    </div>
                                    <div class="col-12">
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-2"
                                        >
                                            <label
                                                class="form-label fw-semibold mb-0"
                                                >Beneficiarios</label
                                            >
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger rounded-pill px-3"
                                                @click="agregarBeneficiario"
                                            >
                                                <i class="bi bi-plus me-1"></i
                                                >Agregar
                                            </button>
                                        </div>
                                        <div
                                            v-for="(
                                                ben, idx
                                            ) in formProyecto.beneficiarios"
                                            :key="idx"
                                            class="row g-2 mb-2"
                                        >
                                            <div class="col-7">
                                                <select
                                                    v-model="ben.tbe_id"
                                                    class="form-select form-select-sm"
                                                >
                                                    <option value="">
                                                        Tipo de beneficiario...
                                                    </option>
                                                    <option
                                                        v-for="tb in tipoBeneficiario"
                                                        :key="tb.tbe_id"
                                                        :value="tb.tbe_id"
                                                    >
                                                        {{ tb.tbe_nombre }}
                                                    </option>
                                                </select>
                                            </div>
                                            <div class="col-3">
                                                <!-- Cantidad beneficiarios -->
                                                <input
                                                    type="number"
                                                    class="form-control form-control-sm"
                                                    v-model="ben.cantidad"
                                                    min="1"
                                                    @keypress="
                                                        (e) =>
                                                            !/[0-9]/.test(
                                                                e.key,
                                                            ) &&
                                                            e.preventDefault()
                                                    "
                                                />
                                            </div>
                                            <div class="col-2">
                                                <button
                                                    type="button"
                                                    class="btn btn-sm btn-outline-danger w-100"
                                                    @click="
                                                        quitarBeneficiario(idx)
                                                    "
                                                >
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="modal-footer border-0 justify-content-between"
                        >
                            <div>
                                <button
                                    v-if="paso === 2"
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    @click="paso = 1"
                                >
                                    <i class="bi bi-arrow-left me-1"></i>Atrás
                                </button>
                            </div>
                            <div class="d-flex gap-2">
                                <button
                                    type="button"
                                    class="btn btn-secondary"
                                    data-bs-dismiss="modal"
                                >
                                    Cancelar
                                </button>
                                <button
                                    v-if="paso === 1"
                                    type="button"
                                    class="btn btn-outline-danger fw-bold"
                                    :disabled="loading"
                                    @click="guardarSinProyecto"
                                >
                                    <span
                                        v-if="loading"
                                        class="spinner-border spinner-border-sm me-1"
                                    ></span>
                                    <i v-else class="bi bi-check-lg me-1"></i
                                    >Guardar sin Comunidad
                                </button>
                                <button
                                    v-if="paso === 1"
                                    type="submit"
                                    class="btn btn-danger fw-bold"
                                >
                                    Siguiente
                                    <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                                <button
                                    v-if="paso === 2"
                                    type="submit"
                                    class="btn btn-danger fw-bold"
                                    :disabled="loading"
                                >
                                    <span
                                        v-if="loading"
                                        class="spinner-border spinner-border-sm me-2"
                                    ></span
                                    >Guardar Equipo
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Editar -->
        <div class="modal fade" id="mEditar" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>Editar Equipo</h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form @submit.prevent="guardarEdicion">
                        <div class="modal-body">
                            <div
                                class="alert py-2 small mb-3"
                                style="
                                    background: rgba(13, 110, 253, 0.08);
                                    border: 1px solid rgba(13, 110, 253, 0.2);
                                    color: var(--text-main);
                                "
                            >
                                <i
                                    class="bi bi-info-circle me-1 text-primary"
                                ></i
                                >El código, sección y trayecto no se pueden
                                modificar.
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Código</label
                                >
                                <input
                                    type="text"
                                    class="form-control fw-bold"
                                    :value="equipoEditando?.equ_codigo"
                                    readonly
                                    style="
                                        background: var(--bg-th) !important;
                                        color: var(--rojo) !important;
                                    "
                                />
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold"
                                    >Título
                                    <span class="text-danger">*</span></label
                                >
                                <input
                                    v-model="formEditar.equ_titulo"
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'is-invalid': erroresEditar.equ_titulo,
                                    }"
                                />
                                <div
                                    v-if="erroresEditar.equ_titulo"
                                    class="invalid-feedback"
                                >
                                    {{ erroresEditar.equ_titulo }}
                                </div>
                            </div>

                            <hr style="border-color: var(--border)" />
                            <p
                                class="fw-bold small mb-2"
                                style="color: var(--text-muted)"
                            >
                                <i class="bi bi-folder me-1 text-danger"></i>
                                Proyecto Comunitario
                                <span
                                    v-if="
                                        !equipoSeleccionado?.proyecto_comunidad
                                    "
                                    class="badge bg-secondary ms-1"
                                    >Sin asignar</span
                                >
                            </p>

                            <div class="mb-3">
                                <label class="form-label small fw-bold"
                                    >Comunidad</label
                                >
                                <div
                                    class="d-flex gap-2 align-items-center w-100"
                                    style="min-width: 0"
                                >
                                    <SelectBuscableComunidad
                                        v-model="formEditar.pco_id_com"
                                        :comunidades="comunidades"
                                        size="sm"
                                        placeholder="Escribe para buscar comunidad..."
                                    />
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger flex-shrink-0 fw-semibold d-inline-flex align-items-center"
                                        style="white-space: nowrap"
                                        @click="
                                            abrirModalNuevaComunidad('editar')
                                        "
                                        title="Registrar nueva comunidad"
                                    >
                                        <i class="bi bi-plus-lg me-1"></i>Nueva
                                    </button>
                                </div>
                            </div>

                            <!-- Radar de Comunidad en Vivo (Editar Equipo) -->
                            <div class="mb-3" v-if="formEditar.pco_id_com">
                                <div class="p-3 rounded-3 border bg-light small">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="fw-bold text-dark text-uppercase">
                                            <i class="bi bi-radar text-danger me-1"></i>
                                            Radar de Comunidad: {{ radarEditar?.comunidad?.com_nombre || 'Consultando...' }}
                                        </span>
                                        <span v-if="loadingRadarEditar" class="spinner-border spinner-border-sm text-danger"></span>
                                        <span v-else-if="radarEditar" class="badge bg-dark">
                                            {{ radarEditar.proyectos_activos?.length || 0 }} activos &bull; {{ radarEditar.proyectos_culminados?.length || 0 }} culminados
                                        </span>
                                    </div>
                                    <div
                                        v-if="radarEditar?.alerta_similitud?.existe"
                                        class="alert p-2 mb-2 rounded-3"
                                        :class="radarEditar.alerta_similitud.es_critico ? 'alert-danger border-danger' : 'alert-warning border-warning'"
                                    >
                                        <div class="fw-bold mb-1">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                            Posible Proyecto Repetido (Similitud del {{ radarEditar.alerta_similitud.porcentaje }}%)
                                        </div>
                                        <p class="mb-2">{{ radarEditar.alerta_similitud.mensaje }}</p>
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-dark fw-bold mb-2"
                                            @click="marcarContinuacionEqu('editar')"
                                        >
                                            <i class="bi bi-diagram-2-fill text-danger me-1"></i>
                                            Marcar como Continuación / Escalamiento
                                        </button>

                                        <div class="mt-2 pt-2 border-top">
                                            <label class="form-label fw-bold text-dark small mb-1">
                                                <i class="bi bi-shield-fill-exclamation text-danger me-1"></i>
                                                Justificación Obligatoria contra Duplicidad <span class="text-danger">*</span>
                                            </label>
                                            <textarea
                                                class="form-control form-control-sm bg-white"
                                                rows="2"
                                                v-model="formEditar.justificacion_duplicidad"
                                                placeholder="Explique claramente el motivo (ej. Fase II, módulo complementario, nuevo alcance o usuarios distintos)..."
                                            ></textarea>
                                            <div class="form-text small" :class="(formEditar.justificacion_duplicidad || '').length >= 15 ? 'text-success' : 'text-danger'">
                                                Mínimo 15 caracteres (actual: {{ (formEditar.justificacion_duplicidad || '').length }})
                                            </div>
                                            <div v-if="erroresEditar.justificacion_duplicidad" class="text-danger small mt-1 fw-semibold">
                                                {{ erroresEditar.justificacion_duplicidad }}
                                            </div>
                                        </div>
                                    </div>
                                    <div v-if="radarEditar && radarEditar.total_historico > 0" class="row g-2">
                                        <div class="col-md-6" v-if="radarEditar.proyectos_activos?.length">
                                            <div class="fw-bold text-danger mb-1">Proyectos en desarrollo:</div>
                                            <ul class="list-unstyled mb-0">
                                                <li v-for="pa in radarEditar.proyectos_activos" :key="pa.pco_id" class="mb-1 p-1 px-2 bg-white border rounded">
                                                    <strong>{{ pa.equ_codigo }}</strong>: {{ pa.equ_titulo }}
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6" v-if="radarEditar.proyectos_culminados?.length">
                                            <div class="fw-bold text-success mb-1">Culminados previamente:</div>
                                            <ul class="list-unstyled mb-0">
                                                <li v-for="pc in radarEditar.proyectos_culminados" :key="pc.pco_id" class="mb-1 p-1 px-2 bg-white border rounded">
                                                    <strong>{{ pc.equ_codigo }}</strong>: {{ pc.equ_titulo }}
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div v-else-if="radarEditar && !loadingRadarEditar" class="text-success fw-semibold">
                                        <i class="bi bi-check-circle-fill me-1"></i> Comunidad sin otros proyectos registrados.
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold"
                                    >Tipo de Proyecto</label
                                >
                                <div
                                    class="d-flex gap-2 align-items-center w-100"
                                    style="min-width: 0"
                                >
                                    <select
                                        class="form-select form-select-sm flex-grow-1"
                                        style="min-width: 0"
                                        v-model="formEditar.pco_id_tpr"
                                    >
                                        <option value="">
                                            Seleccionar tipo...
                                        </option>
                                        <option
                                            v-for="t in tiposProyecto"
                                            :key="t.tpr_id"
                                            :value="t.tpr_id"
                                        >
                                            {{ t.tpr_nombre }}
                                        </option>
                                    </select>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger flex-shrink-0 fw-semibold d-inline-flex align-items-center"
                                        @click="abrirModalNuevoTipo('editar')"
                                        title="Registrar nuevo tipo de proyecto"
                                    >
                                        <i class="bi bi-plus-lg me-1"></i>Nuevo
                                    </button>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold"
                                    >Modalidad</label
                                >
                                <select
                                    class="form-select form-select-sm"
                                    v-model="formEditar.pco_id_mpr"
                                >
                                    <option value="">
                                        Seleccionar modalidad...
                                    </option>
                                    <option
                                        v-for="m in modalidades"
                                        :key="m.mpr_id"
                                        :value="m.mpr_id"
                                    >
                                        {{ m.mpr_nombre }}
                                    </option>
                                </select>
                            </div>

                            <div
                                v-if="requiereProyectoPrevioEditar"
                                class="mb-2"
                            >
                                <div
                                    class="p-2 rounded border"
                                    style="
                                        background: rgba(13, 110, 253, 0.05);
                                        border-color: rgba(
                                            13,
                                            110,
                                            253,
                                            0.2
                                        ) !important;
                                    "
                                >
                                    <label
                                        class="form-label fw-semibold small mb-1"
                                    >
                                        <i
                                            class="bi bi-search me-1 text-primary"
                                        ></i
                                        >Proyecto previo en esta comunidad:
                                    </label>
                                    <select
                                        v-model="formEditar.pco_id_pco_previo"
                                        class="form-select form-select-sm"
                                    >
                                        <option value="">
                                            Seleccionar proyecto anterior...
                                        </option>
                                        <option
                                            v-for="prev in proyectosPreviosComunidadEditar"
                                            :key="prev.pco_id"
                                            :value="prev.pco_id"
                                        >
                                            {{
                                                prev.equipo?.equ_titulo ||
                                                prev.pco_resumen ||
                                                "Proyecto #" + prev.pco_id
                                            }}
                                            ({{
                                                prev.equipo?.equ_codigo ||
                                                "Sin código"
                                            }})
                                        </option>
                                    </select>
                                    <small
                                        v-if="
                                            proyectosPreviosComunidadEditar.length ===
                                            0
                                        "
                                        class="text-muted d-block mt-1"
                                    >
                                        No se encontraron proyectos previos en
                                        esta comunidad.
                                    </small>
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold"
                                    >Fecha Límite</label
                                >
                                <input
                                    type="date"
                                    class="form-control form-control-sm"
                                    v-model="formEditar.pco_fecha_limite"
                                    :min="fechaMinima"
                                    :max="fechaMaxima"
                                />
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold"
                                    >Resumen</label
                                >
                                <textarea
                                    class="form-control form-control-sm"
                                    rows="2"
                                    v-model="formEditar.pco_resumen"
                                    maxlength="150"
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
                                :disabled="loadingEditar"
                            >
                                <span
                                    v-if="loadingEditar"
                                    class="spinner-border spinner-border-sm me-2"
                                ></span
                                >Actualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Integrantes -->
        <div class="modal fade" id="mInts" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>
                            {{ equipoSeleccionado?.equ_codigo }} — Integrantes e
                            Historial
                        </h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="modal-body p-0">
                        <ul
                            class="nav nav-tabs px-3 pt-2"
                            style="border-bottom: 1px solid var(--border)"
                        >
                            <li class="nav-item">
                                <button
                                    class="nav-link fw-semibold"
                                    :class="
                                        tabActivo === 'ints' ? 'active' : ''
                                    "
                                    @click="tabActivo = 'ints'"
                                    style="color: var(--text-main)"
                                >
                                    Integrantes
                                </button>
                            </li>
                            <li class="nav-item">
                                <button
                                    class="nav-link fw-semibold"
                                    :class="
                                        tabActivo === 'hist' ? 'active' : ''
                                    "
                                    @click="cargarHistorial"
                                    style="color: var(--text-main)"
                                >
                                    Historial
                                </button>
                            </li>
                        </ul>

                        <div v-if="tabActivo === 'ints'" class="p-3">
                            <div
                                class="p-2 mb-3 rounded"
                                style="
                                    background: var(--bg-th);
                                    border: 1px solid var(--border);
                                "
                            >
                                <h6 class="fw-bold small mb-2">
                                    <i
                                        class="bi bi-arrow-repeat text-danger me-1"
                                    ></i
                                    >Cambiar Líder
                                </h6>
                                <div class="d-flex gap-2">
                                    <select
                                        v-model="nuevoLiderId"
                                        class="form-select form-select-sm"
                                    >
                                        <option value="">
                                            Seleccionar nuevo líder...
                                        </option>
                                        <option
                                            v-for="int in integrantesNoLider"
                                            :key="int.ein_id"
                                            :value="int.ein_id"
                                        >
                                            {{ int.usuario?.usu_primer_nombre }}
                                            {{
                                                int.usuario?.usu_primer_apellido
                                            }}
                                        </option>
                                    </select>
                                    <button
                                        class="btn btn-sm btn-danger fw-bold"
                                        :disabled="!nuevoLiderId"
                                        @click="confirmarCambioLider"
                                    >
                                        Cambiar
                                    </button>
                                </div>
                            </div>
                            <ul
                                v-if="
                                    integrantesActivosSeleccionados.length > 0
                                "
                                class="list-group list-group-flush"
                            >
                                <li
                                    v-for="int in integrantesActivosSeleccionados"
                                    :key="int.ein_id"
                                    class="list-group-item d-flex align-items-center gap-3 px-1 py-2"
                                    style="
                                        background: transparent;
                                        border-color: var(--border);
                                    "
                                >
                                    <div
                                        class="cdot"
                                        style="
                                            width: 32px;
                                            height: 32px;
                                            border-radius: 50%;
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
                                            class="fw-semibold small"
                                            style="color: var(--text-main)"
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
                                        </div>
                                    </div>
                                    <div
                                        class="d-flex align-items-center gap-2"
                                    >
                                        <span
                                            class="bs"
                                            :class="
                                                int.ein_es_lider ? 'bd' : 'bsec'
                                            "
                                            >{{
                                                int.ein_es_lider
                                                    ? "Líder"
                                                    : "Integrante"
                                            }}</span
                                        >
                                        <button
                                            v-if="!int.ein_es_lider"
                                            class="btn btn-sm btn-outline-primary"
                                            @click="abrirTraslado(int)"
                                            title="Trasladar a otro equipo"
                                        >
                                            <i
                                                class="bi bi-arrow-left-right"
                                            ></i>
                                        </button>
                                        <button
                                            v-if="!int.ein_es_lider"
                                            class="btn btn-sm btn-outline-danger"
                                            @click="removerIntegrante(int)"
                                            title="Remover"
                                        >
                                            <i class="bi bi-person-dash"></i>
                                        </button>
                                    </div>
                                </li>
                            </ul>
                            <div
                                v-else
                                class="text-center py-4 text-muted small"
                            >
                                <i class="bi bi-people me-1"></i>No hay
                                integrantes activos en este equipo.
                            </div>
                        </div>

                        <div v-if="tabActivo === 'hist'" class="p-3">
                            <div
                                v-if="cargandoHistorial"
                                class="text-center py-4"
                            >
                                <div class="spinner-border text-danger"></div>
                            </div>
                            <div
                                v-else-if="historialEquipo.length === 0"
                                class="text-center py-4"
                                style="color: var(--text-muted)"
                            >
                                Sin eventos registrados
                            </div>
                            <ul v-else class="list-group list-group-flush">
                                <li
                                    v-for="evt in historialEquipo"
                                    :key="evt.heq_id"
                                    class="list-group-item d-flex align-items-start gap-3 px-1 py-2"
                                    style="
                                        background: transparent;
                                        border-color: var(--border);
                                    "
                                >
                                    <div
                                        class="evt-dot"
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
                                            class="fw-semibold small"
                                            style="color: var(--text-main)"
                                        >
                                            {{ evt.tipo_evento?.tee_nombre }}
                                        </div>
                                        <div
                                            style="
                                                font-size: 0.82rem;
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
                    <div class="modal-footer border-0">
                        <button
                            type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Trasladar Integrante -->
        <div class="modal fade" id="mTrasladar" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="mh">
                        <h5>Trasladar Integrante</h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div
                            class="alert py-2 small mb-3"
                            style="
                                background: rgba(13, 110, 253, 0.08);
                                border: 1px solid rgba(13, 110, 253, 0.2);
                                color: var(--text-main);
                            "
                        >
                            <i class="bi bi-info-circle me-1 text-primary"></i>
                            Vas a trasladar a
                            <strong
                                >{{
                                    integranteATrasladar?.usuario
                                        ?.usu_primer_nombre
                                }}
                                {{
                                    integranteATrasladar?.usuario
                                        ?.usu_primer_apellido
                                }}</strong
                            >
                            (CI:
                            {{ integranteATrasladar?.usuario?.usu_cedula }}) a
                            otro equipo de la misma sección.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">
                                Equipo de destino en la sección:
                            </label>
                            <select
                                v-model="equipoDestinoId"
                                class="form-select"
                            >
                                <option value="">
                                    Seleccionar equipo de destino...
                                </option>
                                <option
                                    v-for="eq in equiposDestinoDisponibles"
                                    :key="eq.equ_id"
                                    :value="eq.equ_id"
                                >
                                    {{ eq.equ_codigo }} —
                                    {{ eq.equ_titulo }} ({{
                                        eq.integrantes?.length || 0
                                    }}
                                    integrantes)
                                </option>
                            </select>
                            <small
                                v-if="equiposDestinoDisponibles.length === 0"
                                class="text-danger d-block mt-1"
                            >
                                No hay otros equipos activos en esta sección a
                                los cuales trasladar.
                            </small>
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
                            type="button"
                            class="btn btn-danger fw-bold"
                            :disabled="!equipoDestinoId || cargandoTraslado"
                            @click="confirmarTraslado"
                        >
                            <span
                                v-if="cargandoTraslado"
                                class="spinner-border spinner-border-sm me-1"
                            ></span>
                            Confirmar Traslado
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Disolver -->
        <div class="modal fade" id="mDisolver" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div
                        style="
                            background: #dc3545;
                            padding: 1rem 1.3rem;
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                        "
                    >
                        <h5 style="color: #fff; font-weight: 800; margin: 0">
                            <i class="bi bi-x-circle me-2"></i>Disolver Equipo
                        </h5>
                        <button
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x"></i>
                        </button>
                    </div>
                    <form @submit.prevent="confirmarDisolver">
                        <div class="modal-body">
                            <div class="alert alert-danger small py-2 mb-3">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Selecciona si deseas disolver el equipo
                                (desactivarlo para posible reactivación) o
                                eliminarlo por completo si no se volverá a
                                utilizar.
                            </div>
                            <div class="mb-3">
                                <p style="color: var(--text-main)" class="mb-1">
                                    Equipo:
                                    <strong style="color: #dc3545">{{
                                        codigoLimpio(
                                            equipoADisolver?.equ_codigo,
                                        )
                                    }}</strong>
                                </p>
                                <p
                                    style="
                                        color: var(--text-muted);
                                        font-size: 0.88rem;
                                    "
                                    class="mb-0"
                                >
                                    {{ equipoADisolver?.equ_titulo }}
                                </p>
                            </div>

                            <!-- Opción de acción -->
                            <div
                                class="mb-3 p-3 rounded"
                                style="
                                    background: var(
                                        --bg-th,
                                        rgba(0, 0, 0, 0.03)
                                    );
                                    border: 1px solid var(--border);
                                "
                            >
                                <label class="form-label fw-bold mb-2"
                                    >Acción a ejecutar:</label
                                >
                                <div class="form-check mb-2">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="tipoAccionDisolver"
                                        id="accionDisolver"
                                        value="disolver"
                                        v-model="tipoAccionDisolver"
                                    />
                                    <label
                                        class="form-check-label fw-semibold"
                                        for="accionDisolver"
                                    >
                                        Disolver equipo
                                    </label>
                                    <div class="small text-muted">
                                        Desactiva el equipo y libera a los
                                        integrantes. Se conserva en "Ver
                                        Disueltos" y podrá ser reactivado
                                        posteriormente.
                                    </div>
                                </div>
                                <div class="form-check">
                                    <input
                                        class="form-check-input"
                                        type="radio"
                                        name="tipoAccionDisolver"
                                        id="accionEliminar"
                                        value="eliminar"
                                        v-model="tipoAccionDisolver"
                                    />
                                    <label
                                        class="form-check-label fw-semibold text-danger"
                                        for="accionEliminar"
                                    >
                                        Eliminar definitivamente
                                    </label>
                                    <div class="small text-muted">
                                        Elimina por completo el equipo y sus
                                        registros si no se reactivará. Libera el
                                        código y a los integrantes de forma
                                        definitiva.
                                    </div>
                                </div>
                            </div>

                            <div v-if="tipoAccionDisolver === 'disolver'">
                                <label class="form-label fw-semibold"
                                    >Motivo de la disolución
                                    <span class="text-danger">*</span></label
                                >
                                <textarea
                                    v-model="motivoDisolucion"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            !motivoDisolucion &&
                                            intentoDisolver,
                                    }"
                                    rows="3"
                                    placeholder="Explica por qué se disuelve el equipo..."
                                ></textarea>
                                <div
                                    v-if="!motivoDisolucion && intentoDisolver"
                                    class="invalid-feedback"
                                >
                                    El motivo es obligatorio.
                                </div>
                            </div>
                            <div
                                v-else
                                class="alert alert-warning small mb-0 py-2"
                            >
                                <i class="bi bi-info-circle me-1"></i>
                                Se borrarán todos los registros, puntos y
                                entregables de este equipo de forma permanente e
                                irreversible.
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
                                v-if="tipoAccionDisolver === 'disolver'"
                                type="submit"
                                class="btn btn-danger fw-bold"
                            >
                                <i class="bi bi-x-circle me-2"></i>Disolver
                                Equipo
                            </button>
                            <button
                                v-else
                                type="submit"
                                class="btn btn-outline-danger fw-bold"
                            >
                                <i class="bi bi-trash me-2"></i>Eliminar
                                Definitivamente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Historial Académico -->
        <div
            class="modal fade"
            id="mHistorialAcademico"
            tabindex="-1"
            aria-hidden="true"
        >
            <div
                class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"
            >
                <div
                    class="modal-content border-0 shadow-lg overflow-hidden"
                    style="background: var(--bg-card); border-radius: 12px"
                >
                    <div
                        class="mh"
                        style="
                            background: #1a1d23;
                            border-bottom: 2px solid #dc3545;
                            padding: 1.1rem 1.4rem;
                        "
                    >
                        <div class="d-flex align-items-center gap-2">
                            <div
                                style="
                                    width: 34px;
                                    height: 34px;
                                    background: #dc3545;
                                    border-radius: 8px;
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    color: #fff;
                                    font-weight: 800;
                                "
                            >
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-white fw-bold">
                                    Historial Académico del Equipo
                                </h5>
                                <small
                                    class="text-white-50"
                                    v-if="equipoHistorial"
                                >
                                    <span class="badge bg-danger me-1">{{
                                        equipoHistorial.equ_codigo
                                    }}</span>
                                    {{ equipoHistorial.equ_titulo }}
                                </small>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div
                            v-if="cargandoHistorialAcademico"
                            class="text-center py-5"
                        >
                            <div
                                class="spinner-border text-danger"
                                role="status"
                            ></div>
                            <p class="text-muted mt-2">
                                Cargando historial académico...
                            </p>
                        </div>
                        <template v-else-if="equipoHistorial">
                            <!-- Resumen del Equipo -->
                            <div
                                class="sc mb-3 p-3"
                                style="
                                    border-left: 4px solid #dc3545 !important;
                                "
                            >
                                <div class="row g-3 text-center text-md-start">
                                    <div class="col-md-3">
                                        <span
                                            class="small fw-bold text-uppercase d-block"
                                            style="
                                                color: var(--text-muted);
                                                font-size: 0.72rem;
                                            "
                                            >Sección & Trayecto</span
                                        >
                                        <span
                                            class="fw-bold"
                                            style="color: var(--text-main)"
                                            >{{
                                                equipoHistorial.seccion
                                                    ?.sec_codigo
                                            }}
                                            —
                                            {{
                                                equipoHistorial.trayecto
                                                    ?.tra_nombre
                                            }}</span
                                        >
                                    </div>
                                    <div class="col-md-3">
                                        <span
                                            class="small fw-bold text-uppercase d-block"
                                            style="
                                                color: var(--text-muted);
                                                font-size: 0.72rem;
                                            "
                                            >Comunidad</span
                                        >
                                        <span
                                            class="fw-bold"
                                            style="color: var(--text-main)"
                                            >{{
                                                equipoHistorial
                                                    .proyecto_comunidad
                                                    ?.comunidad?.com_nombre ||
                                                "Sin asignar"
                                            }}</span
                                        >
                                    </div>
                                    <div class="col-md-3">
                                        <span
                                            class="small fw-bold text-uppercase d-block"
                                            style="
                                                color: var(--text-muted);
                                                font-size: 0.72rem;
                                            "
                                            >Tipo de Proyecto</span
                                        >
                                        <span
                                            class="fw-bold"
                                            style="color: var(--text-main)"
                                            >{{
                                                equipoHistorial
                                                    .proyecto_comunidad
                                                    ?.tipo_proyecto
                                                    ?.tpr_nombre ||
                                                "Sin definir"
                                            }}</span
                                        >
                                    </div>
                                    <div class="col-md-3">
                                        <span
                                            class="small fw-bold text-uppercase d-block"
                                            style="
                                                color: var(--text-muted);
                                                font-size: 0.72rem;
                                            "
                                            >Estado del Proyecto</span
                                        >
                                        <span
                                            class="bs"
                                            :class="
                                                equipoHistorial
                                                    .resultado_proyecto
                                                    ?.estado_proyecto
                                                    ?.epr_nombre === 'Aprobado'
                                                    ? 'ba'
                                                    : 'bd'
                                            "
                                        >
                                            {{
                                                equipoHistorial
                                                    .resultado_proyecto
                                                    ?.estado_proyecto
                                                    ?.epr_nombre ||
                                                "En Desarrollo"
                                            }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Navegación por pestañas (Rojo, Negro y Blanco) -->
                            <div
                                class="d-flex gap-2 mb-3 pb-2 overflow-x-auto"
                                style="border-bottom: 1px solid var(--border)"
                            >
                                <button
                                    type="button"
                                    class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                                    :style="
                                        tabHistorial === 'puntos'
                                            ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;'
                                            : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'
                                    "
                                    @click="tabHistorial = 'puntos'"
                                >
                                    <i class="bi bi-flag me-1"></i> Puntos de
                                    Control ({{
                                        equipoHistorial.puntos_control
                                            ?.length || 0
                                    }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                                    :style="
                                        tabHistorial === 'entregables'
                                            ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;'
                                            : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'
                                    "
                                    @click="tabHistorial = 'entregables'"
                                >
                                    <i
                                        class="bi bi-file-earmark-check me-1"
                                    ></i>
                                    Entregables ({{
                                        equipoHistorial.entregables?.length ||
                                        0
                                    }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                                    :style="
                                        tabHistorial === 'socializaciones'
                                            ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;'
                                            : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'
                                    "
                                    @click="tabHistorial = 'socializaciones'"
                                >
                                    <i class="bi bi-people me-1"></i>
                                    Socializaciones ({{
                                        equipoHistorial.socializaciones
                                            ?.length || 0
                                    }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                                    :style="
                                        tabHistorial === 'integrantes'
                                            ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;'
                                            : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'
                                    "
                                    @click="tabHistorial = 'integrantes'"
                                >
                                    <i class="bi bi-person-lines-fill me-1"></i>
                                    Integrantes ({{
                                        equipoHistorial.integrantes?.length ||
                                        0
                                    }})
                                </button>
                            </div>

                            <!-- Contenido Pestaña 1: Puntos de Control -->
                            <div v-if="tabHistorial === 'puntos'">
                                <div
                                    v-if="
                                        !equipoHistorial.puntos_control ||
                                        equipoHistorial.puntos_control
                                            .length === 0
                                    "
                                    class="text-center py-4 text-muted"
                                >
                                    <i
                                        class="bi bi-info-circle fs-3 d-block mb-2 text-danger"
                                    ></i>
                                    No hay puntos de control registrados para
                                    este equipo.
                                </div>
                                <div v-else class="sc">
                                    <div class="table-responsive">
                                        <table class="ct">
                                            <thead>
                                                <tr>
                                                    <th>N°</th>
                                                    <th>Punto de Control</th>
                                                    <th>Fecha de Encuentro</th>
                                                    <th>Estado</th>
                                                    <th>Observaciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="(
                                                        puc, idx
                                                    ) in equipoHistorial.puntos_control"
                                                    :key="puc.puc_id"
                                                    style="cursor: pointer"
                                                    class="fila-interactiva"
                                                    @click="
                                                        abrirDetalleHistorial(
                                                            puc,
                                                            'punto',
                                                        )
                                                    "
                                                    title="Haz clic para ver el detalle completo"
                                                >
                                                    <td>
                                                        <span class="bs bsec">{{
                                                            puc.puc_orden ||
                                                            idx + 1
                                                        }}</span>
                                                    </td>
                                                    <td>
                                                        <div
                                                            class="fw-bold text-danger text-decoration-underline-hover"
                                                        >
                                                            {{ puc.puc_nombre }}
                                                            <i
                                                                class="bi bi-box-arrow-up-right small ms-1"
                                                            ></i>
                                                        </div>
                                                        <small
                                                            class="text-muted"
                                                            v-if="
                                                                puc.puc_descripcion
                                                            "
                                                            >{{
                                                                puc.puc_descripcion
                                                            }}</small
                                                        >
                                                    </td>
                                                    <td>
                                                        <span>{{
                                                            puc.puc_fecha_reprogramada ||
                                                            puc.puc_fecha_limite
                                                        }}</span>
                                                        <span
                                                            v-if="
                                                                puc.puc_fecha_reprogramada
                                                            "
                                                            class="bs bw ms-1"
                                                            >Reprogramada</span
                                                        >
                                                    </td>
                                                    <td>
                                                        <span
                                                            v-if="
                                                                puc.seguimientos &&
                                                                puc.seguimientos
                                                                    .length >
                                                                    0 &&
                                                                puc
                                                                    .seguimientos[0]
                                                                    .seq_cumplido
                                                            "
                                                            class="bs ba"
                                                        >
                                                            <i
                                                                class="bi bi-check-circle me-1"
                                                            ></i
                                                            >Cumplido
                                                        </span>
                                                        <span
                                                            v-else-if="
                                                                puc.seguimientos &&
                                                                puc.seguimientos
                                                                    .length >
                                                                    0 &&
                                                                puc
                                                                    .seguimientos[0]
                                                                    .seq_cumplido ===
                                                                    false
                                                            "
                                                            class="bs bd"
                                                        >
                                                            <i
                                                                class="bi bi-x-circle me-1"
                                                            ></i
                                                            >No Cumplido
                                                        </span>
                                                        <span
                                                            v-else
                                                            class="bs bw"
                                                        >
                                                            <i
                                                                class="bi bi-clock me-1"
                                                            ></i
                                                            >Pendiente
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small
                                                            v-if="
                                                                puc.seguimientos &&
                                                                puc.seguimientos
                                                                    .length >
                                                                    0 &&
                                                                puc
                                                                    .seguimientos[0]
                                                                    .seq_observaciones
                                                            "
                                                        >
                                                            {{
                                                                puc
                                                                    .seguimientos[0]
                                                                    .seq_observaciones
                                                            }}
                                                        </small>
                                                        <small
                                                            v-else
                                                            class="text-muted"
                                                            >—</small
                                                        >
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenido Pestaña 2: Entregables -->
                            <div v-if="tabHistorial === 'entregables'">
                                <div
                                    v-if="
                                        !equipoHistorial.entregables ||
                                        equipoHistorial.entregables.length === 0
                                    "
                                    class="text-center py-4 text-muted"
                                >
                                    <i
                                        class="bi bi-info-circle fs-3 d-block mb-2 text-danger"
                                    ></i>
                                    No hay entregables subidos por este equipo.
                                </div>
                                <div v-else class="sc">
                                    <div class="table-responsive">
                                        <table class="ct">
                                            <thead>
                                                <tr>
                                                    <th>Tipo / Archivo</th>
                                                    <th>Punto de Control</th>
                                                    <th>Versión</th>
                                                    <th>Estado</th>
                                                    <th class="text-end pe-3">
                                                        Acción
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="ent in equipoHistorial.entregables"
                                                    :key="ent.entr_id"
                                                >
                                                    <td>
                                                        <div class="fw-bold">
                                                            {{
                                                                ent
                                                                    .tipo_entregable
                                                                    ?.tet_nombre ||
                                                                "Entregable"
                                                            }}
                                                        </div>
                                                        <small
                                                            class="text-muted"
                                                            ><i
                                                                class="bi bi-file-earmark me-1"
                                                            ></i
                                                            >{{
                                                                ent.entr_nombre_archivo
                                                            }}</small
                                                        >
                                                    </td>
                                                    <td>
                                                        <span
                                                            v-if="
                                                                ent.punto_control
                                                            "
                                                            class="bs bsec"
                                                            >{{
                                                                ent
                                                                    .punto_control
                                                                    .puc_nombre
                                                            }}</span
                                                        >
                                                        <span
                                                            v-else
                                                            class="text-muted"
                                                            >—</span
                                                        >
                                                    </td>
                                                    <td>
                                                        <span class="bs bsec"
                                                            >v{{
                                                                ent.entr_version ||
                                                                1
                                                            }}</span
                                                        >
                                                    </td>
                                                    <td>
                                                        <span
                                                            v-if="
                                                                ent.entr_aprobado ===
                                                                true
                                                            "
                                                            class="bs ba"
                                                            >Aprobado</span
                                                        >
                                                        <span
                                                            v-else-if="
                                                                ent.entr_aprobado ===
                                                                false
                                                            "
                                                            class="bs bd"
                                                            >Rechazado</span
                                                        >
                                                        <span
                                                            v-else
                                                            class="bs bw"
                                                            >En Revisión</span
                                                        >
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <a
                                                            v-if="ent.entr_ruta"
                                                            :href="`/storage/${ent.entr_ruta}`"
                                                            target="_blank"
                                                            class="btn btn-sm btn-outline-danger"
                                                        >
                                                            <i
                                                                class="bi bi-download me-1"
                                                            ></i
                                                            >Descargar
                                                        </a>
                                                        <span
                                                            v-else
                                                            class="text-muted"
                                                            >—</span
                                                        >
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenido Pestaña 3: Socializaciones -->
                            <div v-if="tabHistorial === 'socializaciones'">
                                <div
                                    v-if="
                                        !equipoHistorial.socializaciones ||
                                        equipoHistorial.socializaciones
                                            .length === 0
                                    "
                                    class="text-center py-4 text-muted"
                                >
                                    <i
                                        class="bi bi-info-circle fs-3 d-block mb-2 text-danger"
                                    ></i>
                                    No hay socializaciones registradas para este
                                    equipo.
                                </div>
                                <div v-else class="row g-3">
                                    <div
                                        v-for="soc in equipoHistorial.socializaciones"
                                        :key="soc.soc_id"
                                        class="col-md-6"
                                    >
                                        <div
                                            class="sc h-100 p-3 fila-interactiva"
                                            style="
                                                border-left: 3px solid #dc3545 !important;
                                                cursor: pointer;
                                            "
                                            @click="
                                                abrirDetalleHistorial(
                                                    soc,
                                                    'socializacion',
                                                )
                                            "
                                            title="Haz clic para ver el detalle de la socialización"
                                        >
                                            <div
                                                class="d-flex justify-content-between align-items-start mb-2"
                                            >
                                                <h6
                                                    class="fw-bold mb-0 text-danger"
                                                    style="
                                                        color: var(--text-main);
                                                    "
                                                >
                                                    {{
                                                        soc.tipo_socializacion
                                                            ?.tso_nombre ||
                                                        "Socialización"
                                                    }}
                                                    <i
                                                        class="bi bi-box-arrow-up-right small ms-1"
                                                    ></i>
                                                </h6>
                                                <span
                                                    v-if="soc.soc_apto === true"
                                                    class="bs ba"
                                                    >Apto</span
                                                >
                                                <span
                                                    v-else-if="
                                                        soc.soc_apto === false
                                                    "
                                                    class="bs bd"
                                                    >No Apto</span
                                                >
                                                <span v-else class="bs bw"
                                                    >Pendiente</span
                                                >
                                            </div>
                                            <div class="small text-muted mb-2">
                                                <div>
                                                    <i
                                                        class="bi bi-calendar-event me-1 text-danger"
                                                    ></i
                                                    >{{ soc.soc_fecha }} a las
                                                    {{ soc.soc_hora }}
                                                </div>
                                                <div>
                                                    <i
                                                        class="bi bi-geo-alt me-1 text-danger"
                                                    ></i
                                                    >{{ soc.soc_lugar }}
                                                </div>
                                            </div>
                                            <div
                                                v-if="soc.soc_observaciones"
                                                class="p-2 rounded small mb-2"
                                                style="
                                                    background: var(--bg-th);
                                                    border: 1px solid
                                                        var(--border);
                                                "
                                            >
                                                <strong>Observaciones:</strong>
                                                {{ soc.soc_observaciones }}
                                            </div>
                                            <div
                                                v-if="
                                                    soc.jurados &&
                                                    soc.jurados.length > 0
                                                "
                                            >
                                                <small
                                                    class="fw-semibold text-muted d-block mb-1"
                                                    >Jurados:</small
                                                >
                                                <div
                                                    class="d-flex flex-wrap gap-1"
                                                >
                                                    <span
                                                        v-for="j in soc.jurados"
                                                        :key="j.jso_id"
                                                        class="bs bsec"
                                                    >
                                                        {{
                                                            j.usuario
                                                                ?.usu_primer_nombre
                                                        }}
                                                        {{
                                                            j.usuario
                                                                ?.usu_primer_apellido
                                                        }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenido Pestaña 4: Integrantes -->
                            <div v-if="tabHistorial === 'integrantes'">
                                <div class="sc">
                                    <div class="table-responsive">
                                        <table class="ct">
                                            <thead>
                                                <tr>
                                                    <th>Cédula</th>
                                                    <th>Estudiante</th>
                                                    <th>Rol en Equipo</th>
                                                    <th>Estado</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr
                                                    v-for="integ in equipoHistorial.integrantes"
                                                    :key="integ.ein_id"
                                                >
                                                    <td>
                                                        {{
                                                            integ.usuario
                                                                ?.usu_cedula
                                                        }}
                                                    </td>
                                                    <td class="fw-bold">
                                                        {{
                                                            integ.usuario
                                                                ?.usu_primer_nombre
                                                        }}
                                                        {{
                                                            integ.usuario
                                                                ?.usu_primer_apellido
                                                        }}
                                                    </td>
                                                    <td>
                                                        <span
                                                            v-if="
                                                                integ.ein_es_lider
                                                            "
                                                            class="bs bd"
                                                        >
                                                            <i
                                                                class="bi bi-star-fill me-1 text-warning"
                                                            ></i
                                                            >Líder
                                                        </span>
                                                        <span
                                                            v-else
                                                            class="bs bsec"
                                                            >Integrante</span
                                                        >
                                                    </td>
                                                    <td>
                                                        <span
                                                            v-if="
                                                                integ.ein_status
                                                            "
                                                            class="bs ba"
                                                            >Activo</span
                                                        >
                                                        <span
                                                            v-else
                                                            class="bs bsec"
                                                            >Retirado</span
                                                        >
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div
                        class="modal-footer"
                        style="
                            background: var(--bg-th);
                            border-top: 1px solid var(--border);
                        "
                    >
                        <button
                            type="button"
                            class="btn btn-sm btn-outline-secondary px-4 fw-bold"
                            data-bs-dismiss="modal"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Socialización Final y Evaluación (4 Actores y PIN) -->
        <div class="modal fade" id="mSocFinalModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; background: var(--bg-card);">
                    <div class="mh" style="background: #1a1d23; border-bottom: 2px solid #dc3545; padding: 1.1rem 1.4rem; color: #fff; display: flex; justify-content: space-between; align-items: center;">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-award-fill text-danger fs-4"></i>
                            <div>
                                <h5 class="fw-bold m-0 text-white">Socialización Final y Evaluación — {{ equipoSocFinal?.equ_codigo }}</h5>
                                <div class="small text-white-50">{{ equipoSocFinal?.equ_titulo || 'Proyecto Sociotecnológico' }}</div>
                            </div>
                        </div>
                        <button class="btn btn-sm text-white fs-5 p-0" data-bs-dismiss="modal">
                            <i class="bi bi-x"></i>
                        </button>
                    </div>

                    <div class="modal-body p-4">
                        <!-- CASO 1: YA TIENE FECHA DE SOCIALIZACIÓN FINAL -->
                        <div v-if="socFinalActual?.soc_fecha">
                            <div class="alert alert-success d-flex align-items-center justify-content-between p-3 rounded mb-4">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-calendar-check-fill text-success fs-3"></i>
                                    <div>
                                        <div class="fw-bold text-success">Socialización Final Programada</div>
                                        <div class="small text-dark">
                                            <strong>Fecha:</strong> {{ socFinalActual.soc_fecha }}
                                            <span v-if="socFinalActual.soc_hora"> &bull; <strong>Hora:</strong> {{ socFinalActual.soc_hora }}</span>
                                            &bull; <strong>Lugar:</strong> {{ socFinalActual.soc_lugar }}
                                            <span
                                                v-if="formSocFinal.entrega_cd_informe"
                                                class="badge bg-success ms-2"
                                            >
                                                <i class="bi bi-disc-fill me-1"></i>CD / Informe Entregado
                                            </span>
                                            <span v-else class="badge bg-warning text-dark ms-2">
                                                <i class="bi bi-exclamation-circle me-1"></i>CD / Informe Pendiente
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <button class="btn btn-sm btn-outline-secondary" @click="mostrarReprogramar = !mostrarReprogramar">
                                    <i class="bi bi-pencil me-1"></i> {{ mostrarReprogramar ? 'Ocultar' : 'Reprogramar' }}
                                </button>
                            </div>

                            <!-- Formulario de Reprogramación (Colapsable) -->
                            <div v-if="mostrarReprogramar" class="p-3 mb-4 border rounded bg-light">
                                <h6 class="fw-bold mb-2 text-dark">Modificar Fecha, Lugar o Entrega de CD/Informe</h6>
                                <form @submit.prevent="guardarFechaSocFinal">
                                    <div class="row g-2 align-items-end">
                                        <div class="col-md-4">
                                            <label class="form-label small fw-bold">Fecha <span class="text-danger">*</span></label>
                                            <input v-model="formSocFinal.fecha" type="date" class="form-control form-control-sm" required />
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label small fw-bold">Hora</label>
                                            <input v-model="formSocFinal.hora" type="time" class="form-control form-control-sm" />
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label small fw-bold">Lugar <span class="text-danger">*</span></label>
                                            <input v-model="formSocFinal.lugar" type="text" class="form-control form-control-sm" required />
                                        </div>
                                        <div class="col-12 mt-2">
                                            <div class="form-check form-switch">
                                                <input
                                                    v-model="formSocFinal.entrega_cd_informe"
                                                    class="form-check-input"
                                                    type="checkbox"
                                                    id="chkCdReprog"
                                                />
                                                <label class="form-check-label fw-bold small text-dark" for="chkCdReprog">
                                                    <i class="bi bi-disc-fill text-danger me-1"></i>
                                                    El equipo entregó el CD / Informe Final (Físico y Digital)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12 text-end mt-2">
                                            <button type="submit" class="btn btn-sm btn-danger fw-bold rounded-pill px-3" :disabled="loadingSocFinal">
                                                <span v-if="loadingSocFinal" class="spinner-border spinner-border-sm me-1"></span>
                                                Guardar Cambios
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <!-- Link Temporal para Jurado y Vocero Comunitario (Vigencia 12 Horas) -->
                            <div class="card border-2 border-danger shadow-sm rounded-4 bg-white p-4 mb-4">
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3 pb-2 border-bottom">
                                    <div>
                                        <span class="badge bg-danger text-uppercase fw-bold px-2 py-1 small">Seguridad y Evaluación en Vivo</span>
                                        <h5 class="fw-bold text-dark m-0 mt-1">
                                            <i class="bi bi-link-45deg text-danger fs-4 me-1"></i>
                                            Enlace Temporal para Jurado y Vocero (Vigencia 12 Horas)
                                        </h5>
                                        <small class="text-muted">
                                            Permite al jurado consultar los antecedentes del proyecto, expediente por estudiante y calificar en tiempo real.
                                        </small>
                                    </div>

                                    <!-- Estado de Vigencia -->
                                    <div>
                                        <span v-if="linkJuradoVigente" class="badge bg-success text-white fw-bold px-3 py-2 fs-6 rounded-pill shadow-sm">
                                            <i class="bi bi-clock-history me-1"></i> Vigente &bull; {{ tiempoRestanteJuradoTexto }}
                                        </span>
                                        <span v-else-if="linkJuradoUrl" class="badge bg-danger text-white fw-bold px-3 py-2 fs-6 rounded-pill shadow-sm">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Enlace Expirado (12h superadas)
                                        </span>
                                        <span v-else class="badge bg-secondary text-white fw-bold px-3 py-2 fs-6 rounded-pill">
                                            <i class="bi bi-hourglass me-1"></i> Pendiente por Generar
                                        </span>
                                    </div>
                                </div>

                                <!-- Si el enlace ya fue generado -->
                                <div v-if="linkJuradoUrl" class="mb-3">
                                    <label class="form-label small fw-bold text-dark">URL Única de Acceso Temporal para Compartir:</label>
                                    <div class="input-group input-group-lg mb-2">
                                        <input
                                            type="text"
                                            class="form-control font-monospace bg-light border-dark fs-6"
                                            :value="linkJuradoUrl"
                                            readonly
                                            @click="$event.target.select()"
                                        />
                                        <button
                                            type="button"
                                            class="btn fw-bold px-4"
                                            :class="copiadoLink ? 'btn-success text-white' : 'btn-danger text-white'"
                                            @click="copiarLinkJurado"
                                            style="min-height: 48px;"
                                        >
                                            <i :class="copiadoLink ? 'bi bi-check-lg' : 'bi bi-clipboard'" class="me-1"></i>
                                            {{ copiadoLink ? '¡Enlace Copiado!' : 'Copiar Link' }}
                                        </button>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 small">
                                        <div class="text-muted">
                                            <i class="bi bi-shield-check text-success me-1"></i>
                                            <strong>Expira:</strong> {{ formatearFechaHora(linkJuradoExpiraAt) }}
                                        </div>
                                        <div class="d-flex gap-2">
                                            <a :href="linkJuradoUrl" target="_blank" class="btn btn-sm btn-outline-dark fw-bold">
                                                <i class="bi bi-box-arrow-up-right me-1"></i> Abrir Panel de Jurado
                                            </a>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger fw-bold"
                                                @click="generarLinkTemporalJurado"
                                                :disabled="loadingGenerarLink"
                                            >
                                                <span v-if="loadingGenerarLink" class="spinner-border spinner-border-sm me-1"></span>
                                                <i v-else class="bi bi-arrow-repeat me-1"></i>
                                                {{ linkJuradoVigente ? 'Regenerar Enlace' : 'Generar Nuevo Enlace' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Si aún no se ha generado enlace -->
                                <div v-else class="text-center py-4 bg-light rounded-3 border">
                                    <p class="text-dark small mb-3">
                                        Genere el enlace temporal único con vigencia de 12 horas para enviarlo por mensajería o correo al Jurado Evaluador y Vocero Comunitario.
                                    </p>
                                    <button
                                        type="button"
                                        class="btn btn-danger btn-lg px-4 py-2 fw-bold rounded-pill shadow-sm"
                                        @click="generarLinkTemporalJurado"
                                        :disabled="loadingGenerarLink"
                                        style="min-height: 48px;"
                                    >
                                        <span v-if="loadingGenerarLink" class="spinner-border spinner-border-sm me-2"></span>
                                        <i v-else class="bi bi-lightning-charge-fill me-2"></i>
                                        Generar Enlace Temporal para Jurado (12 Horas)
                                    </button>
                                </div>

                                <!-- PIN de Defensa secundario -->
                                <div class="mt-3 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2 text-muted small">
                                    <div>
                                        <span class="fw-bold text-dark me-1">PIN de Respaldo:</span>
                                        <span class="badge bg-dark font-monospace fs-6 text-warning">{{ equipoSocFinal?.proyecto_comunidad?.pin_defensa || '123456' }}</span>
                                    </div>
                                    <button class="btn btn-link btn-sm text-secondary p-0 text-decoration-none" @click="copiarTexto(equipoSocFinal?.proyecto_comunidad?.pin_defensa)">
                                        <i class="bi bi-clipboard me-1"></i> Copiar PIN
                                    </button>
                                </div>
                            </div>

                            <!-- Botón de Acceso al Módulo de Evaluación de los 4 Actores -->
                            <div class="text-center p-3 border rounded bg-white">
                                <h6 class="fw-bold mb-1 text-dark">Módulo de Evaluación Final y Rúbricas (Semestre 2)</h6>
                                <p class="small text-muted mb-3">
                                    Consolida las calificaciones de los 4 actores oficiales: <strong>Docente de Proyecto (40%)</strong>, <strong>Jurado Evaluador (30%)</strong>, <strong>Representante Comunitario (20%)</strong> y <strong>Autoevaluación Estudiantil (10%)</strong>.
                                </p>
                                <a
                                    v-if="equipoSocFinal?.proyecto_comunidad?.pco_id"
                                    :href="'/evaluaciones/recorrido/' + equipoSocFinal.proyecto_comunidad.pco_id"
                                    class="btn btn-danger fw-bold px-4 py-2 rounded-pill shadow-sm"
                                    style="min-height: 48px; font-size: 1rem;"
                                >
                                    <i class="bi bi-award-fill me-2"></i>
                                    Abrir Módulo de Evaluación de los 4 Actores
                                </a>
                            </div>
                        </div>

                        <!-- CASO 2: NO TIENE FECHA DE SOCIALIZACIÓN FINAL (REGLA DE VISIBILIDAD ESTRICTA) -->
                        <div v-else>
                            <div class="alert alert-warning p-4 rounded-3 border-warning mb-4">
                                <div class="d-flex align-items-start gap-3">
                                    <i class="bi bi-calendar-x-fill text-warning fs-2 mt-1"></i>
                                    <div>
                                        <h5 class="fw-bold text-dark mb-1">Fecha de Socialización Final Pendiente</h5>
                                        <p class="small text-muted mb-0" style="line-height: 1.6;">
                                            Por directriz institucional UPTP y auditoría académica, el <strong>PIN de defensa</strong> y el <strong>módulo de evaluación de notas</strong> permanecen bloqueados e inactivos hasta que se seleccione y guarde la <strong>Fecha y Lugar</strong> del acto de Socialización Final.
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Formulario para Registrar Fecha de Socialización Final -->
                            <div class="card border p-4 shadow-sm bg-light">
                                <h6 class="fw-bold mb-3 text-dark">
                                    <i class="bi bi-calendar-plus text-danger me-2"></i>
                                    Programar Socialización Final
                                </h6>
                                <form @submit.prevent="guardarFechaSocFinal">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label fw-bold">Fecha del Acto <span class="text-danger">*</span></label>
                                            <input v-model="formSocFinal.fecha" type="date" class="form-control" required />
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label fw-bold">Hora</label>
                                            <input v-model="formSocFinal.hora" type="time" class="form-control" />
                                        </div>
                                        <div class="col-md-5">
                                            <label class="form-label fw-bold">Lugar del Acto <span class="text-danger">*</span></label>
                                            <input v-model="formSocFinal.lugar" type="text" class="form-control" placeholder="Ej: Aula 4 UPTP, Auditorio" required />
                                        </div>
                                        <div class="col-12 mt-3">
                                            <div class="form-check form-switch p-3 bg-white border rounded-3">
                                                <input
                                                    v-model="formSocFinal.entrega_cd_informe"
                                                    class="form-check-input ms-0 me-2"
                                                    type="checkbox"
                                                    id="chkCdInicial"
                                                />
                                                <label class="form-check-label fw-bold text-dark" for="chkCdInicial">
                                                    <i class="bi bi-disc-fill text-danger me-1"></i>
                                                    Confirmar entrega de CD / Informe Final (Físico y Digital)
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-12 text-end mt-4">
                                            <button type="submit" class="btn btn-danger fw-bold px-4 py-2 rounded-pill shadow-sm" :disabled="loadingSocFinal" style="min-height: 48px;">
                                                <span v-if="loadingSocFinal" class="spinner-border spinner-border-sm me-2"></span>
                                                <i v-else class="bi bi-check-circle-fill me-1"></i>
                                                Guardar Fecha y Habilitar Evaluación Final
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer bg-light border-top">
                        <button type="button" class="btn btn-secondary px-4 fw-bold rounded-pill" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Detalle Interactivo de Actividad del Historial -->
        <div
            class="modal fade"
            id="mDetalleActividadHistorial"
            tabindex="-1"
            style="z-index: 1065"
        >
            <div class="modal-dialog modal-dialog-centered">
                <div
                    class="modal-content border-0 shadow-lg"
                    style="border-radius: 12px; background: var(--bg-card)"
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
                            class="mb-0 fw-bold fs-6 text-white d-flex align-items-center"
                        >
                            <i
                                class="bi bi-info-circle-fill text-danger me-2"
                            ></i
                            >{{ detalleModal.titulo }}
                        </h5>
                        <button
                            type="button"
                            class="btn btn-sm text-white p-0 fs-5"
                            data-bs-dismiss="modal"
                        >
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body p-4" style="color: var(--text-main)">
                        <div
                            class="mb-3 p-3 rounded"
                            style="
                                background: var(--bg-th);
                                border: 1px solid var(--border);
                            "
                        >
                            <div
                                class="d-flex justify-content-between align-items-center mb-2"
                            >
                                <span class="badge bg-danger">{{
                                    detalleModal.subtitulo
                                }}</span>
                                <span class="small text-muted">{{
                                    detalleModal.fecha
                                }}</span>
                            </div>
                            <h6
                                class="fw-bold mb-1"
                                style="color: var(--text-main)"
                            >
                                {{ detalleModal.nombre }}
                            </h6>
                            <p
                                class="small text-muted mb-0"
                                v-if="detalleModal.descripcion"
                            >
                                {{ detalleModal.descripcion }}
                            </p>
                        </div>

                        <div class="row g-2 mb-3 small">
                            <div class="col-6">
                                <span class="text-muted d-block">Estado:</span>
                                <strong>{{ detalleModal.estado }}</strong>
                            </div>
                            <div class="col-6" v-if="detalleModal.semana">
                                <span class="text-muted d-block"
                                    >Semana de Corte:</span
                                >
                                <strong
                                    >Semana {{ detalleModal.semana }}</strong
                                >
                            </div>
                        </div>

                        <div v-if="detalleModal.observaciones" class="mb-3">
                            <label class="fw-bold small text-muted d-block mb-1"
                                >Observaciones / Criterios Evaluados:</label
                            >
                            <div
                                class="p-2 rounded small"
                                style="
                                    background: var(--bg-th);
                                    border: 1px solid var(--border);
                                "
                            >
                                {{ detalleModal.observaciones }}
                            </div>
                        </div>

                        <div v-if="detalleModal.criterios" class="mb-3">
                            <label class="fw-bold small text-muted d-block mb-1"
                                >Criterios de Evaluación:</label
                            >
                            <div
                                class="p-2 rounded small"
                                style="
                                    background: var(--bg-th);
                                    border: 1px solid var(--border);
                                "
                            >
                                {{ detalleModal.criterios }}
                            </div>
                        </div>

                        <div v-if="detalleModal.lugar" class="mb-2 small">
                            <span class="text-muted">Lugar / Aula:</span>
                            <strong>{{ detalleModal.lugar }}</strong>
                        </div>
                    </div>
                    <div
                        class="modal-footer"
                        style="
                            background: var(--bg-th);
                            border-top: 1px solid var(--border);
                        "
                    >
                        <button
                            type="button"
                            class="btn btn-sm btn-secondary px-3"
                            data-bs-dismiss="modal"
                        >
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Nueva Comunidad (Submodal con diseño Rojo, Negro y Blanco) -->
        <div
            class="modal fade"
            id="mModalNuevaComunidad"
            tabindex="-1"
            aria-labelledby="mModalNuevaComunidadLabel"
            aria-hidden="true"
            style="z-index: 1070"
        >
            <div class="modal-dialog modal-dialog-centered">
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
                            class="mb-0 fw-bold fs-6 text-white d-flex align-items-center"
                        >
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i
                            >Registrar Nueva Comunidad
                        </h5>
                        <button
                            type="button"
                            class="btn btn-sm text-white p-0 fs-5"
                            @click="cerrarModalNuevaComunidad"
                        >
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <form @submit.prevent="guardarNuevaComunidad">
                        <div
                            class="modal-body p-4"
                            style="color: var(--text-main)"
                        >
                            <div
                                class="alert py-2 px-3 small mb-3 rounded"
                                style="
                                    background: rgba(220, 53, 69, 0.08);
                                    border-left: 3px solid #dc3545;
                                    color: var(--text-main);
                                "
                            >
                                <i
                                    class="bi bi-info-circle-fill me-1 text-danger"
                                ></i>
                                Registra la comunidad para asociarla
                                directamente al proyecto.
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold"
                                    >Nombre de la Comunidad
                                    <span class="text-danger">*</span></label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            erroresNuevaComunidad.com_nombre,
                                    }"
                                    v-model="nuevaComunidad.com_nombre"
                                    placeholder="Ej: Comunidad Brisas del Este"
                                />
                                <div
                                    v-if="erroresNuevaComunidad.com_nombre"
                                    class="invalid-feedback"
                                >
                                    {{ erroresNuevaComunidad.com_nombre }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold"
                                    >Tipo de Organización
                                    <span class="text-danger">*</span></label
                                >
                                <select
                                    class="form-select"
                                    :class="{
                                        'is-invalid':
                                            erroresNuevaComunidad.com_id_tor,
                                    }"
                                    v-model="nuevaComunidad.com_id_tor"
                                >
                                    <option value="">
                                        Seleccione tipo de organización...
                                    </option>
                                    <option
                                        v-for="t in tiposOrganizacion"
                                        :key="t.tor_id"
                                        :value="t.tor_id"
                                    >
                                        {{ t.tor_nombre }}
                                    </option>
                                </select>
                                <div
                                    v-if="erroresNuevaComunidad.com_id_tor"
                                    class="invalid-feedback"
                                >
                                    {{ erroresNuevaComunidad.com_id_tor }}
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold"
                                    >Ubicación / Dirección
                                    <span class="text-muted fw-normal"
                                        >(opcional)</span
                                    ></label
                                >
                                <textarea
                                    class="form-control"
                                    v-model="nuevaComunidad.com_ubicacion"
                                    rows="2"
                                    placeholder="Ej: Av. Principal c/calle 5, Sector Centro..."
                                ></textarea>
                            </div>
                        </div>
                        <div
                            class="modal-footer border-0 p-3"
                            style="
                                background: var(--bg-th);
                                border-top: 1px solid var(--border) !important;
                            "
                        >
                            <button
                                type="button"
                                class="btn btn-secondary btn-sm px-3"
                                @click="cerrarModalNuevaComunidad"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="btn btn-danger btn-sm px-4 fw-bold"
                                :disabled="guardandoComunidad"
                            >
                                <span
                                    v-if="guardandoComunidad"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>
                                <i v-else class="bi bi-check2 me-1"></i>Guardar
                                Comunidad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Nuevo Tipo de Proyecto (Submodal con diseño Rojo, Negro y Blanco) -->
        <div
            class="modal fade"
            id="mModalNuevoTipo"
            tabindex="-1"
            aria-labelledby="mModalNuevoTipoLabel"
            aria-hidden="true"
            style="z-index: 1070"
        >
            <div class="modal-dialog modal-dialog-centered">
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
                            class="mb-0 fw-bold fs-6 text-white d-flex align-items-center"
                        >
                            <i class="bi bi-tag-fill text-danger me-2"></i
                            >Registrar Nuevo Tipo de Proyecto
                        </h5>
                        <button
                            type="button"
                            class="btn btn-sm text-white p-0 fs-5"
                            @click="cerrarModalNuevoTipo"
                        >
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <form @submit.prevent="guardarNuevoTipo">
                        <div
                            class="modal-body p-4"
                            style="color: var(--text-main)"
                        >
                            <div
                                class="alert py-2 px-3 small mb-3 rounded"
                                style="
                                    background: rgba(220, 53, 69, 0.08);
                                    border-left: 3px solid #dc3545;
                                    color: var(--text-main);
                                "
                            >
                                <i
                                    class="bi bi-info-circle-fill me-1 text-danger"
                                ></i>
                                Ingresa el nombre del tipo de proyecto. Quedará
                                seleccionado automáticamente en el formulario.
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold"
                                    >Nombre del Tipo de Proyecto
                                    <span class="text-danger">*</span></label
                                >
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="{
                                        'is-invalid':
                                            erroresNuevoTipo.tpr_nombre,
                                    }"
                                    v-model="nuevoTipo.tpr_nombre"
                                    placeholder="Ej: Desarrollo de Software, Redes, Capacitación..."
                                />
                                <div
                                    v-if="erroresNuevoTipo.tpr_nombre"
                                    class="invalid-feedback"
                                >
                                    {{ erroresNuevoTipo.tpr_nombre }}
                                </div>
                            </div>
                        </div>
                        <div
                            class="modal-footer border-0 p-3"
                            style="
                                background: var(--bg-th);
                                border-top: 1px solid var(--border) !important;
                            "
                        >
                            <button
                                type="button"
                                class="btn btn-secondary btn-sm px-3"
                                @click="cerrarModalNuevoTipo"
                            >
                                Cancelar
                            </button>
                            <button
                                type="submit"
                                class="btn btn-danger btn-sm px-4 fw-bold"
                                :disabled="guardandoTipo"
                            >
                                <span
                                    v-if="guardandoTipo"
                                    class="spinner-border spinner-border-sm me-1"
                                ></span>
                                <i v-else class="bi bi-check2 me-1"></i>Guardar
                                Tipo
                            </button>
                        </div>
                    </form>
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
import { ref, computed, watch } from "vue";
import { Link, router, usePage } from "@inertiajs/vue3";
import { Modal } from "bootstrap";
import ModalConfirmar from "@/Components/ModalConfirmar.vue";
import SelectBuscableEstudiante from "@/Components/SelectBuscableEstudiante.vue";
import SelectBuscableComunidad from "@/Components/SelectBuscableComunidad.vue";
import SidebarCoordinador from "@/Components/SidebarCoordinador.vue";
import SidebarProfesor from "@/Components/SidebarProfesor.vue";

const props = defineProps({
    equipos: Array,
    equiposDisueltos: { type: Array, default: () => [] },
    secciones: Array,
    trayectos: Array,
    estudiantes: Array,
    comunidades: { type: Array, default: () => [] },
    tiposProyecto: { type: Array, default: () => [] },
    modalidades: { type: Array, default: () => [] },
    tipoBeneficiario: { type: Array, default: () => [] },
    puedeCrear: { type: Boolean, default: true },
    tiposOrganizacion: Array,
    sede: { type: String, default: "ACA" },
    seccion: { type: Object, default: null },
    modoProfesor: { type: Boolean, default: false },
    diagnosticosEquipos: { type: Object, default: () => ({}) },
});

const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(
    localStorage.getItem("coord-theme") ||
        localStorage.getItem("prof-theme") ||
        "light",
);
const esProfesor = computed(() =>
    window.location.pathname.startsWith("/profesor"),
);
const toggleTema = () => {
    const k = esProfesor.value ? "prof-theme" : "coord-theme";
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem(k, tema.value);
};

const modalLogout = ref(null);
const logout = () => {
    modalLogout.value?.abrir();
};
const ejecutarLogout = () => {
    router.post("/logout");
};

// Socialización Final y Evaluación (4 Actores & PIN)
const equipoSocFinal = ref(null);
const socFinalActual = ref(null);
const mostrarReprogramar = ref(false);
const loadingSocFinal = ref(false);
const formSocFinal = ref({
    fecha: "",
    hora: "",
    lugar: "",
});

// Enlace Temporal para Jurado (Vigencia 12 Horas)
const linkJuradoUrl = ref("");
const linkJuradoExpiraAt = ref("");
const copiadoLink = ref(false);
const loadingGenerarLink = ref(false);

const linkJuradoVigente = computed(() => {
    if (!linkJuradoExpiraAt.value) return false;
    return new Date(linkJuradoExpiraAt.value) > new Date();
});

const tiempoRestanteJuradoTexto = computed(() => {
    if (!linkJuradoExpiraAt.value) return "";
    const diffMs = new Date(linkJuradoExpiraAt.value) - new Date();
    if (diffMs <= 0) return "Expirado";
    const hrs = Math.floor(diffMs / 3600000);
    const mins = Math.floor((diffMs % 3600000) / 60000);
    return `Quedan ${hrs}h ${mins}m`;
});

const formatearFechaHora = (f) => {
    if (!f) return "No disponible";
    const d = new Date(f);
    return d.toLocaleString("es-VE", {
        day: "2-digit",
        month: "2-digit",
        year: "numeric",
        hour: "2-digit",
        minute: "2-digit",
        hour12: true,
    });
};

const copiarLinkJurado = () => {
    if (!linkJuradoUrl.value) return;
    navigator.clipboard.writeText(linkJuradoUrl.value);
    copiadoLink.value = true;
    setTimeout(() => {
        copiadoLink.value = false;
    }, 2500);
};

const generarLinkTemporalJurado = async () => {
    if (!equipoSocFinal.value) return;
    loadingGenerarLink.value = true;
    try {
        const prefix = esProfesor.value ? "profesor" : "coordinador";
        const url = `/${prefix}/equipos/${equipoSocFinal.value.equ_id}/generar-link-jurado`;
        const res = await window.axios.post(url);
        if (res.data?.success) {
            linkJuradoUrl.value = res.data.link;
            linkJuradoExpiraAt.value = res.data.expira_at;
            if (!equipoSocFinal.value.proyecto_comunidad) {
                equipoSocFinal.value.proyecto_comunidad = {};
            }
            equipoSocFinal.value.proyecto_comunidad.token_jurado = res.data.token;
            equipoSocFinal.value.proyecto_comunidad.token_jurado_expira_at = res.data.expira_at;
            if (res.data.pin) {
                equipoSocFinal.value.proyecto_comunidad.pin_defensa = res.data.pin;
            }
            if (res.data.id_proyecto) {
                equipoSocFinal.value.proyecto_comunidad.pco_id = res.data.id_proyecto;
            }
        }
    } catch (e) {
        console.error("Error generando link jurado:", e);
        const msg = e.response?.data?.message || e.message || "Ocurrió un error al generar el enlace temporal.";
        alert(msg);
    } finally {
        loadingGenerarLink.value = false;
    }
};

const abrirModalSocializacion = (equ) => {
    equipoSocFinal.value = equ;
    mostrarReprogramar.value = false;
    const sf = (equ.socializaciones || []).find(
        (s) => s.tipo_socializacion?.tso_orden === 2 || s.soc_id_tso === 2
    );
    socFinalActual.value = sf || null;
    formSocFinal.value = {
        fecha: sf?.soc_fecha || "",
        hora: sf?.soc_hora || "",
        lugar: sf?.soc_lugar || "",
        entrega_cd_informe: String(sf?.soc_observaciones || "").includes("[CD/INFORME ENTREGADO]"),
    };

    const pc = equ.proyecto_comunidad || equ.proyectoComunidad;
    if (pc?.token_jurado) {
        linkJuradoUrl.value = window.location.origin + "/evaluacion-jurado/" + pc.token_jurado;
        linkJuradoExpiraAt.value = pc.token_jurado_expira_at || "";
    } else {
        linkJuradoUrl.value = "";
        linkJuradoExpiraAt.value = "";
    }
    copiadoLink.value = false;

    const modal = new Modal(document.getElementById("mSocFinalModal"));
    modal.show();
};

const guardarFechaSocFinal = () => {
    if (!equipoSocFinal.value) return;
    loadingSocFinal.value = true;
    const prefix = currentRole.value === 'profesor' ? 'profesor' : 'coordinador';
    router.post(`/${prefix}/socializaciones/programar-final`, {
        id_equipo: equipoSocFinal.value.equ_id,
        fecha: formSocFinal.value.fecha,
        hora: formSocFinal.value.hora,
        lugar: formSocFinal.value.lugar,
        entrega_cd_informe: formSocFinal.value.entrega_cd_informe,
    }, {
        onSuccess: () => {
            loadingSocFinal.value = false;
            mostrarReprogramar.value = false;
            if (!socFinalActual.value) {
                socFinalActual.value = {
                    soc_fecha: formSocFinal.value.fecha,
                    soc_hora: formSocFinal.value.hora,
                    soc_lugar: formSocFinal.value.lugar,
                };
            } else {
                socFinalActual.value.soc_fecha = formSocFinal.value.fecha;
                socFinalActual.value.soc_hora = formSocFinal.value.hora;
                socFinalActual.value.soc_lugar = formSocFinal.value.lugar;
            }
        },
        onError: () => {
            loadingSocFinal.value = false;
        }
    });
};

const copiarTexto = (txt) => {
    if (!txt) return;
    navigator.clipboard.writeText(String(txt));
    alert("PIN copiado al portapapeles: " + txt);
};

// Promoción a Semestre Par (Transición Semestral)
const esEquipoImpar = (equ) => {
    const secCodigo = String(equ.seccion?.sec_codigo || "");
    const primerDigito = parseInt(secCodigo.charAt(0));
    return !isNaN(primerDigito) && primerDigito % 2 !== 0;
};

const promoverEquipoDirecto = (equ) => {
    const secCodigo = String(equ.seccion?.sec_codigo || "");
    const primerDigito = parseInt(secCodigo.charAt(0));
    const nuevoCodigo = (primerDigito + 1) + secCodigo.substring(1);
    if (!confirm(`¿Confirmas promover el equipo ${equ.equ_codigo} de la sección impar ${secCodigo} a la sección par ${nuevoCodigo} (Transición Semestral)?\n\nSe congelará su historial del Semestre 1 y se habilitará el Semestre 2 conservando todas las notas acumuladas.`)) return;
    router.post(`/coordinador/transicion-semestral/equipo/${equ.equ_id}`);
};

// Historial Académico
const equipoHistorial = ref(null);
const cargandoHistorialAcademico = ref(false);
const tabHistorial = ref("puntos");

const detalleModal = ref({
    titulo: "",
    subtitulo: "",
    nombre: "",
    descripcion: "",
    fecha: "",
    estado: "",
    semana: null,
    observaciones: "",
    criterios: "",
    lugar: "",
});

const abrirDetalleHistorial = (item, tipo) => {
    if (tipo === "punto") {
        const seg =
            item.seguimientos && item.seguimientos.length > 0
                ? item.seguimientos[0]
                : null;
        let estadoStr = "Pendiente";
        if (seg) {
            estadoStr =
                seg.seq_cumplido === true
                    ? "Cumplido"
                    : seg.seq_cumplido === false
                      ? "No Cumplido"
                      : "Pendiente";
        }
        detalleModal.value = {
            titulo: "Detalle del Punto de Control",
            subtitulo: `Punto de Control #${item.puc_orden || 1}`,
            nombre: item.puc_nombre,
            descripcion: item.puc_descripcion || "Sin descripción",
            fecha: item.puc_fecha_reprogramada
                ? `${item.puc_fecha_reprogramada} (Reprogramada)`
                : item.puc_fecha_limite,
            estado: estadoStr,
            semana: item.puc_semana || null,
            observaciones:
                seg?.seq_observaciones || "Sin observaciones registradas",
            criterios: item.puc_criterios_evaluacion || "",
            lugar: "",
        };
    } else if (tipo === "socializacion") {
        let estadoStr = "Pendiente";
        if (item.soc_apto === true) estadoStr = "Apto";
        else if (item.soc_apto === false) estadoStr = "No Apto";

        detalleModal.value = {
            titulo: "Detalle de Socialización",
            subtitulo: item.tipo_socializacion?.tso_nombre || "Socialización",
            nombre:
                item.tipo_socializacion?.tso_nombre ||
                "Socialización Académica",
            descripcion: item.soc_observaciones || "",
            fecha: `${item.soc_fecha} a las ${item.soc_hora || "—"}`,
            estado: estadoStr,
            semana: null,
            observaciones: item.soc_observaciones || "Sin observaciones",
            criterios: "",
            lugar: item.soc_lugar || "No especificado",
        };
    }

    const modalEl = document.getElementById("mDetalleActividadHistorial");
    if (modalEl) {
        const bsModal = Modal.getInstance(modalEl) || new Modal(modalEl);
        bsModal.show();
    }
};

const abrirHistorialAcademico = async (equ) => {
    cargandoHistorialAcademico.value = true;
    equipoHistorial.value = equ;
    tabHistorial.value = "puntos";

    const modalEl = document.getElementById("mHistorialAcademico");
    if (modalEl) {
        const bsModal = Modal.getInstance(modalEl) || new Modal(modalEl);
        bsModal.show();
    }

    try {
        const ruta = `${base.value}/equipos/${equ.equ_id}/historial-academico`;
        const res = await window.axios.get(ruta);
        if (res.data?.equipo) {
            equipoHistorial.value = res.data.equipo;
        }
    } catch (e) {
        console.error("Error al cargar historial académico", e);
    } finally {
        cargandoHistorialAcademico.value = false;
    }
};
const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});
const base = computed(() => (esProfesor.value ? "/profesor" : "/coordinador"));

const busqueda = ref("");
const pagina = ref(1);
const porPagina = 8;
const verDisueltos = ref(false);
const loading = ref(false);
const loadingEditar = ref(false);
const paso = ref(1);
const liderIndex = ref(0);
const equipoEditando = ref(null);
const equipoSeleccionado = ref(null);
const equipoADisolver = ref(null);
const motivoDisolucion = ref("");
const intentoDisolver = ref(false);
const tipoAccionDisolver = ref("disolver");
const codigoLimpio = (cod) => (cod ? String(cod).replace(/#DIS\d+$/, "") : "");
const tabActivo = ref("ints");
const historialEquipo = ref([]);
const cargandoHistorial = ref(false);
const nuevoLiderId = ref("");
const errores = ref({});
const erroresEditar = ref({});

const formNuevo = ref({
    equ_id_sec: "",
    equ_numero_grupo: "",
    equ_semestre: 1,
    equ_total_seccion: 7,
    equ_titulo: "",
    integrantes: [{ usu_id: "", es_lider: true }],
    justificacion_integrantes: "",
    justificacion_duplicidad: "",
});
const formEditar = ref({
    equ_titulo: "",
    pco_id_com: "",
    pco_id_tpr: "",
    pco_id_mpr: "",
    pco_id_pco_previo: "",
    pco_resumen: "",
    pco_fecha_limite: "",
    justificacion_duplicidad: "",
});

const formProyecto = ref({
    pco_id_com: "",
    pco_id_tpr: "",
    pco_id_mpr: "",
    pco_id_pco_previo: "",
    pco_fecha_limite: "",
    pco_resumen: "",
    justificacion_duplicidad: "",
    beneficiarios: [],
});

// Radar de Comunidad y Detector Anti-Duplicidad en Vivo
const radarCrear = ref(null);
const loadingRadarCrear = ref(false);
const radarEditar = ref(null);
const loadingRadarEditar = ref(false);
let timerRadarCrear = null;
let timerRadarEditar = null;

const consultarRadarEqu = async (modo) => {
    const comId = modo === "crear" ? formProyecto.value.pco_id_com : formEditar.value.pco_id_com;
    const titulo = modo === "crear" ? formNuevo.value.equ_titulo : formEditar.value.equ_titulo;
    const equipoId = modo === "editar" ? equipoEditando.value?.equ_id || "" : "";

    if (!comId) {
        if (modo === "crear") radarCrear.value = null;
        else radarEditar.value = null;
        return;
    }

    if (modo === "crear") loadingRadarCrear.value = true;
    else loadingRadarEditar.value = true;

    try {
        const res = await window.axios.get(`/api/comunidades/${comId}/radar-proyectos`, {
            params: { titulo: titulo || "", equipo_id: equipoId },
        });
        if (modo === "crear") radarCrear.value = res.data;
        else radarEditar.value = res.data;
    } catch (e) {
        console.error("Error en Radar de Comunidad:", e);
    } finally {
        if (modo === "crear") loadingRadarCrear.value = false;
        else loadingRadarEditar.value = false;
    }
};

watch(
    () => [formProyecto.value.pco_id_com, formNuevo.value.equ_titulo],
    () => {
        clearTimeout(timerRadarCrear);
        timerRadarCrear = setTimeout(() => consultarRadarEqu("crear"), 350);
    }
);

watch(
    () => [formEditar.value.pco_id_com, formEditar.value.equ_titulo],
    () => {
        clearTimeout(timerRadarEditar);
        timerRadarEditar = setTimeout(() => consultarRadarEqu("editar"), 350);
    }
);

const marcarContinuacionEqu = (modo) => {
    const modCont = (props.modalidades || []).find((m) =>
        ["continuación", "continuacion", "reingeniería", "reingenieria"].includes(
            (m.mpr_nombre || "").toLowerCase()
        )
    );
    const radar = modo === "crear" ? radarCrear.value : radarEditar.value;
    const pcoPrevId = radar?.alerta_similitud?.proyecto_coincidente?.pco_id || "";

    if (modo === "crear") {
        if (modCont) formProyecto.value.pco_id_mpr = modCont.mpr_id;
        if (pcoPrevId) formProyecto.value.pco_id_pco_previo = pcoPrevId;
    } else {
        if (modCont) formEditar.value.pco_id_mpr = modCont.mpr_id;
        if (pcoPrevId) formEditar.value.pco_id_pco_previo = pcoPrevId;
    }
};

const integranteATrasladar = ref(null);
const equipoDestinoId = ref("");
const cargandoTraslado = ref(false);

let mCrear = null;
let mEditar = null;
let mInts = null;
let mDisolver = null;
let mTrasladar = null;

const filtroSemaforo = ref("");

const contarPorSemaforo = (tipo) => {
    if (!listaActual.value || !props.diagnosticosEquipos) return 0;
    return listaActual.value.filter((eq) => {
        const d = props.diagnosticosEquipos[eq.equ_id];
        return d && d.semaforo === tipo;
    }).length;
};

const getIconoSemaforo = (tipo) => {
    switch (tipo) {
        case "en_ruta":
            return { icon: "🟢", class: "bg-success text-white" };
        case "acelerar":
            return { icon: "🟡", class: "bg-warning text-dark" };
        case "riesgo_alto":
            return { icon: "🔴", class: "bg-danger text-white" };
        case "inviable":
            return { icon: "⛔", class: "bg-dark text-white" };
        default:
            return { icon: "⚪", class: "bg-secondary text-white" };
    }
};

const getEtiquetaSemaforo = (tipo) => {
    switch (tipo) {
        case "en_ruta":
            return "En ruta";
        case "acelerar":
            return "Acelerar";
        case "riesgo_alto":
            return "Riesgo alto";
        case "inviable":
            return "Inviable";
        default:
            return "Pendiente";
    }
};

watch([busqueda, verDisueltos, filtroSemaforo], () => {
    pagina.value = 1;
});

const listaActual = computed(() =>
    verDisueltos.value ? props.equiposDisueltos : props.equipos,
);
const equiposFiltrados = computed(() => {
    let list = listaActual.value || [];
    if (busqueda.value.trim()) {
        const q = busqueda.value.toLowerCase();
        list = list.filter(
            (e) =>
                e.equ_codigo?.toLowerCase().includes(q) ||
                e.equ_titulo?.toLowerCase().includes(q),
        );
    }
    if (filtroSemaforo.value) {
        list = list.filter((e) => {
            const d = props.diagnosticosEquipos?.[e.equ_id];
            return d && d.semaforo === filtroSemaforo.value;
        });
    }
    return list;
});
const totalPaginas = computed(() =>
    Math.ceil(equiposFiltrados.value.length / porPagina),
);
const desde = computed(() => (pagina.value - 1) * porPagina);
const hasta = computed(() => desde.value + porPagina);
const equiposPaginados = computed(() =>
    equiposFiltrados.value.slice(desde.value, hasta.value),
);
const paginasV = computed(() => {
    const t = totalPaginas.value,
        a = pagina.value,
        p = [],
        i = Math.max(1, a - 2),
        f = Math.min(t, a + 2);
    for (let x = i; x <= f; x++) p.push(x);
    return p;
});

const seccionSeleccionada = computed(() =>
    props.secciones?.find((s) => s.sec_id == formNuevo.value.equ_id_sec),
);
const trayectoSeleccionado = computed(
    () => seccionSeleccionada.value?.trayecto?.tra_nombre || "",
);
const parsearCodigoEquipo = (codigo) => {
    if (!codigo) return null;
    // Formato estándar institucional: PNFI-ACA33-331-2/5 o PNFI-ACA11-131-1/7
    // Partes: [PNF]-[SEDE][TRAYECTO][SEMESTRE]-[SECCION]-[NUM]/[TOTAL]
    const m = String(codigo).match(
        /^[A-Z]+-[A-Z]+(\d)(\d)-([^-]+)-(\d+)\/(\d+)$/i,
    );
    if (m) {
        return {
            trayecto: parseInt(m[1]),
            semestre: parseInt(m[2]),
            seccionCodigo: m[3],
            numero: parseInt(m[4]),
            totalSeccion: parseInt(m[5]),
        };
    }
    // Formato con slash sin sede: ...-1/5
    const mSlash = String(codigo).match(/-(\d+)\/(\d+)$/);
    if (mSlash) {
        return {
            trayecto: null,
            semestre: null,
            seccionCodigo: null,
            numero: parseInt(mSlash[1]),
            totalSeccion: parseInt(mSlash[2]),
        };
    }
    return null;
};

const esAutoCompletado = ref(false);
const editarParametrosManual = ref(false);

const siguienteNumeroEquipo = computed(() => {
    const secId = formNuevo.value.equ_id_sec;
    if (!secId) return 1;
    const todos = [...(props.equipos || [])];
    const equiposSec = todos.filter(
        (e) => String(e.equ_id_sec) === String(secId),
    );
    let maxN = 0;
    equiposSec.forEach((e) => {
        const parsed = parsearCodigoEquipo(e.equ_codigo);
        if (parsed?.numero) {
            maxN = Math.max(maxN, parsed.numero);
        } else {
            const mSlash = e.equ_codigo
                ? e.equ_codigo.match(/-(\d+)\/(\d+)$/)
                : null;
            if (mSlash) {
                maxN = Math.max(maxN, parseInt(mSlash[1]));
            } else {
                const mG = e.equ_codigo ? e.equ_codigo.match(/G(\d+)/i) : null;
                if (mG) maxN = Math.max(maxN, parseInt(mG[1]));
            }
        }
    });
    return maxN + 1;
});

const totalEquiposSeccion = computed(() => {
    const secId = formNuevo.value.equ_id_sec;
    if (!secId) return 7;
    const todos = [...(props.equipos || [])];
    const equiposSec = todos.filter(
        (e) => String(e.equ_id_sec) === String(secId),
    );
    for (const e of equiposSec) {
        const parsed = parsearCodigoEquipo(e.equ_codigo);
        if (parsed?.totalSeccion) return parsed.totalSeccion;
        const mSlash = e.equ_codigo
            ? e.equ_codigo.match(/-(\d+)\/(\d+)$/)
            : null;
        if (mSlash && parseInt(mSlash[2])) {
            return parseInt(mSlash[2]);
        }
    }
    return 7;
});

const codigoPreview = computed(() => {
    const sec = seccionSeleccionada.value;
    const f = formNuevo.value;
    if (!sec || !f.equ_numero_grupo) return "—";
    const pnf = (sec.trayecto?.pnf?.pnf_siglas || "PNFI").toUpperCase();
    const sede = (props.sede || "ACA").toUpperCase();
    const tra = sec.trayecto?.tra_numero ?? 1;
    const sem = f.equ_semestre || 1;
    const num = f.equ_numero_grupo;
    const tot = f.equ_total_seccion || num;
    return `${pnf}-${sede}${tra}${sem}-${sec.sec_codigo}-${num}/${tot}`;
});

const onSeccionChange = () => {
    const secId = formNuevo.value.equ_id_sec;
    if (!secId) {
        esAutoCompletado.value = false;
        editarParametrosManual.value = false;
        return;
    }

    const sec =
        props.secciones?.find((s) => String(s.sec_id) === String(secId)) ||
        props.seccion;
    const traId = sec?.sec_id_tra || sec?.trayecto?.tra_id;
    const todos = [...(props.equipos || [])];

    // Equipos existentes de la sección
    const equiposSec = todos.filter(
        (e) => String(e.equ_id_sec) === String(secId),
    );

    // Equipos existentes en el mismo trayecto (cualquier sección de este trayecto)
    const equiposTra = todos.filter((e) => {
        const s = props.secciones?.find(
            (item) => String(item.sec_id) === String(e.equ_id_sec),
        );
        const t = e.equ_id_tra || s?.sec_id_tra || s?.trayecto?.tra_id;
        return traId && String(t) === String(traId);
    });

    const sigNum = siguienteNumeroEquipo.value;

    let semestreHeredado = null;
    let totalSecHeredado = null;

    // 1. Buscar primero en equipos de la sección
    if (equiposSec.length > 0) {
        for (let i = equiposSec.length - 1; i >= 0; i--) {
            const parsed = parsearCodigoEquipo(equiposSec[i].equ_codigo);
            if (parsed) {
                if (parsed.semestre && !semestreHeredado)
                    semestreHeredado = parsed.semestre;
                if (parsed.totalSeccion && !totalSecHeredado)
                    totalSecHeredado = parsed.totalSeccion;
            }
        }
    }

    // 2. Si no hay en la sección, buscar en otros equipos del mismo trayecto
    if (!semestreHeredado && equiposTra.length > 0) {
        for (let i = equiposTra.length - 1; i >= 0; i--) {
            const parsed = parsearCodigoEquipo(equiposTra[i].equ_codigo);
            if (parsed && parsed.semestre) {
                semestreHeredado = parsed.semestre;
                break;
            }
        }
    }

    totalOriginalSeccion.value = totalSecHeredado || null;

    // Regla institucional: el primer número de la sección es el semestre del equipo
    const matchPrimerNum = sec?.sec_codigo?.match(/\d/);
    const primerNumeroSeccion = matchPrimerNum
        ? parseInt(matchPrimerNum[0])
        : sec?.trayecto?.tra_numero || 1;

    // Si ya existe al menos un equipo en la sección o en el trayecto: AUTO-COMPLETAR
    if (equiposSec.length > 0 || equiposTra.length > 0) {
        esAutoCompletado.value = true;
        formNuevo.value.equ_semestre = primerNumeroSeccion;
        const totFijado = totalSecHeredado || totalEquiposSeccion.value || 7;
        formNuevo.value.equ_total_seccion = totFijado;

        if (sigNum > totFijado) {
            // Ya se alcanzó el total establecido para la sección
            editarParametrosManual.value = true;
            formNuevo.value.equ_numero_grupo = String(sigNum);
            errores.value.equ_numero_grupo = `El N° de equipo (${sigNum}) supera el total actual de la sección (${totFijado}). Modifica el Total de Equipos a ${sigNum} o más para continuar.`;
        } else {
            editarParametrosManual.value = false;
            formNuevo.value.equ_numero_grupo = String(sigNum);
            if (
                errores.value.equ_numero_grupo?.includes("supera el total") ||
                errores.value.equ_numero_grupo?.includes("no puede sobrepasar")
            ) {
                delete errores.value.equ_numero_grupo;
            }
        }
    } else {
        // Es el PRIMER equipo de este trayecto y sección: pedir datos iniciales
        totalOriginalSeccion.value = null;
        esAutoCompletado.value = false;
        editarParametrosManual.value = true;
        formNuevo.value.equ_numero_grupo = "1";
        formNuevo.value.equ_semestre = primerNumeroSeccion;
        formNuevo.value.equ_total_seccion = 7;
    }

    // Limpiar integrantes que no pertenezcan a la sección elegida
    formNuevo.value.integrantes.forEach((item) => {
        if (!item.usu_id) return;
        const est = props.estudiantes?.find(
            (e) => String(e.usu_id) === String(item.usu_id),
        );
        if (est && est.sec_id && String(est.sec_id) !== String(secId)) {
            item.usu_id = "";
        }
    });
};

const totalOriginalSeccion = ref(null);
const totalModificado = computed(() => {
    return (
        totalOriginalSeccion.value !== null &&
        formNuevo.value.equ_total_seccion &&
        Number(formNuevo.value.equ_total_seccion) !==
            Number(totalOriginalSeccion.value)
    );
});

const validarNumeroVsTotal = () => {
    const num = parseInt(formNuevo.value.equ_numero_grupo);
    const tot = parseInt(formNuevo.value.equ_total_seccion);
    if (!isNaN(num) && !isNaN(tot)) {
        if (num > tot) {
            errores.value.equ_numero_grupo = `El N° de equipo (${num}) no puede sobrepasar el Total de Equipos (${tot}). Modifica el Total de Equipos si deseas agregar más.`;
        } else {
            if (
                errores.value.equ_numero_grupo?.includes(
                    "no puede sobrepasar",
                ) ||
                errores.value.equ_numero_grupo?.includes("supera el total")
            ) {
                delete errores.value.equ_numero_grupo;
            }
        }
    }
};

const onTotalSeccionInput = () => {
    validarNumeroVsTotal();
};
const estudiantesDisponibles = (idActual) => {
    const sel = formNuevo.value.integrantes
        .map((i) => i.usu_id)
        .filter((id) => id && id !== idActual);
    const secId = formNuevo.value.equ_id_sec;

    return (
        props.estudiantes?.filter((e) => {
            if (sel.includes(e.usu_id)) return false;
            if (e.usu_id === idActual) return true;
            if (secId && e.sec_id && String(e.sec_id) !== String(secId)) {
                return false;
            }
            return true;
        }) || []
    );
};
const actualizarLider = () => {
    formNuevo.value.integrantes.forEach((item, idx) => {
        item.es_lider = idx === 0;
    });
};
const agregarIntegrante = () => {
    if (formNuevo.value.integrantes.length >= 6) return;
    formNuevo.value.integrantes.push({
        usu_id: "",
        es_lider: false,
    });
    actualizarLider();
};
const quitarIntegrante = (idx) => {
    if (idx === 0) return; // El líder no se puede eliminar
    formNuevo.value.integrantes.splice(idx, 1);
    actualizarLider();
};
const agregarBeneficiario = () => {
    formProyecto.value.beneficiarios.push({ tbe_id: "", cantidad: "" });
};
const quitarBeneficiario = (idx) => {
    formProyecto.value.beneficiarios.splice(idx, 1);
};

const integrantesNoLider = computed(() =>
    (equipoSeleccionado.value?.integrantes || []).filter(
        (i) => !i.ein_es_lider && i.ein_status,
    ),
);

const abrirModalCrear = () => {
    paso.value = 1;
    editarParametrosManual.value = false;
    formNuevo.value = {
        equ_id_sec: props.seccion?.sec_id ? props.seccion.sec_id : "",
        equ_numero_grupo: "",
        equ_semestre: 1,
        equ_total_seccion: 7,
        equ_titulo: "",
        integrantes: [{ usu_id: "", es_lider: true }],
        justificacion_integrantes: "",
        justificacion_duplicidad: "",
    };
    if (formNuevo.value.equ_id_sec) {
        onSeccionChange();
    }
    formProyecto.value = {
        pco_id_com: "",
        pco_id_tpr: "",
        pco_id_mpr: "",
        pco_id_pco_previo: "",
        pco_fecha_limite: "",
        pco_resumen: "",
        justificacion_duplicidad: "",
        beneficiarios: [],
    };
    radarCrear.value = null;
    liderIndex.value = 0;
    errores.value = {};
    mCrear = mCrear || new Modal(document.getElementById("mCrear"));
    mCrear.show();
};
const abrirModalEditar = (equ) => {
    equipoEditando.value = equ;
    equipoSeleccionado.value = equ;
    const p = equ.proyecto_comunidad || equ.proyectoComunidad;
    formEditar.value = {
        equ_titulo: equ.equ_titulo,
        pco_id_com: p?.pco_id_com ?? "",
        pco_id_tpr: p?.pco_id_tpr ?? "",
        pco_id_mpr: p?.pco_id_mpr ?? "",
        pco_id_pco_previo: p?.pco_id_pco_previo ?? "",
        pco_resumen: p?.pco_resumen ?? "",
        pco_fecha_limite: p?.pco_fecha_limite
            ? String(p.pco_fecha_limite).slice(0, 10)
            : "",
        justificacion_duplicidad: equ.equ_justificacion_duplicidad || p?.pco_justificacion_duplicidad || "",
    };
    radarEditar.value = null;
    erroresEditar.value = {};
    mEditar = mEditar || new Modal(document.getElementById("mEditar"));
    mEditar.show();
};
const verIntegrantes = (equ) => {
    equipoSeleccionado.value = equ;
    tabActivo.value = "ints";
    historialEquipo.value = [];
    nuevoLiderId.value = "";
    mInts = mInts || new Modal(document.getElementById("mInts"));
    mInts.show();
};
const abrirDisolver = (equ) => {
    equipoADisolver.value = equ;
    motivoDisolucion.value = "";
    intentoDisolver.value = false;
    tipoAccionDisolver.value = "disolver";
    mDisolver = mDisolver || new Modal(document.getElementById("mDisolver"));
    mDisolver.show();
};

const validarPaso1 = () => {
    errores.value = {};
    if (!formNuevo.value.equ_id_sec) {
        errores.value.equ_id_sec = "La sección es obligatoria.";
        return false;
    }

    const sec =
        props.secciones?.find(
            (s) => String(s.sec_id) === String(formNuevo.value.equ_id_sec),
        ) || props.seccion;
    if (sec?.sec_codigo) {
        const matchPrimerNum = sec.sec_codigo.match(/\d/);
        const primerNum = matchPrimerNum ? parseInt(matchPrimerNum[0]) : null;
        if (primerNum !== null) {
            formNuevo.value.equ_semestre = primerNum;
        }
    }
    if (!formNuevo.value.equ_semestre) {
        errores.value.equ_semestre = "El semestre es obligatorio.";
        return false;
    }
    if (!formNuevo.value.equ_numero_grupo) {
        errores.value.equ_numero_grupo = "El número de grupo es obligatorio.";
        return false;
    }
    const numG = Number(formNuevo.value.equ_numero_grupo);
    const totS = Number(formNuevo.value.equ_total_seccion || numG);
    if (totS < numG) {
        errores.value.equ_numero_grupo = `El N° de equipo (${numG}) no puede sobrepasar el Total de Equipos (${totS}). Modifica el Total de Equipos primero.`;
        return false;
    }
    if (!formNuevo.value.equ_titulo?.trim()) {
        errores.value.equ_titulo = "El título es obligatorio.";
        return false;
    }
    const intsValidos = formNuevo.value.integrantes.filter((i) => i.usu_id);
    if (intsValidos.length === 0) {
        errores.value.integrantes =
            "Debes seleccionar al menos un integrante para el equipo.";
        return false;
    }
    formNuevo.value.integrantes = intsValidos;
    actualizarLider();

    if (intsValidos.length < 3) {
        if (!formNuevo.value.justificacion_integrantes?.trim()) {
            errores.value.justificacion_integrantes =
                "Debes justificar la creación del equipo con menos de 3 integrantes.";
            return false;
        }
    }

    errores.value = {};
    return true;
};

const irPaso2 = () => {
    if (!validarPaso1()) return;
    paso.value = 2;
};

const guardarSinProyecto = () => {
    if (!validarPaso1()) return;
    formProyecto.value.pco_id_com = "";
    guardarNuevo();
};

const guardarNuevo = () => {
    actualizarLider();

    // Si el radar de duplicidad detectó similitud, exigir justificación mínima de 15 caracteres
    if (radarCrear.value?.alerta_similitud?.existe) {
        const just = (formNuevo.value.justificacion_duplicidad || "").trim();
        if (just.length < 15) {
            errores.value = {
                ...errores.value,
                justificacion_duplicidad: "Debe ingresar una justificación detallada (mínimo 15 caracteres) explicando por qué este proyecto no es un duplicado.",
            };
            paso.value = 2;
            return;
        }
    }

    loading.value = true;

    // Preparar proyecto limpio solo si se eligió comunidad y tipo
    let proyectoLimpio = null;
    const justifDuplicidad = (formNuevo.value.justificacion_duplicidad || formProyecto.value.justificacion_duplicidad || "").trim();
    if (formProyecto.value.pco_id_com) {
        proyectoLimpio = {
            pco_id_com: formProyecto.value.pco_id_com,
            pco_id_tpr: formProyecto.value.pco_id_tpr || null,
            pco_id_mpr: formProyecto.value.pco_id_mpr || null,
            pco_id_pco_previo: formProyecto.value.pco_id_pco_previo || null,
            pco_fecha_limite: formProyecto.value.pco_fecha_limite || null,
            pco_resumen: formProyecto.value.pco_resumen || "",
            pco_justificacion_duplicidad: justifDuplicidad,
            justificacion_duplicidad: justifDuplicidad,
            beneficiarios: (formProyecto.value.beneficiarios || [])
                .filter((b) => b.tbe_id && Number(b.cantidad) > 0)
                .map((b) => ({
                    tbe_id: b.tbe_id,
                    cantidad: Number(b.cantidad),
                })),
        };
    }

    const payload = {
        equ_id_sec: formNuevo.value.equ_id_sec,
        equ_numero_grupo: formNuevo.value.equ_numero_grupo,
        equ_semestre: formNuevo.value.equ_semestre || 1,
        equ_total_seccion:
            formNuevo.value.equ_total_seccion ||
            formNuevo.value.equ_numero_grupo,
        equ_codigo: codigoPreview.value !== "—" ? codigoPreview.value : "",
        equ_titulo: formNuevo.value.equ_titulo,
        equ_justificacion_duplicidad: justifDuplicidad,
        justificacion_duplicidad: justifDuplicidad,
        integrantes: formNuevo.value.integrantes,
        justificacion_integrantes:
            formNuevo.value.justificacion_integrantes ?? "",
        proyecto: proyectoLimpio,
    };

    router.post(`${base.value}/equipos`, payload, {
        onError: (e) => {
            errores.value = e;
            loading.value = false;
            const tieneErrorProyecto = Object.keys(e).some((k) =>
                k.startsWith("proyecto") || k.includes("duplicidad"),
            );
            paso.value = tieneErrorProyecto ? 2 : 1;
        },
        onSuccess: () => {
            loading.value = false;
            paso.value = 1;
            mCrear?.hide();
        },
    });
};
const guardarEdicion = () => {
    // Si el radar de duplicidad detectó similitud en edición, exigir justificación
    if (radarEditar.value?.alerta_similitud?.existe) {
        const just = (formEditar.value.justificacion_duplicidad || "").trim();
        if (just.length < 15) {
            erroresEditar.value = {
                ...erroresEditar.value,
                justificacion_duplicidad: "Debe ingresar una justificación detallada (mínimo 15 caracteres) explicando por qué este proyecto no es un duplicado.",
            };
            return;
        }
    }

    loadingEditar.value = true;
    router.put(
        `${base.value}/equipos/${equipoEditando.value.equ_id}`,
        formEditar.value,
        {
            onError: (e) => {
                erroresEditar.value = e;
                loadingEditar.value = false;
            },
            onSuccess: () => {
                loadingEditar.value = false;
                mEditar?.hide();
            },
        },
    );
};

const confirmarDisolver = () => {
    if (tipoAccionDisolver.value === "eliminar") {
        const cod = codigoLimpio(equipoADisolver.value?.equ_codigo);
        if (
            !confirm(
                `¿Estás completamente seguro de eliminar de forma permanente el equipo ${cod}? Esta acción borrará todos sus datos y no se podrá deshacer.`,
            )
        ) {
            return;
        }
        router.delete(`${base.value}/equipos/${equipoADisolver.value.equ_id}`, {
            onSuccess: () => {
                mDisolver?.hide();
                equipoADisolver.value = null;
            },
        });
        return;
    }

    intentoDisolver.value = true;
    if (!motivoDisolucion.value.trim()) return;
    router.post(
        `${base.value}/equipos/${equipoADisolver.value.equ_id}/disolver`,
        { motivo: motivoDisolucion.value },
        {
            onSuccess: () => {
                mDisolver?.hide();
                equipoADisolver.value = null;
            },
        },
    );
};

const reactivar = (equ) => {
    const cod = codigoLimpio(equ.equ_codigo);
    if (!confirm(`¿Reactivar el equipo ${cod}?`)) return;
    router.post(`${base.value}/equipos/${equ.equ_id}/reactivar`);
};

const eliminarDefinitivamente = (equ) => {
    const cod = codigoLimpio(equ.equ_codigo);
    if (
        !confirm(
            `¿Eliminar definitivamente el equipo ${cod}? Esta acción borrará el equipo y todos sus datos del sistema si no se reactivará.`,
        )
    ) {
        return;
    }
    router.delete(`${base.value}/equipos/${equ.equ_id}`);
};
const removerIntegrante = (int) => {
    if (!confirm(`¿Remover a ${int.usuario?.usu_primer_nombre} del equipo?`))
        return;
    router.delete(
        `${base.value}/equipos/${equipoSeleccionado.value.equ_id}/integrantes/${int.ein_id}`,
        {
            preserveScroll: true,
            onSuccess: () => {
                const act = (props.equipos || []).find(
                    (e) => e.equ_id === equipoSeleccionado.value.equ_id,
                );
                if (act) {
                    equipoSeleccionado.value = act;
                }
            },
        },
    );
};
const confirmarCambioLider = () => {
    if (!nuevoLiderId.value) return;
    const int = equipoSeleccionado.value?.integrantes.find(
        (i) => i.ein_id == nuevoLiderId.value,
    );
    if (
        !confirm(
            `¿Asignar a ${int?.usuario?.usu_primer_nombre} como nuevo líder?`,
        )
    )
        return;
    router.post(
        `${base.value}/equipos/${equipoSeleccionado.value.equ_id}/cambiar-lider`,
        { nuevo_lider_id: nuevoLiderId.value },
        {
            onSuccess: () => {
                nuevoLiderId.value = "";
            },
        },
    );
};

const integrantesActivosSeleccionados = computed(() =>
    (equipoSeleccionado.value?.integrantes || []).filter(
        (i) => i.ein_status !== false,
    ),
);

const cargarHistorial = async () => {
    tabActivo.value = "hist";
    if (!equipoSeleccionado.value) return;
    cargandoHistorial.value = true;
    try {
        const res = await window.axios.get(
            `${base.value}/equipos/${equipoSeleccionado.value.equ_id}/historial`,
        );
        historialEquipo.value = res.data || [];
    } catch (e) {
        historialEquipo.value = [];
    } finally {
        cargandoHistorial.value = false;
    }
};

const colorEvt = (n) => {
    const m = {
        Disuelto: "background:#f8d7da;color:#842029",
        "Líder Cambiado": "background:#fff3cd;color:#664d03",
        "Integrante Removido": "background:rgba(220,53,69,0.12);color:#dc3545",
        Modificado: "background:rgba(13,110,253,0.12);color:#0d6efd",
        Reactivado: "background:#d1e7dd;color:#0f5132",
    };
    return m[n] || "background:#e2e3e5;color:#41464b";
};
const iconEvt = (n) => {
    if (n === "Disuelto") return "bi-x-circle";
    if (n === "Líder Cambiado") return "bi-arrow-repeat";
    if (n === "Integrante Removido") return "bi-arrow-left-right";
    if (n === "Modificado") return "bi-person-plus";
    if (n === "Reactivado") return "bi-arrow-counterclockwise";
    return "bi-circle";
};
const esRutaMaestra =
    typeof window !== "undefined" &&
    (window.location.pathname.includes("/coordinador/maestras") ||
        window.location.pathname.includes("/coordinador/tipos-proyecto"));
const menuMaestras = ref(
    esRutaMaestra || localStorage.getItem("coord-menu-maestras") === "true",
);
const menuAcademico = ref(
    localStorage.getItem("coord-menu-academico") === "true",
);
const menuSistema = ref(localStorage.getItem("coord-menu-sistema") === "true");

const toggleMenuMaestras = () => {
    menuMaestras.value = !menuMaestras.value;
    localStorage.setItem(
        "coord-menu-maestras",
        menuMaestras.value ? "true" : "false",
    );
};
const fechaMinima = computed(() => {
    return new Date().toISOString().split("T")[0];
});

const fechaMaxima = computed(() => {
    const d = new Date();
    d.setFullYear(d.getFullYear() + 1);
    return d.toISOString().split("T")[0];
});
const origenComunidad = ref("crear");
const origenTipo = ref("crear");
const guardandoComunidad = ref(false);
const erroresNuevaComunidad = ref({});
const nuevaComunidad = ref({
    com_nombre: "",
    com_ubicacion: "",
    com_id_tor: "",
});
let modalSubComunidad = null;

const abrirModalNuevaComunidad = (origen = "crear") => {
    origenComunidad.value = origen;
    erroresNuevaComunidad.value = {};
    nuevaComunidad.value = {
        com_nombre: "",
        com_ubicacion: "",
        com_id_tor: "",
    };
    const el = document.getElementById("mModalNuevaComunidad");
    if (el) {
        modalSubComunidad =
            Modal.getInstance(el) || new Modal(el, { backdrop: "static" });
        modalSubComunidad.show();
    }
};

const cerrarModalNuevaComunidad = () => {
    modalSubComunidad?.hide();
    setTimeout(() => {
        if (document.querySelector(".modal.show")) {
            document.body.classList.add("modal-open");
        }
    }, 300);
};

const guardarNuevaComunidad = async () => {
    erroresNuevaComunidad.value = {};
    if (!nuevaComunidad.value.com_nombre.trim()) {
        erroresNuevaComunidad.value.com_nombre = "El nombre es obligatorio.";
        return;
    }
    if (!nuevaComunidad.value.com_id_tor) {
        erroresNuevaComunidad.value.com_id_tor =
            "El tipo de organización es obligatorio.";
        return;
    }
    guardandoComunidad.value = true;
    const ruta = base.value + "/comunidades";

    try {
        const response = await window.axios.post(ruta, nuevaComunidad.value);
        guardandoComunidad.value = false;

        const creada = response.data?.comunidad;
        if (creada) {
            props.comunidades.push(creada);
            if (origenComunidad.value === "crear") {
                formProyecto.value.pco_id_com = creada.com_id;
            } else {
                formEditar.value.pco_id_com = creada.com_id;
            }
        }
        cerrarModalNuevaComunidad();
    } catch (error) {
        guardandoComunidad.value = false;
        if (error.response?.data?.errors) {
            erroresNuevaComunidad.value = Object.fromEntries(
                Object.entries(error.response.data.errors).map(([k, v]) => [
                    k,
                    v[0],
                ]),
            );
        } else if (error.response?.data?.message) {
            erroresNuevaComunidad.value.com_nombre =
                error.response.data.message;
        } else {
            erroresNuevaComunidad.value.com_nombre =
                "Error al registrar la comunidad.";
        }
    }
};

const guardandoTipo = ref(false);
const erroresNuevoTipo = ref({});
const nuevoTipo = ref({ tpr_nombre: "" });
let modalSubTipo = null;

const abrirModalNuevoTipo = (origen = "crear") => {
    origenTipo.value = origen;
    erroresNuevoTipo.value = {};
    nuevoTipo.value = { tpr_nombre: "" };
    const el = document.getElementById("mModalNuevoTipo");
    if (el) {
        modalSubTipo =
            Modal.getInstance(el) || new Modal(el, { backdrop: "static" });
        modalSubTipo.show();
    }
};

const cerrarModalNuevoTipo = () => {
    modalSubTipo?.hide();
    setTimeout(() => {
        if (document.querySelector(".modal.show")) {
            document.body.classList.add("modal-open");
        }
    }, 300);
};

const guardarNuevoTipo = async () => {
    erroresNuevoTipo.value = {};
    if (!nuevoTipo.value.tpr_nombre.trim()) {
        erroresNuevoTipo.value.tpr_nombre = "El nombre es obligatorio.";
        return;
    }
    guardandoTipo.value = true;
    const ruta = `${base.value}/tipos-proyecto`;

    try {
        const response = await window.axios.post(ruta, nuevoTipo.value);
        guardandoTipo.value = false;

        const creado = response.data?.tipoProyecto;
        if (creado) {
            props.tiposProyecto.push(creado);
            if (origenTipo.value === "crear") {
                formProyecto.value.pco_id_tpr = creado.tpr_id;
            } else {
                formEditar.value.pco_id_tpr = creado.tpr_id;
            }
        }
        cerrarModalNuevoTipo();
    } catch (error) {
        guardandoTipo.value = false;
        if (error.response?.data?.errors) {
            erroresNuevoTipo.value = Object.fromEntries(
                Object.entries(error.response.data.errors).map(([k, v]) => [
                    k,
                    v[0],
                ]),
            );
        } else if (error.response?.data?.message) {
            erroresNuevoTipo.value.tpr_nombre = error.response.data.message;
        } else {
            erroresNuevoTipo.value.tpr_nombre =
                "Error al registrar el tipo de proyecto.";
        }
    }
};

const requiereProyectoPrevio = computed(() => {
    if (!formProyecto.value.pco_id_mpr) return false;
    const mod = props.modalidades?.find(
        (m) => m.mpr_id == formProyecto.value.pco_id_mpr,
    );
    if (!mod) return false;
    const n = mod.mpr_nombre.toLowerCase();
    return (
        n.includes("continua") ||
        n.includes("reing") ||
        n.includes("reactiva") ||
        n.includes("previo")
    );
});

const proyectosPreviosComunidad = computed(() => {
    if (!formProyecto.value.pco_id_com) return [];
    const com = props.comunidades?.find(
        (c) => c.com_id == formProyecto.value.pco_id_com,
    );
    if (!com || !com.proyectos) return [];
    return com.proyectos.filter((p) => p.pco_status || p.equipo);
});

const requiereProyectoPrevioEditar = computed(() => {
    if (!formEditar.value.pco_id_mpr) return false;
    const mod = props.modalidades?.find(
        (m) => m.mpr_id == formEditar.value.pco_id_mpr,
    );
    if (!mod) return false;
    const n = mod.mpr_nombre.toLowerCase();
    return (
        n.includes("continua") ||
        n.includes("reing") ||
        n.includes("reactiva") ||
        n.includes("previo")
    );
});

const proyectosPreviosComunidadEditar = computed(() => {
    if (!formEditar.value.pco_id_com) return [];
    const com = props.comunidades?.find(
        (c) => c.com_id == formEditar.value.pco_id_com,
    );
    if (!com || !com.proyectos) return [];
    return com.proyectos.filter(
        (p) =>
            (p.pco_status || p.equipo) &&
            p.pco_id_equ !== equipoEditando.value?.equ_id,
    );
});

const abrirTraslado = (int) => {
    integranteATrasladar.value = int;
    equipoDestinoId.value = "";
    mTrasladar = mTrasladar || new Modal(document.getElementById("mTrasladar"));
    mTrasladar.show();
};

const equiposDestinoDisponibles = computed(() => {
    if (!equipoSeleccionado.value) return [];
    return (props.equipos || []).filter(
        (e) =>
            e.equ_id !== equipoSeleccionado.value.equ_id &&
            e.equ_status &&
            e.equ_id_sec === equipoSeleccionado.value.equ_id_sec,
    );
});

const confirmarTraslado = () => {
    if (!equipoDestinoId.value || !integranteATrasladar.value) return;
    cargandoTraslado.value = true;
    router.post(
        `${base.value}/equipos/${equipoSeleccionado.value.equ_id}/integrantes/${integranteATrasladar.value.ein_id}/trasladar`,
        { nuevo_equipo_id: equipoDestinoId.value },
        {
            preserveScroll: true,
            onSuccess: () => {
                cargandoTraslado.value = false;
                mTrasladar?.hide();
                const act = (props.equipos || []).find(
                    (e) => e.equ_id === equipoSeleccionado.value.equ_id,
                );
                if (act) {
                    equipoSeleccionado.value = act;
                }
            },
            onError: (err) => {
                cargandoTraslado.value = false;
                alert(
                    Object.values(err)[0] || "Error al trasladar integrante.",
                );
            },
        },
    );
};

const integrantesAgregados = computed(
    () => formNuevo.value.integrantes.filter((i) => i.usu_id).length,
);
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
    transition:
        background 0.3s,
        border-color 0.3s;
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
    font-size: 0.9rem;
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
.chip {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 3px 10px;
    border-radius: 20px;
    background: var(--bg-th);
    border: 1px solid var(--border);
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-main);
    margin: 2px;
}
.cdot {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.65rem;
    font-weight: 800;
    color: #fff;
}
.step-ind {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 1.5rem;
}
.step-circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.8rem;
    flex-shrink: 0;
}
.sc-active {
    background: #dc3545;
    color: #fff;
}
.sc-done {
    background: #198754;
    color: #fff;
}
.sc-pending {
    background: var(--bg-th);
    color: var(--text-muted);
    border: 2px solid var(--border);
}
.step-line {
    flex: 1;
    height: 2px;
    background: var(--border);
}
.step-line.done {
    background: #198754;
}
.evt-dot {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.8rem;
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
