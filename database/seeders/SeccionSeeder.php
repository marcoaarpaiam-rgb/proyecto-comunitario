<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeccionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('seccion')->insert([
            ['sec_codigo' => '331', 'sec_id_tra' => 3, 'sec_id_tur' => 1, 'sec_status' => true],
            ['sec_codigo' => '332', 'sec_id_tra' => 3, 'sec_id_tur' => 2, 'sec_status' => true],
            ['sec_codigo' => '431', 'sec_id_tra' => 4, 'sec_id_tur' => 1, 'sec_status' => true],
            ['sec_codigo' => '231', 'sec_id_tra' => 2, 'sec_id_tur' => 1, 'sec_status' => true],
        ]);
    }
}