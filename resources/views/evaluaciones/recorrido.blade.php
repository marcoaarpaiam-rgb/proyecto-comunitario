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
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge bg-danger fs-6 px-3 py-1 rounded-pill">{{ $proyecto->codigo_equipo }}</span>
                    <span class="badge bg-secondary-subtle text-secondary fs-6 px-3 py-1 rounded-pill">{{ $proyecto->trayecto_nombre ?? 'Trayecto' }}</span>
                    <span class="badge bg-light text-dark border fs-6 px-3 py-1 rounded-pill">Sección {{ $proyecto->seccion_codigo ?? '—' }}</span>
                    @php
                        $primerDigito = (int) substr((string)$proyecto->seccion_codigo, 0, 1);
                        $esImpar = ($primerDigito % 2 !== 0);
                    @endphp
                    @if ($esImpar)
                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle rounded-pill">Semestre Impar</span>
                    @else
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">Semestre Par</span>
                    @endif
                </div>
                <h4 class="fw-bold text-dark mb-1">{{ $proyecto->titulo_proyecto }}</h4>
                <p class="text-muted small mb-3" style="max-width: 850px;">{{ $proyecto->resumen }}</p>

                <div class="d-flex align-items-center gap-4 flex-wrap text-muted small">
                    <div><i class="bi bi-building text-danger me-1"></i> Comunidad: <strong>{{ $proyecto->comunidad_nombre ?? 'N/A' }}</strong></div>
                    <div><i class="bi bi-geo-alt text-danger me-1"></i> Ubicación: {{ $proyecto->comunidad_ubicacion ?? 'N/A' }}</div>
                    <div><i class="bi bi-person-badge text-danger me-1"></i> Docente/Tutor: {{ $proyecto->tutor_nombre ?? 'Docente Asignado' }} {{ $proyecto->tutor_apellido ?? '' }}</div>
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

