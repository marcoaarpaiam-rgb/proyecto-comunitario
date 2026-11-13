<?php

namespace App\Http\Controllers;

use App\Services\ExcelExportService;
use Illuminate\Http\Request;

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

    public function descargarInventarioPst($seccionId)
    {
        return $this->excelService->generarInventarioPst($seccionId);
    }
}
