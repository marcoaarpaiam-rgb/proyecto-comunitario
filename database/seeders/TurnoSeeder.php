<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TurnoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('turno')->insert([
            ['tur_nombre' => 'Mañana', 'tur_status' => true],
            ['tur_nombre' => 'Tarde', 'tur_status' => true],
            ['tur_nombre' => 'Noche', 'tur_status' => true],
            ['tur_nombre' => 'Fin de Semana', 'tur_status' => true],
        ]);
    }
}