<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: Arial, sans-serif; font-size: 12px; color: #1a1a1a; padding: 40px; }
    .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #1F3864; padding-bottom: 15px; }
    .header h1 { font-size: 16px; color: #1F3864; text-transform: uppercase; margin-bottom: 4px; }
    .header h2 { font-size: 13px; color: #444; font-weight: normal; }
    .header p  { font-size: 11px; color: #666; }
    .fecha { text-align: right; margin-bottom: 20px; font-size: 11px; color: #555; }
    .cuerpo { line-height: 1.8; margin-bottom: 24px; text-align: justify; }
    .cuerpo .ref { font-weight: bold; color: #1F3864; }
    table.datos { width: 100%; border-collapse: collapse; margin: 20px 0; }
    table.datos td { padding: 6px 10px; border: 1px solid #ccc; font-size: 11px; }
    table.datos td:first-child { font-weight: bold; background: #f0f4f8; width: 35%; }
    .firmas { margin-top: 50px; display: flex; justify-content: space-between; }
    .firma-box { text-align: center; width: 45%; }
    .firma-box img { max-height: 70px; margin-bottom: 8px; }
    .firma-box .linea { border-top: 1px solid #333; padding-top: 6px; font-size: 11px; }
    .sello { position: fixed; bottom: 60px; right: 50px; opacity: 0.15; font-size: 48px; color: #1F3864; }
    .footer { position: fixed; bottom: 20px; left: 40px; right: 40px; text-align: center;
              font-size: 9px; color: #999; border-top: 1px solid #eee; padding-top: 6px; }
  </style>
</head>
<body>
  <!-- Encabezado -->
  <div class="header">
    <h1>Universidad Politécnica Territorial de Portuguesa</h1>
    <h2>Juan de Jesús Montilla — Coordinación PNF Informática</h2>
    <p>Acarigua, Estado Portuguesa, Venezuela</p>
  </div>

  <!-- Fecha -->
  <div class="fecha">
    Acarigua, {{ \Carbon\Carbon::parse($carta->cpr_fecha_aprobacion)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
  </div>

  <!-- Cuerpo -->
  <div class="cuerpo">
    <p>Estimados representantes de la comunidad:</p>
    <br>
    <p>
      Por medio de la presente, la Coordinación del Programa Nacional de Formación en
      Informática de la Universidad Politécnica Territorial de Portuguesa "Juan de Jesús Montilla",
      hace constar que el equipo identificado con el código
      <span class="ref">{{ $proyecto->equipo->equ_codigo }}</span>,
      perteneciente a la sección
      <span class="ref">{{ $proyecto->equipo->seccion->sec_codigo }}</span>
      del <span class="ref">{{ $proyecto->equipo->trayecto->tra_nombre }}</span>,
      se encuentra debidamente inscrito y autorizado para desarrollar su proyecto
      sociocomunitario en su institución.
    </p>
    <br>
    <p>
      El proyecto titulado <span class="ref">"{{ $proyecto->equipo->equ_titulo }}"</span>
      tiene como objetivo principal contribuir al desarrollo tecnológico de la comunidad
      <span class="ref">{{ $proyecto->comunidad->com_nombre }}</span>,
      ubicada en {{ $proyecto->comunidad->com_ubicacion }}.
    </p>
    <br>
    <p>
      Se solicita muy respetuosamente a las autoridades y miembros de dicha comunidad
      prestar el apoyo necesario al equipo de estudiantes para el desarrollo exitoso
      del proyecto, en el marco del compromiso social que caracteriza a nuestra institución.
    </p>
  </div>

  <!-- Datos del equipo -->
  <table class="datos">
    <tr>
      <td>Código del Equipo</td>
      <td>{{ $proyecto->equipo->equ_codigo }}</td>
    </tr>
    <tr>
      <td>Proyecto</td>
      <td>{{ $proyecto->equipo->equ_titulo }}</td>
    </tr>
    <tr>
      <td>Comunidad</td>
      <td>{{ $proyecto->comunidad->com_nombre }}</td>
    </tr>
    <tr>
      <td>Ubicación</td>
      <td>{{ $proyecto->comunidad->com_ubicacion }}</td>
    </tr>
    <tr>
      <td>Trayecto / Sección</td>
      <td>{{ $proyecto->equipo->trayecto->tra_nombre }} — Sección {{ $proyecto->equipo->seccion->sec_codigo }}</td>
    </tr>
    <tr>
      <td>Integrantes</td>
      <td>
        @foreach($proyecto->equipo->integrantes as $int)
          {{ $int->usuario->usu_primer_nombre }} {{ $int->usuario->usu_primer_apellido }}
          @if($int->ein_es_lider)(Líder)@endif
          @if(!$loop->last), @endif
        @endforeach
      </td>
    </tr>
  </table>

  <!-- Firmas -->
  <div class="firmas">
    <div class="firma-box">
      @if($coordinador->usu_firma_digital)
        <img src="{{ public_path('storage/' . $coordinador->usu_firma_digital) }}"
             alt="Firma Coordinador">
      @else
        <div style="height:70px;"></div>
      @endif
      <div class="linea">
        {{ $coordinador->usu_primer_nombre }} {{ $coordinador->usu_primer_apellido }}<br>
        Coordinador(a) PNF Informática<br>
        UPTP Juan de Jesús Montilla
      </div>
    </div>
    <div class="firma-box">
      <div style="height:70px;"></div>
      <div class="linea">
        Representante de la Comunidad<br>
        {{ $proyecto->comunidad->com_nombre }}
      </div>
    </div>
  </div>

  @if($carta->cpr_sello)
    <div class="sello">★ UPTP ★</div>
  @endif

  <div class="footer">
    Documento generado por el Sistema de Seguimiento de Proyectos Comunitarios —
    PNF Informática UPTP Juan de Jesús Montilla
  </div>
</body>
</html>