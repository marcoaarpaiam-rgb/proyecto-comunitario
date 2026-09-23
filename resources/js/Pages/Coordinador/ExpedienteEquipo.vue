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
                <a href="/coordinador/maestras/tipos-entregable" class="sb-lnk"
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
                <a href="/coordinador/maestras/estados-proyecto" class="sb-lnk"
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
                            class="bi bi-folder2-open me-2 text-danger d-none d-sm-inline"
                        ></i
                        >Expediente — {{ equipo.equ_codigo }}
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a
                        href="/coordinador/equipos"
                        class="btn btn-sm btn-outline-secondary rounded-pill px-3"
                        ><i class="bi bi-arrow-left me-1"></i>Equipos</a
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
                                    <span class="info-label">Tipo</span
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

                    <!-- Puntos de Control -->
                    <div class="col-12" v-if="equipo.puntos_control?.length">
                        <div class="sc">
                            <div class="sh">
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
                                        cumplidos</span
                                    >
                                </h6>
                            </div>
                            <div class="table-responsive">
                                <table class="ct">
                                    <thead>
                                        <tr>
                                            <th class="ps-4">Punto</th>
                                            <th>Fecha Límite</th>
                                            <th>Cumplido</th>
                                            <th>Observaciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr
                                            v-for="puc in equipo.puntos_control"
                                            :key="puc.puc_id"
                                        >
                                            <td class="ps-4 fw-semibold">
                                                {{ puc.puc_nombre }}
                                            </td>
                                            <td>
                                                <span
                                                    :style="
                                                        !puc.seguimiento
                                                            ?.seq_cumplido &&
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
                                                    style="
                                                        font-size: 0.73rem;
                                                        color: var(
                                                            --text-muted
                                                        );
                                                        margin-left: 4px;
                                                    "
                                                    >(reprog.)</span
                                                >
                                            </td>
                                            <td>
                                                <span
                                                    v-if="
                                                        puc.seguimiento
                                                            ?.seq_cumplido
                                                    "
                                                    class="bs ba"
                                                    style="font-size: 0.72rem"
                                                    >✓ Cumplido</span
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
                                            </td>
                                            <td
                                                style="
                                                    font-size: 0.83rem;
                                                    color: var(--text-muted);
                                                "
                                            >
                                                {{
                                                    puc.seguimiento
                                                        ?.seq_observaciones ||
                                                    "—"
                                                }}
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
                                            {{
                                                ent.tipo_entregable?.tet_nombre
                                            }}
                                            · v{{ ent.entr_version || "1" }}
                                        </div>
                                    </div>
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
                                                ? "Revisión"
                                                : ent.entr_aprobado
                                                  ? "Aprobado"
                                                  : "Rechazado"
                                        }}
                                    </span>
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
const props = defineProps({ equipo: Object });
const page = usePage();
const sidebarOpen = ref(false);
const tema = ref(localStorage.getItem("coord-theme") || "light");
const toggleTema = () => {
    tema.value = tema.value === "dark" ? "light" : "dark";
    localStorage.setItem("coord-theme", tema.value);
};
const logout = () => router.post("/logout");
const iniciales = computed(() => {
    const u = page.props.auth.user;
    return (
        (u?.usu_primer_nombre?.[0] || "") + (u?.usu_primer_apellido?.[0] || "")
    );
});
const estaVencido = (f) => f && new Date(f) < new Date();
const cumplidos = computed(
    () =>
        (props.equipo.puntos_control || []).filter(
            (p) => p.seguimiento?.seq_cumplido,
        ).length,
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
    return "background:#e2e3e5;color:#41464b";
};
const iconEvt = (n) => {
    if (n === "Disuelto") return "bi-x-circle";
    if (n === "Líder Cambiado") return "bi-arrow-repeat";
    if (n === "Integrante Removido") return "bi-person-dash";
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
