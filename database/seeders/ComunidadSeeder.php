<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComunidadSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('comunidad')->insert([
            ['com_nombre' => 'Consejo Comunal Simón Bolívar', 'com_ubicacion' => 'Sector Bolívar, Acarigua', 'com_id_tor' => 1, 'com_status' => true],
            ['com_nombre' => 'Escuela Básica Juan de Dios Méndez', 'com_ubicacion' => 'Av. Principal, Acarigua', 'com_id_tor' => 1, 'com_status' => true],
            ['com_nombre' => 'Ambulatorio El Libertador', 'com_ubicacion' => 'Sector Libertador, Araure', 'com_id_tor' => 1, 'com_status' => true],
            ['com_nombre' => 'Cooperativa Los Andes', 'com_ubicacion' => 'Sector Industrial, Acarigua', 'com_id_tor' => 2, 'com_status' => true],
        ]);
    }
}