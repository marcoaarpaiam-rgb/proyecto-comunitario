@extends('layouts.admin')

@section('title', 'Evaluación Cualitativa y Cuantitativa por Trayecto — SISCON-PST')

@section('breadcrumb')
    <li class="breadcrumb-item active" aria-current="page">Evaluación por Trayecto</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h4 class="fw-bold m-0 text-dark">
            <i class="bi bi-award-fill text-danger me-2"></i>Motor de Evaluación de Proyectos (Semestres 1 y 2)
        </h4>
        <p class="text-muted small m-0 mt-1">
            Gestión de rúbricas ponderadas, calificaciones cuantitativas sobre 20 pts y veredictos cualitativos oficiales del PNFI.
        </p>
    </div>
    <div class="d-flex gap-2">
        <span class="badge bg-light text-dark border p-2 d-flex align-items-center gap-2">
            <i class="bi bi-info-circle text-danger"></i>
            <span>Semestre 1: <strong>40%</strong> &bull; Semestre 2: <strong>60%</strong></span>
        </span>
    </div>
</div>

<!-- Banner del Portal de Jurado y Vocero Comunitario -->
<div class="card card-custom mb-4 shadow-sm border-0" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
    <div class="card-body p-4 text-white d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <span class="badge bg-danger px-3 py-1 mb-2 text-uppercase fw-bold" style="letter-spacing: 1px;">Evaluación en Vivo (Defensa / Socialización)</span>
            <h5 class="fw-bold mb-1 text-white">Portal de Acceso para Jurado y Vocero Comunitario</h5>
            <p class="text-white-50 small mb-0">
                Los jurados y el vocero comunitario pueden ingresar a evaluar en tiempo real sin credenciales de usuario, utilizando su <strong>Cédula</strong> y el <strong>PIN de Defensa</strong> asignado a cada proyecto.
            </p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('evaluaciones.invitado') }}" target="_blank" class="btn btn-danger fw-bold px-4 py-2 shadow rounded-pill">
                <i class="bi bi-box-arrow-up-right me-1"></i> Abrir Portal de Evaluación con PIN
            </a>
        </div>
    </div>
</div>

<div class="card card-custom">
    <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h6 class="m-0 fw-bold text-dark">
            <i class="bi bi-kanban me-2 text-danger"></i>Proyectos Sociotecnológicos Activos
        </h6>
        <span class="small text-muted">Total registrados: <strong>{{ $proyectos->count() }}</strong></span>
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-hover align-middle datatable w-100">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th>Código / Equipo</th>
                        <th>PIN Defensa</th>
                        <th>Título del Proyecto</th>
                        <th>Trayecto / Sección</th>
                        <th>Comunidad</th>
                        <th class="text-center">Semestre 1 (40%)</th>
                        <th class="text-center">Semestre 2 (60%)</th>
                        <th class="text-center">Resultado Final</th>
                        <th class="text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($proyectos as $p)
                        <tr>
                            <td>
                                <strong class="text-danger d-block">{{ $p->codigo_equipo }}</strong>
                                <span class="badge bg-secondary-subtle text-secondary small">ID: #{{ $p->id_proyecto }}</span>
                            </td>
                            <td>
                                @if ($p->pin_defensa)
                                    <span class="badge bg-warning-subtle text-dark border border-warning font-monospace px-2 py-1 fs-6 fw-bold" title="PIN de defensa para evaluación en vivo">
                                        <i class="bi bi-key-fill text-danger me-1"></i>{{ $p->pin_defensa }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">Pendiente</span>
                                @endif
                            </td>
                            <td style="max-width: 250px;">
                                <div class="fw-semibold text-truncate" title="{{ $p->titulo_proyecto }}">
                                    {{ $p->titulo_proyecto }}
                                </div>
                                <small class="text-muted text-truncate d-block" style="max-width: 250px;">
                                    {{ $p->resumen }}
                                </small>
                            </td>
                            <td>
                                <span class="fw-medium">{{ $p->trayecto_nombre ?? 'Trayecto N/A' }}</span>
                                <div class="small text-muted">Sec: {{ $p->seccion_codigo ?? '—' }}</div>
                            </td>
                            <td>
                                <span class="small text-dark">{{ $p->comunidad_nombre ?? 'Sin comunidad' }}</span>
                            </td>
                            <td class="text-center">
                                @if ($p->semestre1 && $p->semestre1->estado_semestre === 'Culminado')
                                    <span class="badge bg-success px-2 py-1 rounded-pill">
                                        {{ number_format($p->semestre1->nota_cuantitativa_semestre, 2) }} pts
                                    </span>
                                    <div class="small text-success fw-bold" style="font-size: 0.72rem;">Culminado</div>
                                @elseif ($p->semestre1 && $p->semestre1->estado_semestre === 'En Evaluacion')
                                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">En Evaluación</span>
                                @else
                                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill">En Curso</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($p->semestre2 && $p->semestre2->estado_semestre === 'Culminado')
                                    <span class="badge bg-success px-2 py-1 rounded-pill">
                                        {{ number_format($p->semestre2->nota_cuantitativa_semestre, 2) }} pts
                                    </span>
                                    <div class="small text-success fw-bold" style="font-size: 0.72rem;">Culminado</div>
                                @elseif ($p->semestre2 && $p->semestre2->estado_semestre === 'En Evaluacion')
                                    <span class="badge bg-warning text-dark px-2 py-1 rounded-pill">En Evaluación</span>
                                @else
                                    <span class="badge bg-light text-muted border px-2 py-1 rounded-pill">En Curso</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if ($p->acta_cerrada)
                                    <div class="fw-bold text-danger fs-6">
                                        {{ number_format($p->calificacion_definitiva_cuantitativa, 2) }} pts
                                    </div>
                                    <span class="badge {{ $p->calificacion_definitiva_cuantitativa >= 16 ? 'bg-success' : 'bg-danger' }} rounded-pill small" style="font-size: 0.7rem;">
                                        {{ $p->calificacion_definitiva_cualitativa }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border">Pendiente</span>
                                @endif
                            </td>
                            <td class="text-center text-nowrap">
                                <a href="{{ route('evaluaciones.recorrido', $p->id_proyecto) }}" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-clipboard2-check me-1"></i>Ver Recorrido
                                </a>
                                @if ($p->acta_cerrada)
                                    <a href="{{ route('evaluaciones.pdf', $p->id_proyecto) }}" target="_blank" class="btn btn-sm btn-danger ms-1" title="Descargar Acta Final PDF">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
