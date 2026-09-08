<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        // Coordinador
        DB::table('usuario')->insert([
            'usu_cedula'          => '12345678',
            'usu_primer_nombre'   => 'Claudia',
            'usu_primer_apellido' => 'Coordinadora',
            'usu_email'           => 'coordinador@uptp.edu.ve',
            'usu_tiene_acceso'    => true,
            'usu_username'        => 'coordinador',
            'usu_password'        => Hash::make('password123'),
            'usu_status'          => true,
        ]);

        // Profesor de proyecto
        DB::table('usuario')->insert([
            'usu_cedula'          => '87654321',
            'usu_primer_nombre'   => 'Dexia',
            'usu_primer_apellido' => 'Colmenarez',
            'usu_email'           => 'profesor@uptp.edu.ve',
            'usu_tiene_acceso'    => true,
            'usu_username'        => 'profesor',
            'usu_password'        => Hash::make('password123'),
            'usu_status'          => true,
        ]);

        // Líder
        DB::table('usuario')->insert([
            'usu_cedula'          => '11111111',
            'usu_primer_nombre'   => 'Sergio',
            'usu_primer_apellido' => 'Martens',
            'usu_email'           => 'lider@uptp.edu.ve',
            'usu_tiene_acceso'    => true,
            'usu_username'        => 'lider',
            'usu_password'        => Hash::make('password123'),
            'usu_status'          => true,
        ]);

        // Estudiantes sin acceso
        $estudiantes = [
            ['22222222', 'Marco', 'Arpaia'],
            ['33333333', 'Juan', 'Cordero'],
            ['44444444', 'Greinmer', 'Valderrama'],
            ['55555555', 'Angel', 'Aguilar'],
        ];

        foreach ($estudiantes as [$cedula, $nombre, $apellido]) {
            DB::table('usuario')->insert([
                'usu_cedula'          => $cedula,
                'usu_primer_nombre'   => $nombre,
                'usu_primer_apellido' => $apellido,
                'usu_tiene_acceso'    => false,
                'usu_status'          => true,
            ]);
        }

        // Asignar roles
        $roles = ['coordinador' => 1, 'profesor_proyecto' => 2, 'lider' => 3];

        DB::table('usuario_rol')->insert([
            'uro_id_usu'      => 1,
            'uro_id_rol'      => $roles['coordinador'],
            'uro_fecha_inicio' => now()->toDateString(),
            'uro_status'      => true,
        ]);

        DB::table('usuario_rol')->insert([
            'uro_id_usu'      => 2,
            'uro_id_rol'      => $roles['profesor_proyecto'],
            'uro_fecha_inicio' => now()->toDateString(),
            'uro_status'      => true,
        ]);

        DB::table('usuario_rol')->insert([
            'uro_id_usu'      => 3,
            'uro_id_rol'      => $roles['lider'],
            'uro_fecha_inicio' => now()->toDateString(),
            'uro_status'      => true,
        ]);

        // Asignar profesor a sección 331
        DB::table('profesor_seccion')->insert([
            'pse_id_usu'          => 2,
            'pse_id_sec'          => 1,
            'pse_fecha_asignacion' => now()->toDateString(),
            'pse_status'          => true,
        ]);

        // Asignar estudiantes a sección 331
        $estudiantesIds = [3, 4, 5, 6, 7];
        foreach ($estudiantesIds as $id) {
            DB::table('estudiante_seccion')->insert([
                'ese_id_usu'          => $id,
                'ese_id_sec'          => 1,
                'ese_fecha_asignacion' => now()->toDateString(),
                'ese_status'          => true,
            ]);
        }
    }
}