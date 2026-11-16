@extends('layouts.admin')

@section('title', 'Verificación Oficial de Acta PST - UPTP')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4 p-md-5">
                    @if ($acta)
                        <!-- Documento Válido -->
                        <div class="text-center mb-4">
                            <div class="d-inline-flex p-3 rounded-circle bg-success-subtle text-success mb-3">
                                <i class="bi bi-patch-check-fill display-4"></i>
                            </div>
                            <h2 class="h3 fw-bold text-success mb-1">Documento Auténtico y Verificado</h2>
                            <p class="text-muted small">
                                Acta Oficial de Culminación y Calificación de Trayecto registrada en SISCON-PST.<br>
                                Universidad Politécnica Territorial del Estado Portuguesa "Juan de Jesús Montilla".
                            </p>
                        </div>

                        <div class="card border border-success-subtle bg-light mb-4">
                            <div class="card-body p-4">
                                <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                    <i class="bi bi-mortarboard-fill text-danger me-2"></i>Datos del Proyecto Socio Tecnológico
                                </h5>
                                
                                <div class="row g-3">
                                    <div class="col-md-4 text-muted small fw-semibold">Código del Equipo:</div>
                                    <div class="col-md-8"><span class="badge bg-dark fs-6">{{ $acta->codigo_equipo }}</span></div>

                                    <div class="col-md-4 text-muted small fw-semibold">Título del Proyecto:</div>
                                    <div class="col-md-8 fw-bold text-dark">{{ $acta->titulo_proyecto }}</div>

                                    <div class="col-md-4 text-muted small fw-semibold">Trayecto PNFI:</div>
                                    <div class="col-md-8">{{ $acta->trayecto_nombre ?? 'PNFI' }}</div>

                                    <div class="col-md-4 text-muted small fw-semibold">Comunidad Vinculada:</div>
                                    <div class="col-md-8">{{ $acta->comunidad_nombre ?? 'No especificada' }}</div>

                                    <div class="col-md-4 text-muted small fw-semibold">Fecha de Cierre Oficial:</div>
                                    <div class="col-md-8">{{ \Carbon\Carbon::parse($acta->fecha_cierre)->format('d/m/Y') }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Calificaciones Oficiales -->
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="card border-0 bg-white shadow-sm text-center p-3 h-100">
                                    <div class="text-muted small fw-semibold mb-1">Semestre 1 (40%)</div>
                                    <div class="fs-4 fw-bold text-secondary">{{ number_format($acta->nota_semestre_1, 2) }} pts</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-white shadow-sm text-center p-3 h-100">
                                    <div class="text-muted small fw-semibold mb-1">Semestre 2 (60%)</div>
                                    <div class="fs-4 fw-bold text-secondary">{{ number_format($acta->nota_semestre_2, 2) }} pts</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-danger-subtle text-center p-3 h-100 border border-danger">
                                    <div class="text-danger small fw-bold mb-1">Calificación Definitiva</div>
                                    <div class="fs-3 fw-bold text-danger">{{ number_format($acta->calificacion_definitiva_cuantitativa, 2) }} pts</div>
                                    <div class="badge bg-danger mt-1">{{ $acta->calificacion_definitiva_cualitativa }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Hash SHA-256 -->
                        <div class="bg-white p-3 rounded border mb-4">
                            <div class="text-muted small fw-semibold mb-1">
                                <i class="bi bi-shield-lock me-1"></i> Firma Digital SHA-256 Registrada:
                            </div>
                            <code class="text-break small fw-bold text-dark">{{ $acta->hash_verificacion }}</code>
                        </div>

                        <!-- Acciones -->
                        <div class="text-center">
                            @auth
                                <a href="{{ route('evaluaciones.pdf', $acta->id_proyecto) }}" target="_blank" class="btn btn-danger btn-lg px-4 me-2">
                                    <i class="bi bi-file-earmark-pdf-fill me-1"></i> Descargar Documento Oficial PDF
                                </a>
                                <a href="{{ route('evaluaciones.recorrido', $acta->id_proyecto) }}" class="btn btn-outline-secondary btn-lg px-4">
                                    <i class="bi bi-arrow-left me-1"></i> Ir al Recorrido
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-danger px-4">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Acceder a SISCON-PST
                                </a>
                            @endauth
                        </div>

                    @else
                        <!-- Documento Inválido -->
                        <div class="text-center py-4">
                            <div class="d-inline-flex p-3 rounded-circle bg-danger-subtle text-danger mb-3">
                                <i class="bi bi-shield-slash-fill display-4"></i>
                            </div>
                            <h2 class="h3 fw-bold text-danger mb-2">Acta No Válida o Inexistente</h2>
                            <p class="text-muted">
                                El código digital ingresado no coincide con ningún Acta Oficial de PST registrada o el documento ha sido alterado.
                            </p>
                            <div class="bg-light p-3 rounded border text-start my-3">
                                <small class="text-muted d-block mb-1">Código consultado:</small>
                                <code class="text-break small text-danger">{{ $hash }}</code>
                            </div>
                            <a href="/" class="btn btn-primary px-4 mt-2">
                                <i class="bi bi-house me-1"></i> Volver al Inicio
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
