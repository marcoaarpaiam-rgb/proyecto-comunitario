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

        $templatePath = storage_path('app/plantillas/PLANILLA DE VINCULACION 2024 II- PNFI-431.xlsx');
        if (!file_exists($templatePath)) {
            $templatePath = storage_path('app/plantillas/PLANILLA_VINCULACION_PLANTILLA_OFICIAL.xlsx');
        }
        if (!file_exists($templatePath)) {
            $templatePath = storage_path('app/templates/planilla_vinculacion_template.xlsx');
        }

        if (file_exists($templatePath)) {
            $reader = IOFactory::createReaderForFile($templatePath);
            $spreadsheet = $reader->load($templatePath);
            $sheet = $spreadsheet->getSheetByName('Proyectos uptp') ?? $spreadsheet->getActiveSheet();
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
                'DOCENTE DE PROYECTO ',
                'TUTOR ACADEMICO',
                'REPRESENTANTE INSTITUCIONAL',
                'INTEGRANTE Nº 1; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 2; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 3; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 4; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 5; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 6; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 7; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 7; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 8; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 9; CÉDULA DE IDENTIDAD',
                'INTEGRANTE Nº 10; CÉDULA DE IDENTIDAD',
                'LOCALIDAD GEOGRAFICA DONDE SE DESARROLLÓ EL PROYECTO (PARROQUIA, URBANIZACIÓN, BARRIO, ENTRE OTROS)',
                'COMUNIDAD BENEFICIADA ',
                'RESULTADO DE LA SOCIALIZACIÓN',
            ];

            foreach ($headers as $colIdx => $hText) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIdx + 1);
                $sheet->setCellValue("{$colLetter}1", $hText);
            }
        }

        // Anchos de columna óptimos para máxima legibilidad idénticos al formato oficial
        $columnWidths = [
            'A' => 18, // SEDE
            'B' => 22, // PNF
            'C' => 13, // TRAYECTO
            'D' => 13, // SEMESTRE
            'E' => 40, // TÍTULO DE PROYECTO
            'F' => 46, // RESUMEN O PRESENTACIÓN
            'G' => 26, // LÍNEA DE INVESTIGACIÓN
            'H' => 26, // DOCENTE DE PROYECTO
            'I' => 26, // TUTOR ACADEMICO
            'J' => 26, // REPRESENTANTE INSTITUCIONAL
            'K' => 28, // INTEGRANTE Nº 1
            'L' => 28, // INTEGRANTE Nº 2
            'M' => 28, // INTEGRANTE Nº 3
            'N' => 28, // INTEGRANTE Nº 4
            'O' => 28, // INTEGRANTE Nº 5
            'P' => 28, // INTEGRANTE Nº 6
            'Q' => 28, // INTEGRANTE Nº 7
            'R' => 28, // INTEGRANTE Nº 7 (slot 8)
            'S' => 28, // INTEGRANTE Nº 8 (slot 9)
            'T' => 28, // INTEGRANTE Nº 9 (slot 10)
            'U' => 28, // INTEGRANTE Nº 10 (slot 11)
            'V' => 28, // LOCALIDAD GEOGRAFICA
            'W' => 30, // COMUNIDAD BENEFICIADA
            'X' => 22, // RESULTADO DE LA SOCIALIZACIÓN
        ];
        foreach ($columnWidths as $col => $w) {
            $sheet->getColumnDimension($col)->setWidth($w);
        }

        // Configuración y estilo de encabezados (Fila 1)
        $sheet->getRowDimension(1)->setRowHeight(124.5);
        $sheet->getStyle('A1:X1')->applyFromArray([
            'font' => [
                'name' => 'Times New Roman',
                'size' => 12,
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['argb' => 'FFF2F2F2'],
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000'],
                ],
            ],
        ]);

        $profesor = $seccion->profesorActual?->usuario;
        $nombreProf = $profesor ? "{$profesor->usu_primer_nombre} {$profesor->usu_primer_apellido}" : 'Dexia Colmenarez';
        $trayectoRomano = self::romano($seccion->sec_id_tra ?? 1);
        $semestreRomano = self::romano($seccion->getSemestreNumero());

        // Limpiar filas de ejemplo previas si vienen del template (filas 2 a 50, columnas A a X)
        for ($r = 2; $r <= 50; $r++) {
            for ($c = 1; $c <= 24; $c++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($c);
                $sheet->setCellValue("{$colLetter}{$r}", null);
            }
        }

        $currentRow = 2;
        foreach ($seccion->equipos as $eq) {
            $proyecto = $eq->proyectoComunidad;
            $comunidad = $proyecto?->comunidad;
            $tutor = $eq->tutor?->usuario;
            $tutorNombre = $tutor ? "{$tutor->usu_primer_nombre} {$tutor->usu_primer_apellido}" : 'No asignado';
            $resultado = $eq->resultadoProyecto?->estadoProyecto?->epr_nombre ?? 'En Proceso';

            $linea = $proyecto?->pco_linea_investigacion 
                ?: ($seccion->esAlfabetizacion() ? 'Alfabetización Tecnológica' : 'Desarrollo de Software - Web');

            $repNombre = $comunidad?->com_contacto_nombre ?: ($comunidad?->com_nombre ?: 'Comunidad');

            $sheet->setCellValue("A{$currentRow}", 'ACARIGUA');
            $sheet->setCellValue("B{$currentRow}", 'PNFI');
            $sheet->setCellValue("C{$currentRow}", $trayectoRomano);
            $sheet->setCellValue("D{$currentRow}", $semestreRomano);
            $sheet->setCellValue("E{$currentRow}", $eq->equ_titulo ?: 'Sin título');
            $sheet->setCellValue("F{$currentRow}", $proyecto?->pco_resumen ?: 'Proyecto Socio Tecnológico desarrollado según lineamientos del PNFI.');
            $sheet->setCellValue("G{$currentRow}", $linea);
            $sheet->setCellValue("H{$currentRow}", $nombreProf);
            $sheet->setCellValue("I{$currentRow}", $tutorNombre);
            $sheet->setCellValue("J{$currentRow}", $repNombre);

            // Integrantes (Columnas K a U: 11 slots oficiales)
            $integrantes = $eq->integrantes->sortByDesc('ein_es_lider')->values();
            for ($i = 0; $i < 11; $i++) {
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(11 + $i);
                if (isset($integrantes[$i])) {
                    $u = $integrantes[$i]->usuario;
                    $nom = mb_strtoupper(trim("{$u?->usu_primer_nombre} {$u?->usu_primer_apellido}"), 'UTF-8');
                    $ced = $u?->usu_cedula ? (str_starts_with($u->usu_cedula, 'V-') ? $u->usu_cedula : "V-{$u->usu_cedula}") : 'V-00.000.000';
                    $texto = "{$nom}     {$ced}";
                    $sheet->setCellValue("{$colLetter}{$currentRow}", $texto);
                } else {
                    $sheet->setCellValue("{$colLetter}{$currentRow}", '');
                }
            }

            // Ubicación, Comunidad y Resultado
            $ubicacion = $proyecto?->pco_localidad ?: ($comunidad ? ($comunidad->com_ubicacion ?: $comunidad->com_direccion) : 'ACARIGUA');
            $comNombre = $comunidad ? $comunidad->com_nombre : 'Comunidad no asignada';

            $sheet->setCellValue("V{$currentRow}", $ubicacion);
            $sheet->setCellValue("W{$currentRow}", $comNombre);
            $sheet->setCellValue("X{$currentRow}", $resultado);

            // Altura dinámica para que el título y resumen nunca queden recortados
            $maxLen = max(strlen((string)($proyecto?->pco_resumen ?? '')), strlen((string)($eq->equ_titulo ?? '')));
            $dynamicHeight = max(160, min(250, ceil($maxLen / 35) * 16));
            $sheet->getRowDimension($currentRow)->setRowHeight($dynamicHeight);

            // Estilo visual exacto: Times New Roman 12pt, Centrado horizontal y vertical, WrapText, Bordes Finos
            $sheet->getStyle("A{$currentRow}:X{$currentRow}")->applyFromArray([
                'font' => [
                    'name' => 'Times New Roman',
                    'size' => 12,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ]);

            $currentRow++;
        }

        // Estilos
        $sheet->getStyle("A1:X1")->getFont()->setBold(true);
        $sheet->getStyle("A1:X1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFF2F2F2');

        $lapsoActivo = \App\Models\PeriodoAcademico::lapsoActivoTexto();
        $sheet->getHeaderFooter()->setOddHeader('&C&B&13UNIVERSIDAD POLITÉCNICA TERRITORIAL DE PORTUGUESA JJ MONTILLA' . "\n" . 'PLANILLA DE VINCULACIÓN COMUNITARIA — LAPSO ACADÉMICO ' . $lapsoActivo);
        $sheet->getHeaderFooter()->setOddFooter('&LPNF Informática - Sección ' . $seccion->sec_codigo . '&RPágina &P de &N');

        $spreadsheet->getProperties()
            ->setTitle("Planilla de Vinculación - Lapso {$lapsoActivo} - Sección {$seccion->sec_codigo}")
            ->setSubject("Lapso Académico {$lapsoActivo}");

        $lapsoSafe = preg_replace('/[^A-Za-z0-9_\-]/', '_', $lapsoActivo);
        $fileName = "Planilla_Vinculacion_{$lapsoSafe}_Sec_{$seccion->sec_codigo}_" . date('Ymd_His') . ".xlsx";
        $tempFile = tempnam(sys_get_temp_dir(), 'vinculacion_') . '.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($tempFile);

        return response()->download($tempFile, $fileName, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
