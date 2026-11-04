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

        $tnoProximo  = TipoNotificacion::where('tno_nombre', 'like', '%próximo%')->orderBy('tno_id')->first();
        $tnoVencido  = TipoNotificacion::where('tno_nombre', 'like', '%vencido%')->orderBy('tno_id')->first();
        $tnoCumplido = TipoNotificacion::where('tno_nombre', 'like', '%fuera de plazo%')->orderBy('tno_id')->first();

        if (!$tnoProximo || !$tnoVencido) {
            $this->error('Faltan tipos de notificación. Ejecuta el seeder.');
            return;
        }
        $puntos = PuntoControl::where('puc_status', true)
            ->with(['equipo.seccion'])
            ->get();

        foreach ($puntos as $punto) {
            $fechaEfectiva = $punto->puc_fecha_reprogramada ?? $punto->puc_fecha_limite;
            $diasRestantes = (int) now()->startOfDay()->diffInDays(\Carbon\Carbon::parse($fechaEfectiva)->startOfDay(), false);
            $profesores = ProfesorSeccion::where('pse_id_sec', $punto->equipo?->equ_id_sec)
                ->where('pse_status', true)
                ->pluck('pse_id_usu');
            $liderId = \App\Models\EquipoIntegrante::where('ein_id_equ', $punto->puc_id_equ)
                ->where('ein_es_lider', true)->where('ein_status', true)->value('ein_id_usu');
            $destinatarios = $profesores->push($liderId)->filter()->unique();

            foreach ($destinatarios as $profesorId) {
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