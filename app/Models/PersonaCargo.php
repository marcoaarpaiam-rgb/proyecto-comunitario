<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PersonaCargo extends Model
{
    protected $table = 'persona_cargo';
    protected $primaryKey = 'pca_id';
    public $timestamps = false;

    protected $fillable = [
        'pca_cargo',
        'pca_fecha_inicio',
        'pca_fecha_fin',
        'pca_status',
        'pca_created_at',
        'pca_id_usu',
        'pca_id_usu_created',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'pca_id_usu', 'usu_id');
    }
}