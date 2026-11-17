<!DOCTYPE html>
<html lang="es" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SISCON-PST — UPTP Juan de Jesús Montilla')</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- DataTables Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <style>
        :root {
            --primary-red: #dc3545;
            --primary-dark: #b02a37;
            --sidebar-bg: #1a1d23;
            --sidebar-hover: #262a33;
            --topbar-bg: #ffffff;
            --body-bg: #f4f6f9;
            --card-border: #e9ecef;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--body-bg);
            color: #2b303a;
            min-height: 100vh;
        }

        /* Sidebar Styling */
        #sidebar-wrapper {
            width: 270px;
            min-height: 100vh;
            background-color: var(--sidebar-bg);
            color: #e2e8f0;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1000;
            transition: all 0.3s ease;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
            overflow-y: auto;
        }

        .sidebar-brand {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            gap: 12px;
            background: rgba(0, 0, 0, 0.15);
        }

        .sidebar-brand h5 {
            margin: 0;
            font-weight: 800;
            font-size: 1.15rem;
            letter-spacing: -0.5px;
            color: #fff;
        }

        .sidebar-brand span {
            color: var(--primary-red);
        }

        .sidebar-menu {
            padding: 1rem 0;
        }

        .sidebar-menu .menu-header {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #6c757d;
            padding: 0.75rem 1.5rem 0.25rem;
            font-weight: 700;
        }

        .sidebar-menu .nav-link {
            color: #cbd5e1;
            padding: 0.75rem 1.5rem;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            font-size: 0.9rem;
            border-left: 4px solid transparent;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .sidebar-menu .nav-link i {
            font-size: 1.15rem;
            color: #94a3b8;
            transition: color 0.2s;
        }

        .sidebar-menu .nav-link:hover {
            color: #fff;
            background-color: var(--sidebar-hover);
            border-left-color: var(--primary-red);
        }

        .sidebar-menu .nav-link:hover i {
            color: var(--primary-red);
        }

        .sidebar-menu .nav-link.active {
            color: #fff;
            background: linear-gradient(90deg, rgba(220, 53, 69, 0.2) 0%, rgba(220, 53, 69, 0.05) 100%);
            border-left-color: var(--primary-red);
            font-weight: 600;
        }

        .sidebar-menu .nav-link.active i {
            color: var(--primary-red);
        }

        /* Main Content Wrapper */
        #page-content-wrapper {
            margin-left: 270px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        /* Topbar Styling */
        .topbar {
            background-color: var(--topbar-bg);
            border-bottom: 1px solid var(--card-border);
            padding: 0.85rem 2rem;
            position: sticky;
            top: 0;
            z-index: 990;
            box-shadow: 0 1px 3px rgba(0,0,0,0.03);
        }

        /* Modern Card Styling */
        .card-custom {
            border: 1px solid var(--card-border);
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
            background: #fff;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .btn-uptp-red {
            background-color: var(--primary-red);
            border-color: var(--primary-red);
            color: #fff;
            font-weight: 600;
        }

        .btn-uptp-red:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            color: #fff;
        }

        /* Badges */
        .badge-uptp {
            background-color: rgba(220, 53, 69, 0.1);
            color: var(--primary-red);
            border: 1px solid rgba(220, 53, 69, 0.2);
            font-weight: 600;
        }

        @media (max-width: 991.98px) {
            #sidebar-wrapper {
                margin-left: -270px;
            }
            #sidebar-wrapper.show {
                margin-left: 0;
            }
            #page-content-wrapper {
                margin-left: 0;
            }
        }
    </style>
    @yield('styles')
