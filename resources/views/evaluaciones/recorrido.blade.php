@extends('layouts.admin')

@section('title', 'Recorrido de Evaluación — ' . $proyecto->codigo_equipo)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('evaluaciones.index') }}" class="text-decoration-none text-muted">Evaluaciones</a></li>
    <li class="breadcrumb-item active" aria-current="page">{{ $proyecto->codigo_equipo }}</li>
@endsection

@section('content')
<!-- Ficha Informativa del Proyecto -->
<div class="card card-custom mb-4 border-start border-danger border-4">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-danger fs-6 px-3 py-1 rounded-pill">{{ $proyecto->codigo_equipo }}</span>
                    <span class="badge bg-secondary-subtle text-secondary fs-6 px-3 py-1 rounded-pill">{{ $proyecto->trayecto_nombre ?? 'Trayecto' }}</span>
                    <span class="badge bg-light text-dark border fs-6 px-3 py-1 rounded-pill">Sección {{ $proyecto->seccion_codigo ?? '—' }}</span>
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $proyecto->titulo_proyecto }}</h4>
                <p class="text-muted small mb-3" style="max-width: 850px;">{{ $proyecto->resumen }}</p>

                <div class="d-flex align-items-center gap-4 flex-wrap text-muted small">
                    <div><i class="bi bi-building text-danger me-1"></i> Comunidad: <strong>{{ $proyecto->comunidad_nombre ?? 'N/A' }}</strong></div>
                    <div><i class="bi bi-geo-alt text-danger me-1"></i> Ubicación: {{ $proyecto->comunidad_ubicacion ?? 'N/A' }}</div>
                    <div><i class="bi bi-calendar-event text-danger me-1"></i> Aprobado: {{ $proyecto->fecha_aprobacion ?? 'N/A' }}</div>
                </div>
            </div>

            <!-- Integrantes del Equipo -->
            <div class="bg-light p-3 rounded border text-start" style="min-width: 260px;">
                <div class="fw-bold small text-muted text-uppercase mb-2">
                    <i class="bi bi-people-fill text-danger me-1"></i> Integrantes del Equipo
                </div>
                <ul class="list-unstyled mb-0 small">
                    @forelse ($integrantes as $int)
                        <li class="py-1 border-bottom border-light-subtle d-flex justify-content-between align-items-center">
                            <span>{{ $int->primer_nombre }} {{ $int->primer_apellido }}</span>
                            <span class="badge {{ $int->es_lider ? 'bg-danger' : 'bg-secondary' }} rounded-pill" style="font-size: 0.65rem;">
                                {{ $int->es_lider ? 'Líder' : 'C.I. ' . $int->cedula }}
                            </span>
                        </li>
                    @empty
                        <li class="text-muted small">Sin integrantes asignados</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Línea de Tiempo y Recorrido de los 2 Semestres -->
