<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Equipo;

trait AutorizaPorSeccion
{
    /** El profesor solo toca equipos de sus secciones; el coordinador, todos. */
    protected function autorizarEquipoId($equipoId): void
    {
        $user = auth()->user();
        if (!$user->esProfesor()) {
            return;
        }

        $secId = Equipo::where('equ_id', $equipoId)->value('equ_id_sec');

        abort_unless(
            $secId && $user->seccionesProfesor()
                ->where('pse_status', true)
                ->where('pse_id_sec', $secId)
                ->exists(),
            403
        );
    }
}