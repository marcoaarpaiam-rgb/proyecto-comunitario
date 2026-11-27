<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Acta Final de Evaluación PST - {{ $proyecto->codigo_equipo }}</title>
    <style>
        @page {
            margin: 20mm 15mm 20mm 15mm;
            size: letter portrait;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 10.5pt;
            color: #222;
            line-height: 1.35;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #a71d2a;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .header p.institution {
            font-size: 8.5pt;
            font-weight: bold;
            color: #333;
            text-transform: uppercase;
            line-height: 1.25;
            margin-bottom: 3px;
        }
        .header h1 {
            font-size: 13pt;
            color: #a71d2a;
            text-transform: uppercase;
            margin-top: 6px;
            margin-bottom: 2px;
            letter-spacing: 0.5px;
        }
        .header h2 {
            font-size: 10pt;
            color: #444;
            font-weight: normal;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .meta-table td {
            padding: 4px 6px;
            font-size: 9pt;
            border: 1px solid #ddd;
        }
        .meta-table td.label {
            font-weight: bold;
            background-color: #f8f9fa;
            width: 25%;
            color: #495057;
        }
        .section-title {
            font-size: 10pt;
            font-weight: bold;
            color: #a71d2a;
            text-transform: uppercase;
            border-bottom: 1.5px solid #a71d2a;
            padding-bottom: 2px;
            margin-top: 10px;
            margin-bottom: 6px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.data-table th {
            background-color: #212529;
            color: #fff;
            font-size: 8.5pt;
            padding: 4px 6px;
            text-align: left;
            border: 1px solid #343a40;
        }
        table.data-table td {
            padding: 4px 6px;
            font-size: 8.5pt;
            border: 1px solid #ced4da;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        
        .box-resultado {
            border: 2px solid #a71d2a;
            background-color: #fdf7f7;
            padding: 8px 12px;
            margin-bottom: 10px;
            text-align: center;
        }
        .box-resultado .calif-num {
            font-size: 16pt;
            font-weight: bold;
            color: #a71d2a;
        }
        .box-resultado .calif-letra {
            font-size: 10.5pt;
            font-weight: bold;
            color: #111;
            margin-top: 2px;
        }
        .sintesis-box {
            background-color: #fbfbfb;
            border-left: 3px solid #a71d2a;
            padding: 6px 10px;
            font-size: 8.5pt;
            text-align: justify;
            margin-bottom: 12px;
            line-height: 1.35;
        }
        .firmas-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 18px;
        }
        .firmas-table td {
            width: 50%;
            padding: 8px 15px;
            text-align: center;
            vertical-align: top;
            font-size: 8pt;
        }
        .firma-linea {
            border-top: 1px solid #333;
            margin-top: 35px;
            padding-top: 4px;
        }
        .security-footer {
            border-top: 1px solid #ccc;
            padding-top: 5px;
            margin-top: 15px;
            font-size: 7pt;
            color: #666;
            text-align: center;
        }
        .hash-code {
            font-family: 'Courier New', Courier, monospace;
            font-size: 7.5pt;
            font-weight: bold;
            color: #111;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <!-- Encabezado Institucional -->
    <div class="header">
        <p class="institution">
            República Bolivariana de Venezuela<br>
            Ministerio del Poder Popular para la Educación Universitaria<br>
            Universidad Politécnica Territorial del Estado Portuguesa "Juan de Jesús Montilla"<br>
            Programa Nacional de Formación en Informática (PNFI) — Sede Acarigua
        </p>
        <h1>Acta Oficial de Culminación y Calificación de Trayecto</h1>
        <h2>Proyecto Socio Tecnológico (PST) &bull; Período Académico Anual</h2>
    </div>

    <!-- Identificación del Proyecto -->
    <table class="meta-table">
        <tr>
            <td class="label">Código del Equipo:</td>
            <td class="fw-bold" style="width: 25%;">{{ $proyecto->codigo_equipo }}</td>
            <td class="label">Trayecto / Sección:</td>
            <td style="width: 25%;">{{ $proyecto->trayecto_nombre ?? 'PNFI' }} — Sec. {{ $proyecto->seccion_codigo ?? 'N/A' }}</td>
        </tr>
        <tr>
            <td class="label">Título del Proyecto:</td>
            <td colspan="3" class="fw-bold">{{ $proyecto->titulo_proyecto }}</td>
        </tr>
        <tr>
            <td class="label">Comunidad / Institución:</td>
            <td colspan="3">{{ $proyecto->comunidad_nombre ?? 'N/A' }} @if(!empty($proyecto->comunidad_ubicacion)) ({{ $proyecto->comunidad_ubicacion }}) @endif</td>
        </tr>
        <tr>
            <td class="label">Tutor(a) Académico(a):</td>
            <td>{{ $proyecto->tutor_nombre ?? 'Profesor Designado' }} {{ $proyecto->tutor_apellido ?? '' }}</td>
            <td class="label">Fecha de Cierre:</td>
            <td>{{ \Carbon\Carbon::parse($acta->fecha_cierre)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <!-- Integrantes Evaluados -->
    <div class="section-title">1. Estudiantes / Equipo Investigador</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 6%;" class="text-center">#</th>
                <th style="width: 20%;">Cédula de Identidad</th>
                <th style="width: 50%;">Apellidos y Nombres</th>
                <th style="width: 24%;" class="text-center">Rol en el Proyecto</th>
            </tr>
        </thead>
        <tbody>
            @php $idx = 1; @endphp
            @forelse ($integrantes as $est)
                <tr>
                    <td class="text-center">{{ $idx++ }}</td>
                    <td class="fw-bold">V-{{ number_format((float)$est->usu_cedula, 0, '', '.') }}</td>
                    <td>{{ $est->usu_primer_apellido }} {{ $est->usu_primer_nombre }}</td>
                    <td class="text-center">
                        @if ($est->ein_es_lider)
                            <strong>Líder de Proyecto</strong>
                        @else
                            Integrante Desarrollador
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">No se encontraron integrantes asociados al equipo.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Desglose de Evaluación por Semestre -->
    <div class="section-title">2. Desglose del Recorrido de Evaluación por Semestre</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 28%;">Semestre / Momento</th>
                <th style="width: 32%;">Evaluador / Instancia</th>
                <th style="width: 15%;" class="text-center">Puntaje Base</th>
                <th style="width: 10%;" class="text-center">Peso</th>
                <th style="width: 15%;" class="text-end">Ponderado Sem.</th>
            </tr>
        </thead>
        <tbody>
            <!-- Semestre 1 -->
            @php
                $s1 = $semestres->firstWhere('semestre', 'Semestre 1');
                $s2 = $semestres->firstWhere('semestre', 'Semestre 2');
            @endphp
            @if ($s1 && isset($s1->evaluaciones))
                @foreach ($s1->evaluaciones as $ev)
                    <tr>
                        @if ($loop->first)
                            <td rowspan="{{ count($s1->evaluaciones) + 1 }}" style="vertical-align: middle; background-color: #fdfdfd;">
                                <strong>Semestre 1</strong><br>
                                <span style="font-size: 7.5pt; color: #555;">(Ponderación Trayecto: 40%)</span>
                            </td>
                        @endif
                        <td>
                            <strong>
                                @if ($ev->rol_evaluador === 'comunidad')
                                    Representante de la Comunidad
                                @elseif ($ev->rol_evaluador === 'estudiante')
                                    Autoevaluación Estudiantil
                                @elseif (in_array($ev->rol_evaluador, ['docente_proyecto', 'tutor']))
                                    Docente de Proyecto
                                @else
                                    Jurado Evaluador
                                @endif
                            </strong>
                            <div style="font-size: 7.5pt; color: #666;">
                                @if ($ev->rol_evaluador === 'comunidad')
                                    Tutor / Responsable de la Comunidad
                                @elseif ($ev->rol_evaluador === 'estudiante')
                                    Colectivo de Estudiantes del PST
                                @else
                                    {{ $ev->evaluador_nombre }} {{ $ev->evaluador_apellido }}
                                @endif
                            </div>
                        </td>
                        <td class="text-center">{{ number_format($ev->calificacion_cuantitativa, 2) }} / 20</td>
                        <td class="text-center">{{ number_format($ev->peso_porcentual, 0) }}%</td>
                        <td class="text-end fw-bold">
                            {{ number_format(($ev->calificacion_cuantitativa * $ev->peso_porcentual) / 100, 2) }} pts
                        </td>
                    </tr>
                @endforeach
                <tr style="background-color: #f8f9fa;">
                    <td colspan="3" class="text-end fw-bold">Consolidado Semestre 1 (Escala 20 pts):</td>
                    <td class="text-end fw-bold" style="color: #a71d2a;">{{ number_format($acta->nota_semestre_1, 2) }} pts</td>
                </tr>
            @endif

            <!-- Semestre 2 -->
            @if ($s2 && isset($s2->evaluaciones))
                @foreach ($s2->evaluaciones as $ev)
                    <tr>
                        @if ($loop->first)
                            <td rowspan="{{ count($s2->evaluaciones) + 1 }}" style="vertical-align: middle; background-color: #fdfdfd;">
                                <strong>Semestre 2</strong><br>
                                <span style="font-size: 7.5pt; color: #555;">(Ponderación Trayecto: 60%)</span>
                            </td>
                        @endif
                        <td>
                            <strong>
                                @if ($ev->rol_evaluador === 'comunidad')
                                    Representante de la Comunidad
                                @elseif ($ev->rol_evaluador === 'estudiante')
                                    Autoevaluación Estudiantil
                                @elseif (in_array($ev->rol_evaluador, ['docente_proyecto', 'tutor']))
                                    Docente de Proyecto
                                @else
                                    Jurado Evaluador
                                @endif
                            </strong>
                            <div style="font-size: 7.5pt; color: #666;">
                                @if ($ev->rol_evaluador === 'comunidad')
                                    Tutor / Responsable de la Comunidad
                                @elseif ($ev->rol_evaluador === 'estudiante')
                                    Colectivo de Estudiantes del PST
                                @else
                                    {{ $ev->evaluador_nombre }} {{ $ev->evaluador_apellido }}
                                @endif
                            </div>
                        </td>
                        <td class="text-center">{{ number_format($ev->calificacion_cuantitativa, 2) }} / 20</td>
                        <td class="text-center">{{ number_format($ev->peso_porcentual, 0) }}%</td>
                        <td class="text-end fw-bold">
                            {{ number_format(($ev->calificacion_cuantitativa * $ev->peso_porcentual) / 100, 2) }} pts
                        </td>
                    </tr>
                @endforeach
                <tr style="background-color: #f8f9fa;">
                    <td colspan="3" class="text-end fw-bold">Consolidado Semestre 2 (Escala 20 pts):</td>
                    <td class="text-end fw-bold" style="color: #a71d2a;">{{ number_format($acta->nota_semestre_2, 2) }} pts</td>
                </tr>
            @endif
        </tbody>
    </table>

    <!-- Veredicto Final del Trayecto -->
    <div class="section-title">3. Dictamen y Calificación Definitiva del Trayecto</div>
    <div class="box-resultado">
        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; text-align: center; border-right: 1px dashed #a71d2a; padding: 4px;">
                    <div style="font-size: 8.5pt; text-transform: uppercase; color: #555;">Fórmula Institucional PNFI:</div>
                    <div style="font-size: 9pt; margin: 3px 0;">(Semestre 1 &times; 40%) + (Semestre 2 &times; 60%)</div>
                    <div style="font-size: 8.5pt; color: #666;">
                        ({{ number_format($acta->nota_semestre_1, 2) }} &times; 0.40) + ({{ number_format($acta->nota_semestre_2, 2) }} &times; 0.60)
                    </div>
                </td>
                <td style="width: 50%; text-align: center; padding: 4px;">
                    <div style="font-size: 8.5pt; text-transform: uppercase; color: #555;">Calificación Definitiva:</div>
                    <div class="calif-num">{{ number_format($acta->calificacion_definitiva_cuantitativa, 2) }} / 20 Puntos</div>
                    <div class="calif-letra">{{ $acta->calificacion_definitiva_cualitativa }}</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- Síntesis Cualitativa -->
    <div class="section-title">4. Síntesis Cualitativa Institucional y Logros del PST</div>
    <div class="sintesis-box">
        {{ $acta->sintesis_cualitativa_final ?: 'El equipo demostró las competencias requeridas para el trayecto correspondiente del Programa Nacional de Formación en Informática, cumpliendo a cabalidad con los objetivos formulados y la vinculación sociocomunitaria establecida.' }}
    </div>

    <!-- Firmas Oficiales -->
    <table class="firmas-table">
        <tr>
            <td>
                <div class="firma-linea">
                    <strong>TUTOR(A) ACADÉMICO(A)</strong><br>
                    {{ $proyecto->tutor_nombre ?? 'Profesor Asignado' }} {{ $proyecto->tutor_apellido ?? '' }}<br>
                    C.I.: {{ $proyecto->tutor_cedula ?? '____________' }}
                </div>
            </td>
            <td>
                <div class="firma-linea">
                    <strong>TUTOR(A) / RESPONSABLE DE LA COMUNIDAD</strong><br>
                    {{ $proyecto->responsable_comunitario ?? $proyecto->comunidad_nombre }}<br>
                    Representación de la Comunidad
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="firma-linea">
                    <strong>JURADO EVALUADOR PNFI</strong><br>
                    Docente Evaluador Designado<br>
                    Área Académica de Proyecto
                </div>
            </td>
            <td>
                <div class="firma-linea">
                    <strong>COORDINACIÓN PNFI</strong><br>
                    Universidad Politécnica Territorial del Estado Portuguesa<br>
                    "Juan de Jesús Montilla" — Sede Acarigua
                </div>
            </td>
        </tr>
    </table>

    <!-- Código de Seguridad y Verificación SHA-256 -->
    <div class="security-footer">
        <p><strong>CÓDIGO DE AUTENTICIDAD Y VERIFICACIÓN DIGITAL (SHA-256):</strong></p>
        <p class="hash-code">{{ $acta->hash_verificacion }}</p>
        <p style="margin-top: 3px;">
            Verificación en línea: <u>{{ $urlVerificacion }}</u> &bull;
            Generado por SISCON-PST el {{ \Carbon\Carbon::now()->format('d/m/Y H:i:s') }} &bull; Documento Válido Institucionalmente.
        </p>
    </div>
</body>
</html>
