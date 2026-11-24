<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pnf extends Model
{
    protected $table = 'pnf';
    protected $primaryKey = 'pnf_id';
    public $timestamps = false;

    protected $fillable = [
        'pnf_nombre',
        'pnf_siglas',
        'pnf_status',
        'pnf_created_at',
        'pnf_updated_at',
        'pnf_id_usu_created',
        'pnf_id_usu_updated',
    ];

    public function trayectos()
    {
        return $this->hasMany(Trayecto::class, 'tra_id_pnf', 'pnf_id');
    }
}