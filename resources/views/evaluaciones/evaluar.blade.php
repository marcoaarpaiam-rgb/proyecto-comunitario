@extends('layouts.admin')

@section('title', 'Evaluación con Rúbrica - ' . $rubrica->titulo)

@section('content')
<div class="container-fluid py-3">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('evaluaciones.index') }}">Evaluaciones PST</a></li>
            <li class="breadcrumb-item"><a href="{{ route('evaluaciones.recorrido', $recorrido->id_proyecto) }}">Recorrido del Proyecto</a></li>
            <li class="breadcrumb-item active" aria-current="page">Evaluar: {{ ucfirst($rol_evaluador) }} ({{ $recorrido->semestre }})</li>
        </ol>
    </nav>

    <!-- Header Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body bg-light rounded p-4">
            <div class="d-flex flex-wrap justify-content-between align-items-center">
                <div>
                    <span class="badge bg-danger mb-2 px-3 py-2 fs-6">
                        <i class="bi bi-ui-checks-grid me-1"></i> Rúbrica Oficial PNFI
                    </span>
                    <h2 class="h4 fw-bold text-dark mb-1">{{ $rubrica->titulo }}</h2>
                    <p class="text-muted mb-0">
                        <strong>Equipo:</strong> <span class="badge bg-dark">{{ $recorrido->codigo_equipo }}</span> &bull; 
                        <strong>Proyecto:</strong> {{ $recorrido->titulo_proyecto }} &bull;
                        <strong>Trayecto:</strong> {{ $recorrido->trayecto_nombre ?? 'PNFI' }} &bull;
                        <strong>Comunidad:</strong> {{ $recorrido->comunidad_nombre ?? 'N/A' }}
                    </p>
                </div>
                <div class="text-end mt-2 mt-md-0">
                    <span class="badge bg-primary fs-6 px-3 py-2 mb-1">
                        {{ $recorrido->semestre }} (Peso en Trayecto: {{ number_format($recorrido->peso_semestre, 0) }}%)
                    </span>
                    <div class="text-muted small">
                        Ponderación del Evaluador: <strong>{{ number_format($rubrica->peso_porcentual, 0) }}%</strong> del Semestre
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <h6 class="alert-heading fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> Por favor verifique los siguientes errores:</h6>
            <ul class="mb-0 small">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($evaluacionPrevia)
        <div class="alert alert-warning d-flex align-items-center shadow-sm mb-4" role="alert">
            <i class="bi bi-info-circle-fill fs-4 me-3 text-warning"></i>
            <div>
                <strong>Aviso de Edición:</strong> Ya existe una evaluación registrada para este rol con fecha 
                <u>{{ \Carbon\Carbon::parse($evaluacionPrevia->fecha_evaluacion)->format('d/m/Y h:i A') }}</u> 
                con calificación de <strong>{{ number_format($evaluacionPrevia->calificacion_cuantitativa, 2) }}/20 pts</strong>.
                Al guardar nuevamente, se actualizarán las calificaciones y la síntesis cualitativa.
            </div>
        </div>
    @endif

    <form action="{{ route('evaluaciones.guardar') }}" method="POST" id="formEvaluacion">
        @csrf
        <input type="hidden" name="id_recorrido" value="{{ $recorrido->id_recorrido }}">
        <input type="hidden" name="id_proyecto" value="{{ $recorrido->id_proyecto }}">
        <input type="hidden" name="rol_evaluador" value="{{ $rol_evaluador }}">

        <!-- Evaluador Asignado -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-person-badge text-danger me-2"></i>Datos del Evaluador ({{ ucfirst($rol_evaluador) }})
                </h5>
            </div>
            <div class="card-body">
                @if ($rol_evaluador === 'comunidad')
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <p class="mb-1"><strong>Comunidad Beneficiaria:</strong> {{ $recorrido->comunidad_nombre ?? 'No asignada' }}</p>
                            <p class="text-muted small mb-0">
                                La evaluación comunitaria es consignada con base en la interacción de la comunidad con los estudiantes, el impacto social y los productos entregados.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="badge bg-info text-dark fs-6 px-3 py-2">
                                <i class="bi bi-people-fill me-1"></i> Tutor / Vocero Comunitario
                            </span>
                        </div>
                    </div>
                @else
                    <div class="row">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label for="id_profesor" class="form-label fw-bold">Docente Evaluador / Jurado:</label>
                            <select name="id_profesor" id="id_profesor" class="form-select @error('id_profesor') is-invalid @enderror" required>
                                <option value="">-- Seleccione el docente evaluador --</option>
                                @foreach ($profesores as $prof)
                                    @php
                                        $selectedId = old('id_profesor', $evaluacionPrevia->id_profesor ?? auth()->id());
                                    @endphp
                                    <option value="{{ $prof->usu_id }}" {{ $selectedId == $prof->usu_id ? 'selected' : '' }}>
                                        {{ $prof->usu_primer_nombre }} {{ $prof->usu_primer_apellido }} (C.I. {{ $prof->usu_cedula }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">Docente del PNFI responsable de emitir esta calificación.</div>
                        </div>
                        <div class="col-md-6 d-flex align-items-center">
                            <div class="p-3 bg-light rounded w-100 border">
                                <span class="fw-bold text-secondary d-block mb-1">Rol en esta evaluación:</span>
                                <span class="badge {{ $rol_evaluador === 'tutor' ? 'bg-primary' : 'bg-warning text-dark' }} fs-6">
                                    {{ $rol_evaluador === 'tutor' ? 'Tutor Académico del PST' : 'Miembro del Jurado Evaluador' }}
                                </span>
                                <span class="ms-2 small text-muted">Peso asignado: {{ number_format($rubrica->peso_porcentual, 0) }}% del Semestre</span>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Tabla de Criterios de la Rúbrica -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-clipboard-check text-danger me-2"></i>Criterios de Evaluación Oficiales
                </h5>
                <span class="badge bg-secondary">
                    Total Criterios: {{ count($criterios) }}
                </span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>
                                <th style="width: 55%;">Criterio e Indicador de Logro</th>
                                <th style="width: 15%;" class="text-center">Tipo</th>
                                <th style="width: 10%;" class="text-center">Escala Máx.</th>
                                <th style="width: 15%;" class="text-end">Calificación Asignada</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $num = 1; @endphp
                            @foreach ($criterios as $criterio)
                                <tr>
                                    <td class="text-center fw-bold text-muted">{{ $num++ }}</td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $criterio->nombre_criterio }}</div>
                                        <div class="text-muted small">
                                            @if($criterio->tipo_criterio === 'cuantitativo')
                                                Puntaje asignable entre 0.00 y {{ number_format($criterio->puntaje_maximo, 2) }} pts.
                                            @else
                                                Apreciación cualitativa del avance y desenvolvimiento técnico.
                                            @endif
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if ($criterio->tipo_criterio === 'cuantitativo')
                                            <span class="badge bg-primary-subtle text-primary border border-primary">Cuantitativo</span>
                                        @else
                                            <span class="badge bg-success-subtle text-success border border-success">Cualitativo</span>
                                        @endif
                                    </td>
                                    <td class="text-center fw-bold text-secondary">
                                        {{ number_format($criterio->puntaje_maximo, 2) }} pts
                                    </td>
                                    <td class="text-end">
                                        @php
                                            $valPrevia = old('criterios.' . $criterio->id_criterio, $detallesPrevios[$criterio->id_criterio] ?? '');
                                        @endphp
                                        <div class="input-group input-group-sm justify-content-end">
                                            <input 
                                                type="number" 
                                                step="0.25" 
                                                min="0" 
                                                max="{{ $criterio->puntaje_maximo }}" 
                                                name="criterios[{{ $criterio->id_criterio }}]" 
                                                id="criterio_{{ $criterio->id_criterio }}"
                                                data-max="{{ $criterio->puntaje_maximo }}"
                                                value="{{ $valPrevia !== '' ? number_format((float)$valPrevia, 2, '.', '') : '' }}" 
                                                class="form-control form-control-sm criterio-input text-end fw-bold text-danger border-danger-subtle" 
                                                placeholder="0.00" 
                                                style="max-width: 110px;"
                                                required
                                            >
                                            <span class="input-group-text bg-light text-muted">/ {{ number_format($criterio->puntaje_maximo, 1) }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light border-top">
                            <tr>
                                <th colspan="3" class="text-end py-3 fs-5">Total Cuantitativo de la Rúbrica:</th>
                                <th class="text-center py-3 fs-5 text-secondary">20.00 pts</th>
                                <th class="text-end py-3">
                                    <div class="d-flex align-items-center justify-content-end">
                                        <span id="totalPuntaje" class="fs-4 fw-bold text-danger me-2">0.00</span>
                                        <span class="text-muted fs-6">/ 20.00 pts</span>
                                    </div>
                                    <div id="mensajeAlerta" class="small mt-1 text-end"></div>
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Evaluación Cualitativa y Síntesis -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0 text-dark">
                    <i class="bi bi-chat-square-quote text-danger me-2"></i>Apreciación Cualitativa y Nivel de Logro
                </h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nivel_logro_cualitativo" class="form-label fw-bold">
                            Nivel de Logro Cualitativo Institucional <span class="text-danger">*</span>:
                        </label>
                        @php
                            $nivelActual = old('nivel_logro_cualitativo', $evaluacionPrevia->nivel_logro_cualitativo ?? '');
                        @endphp
                        <select name="nivel_logro_cualitativo" id="nivel_logro_cualitativo" class="form-select @error('nivel_logro_cualitativo') is-invalid @enderror" required>
                            <option value="">-- Seleccione el nivel de logro alcanzado --</option>
                            <option value="Logrado con Excelencia" {{ $nivelActual === 'Logrado con Excelencia' ? 'selected' : '' }}>
                                Logrado con Excelencia (Dominio sobresaliente y aportes de alto impacto)
                            </option>
                            <option value="Logrado" {{ $nivelActual === 'Logrado' ? 'selected' : '' }}>
                                Logrado (Cumplimiento satisfactorio de todas las competencias requeridas)
                            </option>
                            <option value="En Proceso" {{ $nivelActual === 'En Proceso' ? 'selected' : '' }}>
                                En Proceso (Competencias básicas alcanzadas con aspectos por consolidar)
                            </option>
                            <option value="No Logrado" {{ $nivelActual === 'No Logrado' ? 'selected' : '' }}>
                                No Logrado (No alcanza las competencias mínimas exigidas en el trayecto)
                            </option>
                        </select>
                        <div class="form-text">Refleja la apreciación formativa y cualitativa según lineamientos del PNFI.</div>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="observacion_cualitativa" class="form-label fw-bold">
                        Observación, Síntesis y Recomendaciones Cualitativas <span class="text-danger">*</span>:
                    </label>
                    <textarea 
                        name="observacion_cualitativa" 
                        id="observacion_cualitativa" 
                        rows="4" 
                        class="form-control @error('observacion_cualitativa') is-invalid @enderror" 
                        placeholder="Escriba aquí la síntesis del desempeño del equipo, la calidad técnica de la solución desarrollada, el compromiso comunitario y las orientaciones formativas pertinentes (mínimo 15 caracteres)..."
                        required
                        minlength="15"
                        maxlength="2000"
                    >{{ old('observacion_cualitativa', $evaluacionPrevia->observacion_cualitativa ?? '') }}</textarea>
                    <div class="d-flex justify-content-between form-text">
                        <span>Esta observación quedará registrada en el historial y en el Acta Final del Trayecto.</span>
                        <span id="charCount" class="text-muted">0 / 2000 caracteres</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body bg-light rounded d-flex flex-wrap justify-content-between align-items-center p-3">
                <a href="{{ route('evaluaciones.recorrido', $recorrido->id_proyecto) }}" class="btn btn-outline-secondary px-4 py-2">
                    <i class="bi bi-arrow-left me-1"></i> Regresar al Recorrido
                </a>
                <button type="submit" class="btn btn-danger px-5 py-2 fw-bold shadow-sm" id="btnGuardar">
                    <i class="bi bi-check2-circle me-1"></i> Guardar y Consolidar Evaluación
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    function calcularTotal() {
        let total = 0;
        let hayExceso = false;

        $('.criterio-input').each(function() {
            let val = parseFloat($(this).val()) || 0;
            let max = parseFloat($(this).data('max')) || 20;

            if (val > max) {
                $(this).addClass('is-invalid');
                hayExceso = true;
            } else {
                $(this).removeClass('is-invalid');
            }

            total += val;
        });

        // Redondear a 2 decimales
        total = Math.round(total * 100) / 100;
        $('#totalPuntaje').text(total.toFixed(2));

        let $alerta = $('#mensajeAlerta');
        let $btn = $('#btnGuardar');

        if (hayExceso) {
            $alerta.html('<span class="text-danger fw-bold"><i class="bi bi-exclamation-triangle"></i> Hay criterios que superan su puntaje máximo.</span>');
            $btn.prop('disabled', true);
        } else if (total > 20) {
            $alerta.html('<span class="text-danger fw-bold"><i class="bi bi-exclamation-octagon"></i> La suma supera el máximo de 20.00 pts (' + total.toFixed(2) + ' pts).</span>');
            $btn.prop('disabled', true);
        } else {
            $alerta.html('<span class="text-success"><i class="bi bi-check-circle"></i> Ponderación cuantitativa válida (' + total.toFixed(2) + ' / 20.00 pts)</span>');
            $btn.prop('disabled', false);
        }
    }

    // Listener para campos de criterios
    $(document).on('input change', '.criterio-input', function() {
        calcularTotal();
    });

    // Contador de caracteres para observación cualitativa
    function actualizarContador() {
        let len = $('#observacion_cualitativa').val().length;
        $('#charCount').text(len + ' / 2000 caracteres (mínimo 15)');
        if (len < 15) {
            $('#charCount').addClass('text-danger').removeClass('text-muted');
        } else {
            $('#charCount').removeClass('text-danger').addClass('text-muted');
        }
    }

    $('#observacion_cualitativa').on('input', actualizarContador);

    // Ejecutar cálculos iniciales
    calcularTotal();
    actualizarContador();
});
</script>
@endpush