</head>
<body>
    <div class="d-flex" id="wrapper">
        <!-- Sidebar -->
        <aside id="sidebar-wrapper">
            <div class="sidebar-brand">
                <div class="rounded-circle bg-danger text-white d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="bi bi-mortarboard-fill fs-5"></i>
                </div>
                <div>
                    <h5>SISCON<span>-PST</span></h5>
                    <small class="text-secondary" style="font-size: 0.7rem; font-weight: 600;">UPTP JJ MONTILLA</small>
                </div>
            </div>

            <div class="sidebar-menu">
                <div class="menu-header">Navegación Principal</div>
                <a href="/coordinador" class="nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="me-2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    <span>Dashboard</span>
                </a>
                <a href="/coordinador/proyectos" class="nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M20 20a2 2 0 0 0 2-2V8a2 2 0 0 0-2-2h-7.9a2 2 0 0 1-1.69-.9L9.6 3.9A2 2 0 0 0 7.93 3H4a2 2 0 0 0-2 2v13a2 2 0 0 0 2 2Z"/></svg>
                    <span>Proyectos</span>
                </a>
                <a href="/coordinador/equipos" class="nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span>Equipos de Trabajo</span>
                </a>

                <div class="menu-header">Evaluación y Calificación</div>
                <a href="{{ route('evaluaciones.index') }}" class="nav-link {{ request()->routeIs('evaluaciones.*') ? 'active' : '' }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="me-2 text-danger"><circle cx="12" cy="8" r="6"/><path d="m15.477 12.89 1.515 8.526a.5.5 0 0 1-.81.47l-3.58-2.687a1 1 0 0 0-1.197 0l-3.586 2.686a.5.5 0 0 1-.81-.469l1.514-8.526"/></svg>
                    <span>Evaluación por Trayecto</span>
                </a>
                <a href="{{ route('evaluaciones.invitado') }}" target="_blank" class="nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="me-2 text-warning"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                    <span>Portal Jurado y Comunidad (PIN)</span>
                </a>
                <a href="/profesor/puntos-control" class="nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" x2="4" y1="22" y2="15"/></svg>
                    <span>Puntos de Control</span>
                </a>
                <a href="/profesor/socializaciones" class="nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M2 3h20"/><path d="M21 3v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V3"/><path d="m7 21 5-5 5 5"/></svg>
                    <span>Socializaciones</span>
                </a>

                <div class="menu-header">Académico y Documentos</div>
                <a href="/coordinador/estudiantes/buscar" class="nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="m16 11 2 2 4-4"/></svg>
                    <span>Expediente Estudiantil</span>
                </a>
                <a href="/coordinador/cartas" class="nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M15 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7Z"/><path d="M14 2v4a2 2 0 0 0 2 2h4"/><path d="M10 9H8"/><path d="M16 13H8"/><path d="M16 17H8"/></svg>
                    <span>Cartas de Presentación</span>
                </a>
                <a href="/coordinador/bitacora" class="nav-link">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" class="me-2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                    <span>Bitácora de Eventos</span>
                </a>
            </div>
        </aside>

        <!-- Page Content -->
        <div id="page-content-wrapper">
            <!-- Topbar Navigation -->
            <header class="topbar d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-sm btn-outline-secondary d-lg-none" id="sidebarToggle">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb m-0 small">
                            <li class="breadcrumb-item"><a href="/" class="text-decoration-none text-muted">SISCON-PST</a></li>
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <span class="badge bg-danger px-3 py-2 rounded-pill small">
                        <i class="bi bi-shield-check me-1"></i> PNFI - UPTP
                    </span>
                    @auth
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border d-flex align-items-center gap-2 rounded-pill px-3 py-1 dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-6 text-danger"></i>
                            <span class="fw-semibold small">{{ auth()->user()?->usu_primer_nombre ?? 'Usuario' }} {{ auth()->user()?->usu_primer_apellido ?? '' }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item small" href="/coordinador/configuracion"><i class="bi bi-gear me-2"></i>Configuración</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item small text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1">
                        <i class="bi bi-box-arrow-in-right me-1"></i> Iniciar Sesión
                    </a>
                    @endauth
                </div>
            </header>

            <!-- Main Body Container -->
            <main class="flex-grow-1 p-3 p-md-4">
                <!-- Alerts / Flashes -->
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="bi bi-check-circle-fill me-2 fs-6"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
                        <div class="fw-bold mb-1"><i class="bi bi-exclamation-octagon-fill me-2"></i>Se encontraron los siguientes errores:</div>
                        <ul class="mb-0 ps-3 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="bg-white border-top py-3 px-4 text-center text-muted small">
                SISCON-PST &bull; Universidad Politécnica Territorial del Estado Portuguesa "Juan de Jesús Montilla", Sede Acarigua.
            </footer>
        </div>
    </div>

    <!-- Scripts: jQuery, Bootstrap Bundle, DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Sidebar toggle for mobile
            $('#sidebarToggle').on('click', function(e) {
                e.preventDefault();
                $('#sidebar-wrapper').toggleClass('show');
            });

            // Initialize standard DataTables if present
            if ($('.datatable').length) {
                $('.datatable').DataTable({
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
                    },
                    responsive: true,
                    pageLength: 10
                });
            }
        });
    </script>
    @yield('scripts')
</body>
</html>
