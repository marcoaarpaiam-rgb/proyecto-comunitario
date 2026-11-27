<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Carta de Presentación - UPTP Juan de Jesús Montilla</title>
  <style>
    @page {
      margin: 98px 42px 66px 48px;
    }
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }
    body {
      font-family: "Times-Roman", "Times New Roman", Times, serif;
      font-size: 10.5pt;
      line-height: 1.32;
      color: #000000;
    }

    /* Encabezado fijo oficial */
    .header-fixed {
      position: fixed;
      top: -86px;
      left: 0;
      right: 0;
      height: 82px;
      text-align: center;
    }
    .cintillo-img {
      width: 100%;
      max-height: 54px;
      object-fit: contain;
      display: block;
    }
    .linea-doble-header {
      width: 100%;
      margin-top: 5px;
    }
    .linea-negra {
      height: 2px;
      background-color: #000000;
      width: 100%;
      margin-bottom: 3px;
    }
    .linea-roja {
      height: 2px;
      background-color: #C0504D;
      width: 100%;
    }

    /* Pie de página fijo oficial */
    .footer-fixed {
      position: fixed;
      bottom: -60px;
      left: 0;
      right: 0;
      height: 55px;
      border-top: 1.5px solid #C0504D;
      padding-top: 5px;
      text-align: center;
      font-size: 8pt;
      color: #333333;
      line-height: 1.3;
    }
    .footer-motto {
      font-style: italic;
      font-weight: bold;
      color: #8B0000;
      font-size: 8.5pt;
      margin-top: 2px;
    }

    /* Metadatos: Ciudad, Fecha y Código */
    .meta-bar {
      margin-top: 5px;
      margin-bottom: 12px;
    }
    .meta-bar table {
      width: 100%;
      border-collapse: collapse;
    }
    .meta-bar td {
      font-size: 10.5pt;
      vertical-align: top;
    }
    .codigo-doc {
      font-weight: bold;
      color: #000000;
    }
    .fecha-doc {
      text-align: right;
      color: #000000;
    }

    /* Destinatario */
    .destinatario {
      margin-bottom: 14px;
      line-height: 1.35;
    }
    .destinatario p {
      margin: 1px 0;
      font-size: 11pt;
    }
    .destinatario .cargo {
      font-weight: bold;
    }
    .destinatario .presente {
      font-weight: bold;
      margin-top: 4px;
    }

    /* Cuerpo del texto */
    .cuerpo {
      text-align: justify;
      margin-bottom: 12px;
    }
    .cuerpo p {
      margin-bottom: 8px;
      text-indent: 28px;
      text-align: justify;
      font-size: 11pt;
    }

    /* Caja de Proyecto */
    .titulo-proyecto-box {
      margin: 6px 0 10px 0;
      padding: 6px 12px;
      border-left: 3px solid #8B0000;
      background: #fafafa;
      text-align: center;
    }
    .titulo-proyecto-box .label {
      font-size: 8.5pt;
      text-transform: uppercase;
      font-weight: bold;
      color: #555555;
      display: block;
      margin-bottom: 2px;
    }
    .titulo-proyecto-box .titulo {
      font-weight: bold;
      color: #8B0000;
      font-size: 11pt;
      font-style: italic;
    }

    /* Tabla de Estudiantes */
    .seccion-titulo {
      font-size: 9.5pt;
      font-weight: bold;
      color: #000000;
      text-transform: uppercase;
      letter-spacing: 0.5px;
      margin: 10px 0 5px 0;
      border-bottom: 1px solid #333333;
      padding-bottom: 2px;
    }
    table.integrantes {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 14px;
    }
    table.integrantes th {
      background: #f2f2f2;
      color: #000000;
      font-size: 8.5pt;
      font-weight: bold;
      text-transform: uppercase;
      padding: 4px 6px;
      border: 1px solid #666666;
      text-align: center;
    }
    table.integrantes td {
      font-size: 9pt;
      padding: 4px 6px;
      border: 1px solid #888888;
      vertical-align: middle;
    }
    table.integrantes tr:nth-child(even) td {
      background: #fbfbfb;
    }

    /* Firmas y Sellos */
    .firmas-container {
      margin-top: 12px;
      width: 100%;
      page-break-inside: avoid;
    }
    .firmas-container table {
      width: 100%;
      border-collapse: collapse;
    }
    .firma-col {
      width: 48%;
      vertical-align: top;
      text-align: center;
      padding: 0 10px;
    }
    .espacio-firma {
      height: 44px;
      display: block;
      text-align: center;
    }
    .espacio-firma img {
      max-height: 44px;
      max-width: 140px;
    }
    .linea-firma {
      border-top: 1px solid #000000;
      margin-top: 2px;
      padding-top: 4px;
      font-size: 9pt;
      line-height: 1.3;
    }
    .linea-firma .nombre {
      font-weight: bold;
      color: #000000;
    }
    .linea-firma .cargo {
      color: #333333;
      font-size: 8pt;
    }
    .sello-cuadro {
      display: inline-block;
      width: 85px;
      height: 38px;
      border: 1px dashed #777777;
      margin-top: 4px;
      font-size: 7pt;
      color: #666666;
      line-height: 38px;
    }
  </style>
