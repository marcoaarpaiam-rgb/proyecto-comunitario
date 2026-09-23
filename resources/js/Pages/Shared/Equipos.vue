<template>
    <div
        :data-theme="tema"
        style="
            min-height: 100vh;
            background: var(--bg-body);
            color: var(--text-main);
            font-family: &quot;Segoe UI&quot;, system-ui, sans-serif;
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
                    <div class="sb-sec">Principal</div>
                    <a href="/coordinador" class="sb-lnk"
                        ><i class="bi bi-speedometer2"></i> Dashboard</a
                    >
                    <a href="/coordinador/secciones" class="sb-lnk"
                        ><i class="bi bi-collection"></i> Secciones</a
                    >
                    <a href="/coordinador/comunidades" class="sb-lnk"
                        ><i class="bi bi-building"></i> Comunidades</a
                    >
                    <a href="/coordinador/equipos" class="sb-lnk active"
                        ><i class="bi bi-people"></i> Equipos</a
                    >
                    <a href="/coordinador/proyectos" class="sb-lnk"
                        ><i class="bi bi-folder"></i> Proyectos</a
                    >
                    <div class="sb-sec">Tablas Maestras</div>
                    <a href="/coordinador/tipos-proyecto" class="sb-lnk"
                        ><i class="bi bi-tag"></i> Tipos de Proyecto</a
                    >
                    <a href="/coordinador/maestras/modalidades" class="sb-lnk"
                        ><i class="bi bi-layers"></i> Modalidades</a
                    >
                    <a href="/coordinador/maestras/tipos-jurado" class="sb-lnk"
                        ><i class="bi bi-person-badge"></i> Tipos de Jurado</a
                    >
                    <a
                        href="/coordinador/maestras/tipos-beneficiario"
                        class="sb-lnk"
                        ><i class="bi bi-people"></i> Tipos Beneficiario</a
                    >
                    <a
                        href="/coordinador/maestras/tipos-entregable"
                        class="sb-lnk"
                        ><i class="bi bi-file-earmark"></i> Tipos Entregable</a
                    >
                    <a
                        href="/coordinador/maestras/tipos-evento-equipo"
                        class="sb-lnk"
                        ><i class="bi bi-calendar-event"></i> Eventos Equipo</a
                    >
                    <a href="/coordinador/maestras/turnos" class="sb-lnk"
                        ><i class="bi bi-clock"></i> Turnos</a
                    >
                    <a
                        href="/coordinador/maestras/estados-proyecto"
                        class="sb-lnk"
                        ><i class="bi bi-award"></i> Estados Proyecto</a
                    >
                    <div class="sb-sec">Académico</div>
                    <a href="/coordinador/socializaciones" class="sb-lnk"
                        ><i class="bi bi-mic"></i> Socializaciones</a
                    >
                    <a href="/coordinador/resultados" class="sb-lnk"
                        ><i class="bi bi-trophy"></i> Resultados</a
                    >
                    <div class="sb-sec">Sistema</div>
                    <a href="/coordinador/reportes" class="sb-lnk"
                        ><i class="bi bi-bar-chart"></i> Reportes</a
                    >
                    <a href="/coordinador/cartas" class="sb-lnk"
                        ><i class="bi bi-file-earmark-text"></i> Cartas</a
                    >
                    <a href="/coordinador/bitacora" class="sb-lnk"
                        ><i class="bi bi-journal-text"></i> Bitácora</a
                    >
                    <a href="/coordinador/configuracion" class="sb-lnk"
                        ><i class="bi bi-gear"></i> Configuración</a
                    >
                </template>
                <!-- Profesor -->
                <template v-else>
                    <div class="sb-sec">Principal</div>
                    <a href="/profesor" class="sb-lnk"
                        ><i class="bi bi-speedometer2"></i> Dashboard</a
                    >
                    <a href="/profesor/equipos" class="sb-lnk active"
                        ><i class="bi bi-people"></i> Mis Equipos</a
                    >
                    <div class="sb-sec">Gestión</div>
                    <a href="/profesor/puntos-control" class="sb-lnk"
                        ><i class="bi bi-flag"></i> Puntos de Control</a
                    >
                    <a href="/profesor/socializaciones" class="sb-lnk"
                        ><i class="bi bi-mic"></i> Socializaciones</a
                    >
                    <a href="/profesor/entregables" class="sb-lnk"
                        ><i class="bi bi-file-earmark-check"></i> Entregables</a
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
                                            >{{ equ.integrantes?.length || 0 }}
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
                                    <div class="col-md-4">
                                        <label class="form-label fw-semibold"
                                            >N° de Grupo
                                            <span class="text-danger"
                                                >*</span
                                            ></label
                                        >
                                        <input
                                            v-model="formNuevo.equ_numero_grupo"
                                            type="number"
                                            class="form-control"
                                            :class="{
                                                'is-invalid':
                                                    errores.equ_numero_grupo,
                                            }"
                                            min="1"
                                            placeholder="Ej: 1"
                                        />
                                        <div
                                            v-if="errores.equ_numero_grupo"
                                            class="invalid-feedback"
                                        >
                                            {{ errores.equ_numero_grupo }}
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <label class="form-label fw-semibold"
                                            >Código generado (automático)</label
                                        >
                                        <input
                                            type="text"
                                            class="form-control fw-bold"
                                            :value="codigoPreview"
                                            readonly
                                            style="
                                                background: var(
                                                    --bg-th
                                                ) !important;
                                                color: var(--rojo) !important;
                                            "
                                        />
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
                                <div
                                    class="alert py-2 small mb-2"
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
                                    >El líder recibirá acceso con su cédula como
                                    usuario y contraseña.
                                </div>
                                <div
                                    v-for="(item, idx) in formNuevo.integrantes"
                                    :key="idx"
                                    class="row g-2 mb-2 align-items-center"
                                >
                                    <div class="col-7">
                                        <select
                                            v-model="item.usu_id"
                                            class="form-select form-select-sm"
                                        >
                                            <option value="">
                                                Seleccionar estudiante...
                                            </option>
                                            <option
                                                v-for="est in estudiantesDisponibles(
                                                    item.usu_id,
                                                )"
                                                :key="est.usu_id"
                                                :value="est.usu_id"
                                            >
                                                {{ est.usu_primer_nombre }}
                                                {{ est.usu_primer_apellido }}
                                                ({{ est.usu_cedula }})
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-check ms-2">
                                            <input
                                                type="radio"
                                                class="form-check-input"
                                                name="lider"
                                                :value="idx"
                                                v-model="liderIndex"
                                                @change="actualizarLider"
                                            />
                                            <label
                                                class="form-check-label small fw-bold"
                                                style="color: var(--rojo)"
                                                >Líder</label
                                            >
                                        </div>
                                    </div>
                                    <div class="col-2">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-danger w-100"
                                            @click="quitarIntegrante(idx)"
                                            :disabled="
                                                formNuevo.integrantes.length ===
                                                1
                                            "
                                        >
                                            <i class="bi bi-trash"></i>
                                        </button>
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
                                        <select
                                            v-model="formProyecto.pco_id_com"
                                            class="form-select"
                                        >
                                            <option value="">
                                                Seleccionar comunidad...
                                            </option>
                                            <option
                                                v-for="com in comunidades"
                                                :key="com.com_id"
                                                :value="com.com_id"
                                            >
                                                {{ com.com_nombre }} —
                                                {{ com.com_ubicacion }}
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                            >Tipo de Proyecto</label
                                        >
                                        <select
                                            v-model="formProyecto.pco_id_tpr"
                                            class="form-select"
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
                                    <div class="col-md-6">
                                        <label class="form-label fw-semibold"
                                            >Fecha Límite</label
                                        >
                                        <input
                                            v-model="
                                                formProyecto.pco_fecha_limite
                                            "
                                            type="date"
                                            class="form-control"
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
                                                <input
                                                    v-model="ben.cantidad"
                                                    type="number"
                                                    class="form-control form-control-sm"
                                                    placeholder="Cantidad"
                                                    min="1"
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
                            <ul class="list-group list-group-flush">
                                <li
                                    v-for="int in equipoSeleccionado?.integrantes"
                                    :key="int.ein_id"
                                    class="list-group-item d-flex align-items-center gap-3 px-1 py-2"
                                    style="
                                        background: transparent;
                                        border-color: var(--border);
                                    "
                                    :class="!int.ein_status ? 'opacity-50' : ''"
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
                                            v-if="
                                                int.ein_status &&
                                                !int.ein_es_lider
                                            "
                                            class="btn btn-sm btn-outline-danger"
                                            @click="removerIntegrante(int)"
                                            title="Remover"
                                        >
                                            <i class="bi bi-person-dash"></i>
                                        </button>
                                    </div>
                                </li>
                            </ul>
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
    </div>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import { Modal } from "bootstrap";

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
const logout = () => router.post("/logout");
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
    equ_titulo: "",
    integrantes: [{ usu_id: "", es_lider: true }],
});
const formEditar = ref({ equ_titulo: "" });
const formProyecto = ref({
    pco_id_com: "",
    pco_id_tpr: "",
    pco_id_mpr: "",
    pco_fecha_limite: "",
    pco_resumen: "",
    beneficiarios: [],
});

