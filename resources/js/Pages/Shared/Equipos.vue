<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
        "
    >
        <div
            class="sb-overlay"
            :class="{ show: sidebarOpen }"
            @click="sidebarOpen = false"
        ></div>

        <!-- SIDEBAR coordinador vs profesor -->
        <div class="sb" :class="{ show: sidebarOpen }">
            <div class="sb-brand">
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
                    💻
                </div>
                <div>
                    <div class="sb-title">UPTP — Proyectos</div>
                    <div class="sb-sub">PNF Informática</div>
                </div>
                <button
                    class="btn btn-sm text-white ms-auto fs-5 p-0 d-lg-none"
                    @click="sidebarOpen = false"
                >
                    <i class="bi bi-x"></i>
                </button>
            </div>
            <div class="sb-user">
                <div class="sb-av">{{ iniciales }}</div>
                <div>
                    <div class="sb-name">
                        {{ $page.props.auth.user.usu_primer_nombre }}
                        {{ $page.props.auth.user.usu_primer_apellido }}
                    </div>
                    <div class="sb-role">
                        {{
                            esProfesor
                                ? "Profesor de Proyecto"
                                : "Coordinador PNF"
                        }}
                    </div>
                </div>
            </div>
            <div class="sb-nav">
                <!-- Coordinador -->
                <template v-if="!esProfesor">
                    <!-- Principal -->
                    <div class="sb-sec">Principal</div>
                    <Link href="/coordinador" class="sb-lnk">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </Link>
                    <Link href="/coordinador/secciones" class="sb-lnk">
                        <i class="bi bi-collection"></i> Secciones
                    </Link>
                    <Link href="/coordinador/comunidades" class="sb-lnk">
                        <i class="bi bi-building"></i> Comunidades
                    </Link>
                    <Link href="/coordinador/equipos" class="sb-lnk active">
                        <i class="bi bi-people"></i> Equipos
                    </Link>
                    <Link href="/coordinador/proyectos" class="sb-lnk">
                        <i class="bi bi-folder"></i> Proyectos
                    </Link>

                    <!-- Tablas Maestras — desplegable -->
                    <div
                        class="sb-sec sb-toggle"
                        @click="toggleMenuMaestras"
                        style="
                            cursor: pointer;
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            padding-right: 1.2rem;
                        "
                    >
                        <span>Tablas Maestras</span>
                        <i
                            class="bi"
                            :class="
                                menuMaestras
                                    ? 'bi-chevron-up'
                                    : 'bi-chevron-down'
                            "
                            style="font-size: 0.75rem"
                        ></i>
                    </div>
                    <div v-show="menuMaestras">
                        <Link href="/coordinador/tipos-proyecto" class="sb-lnk">
                            <i class="bi bi-tag"></i> Tipos de Proyecto
                        </Link>
                        <Link
                            href="/coordinador/maestras/modalidades"
                            class="sb-lnk"
                        >
                            <i class="bi bi-layers"></i> Modalidades
                        </Link>
                        <Link
                            href="/coordinador/maestras/tipos-jurado"
                            class="sb-lnk"
                        >
                            <i class="bi bi-person-badge"></i> Tipos de Jurado
                        </Link>
                        <Link
                            href="/coordinador/maestras/tipos-beneficiario"
                            class="sb-lnk"
                        >
                            <i class="bi bi-people"></i> Tipos Beneficiario
                        </Link>
                        <Link
                            href="/coordinador/maestras/tipos-entregable"
                            class="sb-lnk"
                        >
                            <i class="bi bi-file-earmark"></i> Tipos Entregable
                        </Link>
                        <Link
                            href="/coordinador/maestras/tipos-evento-equipo"
                            class="sb-lnk"
                        >
                            <i class="bi bi-calendar-event"></i> Eventos Equipo
                        </Link>
                        <Link
                            href="/coordinador/maestras/turnos"
                            class="sb-lnk"
                        >
                            <i class="bi bi-clock"></i> Turnos
                        </Link>
                        <Link
                            href="/coordinador/maestras/estados-proyecto"
                            class="sb-lnk"
                        >
                            <i class="bi bi-award"></i> Estados Proyecto
                        </Link>
                    </div>

                    <!-- Académico — desplegable -->
                    <div
                        class="sb-sec sb-toggle"
                        @click="menuAcademico = !menuAcademico"
                        style="
                            cursor: pointer;
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            padding-right: 1.2rem;
                        "
                    >
                        <span>Académico</span>
                        <i
                            class="bi"
                            :class="
                                menuAcademico
                                    ? 'bi-chevron-up'
                                    : 'bi-chevron-down'
                            "
                            style="font-size: 0.75rem"
                        ></i>
                    </div>
                    <div v-show="menuAcademico">
                        <Link
                            href="/coordinador/socializaciones"
                            class="sb-lnk"
                        >
                            <i class="bi bi-mic"></i> Socializaciones
                        </Link>
                        <Link href="/coordinador/resultados" class="sb-lnk">
                            <i class="bi bi-trophy"></i> Resultados
                        </Link>
                    </div>

                    <!-- Sistema — desplegable -->
                    <div
                        class="sb-sec sb-toggle"
                        @click="menuSistema = !menuSistema"
                        style="
                            cursor: pointer;
                            display: flex;
                            align-items: center;
                            justify-content: space-between;
                            padding-right: 1.2rem;
                        "
                    >
                        <span>Sistema</span>
                        <i
                            class="bi"
                            :class="
                                menuSistema
                                    ? 'bi-chevron-up'
                                    : 'bi-chevron-down'
                            "
                            style="font-size: 0.75rem"
                        ></i>
                    </div>
                    <div v-show="menuSistema">
                        <Link href="/coordinador/reportes" class="sb-lnk">
                            <i class="bi bi-bar-chart"></i> Reportes
                        </Link>
                        <Link href="/coordinador/cartas" class="sb-lnk">
                            <i class="bi bi-file-earmark-text"></i> Cartas
                        </Link>
                        <Link href="/coordinador/bitacora" class="sb-lnk">
                            <i class="bi bi-journal-text"></i> Bitácora
                        </Link>
                        <Link href="/coordinador/configuracion" class="sb-lnk">
                            <i class="bi bi-gear"></i> Configuración
                        </Link>
                    </div>
                </template>
                <!-- Profesor -->
                <template v-else>
                    <div class="sb-sec">Principal</div>
                    <Link href="/profesor" class="sb-lnk"
                        ><i class="bi bi-speedometer2"></i> Dashboard</Link
                    >
                    <Link href="/profesor/secciones" class="sb-lnk active"
                        ><i class="bi bi-people"></i> Mis Equipos</Link
                    >
                    <div class="sb-sec">Gestión</div>
                    <Link href="/profesor/puntos-control" class="sb-lnk"
                        ><i class="bi bi-flag"></i> Puntos de Control</Link
                    >
                    <Link href="/profesor/socializaciones" class="sb-lnk"
                        ><i class="bi bi-mic"></i> Socializaciones</Link
                    >
                    <Link href="/profesor/entregables" class="sb-lnk"
                        ><i class="bi bi-file-earmark-check"></i>
                        Entregables</Link
                    >
                </template>
                <div style="padding: 0.75rem 1.4rem">
                    <form @submit.prevent="logout">
                        <button
                            type="submit"
                            class="sb-lnk w-100 text-start border-0 p-0"
                            style="
                                color: #ff8591;
                                font-weight: 700;
                                background: none;
                                font-size: 0.88rem;
                            "
                        >
                            <i class="bi bi-box-arrow-left"></i> Cerrar Sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>

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
                        <span class="badge bg-danger fs-6">Sección {{ seccion.sec_codigo }}</span>
                        <span class="fw-bold" style="color: var(--text-main)">{{ seccion.trayecto }}</span>
                        <span class="badge bg-secondary">Turno {{ seccion.turno }}</span>
                    </div>
                    <Link
                        v-if="modoProfesor || esProfesor"
                        href="/profesor/secciones"
                        class="btn btn-sm btn-outline-danger fw-bold"
                    >
                        <i class="bi bi-arrow-left me-1"></i>Volver a Mis Secciones
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
                                        colspan="7"
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
                                        {{ equ.equ_codigo }}
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
                                            >{{ (equ.integrantes || []).filter(i => i.ein_status !== false).length }}
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
                                            <a
                                                v-if="!esProfesor"
                                                :href="`/coordinador/equipos/${equ.equ_id}/expediente`"
                                                class="btn btn-sm btn-outline-info me-1"
                                                title="Expediente"
                                                ><i
                                                    class="bi bi-folder2-open"
                                                ></i
                                            ></a>
                                            <button
                                                class="btn btn-sm btn-outline-warning me-1"
                                                @click="abrirHistorialAcademico(equ)"
                                                title="Historial Académico"
                                            >
                                                <i class="bi bi-mortarboard-fill"></i>
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
                                            >
                                                <i
                                                    class="bi bi-arrow-counterclockwise me-1"
                                                ></i
                                                >Reactivar
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
                                    <div class="col-12" v-if="formNuevo.equ_id_sec && esAutoCompletado">
                                        <div
                                            class="py-2 px-3 rounded small d-flex align-items-center justify-content-between"
                                            style="background: rgba(13, 110, 253, 0.08); border-left: 3px solid #0d6efd; color: var(--text-main);"
                                        >
                                            <div>
                                                <i class="bi bi-magic text-primary me-1"></i>
                                                <strong>Datos auto-completados:</strong> Semestre ({{ formNuevo.equ_semestre }}) y Total de Equipos ({{ formNuevo.equ_total_seccion }}) configurados según el equipo previo de este trayecto. Solo debes indicar el <strong>N° de Equipo</strong>.
                                            </div>
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-link p-0 text-decoration-none fw-semibold text-primary ms-2 flex-shrink-0"
                                                @click="editarParametrosManual = !editarParametrosManual"
                                            >
                                                <i class="bi bi-pencil-square me-1"></i>{{ editarParametrosManual ? 'Bloquear' : 'Modificar' }}
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-12" v-else-if="formNuevo.equ_id_sec">
                                        <div
                                            class="py-2 px-3 rounded small"
                                            style="background: rgba(255, 193, 7, 0.12); border-left: 3px solid #ffc107; color: var(--text-main);"
                                        >
                                            <i class="bi bi-flag-fill text-warning me-1"></i>
                                            <strong>Primer equipo de este trayecto:</strong> Define el Semestre y Total de Equipos para la sección. A partir del 2do equipo, estos datos se auto-completarán solos.
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="d-flex align-items-end mb-1" style="min-height: 22px;">
                                            <label class="form-label fw-semibold small mb-0" :class="{ 'text-danger': esAutoCompletado }">
                                                N° Equipo
                                                <span class="text-danger">*</span>
                                            </label>
                                        </div>
                                        <input
                                            type="number"
                                            class="form-control"
                                            :class="{
                                                'border-danger fw-bold shadow-sm': esAutoCompletado,
                                                'is-invalid': errores.equ_numero_grupo
                                            }"
                                            v-model="formNuevo.equ_numero_grupo"
                                            min="1"
                                            :max="formNuevo.equ_total_seccion || 99"
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
                                        <div class="d-flex align-items-end justify-content-between mb-1" style="min-height: 22px;">
                                            <label class="form-label fw-semibold small mb-0">
                                                Semestre
                                                <span class="text-danger">*</span>
                                            </label>
                                            <i v-if="esAutoCompletado && !editarParametrosManual" class="bi bi-lock-fill text-muted small" title="Auto-completado"></i>
                                        </div>
                                        <input
                                            type="number"
                                            class="form-control"
                                            v-model="formNuevo.equ_semestre"
                                            min="1"
                                            max="9"
                                            :readonly="esAutoCompletado && !editarParametrosManual"
                                            :style="(esAutoCompletado && !editarParametrosManual) ? 'background: var(--bg-th) !important;' : ''"
                                            @keypress="
                                                (e) =>
                                                    !/[0-9]/.test(e.key) &&
                                                    e.preventDefault()
                                            "
                                        />
                                        <div
                                            v-if="errores.equ_semestre"
                                            class="text-danger small mt-1"
                                        >
                                            {{ errores.equ_semestre }}
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-end justify-content-between mb-1" style="min-height: 22px;">
                                            <label class="form-label fw-semibold small mb-0 text-truncate">
                                                Total Equipos
                                                <span class="text-danger">*</span>
                                            </label>
                                            <button
                                                v-if="esAutoCompletado"
                                                type="button"
                                                class="btn btn-sm btn-link p-0 text-decoration-none fw-semibold"
                                                style="font-size: 0.75rem;"
                                                @click="editarParametrosManual = !editarParametrosManual"
                                                :title="editarParametrosManual ? 'Fijar total' : 'Modificar total de equipos de la sección'"
                                            >
                                                <i class="bi" :class="editarParametrosManual ? 'bi-lock-fill text-success' : 'bi-pencil-square text-primary'"></i>
                                                {{ editarParametrosManual ? 'Fijar' : 'Modificar' }}
                                            </button>
                                        </div>
                                        <input
                                            type="number"
                                            class="form-control"
                                            :class="{
                                                'border-primary fw-bold': editarParametrosManual,
                                                'is-invalid': errores.equ_total_seccion
                                            }"
                                            v-model="formNuevo.equ_total_seccion"
                                            min="1"
                                            max="99"
                                            :readonly="esAutoCompletado && !editarParametrosManual"
                                            :style="(esAutoCompletado && !editarParametrosManual) ? 'background: var(--bg-th) !important;' : ''"
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
                                        <div class="d-flex align-items-end mb-1" style="min-height: 22px;">
                                            <label class="form-label fw-semibold small mb-0 text-truncate">
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
                                            style="background: rgba(25, 135, 84, 0.08); border-left: 3px solid #198754; color: var(--text-main);"
                                        >
                                            <i class="bi bi-arrow-repeat text-success fs-6 flex-shrink-0"></i>
                                            <span>
                                                <strong>Actualización automática de códigos:</strong> El total de la sección cambiará de <strong>{{ totalOriginalSeccion }}</strong> a <strong>{{ formNuevo.equ_total_seccion }}</strong>. Al guardar este equipo, los códigos de todos los equipos de esta sección se actualizarán automáticamente al nuevo total (ej: <strong>/{{ formNuevo.equ_total_seccion }}</strong>).
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
                                    <strong>Líder de Equipo:</strong> El primer estudiante de la lista es asignado automáticamente como líder del equipo. Recibirá credenciales de acceso con su cédula.
                                </div>
                                <!-- Encabezado de columnas ANTES del v-for -->
                                <div
                                    class="row mb-1 small fw-bold"
                                    style="color: var(--text-muted)"
                                >
                                    <div class="col-8">Estudiante</div>
                                    <div class="col-3 text-center">Rol en el Equipo</div>
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
                                            :estudiantes="estudiantesDisponibles(item.usu_id)"
                                            :todos-estudiantes="props.estudiantes"
                                            :is-lider="idx === 0"
                                            :disabled="!formNuevo.equ_id_sec"
                                            :placeholder="!formNuevo.equ_id_sec ? 'Seleccione primero una sección...' : (idx === 0 ? 'Escribe para buscar Líder...' : 'Escribe para buscar estudiante...')"
                                        />
                                    </div>
                                    <div class="col-3 text-center">
                                        <span
                                            v-if="idx === 0"
                                            class="badge bg-danger py-2 px-2 shadow-sm d-inline-flex align-items-center justify-content-center w-100"
                                            style="font-size: 0.78rem; letter-spacing: 0.2px;"
                                        >
                                            <i class="bi bi-star-fill text-warning me-1"></i> Líder de Equipo
                                        </span>
                                        <span
                                            v-else
                                            class="badge bg-secondary py-2 px-2 text-white d-inline-flex align-items-center justify-content-center w-100"
                                            style="font-size: 0.78rem;"
                                        >
                                            <i class="bi bi-person me-1"></i> Integrante
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
                                        <label class="form-label fw-semibold">Comunidad</label>
                                        <div class="d-flex gap-2 align-items-center w-100" style="min-width: 0;">
                                            <SelectBuscableComunidad
                                                v-model="formProyecto.pco_id_com"
                                                :comunidades="comunidades"
                                                size="sm"
                                                placeholder="Escribe para buscar comunidad..."
                                            />
                                            <button
                                                type="button"
                                                class="btn btn-sm btn-outline-danger flex-shrink-0 fw-semibold d-inline-flex align-items-center"
                                                style="white-space: nowrap;"
                                                @click="abrirModalNuevaComunidad('crear')"
                                                title="Registrar nueva comunidad"
                                            >
                                                <i class="bi bi-plus-lg me-1"></i>Nueva
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold">Tipo de Proyecto</label>
                                        <div class="d-flex gap-2 align-items-center w-100" style="min-width: 0;">
                                            <select
                                                class="form-select form-select-sm flex-grow-1"
                                                style="min-width: 0;"
                                                v-model="formProyecto.pco_id_tpr"
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
                                                style="white-space: nowrap;"
                                                @click="abrirModalNuevoTipo('crear')"
                                                title="Registrar nuevo tipo de proyecto"
                                            >
                                                <i class="bi bi-plus-lg me-1"></i>Nuevo
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
                                    <div v-if="requiereProyectoPrevio" class="col-12">
                                        <div
                                            class="p-2 rounded border"
                                            style="
                                                background: rgba(13, 110, 253, 0.05);
                                                border-color: rgba(13, 110, 253, 0.2) !important;
                                            "
                                        >
                                            <label class="form-label fw-semibold small mb-1">
                                                <i class="bi bi-search me-1 text-primary"></i>Proyecto previo en esta comunidad:
                                            </label>
                                            <select
                                                v-model="formProyecto.pco_id_pco_previo"
                                                class="form-select form-select-sm"
                                            >
                                                <option value="">Seleccionar proyecto anterior...</option>
                                                <option
                                                    v-for="prev in proyectosPreviosComunidad"
                                                    :key="prev.pco_id"
                                                    :value="prev.pco_id"
                                                >
                                                    {{ prev.equipo?.equ_titulo || prev.pco_resumen || ('Proyecto #' + prev.pco_id) }} ({{ prev.equipo?.equ_codigo || 'Sin código' }})
                                                </option>
                                            </select>
                                            <small v-if="proyectosPreviosComunidad.length === 0" class="text-muted d-block mt-1">
                                                No se encontraron proyectos previos en esta comunidad.
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
                                    <i v-else class="bi bi-check-lg me-1"></i>Guardar sin Comunidad
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
                                <span v-if="!equipoSeleccionado?.proyecto_comunidad" class="badge bg-secondary ms-1">Sin asignar</span>
                            </p>

                            <div class="mb-3">
                                <label class="form-label small fw-bold"
                                    >Comunidad</label
                                >
                                <div class="d-flex gap-2 align-items-center w-100" style="min-width: 0;">
                                    <SelectBuscableComunidad
                                        v-model="formEditar.pco_id_com"
                                        :comunidades="comunidades"
                                        size="sm"
                                        placeholder="Escribe para buscar comunidad..."
                                    />
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger flex-shrink-0 fw-semibold d-inline-flex align-items-center"
                                        style="white-space: nowrap;"
                                        @click="abrirModalNuevaComunidad('editar')"
                                        title="Registrar nueva comunidad"
                                    >
                                        <i class="bi bi-plus-lg me-1"></i>Nueva
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold"
                                    >Tipo de Proyecto</label
                                >
                                <div class="d-flex gap-2 align-items-center w-100" style="min-width: 0;">
                                    <select
                                        class="form-select form-select-sm flex-grow-1"
                                        style="min-width: 0;"
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

                            <div v-if="requiereProyectoPrevioEditar" class="mb-2">
                                <div
                                    class="p-2 rounded border"
                                    style="
                                        background: rgba(13, 110, 253, 0.05);
                                        border-color: rgba(13, 110, 253, 0.2) !important;
                                    "
                                >
                                    <label class="form-label fw-semibold small mb-1">
                                        <i class="bi bi-search me-1 text-primary"></i>Proyecto previo en esta comunidad:
                                    </label>
                                    <select
                                        v-model="formEditar.pco_id_pco_previo"
                                        class="form-select form-select-sm"
                                    >
                                        <option value="">Seleccionar proyecto anterior...</option>
                                        <option
                                            v-for="prev in proyectosPreviosComunidadEditar"
                                            :key="prev.pco_id"
                                            :value="prev.pco_id"
                                        >
                                            {{ prev.equipo?.equ_titulo || prev.pco_resumen || ('Proyecto #' + prev.pco_id) }} ({{ prev.equipo?.equ_codigo || 'Sin código' }})
                                        </option>
                                    </select>
                                    <small v-if="proyectosPreviosComunidadEditar.length === 0" class="text-muted d-block mt-1">
                                        No se encontraron proyectos previos en esta comunidad.
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
                                    v-model="
                                        formEditar.pco_fecha_limite
                                    "
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
                            <ul v-if="integrantesActivosSeleccionados.length > 0" class="list-group list-group-flush">
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
                                            <i class="bi bi-arrow-left-right"></i>
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
                            <div v-else class="text-center py-4 text-muted small">
                                <i class="bi bi-people me-1"></i>No hay integrantes activos en este equipo.
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
                            <strong>{{
                                integranteATrasladar?.usuario?.usu_primer_nombre
                            }}
                            {{
                                integranteATrasladar?.usuario?.usu_primer_apellido
                            }}</strong>
                            (CI: {{ integranteATrasladar?.usuario?.usu_cedula }}) a otro equipo de la misma sección.
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">
                                Equipo de destino en la sección:
                            </label>
                            <select
                                v-model="equipoDestinoId"
                                class="form-select"
                            >
                                <option value="">Seleccionar equipo de destino...</option>
                                <option
                                    v-for="eq in equiposDestinoDisponibles"
                                    :key="eq.equ_id"
                                    :value="eq.equ_id"
                                >
                                    {{ eq.equ_codigo }} — {{ eq.equ_titulo }} ({{ eq.integrantes?.length || 0 }} integrantes)
                                </option>
                            </select>
                            <small
                                v-if="equiposDestinoDisponibles.length === 0"
                                class="text-danger d-block mt-1"
                            >
                                No hay otros equipos activos en esta sección a los cuales trasladar.
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
                                <i class="bi bi-exclamation-triangle me-1"></i
                                >Esta acción desactivará el equipo y todos sus
                                integrantes. No se puede deshacer.
                            </div>
                            <div class="mb-3">
                                <p style="color: var(--text-main)" class="mb-1">
                                    Equipo:
                                    <strong style="color: #dc3545">{{
                                        equipoADisolver?.equ_codigo
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
                            <div>
                                <label class="form-label fw-semibold"
                                    >Motivo
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
                            >
                                <i class="bi bi-x-circle me-2"></i>Disolver
                                Equipo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Historial Académico -->
        <div class="modal fade" id="mHistorialAcademico" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg overflow-hidden" style="background: var(--bg-card); border-radius: 12px;">
                    <div class="mh" style="background: #1a1d23; border-bottom: 2px solid #dc3545; padding: 1.1rem 1.4rem;">
                        <div class="d-flex align-items-center gap-2">
                            <div style="width: 34px; height: 34px; background: #dc3545; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800;">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 text-white fw-bold">Historial Académico del Equipo</h5>
                                <small class="text-white-50" v-if="equipoHistorial">
                                    <span class="badge bg-danger me-1">{{ equipoHistorial.equ_codigo }}</span> {{ equipoHistorial.equ_titulo }}
                                </small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm text-white p-0 fs-5" data-bs-dismiss="modal">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <div class="modal-body p-4">
                        <div v-if="cargandoHistorialAcademico" class="text-center py-5">
                            <div class="spinner-border text-danger" role="status"></div>
                            <p class="text-muted mt-2">Cargando historial académico...</p>
                        </div>
                        <template v-else-if="equipoHistorial">
                            <!-- Resumen del Equipo -->
                            <div class="sc mb-3 p-3" style="border-left: 4px solid #dc3545 !important;">
                                <div class="row g-3 text-center text-md-start">
                                    <div class="col-md-3">
                                        <span class="small fw-bold text-uppercase d-block" style="color: var(--text-muted); font-size: 0.72rem;">Sección & Trayecto</span>
                                        <span class="fw-bold" style="color: var(--text-main);">{{ equipoHistorial.seccion?.sec_codigo }} — {{ equipoHistorial.trayecto?.tra_nombre }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="small fw-bold text-uppercase d-block" style="color: var(--text-muted); font-size: 0.72rem;">Comunidad</span>
                                        <span class="fw-bold" style="color: var(--text-main);">{{ equipoHistorial.proyecto_comunidad?.comunidad?.com_nombre || 'Sin asignar' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="small fw-bold text-uppercase d-block" style="color: var(--text-muted); font-size: 0.72rem;">Tipo de Proyecto</span>
                                        <span class="fw-bold" style="color: var(--text-main);">{{ equipoHistorial.proyecto_comunidad?.tipo_proyecto?.tpr_nombre || 'Sin definir' }}</span>
                                    </div>
                                    <div class="col-md-3">
                                        <span class="small fw-bold text-uppercase d-block" style="color: var(--text-muted); font-size: 0.72rem;">Estado del Proyecto</span>
                                        <span class="bs" :class="equipoHistorial.resultado_proyecto?.estado_proyecto?.epr_nombre === 'Aprobado' ? 'ba' : 'bd'">
                                            {{ equipoHistorial.resultado_proyecto?.estado_proyecto?.epr_nombre || 'En Desarrollo' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Navegación por pestañas (Rojo, Negro y Blanco) -->
                            <div class="d-flex gap-2 mb-3 pb-2 overflow-x-auto" style="border-bottom: 1px solid var(--border);">
                                <button
                                    type="button"
                                    class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                                    :style="tabHistorial === 'puntos' ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;' : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'"
                                    @click="tabHistorial = 'puntos'"
                                >
                                    <i class="bi bi-flag me-1"></i> Puntos de Control ({{ equipoHistorial.puntos_control?.length || 0 }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                                    :style="tabHistorial === 'entregables' ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;' : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'"
                                    @click="tabHistorial = 'entregables'"
                                >
                                    <i class="bi bi-file-earmark-check me-1"></i> Entregables ({{ equipoHistorial.entregables?.length || 0 }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                                    :style="tabHistorial === 'socializaciones' ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;' : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'"
                                    @click="tabHistorial = 'socializaciones'"
                                >
                                    <i class="bi bi-people me-1"></i> Socializaciones ({{ equipoHistorial.socializaciones?.length || 0 }})
                                </button>
                                <button
                                    type="button"
                                    class="btn btn-sm fw-bold px-3 py-2 rounded-3"
                                    :style="tabHistorial === 'integrantes' ? 'background: #dc3545; color: #fff; border: 1px solid #dc3545;' : 'background: var(--bg-th); color: var(--text-muted); border: 1px solid var(--border);'"
                                    @click="tabHistorial = 'integrantes'"
                                >
                                    <i class="bi bi-person-lines-fill me-1"></i> Integrantes ({{ equipoHistorial.integrantes?.length || 0 }})
                                </button>
                            </div>

                            <!-- Contenido Pestaña 1: Puntos de Control -->
                            <div v-if="tabHistorial === 'puntos'">
                                <div v-if="!equipoHistorial.puntos_control || equipoHistorial.puntos_control.length === 0" class="text-center py-4 text-muted">
                                    <i class="bi bi-info-circle fs-3 d-block mb-2 text-danger"></i>
                                    No hay puntos de control registrados para este equipo.
                                </div>
                                <div v-else class="sc">
                                    <div class="table-responsive">
                                        <table class="ct">
                                            <thead>
                                                <tr>
                                                    <th>N°</th>
                                                    <th>Punto de Control</th>
                                                    <th>Fecha Límite</th>
                                                    <th>Estado</th>
                                                    <th>Observaciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(puc, idx) in equipoHistorial.puntos_control" :key="puc.puc_id">
                                                    <td><span class="bs bsec">{{ puc.puc_orden || idx + 1 }}</span></td>
                                                    <td>
                                                        <div class="fw-bold">{{ puc.puc_nombre }}</div>
                                                        <small class="text-muted" v-if="puc.puc_descripcion">{{ puc.puc_descripcion }}</small>
                                                    </td>
                                                    <td>
                                                        <span>{{ puc.puc_fecha_reprogramada || puc.puc_fecha_limite }}</span>
                                                        <span v-if="puc.puc_fecha_reprogramada" class="bs bw ms-1">Reprogramada</span>
                                                    </td>
                                                    <td>
                                                        <span v-if="puc.seguimientos && puc.seguimientos.length > 0 && puc.seguimientos[0].seq_cumplido" class="bs ba">
                                                            <i class="bi bi-check-circle me-1"></i>Cumplido
                                                        </span>
                                                        <span v-else-if="puc.seguimientos && puc.seguimientos.length > 0 && puc.seguimientos[0].seq_cumplido === false" class="bs bd">
                                                            <i class="bi bi-x-circle me-1"></i>No Cumplido
                                                        </span>
                                                        <span v-else class="bs bw">
                                                            <i class="bi bi-clock me-1"></i>Pendiente
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <small v-if="puc.seguimientos && puc.seguimientos.length > 0 && puc.seguimientos[0].seq_observaciones">
                                                            {{ puc.seguimientos[0].seq_observaciones }}
                                                        </small>
                                                        <small v-else class="text-muted">—</small>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenido Pestaña 2: Entregables -->
                            <div v-if="tabHistorial === 'entregables'">
                                <div v-if="!equipoHistorial.entregables || equipoHistorial.entregables.length === 0" class="text-center py-4 text-muted">
                                    <i class="bi bi-info-circle fs-3 d-block mb-2 text-danger"></i>
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
                                                    <th class="text-end pe-3">Acción</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="ent in equipoHistorial.entregables" :key="ent.entr_id">
                                                    <td>
                                                        <div class="fw-bold">{{ ent.tipo_entregable?.tet_nombre || 'Entregable' }}</div>
                                                        <small class="text-muted"><i class="bi bi-file-earmark me-1"></i>{{ ent.entr_nombre_archivo }}</small>
                                                    </td>
                                                    <td>
                                                        <span v-if="ent.punto_control" class="bs bsec">{{ ent.punto_control.puc_nombre }}</span>
                                                        <span v-else class="text-muted">—</span>
                                                    </td>
                                                    <td><span class="bs bsec">v{{ ent.entr_version || 1 }}</span></td>
                                                    <td>
                                                        <span v-if="ent.entr_aprobado === true" class="bs ba">Aprobado</span>
                                                        <span v-else-if="ent.entr_aprobado === false" class="bs bd">Rechazado</span>
                                                        <span v-else class="bs bw">En Revisión</span>
                                                    </td>
                                                    <td class="text-end pe-3">
                                                        <a v-if="ent.entr_ruta" :href="`/storage/${ent.entr_ruta}`" target="_blank" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-download me-1"></i>Descargar
                                                        </a>
                                                        <span v-else class="text-muted">—</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Contenido Pestaña 3: Socializaciones -->
                            <div v-if="tabHistorial === 'socializaciones'">
                                <div v-if="!equipoHistorial.socializaciones || equipoHistorial.socializaciones.length === 0" class="text-center py-4 text-muted">
                                    <i class="bi bi-info-circle fs-3 d-block mb-2 text-danger"></i>
                                    No hay socializaciones registradas para este equipo.
                                </div>
                                <div v-else class="row g-3">
                                    <div v-for="soc in equipoHistorial.socializaciones" :key="soc.soc_id" class="col-md-6">
                                        <div class="sc h-100 p-3" style="border-left: 3px solid #dc3545 !important;">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="fw-bold mb-0" style="color: var(--text-main);">{{ soc.tipo_socializacion?.tso_nombre || 'Socialización' }}</h6>
                                                <span v-if="soc.soc_apto === true" class="bs ba">Apto</span>
                                                <span v-else-if="soc.soc_apto === false" class="bs bd">No Apto</span>
                                                <span v-else class="bs bw">Pendiente</span>
                                            </div>
                                            <div class="small text-muted mb-2">
                                                <div><i class="bi bi-calendar-event me-1 text-danger"></i>{{ soc.soc_fecha }} a las {{ soc.soc_hora }}</div>
                                                <div><i class="bi bi-geo-alt me-1 text-danger"></i>{{ soc.soc_lugar }}</div>
                                            </div>
                                            <div v-if="soc.soc_observaciones" class="p-2 rounded small mb-2" style="background: var(--bg-th); border: 1px solid var(--border);">
                                                <strong>Observaciones:</strong> {{ soc.soc_observaciones }}
                                            </div>
                                            <div v-if="soc.jurados && soc.jurados.length > 0">
                                                <small class="fw-semibold text-muted d-block mb-1">Jurados:</small>
                                                <div class="d-flex flex-wrap gap-1">
                                                    <span v-for="j in soc.jurados" :key="j.jso_id" class="bs bsec">
                                                        {{ j.usuario?.usu_primer_nombre }} {{ j.usuario?.usu_primer_apellido }}
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
                                                <tr v-for="integ in equipoHistorial.integrantes" :key="integ.ein_id">
                                                    <td>{{ integ.usuario?.usu_cedula }}</td>
                                                    <td class="fw-bold">{{ integ.usuario?.usu_primer_nombre }} {{ integ.usuario?.usu_primer_apellido }}</td>
                                                    <td>
                                                        <span v-if="integ.ein_es_lider" class="bs bd">
                                                            <i class="bi bi-star-fill me-1 text-warning"></i>Líder
                                                        </span>
                                                        <span v-else class="bs bsec">Integrante</span>
                                                    </td>
                                                    <td>
                                                        <span v-if="integ.ein_status" class="bs ba">Activo</span>
                                                        <span v-else class="bs bsec">Retirado</span>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <div class="modal-footer" style="background: var(--bg-th); border-top: 1px solid var(--border);">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-4 fw-bold" data-bs-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Nueva Comunidad (Submodal con diseño Rojo, Negro y Blanco) -->
        <div class="modal fade" id="mModalNuevaComunidad" tabindex="-1" aria-labelledby="mModalNuevaComunidadLabel" aria-hidden="true" style="z-index: 1070;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden; background: var(--bg-card);">
                    <div class="mh" style="background: #1a1d23; border-bottom: 2px solid #dc3545; color: #fff; padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                        <h5 class="mb-0 fw-bold fs-6 text-white d-flex align-items-center">
                            <i class="bi bi-geo-alt-fill text-danger me-2"></i>Registrar Nueva Comunidad
                        </h5>
                        <button type="button" class="btn btn-sm text-white p-0 fs-5" @click="cerrarModalNuevaComunidad">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <form @submit.prevent="guardarNuevaComunidad">
                        <div class="modal-body p-4" style="color: var(--text-main);">
                            <div class="alert py-2 px-3 small mb-3 rounded" style="background: rgba(220, 53, 69, 0.08); border-left: 3px solid #dc3545; color: var(--text-main);">
                                <i class="bi bi-info-circle-fill me-1 text-danger"></i>
                                Registra la comunidad para asociarla directamente al proyecto.
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Nombre de la Comunidad <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': erroresNuevaComunidad.com_nombre }"
                                    v-model="nuevaComunidad.com_nombre"
                                    placeholder="Ej: Comunidad Brisas del Este"
                                />
                                <div v-if="erroresNuevaComunidad.com_nombre" class="invalid-feedback">
                                    {{ erroresNuevaComunidad.com_nombre }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold">Tipo de Organización <span class="text-danger">*</span></label>
                                <select
                                    class="form-select"
                                    :class="{ 'is-invalid': erroresNuevaComunidad.com_id_tor }"
                                    v-model="nuevaComunidad.com_id_tor"
                                >
                                    <option value="">Seleccione tipo de organización...</option>
                                    <option v-for="t in tiposOrganizacion" :key="t.tor_id" :value="t.tor_id">
                                        {{ t.tor_nombre }}
                                    </option>
                                </select>
                                <div v-if="erroresNuevaComunidad.com_id_tor" class="invalid-feedback">
                                    {{ erroresNuevaComunidad.com_id_tor }}
                                </div>
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold">Ubicación / Dirección <span class="text-muted fw-normal">(opcional)</span></label>
                                <textarea
                                    class="form-control"
                                    v-model="nuevaComunidad.com_ubicacion"
                                    rows="2"
                                    placeholder="Ej: Av. Principal c/calle 5, Sector Centro..."
                                ></textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-3" style="background: var(--bg-th); border-top: 1px solid var(--border) !important;">
                            <button type="button" class="btn btn-secondary btn-sm px-3" @click="cerrarModalNuevaComunidad">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold" :disabled="guardandoComunidad">
                                <span v-if="guardandoComunidad" class="spinner-border spinner-border-sm me-1"></span>
                                <i v-else class="bi bi-check2 me-1"></i>Guardar Comunidad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Nuevo Tipo de Proyecto (Submodal con diseño Rojo, Negro y Blanco) -->
        <div class="modal fade" id="mModalNuevoTipo" tabindex="-1" aria-labelledby="mModalNuevoTipoLabel" aria-hidden="true" style="z-index: 1070;">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden; background: var(--bg-card);">
                    <div class="mh" style="background: #1a1d23; border-bottom: 2px solid #dc3545; color: #fff; padding: 1rem 1.25rem; display: flex; justify-content: space-between; align-items: center;">
                        <h5 class="mb-0 fw-bold fs-6 text-white d-flex align-items-center">
                            <i class="bi bi-tag-fill text-danger me-2"></i>Registrar Nuevo Tipo de Proyecto
                        </h5>
                        <button type="button" class="btn btn-sm text-white p-0 fs-5" @click="cerrarModalNuevoTipo">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                    <form @submit.prevent="guardarNuevoTipo">
                        <div class="modal-body p-4" style="color: var(--text-main);">
                            <div class="alert py-2 px-3 small mb-3 rounded" style="background: rgba(220, 53, 69, 0.08); border-left: 3px solid #dc3545; color: var(--text-main);">
                                <i class="bi bi-info-circle-fill me-1 text-danger"></i>
                                Ingresa el nombre del tipo de proyecto. Quedará seleccionado automáticamente en el formulario.
                            </div>

                            <div class="mb-2">
                                <label class="form-label small fw-bold">Nombre del Tipo de Proyecto <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    class="form-control"
                                    :class="{ 'is-invalid': erroresNuevoTipo.tpr_nombre }"
                                    v-model="nuevoTipo.tpr_nombre"
                                    placeholder="Ej: Desarrollo de Software, Redes, Capacitación..."
                                />
                                <div v-if="erroresNuevoTipo.tpr_nombre" class="invalid-feedback">
                                    {{ erroresNuevoTipo.tpr_nombre }}
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-3" style="background: var(--bg-th); border-top: 1px solid var(--border) !important;">
                            <button type="button" class="btn btn-secondary btn-sm px-3" @click="cerrarModalNuevoTipo">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-danger btn-sm px-4 fw-bold" :disabled="guardandoTipo">
                                <span v-if="guardandoTipo" class="spinner-border spinner-border-sm me-1"></span>
                                <i v-else class="bi bi-check2 me-1"></i>Guardar Tipo
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

// Historial Académico
const equipoHistorial = ref(null);
const cargandoHistorialAcademico = ref(false);
const tabHistorial = ref("puntos");

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
});
const formEditar = ref({
    equ_titulo: "",
    pco_id_com: "",
    pco_id_tpr: "",
    pco_id_mpr: "",
    pco_id_pco_previo: "",
    pco_resumen: "",
    pco_fecha_limite: "",
});

const formProyecto = ref({
    pco_id_com: "",
    pco_id_tpr: "",
    pco_id_mpr: "",
    pco_id_pco_previo: "",
    pco_fecha_limite: "",
    pco_resumen: "",
    beneficiarios: [],
});

const integranteATrasladar = ref(null);
const equipoDestinoId = ref("");
const cargandoTraslado = ref(false);

let mCrear = null;
let mEditar = null;
let mInts = null;
let mDisolver = null;
let mTrasladar = null;

watch([busqueda, verDisueltos], () => {
    pagina.value = 1;
});

const listaActual = computed(() =>
    verDisueltos.value ? props.equiposDisueltos : props.equipos,
);
const equiposFiltrados = computed(() => {
    if (!busqueda.value.trim()) return listaActual.value;
    const q = busqueda.value.toLowerCase();
    return listaActual.value.filter(
        (e) =>
            e.equ_codigo?.toLowerCase().includes(q) ||
            e.equ_titulo?.toLowerCase().includes(q),
    );
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
    const m = String(codigo).match(/^[A-Z]+-[A-Z]+(\d)(\d)-([^-]+)-(\d+)\/(\d+)$/i);
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
    const todos = [...(props.equipos || []), ...(props.equiposDisueltos || [])];
    const equiposSec = todos.filter((e) => String(e.equ_id_sec) === String(secId));
    let maxN = 0;
    equiposSec.forEach((e) => {
        const parsed = parsearCodigoEquipo(e.equ_codigo);
        if (parsed?.numero) {
            maxN = Math.max(maxN, parsed.numero);
        } else {
            const mSlash = e.equ_codigo ? e.equ_codigo.match(/-(\d+)\/(\d+)$/) : null;
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
    const equiposSec = todos.filter((e) => String(e.equ_id_sec) === String(secId));
    for (const e of equiposSec) {
        const parsed = parsearCodigoEquipo(e.equ_codigo);
        if (parsed?.totalSeccion) return parsed.totalSeccion;
        const mSlash = e.equ_codigo ? e.equ_codigo.match(/-(\d+)\/(\d+)$/) : null;
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

    const sec = props.secciones?.find((s) => String(s.sec_id) === String(secId));
    const traId = sec?.sec_id_tra || sec?.trayecto?.tra_id;
    const todos = [...(props.equipos || []), ...(props.equiposDisueltos || [])];

    // Equipos existentes de la sección
    const equiposSec = todos.filter((e) => String(e.equ_id_sec) === String(secId));

    // Equipos existentes en el mismo trayecto (cualquier sección de este trayecto)
    const equiposTra = todos.filter((e) => {
        const s = props.secciones?.find((item) => String(item.sec_id) === String(e.equ_id_sec));
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
                if (parsed.semestre && !semestreHeredado) semestreHeredado = parsed.semestre;
                if (parsed.totalSeccion && !totalSecHeredado) totalSecHeredado = parsed.totalSeccion;
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

    // Si ya existe al menos un equipo en la sección o en el trayecto: AUTO-COMPLETAR
    if (equiposSec.length > 0 || equiposTra.length > 0) {
        esAutoCompletado.value = true;
        formNuevo.value.equ_semestre = semestreHeredado || (sec?.trayecto?.tra_numero || 1);
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
            if (errores.value.equ_numero_grupo?.includes("supera el total") || errores.value.equ_numero_grupo?.includes("no puede sobrepasar")) {
                delete errores.value.equ_numero_grupo;
            }
        }
    } else {
        // Es el PRIMER equipo de este trayecto y sección: pedir datos iniciales
        totalOriginalSeccion.value = null;
        esAutoCompletado.value = false;
        editarParametrosManual.value = true;
        formNuevo.value.equ_numero_grupo = "1";
        formNuevo.value.equ_semestre = sec?.trayecto?.tra_numero || 1;
        formNuevo.value.equ_total_seccion = 7;
    }

    // Limpiar integrantes que no pertenezcan a la sección elegida
    formNuevo.value.integrantes.forEach((item) => {
        if (!item.usu_id) return;
        const est = props.estudiantes?.find((e) => String(e.usu_id) === String(item.usu_id));
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
        Number(formNuevo.value.equ_total_seccion) !== Number(totalOriginalSeccion.value)
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
                errores.value.equ_numero_grupo?.includes("no puede sobrepasar") ||
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
        beneficiarios: [],
    };
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
    };
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
    mDisolver = mDisolver || new Modal(document.getElementById("mDisolver"));
    mDisolver.show();
};

const validarPaso1 = () => {
    errores.value = {};
    if (!formNuevo.value.equ_id_sec) {
        errores.value.equ_id_sec = "La sección es obligatoria.";
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
    loading.value = true;

    // Preparar proyecto limpio solo si se eligió comunidad y tipo
    let proyectoLimpio = null;
    if (formProyecto.value.pco_id_com) {
        proyectoLimpio = {
            pco_id_com: formProyecto.value.pco_id_com,
            pco_id_tpr: formProyecto.value.pco_id_tpr || null,
            pco_id_mpr: formProyecto.value.pco_id_mpr || null,
            pco_id_pco_previo: formProyecto.value.pco_id_pco_previo || null,
            pco_fecha_limite: formProyecto.value.pco_fecha_limite || null,
            pco_resumen: formProyecto.value.pco_resumen || "",
            beneficiarios: (formProyecto.value.beneficiarios || [])
                .filter((b) => b.tbe_id && Number(b.cantidad) > 0)
                .map((b) => ({ tbe_id: b.tbe_id, cantidad: Number(b.cantidad) })),
        };
    }

    const payload = {
        equ_id_sec: formNuevo.value.equ_id_sec,
        equ_numero_grupo: formNuevo.value.equ_numero_grupo,
        equ_semestre: formNuevo.value.equ_semestre || 1,
        equ_total_seccion: formNuevo.value.equ_total_seccion || formNuevo.value.equ_numero_grupo,
        equ_codigo: codigoPreview.value !== "—" ? codigoPreview.value : "",
        equ_titulo: formNuevo.value.equ_titulo,
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
                k.startsWith("proyecto"),
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
    intentoDisolver.value = true;
    if (!motivoDisolucion.value.trim()) return;
    router.post(
        `${base.value}/equipos/${equipoADisolver.value.equ_id}/disolver`,
        { motivo: motivoDisolucion.value },
        { onSuccess: () => mDisolver?.hide() },
    );
};
const reactivar = (equ) => {
    if (!confirm(`¿Reactivar el equipo ${equ.equ_codigo}?`)) return;
    router.post(`${base.value}/equipos/${equ.equ_id}/reactivar`);
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
const esRutaMaestra = typeof window !== "undefined" && (
    window.location.pathname.includes("/coordinador/maestras") ||
    window.location.pathname.includes("/coordinador/tipos-proyecto")
);
const menuMaestras = ref(esRutaMaestra || localStorage.getItem("coord-menu-maestras") === "true");
const menuAcademico = ref(localStorage.getItem("coord-menu-academico") === "true");
const menuSistema = ref(localStorage.getItem("coord-menu-sistema") === "true");

const toggleMenuMaestras = () => {
    menuMaestras.value = !menuMaestras.value;
    localStorage.setItem("coord-menu-maestras", menuMaestras.value ? "true" : "false");
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
        modalSubComunidad = Modal.getInstance(el) || new Modal(el, { backdrop: "static" });
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
        erroresNuevaComunidad.value.com_id_tor = "El tipo de organización es obligatorio.";
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
                Object.entries(error.response.data.errors).map(([k, v]) => [k, v[0]])
            );
        } else if (error.response?.data?.message) {
            erroresNuevaComunidad.value.com_nombre = error.response.data.message;
        } else {
            erroresNuevaComunidad.value.com_nombre = "Error al registrar la comunidad.";
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
        modalSubTipo = Modal.getInstance(el) || new Modal(el, { backdrop: "static" });
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
                Object.entries(error.response.data.errors).map(([k, v]) => [k, v[0]])
            );
        } else if (error.response?.data?.message) {
            erroresNuevoTipo.value.tpr_nombre = error.response.data.message;
        } else {
            erroresNuevoTipo.value.tpr_nombre = "Error al registrar el tipo de proyecto.";
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
                alert(Object.values(err)[0] || "Error al trasladar integrante.");
            },
        },
    );
};

const integrantesAgregados = computed(
    () => formNuevo.value.integrantes.filter((i) => i.usu_id).length,
);
</script>
<style>
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
