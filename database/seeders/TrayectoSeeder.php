<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrayectoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('trayecto')->insert([
            ['tra_numero' => 1, 'tra_nombre' => 'Trayecto I', 'tra_id_pnf' => 1, 'tra_status' => true],
            ['tra_numero' => 2, 'tra_nombre' => 'Trayecto II', 'tra_id_pnf' => 1, 'tra_status' => true],
            ['tra_numero' => 3, 'tra_nombre' => 'Trayecto III', 'tra_id_pnf' => 1, 'tra_status' => true],
            ['tra_numero' => 4, 'tra_nombre' => 'Trayecto IV', 'tra_id_pnf' => 1, 'tra_status' => true],
        ]);
    }
}