<template>
    <div :data-bs-theme="theme">
        <!-- Botón Flotante Modo Oscuro -->
        <button
            class="btn btn-dark theme-toggle-btn"
            @click="toggleTheme"
            title="Cambiar tema"
        >
            <i :class="themeIcon + ' fs-4'"></i>
        </button>

        <!-- Topbar -->
        <div class="bg-dark text-white py-2">
            <div
                class="container d-flex justify-content-between align-items-center small"
            >
                <span
                    ><i class="bi bi-geo-alt-fill text-danger me-2"></i
                    >Acarigua, Portuguesa</span
                >
                <div class="d-none d-sm-block">
                    <span class="me-3"
                        ><i
                            class="bi bi-envelope-open-fill text-danger me-1"
                        ></i>
                        contacto@uptp.edu.ve</span
                    >
                    <span
                        ><i class="bi bi-calendar3 text-danger me-1"></i> Lun -
                        Vie: 8:00 AM - 4:00 PM</span
                    >
                </div>
            </div>
        </div>

        <!-- Header Principal -->
        <header class="bg-body-tertiary py-3 border-bottom shadow-sm">
            <div
                class="container d-flex align-items-center justify-content-between"
            >
                <a class="navbar-brand d-flex align-items-center" href="#">
                    <img
                        :src="'/images/images.png'"
                        alt="Logo UPTP"
                        style="height: 65px"
                    />
                    <div class="ms-3">
                        <span class="d-block fw-bold fs-3 text-body lh-1"
                            >UPTP</span
                        >
                        <small class="text-muted fw-bold fs-6"
                            >J.J. Montilla</small
                        >
                    </div>
                </a>
                <div class="d-none d-md-flex align-items-center gap-3">
                    <a
                        href="/"
                        class="btn btn-danger btn-lg fw-bold rounded-pill px-4 shadow-sm"
                    >
                        <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar
                        Sesión
                    </a>
                    <a
                        href="https://uptp.sytes.net/daece/"
                        target="_blank"
                        class="btn btn-outline-danger btn-lg fw-bold rounded-pill px-4"
                    >
                        <i class="bi bi-database-fill-gear me-2"></i>Ir a SOGAC
                    </a>
                </div>
            </div>
        </header>

        <!-- Navbar -->
        <nav
            class="navbar navbar-expand-md navbar-dark bg-danger sticky-top shadow"
        >
            <div class="container">
                <button
                    class="navbar-toggler"
                    type="button"
                    data-bs-toggle="collapse"
                    data-bs-target="#navbarNavFunciones"
                >
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavFunciones">
                    <ul class="navbar-nav w-100 align-items-center">
                        <li class="nav-item">
                            <a
                                class="nav-link active fw-bold fs-5 py-2 px-3"
                                href="#"
                            >
                                <i class="bi bi-house-door-fill me-1"></i>
                                Inicio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link text-white fw-semibold fs-5 py-2 px-3"
                                href="#"
                                data-bs-toggle="modal"
                                data-bs-target="#modalSistema"
                            >
                                <i class="bi bi-info-circle-fill me-1"></i>
                                Sobre el sistema
                            </a>
                        </li>
                        <li class="nav-item">
                            <a
                                class="nav-link text-white fw-semibold fs-5 py-2 px-3"
                                href="/"
                            >
                                <i class="bi bi-folder-fill me-1"></i> Catálogo
                                de Proyectos
                            </a>
                        </li>
                        <li class="nav-item d-md-none w-100 my-2">
                            <a
                                href="/"
                                class="btn btn-light text-danger w-100 fw-bold rounded-pill"
                            >
                                <i class="bi bi-box-arrow-in-right me-2"></i
                                >Iniciar Sesión
                            </a>
                        </li>
                        <li class="nav-item d-md-none w-100 mb-2">
                            <a
                                href="https://uptp.sytes.net/daece/"
                                target="_blank"
                                class="btn btn-outline-light w-100 fw-bold rounded-pill"
                            >
                                <i class="bi bi-database-fill-gear me-2"></i>Ir
                                a SOGAC
                            </a>
                        </li>
                        <li
                            class="nav-item ms-md-auto text-center text-md-end border-start-md ps-md-3 border-light border-opacity-25 mt-3 mt-md-0"
                        >
                            <span
                                class="badge bg-white text-danger fw-bold fs-6 px-3"
                                >Coordinación de Informática</span
                            >
                            <p class="small text-white-50 mb-0">
                                Sede Principal
                            </p>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Sección PNF con imagen de fondo -->
        <section
            class="py-5 position-relative text-white shadow-inner"
            id="programas"
            style="
                background:
                    linear-gradient(rgba(0, 0, 0, 0.75), rgba(0, 0, 0, 0.85)),
                    url(&quot;/images/ingenieria.jpg&quot;) no-repeat center
                        center / cover;
            "
        >
            <div class="container py-4 position-relative" style="z-index: 2">
                <div class="row align-items-center">
                    <div
                        class="col-12 col-lg-5 text-center text-lg-start mb-5 mb-lg-0"
                    >
                        <h1
                            class="display-4 fw-bolder text-uppercase mb-3 text-white"
                        >
                            PNF en <br />
                            <span class="text-danger">Informática</span>
                        </h1>
                        <p
                            class="fs-5 text-justify text-light opacity-90 pe-lg-3"
                        >
                            El Programa Nacional de Formación (PNF) en
                            Informática es una disciplina fundamental en el
                            mundo digital actual, enfocada en el diseño,
                            desarrollo, implementación y mantenimiento de
                            sistemas informáticos, desarrollo de software,
                            arquitectura de redes y soluciones tecnológicas
                            avanzadas.
                        </p>
                        <button
                            type="button"
                            class="btn btn-danger btn-lg mt-3 px-4 shadow"
                            data-bs-toggle="modal"
                            data-bs-target="#modalCarrera"
                        >
                            <i class="bi bi-plus-circle-fill me-2"></i>Leer más
                            sobre la carrera
                        </button>
                    </div>
                    <div class="col-12 col-lg-7">
                        <div
                            class="p-4 rounded-4 shadow"
                            style="
                                background: rgba(0, 0, 0, 0.45);
                                backdrop-filter: blur(10px);
                                border: 1px solid rgba(255, 255, 255, 0.15);
                            "
                        >
                            <div class="row">
                                <div class="col-md-6 mb-4 mb-md-0">
                                    <h4 class="fw-bold mb-3 text-danger">
                                        <i class="bi bi-book-half me-2"></i>¿Qué
                                        aprenderás?
                                    </h4>
                                    <ul
                                        class="list-unstyled fs-5 text-white-50"
                                    >
                                        <li class="mb-2">
                                            <i
                                                class="bi bi-check-circle-fill text-danger me-2"
                                            ></i
                                            ><span class="text-white"
                                                >Desarrollo de Software</span
                                            >
                                        </li>
                                        <li class="mb-2">
                                            <i
                                                class="bi bi-shield-lock-fill text-danger me-2"
                                            ></i
                                            ><span class="text-white"
                                                >Ciberseguridad</span
                                            >
                                        </li>
                                        <li class="mb-2">
                                            <i
                                                class="bi bi-cpu-fill text-danger me-2"
                                            ></i
                                            ><span class="text-white"
                                                >Inteligencia Artificial</span
                                            >
                                        </li>
                                        <li>
                                            <i
                                                class="bi bi-diagram-3-fill text-danger me-2"
                                            ></i
                                            ><span class="text-white"
                                                >Arquitectura de Redes</span
                                            >
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-md-6">
                                    <h4 class="fw-bold mb-3 text-danger">
                                        <i class="bi bi-briefcase-fill me-2"></i
                                        >Campo Laboral
                                    </h4>
                                    <ul
                                        class="list-unstyled fs-5 text-white-50"
                                    >
                                        <li class="mb-2">
                                            <i
                                                class="bi bi-terminal-box text-success me-2"
                                            ></i
                                            ><span class="text-white"
                                                >Desarrollador Full-Stack</span
                                            >
                                        </li>
                                        <li class="mb-2">
                                            <i
                                                class="bi bi-cloud-check-fill text-success me-2"
                                            ></i
                                            ><span class="text-white"
                                                >Administrador de Redes</span
                                            >
                                        </li>
                                        <li class="mb-2">
                                            <i
                                                class="bi bi-lock-fill text-success me-2"
                                            ></i
                                            ><span class="text-white"
                                                >Consultor de
                                                Ciberseguridad</span
                                            >
                                        </li>
                                        <li>
                                            <i
                                                class="bi bi-kanban-fill text-success me-2"
                                            ></i
                                            ><span class="text-white"
                                                >Director de Proyectos TI</span
                                            >
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Hero -->
        <section class="hero py-5" id="hero">
            <div class="container">
                <div class="row align-items-center">
                    <div
                        class="col-12 col-md-6 text-center text-md-start mb-4 mb-md-0"
                    >
                        <h1 class="display-5 fw-bold mb-3">
                            SISTEMA DIGITAL DE GESTIÓN DE PROYECTOS
                        </h1>
                        <h4 class="text-danger fw-semibold text-uppercase mb-3">
                            <i class="bi bi-gpu-card me-2"></i>Coordinación de
                            Informática
                        </h4>
                        <p class="fs-5 text-justify text-muted mb-4">
                            Agiliza la inscripción, revisión y consulta de tus
                            proyectos académicos. Reemplaza el papeleo manual de
                            forma completamente segura, eficiente y optimizada.
                        </p>
                        <a href="/" class="btn btn-danger btn-lg me-2 shadow">
                            <i class="bi bi-pencil-square me-2"></i>Registrar mi
                            proyecto
                        </a>
                        <a
                            href="#malla-curricular"
                            class="btn btn-outline-secondary btn-lg"
                        >
                            <i class="bi bi-eye-fill me-2"></i>Ver Trayectos
                        </a>
                    </div>
                    <div class="col-12 col-md-6 text-center">
                        <img
                            :src="'/images/centro.jpg'"
                            class="img-fluid rounded-4 shadow-sm"
                            style="
                                max-height: 380px;
                                object-fit: cover;
                                width: 100%;
                            "
                            alt="Centro tecnológico"
                        />
                    </div>
                </div>
            </div>
        </section>

        <!-- Malla Curricular -->
        <section class="py-5 bg-body-secondary" id="malla-curricular">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold display-6">
                        Contenido Académico por
                        <span class="text-danger">Trayectos</span>
                    </h2>
                    <p class="text-muted fs-5">
                        Explora las unidades curriculares que cursarás en la
                        ingeniería.
                    </p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div
                            class="accordion shadow border-0"
                            id="accordionMalla"
                        >
                            <div
                                class="accordion-item border-0 mb-3 rounded-3 overflow-hidden"
                            >
                                <h2 class="accordion-header">
                                    <button
                                        class="accordion-button fw-bold fs-5"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#t1"
                                    >
                                        <i
                                            class="bi bi-1-circle-fill me-3 text-danger fs-4"
                                        ></i>
                                        Trayecto I - Fundamentos
                                    </button>
                                </h2>
                                <div
                                    id="t1"
                                    class="accordion-collapse collapse show"
                                    data-bs-parent="#accordionMalla"
                                >
                                    <div class="accordion-body bg-body fs-5">
                                        <ul
                                            class="row row-cols-1 row-cols-md-2 g-2 list-unstyled ps-3"
                                        >
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Algorítmica y Programación I
                                            </li>
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Arquitectura del Computador
                                            </li>
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Matemática Aplicada
                                            </li>
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Proyecto Socio-Tecnológico I
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="accordion-item border-0 mb-3 rounded-3 overflow-hidden"
                            >
                                <h2 class="accordion-header">
                                    <button
                                        class="accordion-button collapsed fw-bold fs-5"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#t2"
                                    >
                                        <i
                                            class="bi bi-2-circle-fill me-3 text-danger fs-4"
                                        ></i>
                                        Trayecto II - Desarrollo Profesional
                                    </button>
                                </h2>
                                <div
                                    id="t2"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordionMalla"
                                >
                                    <div class="accordion-body bg-body fs-5">
                                        <ul
                                            class="row row-cols-1 row-cols-md-2 g-2 list-unstyled ps-3"
                                        >
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Programación II (POO)
                                            </li>
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Bases de Datos I
                                            </li>
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Redes de Computadoras
                                            </li>
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Ingeniería de Software I
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="accordion-item border-0 mb-3 rounded-3 overflow-hidden"
                            >
                                <h2 class="accordion-header">
                                    <button
                                        class="accordion-button collapsed fw-bold fs-5"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#t3"
                                    >
                                        <i
                                            class="bi bi-3-circle-fill me-3 text-danger fs-4"
                                        ></i>
                                        Trayecto III - Especialización
                                    </button>
                                </h2>
                                <div
                                    id="t3"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordionMalla"
                                >
                                    <div class="accordion-body bg-body fs-5">
                                        <ul
                                            class="row row-cols-1 row-cols-md-2 g-2 list-unstyled ps-3"
                                        >
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Sistemas Operativos
                                            </li>
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Gestión de Proyectos
                                                Sociotecnológicos
                                            </li>
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Bases de Datos Avanzadas
                                            </li>
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Programación Web
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="accordion-item border-0 mb-3 rounded-3 overflow-hidden"
                            >
                                <h2 class="accordion-header">
                                    <button
                                        class="accordion-button collapsed fw-bold fs-5"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#t4"
                                    >
                                        <i
                                            class="bi bi-4-circle-fill me-3 text-danger fs-4"
                                        ></i>
                                        Trayecto IV - Gestión y Optimización
                                    </button>
                                </h2>
                                <div
                                    id="t4"
                                    class="accordion-collapse collapse"
                                    data-bs-parent="#accordionMalla"
                                >
                                    <div class="accordion-body bg-body fs-5">
                                        <ul
                                            class="row row-cols-1 row-cols-md-2 g-2 list-unstyled ps-3"
                                        >
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Administración de Bases de
                                                Datos
                                            </li>
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Seguridad Informática
                                            </li>
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Auditoría de Sistemas
                                            </li>
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Proyecto Socio-Tecnológico IV
                                            </li>
                                            <li>
                                                <i
                                                    class="bi bi-chevron-right text-danger me-2"
                                                ></i
                                                >Inteligencia Artificial
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Áreas de Desempeño -->
        <section class="py-5 bg-body" id="areas-trabajo">
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold display-6">
                        Áreas de
                        <span class="text-danger">Desempeño Profesional</span>
                    </h2>
                    <p class="text-muted fs-5">
                        Como futuro ingeniero, estas son algunas de las áreas
                        donde podrás liderar proyectos.
                    </p>
                </div>
                <div class="row g-4 text-center">
                    <div class="col-6 col-lg-3">
                        <div
                            class="p-4 border rounded-4 shadow-sm h-100 bg-body-tertiary card border-0"
                        >
                            <i
                                class="bi bi-terminal-dash text-danger display-4 mb-3"
                            ></i>
                            <h5 class="fw-bold">Desarrollo de Software</h5>
                            <small class="text-muted fs-6"
                                >Creación de aplicaciones móviles, plataformas
                                web y sistemas empresariales complejos.</small
                            >
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div
                            class="p-4 border rounded-4 shadow-sm h-100 bg-body-tertiary card border-0"
                        >
                            <i
                                class="bi bi-shield-lock-fill text-danger display-4 mb-3"
                            ></i>
                            <h5 class="fw-bold">Ciberseguridad</h5>
                            <small class="text-muted fs-6"
                                >Protección robusta de activos digitales, datos
                                críticos y auditorías preventivas.</small
                            >
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div
                            class="p-4 border rounded-4 shadow-sm h-100 bg-body-tertiary card border-0"
                        >
                            <i
                                class="bi bi-router-fill text-danger display-4 mb-3"
                            ></i>
                            <h5 class="fw-bold">Redes y Telecom</h5>
                            <small class="text-muted fs-6"
                                >Diseño, despliegue y administración avanzada de
                                infraestructuras globales de red.</small
                            >
                        </div>
                    </div>
                    <div class="col-6 col-lg-3">
                        <div
                            class="p-4 border rounded-4 shadow-sm h-100 bg-body-tertiary card border-0"
                        >
                            <i
                                class="bi bi-boxes text-danger display-4 mb-3"
                            ></i>
                            <h5 class="fw-bold">Big Data & IA</h5>
                            <small class="text-muted fs-6"
                                >Análisis inteligente de volúmenes masivos de
                                datos y modelos neuronales modernos.</small
                            >
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Cómo funciona -->
        <div
            class="container mt-5 pt-4 text-center"
            id="sistema-funcionamiento"
        >
            <h2 class="fw-bold display-6">
                ¿Cómo <span class="text-danger">funciona</span> el sistema?
            </h2>
            <p class="text-muted fs-5">
                Sigue estos tres sencillos pasos para formalizar tu proyecto
                académico.
            </p>
        </div>
        <div class="container my-5">
            <div class="row g-4">
                <div class="col-md-4">
                    <div
                        class="card h-100 border shadow-sm text-center p-3 bg-body"
                    >
                        <div
                            class="d-flex align-items-center justify-content-center"
                            style="height: 180px"
                        >
                            <img
                                :src="'/images/registro.jpg'"
                                class="img-fluid rounded-3"
                                style="max-height: 150px"
                                alt="Registrar"
                            />
                        </div>
                        <div class="card-body">
                            <h4 class="card-title fw-bold">
                                1. Registrar Grupo
                            </h4>
                            <p class="card-text text-muted fs-6 text-justify">
                                Ingresa de forma estructurada los datos de
                                identificación de los autores integrantes y el
                                tutor académico asignado.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div
                        class="card h-100 border shadow-sm text-center p-3 bg-body"
                    >
                        <div
                            class="d-flex align-items-center justify-content-center"
                            style="height: 180px"
                        >
                            <img
                                :src="'/images/archivo.png'"
                                class="img-fluid rounded-3"
                                style="max-height: 150px"
                                alt="Subir"
                            />
                        </div>
                        <div class="card-body">
                            <h4 class="card-title fw-bold">
                                2. Subir la Propuesta
                            </h4>
                            <p class="card-text text-muted fs-6 text-justify">
                                Adjunta los documentos requeridos en la
                                plataforma para un almacenamiento digital seguro
                                y centralizado.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div
                        class="card h-100 border shadow-sm text-center p-3 bg-body"
                    >
                        <div
                            class="d-flex align-items-center justify-content-center"
                            style="height: 180px"
                        >
                            <img
                                :src="'/images/mano.png'"
                                class="img-fluid rounded-3"
                                style="max-height: 150px"
                                alt="Esperar"
                            />
                        </div>
                        <div class="card-body">
                            <h4 class="card-title fw-bold">
                                3. Esperar Confirmación
                            </h4>
                            <p class="card-text text-muted fs-6 text-justify">
                                La coordinación del PNF evalúa la propuesta
                                recibida y notifica los resultados directamente
                                a través del portal.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carrusel de Instalaciones -->
        <section
            id="areas-disponibles"
            class="py-5 bg-body-secondary border-top"
        >
            <div class="container">
                <div class="text-center mb-5">
                    <h2 class="fw-bold display-6">
                        Instalaciones de
                        <span class="text-danger">Informática</span>
                    </h2>
                    <p class="text-muted fs-5">
                        Explora los entornos académicos diseñados para tu
                        desarrollo profesional.
                    </p>
                </div>
                <div class="row justify-content-center">
                    <div class="col-lg-10">
                        <div
                            id="carouselAreas"
                            class="carousel slide shadow-lg rounded-4 overflow-hidden"
                            data-bs-ride="carousel"
                        >
                            <div class="carousel-indicators">
                                <button
                                    type="button"
                                    data-bs-target="#carouselAreas"
                                    data-bs-slide-to="0"
                                    class="active"
                                ></button>
                                <button
                                    type="button"
                                    data-bs-target="#carouselAreas"
                                    data-bs-slide-to="1"
                                ></button>
                                <button
                                    type="button"
                                    data-bs-target="#carouselAreas"
                                    data-bs-slide-to="2"
                                ></button>
                                <button
                                    type="button"
                                    data-bs-target="#carouselAreas"
                                    data-bs-slide-to="3"
                                ></button>
                                <button
                                    type="button"
                                    data-bs-target="#carouselAreas"
                                    data-bs-slide-to="4"
                                ></button>
                            </div>
                            <div class="carousel-inner">
                                <div
                                    class="carousel-item active"
                                    data-bs-interval="4000"
                                >
                                    <img
                                        :src="'/images/lab1.jpg'"
                                        class="d-block w-100 object-fit-cover"
                                        style="
                                            height: 500px;
                                            filter: brightness(45%);
                                        "
                                        alt="Lab Francisco de Miranda"
                                    />
                                    <div
                                        class="carousel-caption d-block bg-dark bg-opacity-75 rounded-4 p-4 mx-auto mb-4"
                                        style="max-width: 80%"
                                    >
                                        <h3 class="fw-bold text-white mb-0">
                                            <i
                                                class="bi bi-window-sidebar me-2"
                                            ></i
                                            >Laboratorio Francisco de Miranda
                                        </h3>
                                    </div>
                                </div>
                                <div
                                    class="carousel-item"
                                    data-bs-interval="4000"
                                >
                                    <img
                                        :src="'/images/cor1.jpg'"
                                        class="d-block w-100 object-fit-cover"
                                        style="
                                            height: 500px;
                                            filter: brightness(45%);
                                        "
                                        alt="Coordinación"
                                    />
                                    <div
                                        class="carousel-caption d-block bg-dark bg-opacity-75 rounded-4 p-4 mx-auto mb-4"
                                        style="max-width: 80%"
                                    >
                                        <h3 class="fw-bold text-white mb-0">
                                            <i
                                                class="bi bi-people-fill me-2"
                                            ></i
                                            >Coordinación del PNF en Informática
                                        </h3>
                                    </div>
                                </div>
                                <div
                                    class="carousel-item"
                                    data-bs-interval="4000"
                                >
                                    <img
                                        :src="'/images/IMG_8467.jpeg'"
                                        class="d-block w-100 object-fit-cover"
                                        style="
                                            height: 500px;
                                            filter: brightness(45%);
                                        "
                                        alt="Lab Simón Bolívar"
                                    />
                                    <div
                                        class="carousel-caption d-block bg-dark bg-opacity-75 rounded-4 p-4 mx-auto mb-4"
                                        style="max-width: 80%"
                                    >
                                        <h3 class="fw-bold text-white mb-0">
                                            <i class="bi bi-pc-display me-2"></i
                                            >Laboratorio Simón Bolívar
                                        </h3>
                                    </div>
                                </div>
                                <div
                                    class="carousel-item"
                                    data-bs-interval="4000"
                                >
                                    <img
                                        :src="'/images/lab4.jpeg'"
                                        class="d-block w-100 object-fit-cover"
                                        style="
                                            height: 500px;
                                            filter: brightness(45%);
                                        "
                                        alt="Lab Antonio José de Sucre"
                                    />
                                    <div
                                        class="carousel-caption d-block bg-dark bg-opacity-75 rounded-4 p-4 mx-auto mb-4"
                                        style="max-width: 80%"
                                    >
                                        <h3 class="fw-bold text-white mb-0">
                                            <i
                                                class="bi bi-terminal-fill me-2"
                                            ></i
                                            >Laboratorio Antonio José de Sucre
                                        </h3>
                                    </div>
                                </div>
                                <div
                                    class="carousel-item"
                                    data-bs-interval="4000"
                                >
                                    <img
                                        :src="'/images/lab3.jpg'"
                                        class="d-block w-100 object-fit-cover"
                                        style="
                                            height: 500px;
                                            filter: brightness(45%);
                                        "
                                        alt="Lab Soporte"
                                    />
                                    <div
                                        class="carousel-caption d-block bg-dark bg-opacity-75 rounded-4 p-4 mx-auto mb-4"
                                        style="max-width: 80%"
                                    >
                                        <h3 class="fw-bold text-white mb-0">
                                            <i class="bi bi-tools me-2"></i
                                            >Laboratorio de Investigación y
                                            Soporte
                                        </h3>
                                    </div>
                                </div>
                            </div>
                            <button
                                class="carousel-control-prev"
                                type="button"
                                data-bs-target="#carouselAreas"
                                data-bs-slide="prev"
                            >
                                <span
                                    class="carousel-control-prev-icon bg-danger rounded-circle p-3"
                                    aria-hidden="true"
                                ></span>
                                <span class="visually-hidden">Anterior</span>
                            </button>
                            <button
                                class="carousel-control-next"
                                type="button"
                                data-bs-target="#carouselAreas"
                                data-bs-slide="next"
                            >
                                <span
                                    class="carousel-control-next-icon bg-danger rounded-circle p-3"
                                    aria-hidden="true"
                                ></span>
                                <span class="visually-hidden">Siguiente</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Reseña, Misión y Visión -->
        <section id="nosotros" class="py-5 bg-body">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div
                            class="card h-100 border shadow-sm overflow-hidden bg-body"
                        >
                            <img
                                :src="'/images/resena.jpg'"
                                class="card-img-top"
                                alt="Reseña Histórica"
                                style="height: 200px; object-fit: cover"
                            />
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center gap-2 mb-3"
                                >
                                    <span class="punto-rojo"></span>
                                    <h4 class="card-title fw-bold mb-0">
                                        Reseña Histórica
                                    </h4>
                                </div>
                                <p
                                    class="card-text text-muted fs-6 text-justify"
                                >
                                    La Universidad Politécnica Territorial de
                                    Portuguesa (UPTP) "Juan de Jesús Montilla"
                                    nace con el firme compromiso de transformar
                                    activamente la educación tecnológica de la
                                    región, evolución integral desde su creación
                                    para formar ingenieros competentes e
                                    íntegros.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div
                            class="card h-100 border shadow-sm overflow-hidden bg-body"
                        >
                            <img
                                :src="'/images/mision.jpg'"
                                class="card-img-top"
                                alt="Misión"
                                style="height: 200px; object-fit: cover"
                            />
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center gap-2 mb-3"
                                >
                                    <span class="punto-rojo"></span>
                                    <h4 class="card-title fw-bold mb-0">
                                        Misión
                                    </h4>
                                </div>
                                <p
                                    class="card-text text-muted fs-6 text-justify"
                                >
                                    Formar ciudadanos y ciudadanas con sólidas
                                    capacidades científico-técnicas, valores
                                    éticos arraigados y profundo compromiso
                                    social para impulsar eficientemente el
                                    desarrollo tecnológico autónomo y soberano
                                    del país.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div
                            class="card h-100 border shadow-sm overflow-hidden bg-body"
                        >
                            <img
                                :src="'/images/vision.jpg'"
                                class="card-img-top"
                                alt="Visión"
                                style="height: 200px; object-fit: cover"
                            />
                            <div class="card-body">
                                <div
                                    class="d-flex align-items-center gap-2 mb-3"
                                >
                                    <span class="punto-rojo"></span>
                                    <h4 class="card-title fw-bold mb-0">
                                        Visión
                                    </h4>
                                </div>
                                <p
                                    class="card-text text-muted fs-6 text-justify"
                                >
                                    Posicionarse como una institución de
                                    educación universitaria de referencia
                                    nacional e internacional en la formación
                                    tecnológica, liderando la vanguardia en
                                    innovación, transformación digital e
                                    investigación sociocomunitaria.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Noticias -->
        <section class="py-5 bg-body-secondary" id="noticias">
            <div class="container">
                <div
                    class="d-flex justify-content-between align-items-center mb-5"
                >
                    <h2 class="fw-bold display-6 m-0">
                        Informática <span class="text-danger">Global</span>
                    </h2>
                    <span class="badge bg-danger rounded-pill px-3 fs-6"
                        >Actualidad 2026</span
                    >
                </div>
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0 bg-body">
                            <img
                                :src="'/images/photo-1550751827-4bd374c3f58b.avif'"
                                class="card-img-top"
                                style="height: 200px; object-fit: cover"
                                alt="Ciberseguridad"
                            />
                            <div class="card-body">
                                <h5 class="card-title fw-bold text-center">
                                    Ciberseguridad Autónoma
                                </h5>
                                <p
                                    class="card-text fs-6 text-justify text-muted"
                                >
                                    Sistemas de defensa global ahora detectan y
                                    bloquean de forma proactiva ataques de día
                                    cero en milisegundos mediante la integración
                                    de redes neuronales de comportamiento
                                    predictivo.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0 bg-body">
                            <img
                                :src="'/images/cuantica.jpg'"
                                class="card-img-top"
                                style="height: 200px; object-fit: cover"
                                alt="Cuántica"
                            />
                            <div class="card-body">
                                <h5 class="card-title fw-bold text-center">
                                    Computación Cuántica
                                </h5>
                                <p
                                    class="card-text fs-6 text-justify text-muted"
                                >
                                    Apertura formal de los primeros centers de
                                    procesamiento de datos cuánticos a nivel
                                    comercial orientados a resolver
                                    complejidades de alta escala en telemedicina
                                    y logística global.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0 bg-body">
                            <img
                                :src="'/images/photo-1573164713988-8665fc963095.avif'"
                                class="card-img-top"
                                style="height: 200px; object-fit: cover"
                                alt="IoT"
                            />
                            <div class="card-body">
                                <h5 class="card-title fw-bold text-center">
                                    Informática Sostenible
                                </h5>
                                <p
                                    class="card-text fs-6 text-justify text-muted"
                                >
                                    Grandes infraestructuras en la nube logran
                                    reducir de forma histórica su consumo
                                    hídrico general en un 95% gracias al
                                    despliegue masivo de tecnologías de
                                    enfriamiento seco por aireación.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer id="contacto" class="bg-dark text-white pt-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-lg-4 col-md-6">
                        <img
                            :src="'/images/images.png'"
                            alt="Logo UPTP"
                            height="75"
                            class="mb-3"
                        />
                        <p class="fs-6 text-secondary text-justify">
                            Universidad Politécnica Territorial de Portuguesa
                            (UPTP) "Juan de Jesús Montilla". Impulsando con
                            excelencia la soberanía tecnológica y el
                            conocimiento transformador en toda la región de
                            Portuguesa.
                        </p>
                        <div class="social-links fs-3 mt-3">
                            <a
                                href="https://www.instagram.com/uptpjjmontillaacarigua/"
                                target="_blank"
                                class="text-white me-3"
                                ><i class="bi bi-instagram"></i
                            ></a>
                            <a
                                href="https://www.facebook.com/profile.php?id=100064113455547"
                                target="_blank"
                                class="text-white me-3"
                                ><i class="bi bi-facebook"></i
                            ></a>
                            <a
                                href="https://x.com/UptpJuandeJesus/"
                                target="_blank"
                                class="text-white me-3"
                                ><i class="bi bi-twitter-x"></i
                            ></a>
                            <a
                                href="https://www.youtube.com/@educaciondigitaluptpjjmon6898"
                                target="_blank"
                                class="text-white"
                                ><i class="bi bi-youtube"></i
                            ></a>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <h4
                            class="fw-bold mb-4 border-bottom border-danger border-2 d-inline-block pb-1"
                        >
                            Navegación
                        </h4>
                        <ul class="list-unstyled footer-nav fs-5">
                            <li class="mb-2">
                                <a
                                    href="#hero"
                                    class="text-secondary text-decoration-none"
                                    ><i
                                        class="bi bi-chevron-right text-danger me-1"
                                    ></i>
                                    Inicio</a
                                >
                            </li>
                            <li class="mb-2">
                                <a
                                    href="#nosotros"
                                    class="text-secondary text-decoration-none"
                                    ><i
                                        class="bi bi-chevron-right text-danger me-1"
                                    ></i>
                                    Nuestra Universidad</a
                                >
                            </li>
                            <li class="mb-2">
                                <a
                                    href="#programas"
                                    class="text-secondary text-decoration-none"
                                    ><i
                                        class="bi bi-chevron-right text-danger me-1"
                                    ></i>
                                    PNF en Informática</a
                                >
                            </li>
                            <li class="mb-2">
                                <a
                                    href="#noticias"
                                    class="text-secondary text-decoration-none"
                                    ><i
                                        class="bi bi-chevron-right text-danger me-1"
                                    ></i>
                                    Actualidad Global</a
                                >
                            </li>
                            <li>
                                <a
                                    href="/"
                                    class="text-secondary text-decoration-none"
                                    ><i
                                        class="bi bi-chevron-right text-danger me-1"
                                    ></i>
                                    Iniciar Sesión</a
                                >
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-4 col-md-12">
                        <h4
                            class="fw-bold mb-4 border-bottom border-danger border-2 d-inline-block pb-1"
                        >
                            Contacto Directo
                        </h4>
                        <ul class="list-unstyled fs-5 text-secondary">
                            <li class="mb-3 d-flex align-items-start">
                                <i
                                    class="bi bi-geo-alt-fill text-danger me-3 fs-4"
                                ></i>
                                <span class="text-justify"
                                    >Avenida Circunvalación, Sector Bellas
                                    Artes, Acarigua, Estado Portuguesa,
                                    Venezuela.</span
                                >
                            </li>
                            <li class="mb-3 d-flex align-items-center">
                                <i
                                    class="bi bi-envelope-at-fill text-danger me-3 fs-4"
                                ></i>
                                <a
                                    href="mailto:contacto@uptp.edu.ve"
                                    class="text-secondary text-decoration-none"
                                    >contacto@uptp.edu.ve</a
                                >
                            </li>
                            <li class="d-flex align-items-center">
                                <i
                                    class="bi bi-clock-fill text-danger me-3 fs-4"
                                ></i>
                                <span
                                    >Atención Administrativa: 8:00 AM - 4:00
                                    PM</span
                                >
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="footer-bottom bg-black py-3 mt-5">
                <div class="container text-center">
                    <p class="mb-0 text-white fs-6">
                        © 2026 UPTP "Juan de Jesús Montilla" | Desarrollado por
                        el Grupo 1 - Trayecto II Sección 331
                    </p>
                </div>
            </div>
        </footer>

        <!-- Modal Carrera -->
        <div class="modal fade" id="modalCarrera" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-mortarboard-fill me-2"></i>Perfil
                            del Egresado en Informática
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-5 mb-3 mb-md-0">
                                <img
                                    :src="'/images/estudiantes.jpg'"
                                    class="img-fluid rounded-3 shadow-sm"
                                    alt="Estudiantes"
                                />
                            </div>
                            <div class="col-md-7 fs-5">
                                <h4 class="text-danger fw-bold">
                                    Competencias del Ingeniero
                                </h4>
                                <p class="text-justify text-muted">
                                    El Ingeniero en Informática egresado de la
                                    UPTP es un profesional integral altamente
                                    capacitado para liderar proyectos de
                                    desarrollo tecnológico, gestionar
                                    infraestructuras críticas de red y plantear
                                    soluciones lógicas e innovadoras con un
                                    fuerte enfoque social.
                                </p>
                                <h5 class="fw-bold mt-3">
                                    <i
                                        class="bi bi-check-all text-danger me-1"
                                    ></i
                                    >Cualidades Principales:
                                </h5>
                                <ul class="list-unstyled ps-2">
                                    <li>
                                        <i
                                            class="bi bi-dot text-danger fs-4"
                                        ></i>
                                        Gestión Avanzada de Bases de Datos
                                        Relacionales y NoSQL.
                                    </li>
                                    <li>
                                        <i
                                            class="bi bi-dot text-danger fs-4"
                                        ></i>
                                        Auditorías de Seguridad Lógica.
                                    </li>
                                    <li>
                                        <i
                                            class="bi bi-dot text-danger fs-4"
                                        ></i>
                                        Arquitectura y Programación de Sistemas
                                        Modernos.
                                    </li>
                                </ul>
                            </div>
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
                        <a
                            href="/images/malla.pdf"
                            download
                            class="btn btn-danger shadow-sm"
                        >
                            <i class="bi bi-file-earmark-pdf-fill me-2"></i
                            >Descargar Malla Curricular
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Sistema -->
        <div class="modal fade" id="modalSistema" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-pc-display-horizontal me-2"></i
                            >Sobre el Sistema de Gestión Digital
                        </h5>
                        <button
                            type="button"
                            class="btn-close btn-close-white"
                            data-bs-dismiss="modal"
                        ></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row align-items-center">
                            <div class="col-md-5 text-center mb-4 mb-md-0">
                                <img
                                    :src="'/images/sistema.webp'"
                                    class="img-fluid rounded-3 shadow-sm"
                                    alt="Sistema"
                                />
                            </div>
                            <div class="col-md-7 fs-5">
                                <h4 class="text-danger fw-bold mb-3">
                                    Eficiencia Institucional
                                </h4>
                                <p class="text-justify text-muted">
                                    Esta herramienta centralizada ha sido
                                    proyectada e implementada por estudiantes
                                    avanzados con la premisa clara de
                                    automatizar y optimizar los flujos
                                    administrativos internos de la institución.
                                </p>
                                <div class="mt-4">
                                    <div class="d-flex mb-3 align-items-start">
                                        <i
                                            class="bi bi-shield-fill-check text-success fs-3 me-3"
                                        ></i>
                                        <div>
                                            <h6 class="fw-bold mb-0 fs-5">
                                                Seguridad y Resguardo Integral
                                            </h6>
                                            <p class="small text-muted mb-0">
                                                Toda tu información académica
                                                permanece cifrada bajo estrictos
                                                esquemas lógicos.
                                            </p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-start">
                                        <i
                                            class="bi bi-cloud-arrow-up-fill text-primary fs-3 me-3"
                                        ></i>
                                        <div>
                                            <h6 class="fw-bold mb-0 fs-5">
                                                Cero Papeleo Manual
                                            </h6>
                                            <p class="small text-muted mb-0">
                                                Se elimina la obligatoriedad de
                                                carpetas en físico, dando paso a
                                                correcciones y asignaciones
                                                fluidas en tiempo real.
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button
                            type="button"
                            class="btn btn-outline-danger px-4"
                            data-bs-dismiss="modal"
                        >
                            Entendido
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from "vue";

const theme = ref("light");
const themeIcon = ref("bi bi-moon-stars-fill");

const toggleTheme = () => {
    if (theme.value === "light") {
        theme.value = "dark";
        themeIcon.value = "bi bi-sun-fill";
    } else {
        theme.value = "light";
        themeIcon.value = "bi bi-moon-stars-fill";
    }
};
</script>

<style scoped>
.punto-rojo {
    display: inline-block;
    width: 12px;
    height: 12px;
    background-color: #dc3545;
    border-radius: 50%;
    flex-shrink: 0;
}
.theme-toggle-btn {
    position: fixed;
    bottom: 25px;
    right: 25px;
    z-index: 1050;
    width: 55px;
    height: 55px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
    cursor: pointer;
}
.hero {
    background-color: var(--bg-hero, #ffffff);
    color: var(--text-hero, #212529);
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 1rem 3rem rgba(0, 0, 0, 0.15) !important;
}
section,
footer,
div[id] {
    scroll-margin-top: 85px;
}
</style>
