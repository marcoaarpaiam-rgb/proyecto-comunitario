<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorialEquipo extends Model
{
    protected $table = 'historial_equipo';
    protected $primaryKey = 'heq_id';
    public $timestamps = false;

    protected $fillable = [
        'heq_motivo',
        'heq_fecha_evento',
        'heq_created_at',
        'heq_id_equ',
        'heq_id_tee',
        'heq_id_usu_created',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'heq_id_equ', 'equ_id');
    }

    public function tipoEvento()
    {
        return $this->belongsTo(TipoEventoEquipo::class, 'heq_id_tee', 'tee_id');
    }
}