</head>
<body>
  @php
    $cintilloPath = public_path('images/cintillo_uptp.png');
    $cintilloBase64 = file_exists($cintilloPath) ? base64_encode(file_get_contents($cintilloPath)) : null;
    $fechaAprobacion = $carta->cpr_fecha_aprobacion ?? now();
    $firmaRelativa = $coordinador?->usu_firma_digital;
    $firmaPath = $firmaRelativa ? storage_path('app/public/' . $firmaRelativa) : null;
    $firmaExt = $firmaPath ? strtolower(pathinfo($firmaPath, PATHINFO_EXTENSION)) : 'png';
    $firmaMime = $firmaExt === 'jpg' || $firmaExt === 'jpeg' ? 'image/jpeg' : 'image/png';
    $firmaBase64 = ($firmaPath && file_exists($firmaPath)) ? base64_encode(file_get_contents($firmaPath)) : null;
    $responsableActivo = $proyecto?->comunidad?->responsables?->where('rco_status', true)->first()
        ?? $proyecto?->comunidad?->responsables?->first();
    $respNombre = $responsableActivo?->usuario
        ? trim($responsableActivo->usuario->usu_primer_nombre . ' ' . $responsableActivo->usuario->usu_primer_apellido)
        : null;
    $integrantesActivos = ($proyecto?->equipo?->integrantes ?? collect())->where('ein_status', '!=', false)->values();
  @endphp

  <!-- Encabezado Oficial con Logos y Doble Línea Institucional -->
  <div class="header-fixed">
    @if($cintilloBase64)
      <img src="data:image/png;base64,{{ $cintilloBase64 }}" class="cintillo-img" alt="Cintillo Oficial UPTP JJM">
    @endif
    <div class="linea-doble-header">
      <div class="linea-negra"></div>
      <div class="linea-roja"></div>
    </div>
  </div>

  <!-- Pie de Página Institucional Formal -->
  <div class="footer-fixed">
    <div>
      <strong>Universidad Politécnica Territorial de Portuguesa «Juan de Jesús Montilla»</strong> &bull; RIF: G-20002161-0
    </div>
    <div>
      Av. Circunvalación Sur con Av. Las Lágrimas, Campus Universitario, Acarigua, Estado Portuguesa &bull; Zona Postal 3301
    </div>
    <div>
      Teléfonos: (0255) 621-1234 / 621-5678 &bull; Correo: pnfi.portuguesa@uptp.edu.ve &bull; Web: www.uptp.edu.ve
    </div>
    <div class="footer-motto">
      «Educación Universitaria para el Desarrollo Sociocomunitario y la Soberanía Tecnológica Popular»
    </div>
  </div>

  <!-- Barra de Metadatos: Código y Fecha Oficial -->
  <div class="meta-bar">
    <table>
      <tr>
        <td class="codigo-doc">
          OFICIO N° CPI-PNFI-{{ \Carbon\Carbon::parse($fechaAprobacion)->format('Y') }}-{{ str_pad($carta->cpr_id, 4, '0', STR_PAD_LEFT) }}
        </td>
        <td class="fecha-doc">
          Acarigua, {{ \Carbon\Carbon::parse($fechaAprobacion)->locale('es')->isoFormat('D [de] MMMM [de] YYYY') }}
        </td>
      </tr>
    </table>
  </div>

  <!-- Destinatario Formal -->
  <div class="destinatario">
    <p class="cargo">Ciudadanos(as):</p>
    <p><strong>Responsable de la Comunidad y Demás Miembros de:</strong></p>
    <p><strong>{{ $proyecto->comunidad->com_nombre ?? 'Comunidad Receptora' }}</strong></p>
    @if($respNombre)
      <p>Atención: {{ $respNombre }}@if($responsableActivo?->usuario?->usu_cedula) (C.I. {{ $responsableActivo->usuario->usu_cedula }})@endif</p>
    @endif
    <p>Ubicación / Sector: {{ $proyecto->comunidad->com_ubicacion ?? 'Presente' }}</p>
    <p class="presente">Presente.-</p>
  </div>

  <!-- Cuerpo de la Carta -->
  <div class="cuerpo">
    <p>
      Ante todo, reciban un cordial y fraterno saludo institucional en nombre de la <strong>Universidad Politécnica Territorial de Portuguesa «Juan de Jesús Montilla» (UPTP JJM)</strong>. En cumplimiento de las directrices establecidas en la Ley Orgánica de Educación, la Ley Orgánica de los Consejos Comunales y los lineamientos del <strong>Programa Nacional de Formación en Informática (PNFI)</strong>, nos dirigimos a ustedes con el propósito de presentar formalmente al equipo de estudiantes cursantes del <strong>{{ $proyecto->equipo->trayecto->tra_nombre ?? 'Trayecto Académico' }}</strong>, Sección <strong>{{ $proyecto->equipo->seccion->sec_codigo ?? 'Sin Sección' }}</strong>, identificado institucionalmente con el código <strong>{{ $proyecto->equipo->equ_codigo }}</strong>.
    </p>

    <p>
      Los mencionados estudiantes universitarios se encuentran debidamente autorizados por esta Casa de Estudios para desarrollar en su sector las actividades vinculadas al Proyecto Sociotecnológico (PST), orientado a la articulación comunitaria y a la formulación de soluciones computacionales acordes a las necesidades de su localidad:
    </p>

    <div class="titulo-proyecto-box">
      <span class="label">Título Oficial del Proyecto Sociotecnológico</span>
      <span class="titulo">«{{ $proyecto->equipo->equ_titulo ?: ($proyecto->pco_titulo ?: 'Desarrollo de Solución Tecnológica y Comunitaria') }}»</span>
    </div>

    <p>
      En tal sentido, solicitamos respetuosamente su colaboración y receptividad para que los estudiantes puedan llevar a cabo las fases de diagnóstico participativo, recolección de información técnica y socialización de avances, dentro del marco de la corresponsabilidad y el desarrollo sociotecnológico.
    </p>
  </div>

  <!-- Nómina de Estudiantes Integrantes -->
  <div class="seccion-titulo">
    Nómina Oficial de Estudiantes Integrantes del Equipo
  </div>
  <table class="integrantes">
    <thead>
      <tr>
        <th style="width: 25px;">N°</th>
        <th>Nombres y Apellidos</th>
        <th style="width: 85px;">Cédula</th>
        <th>Correo Electrónico / Teléfono</th>
        <th style="width: 80px;">Condición</th>
        <th style="width: 75px;">Firma</th>
      </tr>
    </thead>
    <tbody>
      @forelse($integrantesActivos as $index => $int)
        <tr>
          <td style="text-align: center; font-weight: bold;">{{ $index + 1 }}</td>
          <td style="font-weight: bold;">
            {{ $int->usuario->usu_primer_nombre }} {{ $int->usuario->usu_segundo_nombre }} {{ $int->usuario->usu_primer_apellido }} {{ $int->usuario->usu_segundo_apellido }}
          </td>
          <td style="text-align: center;">
            {{ $int->usuario->usu_cedula ?: 'V-' . $int->usuario->usu_id }}
          </td>
          <td style="font-size: 8pt;">
            {{ $int->usuario->usu_email ?: 'No registrado' }}
            @if($int->usuario->usu_telefono) &bull; {{ $int->usuario->usu_telefono }} @endif
          </td>
          <td style="text-align: center; font-size: 8pt; font-weight: bold;">
            {{ $int->ein_es_lider ? 'LÍDER' : 'INTEGRANTE' }}
          </td>
          <td></td>
        </tr>
      @empty
        <tr>
          <td colspan="6" style="text-align: center; color: #888;">No hay integrantes registrados en este equipo.</td>
        </tr>
      @endforelse
    </tbody>
  </table>

  <!-- Bloque de Firmas y Sellos -->
  <div class="firmas-container">
    <table>
      <tr>
        <td class="firma-col">
          <div class="espacio-firma">
            @if($firmaBase64)
              <img src="data:{{ $firmaMime }};base64,{{ $firmaBase64 }}" alt="Firma Coordinador">
            @endif
          </div>
          <div class="linea-firma">
            <span class="nombre">{{ $coordinador->usu_primer_nombre ?? 'Coordinación' }} {{ $coordinador->usu_primer_apellido ?? 'PNFI' }}</span><br>
            <span class="cargo">Coordinación del PNF en Informática<br>UPTP «Juan de Jesús Montilla»</span>
          </div>
          @if($carta->cpr_sello)
            <div class="sello-cuadro">SELLO OFICIAL</div>
          @endif
        </td>
        <td class="firma-col">
          <div class="espacio-firma"></div>
          <div class="linea-firma">
            <span class="nombre">{{ $respNombre ?: 'Por la Comunidad / Organización Receptora' }}</span><br>
            <span class="cargo">Responsable de la Comunidad<br>{{ $proyecto->comunidad->com_nombre ?? 'Comunidad Receptora' }}</span>
          </div>
          <div class="sello-cuadro">SELLO COMUNIDAD</div>
        </td>
      </tr>
    </table>
  </div>
</body>
</html>
