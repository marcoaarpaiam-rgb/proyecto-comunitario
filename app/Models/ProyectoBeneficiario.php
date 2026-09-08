<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProyectoBeneficiario extends Model
{
    protected $table = 'proyecto_beneficiario';
    protected $primaryKey = 'pbe_id';
    public $timestamps = false;

    protected $fillable = [
        'pbe_cantidad',
        'pbe_created_at',
        'pbe_updated_at',
        'pbe_id_pco',
        'pbe_id_tbe',
        'pbe_id_usu_created',
        'pbe_id_usu_updated',
    ];

    public function proyectoComunidad()
    {
        return $this->belongsTo(ProyectoComunidad::class, 'pbe_id_pco', 'pco_id');
    }

    public function tipoBeneficiario()
    {
        return $this->belongsTo(TipoBeneficiario::class, 'pbe_id_tbe', 'tbe_id');
    }
}