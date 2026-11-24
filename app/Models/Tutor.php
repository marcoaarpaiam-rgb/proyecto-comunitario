<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    protected $table      = 'tutor';
    protected $primaryKey = 'tut_id';
    public    $timestamps = false;

    protected $fillable = [
        'tut_id_equ',
        'tut_id_usu',
        'tut_status',
        'tut_id_usu_created',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'tut_id_usu', 'usu_id');
    }

    public function equipo()
    {
        return $this->belongsTo(Equipo::class, 'tut_id_equ', 'equ_id');
    }
}