<div class="row g-4 mb-4">
    @foreach ($semestres as $sem)
        @php
            $esS1 = ($sem->semestre === 'Semestre 1');
            $pesoTrayecto = $esS1 ? 40 : 60;
            $notaActual = (float) $sem->nota_cuantitativa_semestre;
            $evaluacionesEmitidas = $sem->evaluaciones->keyBy('rol_evaluador');
        @endphp
        <div class="col-lg-6">
            <div class="card card-custom h-100 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-danger rounded-pill px-3 py-1 mb-1">{{ $sem->semestre }}</span>
                        <h5 class="fw-bold m-0 text-dark">
                            Ponderación en Trayecto: <span class="text-danger">{{ $pesoTrayecto }}%</span>
                        </h5>
                    </div>
                    <div>
                        @if ($sem->estado_semestre === 'Culminado')
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill fw-bold">
                                <i class="bi bi-check-circle-fill me-1"></i> Culminado
                            </span>
                        @elseif ($sem->estado_semestre === 'En Evaluacion')
                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-3 py-2 rounded-pill fw-bold">
                                <i class="bi bi-hourglass-split me-1"></i> En Evaluación
                            </span>
                        @else
                            <span class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-bold">
                                <i class="bi bi-clock me-1"></i> En Curso
                            </span>
                        @endif
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- Calificación Semestral Cuantitativa -->
                    <div class="bg-light p-3 rounded mb-4 border d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small text-muted text-uppercase fw-bold">Nota Cuantitativa del {{ $sem->semestre }}</div>
                            <div class="text-muted small">Calculada sobre una base de 20 puntos</div>
                        </div>
                        <div class="text-end">
                            <span class="fs-3 fw-bold {{ $notaActual >= 16 ? 'text-success' : ($notaActual > 0 ? 'text-danger' : 'text-muted') }}">
                                {{ number_format($notaActual, 2) }}
                            </span>
                            <span class="text-muted small">/ 20.00 pts</span>
                        </div>
                    </div>

                    <!-- Roles Evaluadores según Semestre -->
                    <h6 class="fw-bold small text-muted text-uppercase mb-3">
                        <i class="bi bi-person-check-fill text-danger me-1"></i> Evaluadores Requeridos
                    </h6>

                    <div class="d-flex flex-column gap-3 mb-4">
                        <!-- 1. Tutor -->
                        @php
                            $evalTutor = $evaluacionesEmitidas->get('tutor');
                            $pesoTutor = $esS1 ? 60 : 30;
                        @endphp
                        <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <div class="fw-bold text-dark">
                                    <i class="bi bi-mortarboard text-danger me-2"></i>Tutor Académico
                                    <span class="badge bg-light text-dark border ms-1 small">Peso: {{ $pesoTutor }}%</span>
                                </div>
                                <div class="small text-muted mt-1">
                                    @if ($evalTutor)
                                        <span class="badge bg-success-subtle text-success me-1">✓ Evaluado</span>
                                        Calificación: <strong>{{ number_format($evalTutor->subtotal_cuantitativo, 2) }} pts</strong>
                                        &bull; Nivel: <em>{{ $evalTutor->nivel_logro_cualitativo }}</em>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Pendiente de evaluación</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('evaluaciones.evaluar', ['id_recorrido' => $sem->id_recorrido, 'rol_evaluador' => 'tutor']) }}" class="btn btn-sm {{ $evalTutor ? 'btn-outline-secondary' : 'btn-danger' }}">
                                    <i class="bi {{ $evalTutor ? 'bi-pencil' : 'bi-clipboard-check' }} me-1"></i>
                                    {{ $evalTutor ? 'Modificar' : 'Evaluar Rúbrica' }}
                                </a>
                            </div>
                        </div>

                        <!-- 2. Comunidad -->
                        @php
                            $evalCom = $evaluacionesEmitidas->get('comunidad');
                            $pesoCom = $esS1 ? 40 : 20;
                        @endphp
                        <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <div class="fw-bold text-dark">
                                    <i class="bi bi-people text-danger me-2"></i>Vocero Comunitario
                                    <span class="badge bg-light text-dark border ms-1 small">Peso: {{ $pesoCom }}%</span>
                                </div>
                                <div class="small text-muted mt-1">
                                    @if ($evalCom)
                                        <span class="badge bg-success-subtle text-success me-1">✓ Evaluado</span>
                                        Calificación: <strong>{{ number_format($evalCom->subtotal_cuantitativo, 2) }} pts</strong>
                                        &bull; Nivel: <em>{{ $evalCom->nivel_logro_cualitativo }}</em>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary">Pendiente de evaluación</span>
                                    @endif
                                </div>
                            </div>
                            <div>
                                <a href="{{ route('evaluaciones.evaluar', ['id_recorrido' => $sem->id_recorrido, 'rol_evaluador' => 'comunidad']) }}" class="btn btn-sm {{ $evalCom ? 'btn-outline-secondary' : 'btn-danger' }}">
                                    <i class="bi {{ $evalCom ? 'bi-pencil' : 'bi-clipboard-check' }} me-1"></i>
                                    {{ $evalCom ? 'Modificar' : 'Evaluar Rúbrica' }}
                                </a>
                            </div>
                        </div>

                        <!-- 3. Jurado (Solo en Semestre 2) -->
                        @if (!$esS1)
                            @php
                                $evalJurado = $evaluacionesEmitidas->get('jurado');
                            @endphp
                            <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="fw-bold text-dark">
                                        <i class="bi bi-award text-danger me-2"></i>Jurados Evaluadores (Socialización Final)
                                        <span class="badge bg-light text-dark border ms-1 small">Peso: 50%</span>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        @if ($evalJurado)
                                            <span class="badge bg-success-subtle text-success me-1">✓ Evaluado</span>
                                            Calificación Promedio: <strong>{{ number_format($sem->nota_jurado ?? $evalJurado->subtotal_cuantitativo, 2) }} pts</strong>
                                            &bull; Nivel: <em>{{ $evalJurado->nivel_logro_cualitativo }}</em>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Pendiente de evaluación</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('evaluaciones.evaluar', ['id_recorrido' => $sem->id_recorrido, 'rol_evaluador' => 'jurado']) }}" class="btn btn-sm {{ $evalJurado ? 'btn-outline-secondary' : 'btn-danger' }}">
                                        <i class="bi {{ $evalJurado ? 'bi-pencil' : 'bi-clipboard-check' }} me-1"></i>
                                        {{ $evalJurado ? 'Modificar' : 'Evaluar Rúbrica' }}
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Síntesis Cualitativa del Semestre -->
                    <div class="border rounded p-3 bg-light">
                        <div class="small fw-bold text-dark mb-1">
                            <i class="bi bi-journal-text text-danger me-1"></i> Síntesis Cualitativa del {{ $sem->semestre }}
                        </div>
                        <p class="small text-muted m-0" style="white-space: pre-wrap;">{{ $sem->valoracion_cualitativa_semestre ?: 'La valoración cualitativa se generará automáticamente en cuanto los evaluadores consoliden sus rúbricas.' }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<!-- Acta Final y Consolidación del Trayecto -->
<div class="card card-custom border-danger">
    <div class="card-header bg-danger text-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-patch-check-fill fs-5"></i>
            <h5 class="fw-bold m-0">Consolidación Definitiva y Acta Final de Evaluación del Trayecto</h5>
        </div>
        <div>
            @if ($acta && $acta->cerrada)
                <span class="badge bg-white text-danger px-3 py-2 rounded-pill fw-bold">
                    <i class="bi bi-shield-lock-fill me-1"></i> ACTA CERRADA Y REGISTRADA
                </span>
            @else
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                    <i class="bi bi-hourglass-split me-1"></i> En Espera de Culminación de Semestres
                </span>
            @endif
        </div>
    </div>

    <div class="card-body p-4">
        @if ($acta && $acta->cerrada)
            <div class="row align-items-center g-4">
                <div class="col-md-3 text-center border-end">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Calificación Definitiva</div>
                    <div class="display-4 fw-bold text-danger">
                        {{ number_format($acta->calificacion_definitiva_cuantitativa, 2) }}
                    </div>
                    <div class="text-muted small">sobre 20.00 pts</div>
                </div>

                <div class="col-md-5">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Veredicto y Mención Oficial</div>
                    <div class="fs-5 fw-bold text-dark mb-2">
                        <span class="badge {{ $acta->calificacion_definitiva_cuantitativa >= 16 ? 'bg-success' : 'bg-danger' }} px-3 py-2 rounded-pill">
                            {{ $acta->calificacion_definitiva_cualitativa }}
                        </span>
                    </div>
                    <div class="small text-muted">
                        Fórmula aplicada: <strong>(S1 &times; 40%) + (S2 &times; 60%)</strong>
                        <br>
                        S1: {{ number_format($acta->nota_semestre_1, 2) }} pts &bull; S2: {{ number_format($acta->nota_semestre_2, 2) }} pts
                    </div>
                </div>

                <div class="col-md-4 text-md-end">
                    <a href="{{ route('evaluaciones.pdf', $proyecto->id_proyecto) }}" target="_blank" class="btn btn-danger btn-lg px-4 shadow-sm fw-bold">
                        <i class="bi bi-file-earmark-pdf-fill me-2"></i>Descargar Acta Oficial PDF
                    </a>
                    <div class="small text-muted mt-2">
                        <i class="bi bi-shield-check text-success me-1"></i> Documento Institucional con Hash
                    </div>
                </div>
            </div>

            <!-- Código Hash de Verificación -->
            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="small text-muted font-monospace">
                    <i class="bi bi-fingerprint me-1"></i> Hash SHA-256: <strong>{{ $acta->hash_verificacion }}</strong>
                </div>
                <div>
                    <a href="{{ route('evaluaciones.verificar', ['hash' => $acta->hash_verificacion]) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-search me-1"></i>Verificar Autenticidad Online
                    </a>
                </div>
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-info-circle fs-1 text-danger d-block mb-3"></i>
                <h6 class="fw-bold text-dark">El Acta Definitiva se generará automáticamente</h6>
                <p class="small mx-auto" style="max-width: 600px;">
                    Para realizar el cierre institucional del trayecto, el <strong>Semestre 1 (40%)</strong> y el <strong>Semestre 2 (60%)</strong> deben completar todas sus evaluaciones (Tutor, Comunidad y Jurado).
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
