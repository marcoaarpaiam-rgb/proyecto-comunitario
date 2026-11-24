<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartaPresentacion extends Model
{
    protected $table = 'carta_presentacion';
    protected $primaryKey = 'cpr_id';
    public $timestamps = false;

    protected $fillable = [
        'cpr_id_pco',
        'cpr_solicitada',
        'cpr_ruta_archivo',
        'cpr_firma_digital',
        'cpr_sello',
        'cpr_fecha_generacion',
        'cpr_fecha_aprobacion',
        'cpr_id_usu_created',
    ];

    public function proyectoComunidad()
    {
        return $this->belongsTo(ProyectoComunidad::class, 'cpr_id_pco', 'pco_id');
    }
}