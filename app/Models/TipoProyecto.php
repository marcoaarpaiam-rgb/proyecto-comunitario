<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoProyecto extends Model
{
    protected $table = 'tipo_proyecto';
    protected $primaryKey = 'tpr_id';
    public $timestamps = false;

    protected $fillable = [
        'tpr_nombre',
        'tpr_status',
        'tpr_created_at',
        'tpr_updated_at',
        'tpr_id_usu_created',
        'tpr_id_usu_updated',
    ];

    public function proyectosComunidad()
    {
        return $this->hasMany(ProyectoComunidad::class, 'pco_id_tpr', 'tpr_id');
    }

    /**
     * Obtiene el ID del TipoProyecto (Línea de Investigación) automático según el Trayecto:
     * - Trayecto I: Alfabetización Tecnológica
     * - Trayectos II, III y IV: Desarrollo de Software
     */
    public static function obtenerIdPorTrayecto($trayecto): int
    {
        $trayectoNum = is_numeric($trayecto) ? (int) $trayecto : 1;
        if ($trayectoNum === 1) {
            $tipo = static::whereRaw('LOWER(TRIM(tpr_nombre)) LIKE ?', ['%alfabetizaci%'])->first();
            if (!$tipo) {
                $tipo = static::create([
                    'tpr_nombre' => 'Alfabetización Tecnológica',
                    'tpr_status' => true,
                    'tpr_created_at' => now(),
                    'tpr_updated_at' => now(),
                ]);
            }
            return $tipo->tpr_id;
        }

        $tipo = static::whereRaw('LOWER(TRIM(tpr_nombre)) LIKE ?', ['%desarrollo de software%'])->first();
        if (!$tipo) {
            $tipo = static::create([
                'tpr_nombre' => 'Desarrollo de Software',
                'tpr_status' => true,
                'tpr_created_at' => now(),
                'tpr_updated_at' => now(),
            ]);
        }
        return $tipo->tpr_id;
    }

    public static function obtenerNombrePorTrayecto($trayecto): string
    {
        $trayectoNum = is_numeric($trayecto) ? (int) $trayecto : 1;
        return $trayectoNum === 1 ? 'Alfabetización Tecnológica' : 'Desarrollo de Software';
    }
}