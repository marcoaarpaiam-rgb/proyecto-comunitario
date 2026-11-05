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
                    <div class="sb-role">Coordinador PNF</div>
                </div>
            </div>
            <div class="sb-nav">
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
                <Link href="/coordinador/proyectos" class="sb-lnk">
                    <i class="bi bi-folder"></i> Proyectos
                </Link>
                <Link href="/coordinador/socializaciones" class="sb-lnk">
                    <i class="bi bi-mic"></i> Socializaciones
                </Link>
                <Link href="/coordinador/resultados" class="sb-lnk">
                    <i class="bi bi-trophy"></i> Resultados
                </Link>

                <!-- Tablas Maestras — desplegable -->
                <div
                    class="sb-sec sb-toggle"
                    @click="menuMaestras = !menuMaestras"
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
                            menuMaestras ? 'bi-chevron-up' : 'bi-chevron-down'
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
                    <Link href="/coordinador/maestras/turnos" class="sb-lnk">
                        <i class="bi bi-clock"></i> Turnos
                    </Link>
                    <Link
                        href="/coordinador/maestras/estados-proyecto"
                        class="sb-lnk"
                    >
                        <i class="bi bi-award"></i> Estados Proyecto
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
                            menuSistema ? 'bi-chevron-up' : 'bi-chevron-down'
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
                            class="bi bi-bar-chart me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Reportes
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a
                        href="/coordinador/reportes/exportar-general"
                        target="_blank"
                        class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-bold"
                        ><i class="bi bi-file-earmark-pdf me-1"></i>Exportar
                        General</a
                    >
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
                <div style="margin-bottom: 1.25rem">
                    <h4
                        style="
                            font-weight: 800;
                            font-size: 1.25rem;
                            color: var(--text-main);
                            margin: 0;
                        "
                    >
                        Reportes del Sistema
                    </h4>
                    <p
                        style="
                            color: var(--text-muted);
                            font-size: 0.85rem;
                            margin-top: 2px;
                        "
                    >
                        Estadísticas generales y solvencia estudiantil
                    </p>
                </div>

                <!-- Stats generales -->
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon si-r">
                                <i class="bi bi-people-fill"></i>
                            </div>
                            <div>
                                <div class="stat-num">
                                    {{ resumenGeneral.total_equipos }}
                                </div>
                                <div class="stat-label">Total Equipos</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon si-g">
                                <i class="bi bi-trophy-fill"></i>
                            </div>
                            <div>
                                <div class="stat-num">
                                    {{ resumenGeneral.aprobados }}
                                </div>
                                <div class="stat-label">Aprobados</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon si-y">
                                <i class="bi bi-hourglass-split"></i>
                            </div>
                            <div>
                                <div class="stat-num">
                                    {{ resumenGeneral.aplazados }}
                                </div>
                                <div class="stat-label">Aplazados</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="stat-card">
                            <div class="stat-icon si-s">
                                <i class="bi bi-dash-circle"></i>
                            </div>
                            <div>
                                <div class="stat-num">
                                    {{ resumenGeneral.sin_resultado }}
                                </div>
                                <div class="stat-label">Sin Resultado</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reporte por sección -->
                <div class="sc mb-4">
                    <div class="sh">
                        <h6>
                            <i class="bi bi-collection me-2 text-danger"></i
                            >Equipos por Sección
                        </h6>
                    </div>
                    <div class="table-responsive">
                        <table class="ct">
                            <thead>
                                <tr>
                                    <th class="ps-4">Sección</th>
                                    <th>Trayecto</th>
                                    <th>Turno</th>
                                    <th class="text-center">Total</th>
                                    <th class="text-center">Aprobados</th>
                                    <th class="text-center">Aplazados</th>
                                    <th class="text-center">Sin Resultado</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!reportePorSeccion?.length">
                                    <td
                                        colspan="7"
                                        class="text-center py-5"
                                        style="color: var(--text-muted)"
                                    >
                                        Sin datos
                                    </td>
                                </tr>
                                <tr
                                    v-for="rep in reportePorSeccion"
                                    :key="rep.seccion"
                                >
                                    <td class="ps-4 fw-bold">
                                        {{ rep.seccion }}
                                    </td>
                                    <td style="font-size: 0.88rem">
                                        {{ rep.trayecto }}
                                    </td>
                                    <td
                                        style="
                                            font-size: 0.85rem;
                                            color: var(--text-muted);
                                        "
                                    >
                                        {{ rep.turno }}
                                    </td>
                                    <td class="text-center fw-bold">
                                        {{ rep.total }}
                                    </td>
                                    <td class="text-center">
                                        <span class="bs ba">{{
                                            rep.aprobados
                                        }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="bs bw">{{
                                            rep.aplazados
                                        }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="bs bsec">{{
                                            rep.sin_resultado
                                        }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pestañas de reportes -->
                <div class="d-flex gap-2 flex-wrap mb-4">
                    <button
                        @click="tabActiva = 'general'"
                        :class="
                            tabActiva === 'general'
                                ? 'btn-danger'
                                : 'btn-outline-danger'
                        "
                        class="btn btn-sm fw-bold rounded-pill"
                    >
                        <i class="bi bi-bar-chart me-1"></i> General
                    </button>
                    <button
                        @click="cargarReporteComunidad"
                        :class="
                            tabActiva === 'comunidad'
                                ? 'btn-danger'
                                : 'btn-outline-danger'
                        "
                        class="btn btn-sm fw-bold rounded-pill"
                    >
                        <i class="bi bi-building me-1"></i> Por Comunidad
                    </button>
                    <button
                        @click="tabActiva = 'filtro'"
                        :class="
                            tabActiva === 'filtro'
                                ? 'btn-danger'
                                : 'btn-outline-danger'
                        "
                        class="btn btn-sm fw-bold rounded-pill"
                    >
                        <i class="bi bi-funnel me-1"></i> Filtro Avanzado
                    </button>
                    <button
                        @click="cargarEntregables"
                        :class="
                            tabActiva === 'entregables'
                                ? 'btn-danger'
                                : 'btn-outline-danger'
                        "
                        class="btn btn-sm fw-bold rounded-pill"
                    >
                        <i class="bi bi-file-earmark me-1"></i> Entregables
                    </button>
                    <button
                        @click="cargarPuntos"
                        :class="
                            tabActiva === 'puntos'
                                ? 'btn-danger'
                                : 'btn-outline-danger'
                        "
                        class="btn btn-sm fw-bold rounded-pill"
                    >
                        <i class="bi bi-check2-square me-1"></i> Puntos de
                        Control
                    </button>
                </div>

                <!-- ── FILTRO AVANZADO ────────────────────────────────────── -->
                <div v-if="tabActiva === 'filtro'" class="sc mb-4">
                    <div class="sh">
                        <h6>
                            <i class="bi bi-funnel me-2 text-danger"></i>Filtro
                            Avanzado de Equipos
                        </h6>
                    </div>
                    <div class="p-3">
                        <div class="row g-2 mb-3">
                            <div class="col-md-4 col-6">
                                <label class="form-label small fw-bold"
                                    >Sección</label
                                >
                                <select
                                    v-model="filtros.sec_id"
                                    class="form-select form-select-sm"
                                >
                                    <option value="">Todas</option>
                                    <option
                                        v-for="s in secciones"
                                        :key="s.sec_id"
                                        :value="s.sec_id"
                                    >
                                        {{ s.sec_codigo }} —
                                        {{ s.trayecto?.tra_nombre }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 col-6">
                                <label class="form-label small fw-bold"
                                    >Trayecto</label
                                >
                                <select
                                    v-model="filtros.tra_id"
                                    class="form-select form-select-sm"
                                >
                                    <option value="">Todos</option>
                                    <option
                                        v-for="t in trayectos"
                                        :key="t.tra_id"
                                        :value="t.tra_id"
                                    >
                                        {{ t.tra_nombre }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 col-6">
                                <label class="form-label small fw-bold"
                                    >Comunidad</label
                                >
                                <select
                                    v-model="filtros.com_id"
                                    class="form-select form-select-sm"
                                >
                                    <option value="">Todas</option>
                                    <option
                                        v-for="c in comunidades"
                                        :key="c.com_id"
                                        :value="c.com_id"
                                    >
                                        {{ c.com_nombre }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 col-6">
                                <label class="form-label small fw-bold"
                                    >Tipo de Proyecto</label
                                >
                                <select
                                    v-model="filtros.tpr_id"
                                    class="form-select form-select-sm"
                                >
                                    <option value="">Todos</option>
                                    <option
                                        v-for="t in tiposProyecto"
                                        :key="t.tpr_id"
                                        :value="t.tpr_id"
                                    >
                                        {{ t.tpr_nombre }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 col-6">
                                <label class="form-label small fw-bold"
                                    >Estado</label
                                >
                                <select
                                    v-model="filtros.estado"
                                    class="form-select form-select-sm"
                                >
                                    <option value="">Todos</option>
                                    <option value="Aprobado">Aprobado</option>
                                    <option value="Aplazado">Aplazado</option>
                                    <option value="No Presentado">
                                        No Presentado
                                    </option>
                                    <option value="sin_resultado">
                                        Sin Resultado
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4 col-6">
                                <label class="form-label small fw-bold"
                                    >Profesor</label
                                >
                                <select
                                    v-model="filtros.prof_id"
                                    class="form-select form-select-sm"
                                >
                                    <option value="">Todos</option>
                                    <option
                                        v-for="p in profesores"
                                        :key="p.usu_id"
                                        :value="p.usu_id"
                                    >
                                        {{ p.usu_primer_nombre }}
                                        {{ p.usu_primer_apellido }}
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="form-label small fw-bold"
                                    >Fecha desde</label
                                >
                                <input
                                    type="date"
                                    v-model="filtros.fecha_desde"
                                    class="form-control form-control-sm"
                                />
                            </div>
                            <div class="col-md-3 col-6">
                                <label class="form-label small fw-bold"
                                    >Fecha hasta</label
                                >
                                <input
                                    type="date"
                                    v-model="filtros.fecha_hasta"
                                    class="form-control form-control-sm"
                                />
                            </div>
                            <div class="col-md-3 col-6 d-flex align-items-end">
                                <div class="form-check">
                                    <input
                                        type="checkbox"
                                        class="form-check-input"
                                        id="chkVencidos"
                                        v-model="filtros.vencidos"
                                    />
                                    <label
                                        class="form-check-label small fw-bold"
                                        for="chkVencidos"
                                    >
                                        Solo vencidos
                                    </label>
                                </div>
                            </div>
                            <div
                                class="col-md-3 col-6 d-flex align-items-end gap-2"
                            >
                                <button
                                    @click="aplicarFiltro"
                                    :disabled="cargandoFiltro"
                                    class="btn btn-sm btn-danger fw-bold flex-fill"
                                >
                                    <i class="bi bi-search me-1"></i>
                                    {{
                                        cargandoFiltro
                                            ? "Buscando..."
                                            : "Buscar"
                                    }}
                                </button>
                                <button
                                    @click="limpiarFiltros"
                                    class="btn btn-sm btn-outline-secondary fw-bold"
                                >
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Resultados del filtro -->
                        <div v-if="resultadosFiltro.length > 0">
                            <p
                                class="small fw-bold mb-2"
                                style="color: var(--text-muted)"
                            >
                                {{ totalFiltro }} equipo(s) encontrado(s)
                            </p>
                            <div class="table-responsive">
                                <table class="ct">
                                    <thead>
                                        <tr>
                                            <th>Código</th>
                                            <th>Título</th>
                                            <th>Sección</th>
                                            <th>Trayecto</th>
                                            <th>Comunidad</th>
                                            <th>Tipo Proyecto</th>
                                            <th>Fecha Límite</th>
                                            <th class="text-center">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="e in resultadosFiltro"
                                            :key="e.equ_id"
                                        >
                                            <td class="fw-bold">
                                                {{ e.equ_codigo }}
                                            </td>
                                            <td style="font-size: 0.85rem">
                                                {{ e.equ_titulo }}
                                            </td>
                                            <td>{{ e.seccion }}</td>
                                            <td style="font-size: 0.85rem">
                                                {{ e.trayecto }}
                                            </td>
                                            <td style="font-size: 0.85rem">
                                                {{ e.comunidad ?? "—" }}
                                            </td>
                                            <td style="font-size: 0.82rem">
                                                {{ e.tipo_proyecto ?? "—" }}
                                            </td>
                                            <td style="font-size: 0.82rem">
                                                {{ e.fecha_limite ?? "—" }}
                                            </td>
                                            <td class="text-center">
                                                <span
                                                    :class="{
                                                        ba:
                                                            e.resultado ===
                                                            'Aprobado',
                                                        bd:
                                                            e.resultado ===
                                                            'Aplazado',
                                                        bw:
                                                            e.resultado ===
                                                            'Sin resultado',
                                                        bsec:
                                                            e.resultado ===
                                                            'No Presentado',
                                                    }"
                                                    class="bs"
                                                    >{{ e.resultado }}</span
                                                >
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div
                            v-else-if="
                                filtroEjecutado &&
                                !cargandoFiltro &&
                                resultadosFiltro.length === 0
                            "
                            class="alert alert-warning d-flex align-items-center gap-2 my-3 p-3 rounded-3"
                            role="alert"
                        >
                            <i class="bi bi-exclamation-triangle-fill fs-4 text-warning flex-shrink-0"></i>
                            <div>
                                <div class="fw-bold">Inexistencia de los datos ingresados</div>
                                <div class="small">No se encontraron registros que coincidan con los filtros seleccionados. Intenta con otros parámetros.</div>
                            </div>
                        </div>
                        <div
                            v-else-if="
                                !cargandoFiltro &&
                                totalFiltro === 0 &&
                                resultadosFiltro.length === 0
                            "
                            class="text-center py-4"
                            style="color: var(--text-muted)"
                        >
                            Aplica un filtro para ver resultados
                        </div>
                    </div>
                </div>

                <!-- ── POR COMUNIDAD ──────────────────────────────────────── -->
                <div v-if="tabActiva === 'comunidad'" class="sc mb-4">
                    <div class="sh">
                        <h6>
                            <i class="bi bi-building me-2 text-danger"></i
                            >Proyectos por Comunidad
                        </h6>
                    </div>
                    <div
                        v-if="cargandoComunidad"
                        class="text-center py-4"
                        style="color: var(--text-muted)"
                    >
                        Cargando...
                    </div>
                    <div v-else class="table-responsive">
                        <table class="ct">
                            <thead>
                                <tr>
                                    <th class="ps-4">Comunidad</th>
                                    <th>Ubicación</th>
                                    <th class="text-center">Equipos</th>
                                    <th>Proyectos</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-if="!reporteComunidades.length">
                                    <td
                                        colspan="4"
                                        class="text-center py-4"
                                        style="color: var(--text-muted)"
                                    >
                                        Sin datos
                                    </td>
                                </tr>
                                <tr
                                    v-for="c in reporteComunidades"
                                    :key="c.com_id"
                                >
                                    <td class="ps-4 fw-bold">
                                        {{ c.com_nombre }}
                                    </td>
                                    <td
                                        style="
                                            font-size: 0.83rem;
                                            color: var(--text-muted);
                                        "
                                    >
                                        {{ c.com_ubicacion }}
                                    </td>
                                    <td class="text-center">
                                        <span class="bs bp">{{
                                            c.total_equipos
                                        }}</span>
                                    </td>
                                    <td>
                                        <div
                                            v-for="p in c.proyectos"
                                            :key="p.codigo"
                                            class="small"
                                            style="color: var(--text-muted)"
                                        >
                                            <span
                                                class="fw-bold"
                                                style="color: var(--text-main)"
                                                >{{ p.codigo }}</span
                                            >
                                            — {{ p.titulo }}
                                            <span
                                                :class="
                                                    p.resultado === 'Aprobado'
                                                        ? 'ba'
                                                        : 'bsec'
                                                "
                                                class="bs ms-1"
                                            >
                                                {{ p.resultado }}
                                            </span>
                                            <span
                                                v-if="p.total_beneficiarios > 0"
                                                class="ms-1"
                                                style="font-size: 0.78rem"
                                            >
                                                ({{ p.total_beneficiarios }}
                                                beneficiarios)
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- ── ENTREGABLES ────────────────────────────────────────── -->
                <div v-if="tabActiva === 'entregables'" class="sc mb-4">
                    <div class="sh">
                        <h6>
                            <i class="bi bi-file-earmark me-2 text-danger"></i
                            >Reporte de Entregables
                        </h6>
                    </div>
                    <div class="p-3">
                        <div class="d-flex gap-2 align-items-end mb-3">
                            <div>
                                <label class="form-label small fw-bold"
                                    >Filtrar por sección</label
                                >
                                <select
                                    v-model="seccionEntregables"
                                    class="form-select form-select-sm"
                                >
                                    <option value="">
                                        Todas las secciones
                                    </option>
                                    <option
                                        v-for="s in secciones"
                                        :key="s.sec_id"
                                        :value="s.sec_id"
                                    >
                                        {{ s.sec_codigo }} —
                                        {{ s.trayecto?.tra_nombre }}
                                    </option>
                                </select>
                            </div>
                            <button
                                @click="cargarEntregables"
                                class="btn btn-sm btn-danger fw-bold"
                            >
                                <i class="bi bi-search me-1"></i> Filtrar
                            </button>
                        </div>
                        <div v-if="reporteEntregables" class="row g-3 mb-3">
                            <div class="col-6 col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon si-s">
                                        <i class="bi bi-file-earmark"></i>
                                    </div>
                                    <div>
                                        <div class="stat-num">
                                            {{ reporteEntregables.total }}
                                        </div>
                                        <div class="stat-label">Total</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon si-g">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                    <div>
                                        <div class="stat-num">
                                            {{ reporteEntregables.aprobados }}
                                        </div>
                                        <div class="stat-label">Aprobados</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon si-r">
                                        <i class="bi bi-x-circle"></i>
                                    </div>
                                    <div>
                                        <div class="stat-num">
                                            {{ reporteEntregables.rechazados }}
                                        </div>
                                        <div class="stat-label">Rechazados</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon si-y">
                                        <i class="bi bi-hourglass"></i>
                                    </div>
                                    <div>
                                        <div class="stat-num">
                                            {{ reporteEntregables.pendientes }}
                                        </div>
                                        <div class="stat-label">Pendientes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            v-if="reporteEntregables?.detalle?.length"
                            class="table-responsive"
                        >
                            <table class="ct">
                                <thead>
                                    <tr>
                                        <th>Equipo</th>
                                        <th>Sección</th>
                                        <th>Tipo</th>
                                        <th>Archivo</th>
                                        <th class="text-center">Estado</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr
                                        v-for="e in reporteEntregables.detalle"
                                        :key="e.archivo"
                                    >
                                        <td class="fw-bold">{{ e.equipo }}</td>
                                        <td>{{ e.seccion }}</td>
                                        <td style="font-size: 0.85rem">
                                            {{ e.tipo }}
                                        </td>
                                        <td style="font-size: 0.82rem">
                                            {{ e.archivo }}
                                        </td>
                                        <td class="text-center">
                                            <span
                                                :class="{
                                                    ba: e.estado === 'Aprobado',
                                                    bd:
                                                        e.estado ===
                                                        'Rechazado',
                                                    bw:
                                                        e.estado ===
                                                        'Pendiente',
                                                }"
                                                class="bs"
                                                >{{ e.estado }}</span
                                            >
                                        </td>
                                        <td style="font-size: 0.82rem">
                                            {{ e.fecha?.split("T")[0] }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- ── PUNTOS DE CONTROL ──────────────────────────────────── -->
                <div v-if="tabActiva === 'puntos'" class="sc mb-4">
                    <div class="sh">
                        <h6>
                            <i class="bi bi-check2-square me-2 text-danger"></i
                            >Reporte de Puntos de Control
                        </h6>
                    </div>
                    <div class="p-3">
                        <div class="d-flex gap-2 align-items-end mb-3">
                            <div>
                                <label class="form-label small fw-bold"
                                    >Filtrar por sección</label
                                >
                                <select
                                    v-model="seccionPuntos"
                                    class="form-select form-select-sm"
                                >
                                    <option value="">
                                        Todas las secciones
                                    </option>
                                    <option
                                        v-for="s in secciones"
                                        :key="s.sec_id"
                                        :value="s.sec_id"
                                    >
                                        {{ s.sec_codigo }} —
                                        {{ s.trayecto?.tra_nombre }}
                                    </option>
                                </select>
                            </div>
                            <button
                                @click="cargarPuntos"
                                class="btn btn-sm btn-danger fw-bold"
                            >
                                <i class="bi bi-search me-1"></i> Filtrar
                            </button>
                        </div>
                        <div v-if="reportePuntos" class="row g-3">
                            <div class="col-6 col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon si-s">
                                        <i class="bi bi-check2-square"></i>
                                    </div>
                                    <div>
                                        <div class="stat-num">
                                            {{ reportePuntos.total }}
                                        </div>
                                        <div class="stat-label">Total</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon si-g">
                                        <i class="bi bi-check-circle"></i>
                                    </div>
                                    <div>
                                        <div class="stat-num">
                                            {{ reportePuntos.cumplidos }}
                                        </div>
                                        <div class="stat-label">Cumplidos</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon si-r">
                                        <i class="bi bi-exclamation-circle"></i>
                                    </div>
                                    <div>
                                        <div class="stat-num">
                                            {{ reportePuntos.vencidos }}
                                        </div>
                                        <div class="stat-label">Vencidos</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6 col-md-3">
                                <div class="stat-card">
                                    <div class="stat-icon si-y">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                    <div>
                                        <div class="stat-num">
                                            {{ reportePuntos.pendientes }}
                                        </div>
                                        <div class="stat-label">Pendientes</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Reporte solvencia -->
                <div class="sc">
                    <div class="sh">
                        <h6>
                            <i
                                class="bi bi-person-check-fill me-2 text-danger"
                            ></i
                            >Reporte de Solvencia por Sección
                        </h6>
                    </div>
                    <div class="p-3">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label
                                    class="form-label fw-semibold"
                                    style="color: var(--text-main)"
                                    >Seleccionar Sección</label
                                >
                                <select
                                    v-model="seccionSeleccionada"
                                    class="form-select"
                                >
                                    <option value="">Seleccionar...</option>
                                    <option
                                        v-for="sec in secciones"
                                        :key="sec.sec_id"
                                        :value="sec.sec_id"
                                    >
                                        {{ sec.sec_codigo }} —
                                        {{ sec.trayecto?.tra_nombre }} ({{
                                            sec.turno?.tur_nombre
                                        }})
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <button
                                    class="btn btn-danger w-100 fw-bold rounded-pill"
                                    :disabled="!seccionSeleccionada"
                                    @click="generarSolvencia"
                                >
                                    <i class="bi bi-bar-chart-fill me-2"></i
                                    >Generar
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a
                                    v-if="seccionSeleccionada"
                                    :href="`/coordinador/reportes/exportar-solvencia?sec_id=${seccionSeleccionada}`"
                                    target="_blank"
                                    class="btn btn-outline-danger w-100 fw-bold rounded-pill"
                                    ><i class="bi bi-file-earmark-pdf me-1"></i
                                    >Exportar PDF</a
                                >
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
import { router, usePage, Link } from "@inertiajs/vue3";

const menuMaestras = ref(false);
const menuSistema = ref(false);
const page = usePage();
const props = defineProps({
    secciones: Array,
    trayectos: Array,
    comunidades: Array,
    tiposProyecto: Array,
    profesores: Array,
    resumenGeneral: Object,
    reportePorSeccion: Array,
});

const tema = ref(localStorage.getItem("coord-theme") || "light");
const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("coord-theme", tema.value);
};

const iniciales = computed(() => {
    const u = page.props.auth?.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});

const logout = () => router.post("/logout");

// ── Solvencia ────────────────────────────────────────────────────────
const seccionSeleccionada = ref("");
const generarSolvencia = () => {
    if (!seccionSeleccionada.value) return;
    router.get("/coordinador/reportes/solvencia", {
        sec_id: seccionSeleccionada.value,
    });
};

// ── Filtro combinado ─────────────────────────────────────────────────
const filtros = ref({
    sec_id: "",
    tra_id: "",
    com_id: "",
    tpr_id: "",
    estado: "",
    prof_id: "",
    fecha_desde: "",
    fecha_hasta: "",
    vencidos: false,
});
const resultadosFiltro = ref([]);
const totalFiltro = ref(0);
const cargandoFiltro = ref(false);
const filtroEjecutado = ref(false);
const tabActiva = ref("general");

const aplicarFiltro = async () => {
    cargandoFiltro.value = true;
    filtroEjecutado.value = true;
    const params = new URLSearchParams();
    Object.entries(filtros.value).forEach(([k, v]) => {
        if (v !== "" && v !== false) params.append(k, v);
    });
    try {
        const res = await fetch(
            `/coordinador/reportes/filtrar?${params.toString()}`,
            {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            },
        );
        const data = await res.json();
        resultadosFiltro.value = data.equipos;
        totalFiltro.value = data.total;
        tabActiva.value = "filtro";
    } catch (e) {
        console.error(e);
    } finally {
        cargandoFiltro.value = false;
    }
};

const limpiarFiltros = () => {
    filtros.value = {
        sec_id: "",
        tra_id: "",
        com_id: "",
        tpr_id: "",
        estado: "",
        prof_id: "",
        fecha_desde: "",
        fecha_hasta: "",
        vencidos: false,
    };
    resultadosFiltro.value = [];
    totalFiltro.value = 0;
    filtroEjecutado.value = false;
};

// ── Por comunidad ────────────────────────────────────────────────────
const reporteComunidades = ref([]);
const cargandoComunidad = ref(false);

const cargarReporteComunidad = async () => {
    cargandoComunidad.value = true;
    try {
        const res = await fetch("/coordinador/reportes/por-comunidad", {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        const data = await res.json();
        reporteComunidades.value = data.comunidades;
        tabActiva.value = "comunidad";
    } catch (e) {
        console.error(e);
    } finally {
        cargandoComunidad.value = false;
    }
};

// ── Entregables ──────────────────────────────────────────────────────
const reporteEntregables = ref(null);
const seccionEntregables = ref("");
const cargandoEntregables = ref(false);

const cargarEntregables = async () => {
    cargandoEntregables.value = true;
    try {
        const params = seccionEntregables.value
            ? `?sec_id=${seccionEntregables.value}`
            : "";
        const res = await fetch(`/coordinador/reportes/entregables${params}`, {
            headers: { "X-Requested-With": "XMLHttpRequest" },
        });
        reporteEntregables.value = await res.json();
        tabActiva.value = "entregables";
    } catch (e) {
        console.error(e);
    } finally {
        cargandoEntregables.value = false;
    }
};

// ── Puntos de control ────────────────────────────────────────────────
const reportePuntos = ref(null);
const seccionPuntos = ref("");
const cargandoPuntos = ref(false);

const cargarPuntos = async () => {
    cargandoPuntos.value = true;
    try {
        const params = seccionPuntos.value
            ? `?sec_id=${seccionPuntos.value}`
            : "";
        const res = await fetch(
            `/coordinador/reportes/puntos-control${params}`,
            {
                headers: { "X-Requested-With": "XMLHttpRequest" },
            },
        );
        reportePuntos.value = await res.json();
        tabActiva.value = "puntos";
    } catch (e) {
        console.error(e);
    } finally {
        cargandoPuntos.value = false;
    }
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
.sh {
    padding: 1rem 1.3rem;
    border-bottom: 1px solid var(--border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
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
.stat-card {
    background: var(--bg-card);
    border-radius: 13px;
    padding: 1.1rem;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 3px 10px rgba(0, 0, 0, 0.04);
    border: 1px solid var(--border);
}
.stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 11px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
}
.si-r {
    background: rgba(220, 53, 69, 0.12);
    color: #dc3545;
}
.si-g {
    background: rgba(25, 135, 84, 0.12);
    color: #198754;
}
.si-y {
    background: rgba(255, 193, 7, 0.12);
    color: #856404;
}
.si-s {
    background: rgba(108, 117, 125, 0.12);
    color: #6c757d;
}
.stat-num {
    font-size: 1.4rem;
    font-weight: 800;
    color: var(--text-main);
    line-height: 1;
}
.stat-label {
    font-size: 0.78rem;
    color: var(--text-muted);
    font-weight: 600;
    margin-top: 2px;
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
.form-control,
.form-select {
    background: var(--bg-card) !important;
    color: var(--text-main) !important;
    border-color: var(--border) !important;
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
