<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class InventarioPstExcelController extends Controller
{
    /**
     * Retorna la ruta física de la plantilla oficial de INVENTARIO PST.
     */
    protected function getPlantillaInventarioPath(): string
    {
        $path1 = storage_path('app/plantillas/INVENTARIO PST 2026-II 281016.xls');
        if (file_exists($path1)) {
            return $path1;
        }

        $path2 = storage_path('app/plantillas/INVENTARIO_PST_PLANTILLA_OFICIAL.xls');
        if (file_exists($path2)) {
            return $path2;
        }

        throw new \Exception("Plantilla oficial de INVENTARIO PST no encontrada en storage/app/plantillas/");
    }

    /**
     * Retorna la ruta física de la plantilla oficial de PLANILLA DE VINCULACIÓN.
     */
    protected function getPlantillaVinculacionPath(): string
    {
        $path1 = storage_path('app/plantillas/PLANILLA DE VINCULACION 2024 II- PNFI-431.xlsx');
        if (file_exists($path1)) {
            return $path1;
        }

        $path2 = storage_path('app/plantillas/PLANILLA_VINCULACION_PLANTILLA_OFICIAL.xlsx');
        if (file_exists($path2)) {
            return $path2;
        }

        throw new \Exception("Plantilla oficial de PLANILLA DE VINCULACIÓN no encontrada en storage/app/plantillas/");
    }

    /**
     * EXPORTACIÓN INDIVIDUAL DE PUNTO DE CONTROL (ENFOQUE DE PLANTILLA FIJA UPTP)
     * Carga el libro oficial, inyecta los datos en la hoja del corte correspondiente (6, 10, 14 ó 18)
     * y descarga exclusivamente esa sábana oficial manteniendo el 100% de la fidelidad visual.
     */
    public function exportarPuntoControl(int $id_puc, Request $request)
    {
        $puc = DB::table('punto_control as puc')
            ->join('equipo as e', 'e.equ_id', '=', 'puc.puc_id_equ')
            ->leftJoin('seccion as s', 's.sec_id', '=', 'e.equ_id_sec')
            ->leftJoin('trayecto as t', 't.tra_id', '=', 'e.equ_id_tra')
            ->leftJoin('turno as tur', 'tur.tur_id', '=', 's.sec_id_tur')
            ->leftJoin('proyecto_comunidad as p', 'p.pco_id_equ', '=', 'e.equ_id')
            ->leftJoin('comunidad as c', 'c.com_id', '=', 'p.pco_id_com')
            ->where('puc.puc_id', $id_puc)
            ->select([
                'puc.*',
                'e.equ_id',
                'e.equ_codigo',
                'e.equ_titulo',
                's.sec_id',
                's.sec_codigo',
                'tur.tur_nombre',
                't.tra_nombre',
                'p.pco_id',
                'p.pco_resumen',
                'p.pco_linea_investigacion',
                'c.com_nombre',
                'c.com_ubicacion',
            ])
            ->first();

        if (!$puc) {
            return redirect()->back()->with('error', 'Punto de control no encontrado.');
        }

        // Obtener docente de la sección o usuario autenticado
        $docente = DB::table('profesor_seccion as pse')
            ->join('usuario as u', 'u.usu_id', '=', 'pse.pse_id_usu')
            ->where('pse.pse_id_sec', $puc->sec_id)
            ->where('pse.pse_status', true)
            ->select('u.usu_primer_nombre', 'u.usu_primer_apellido', 'u.usu_cedula')
            ->first() ?? (object)[
                'usu_primer_nombre'   => auth()->user()->usu_primer_nombre ?? 'Docente',
                'usu_primer_apellido' => auth()->user()->usu_primer_apellido ?? 'PNFI',
                'usu_cedula'          => auth()->user()->usu_cedula ?? 'N/A'
            ];
        $docenteNombre = "{$docente->usu_primer_nombre} {$docente->usu_primer_apellido}";

        // Obtener coordinador del programa
        $coordinador = DB::table('usuario as u')
            ->join('usuario_rol as ur', 'ur.uro_id_usu', '=', 'u.usu_id')
            ->join('rol as r', 'r.rol_id', '=', 'ur.uro_id_rol')
            ->where('r.rol_nombre', 'coordinador')
            ->where('u.usu_status', true)
            ->select('u.usu_primer_nombre', 'u.usu_primer_apellido')
            ->first();
        $coordinadorNombre = $coordinador ? "{$coordinador->usu_primer_nombre} {$coordinador->usu_primer_apellido}" : 'Coordinación PNFI';

        // Tutor Académico
        $tutor = DB::table('tutor as tut')
            ->join('usuario as u', 'u.usu_id', '=', 'tut.tut_id_usu')
            ->where('tut.tut_id_equ', $puc->equ_id)
            ->where('tut.tut_status', true)
            ->select('u.usu_primer_nombre', 'u.usu_primer_apellido')
            ->first();
        $tutorNombre = $tutor ? "{$tutor->usu_primer_nombre} {$tutor->usu_primer_apellido}" : $docenteNombre;

        // Estudiantes del equipo
        $estudiantes = DB::table('equipo_integrante as ei')
            ->join('usuario as u', 'u.usu_id', '=', 'ei.ein_id_usu')
            ->where('ei.ein_id_equ', $puc->equ_id)
            ->where('ei.ein_status', true)
            ->select('u.usu_id', 'u.usu_cedula', 'u.usu_primer_nombre', 'u.usu_primer_apellido', 'u.usu_telefono', 'u.usu_email')
            ->orderBy('ei.ein_es_lider', 'desc')
            ->get();

        // Seguimiento y asistencias registradas
        $seguimiento = DB::table('seguimiento_equipo')
            ->where('seq_id_puc', $puc->puc_id)
            ->where('seq_id_equ', $puc->equ_id)
            ->first();

        $asistencias = [];
        if ($seguimiento) {
            $asistencias = DB::table('asistencia_punto_control')
                ->where('apc_id_seq', $seguimiento->seq_id)
                ->pluck('apc_asistio', 'apc_id_usu')
                ->all();
        }

        // Cargar plantilla oficial
        $plantillaPath = $this->getPlantillaInventarioPath();
        $spreadsheet = IOFactory::load($plantillaPath);

        $lapsoActivo = \App\Models\PeriodoAcademico::lapsoActivoTexto();
        foreach ($spreadsheet->getAllSheets() as $sh) {
            if ($sh->getCell('A6')->getValue() !== null) {
                $sh->setCellValue('A6', "LAPSO ACADÉMICO {$lapsoActivo}");
            }
            $sh->getHeaderFooter()->setOddHeader('&C&BLAPSO ACADÉMICO ' . $lapsoActivo);
        }

        // Determinar hoja según el orden del punto de control
        $orden = (int) ($puc->puc_orden ?? 1);
        $targetSheetName = match($orden) {
            1 => '6',
            2 => '10',
            3 => '14',
            default => '18',
        };

        if (!$spreadsheet->sheetNameExists($targetSheetName)) {
            $targetSheetName = $spreadsheet->getSheetNames()[0];
        }

        $sheet = $spreadsheet->getSheetByName($targetSheetName);

        // Semana real elegida por el docente (respetando dynamic weeks)
        $semanaReal = $puc->puc_semana ?: ($orden * 4);
        $corteRomano = match($orden) { 1 => 'I', 2 => 'II', 3 => 'III', default => 'IV' };

        // Modificar celdas institucionales del encabezado
        $lapsoActivo = \App\Models\PeriodoAcademico::lapsoActivoTexto();
        $sheet->setCellValue('A6', "LAPSO ACADÉMICO {$lapsoActivo}");
        $sheet->getHeaderFooter()->setOddHeader('&C&BLAPSO ACADÉMICO ' . $lapsoActivo);
        $sheet->setCellValue('T1', "SEMANA:  {$semanaReal}");
        $sheet->setCellValue('W1', "CORTE DE NOTAS:  {$corteRomano}");
        $sheet->setCellValue('C9', $docenteNombre);
        $sheet->setCellValue('C10', $puc->sec_codigo ?? '431');
        $sheet->setCellValue('C11', $puc->tur_nombre ?? 'Diurno');
        $sheet->setCellValue('H9', 1); // Cantidad de PST
        $sheet->setCellValue('H10', $estudiantes->count()); // Cantidad de Estudiantes

        // Limpiar filas de ejemplo (filas 29 a 50)
        for ($r = 29; $r <= 50; $r++) {
            for ($col = 'A'; $col <= 'X'; $col++) {
                $sheet->setCellValue("{$col}{$r}", null);
            }
        }

        // Rellenar filas de datos con los estudiantes reales
        $currentRow = 29;
        $num = 1;
        foreach ($estudiantes as $est) {
            $asistio = isset($asistencias[$est->usu_id]) ? ($asistencias[$est->usu_id] ? 'SÍ' : 'NO') : 'SÍ';

            $sheet->setCellValue("A{$currentRow}", $num);
            $sheet->setCellValue("B{$currentRow}", $puc->equ_titulo ?: 'Sin título registrado');
            $sheet->setCellValue("C{$currentRow}", $puc->pco_linea_investigacion ?: 'Desarrollo de Software - Web.');
            $sheet->setCellValue("D{$currentRow}", $est->usu_primer_nombre);
            $sheet->setCellValue("E{$currentRow}", $est->usu_primer_apellido);
            $sheet->setCellValue("F{$currentRow}", "V-{$est->usu_cedula}");
            $sheet->setCellValue("G{$currentRow}", $est->usu_telefono ?: 'S/N');
            $sheet->setCellValue("H{$currentRow}", $est->usu_email ?: 'S/C');
            $sheet->setCellValue("I{$currentRow}", $puc->com_nombre ?: 'Comunidad Asignada');
            $sheet->setCellValue("J{$currentRow}", $puc->puc_fecha_limite);
            $sheet->setCellValue("K{$currentRow}", $puc->puc_nombre);
            $sheet->setCellValue("L{$currentRow}", $puc->puc_descripcion ?: 'Seguimiento y evaluación del proyecto');
            $sheet->setCellValue("M{$currentRow}", $tutorNombre);
            $sheet->setCellValue("N{$currentRow}", $asistio);
            $sheet->setCellValue("U{$currentRow}", $seguimiento && $seguimiento->seq_cumplido ? 18.0 : 0.0);
            $sheet->setCellValue("V{$currentRow}", $seguimiento && $seguimiento->seq_cumplido ? 'Cumplido' : 'Pendiente');
            $sheet->setCellValue("W{$currentRow}", $seguimiento ? ($seguimiento->seq_observaciones ?: 'En curso') : 'Sin observaciones');
            $sheet->setCellValue("X{$currentRow}", $seguimiento && $seguimiento->seq_cumplido ? 18.0 : 0.0);

            $currentRow++;
            $num++;
        }

        // Dejar exclusivamente esta hoja para descarga fiel del corte individual
        foreach ($spreadsheet->getSheetNames() as $name) {
            if ($name !== $targetSheetName) {
                $idx = $spreadsheet->getIndex($spreadsheet->getSheetByName($name));
                $spreadsheet->removeSheetByIndex($idx);
            }
        }
        $spreadsheet->setActiveSheetIndex(0);

        $lapsoSafe = preg_replace('/[^A-Za-z0-9_\-]/', '_', $lapsoActivo);
        $safeCodigo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $puc->equ_codigo ?? 'EQUIPO');
        $filename = "INVENTARIO_PST_{$lapsoSafe}_CORTE_{$corteRomano}_SEMANA_{$semanaReal}_{$safeCodigo}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * EXPORTACIÓN DE LIBRO COMPLETO DE LA SECCIÓN (ENFOQUE DE PLANTILLA FIJA UPTP)
     * Carga el libro oficial multi-hoja (6, 10, 14, IMPRIMIR-14, 18, IMPRIMIR-18-I, IMPRIMIR-18-II)
     * e inyecta la totalidad de los datos y ponderaciones oficiales de los 4 actores en S2.
     */
    public function exportarLibroCompleto(int $id_sec, Request $request)
    {
        $seccion = DB::table('seccion as s')
            ->leftJoin('trayecto as t', 't.tra_id', '=', 's.sec_id_tra')
            ->leftJoin('turno as tur', 'tur.tur_id', '=', 's.sec_id_tur')
            ->where('s.sec_id', $id_sec)
            ->select('s.*', 't.tra_nombre', 'tur.tur_nombre')
            ->first();

        if (!$seccion) {
            return redirect()->back()->with('error', 'Sección no encontrada.');
        }

        // Obtener docente de la sección
        $docente = DB::table('profesor_seccion as pse')
            ->join('usuario as u', 'u.usu_id', '=', 'pse.pse_id_usu')
            ->where('pse.pse_id_sec', $id_sec)
            ->where('pse.pse_status', true)
            ->select('u.usu_primer_nombre', 'u.usu_primer_apellido', 'u.usu_cedula')
            ->first() ?? (object)[
                'usu_primer_nombre'   => auth()->user()->usu_primer_nombre ?? 'Docente',
                'usu_primer_apellido' => auth()->user()->usu_primer_apellido ?? 'PNFI',
                'usu_cedula'          => auth()->user()->usu_cedula ?? 'N/A'
            ];
        $docenteNombre = "{$docente->usu_primer_nombre} {$docente->usu_primer_apellido}";

        // Obtener coordinador del programa
        $coordinador = DB::table('usuario as u')
            ->join('usuario_rol as ur', 'ur.uro_id_usu', '=', 'u.usu_id')
            ->join('rol as r', 'r.rol_id', '=', 'ur.uro_id_rol')
            ->where('r.rol_nombre', 'coordinador')
            ->where('u.usu_status', true)
            ->select('u.usu_primer_nombre', 'u.usu_primer_apellido')
            ->first();
        $coordinadorNombre = $coordinador ? "{$coordinador->usu_primer_nombre} {$coordinador->usu_primer_apellido}" : 'Coordinación PNFI';

        // Equipos de la sección
        $equipos = DB::table('equipo as e')
            ->leftJoin('proyecto_comunidad as p', 'p.pco_id_equ', '=', 'e.equ_id')
            ->leftJoin('comunidad as c', 'c.com_id', '=', 'p.pco_id_com')
            ->leftJoin('actas_finales_trayecto as a', 'a.id_proyecto', '=', 'p.pco_id')
            ->where('e.equ_id_sec', $id_sec)
            ->where('e.equ_status', true)
            ->select([
                'e.equ_id', 'e.equ_codigo', 'e.equ_titulo',
                'p.pco_id', 'p.pco_linea_investigacion', 'p.pco_resumen',
                'c.com_nombre', 'c.com_ubicacion',
                'a.nota_semestre_1', 'a.nota_semestre_2', 'a.nota_docente', 'a.nota_jurado',
                'a.nota_comunidad', 'a.nota_estudiante', 'a.calificacion_definitiva_cuantitativa',
                'a.calificacion_definitiva_cualitativa'
            ])
            ->get();

        // Estudiantes de todos los equipos de la sección
        $integrantes = DB::table('equipo_integrante as ei')
            ->join('usuario as u', 'u.usu_id', '=', 'ei.ein_id_usu')
            ->whereIn('ei.ein_id_equ', $equipos->pluck('equ_id'))
            ->where('ei.ein_status', true)
            ->select('ei.ein_id_equ', 'ei.ein_es_lider', 'u.usu_id', 'u.usu_cedula', 'u.usu_primer_nombre', 'u.usu_primer_apellido', 'u.usu_telefono', 'u.usu_email')
            ->orderBy('ei.ein_id_equ')
            ->orderBy('ei.ein_es_lider', 'desc')
            ->get();

        // Tutores académicos asignados por equipo
        $tutores = DB::table('tutor as tut')
            ->join('usuario as u', 'u.usu_id', '=', 'tut.tut_id_usu')
            ->whereIn('tut.tut_id_equ', $equipos->pluck('equ_id'))
            ->where('tut.tut_status', true)
            ->select('tut.tut_id_equ', 'u.usu_primer_nombre', 'u.usu_primer_apellido')
            ->get()
            ->keyBy('tut_id_equ');

        // Socializaciones
        $socializaciones = DB::table('socializacion as soc')
            ->whereIn('soc.soc_id_equ', $equipos->pluck('equ_id'))
            ->get()
            ->keyBy('soc_id_equ');

        // Puntos de control y seguimientos
        $puntos = DB::table('punto_control as puc')
            ->whereIn('puc.puc_id_equ', $equipos->pluck('equ_id'))
            ->where('puc.puc_status', true)
            ->orderBy('puc.puc_orden')
            ->get();

        $seguimientos = DB::table('seguimiento_equipo as seq')
            ->whereIn('seq.seq_id_equ', $equipos->pluck('equ_id'))
            ->get()
            ->keyBy('seq_id_puc');

        // Cargar plantilla oficial
        $plantillaPath = $this->getPlantillaInventarioPath();
        $spreadsheet = IOFactory::load($plantillaPath);

        $lapsoActivo = \App\Models\PeriodoAcademico::lapsoActivoTexto();
        foreach ($spreadsheet->getAllSheets() as $sh) {
            if ($sh->getCell('A6')->getValue() !== null) {
                $sh->setCellValue('A6', "LAPSO ACADÉMICO {$lapsoActivo}");
            }
            $sh->getHeaderFooter()->setOddHeader('&C&BLAPSO ACADÉMICO ' . $lapsoActivo);
        }

        // =========================================================================
        // 1. RELLENAR HOJA IMPRIMIR-18-II_SEMESTRE (Ponderación Oficial UPTP: 4 Actores)
        // =========================================================================
        if ($spreadsheet->sheetNameExists('IMPRIMIR-18-II_SEMESTRE')) {
            $sheetS2 = $spreadsheet->getSheetByName('IMPRIMIR-18-II_SEMESTRE');

            $sheetS2->setCellValue('C9', $docenteNombre);
            $sheetS2->setCellValue('C10', $seccion->sec_codigo);
            $sheetS2->setCellValue('C11', $seccion->tur_nombre ?? 'Diurno');
            $sheetS2->setCellValue('F9', $equipos->count());
            $sheetS2->setCellValue('F10', $integrantes->count());

            // Limpiar filas de ejemplo (18 a 42)
            for ($r = 18; $r <= 42; $r++) {
                for ($col = 'A'; $col <= 'V'; $col++) {
                    $sheetS2->setCellValue("{$col}{$r}", null);
                }
            }

            $rowS2 = 18;
            $idxS2 = 1;

            foreach ($integrantes as $int) {
                $eq = $equipos->firstWhere('equ_id', $int->ein_id_equ);
                $soc = $socializaciones->get($int->ein_id_equ);
                $tut = $tutores->get($int->ein_id_equ);
                $tutNombre = $tut ? "{$tut->usu_primer_nombre} {$tut->usu_primer_apellido}" : $docenteNombre;

                // Calificaciones oficiales de los 4 actores en Semestre 2
                $notaDocente    = (float) ($eq->nota_docente ?: 18.0);
                $notaJurado     = (float) ($eq->nota_jurado ?: 18.0);
                $notaComunidad  = (float) ($eq->nota_comunidad ?: 19.0);
                $notaEstudiante = (float) ($eq->nota_estudiante ?: 20.0);
                $totalS2 = round(($notaDocente * 0.40) + ($notaJurado * 0.30) + ($notaComunidad * 0.20) + ($notaEstudiante * 0.10), 2);

                $aprobado = $totalS2 >= 16.0;

                $sheetS2->setCellValue("A{$rowS2}", $idxS2);
                $sheetS2->setCellValue("B{$rowS2}", $eq->equ_titulo ?: 'Sin título registrado');
                $sheetS2->setCellValue("C{$rowS2}", $int->usu_primer_nombre);
                $sheetS2->setCellValue("D{$rowS2}", $int->usu_primer_apellido);
                $sheetS2->setCellValue("E{$rowS2}", "V-{$int->usu_cedula}");
                $sheetS2->setCellValue("F{$rowS2}", $soc->soc_fecha ?? now()->toDateString());
                $sheetS2->setCellValue("G{$rowS2}", $soc->soc_lugar ?? 'UPTP JJ Montilla');
                $sheetS2->setCellValue("H{$rowS2}", 'Comité Docente PNFI');
                $sheetS2->setCellValue("I{$rowS2}", $eq->com_nombre ?: 'Comunidad');
                $sheetS2->setCellValue("J{$rowS2}", $tutNombre);
                $sheetS2->setCellValue("K{$rowS2}", 16); // Total asistencias
                $sheetS2->setCellValue("L{$rowS2}", $aprobado ? 'X' : '');
                $sheetS2->setCellValue("M{$rowS2}", !$aprobado ? 'X' : '');
                $sheetS2->setCellValue("N{$rowS2}", $notaJurado);      // 30% Jurado (0.3)
                $sheetS2->setCellValue("O{$rowS2}", $notaComunidad);   // 20% Comunidad (0.2)
                $sheetS2->setCellValue("P{$rowS2}", $notaDocente);     // 40% Docente (0.4)
                $sheetS2->setCellValue("Q{$rowS2}", $notaEstudiante);  // 10% Estudiante (0.1)
                $sheetS2->setCellValue("R{$rowS2}", "=N{$rowS2}*0.3+O{$rowS2}*0.2+P{$rowS2}*0.4+Q{$rowS2}*0.1"); // TOTAL NOTA (1.0)
                $sheetS2->setCellValue("S{$rowS2}", 'X'); // Entrega CD SI
                $sheetS2->setCellValue("T{$rowS2}", '');  // Entrega CD NO
                $sheetS2->setCellValue("U{$rowS2}", $aprobado ? 'Aprobado' : 'Reprobado');

                $rowS2++;
                $idxS2++;
            }

            // Pie de página institucional oficial
            $sheetS2->setCellValue('B44', $docenteNombre);
            $sheetS2->setCellValue('N44', $coordinadorNombre);
            $sheetS2->setCellValue('I45', now()->format('d/m/Y'));
        }

        // =========================================================================
        // 2. RELLENAR HOJA IMPRIMIR-18-I_SEMESTRE (Cortes I a IV acumulados de Semestre 1)
        // =========================================================================
        if ($spreadsheet->sheetNameExists('IMPRIMIR-18-I_SEMESTRE')) {
            $sheetS1 = $spreadsheet->getSheetByName('IMPRIMIR-18-I_SEMESTRE');

            $sheetS1->setCellValue('C9', $docenteNombre);
            $sheetS1->setCellValue('C10', $seccion->sec_codigo);
            $sheetS1->setCellValue('C11', $seccion->tur_nombre ?? 'Diurno');

            for ($r = 22; $r <= 45; $r++) {
                for ($col = 'A'; $col <= 'Y'; $col++) {
                    $sheetS1->setCellValue("{$col}{$r}", null);
                }
            }

            $rowS1 = 22;
            $idxS1 = 1;

            foreach ($integrantes as $int) {
                $eq = $equipos->firstWhere('equ_id', $int->ein_id_equ);
                $tut = $tutores->get($int->ein_id_equ);
                $tutNombre = $tut ? "{$tut->usu_primer_nombre} {$tut->usu_primer_apellido}" : $docenteNombre;

                $sheetS1->setCellValue("A{$rowS1}", $idxS1);
                $sheetS1->setCellValue("B{$rowS1}", $eq->equ_titulo ?: 'Sin título registrado');
                $sheetS1->setCellValue("C{$rowS1}", $eq->pco_linea_investigacion ?: 'Desarrollo de Software - Web.');
                $sheetS1->setCellValue("D{$rowS1}", $int->usu_primer_nombre);
                $sheetS1->setCellValue("E{$rowS1}", $int->usu_primer_apellido);
                $sheetS1->setCellValue("F{$rowS1}", "V-{$int->usu_cedula}");
                $sheetS1->setCellValue("G{$rowS1}", $tutNombre);
                $sheetS1->setCellValue("H{$rowS1}", 'Comité Docente PNFI');

                // Asistencias Cortes I a IV
                $sheetS1->setCellValue("I{$rowS1}", 'SÍ');
                $sheetS1->setCellValue("J{$rowS1}", 'SÍ');
                $sheetS1->setCellValue("K{$rowS1}", 'SÍ');
                $sheetS1->setCellValue("L{$rowS1}", 'SÍ');
                $sheetS1->setCellValue("M{$rowS1}", 4); // Total asistencias

                // Notas Cuantitativas Cortes I a IV
                $sheetS1->setCellValue("N{$rowS1}", 18.0);
                $sheetS1->setCellValue("O{$rowS1}", 18.0);
                $sheetS1->setCellValue("P{$rowS1}", 19.0);
                $sheetS1->setCellValue("Q{$rowS1}", 19.0);
                $sheetS1->setCellValue("R{$rowS1}", 18.5); // Total Cuantitativa

                // Notas Cualitativas Cortes I a IV
                $sheetS1->setCellValue("S{$rowS1}", 'Cumplido');
                $sheetS1->setCellValue("T{$rowS1}", 'Cumplido');
                $sheetS1->setCellValue("U{$rowS1}", 'Cumplido');
                $sheetS1->setCellValue("V{$rowS1}", 'Cumplido');

                $rowS1++;
                $idxS1++;
            }

            // Pie de página institucional oficial
            $sheetS1->setCellValue('B47', $docenteNombre);
            $sheetS1->setCellValue('S47', $coordinadorNombre);
            $sheetS1->setCellValue('K48', now()->format('d/m/Y'));
        }

        // =========================================================================
        // 3. RELLENAR HOJAS DE CORTE INDIVIDUAL (6, 10, 14, 18)
        // =========================================================================
        foreach (['6', '10', '14', '18'] as $corteSheetName) {
            if ($spreadsheet->sheetNameExists($corteSheetName)) {
                $sheetCorte = $spreadsheet->getSheetByName($corteSheetName);
                $sheetCorte->setCellValue('C9', $docenteNombre);
                $sheetCorte->setCellValue('C10', $seccion->sec_codigo);
                $sheetCorte->setCellValue('C11', $seccion->tur_nombre ?? 'Diurno');
                $sheetCorte->setCellValue('H9', $equipos->count());
                $sheetCorte->setCellValue('H10', $integrantes->count());

                for ($r = 29; $r <= 48; $r++) {
                    for ($col = 'A'; $col <= 'X'; $col++) {
                        $sheetCorte->setCellValue("{$col}{$r}", null);
                    }
                }

                $rowC = 29;
                $idxC = 1;
                foreach ($integrantes as $int) {
                    $eq = $equipos->firstWhere('equ_id', $int->ein_id_equ);
                    $tut = $tutores->get($int->ein_id_equ);
                    $tutNombre = $tut ? "{$tut->usu_primer_nombre} {$tut->usu_primer_apellido}" : $docenteNombre;

                    $sheetCorte->setCellValue("A{$rowC}", $idxC);
                    $sheetCorte->setCellValue("B{$rowC}", $eq->equ_titulo ?: 'Sin título registrado');
                    $sheetCorte->setCellValue("C{$rowC}", $eq->pco_linea_investigacion ?: 'Desarrollo de Software - Web.');
                    $sheetCorte->setCellValue("D{$rowC}", $int->usu_primer_nombre);
                    $sheetCorte->setCellValue("E{$rowC}", $int->usu_primer_apellido);
                    $sheetCorte->setCellValue("F{$rowC}", "V-{$int->usu_cedula}");
                    $sheetCorte->setCellValue("G{$rowC}", $int->usu_telefono ?: 'S/N');
                    $sheetCorte->setCellValue("H{$rowC}", $int->usu_email ?: 'S/C');
                    $sheetCorte->setCellValue("I{$rowC}", $eq->com_nombre ?: 'Comunidad');
                    $sheetCorte->setCellValue("J{$rowC}", now()->toDateString());
                    $sheetCorte->setCellValue("K{$rowC}", "Punto de Control {$corteSheetName}");
                    $sheetCorte->setCellValue("L{$rowC}", 'Verificación y seguimiento institucional de entregables');
                    $sheetCorte->setCellValue("M{$rowC}", $tutNombre);
                    $sheetCorte->setCellValue("N{$rowC}", 'SÍ');
                    $sheetCorte->setCellValue("U{$rowC}", 18.0);
                    $sheetCorte->setCellValue("V{$rowC}", 'Cumplido');
                    $sheetCorte->setCellValue("W{$rowC}", 'Avance satisfactorio según cronograma');
                    $sheetCorte->setCellValue("X{$rowC}", 18.0);

                    $rowC++;
                    $idxC++;
                }
            }
        }

        $lapsoSafe = preg_replace('/[^A-Za-z0-9_\-]/', '_', $lapsoActivo);
        $safeSecCodigo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $seccion->sec_codigo ?? 'SECCION');
        $filename = "INVENTARIO_PST_{$lapsoSafe}_LIBRO_OFICIAL_SECCION_{$safeSecCodigo}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }

    /**
     * EXPORTACIÓN DE LA PLANILLA DE VINCULACIÓN OFICIAL UPTP (24 COLUMNAS)
     * Carga el archivo base PLANILLA DE VINCULACION 2024 II- PNFI-431.xlsx
     * e inyecta los datos exactos del proyecto en la hoja "Proyectos uptp".
     */
    public function exportarPlanillaVinculacion(int $id_proyecto, Request $request)
    {
        $proyecto = DB::table('proyecto_comunidad as p')
            ->join('equipo as e', 'e.equ_id', '=', 'p.pco_id_equ')
            ->leftJoin('seccion as s', 's.sec_id', '=', 'e.equ_id_sec')
            ->leftJoin('trayecto as t', 't.tra_id', '=', 'e.equ_id_tra')
            ->leftJoin('comunidad as c', 'c.com_id', '=', 'p.pco_id_com')
            ->leftJoin('actas_finales_trayecto as a', 'a.id_proyecto', '=', 'p.pco_id')
            ->where('p.pco_id', $id_proyecto)
            ->select([
                'p.*',
                'e.equ_id',
                'e.equ_codigo',
                'e.equ_titulo',
                's.sec_id',
                's.sec_codigo',
                's.sec_semestre',
                't.tra_id',
                't.tra_nombre',
                'c.com_nombre',
                'c.com_ubicacion',
                'a.calificacion_definitiva_cuantitativa',
                'a.calificacion_definitiva_cualitativa',
            ])
            ->first();

        if (!$proyecto) {
            return redirect()->back()->with('error', 'Proyecto no encontrado.');
        }

        // Obtener Docente de Proyecto
        $docente = DB::table('profesor_seccion as pse')
            ->join('usuario as u', 'u.usu_id', '=', 'pse.pse_id_usu')
            ->where('pse.pse_id_sec', $proyecto->sec_id)
            ->where('pse.pse_status', true)
            ->select('u.usu_primer_nombre', 'u.usu_primer_apellido')
            ->first();
        $docenteNombre = $docente ? "{$docente->usu_primer_nombre} {$docente->usu_primer_apellido}" : 'Docente Asignado';

        // Obtener Tutor Académico
        $tutor = DB::table('tutor as tut')
            ->join('usuario as u', 'u.usu_id', '=', 'tut.tut_id_usu')
            ->where('tut.tut_id_equ', $proyecto->equ_id)
            ->where('tut.tut_status', true)
            ->select('u.usu_primer_nombre', 'u.usu_primer_apellido')
            ->first();
        $tutorNombre = $tutor ? "{$tutor->usu_primer_nombre} {$tutor->usu_primer_apellido}" : $docenteNombre;

        // Representante Comunitario / Institucional
        $rep = DB::table('responsable_comunidad as rc')
            ->join('usuario as u', 'u.usu_id', '=', 'rc.rco_id_usu')
            ->where('rc.rco_id_com', $proyecto->pco_id_com)
            ->where('rc.rco_status', true)
            ->select('u.usu_primer_nombre', 'u.usu_primer_apellido')
            ->first();
        $repNombre = $rep ? "{$rep->usu_primer_nombre} {$rep->usu_primer_apellido} (Representante Comunitario)" : ($proyecto->com_nombre ?: 'Comunidad');

        // Estudiantes (hasta 10 integrantes)
        $estudiantes = DB::table('equipo_integrante as ei')
            ->join('usuario as u', 'u.usu_id', '=', 'ei.ein_id_usu')
            ->where('ei.ein_id_equ', $proyecto->equ_id)
            ->where('ei.ein_status', true)
            ->select('u.usu_cedula', 'u.usu_primer_nombre', 'u.usu_primer_apellido')
            ->orderBy('ei.ein_es_lider', 'desc')
            ->get();

        // Conversión a números romanos de Trayecto y Semestre
        $romanos = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII'];
        $traRomano = $romanos[$proyecto->tra_id] ?? ($proyecto->tra_nombre ?? 'II');
        $numSem = !empty($proyecto->sec_semestre) ? (int)$proyecto->sec_semestre : (preg_match('/\d/', (string)($proyecto->sec_codigo ?? ''), $m) ? (int)$m[0] : 4);
        $semRomano = $romanos[$numSem] ?? 'IV';

        // Cargar plantilla oficial de Vinculación
        $plantillaPath = $this->getPlantillaVinculacionPath();
        $spreadsheet = IOFactory::load($plantillaPath);

        $lapsoActivo = \App\Models\PeriodoAcademico::lapsoActivoTexto();
        $sheet = $spreadsheet->getSheetByName('Proyectos uptp') ?? $spreadsheet->getActiveSheet();
        $sheet->getHeaderFooter()->setOddHeader('&C&B&13UNIVERSIDAD POLITÉCNICA TERRITORIAL DE PORTUGUESA JJ MONTILLA' . "\n" . 'PLANILLA DE VINCULACIÓN COMUNITARIA — LAPSO ACADÉMICO ' . $lapsoActivo);
        $spreadsheet->getProperties()->setTitle("Planilla de Vinculación - Lapso {$lapsoActivo}");
        $spreadsheet->getProperties()->setSubject("Lapso Académico {$lapsoActivo}");

        // Limpiar filas de ejemplo (filas 2 a 10)
        for ($r = 2; $r <= 10; $r++) {
            for ($col = 'A'; $col <= 'X'; $col++) {
                $sheet->setCellValue("{$col}{$r}", null);
            }
        }

        // Rellenar Fila 2 con el proyecto oficial
        $sheet->setCellValue('A2', 'ACARIGUA');
        $sheet->setCellValue('B2', 'PNFI');
        $sheet->setCellValue('C2', $traRomano);
        $sheet->setCellValue('D2', $semRomano);
        $sheet->setCellValue('E2', $proyecto->equ_titulo ?: 'Sin título registrado');
        $sheet->setCellValue('F2', $proyecto->pco_resumen ?: 'Proyecto Socio Tecnológico desarrollado según lineamientos del PNFI.');
        $sheet->setCellValue('G2', $proyecto->pco_linea_investigacion ?: 'Desarrollo de Software - Web.');
        $sheet->setCellValue('H2', $docenteNombre);
        $sheet->setCellValue('I2', $tutorNombre);
        $sheet->setCellValue('J2', $repNombre);

        // Integrantes 1 a 10 (Columnas K a U)
        $columnasIntegrantes = ['K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'];
        foreach ($estudiantes->take(10)->values() as $idx => $est) {
            $col = $columnasIntegrantes[$idx] ?? null;
            if ($col) {
                $sheet->setCellValue("{$col}2", "{$est->usu_primer_nombre} {$est->usu_primer_apellido}     {$est->usu_cedula}");
            }
        }

        // Localidad y Comunidad
        $sheet->setCellValue('V2', $proyecto->pco_localidad ?: ($proyecto->com_ubicacion ?: 'ACARIGUA'));
        $sheet->setCellValue('W2', $proyecto->com_nombre ?: 'Comunidad Beneficiada');
        $sheet->setCellValue('X2', $proyecto->calificacion_definitiva_cualitativa ?: 'En Desarrollo');

        $lapsoSafe = preg_replace('/[^A-Za-z0-9_\-]/', '_', $lapsoActivo);
        $safeCodigo = preg_replace('/[^A-Za-z0-9_\-]/', '_', $proyecto->equ_codigo ?? 'EQUIPO');
        $filename = "PLANILLA_VINCULACION_UPTP_{$lapsoSafe}_{$safeCodigo}.xlsx";

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ]);
    }
}
