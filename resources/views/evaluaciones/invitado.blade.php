@extends('layouts.admin')

@section('title', 'Acceso Rápido con PIN — Evaluación en Vivo PST UPTP')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <!-- Header con branding UPTP -->
                <div class="card-header bg-dark text-white p-4 text-center border-bottom border-danger border-3">
                    <div class="d-inline-flex p-3 rounded-circle bg-danger-subtle text-danger mb-2">
                        <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="m9 12 2 2 4-4"/>
                        </svg>
                    </div>
                    <h4 class="fw-bold mb-1">Evaluación en Vivo de Socialización</h4>
                    <p class="text-white-50 small mb-0">UPTP "Juan de Jesús Montilla" &bull; PNFI Sede Acarigua</p>
                </div>

                <div class="card-body p-4 p-md-5 bg-white">
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <p class="text-muted small text-center mb-4">
                        Ingrese con su <strong>Cédula de Identidad</strong> y el <strong>PIN de Defensa (6 dígitos)</strong> proporcionado por el Docente de Proyecto o Equipo para consignar su calificación en tiempo real.
                    </p>

                    <!-- Pestañas de Selección de Rol -->
                    <ul class="nav nav-pills nav-fill mb-4 bg-light p-1 rounded-3" id="pills-tab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active fw-bold py-2" id="tab-comunidad" data-bs-toggle="pill" data-bs-target="#panel-comunidad" type="button" role="tab" onclick="setTipo('comunidad')">
                                <i class="bi bi-people me-1"></i> Vocero Comunitario (20%)
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link fw-bold py-2" id="tab-jurado" data-bs-toggle="pill" data-bs-target="#panel-jurado" type="button" role="tab" onclick="setTipo('jurado')">
                                <i class="bi bi-award me-1"></i> Jurado Evaluador (30%)
                            </button>
                        </li>
                    </ul>

                    <form action="{{ route('evaluaciones.invitado.acceder') }}" method="POST" id="formInvitado">
                        @csrf
                        <input type="hidden" name="tipo" id="input_tipo" value="comunidad">

                        <!-- Cédula de Identidad -->
                        <div class="mb-3">
                            <label for="cedula" class="form-label fw-bold text-dark" id="label_cedula">
                                <i class="bi bi-person-vcard text-danger me-1"></i> Cédula del Vocero Comunitario:
                            </label>
                            <div class="input-group input-group-lg">
                                <span class="input-group-text bg-light text-muted">V-</span>
                                <input 
                                    type="text" 
                                    name="cedula" 
                                    id="cedula" 
                                    class="form-control" 
                                    placeholder="Ej: 12345678" 
                                    required
                                    value="{{ old('cedula') }}"
                                    pattern="[0-9]+"
                                    autocomplete="off"
                                >
                            </div>
                            <div class="form-text small" id="help_cedula">
                                Debe coincidir con la cédula registrada por el Líder del Equipo en el proyecto.
                            </div>
                        </div>

                        <!-- PIN de Defensa -->
                        <div class="mb-4">
                            <label for="pin" class="form-label fw-bold text-dark">
                                <i class="bi bi-key text-danger me-1"></i> PIN de Defensa (6 Dígitos):
                            </label>
                            <input 
                                type="text" 
                                name="pin" 
                                id="pin" 
                                class="form-control form-control-lg text-center fw-bold fs-3 tracking-widest border-danger-subtle" 
                                placeholder="000000" 
                                maxlength="6" 
                                pattern="[0-9]{6}" 
                                required
                                value="{{ old('pin', request('pin')) }}"
                                autocomplete="off"
                                style="letter-spacing: 6px;"
                            >
                            <div class="form-text small text-center">
                                Solicite este código al profesor de proyecto o equipo evaluado.
                            </div>
                        </div>

                        <!-- Botón de Ingreso -->
                        <button type="submit" class="btn btn-danger btn-lg w-100 py-3 fw-bold shadow-sm rounded-3">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Validar Acceso y Calificar
                        </button>
                    </form>
                </div>

                <div class="card-footer bg-light p-3 text-center text-muted small">
                    Sistema Web de Proyectos Socio Tecnológicos (SISCON-PST) &bull; UPTP Juan de Jesús Montilla
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setTipo(tipo) {
    document.getElementById('input_tipo').value = tipo;
    const label = document.getElementById('label_cedula');
    const help = document.getElementById('help_cedula');
    if (tipo === 'comunidad') {
        label.innerHTML = '<i class="bi bi-person-vcard text-danger me-1"></i> Cédula del Vocero Comunitario:';
        help.innerText = 'Debe coincidir con la cédula registrada en el proyecto comunitario.';
    } else {
        label.innerHTML = '<i class="bi bi-mortarboard text-danger me-1"></i> Cédula del Docente / Jurado:';
        help.innerText = 'Cédula del docente del PNFI asignado para evaluar esta socialización.';
    }
}
</script>
@endsection
