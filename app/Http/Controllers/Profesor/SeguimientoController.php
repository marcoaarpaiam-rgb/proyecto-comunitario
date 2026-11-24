<?php

namespace App\Http\Controllers\Profesor;

use App\Http\Controllers\Controller;

/**
 * Nota de arquitectura SISCON-PST:
 * El registro y control de seguimientos de equipos y asistencias a puntos de control
 * fue unificado en PuntoControlController::registrarSeguimiento() para mantener
 * la consistencia transaccional del flujo de evaluación docente.
 */
class SeguimientoController extends Controller
{
    // Controlador reservado; lógica unificada en PuntoControlController.
}
