<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoBeneficiario extends Model
{
    protected $table = 'tipo_beneficiario';
    protected $primaryKey = 'tbe_id';
    public $timestamps = false;

    protected $fillable = [
        'tbe_nombre',
        'tbe_status',
        'tbe_created_at',
        'tbe_updated_at',
        'tbe_id_usu_created',
        'tbe_id_usu_updated',
    ];

    public function proyectosBeneficiario()
    {
        return $this->hasMany(ProyectoBeneficiario::class, 'pbe_id_tbe', 'tbe_id');
    }
}