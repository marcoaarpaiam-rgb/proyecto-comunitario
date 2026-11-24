<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family:Arial,sans-serif; font-size:10px; color:#1a1a1a; padding:25px; }
    .header { text-align:center; border-bottom:2px solid #1F3864; padding-bottom:10px; margin-bottom:16px; }
    .header h1 { font-size:13px; color:#1F3864; text-transform:uppercase; }
    .resumen { display:flex; gap:10px; margin-bottom:16px; }
    .resumen-card { flex:1; background:#f0f4f8; padding:8px; border-radius:4px; text-align:center; }
    .resumen-card .num { font-size:18px; font-weight:bold; color:#1F3864; }
    .resumen-card .lbl { font-size:9px; color:#666; }
    table { width:100%; border-collapse:collapse; margin-bottom:16px; }
    th { background:#1F3864; color:white; padding:5px 8px; font-size:9px; }
    td { padding:5px 8px; border:1px solid #ddd; font-size:9px; }
    tr:nth-child(even) td { background:#f8f9fa; }
    .footer { position:fixed; bottom:15px; left:25px; right:25px; text-align:center; font-size:8px; color:#999; border-top:1px solid #eee; padding-top:4px; }
  </style>
</head>
<body>
  <div class="header">
    <h1>Reporte General del Sistema</h1>
    <p style="font-size:10px;color:#555;margin-top:3px;">PNF Informática — UPTP Juan de Jesús Montilla — Generado: {{ now()->format('d/m/Y H:i') }}</p>
  </div>

  <div class="resumen">
    <div class="resumen-card">
      <div class="num">{{ $resumen['total_equipos'] }}</div>
      <div class="lbl">Total Equipos</div>
    </div>
    <div class="resumen-card" style="background:#d4edda;">
      <div class="num" style="color:#198754;">{{ $resumen['aprobados'] }}</div>
      <div class="lbl">Aprobados</div>
    </div>
    <div class="resumen-card" style="background:#fff3cd;">
      <div class="num" style="color:#856404;">{{ $resumen['aplazados'] }}</div>
      <div class="lbl">Aplazados</div>
    </div>
    <div class="resumen-card" style="background:#f8d7da;">
      <div class="num" style="color:#842029;">{{ $resumen['no_presentados'] }}</div>
      <div class="lbl">No Presentados</div>
    </div>
    <div class="resumen-card">
      <div class="num" style="color:#6c757d;">{{ $resumen['sin_resultado'] }}</div>
      <div class="lbl">Sin Resultado</div>
    </div>
  </div>

  <table>
    <thead>
      <tr>
        <th>Sección</th>
        <th>Trayecto</th>
        <th>Turno</th>
        <th style="text-align:center;">Total Equipos</th>
        <th style="text-align:center;">Aprobados</th>
        <th style="text-align:center;">Aplazados</th>
        <th style="text-align:center;">Sin Resultado</th>
      </tr>
    </thead>
    <tbody>
      @foreach($secciones as $sec)
        @php
          $equipos = $sec->equipos()->where('equ_status', true)
              ->with('resultadoProyecto.estadoProyecto')->get();
          $aprobados  = $equipos->filter(fn($e) => $e->resultadoProyecto?->estadoProyecto?->epr_nombre === 'Aprobado')->count();
          $aplazados  = $equipos->filter(fn($e) => $e->resultadoProyecto?->estadoProyecto?->epr_nombre === 'Aplazado')->count();
          $sinResult  = $equipos->filter(fn($e) => !$e->resultadoProyecto)->count();
        @endphp
        <tr>
          <td><strong>{{ $sec->sec_codigo }}</strong></td>
          <td>{{ $sec->trayecto?->tra_nombre }}</td>
          <td>{{ $sec->turno?->tur_nombre }}</td>
          <td style="text-align:center;">{{ $equipos->count() }}</td>
          <td style="text-align:center;color:#198754;font-weight:bold;">{{ $aprobados }}</td>
          <td style="text-align:center;color:#856404;font-weight:bold;">{{ $aplazados }}</td>
          <td style="text-align:center;color:#6c757d;">{{ $sinResult }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <div class="footer">
    Sistema de Seguimiento de Proyectos Comunitarios — PNF Informática UPTP Juan de Jesús Montilla
  </div>
</body>
</html>