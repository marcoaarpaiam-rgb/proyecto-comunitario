<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Planilla de Evaluación de Socialización Final - {{ $equipo->equ_codigo }}</title>
    <style>
        @page {
            margin: 16mm 14mm 18mm 14mm;
            size: letter portrait;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, Helvetica, sans-serif;
            font-size: 9.5pt;
            color: #111;
            line-height: 1.35;
        }
        .header {
            text-align: center;
            border-bottom: 2.5px solid #a71d2a;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .header p.inst {
            font-size: 8.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #333;
        }
        .header h1 {
            font-size: 12.5pt;
            color: #a71d2a;
            text-transform: uppercase;
            margin-top: 5px;
        }
        .header h2 {
            font-size: 9.5pt;
            font-weight: normal;
            color: #444;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .meta td {
            border: 1px solid #bbb;
            padding: 4px 6px;
            font-size: 8.8pt;
        }
        .meta td.lbl {
            background: #f3f4f6;
            font-weight: bold;
            width: 20%;
            color: #333;
        }
        .section-title {
            font-size: 9.5pt;
            font-weight: bold;
            text-transform: uppercase;
            color: #a71d2a;
            border-bottom: 1.5px solid #a71d2a;
            padding-bottom: 2px;
            margin: 8px 0 6px;
        }
        .grid th {
            background: #212529;
            color: #fff;
            font-size: 8.5pt;
            padding: 5px 6px;
            border: 1px solid #343a40;
            text-align: left;
        }
        .grid td {
            border: 1px solid #999;
            padding: 6px;
            font-size: 8.5pt;
            vertical-align: middle;
        }
        .text-center { text-align: center; }
        .obs-box {
            border: 1px solid #999;
            min-height: 55px;
            padding: 6px;
            margin-bottom: 10px;
            font-size: 8.5pt;
        }
        .firmas td {
            width: 33.3%;
            text-align: center;
            padding: 26px 8px 4px;
            vertical-align: bottom;
            font-size: 8pt;
        }
        .linea-firma {
            border-top: 1px solid #222;
            padding-top: 3px;
            margin: 0 8px;
        }
        .footer {
            margin-top: 10px;
            font-size: 7.5pt;
            color: #555;
            text-align: center;
            border-top: 1px solid #ccc;
            padding-top: 4px;
        }
    </style>
