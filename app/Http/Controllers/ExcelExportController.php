<?php

namespace App\Http\Controllers;

use App\Services\ExcelExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExcelExportController extends Controller
{
    protected ExcelExportService $excelService;

    public function __construct(ExcelExportService $excelService)
    {
        $this->excelService = $excelService;
    }

    public function descargarPlanillaVinculacion($seccionId)
    {
        return $this->excelService->generarPlanillaVinculacion($seccionId);
    }

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
     * Exporta la Planilla de Vinculación Oficial UPTP (24 columnas) para un proyecto.
     */
    public function exportarPlanillaVinculacionProyecto(int $id_proyecto, Request $request)
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

        $docente = DB::table('profesor_seccion as pse')
            ->join('usuario as u', 'u.usu_id', '=', 'pse.pse_id_usu')
            ->where('pse.pse_id_sec', $proyecto->sec_id)
            ->where('pse.pse_status', true)
            ->select('u.usu_primer_nombre', 'u.usu_primer_apellido')
            ->first();
        $docenteNombre = $docente ? "{$docente->usu_primer_nombre} {$docente->usu_primer_apellido}" : 'Docente Asignado';

        $tutor = DB::table('tutor as tut')
            ->join('usuario as u', 'u.usu_id', '=', 'tut.tut_id_usu')
            ->where('tut.tut_id_equ', $proyecto->equ_id)
            ->where('tut.tut_status', true)
            ->select('u.usu_primer_nombre', 'u.usu_primer_apellido')
            ->first();
        $tutorNombre = $tutor ? "{$tutor->usu_primer_nombre} {$tutor->usu_primer_apellido}" : $docenteNombre;

        $rep = DB::table('responsable_comunidad as rc')
            ->join('usuario as u', 'u.usu_id', '=', 'rc.rco_id_usu')
            ->where('rc.rco_id_com', $proyecto->pco_id_com)
            ->where('rc.rco_status', true)
            ->select('u.usu_primer_nombre', 'u.usu_primer_apellido')
            ->first();
        $repNombre = $rep ? "{$rep->usu_primer_nombre} {$rep->usu_primer_apellido} (Representante Comunitario)" : ($proyecto->com_nombre ?: 'Comunidad');

        $estudiantes = DB::table('equipo_integrante as ei')
            ->join('usuario as u', 'u.usu_id', '=', 'ei.ein_id_usu')
            ->where('ei.ein_id_equ', $proyecto->equ_id)
            ->where('ei.ein_status', true)
            ->select('u.usu_cedula', 'u.usu_primer_nombre', 'u.usu_primer_apellido')
            ->orderBy('ei.ein_es_lider', 'desc')
            ->get();

        $romanos = [1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV', 5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII'];
        $traRomano = $romanos[$proyecto->tra_id] ?? ($proyecto->tra_nombre ?? 'II');
        $numSem = !empty($proyecto->sec_semestre) ? (int) $proyecto->sec_semestre : (preg_match('/\d/', (string) ($proyecto->sec_codigo ?? ''), $m) ? (int) $m[0] : 4);
        $semRomano = $romanos[$numSem] ?? 'IV';

        $plantillaPath = $this->getPlantillaVinculacionPath();
        $spreadsheet = IOFactory::load($plantillaPath);

        $lapsoActivo = \App\Models\PeriodoAcademico::lapsoActivoTexto();
        $sheet = $spreadsheet->getSheetByName('Proyectos uptp') ?? $spreadsheet->getActiveSheet();
        $sheet->getHeaderFooter()->setOddHeader('&C&B&13UNIVERSIDAD POLITÉCNICA TERRITORIAL DE PORTUGUESA JJ MONTILLA' . "\n" . 'PLANILLA DE VINCULACIÓN COMUNITARIA — LAPSO ACADÉMICO ' . $lapsoActivo);
        $spreadsheet->getProperties()->setTitle("Planilla de Vinculación - Lapso {$lapsoActivo}");
        $spreadsheet->getProperties()->setSubject("Lapso Académico {$lapsoActivo}");

        $columnWidths = [
            'A' => 18, 'B' => 22, 'C' => 13, 'D' => 13, 'E' => 40, 'F' => 46,
            'G' => 26, 'H' => 26, 'I' => 26, 'J' => 26, 'K' => 28, 'L' => 28,
            'M' => 28, 'N' => 28, 'O' => 28, 'P' => 28, 'Q' => 28, 'R' => 28,
            'S' => 28, 'T' => 28, 'U' => 28, 'V' => 28, 'W' => 30, 'X' => 22,
        ];
        foreach ($columnWidths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        $sheet->getRowDimension(1)->setRowHeight(124.5);
        $sheet->getStyle('A1:X1')->applyFromArray([
            'font'      => ['name' => 'Times New Roman', 'size' => 12, 'bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FFF2F2F2']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

        for ($r = 2; $r <= 20; $r++) {
            for ($col = 'A'; $col <= 'X'; $col++) {
                $sheet->setCellValue("{$col}{$r}", null);
            }
        }

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

        $columnasIntegrantes = ['K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U'];
        foreach ($columnasIntegrantes as $idx => $col) {
            $est = $estudiantes[$idx] ?? null;
            if ($est) {
                $nom = mb_strtoupper(trim("{$est->usu_primer_nombre} {$est->usu_primer_apellido}"), 'UTF-8');
                $ced = $est->usu_cedula ? (str_starts_with($est->usu_cedula, 'V-') ? $est->usu_cedula : "V-{$est->usu_cedula}") : 'V-00.000.000';
                $sheet->setCellValue("{$col}2", "{$nom}     {$ced}");
            } else {
                $sheet->setCellValue("{$col}2", '');
            }
        }

        $sheet->setCellValue('V2', $proyecto->pco_localidad ?: ($proyecto->com_ubicacion ?: 'ACARIGUA'));
        $sheet->setCellValue('W2', $proyecto->com_nombre ?: 'Comunidad Beneficiada');
        $sheet->setCellValue('X2', $proyecto->calificacion_definitiva_cualitativa ?: 'En Desarrollo');

        $maxLen = max(strlen((string) ($proyecto->pco_resumen ?? '')), strlen((string) ($proyecto->equ_titulo ?? '')));
        $dynamicHeight = max(180, min(260, ceil($maxLen / 35) * 16));
        $sheet->getRowDimension(2)->setRowHeight($dynamicHeight);

        $sheet->getStyle('A2:X2')->applyFromArray([
            'font'      => ['name' => 'Times New Roman', 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);

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
