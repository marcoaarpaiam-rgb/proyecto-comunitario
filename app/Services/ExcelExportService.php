<?php

namespace App\Services;

use App\Models\Seccion;
use App\Models\Equipo;
use App\Models\PuntoControl;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExcelExportService
{
    private static function romano($num): string
    {
        $map = [
            1 => 'I', 2 => 'II', 3 => 'III', 4 => 'IV',
            5 => 'V', 6 => 'VI', 7 => 'VII', 8 => 'VIII'
        ];
        return $map[(int)$num] ?? (string)$num;
    }

    /**
     * Genera la Planilla de Vinculación de 24 columnas oficial de la UPTP
     */
    public function generarPlanillaVinculacion($seccionId): BinaryFileResponse
    {
        $seccion = Seccion::with([
            'trayecto',
            'turno',
            'profesorActual.usuario',
            'equipos' => function ($q) {
                $q->where('equ_status', true)
                  ->with([
                      'integrantes.usuario',
                      'proyectoComunidad.comunidad',
                      'proyectoComunidad.tipoProyecto',
                      'resultadoProyecto.estadoProyecto',
                      'tutor.usuario',
                  ]);
            }
        ])->findOrFail($seccionId);

        $templatePath = storage_path('app/templates/planilla_vinculacion_template.xlsx');

        if (file_exists($templatePath)) {
            $reader = IOFactory::createReaderForFile($templatePath);
            $spreadsheet = $reader->load($templatePath);
            $sheet = $spreadsheet->getActiveSheet();
        } else {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setTitle('Proyectos uptp');

            $headers = [
                'SEDE',
                'PROGRAMA NACIONAL DE FORMACIÓN',
                'TRAYECTO',
                'SEMESTRE',
                'TÍTULO DE PROYECTO',
                'RESUMEN O PRESENTACIÓN (NO MAS DE 150 PALABRAS)',
                'LÍNEA DE INVESTIGACIÓN',
                'DOCENTE DE PROYECTO',
                'TUTOR ACADEMICO',
                'REPRESENTANTE INSTITUCIONAL',
                'INTEGRANTE Nº 1; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 2; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 3; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 4; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 5; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 6; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 7; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 8; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 9; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 10; CÉDULA DE IDENTIDAD',
                'LOCALIDAD GEOGRAFICA DONDE SE DESARROLLÓ EL PROYECTO (PARROQUIA, URBANIZACIÓN, BARRIO, ENTRE OTROS)',
                'COMUNIDAD BENEFICIADA',
                'RESULTADO DE LA SOCIALIZACIÓN',
            ];

            foreach ($headers as $colIdx => $hText) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                $sheet->setCellValue("{$colLetter}1", $hText);
            }
        }

        $profesor = $seccion->profesorActual?->usuario;
        $nombreProf = $profesor ? "{$profesor->usu_primer_nombre} {$profesor->usu_primer_apellido}" : 'Dexia Colmenarez';
        $trayectoRomano = self::romano($seccion->sec_id_tra ?? 1);
        $semestreRomano = self::romano($seccion->sec_semestre ?? ($seccion->sec_id_tra ? $seccion->sec_id_tra * 2 : 2));

        $startRow = 2;
        // Limpiar filas previas si vienen del template
        for ($r = 2; $r <= 50; $r++) {
            $sheet->setCellValue("A{$r}", '');
        }

        $currentRow = $startRow;
        foreach ($seccion->equipos as $eq) {
            $proyecto = $eq->proyectoComunidad;
            $comunidad = $proyecto?->comunidad;
            $tutor = $eq->tutor?->usuario;
            $tutorNombre = $tutor ? "{$tutor->usu_primer_nombre} {$tutor->usu_primer_apellido}" : 'No asignado';
            $resultado = $eq->resultadoProyecto?->estadoProyecto?->epr_nombre ?? 'En Proceso';

            $linea = $proyecto?->pco_linea_investigacion 
                ?: ($seccion->esAlfabetizacion() ? 'Alfabetización Tecnológica' : 'Desarrollo de Software - Web');

            $sheet->setCellValue("A{$currentRow}", 'ACARIGUA');
            $sheet->setCellValue("B{$currentRow}", 'PNFI');
            $sheet->setCellValue("C{$currentRow}", $trayectoRomano);
            $sheet->setCellValue("D{$currentRow}", $semestreRomano);
            $sheet->setCellValue("E{$currentRow}", $eq->equ_titulo ?: 'Sin título');
            $sheet->setCellValue("F{$currentRow}", $proyecto?->pco_resumen ?: 'Proyecto Socio Tecnológico');
            $sheet->setCellValue("G{$currentRow}", $linea);
            $sheet->setCellValue("H{$currentRow}", $nombreProf);
            $sheet->setCellValue("I{$currentRow}", $tutorNombre);
            $sheet->setCellValue("J{$currentRow}", $comunidad?->com_contacto_nombre ?: 'Comunidad');

            // Integrantes (Columnas K a T, es decir col 11 a 20)
            $integrantes = $eq->integrantes->sortByDesc('ein_es_lider')->values();
            for ($i = 0; $i < 10; $i++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(11 + $i);
                if (isset($integrantes[$i])) {
                    $u = $integrantes[$i]->usuario;
                    $texto = "{$u?->usu_primer_nombre} {$u?->usu_primer_apellido}; {$u?->usu_cedula}";
                    if ($integrantes[$i]->ein_es_lider) {
                        $texto .= " (Líder)";
                    }
                    $sheet->setCellValue("{$colLetter}{$currentRow}", $texto);
                } else {
                    $sheet->setCellValue("{$colLetter}{$currentRow}", '');
                }
            }

            // Ubicación (col 21 / U o col 22 / V)
            $ubicacion = $comunidad ? ($comunidad->com_ubicacion ?: $comunidad->com_direccion) : 'Acarigua, Edo. Portuguesa';
            $comNombre = $comunidad ? $comunidad->com_nombre : 'Comunidad no asignada';

            $sheet->setCellValue("V{$currentRow}", $ubicacion);
            $sheet->setCellValue("W{$currentRow}", $comNombre);
            $sheet->setCellValue("X{$currentRow}", $resultado);

            $currentRow++;
        }

        // Estilos
        $sheet->getStyle("A1:X1")->getFont()->setBold(true);
        $sheet->getStyle("A1:X1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');

        $fileName = "Planilla_Vinculacion_Sec_{$seccion->sec_codigo}_" . date('Ymd_His') . ".xlsx";
        $tempFile = tempnam(sys_get_temp_dir(), 'vinculacion_') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }

    /**
     * Genera el Inventario PST multi-semanas (Semana 6, 10, 14, 18)
     */
    public function generarInventarioPst($seccionId): BinaryFileResponse
    {
        $seccion = Seccion::with([
            'trayecto',
            'turno',
            'profesorActual.usuario',
            'equipos' => function ($q) {
                $q->where('equ_status', true)
                  ->with([
                      'integrantes.usuario',
                      'proyectoComunidad.comunidad',
                      'puntosControl.seguimientos.asistencias',
                      'tutor.usuario',
                      'resultadoProyecto',
                  ]);
            }
        ])->findOrFail($seccionId);

        $templatePath = storage_path('app/templates/inventario_pst_template.xls');

        if (file_exists($templatePath)) {
            $reader = IOFactory::createReaderForFile($templatePath);
            $spreadsheet = $reader->load($templatePath);
        } else {
            $spreadsheet = new Spreadsheet();
            $spreadsheet->removeSheetByIndex(0);
        }

        $semanas = [6, 10, 14, 18];
        $profesor = $seccion->profesorActual?->usuario;
        $nombreProf = $profesor ? "{$profesor->usu_primer_nombre} {$profesor->usu_primer_apellido}" : 'Docente PNF';
        $turnoNombre = $seccion->turno?->tur_nombre ?? 'Diurno';
        $totalPst = $seccion->equipos->count();

        // Contar estudiantes únicos
        $todosIntegrantes = $seccion->equipos->flatMap(fn($e) => $e->integrantes)->pluck('usuario')->filter()->unique('usu_id');
        $totalEstudiantes = $todosIntegrantes->count();

        foreach ($semanas as $sem) {
            $sheetName = (string)$sem;
            $sheet = $spreadsheet->getSheetByName($sheetName);
            if (!$sheet) {
                $sheet = $spreadsheet->createSheet();
                $sheet->setTitle($sheetName);
            }

            // Encabezado general del inventario
            $sheet->setCellValue('A5', 'INVENTARIO DE PROYECTO SOCIO TECNOLÓGICO — PNF INFORMÁTICA');
            $sheet->setCellValue('A6', 'LAPSO ACADÉMICO ' . date('Y') . '-' . ($seccion->esSemestrePar() ? 'II' : 'I'));
            $sheet->setCellValue('C9', $nombreProf);
            $sheet->setCellValue('H8', "SEMANA {$sem}");
            $sheet->setCellValue('C10', $seccion->sec_codigo);
            $sheet->setCellValue('C11', $turnoNombre);
            $sheet->setCellValue('H9', $totalPst);
            $sheet->setCellValue('H10', $totalEstudiantes);

            // Poblar equipos a partir de la fila 29
            $row = 29;
            $num = 1;
            foreach ($seccion->equipos as $eq) {
                $proyecto = $eq->proyectoComunidad;
                $comunidad = $proyecto?->comunidad;
                $tutor = $eq->tutor?->usuario;
                $tutorNombre = $tutor ? "{$tutor->usu_primer_nombre} {$tutor->usu_primer_apellido}" : 'Sin asignar';

                $linea = $proyecto?->pco_linea_investigacion 
                    ?: ($seccion->esAlfabetizacion() ? 'Alfabetización Tecnológica' : 'Desarrollo de Software - Web');

                $sheet->setCellValue("A{$row}", $num);
                $sheet->setCellValue("B{$row}", $eq->equ_titulo ?: "Equipo {$eq->equ_codigo}");
                $sheet->setCellValue("C{$row}", $linea);

                // Integrantes
                $integrantes = $eq->integrantes->values();
                $intRow = $row;
                foreach ($integrantes as $intIdx => $integ) {
                    $u = $integ->usuario;
                    if ($intIdx > 0 && $intIdx < 6) {
                        $intRow = $row + $intIdx;
                    }
                    $sheet->setCellValue("D{$intRow}", $u?->usu_primer_nombre ?? '');
                    $sheet->setCellValue("E{$intRow}", $u?->usu_primer_apellido ?? '');
                    $sheet->setCellValue("F{$intRow}", $u?->usu_cedula ?? '');
                    $sheet->setCellValue("G{$intRow}", $u?->usu_telefono ?? 'N/A');
                    $sheet->setCellValue("H{$intRow}", $u?->usu_email ?? 'N/A');
                }

                $sheet->setCellValue("I{$row}", $comunidad?->com_nombre ?? 'Comunidad');
                $sheet->setCellValue("J{$row}", $comunidad?->com_ubicacion ?? 'Ubicación');
                $sheet->setCellValue("M{$row}", $tutorNombre);

                // Asistencias acumuladas en puntos de control hasta la semana actual
                $asistenciasCount = 0;
                foreach ($eq->puntosControl as $pc) {
                    if (($pc->puc_semana ?? 0) <= $sem) {
                        foreach ($pc->seguimientos as $seq) {
                            $asistenciasCount += $seq->asistencias->where('apc_asistio', true)->count();
                        }
                    }
                }
                $sheet->setCellValue("N{$row}", $asistenciasCount);

                $row = max($row + 1, $intRow + 1);
                $num++;
            }
        }

        $fileName = "Inventario_PST_Sec_{$seccion->sec_codigo}_" . date('Ymd_His') . ".xlsx";
        $tempFile = tempnam(sys_get_temp_dir(), 'inventario_') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