</head>
<body>
    <div class="header">
        <p class="inst">
            República Bolivariana de Venezuela &bull; Ministerio del Poder Popular para la Educación Universitaria<br>
            Universidad Politécnica Territorial del Estado Portuguesa "Juan de Jesús Montilla"<br>
            Programa Nacional de Formación en Informática (PNFI)
        </p>
        <h1>Planilla Oficial de Evaluación — Defensa Final PST</h1>
        <h2>Instrumento Físico de Respaldo Híbrido &bull; Escala Cuantitativa: 1.00 a 20.00 pts (Mín. Aprobatorio: 16.00 pts)</h2>
    </div>

    <table class="meta">
        <tr>
            <td class="lbl">Código Equipo:</td>
            <td><strong>{{ $equipo->equ_codigo }}</strong></td>
            <td class="lbl">Trayecto / Sección:</td>
            <td>{{ $equipo->trayecto?->tra_nombre ?? 'PNFI' }} &bull; Sec. {{ $equipo->seccion?->sec_codigo ?? '—' }}</td>
        </tr>
        <tr>
            <td class="lbl">Título Proyecto:</td>
            <td colspan="3">{{ $equipo->equ_titulo }}</td>
        </tr>
        <tr>
            <td class="lbl">Comunidad:</td>
            <td>{{ $proyecto?->comunidad?->com_nombre ?? 'Por definir' }}</td>
            <td class="lbl">Fecha / Lugar:</td>
            <td>{{ $socFinal?->soc_fecha ?? '____/____/______' }} &bull; {{ $socFinal?->soc_lugar ?? '________________' }}</td>
        </tr>
        <tr>
            <td class="lbl">Docente Proyecto:</td>
            <td>{{ $docente ? "{$docente->usu_primer_nombre} {$docente->usu_primer_apellido}" : '_______________________' }}</td>
            <td class="lbl">Rol Evaluador:</td>
            <td>[ ] Jurado (30%) &nbsp; [ ] Comunidad (20%) &nbsp; [ ] Docente (40%)</td>
        </tr>
    </table>

    <div class="section-title">1. Rúbrica Estructurada por Criterios e Indicadores (Total: 20.00 pts)</div>
    <table class="grid">
        <thead>
            <tr>
                <th style="width: 8%;" class="text-center">N°</th>
                <th style="width: 58%;">Criterio e Indicadores de Evaluación</th>
                <th style="width: 17%;" class="text-center">Puntaje Máx.</th>
                <th style="width: 17%;" class="text-center">Nota Obtenida</th>
            </tr>
        </thead>
        <tbody>
            @php $totalMax = 0; @endphp
            @foreach($criterios as $idx => $crit)
                @php $totalMax += (float) $crit->puntaje_maximo; @endphp
                <tr>
                    <td class="text-center"><strong>{{ $idx + 1 }}</strong></td>
                    <td>{{ $crit->nombre_criterio }}</td>
                    <td class="text-center">{{ number_format((float) $crit->puntaje_maximo, 2) }} pts</td>
                    <td class="text-center">&nbsp;</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" style="text-align: right; font-weight: bold; background: #f8f9fa;">TOTAL CALIFICACIÓN DEL EQUIPO:</td>
                <td class="text-center" style="font-weight: bold; background: #f8f9fa;">{{ number_format($totalMax, 2) }} pts</td>
                <td class="text-center" style="font-weight: bold;">&nbsp;</td>
            </tr>
        </tbody>
    </table>

    <div class="section-title">2. Calificación Individualizada por Estudiante</div>
    <table class="grid">
        <thead>
            <tr>
                <th style="width: 18%;">Cédula</th>
                <th style="width: 34%;">Apellidos y Nombres</th>
                <th style="width: 14%;" class="text-center">Asistió (S/N)</th>
                <th style="width: 16%;" class="text-center">Nota (1 - 20)</th>
                <th style="width: 18%;" class="text-center">Firma Estudiante</th>
            </tr>
        </thead>
        <tbody>
            @foreach($equipo->integrantes->where('ein_status', true) as $integ)
                <tr>
                    <td>{{ $integ->usuario?->usu_cedula }}</td>
                    <td>{{ $integ->usuario?->usu_primer_apellido }} {{ $integ->usuario?->usu_primer_nombre }} {{ $integ->ein_es_lider ? '(Líder)' : '' }}</td>
                    <td class="text-center">&nbsp;</td>
                    <td class="text-center">&nbsp;</td>
                    <td>&nbsp;</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="section-title">3. Nivel de Logro y Síntesis Cualitativa</div>
    <div style="font-size: 8.5pt; margin-bottom: 6px;">
        <strong>Nivel de Logro:</strong>
        &nbsp; [ ] Logrado con Excelencia (18-20)
        &nbsp; [ ] Logrado (16-17.99)
        &nbsp; [ ] En Proceso (10-15.99)
        &nbsp; [ ] No Logrado (&lt;10)
    </div>
    <div class="obs-box">
        <strong>Observaciones y Recomendaciones del Evaluador:</strong><br><br>
    </div>

    <div class="section-title">4. Validación y Firmas Autógrafas</div>
    <table class="firmas">
        <tr>
            <td>
                <div class="linea-firma">
                    <strong>Jurado Evaluador 1</strong><br>
                    Nombre y C.I.: ___________________
                </div>
            </td>
            <td>
                <div class="linea-firma">
                    <strong>Jurado Evaluador 2</strong><br>
                    Nombre y C.I.: ___________________
                </div>
            </td>
            <td>
                <div class="linea-firma">
                    <strong>Responsable de la Comunidad</strong><br>
                    Nombre y C.I.: ___________________
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div class="linea-firma">
                    <strong>Docente de Proyecto</strong><br>
                    {{ $docente ? "{$docente->usu_primer_nombre} {$docente->usu_primer_apellido}" : '___________________' }}
                </div>
            </td>
            <td>
                <div class="linea-firma">
                    <strong>Coordinación PNFI</strong><br>
                    Sello y Firma
                </div>
            </td>
            <td>
                <div class="linea-firma">
                    <strong>Fecha de Consignación</strong><br>
                    ____ / ____ / 20____
                </div>
            </td>
        </tr>
    </table>

    <div class="footer">
        SISCON-PST &bull; Instrumento oficial de respaldo físico ante contingencias de conectividad. Consignar posteriormente en el sistema.
    </div>
</body>
</html>
