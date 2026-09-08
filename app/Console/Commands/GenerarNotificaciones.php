<?php

namespace App\Console\Commands;

use App\Models\PuntoControl;
use App\Models\Notificacion;
use App\Models\TipoNotificacion;
use App\Models\ProfesorSeccion;
use Illuminate\Console\Command;

class GenerarNotificaciones extends Command
{
    protected $signature   = 'notificaciones:generar';
    protected $description = 'Genera notificaciones automáticas de puntos de control';

    public function handle(): void
    {
        $hoy = now()->toDateString();

        $tnoProximo  = TipoNotificacion::where('tno_nombre', 'like', '%próximo%')->first();
        $tnoVencido  = TipoNotificacion::where('tno_nombre', 'like', '%vencido%')->first();
        $tnoCumplido = TipoNotificacion::where('tno_nombre', 'like', '%fuera de plazo%')->first();

        $puntos = PuntoControl::where('puc_status', true)
            ->with(['equipo.seccion'])
            ->get();

        foreach ($puntos as $punto) {
            $diasRestantes = now()->diffInDays($punto->puc_fecha_limite, false);
            $profesores = ProfesorSeccion::where('pse_id_sec', $punto->equipo?->equ_id_sec)
                ->where('pse_status', true)
                ->pluck('pse_id_usu');

            foreach ($profesores as $profesorId) {
                // Próximo a vencer
                if ($diasRestantes > 0 && $diasRestantes <= $punto->puc_dias_aviso) {
                    $this->crearSiNoExiste($profesorId, $tnoProximo?->tno_id,
                        $punto->puc_id, $punto->equipo?->equ_id,
                        "El punto de control \"{$punto->puc_nombre}\" vence en {$diasRestantes} día(s)."
                    );
                }

                // Vencido sin cumplir
                if ($diasRestantes < 0) {
                    $cumplido = $punto->seguimientos()
                        ->where('seq_cumplido', true)->exists();
                    if (!$cumplido) {
                        $this->crearSiNoExiste($profesorId, $tnoVencido?->tno_id,
                            $punto->puc_id, $punto->equipo?->equ_id,
                            "El punto de control \"{$punto->puc_nombre}\" venció sin cumplirse."
                        );
                    }
                }
            }
        }

        $this->info('Notificaciones generadas correctamente.');
    }

    private function crearSiNoExiste(
        int $usuId, ?int $tnoId, int $pucId, ?int $equId, string $mensaje
    ): void {
        $existe = Notificacion::where('not_id_usu', $usuId)
            ->where('not_id_puc', $pucId)
            ->where('not_id_tno', $tnoId)
            ->exists();

        if (!$existe) {
            Notificacion::create([
                'not_id_usu'          => $usuId,
                'not_id_tno'          => $tnoId,
                'not_id_equ'          => $equId,
                'not_id_puc'          => $pucId,
                'not_mensaje'         => $mensaje,
                'not_leida'           => false,
                'not_fecha_generacion'=> now(),
            ]);
        }
    }
}