let mCrear = null;
let mEditar = null;
let mInts = null;
let mDisolver = null;

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
const codigoPreview = computed(() => {
    const sec = seccionSeleccionada.value;
    if (!sec || !formNuevo.value.equ_numero_grupo) return "—";
    const pnf = sec.trayecto?.pnf?.pnf_siglas || "PNFI";
    const tra = sec.trayecto?.tra_numero || "";
    return `${pnf.toUpperCase()}-${sec.sec_codigo}-G${formNuevo.value.equ_numero_grupo}-T${tra}`;
});
const onSeccionChange = () => {
    formNuevo.value.equ_numero_grupo = "";
};
const estudiantesDisponibles = (idActual) => {
    const sel = formNuevo.value.integrantes
        .map((i) => i.usu_id)
        .filter((id) => id && id !== idActual);
    return props.estudiantes?.filter((e) => !sel.includes(e.usu_id)) || [];
};
const actualizarLider = () => {
    formNuevo.value.integrantes.forEach((item, idx) => {
        item.es_lider = idx === liderIndex.value;
    });
};
const agregarIntegrante = () => {
    if (formNuevo.value.integrantes.length >= 6) return;
    formNuevo.value.integrantes.push({ usu_id: "", es_lider: false });
};
const quitarIntegrante = (idx) => {
    formNuevo.value.integrantes.splice(idx, 1);
    if (liderIndex.value >= formNuevo.value.integrantes.length) {
        liderIndex.value = 0;
    }
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
    formNuevo.value = {
        equ_id_sec: "",
        equ_numero_grupo: "",
        equ_titulo: "",
        integrantes: [{ usu_id: "", es_lider: true }],
    };
    formProyecto.value = {
        pco_id_com: "",
        pco_id_tpr: "",
        pco_id_mpr: "",
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
    formEditar.value = { equ_titulo: equ.equ_titulo };
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

const irPaso2 = () => {
    if (!formNuevo.value.equ_id_sec) {
        errores.value.equ_id_sec = "La sección es obligatoria.";
        return;
    }
    if (!formNuevo.value.equ_numero_grupo) {
        errores.value.equ_numero_grupo = "El número de grupo es obligatorio.";
        return;
    }
    if (!formNuevo.value.equ_titulo) {
        errores.value.equ_titulo = "El título es obligatorio.";
        return;
    }
    if (formNuevo.value.integrantes.some((i) => !i.usu_id)) {
        errores.value.integrantes =
            "Todos los integrantes deben ser seleccionados.";
        return;
    }
    errores.value = {};
    paso.value = 2;
};

const guardarNuevo = () => {
    actualizarLider();
    loading.value = true;
    const payload = {
        ...formNuevo.value,
        proyecto: formProyecto.value.pco_id_com ? formProyecto.value : null,
    };
    router.post(`${base.value}/equipos`, payload, {
        onError: (e) => {
            errores.value = e;
            loading.value = false;
            paso.value = 1;
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
const cargarHistorial = async () => {
    tabActivo.value = "hist";
    if (!equipoSeleccionado.value) return;
    cargandoHistorial.value = true;
    const res = await fetch(
        `${base.value}/equipos/${equipoSeleccionado.value.equ_id}/historial`,
    );
    historialEquipo.value = await res.json();
    cargandoHistorial.value = false;
};

const colorEvt = (n) => {
    const m = {
        Disuelto: "background:#f8d7da;color:#842029",
        "Líder Cambiado": "background:#fff3cd;color:#664d03",
        "Integrante Removido": "background:#ffe5d0;color:#984c0c",
        Reactivado: "background:#d1e7dd;color:#0f5132",
    };
    return m[n] || "background:#e2e3e5;color:#41464b";
};
const iconEvt = (n) => {
    if (n === "Disuelto") return "bi-x-circle";
    if (n === "Líder Cambiado") return "bi-arrow-repeat";
    if (n === "Integrante Removido") return "bi-person-dash";
    if (n === "Reactivado") return "bi-arrow-counterclockwise";
    return "bi-circle";
};
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
