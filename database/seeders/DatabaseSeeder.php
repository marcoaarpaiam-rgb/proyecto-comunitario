<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TrayectoSeeder::class,
            TurnoSeeder::class,
            SeccionSeeder::class,
            UsuarioSeeder::class,
            ComunidadSeeder::class,
            EquipoSeeder::class,
        ]);
    }
}