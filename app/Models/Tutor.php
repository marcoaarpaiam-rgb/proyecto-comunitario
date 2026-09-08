<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    protected $table = 'tutor';
    protected $primaryKey = 'tut_id';
    public $timestamps = false;

    protected $fillable = [
        'tut_status',
        'tut_created_at',
        'tut_updated_at',
        'tut_id_equ',
        'tut_id_usu',
        'tut_id_usu_created',
        'tut_id_usu_updated',
    ];

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'tut_id_equ', 'equ_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'tut_id_usu', 'usu_id');
    }
}