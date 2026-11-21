<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AcuerdoEncuentro extends Model
{
    protected $table = 'acuerdos_encuentro';
    protected $primaryKey = 'acue_id';

    protected $fillable = [
        'acue_id_seq',
        'acue_descripcion',
        'acue_estado',
        'acue_fecha_limite',
        'acue_observacion_cierre',
    ];

    protected $casts = [
        'acue_fecha_limite' => 'date',
    ];

    public function seguimiento()
    {
        return $this->belongsTo(SeguimientoEquipo::class, 'acue_id_seq', 'seq_id');
    }
}
