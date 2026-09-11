<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:Arial,sans-serif; font-size:11px; color:#1a1a1a; padding:30px; }
    .header { text-align:center; border-bottom:2px solid #1F3864; padding-bottom:12px; margin-bottom:20px; }
    .header h1 { font-size:14px; color:#1F3864; text-transform:uppercase; }
    .header h2 { font-size:12px; color:#555; font-weight:normal; margin-top:3px; }
    .seccion-info { background:#f0f4f8; padding:8px 12px; border-radius:4px; margin-bottom:16px; font-size:11px; }
    .equipo { margin-bottom:20px; page-break-inside:avoid; }
    .equipo-header { background:#1F3864; color:white; padding:6px 10px; border-radius:4px 4px 0 0; font-weight:bold; }
    table { width:100%; border-collapse:collapse; }
    th { background:#f0f4f8; padding:5px 8px; text-align:left; font-size:10px; border:1px solid #ddd; }
    td { padding:5px 8px; border:1px solid #ddd; font-size:10px; }
    .solvente { color:#198754; font-weight:bold; }
    .no-solvente { color:#dc3545; font-weight:bold; }
    .badge-aprobado { background:#198754; color:white; padding:2px 6px; border-radius:3px; font-size:9px; }
    .badge-aplazado { background:#ffc107; color:#333; padding:2px 6px; border-radius:3px; font-size:9px; }
    .badge-otro { background:#6c757d; color:white; padding:2px 6px; border-radius:3px; font-size:9px; }
    .footer { position:fixed; bottom:15px; left:30px; right:30px; text-align:center; font-size:9px; color:#999; border-top:1px solid #eee; padding-top:5px; }
  </style>
</head>
<body>
  <div class="header">
    <h1>Reporte de Solvencia Estudiantil</h1>
    <h2>PNF Informática — UPTP Juan de Jesús Montilla</h2>
  </div>

  <div class="seccion-info">
    <strong>Sección:</strong> {{ $seccion->sec_codigo }} &nbsp;|&nbsp;
    <strong>Trayecto:</strong> {{ $seccion->trayecto?->tra_nombre }} &nbsp;|&nbsp;
    <strong>Turno:</strong> {{ $seccion->turno?->tur_nombre }} &nbsp;|&nbsp;
    <strong>Generado:</strong> {{ now()->format('d/m/Y H:i') }}
  </div>

  @forelse($reporte as $equ)
    <div class="equipo">
      <div class="equipo-header">
        {{ $equ['equipo'] }} — {{ $equ['titulo'] }}
        @if($equ['comunidad'])
          | Comunidad: {{ $equ['comunidad'] }}
        @endif
        @if($equ['resultado'])
          &nbsp;
          <span class="{{ $equ['resultado'] === 'Aprobado' ? 'badge-aprobado' : ($equ['resultado'] === 'Aplazado' ? 'badge-aplazado' : 'badge-otro') }}">
            {{ $equ['resultado'] }}
          </span>
        @endif
      </div>
      <table>
        <thead>
          <tr>
            <th>Estudiante</th>
            <th>Cédula</th>
            <th>Rol</th>
            <th>Asistencias</th>
            <th>%</th>
            <th>Estado</th>
          </tr>
        </thead>
        <tbody>
          @foreach($equ['integrantes'] as $int)
            <tr>
              <td>{{ $int['nombre'] }}</td>
              <td>{{ $int['cedula'] }}</td>
              <td>{{ $int['es_lider'] ? 'Líder' : 'Integrante' }}</td>
              <td>{{ $int['asistencias'] }} / {{ $int['total'] }}</td>
              <td>{{ $int['porcentaje'] }}%</td>
              <td class="{{ $int['solvente'] ? 'solvente' : 'no-solvente' }}">
                {{ $int['solvente'] ? 'Solvente' : 'No Solvente' }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @empty
    <p style="text-align:center;color:#999;padding:30px;">No hay equipos en esta sección.</p>
  @endforelse

  <div class="footer">
    Sistema de Seguimiento de Proyectos Comunitarios — PNF Informática UPTP Juan de Jesús Montilla
  </div>
</body>
</html>