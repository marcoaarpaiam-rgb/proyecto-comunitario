<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEncuentro extends Model
{
    protected $table = 'tipos_encuentro_puntual';
    protected $primaryKey = 'tep_id';
    public $timestamps = false;

    protected $fillable = [
        'tep_nombre',
        'tep_descripcion',
        'tep_activo',
    ];

    protected $casts = [
        'tep_activo' => 'boolean',
    ];

    public function seguimientos()
    {
        return $this->hasMany(SeguimientoEquipo::class, 'seq_id_tipo_encuentro', 'tep_id');
    }
}