@if ($tieneFechaSocializacion)
    @php
        $cdEntregado = str_contains($socializacionFinal->soc_observaciones ?? '', '[CD/INFORME ENTREGADO]');
    @endphp
    <!-- Header Informativo de la Fecha de Socialización Final Programada -->
    <div class="card shadow-sm border-0 mb-3 bg-light border-start border-success border-4">
        <div class="card-body p-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                    <span class="badge bg-success px-3 py-1 text-uppercase fw-bold">
                        <i class="bi bi-calendar-check-fill me-1"></i> Fecha de Socialización Final Programada
                    </span>
                    @if ($cdEntregado)
                        <span class="badge bg-dark text-white px-3 py-1 fw-bold">
                            <i class="bi bi-disc-fill me-1"></i> CD / Informe Entregado
                        </span>
                    @else
                        <span class="badge bg-warning text-dark px-3 py-1 fw-bold">
                            <i class="bi bi-exclamation-circle me-1"></i> CD / Informe Pendiente
                        </span>
                    @endif
                </div>
                <div class="fs-6 fw-bold text-dark mt-1">
                    <i class="bi bi-calendar-event text-danger me-1"></i> {{ \Carbon\Carbon::parse($socializacionFinal->soc_fecha)->format('d/m/Y') }}
                    @if ($socializacionFinal->soc_hora)
                        &bull; <i class="bi bi-clock text-danger me-1"></i> {{ $socializacionFinal->soc_hora }}
                    @endif
                    &bull; <i class="bi bi-geo-alt text-danger me-1"></i> {{ $socializacionFinal->soc_lugar }}
                </div>
            </div>
            <div>
                <button class="btn btn-sm btn-outline-secondary rounded-pill" type="button" data-bs-toggle="collapse" data-bs-target="#formCambiarFecha">
                    <i class="bi bi-pencil me-1"></i> Reprogramar Fecha / Lugar / CD
                </button>
            </div>
        </div>
        <div class="collapse px-3 pb-3" id="formCambiarFecha">
            <form action="{{ route('evaluaciones.socializacion.guardar', $proyecto->id_proyecto) }}" method="POST" class="p-3 bg-white border rounded">
                @csrf
                <div class="row g-2 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">Nueva Fecha <span class="text-danger">*</span></label>
                        <input type="date" name="fecha" value="{{ $socializacionFinal->soc_fecha }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold small">Hora</label>
                        <input type="time" name="hora" value="{{ $socializacionFinal->soc_hora }}" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">Lugar <span class="text-danger">*</span></label>
                        <input type="text" name="lugar" value="{{ $socializacionFinal->soc_lugar }}" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-3">
                        <div class="form-check p-2 border rounded bg-light" style="padding-left: 2rem !important;">
                            <input class="form-check-input" type="checkbox" name="entrega_cd_informe" value="1" id="chkCdEditRecorrido" {{ $cdEntregado ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold small text-dark" for="chkCdEditRecorrido">
                                Entregó CD / Informe
                            </label>
                        </div>
                    </div>
                    <div class="col-12 text-end mt-2">
                        <button type="submit" class="btn btn-sm btn-danger fw-bold rounded-pill px-3">
                            <i class="bi bi-save me-1"></i> Guardar Cambios de Programación
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Card de Acceso Rápido con PIN para Defensa en Vivo (Solo visible con fecha guardada) -->
    <div class="card shadow-sm border-0 mb-4 bg-dark text-white rounded-3 overflow-hidden">
        <div class="card-body p-3 p-md-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-danger bg-opacity-25 rounded-circle text-danger fs-2 d-none d-sm-block">
                    <i class="bi bi-key-fill"></i>
                </div>
                <div>
                    <span class="badge bg-danger px-2 py-1 small text-uppercase">Acceso Rápido con PIN — Socialización Final</span>
                    <div class="d-flex align-items-baseline gap-2 mt-1">
                        <span class="fs-1 fw-bold text-warning font-monospace" style="letter-spacing: 4px;">
                            {{ $proyecto->pin_defensa ?: '123456' }}
                        </span>
                        <button class="btn btn-sm btn-outline-light border-0 text-white-50" onclick="navigator.clipboard.writeText('{{ $proyecto->pin_defensa }}'); alert('PIN copiado al portapapeles');" title="Copiar PIN">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <div class="text-white-50 small">
                        @if (!empty($proyecto->token_jurado))
                            Enlace temporal de 12 horas activo para Jurados: 
                            <a href="{{ url('/evaluacion-jurado/' . $proyecto->token_jurado) }}" target="_blank" class="text-warning text-decoration-underline fw-bold">/evaluacion-jurado/{{ Str::limit($proyecto->token_jurado, 12) }}</a>
                        @else
                            Comparta este PIN y genere el enlace temporal de 12 horas en la gestión de equipos para calificar en tiempo real.
                        @endif
                    </div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <form action="{{ route('evaluaciones.pin.generar', $proyecto->id_proyecto) }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-light px-3" onclick="return confirm('¿Desea regenerar el PIN de defensa para este proyecto?');">
                        <i class="bi bi-arrow-repeat me-1"></i> Regenerar PIN
                    </button>
                </form>
                @if (!empty($proyecto->token_jurado))
                    <a href="{{ url('/evaluacion-jurado/' . $proyecto->token_jurado) }}" target="_blank" class="btn btn-sm btn-danger px-3 fw-bold">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Abrir Panel Jurado (12h)
                    </a>
                @endif
            </div>
        </div>
    </div>
@else
    <!-- Alerta Bloqueante: Fecha de Socialización Final Pendiente (PIN y Módulo Ocultos) -->
    <div class="card border-warning shadow-sm mb-4" style="background: #fffdf5; border-left: 6px solid #ffc107 !important; border-radius: 12px;">
        <div class="card-body p-4">
            <div class="d-flex align-items-start gap-3 flex-wrap">
                <div class="p-3 bg-warning text-dark rounded-circle fs-3 d-none d-sm-block">
                    <i class="bi bi-calendar-x-fill"></i>
                </div>
                <div style="flex: 1; min-width: 280px;">
                    <span class="badge bg-warning text-dark px-3 py-1 mb-2 text-uppercase fw-bold">
                        Fecha de Socialización Final Pendiente
                    </span>
                    <h5 class="fw-bold text-dark mb-1">
                        El PIN de Defensa y la Evaluación Final están temporalmente inactivos
                    </h5>
                    <p class="text-muted small mb-3" style="line-height: 1.6;">
                        Por directriz institucional del PNFI y control estricto de auditoría académica, el <strong>PIN de defensa</strong> para jurados y vocero comunitario, así como el <strong>registro de notas de la Socialización Final</strong> (Semestre 2), <strong>solo se habilitan cuando se haya programado y guardado la Fecha y Lugar</strong> del acto de defensa.
                    </p>

                    <!-- Formulario Contextual para Registrar Fecha y Lugar -->
                    <form action="{{ route('evaluaciones.socializacion.guardar', $proyecto->id_proyecto) }}" method="POST" class="p-3 bg-white border rounded shadow-sm">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label fw-bold small text-dark">Fecha de la Socialización Final <span class="text-danger">*</span></label>
                                <input type="date" name="fecha" class="form-control" required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold small text-dark">Hora del Acto</label>
                                <input type="time" name="hora" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold small text-dark">Lugar del Acto <span class="text-danger">*</span></label>
                                <input type="text" name="lugar" class="form-control" placeholder="Ej: Aula 4 UPTP, Auditorio, Casa Comunal" required>
                            </div>
                            <div class="col-md-3">
                                <div class="form-check p-2 border rounded bg-light" style="padding-left: 2rem !important;">
                                    <input class="form-check-input" type="checkbox" name="entrega_cd_informe" value="1" id="chkCdNewRecorrido">
                                    <label class="form-check-label fw-bold small text-dark" for="chkCdNewRecorrido">
                                        Entregó CD / Informe
                                    </label>
                                </div>
                            </div>
                            <div class="col-12 text-end mt-3">
                                <button type="submit" class="btn btn-danger fw-bold px-4 py-2 rounded-pill shadow-sm">
                                    <i class="bi bi-calendar-check-fill me-1"></i>
                                    Guardar Fecha y Habilitar Evaluación Final
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif

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
            <div class="card card-custom h-100 shadow-sm border-{{ $sem->estado_semestre === 'Culminado' ? 'success' : 'light' }}">
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
                            <div class="text-muted small">
                                @if ($esS1)
                                    Docente (60%) + Comunidad (40%)
                                @else
                                    Docente (40%) + Jurado (30%) + Comunidad (20%) + Estudiante (10%)
                                @endif
                            </div>
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
                        <i class="bi bi-person-check-fill text-danger me-1"></i> Actores Evaluadores ({{ $esS1 ? '2 Actores' : '4 Actores Oficiales UPTP' }})
                    </h6>

                    <div class="d-flex flex-column gap-3 mb-4">
                        @if ($esS1)
                            <!-- Semestre 1: Docente (60%) y Comunidad (40%) -->
                            <!-- 1. Docente / Tutor S1 -->
                            @php
                                $evalDocente = $evaluacionesEmitidas->get('docente_proyecto') ?? $evaluacionesEmitidas->get('tutor');
                            @endphp
                            <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="fw-bold text-dark">
                                        <i class="bi bi-mortarboard text-danger me-2"></i>Docente de Proyecto / Tutor
                                        <span class="badge bg-light text-dark border ms-1 small">Peso: 60%</span>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        @if ($evalDocente)
                                            <span class="badge bg-success-subtle text-success me-1">✓ Evaluado</span>
                                            Calificación: <strong>{{ number_format($evalDocente->subtotal_cuantitativo, 2) }} pts</strong>
                                            &bull; Nivel: <em>{{ $evalDocente->nivel_logro_cualitativo }}</em>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Pendiente de evaluación</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('evaluaciones.evaluar', ['id_recorrido' => $sem->id_recorrido, 'rol_evaluador' => 'docente_proyecto']) }}" class="btn btn-sm {{ $evalDocente ? 'btn-outline-secondary' : 'btn-danger' }}">
                                        <i class="bi {{ $evalDocente ? 'bi-pencil' : 'bi-clipboard-check' }} me-1"></i>
                                        {{ $evalDocente ? 'Modificar' : 'Evaluar Rúbrica' }}
                                    </a>
                                </div>
                            </div>

                            <!-- 2. Comunidad S1 -->
                            @php
                                $evalCom = $evaluacionesEmitidas->get('comunidad');
                            @endphp
                            <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="fw-bold text-dark">
                                        <i class="bi bi-people text-danger me-2"></i>Vocero Comunitario
                                        <span class="badge bg-light text-dark border ms-1 small">Peso: 40%</span>
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

                        @else
                            <!-- Semestre 2: 4 Actores Oficiales UPTP (Docente 40%, Jurado 30%, Comunidad 20%, Estudiante 10%) -->
                            @if (!$tieneFechaSocializacion)
                                <div class="p-4 rounded border bg-light text-center my-3">
                                    <i class="bi bi-lock-fill text-warning fs-1 d-block mb-2"></i>
                                    <h6 class="fw-bold text-dark">Evaluación Final del Semestre 2 Inactiva</h6>
                                    <p class="small text-muted mb-0">
                                        Para habilitar el registro de notas por rúbrica de los 4 actores y el PIN de jurado/comunidad, debe programar y guardar la <strong>Fecha y Lugar de la Socialización Final</strong> en el formulario superior.
                                    </p>
                                </div>
                            @else
                            <!-- 1. Docente de Proyecto (40%) -->
                            @php
                                $evalDocente = $evaluacionesEmitidas->get('docente_proyecto') ?? $evaluacionesEmitidas->get('tutor');
                            @endphp
                            <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="fw-bold text-dark">
                                        <i class="bi bi-mortarboard text-danger me-2"></i>Docente de Proyecto
                                        <span class="badge bg-light text-dark border ms-1 small">Peso: 40%</span>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        @if ($evalDocente)
                                            <span class="badge bg-success-subtle text-success me-1">✓ Evaluado</span>
                                            Calificación: <strong>{{ number_format($evalDocente->subtotal_cuantitativo, 2) }} pts</strong>
                                            &bull; Nivel: <em>{{ $evalDocente->nivel_logro_cualitativo }}</em>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Pendiente de evaluación</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('evaluaciones.evaluar', ['id_recorrido' => $sem->id_recorrido, 'rol_evaluador' => 'docente_proyecto']) }}" class="btn btn-sm {{ $evalDocente ? 'btn-outline-secondary' : 'btn-danger' }}">
                                        <i class="bi {{ $evalDocente ? 'bi-pencil' : 'bi-clipboard-check' }} me-1"></i>
                                        {{ $evalDocente ? 'Modificar' : 'Evaluar Rúbrica' }}
                                    </a>
                                </div>
                            </div>

                            <!-- 2. Jurado Evaluador (30%) -->
                            @php
                                $evalJurado = $evaluacionesEmitidas->get('jurado');
                            @endphp
                            <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="fw-bold text-dark">
                                        <i class="bi bi-award text-danger me-2"></i>Jurado Evaluador (Socialización Final)
                                        <span class="badge bg-light text-dark border ms-1 small">Peso: 30%</span>
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

                            <!-- 3. Representante de la Comunidad (20%) -->
                            @php
                                $evalCom = $evaluacionesEmitidas->get('comunidad');
                            @endphp
                            <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="fw-bold text-dark">
                                        <i class="bi bi-people text-danger me-2"></i>Representante de la Comunidad
                                        <span class="badge bg-light text-dark border ms-1 small">Peso: 20%</span>
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

                            <!-- 4. Estudiante: Autoevaluación y Coevaluación (10%) -->
                            @php
                                $evalEst = $evaluacionesEmitidas->get('estudiante');
                            @endphp
                            <div class="p-3 rounded border bg-white shadow-sm d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div>
                                    <div class="fw-bold text-dark">
                                        <i class="bi bi-person-check text-success me-2"></i>Autoevaluación y Coevaluación Estudiantil
                                        <span class="badge bg-light text-dark border ms-1 small">Peso: 10%</span>
                                    </div>
                                    <div class="small text-muted mt-1">
                                        @if ($evalEst)
                                            <span class="badge bg-success-subtle text-success me-1">✓ Evaluado</span>
                                            Calificación: <strong>{{ number_format($evalEst->subtotal_cuantitativo, 2) }} pts</strong>
                                            &bull; Nivel: <em>{{ $evalEst->nivel_logro_cualitativo }}</em>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary">Pendiente de registro grupal</span>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('evaluaciones.evaluar', ['id_recorrido' => $sem->id_recorrido, 'rol_evaluador' => 'estudiante']) }}" class="btn btn-sm {{ $evalEst ? 'btn-outline-secondary' : 'btn-success' }}">
                                        <i class="bi {{ $evalEst ? 'bi-pencil' : 'bi-clipboard-check' }} me-1"></i>
                                        {{ $evalEst ? 'Modificar' : 'Cargar Autoevaluación' }}
                                    </a>
                                </div>
                            </div>
                            @endif
                        @endif
                    </div>

                    <!-- Botón de Transición Semestral al culminar Semestre 1 -->
                    @if ($esS1 && $sem->estado_semestre === 'Culminado' && $esImpar)
                        <div class="p-3 bg-primary-subtle rounded border border-primary mb-3 text-center">
                            <div class="fw-bold text-primary mb-1">
                                <i class="bi bi-arrow-right-circle me-1"></i> ¡Semestre 1 Culminado Exitosamente!
                            </div>
                            <p class="small text-muted mb-2">
                                El equipo ha completado sus evaluaciones del Semestre 1. Puede promover automáticamente al equipo a la sección del Semestre Par (+1 al código).
                            </p>
                            <form action="{{ route('evaluaciones.transicion.semestre', $proyecto->id_equipo) }}" method="POST" class="m-0">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-sm fw-bold px-3" onclick="return confirm('¿Promover el equipo a la sección par y congelar el historial del Semestre 1?');">
                                    <i class="bi bi-rocket-takeoff-fill me-1"></i> Promover a Semestre Par (Transición Semestral)
                                </button>
                            </form>
                        </div>
                    @endif

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

