<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoEntregableTrayecto extends Model
{
    protected $table = 'tipo_entregable_trayecto';
    protected $primaryKey = 'tet_id';
    public $timestamps = false;

    protected $fillable = [
        'tet_nombre',
        'tet_trayecto_numero',
        'tet_status',
        'tet_created_at',
        'tet_updated_at',
        'tet_id_usu_created',
        'tet_id_usu_updated',
    ];

    public function entregables()
    {
        return $this->hasMany(Entregable::class, 'entr_id_tet', 'tet_id');
    }
}