<!-- Control Individual PNF: Asistencia (>25% Inasistencia) y Nota Mínima 16.00 pts -->
@if (!empty($metricasIndividuales))
<div class="card card-custom mb-4 border-dark">
    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-person-lines-fill text-danger fs-5"></i>
            <h5 class="fw-bold m-0">Expediente Individual por Estudiante (Reglamento PNF)</h5>
        </div>
        <span class="badge bg-danger px-3 py-2 rounded-pill">
            Mínimo Aprobatorio: 16.00 pts &bull; Límite de Inasistencias: 25%
        </span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Cédula</th>
                        <th>Estudiante</th>
                        <th class="text-center">Asistencia Puntos de Control</th>
                        <th class="text-center">Nota Individual</th>
                        <th class="pe-4">Condición Reglamentaria PNF</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($metricasIndividuales as $m)
                        <tr class="{{ !empty($m['reprobado_inasistencia']) ? 'table-danger' : '' }}">
                            <td class="ps-4 font-monospace fw-bold">V-{{ $m['cedula'] }}</td>
                            <td>
                                <span class="fw-bold text-dark">{{ $m['nombre_completo'] }}</span>
                                @if (!empty($m['es_lider']))
                                    <span class="badge bg-danger ms-1" style="font-size: 0.7rem;">Líder</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <span class="badge {{ !empty($m['reprobado_inasistencia']) ? 'bg-danger' : 'bg-success' }} px-3 py-1">
                                    {{ number_format((float)($m['porcentaje_asistencia'] ?? 100), 1) }}% Asistencia
                                </span>
                                <div class="small text-muted mt-1">
                                    Faltas injustificadas: {{ number_format((float)($m['porcentaje_inasistencia'] ?? 0), 1) }}%
                                </div>
                            </td>
                            <td class="text-center">
                                @if ($m['nota_individual'] !== null)
                                    <span class="fs-5 fw-bold {{ ((float)$m['nota_individual'] >= 16 && empty($m['reprobado_inasistencia'])) ? 'text-success' : 'text-danger' }}">
                                        {{ number_format((float)$m['nota_individual'], 2) }} / 20
                                    </span>
                                @else
                                    <span class="text-muted small">Pendiente</span>
                                @endif
                            </td>
                            <td class="pe-4">
                                @if (!empty($m['reprobado_inasistencia']))
                                    <div class="text-danger fw-bold small">
                                        <i class="bi bi-exclamation-octagon-fill me-1"></i>
                                        ALERTA PNF: Reprobado individualmente por inasistencia (&gt;25% faltas injustificadas). No afecta la nota ni aprobación de sus compañeros.
                                    </div>
                                @elseif (($m['aprobado_individual'] ?? null) === true)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 fw-bold">
                                        <i class="bi bi-check-circle-fill me-1"></i> {{ $m['condicion_pnf'] }}
                                    </span>
                                @elseif (($m['aprobado_individual'] ?? null) === false)
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-1 fw-bold">
                                        <i class="bi bi-x-circle-fill me-1"></i> {{ $m['condicion_pnf'] }}
                                    </span>
                                @else
                                    <span class="badge bg-light text-dark border px-3 py-1">{{ $m['condicion_pnf'] ?? 'En Curso' }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

<!-- Acta Final, Cierre de Trayecto y Exportaciones Oficiales UPTP -->
<div class="card card-custom border-danger">
    <div class="card-header bg-danger text-white py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-patch-check-fill fs-5"></i>
            <h5 class="fw-bold m-0">Consolidación Definitiva y Acta Final del Trayecto</h5>
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
                    <div class="text-muted small">sobre 20.00 pts (Aprobatorio &ge; 16.00)</div>
                </div>

                <div class="col-md-5">
                    <div class="small text-muted text-uppercase fw-bold mb-1">Veredicto y Mención Oficial</div>
                    <div class="fs-5 fw-bold text-dark mb-2">
                        <span class="badge {{ $acta->calificacion_definitiva_cuantitativa >= 16 ? 'bg-success' : 'bg-danger' }} px-3 py-2 rounded-pill">
                            {{ $acta->calificacion_definitiva_cualitativa }}
                        </span>
                    </div>
                    <div class="small text-muted">
                        Fórmula institucional: <strong>(S1 &times; 40%) + (S2 &times; 60%)</strong>
                        <br>
                        S1: {{ number_format($acta->nota_semestre_1, 2) }} pts &bull; S2: {{ number_format($acta->nota_semestre_2, 2) }} pts
                    </div>
                </div>

                <div class="col-md-4 text-md-end d-flex flex-column gap-2">
                    <a href="{{ route('evaluaciones.pdf', $proyecto->id_proyecto) }}" target="_blank" class="btn btn-danger shadow-sm fw-bold">
                        <i class="bi bi-file-earmark-pdf-fill me-1"></i> Descargar Acta Oficial PDF
                    </a>
                    
                    <!-- Botón Enviar a Repositorio Institucional (TAREA 2) -->
                    <form action="{{ route('evaluaciones.repositorio.enviar', $proyecto->id_equipo) }}" method="POST" class="m-0">
                        @csrf
                        <button type="submit" class="btn btn-outline-dark w-100 fw-bold" onclick="return confirm('¿Archivar y enviar este proyecto al Repositorio Institucional?');">
                            <i class="bi bi-archive-fill me-1"></i> Enviar a Repositorio Institucional
                        </button>
                    </form>

                    <!-- Exportación de Planilla de Vinculación (TAREA 4) -->
                    <a href="{{ route('inventario.excel.vinculacion', $proyecto->id_proyecto) }}" class="btn btn-outline-success w-100 fw-bold">
                        <i class="bi bi-file-earmark-excel-fill me-1"></i> Exportar Planilla de Vinculación (.xlsx)
                    </a>
                </div>
            </div>

            <!-- Código Hash de Verificación -->
            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="small text-muted font-monospace">
                    <i class="bi bi-fingerprint me-1"></i> Hash SHA-256: <strong>{{ $acta->hash_verificacion }}</strong>
                </div>
                <div>
                    <a href="{{ route('evaluaciones.verificar', ['hash' => $acta->hash_verificacion]) }}" target="_blank" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-search me-1"></i> Verificar Autenticidad Online
                    </a>
                </div>
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-info-circle fs-1 text-danger d-block mb-3"></i>
                <h6 class="fw-bold text-dark">El Acta Definitiva se generará automáticamente</h6>
                <p class="small mx-auto" style="max-width: 600px;">
                    Para realizar el cierre institucional del trayecto, el <strong>Semestre 1 (40%)</strong> y el <strong>Semestre 2 (60%)</strong> deben completar todas sus evaluaciones oficiales.
                </p>
                <div class="d-inline-flex gap-2">
                    <a href="{{ route('inventario.excel.vinculacion', $proyecto->id_proyecto) }}" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-file-earmark-excel me-1"></i> Descargar Planilla de Vinculación Preliminar
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
