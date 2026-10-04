<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        $tablas = [
            'bitacora','notificacion','evidencia_socializacion',
            'integrante_socializacion','jurado_socializacion',
            'socializacion','resultado_proyecto','carta_presentacion',
            'entregable','asistencia_punto_control',
            'seguimiento_equipo','punto_control',
            'proyecto_beneficiario','proyecto_comunidad',
            'historial_equipo','equipo_integrante','equipo',
            'estudiante_seccion','profesor_seccion',
            'seccion','trayecto','turno','pnf',
            'comunidad','usuario_rol','usuario',
        ];
        foreach ($tablas as $t) {
            DB::table($t)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $now = Carbon::now();
        $hoy = $now->toDateString();

        // ── USUARIOS ORIGINALES ──────────────────────────────────────
        DB::table('usuario')->insert([
            [
                'usu_cedula'          => '00000001',
                'usu_primer_nombre'   => 'Dexia',
                'usu_segundo_nombre'  => 'María',
                'usu_primer_apellido' => 'Colmenarez',
                'usu_segundo_apellido'=> 'Pérez',
                'usu_email'           => 'coordinador@uptp.edu.ve',
                'usu_username'        => 'coordinador',
                'usu_password'        => Hash::make('password123'),
                'usu_tiene_acceso'    => true,
                'usu_status'          => true,
            ],
            [
                'usu_cedula'          => '00000002',
                'usu_primer_nombre'   => 'Carlos',
                'usu_segundo_nombre'  => 'Enrique',
                'usu_primer_apellido' => 'Pérez',
                'usu_segundo_apellido'=> 'Gómez',
                'usu_email'           => 'profesor@uptp.edu.ve',
                'usu_username'        => 'profesor',
                'usu_password'        => Hash::make('password123'),
                'usu_tiene_acceso'    => true,
                'usu_status'          => true,
            ],
            [
                'usu_cedula'          => '00000003',
                'usu_primer_nombre'   => 'Sergio',
                'usu_segundo_nombre'  => 'Andrés',
                'usu_primer_apellido' => 'Martens',
                'usu_segundo_apellido'=> 'Ríos',
                'usu_email'           => 'lider@uptp.edu.ve',
                'usu_username'        => 'lider',
                'usu_password'        => Hash::make('password123'),
                'usu_tiene_acceso'    => true,
                'usu_status'          => true,
            ],
        ]);

        // IDs: coordinador=1, profesor=2, lider=3

        // ── ROLES ────────────────────────────────────────────────────
        // La migración ya inserta los roles automáticamente,
        // así que solo asignamos los roles a los usuarios.
        // Roles: coordinador=1, profesor_proyecto=2, lider=3
        DB::table('usuario_rol')->insert([
            ['uro_id_usu'=>1,'uro_id_rol'=>1,'uro_fecha_inicio'=>$hoy,'uro_status'=>true,'uro_id_usu_created'=>1],
            ['uro_id_usu'=>2,'uro_id_rol'=>2,'uro_fecha_inicio'=>$hoy,'uro_status'=>true,'uro_id_usu_created'=>1],
            ['uro_id_usu'=>3,'uro_id_rol'=>3,'uro_fecha_inicio'=>$hoy,'uro_status'=>true,'uro_id_usu_created'=>1],
        ]);

        // ── PNF + TRAYECTOS + TURNOS ─────────────────────────────────
        DB::table('pnf')->insert([
            'pnf_nombre'=>'PNF en Informática','pnf_siglas'=>'PNFI','pnf_status'=>true,
        ]);

        DB::table('trayecto')->insert([
            ['tra_id_pnf'=>1,'tra_nombre'=>'Trayecto I','tra_numero'=>1,'tra_status'=>true],
            ['tra_id_pnf'=>1,'tra_nombre'=>'Trayecto II','tra_numero'=>2,'tra_status'=>true],
            ['tra_id_pnf'=>1,'tra_nombre'=>'Trayecto III','tra_numero'=>3,'tra_status'=>true],
            ['tra_id_pnf'=>1,'tra_nombre'=>'Trayecto IV','tra_numero'=>4,'tra_status'=>true],
        ]);

        DB::table('turno')->insert([
            ['tur_nombre'=>'Mañana','tur_status'=>true],
            ['tur_nombre'=>'Tarde','tur_status'=>true],
            ['tur_nombre'=>'Noche','tur_status'=>true],
        ]);

        // ── SECCIONES ────────────────────────────────────────────────
        DB::table('seccion')->insert([
            ['sec_codigo'=>'331','sec_id_tra'=>3,'sec_id_tur'=>1,'sec_status'=>true,'sec_id_usu_created'=>1],
            ['sec_codigo'=>'332','sec_id_tra'=>3,'sec_id_tur'=>2,'sec_status'=>true,'sec_id_usu_created'=>1],
            ['sec_codigo'=>'221','sec_id_tra'=>2,'sec_id_tur'=>1,'sec_status'=>true,'sec_id_usu_created'=>1],
            ['sec_codigo'=>'441','sec_id_tra'=>4,'sec_id_tur'=>2,'sec_status'=>true,'sec_id_usu_created'=>1],
            ['sec_codigo'=>'111','sec_id_tra'=>1,'sec_id_tur'=>1,'sec_status'=>true,'sec_id_usu_created'=>1],
            ['sec_codigo'=>'112','sec_id_tra'=>1,'sec_id_tur'=>2,'sec_status'=>true,'sec_id_usu_created'=>1],
            ['sec_codigo'=>'222','sec_id_tra'=>2,'sec_id_tur'=>2,'sec_status'=>true,'sec_id_usu_created'=>1],
        ]);

        // ── CATÁLOGOS ────────────────────────────────────────────────
        // Las migraciones ya insertan los datos de:
        // tipo_proyecto, modalidad_proyecto, tipo_beneficiario,
        // tipo_socializacion, estado_proyecto, tipo_evento_equipo,
        // tipo_notificacion, entidad, rol
        // NO insertar nada aquí para esas tablas.

        // tipo_organizacion SÍ necesita datos (su migración no los inserta)
        DB::table('tipo_organizacion')->insert([
            ['tor_nombre'=>'Consejo Comunal','tor_status'=>true],
            ['tor_nombre'=>'Institución Educativa','tor_status'=>true],
            ['tor_nombre'=>'Centro de Salud','tor_status'=>true],
            ['tor_nombre'=>'Cooperativa','tor_status'=>true],
            ['tor_nombre'=>'Institución Cultural','tor_status'=>true],
            ['tor_nombre'=>'Empresa','tor_status'=>true],
            ['tor_nombre'=>'Otro','tor_status'=>true],
        ]);

        // ── COMUNIDADES ──────────────────────────────────────────────
        // tor_id: Consejo Comunal=1, Inst.Educativa=2, Centro Salud=3,
        //         Cooperativa=4, Inst.Cultural=5
        DB::table('comunidad')->insert([
            ['com_nombre'=>'Consejo Comunal Los Pinos','com_ubicacion'=>'Urb. Los Pinos, Sector 2, Trujillo','com_id_tor'=>1,'com_status'=>true,'com_id_usu_created'=>1],
            ['com_nombre'=>'Escuela Básica Simón Bolívar','com_ubicacion'=>'Av. Principal, Valera, Trujillo','com_id_tor'=>2,'com_status'=>true,'com_id_usu_created'=>1],
            ['com_nombre'=>'Consejo Comunal El Molino','com_ubicacion'=>'Sector El Molino, Trujillo','com_id_tor'=>1,'com_status'=>true,'com_id_usu_created'=>1],
            ['com_nombre'=>'Cooperativa de Productores Agropecuarios','com_ubicacion'=>'Carretera Panamericana Km 5, Trujillo','com_id_tor'=>4,'com_status'=>true,'com_id_usu_created'=>1],
            ['com_nombre'=>'Centro de Salud La Plazuela','com_ubicacion'=>'Calle La Plazuela, Valera','com_id_tor'=>3,'com_status'=>true,'com_id_usu_created'=>1],
            ['com_nombre'=>'Consejo Comunal Villa Esperanza','com_ubicacion'=>'Urb. Villa Esperanza, Trujillo','com_id_tor'=>1,'com_status'=>true,'com_id_usu_created'=>1],
            ['com_nombre'=>'Instituto de Cultura Municipal','com_ubicacion'=>'Av. Bolívar, Trujillo','com_id_tor'=>5,'com_status'=>true,'com_id_usu_created'=>1],
            ['com_nombre'=>'Consejo Comunal La Arboleda','com_ubicacion'=>'Sector La Arboleda, Valera','com_id_tor'=>1,'com_status'=>true,'com_id_usu_created'=>1],
            ['com_nombre'=>'Liceo Bolivariano Los Andes','com_ubicacion'=>'Calle 5, Valera, Trujillo','com_id_tor'=>2,'com_status'=>true,'com_id_usu_created'=>1],
            ['com_nombre'=>'Centro Comunitario El Paraíso','com_ubicacion'=>'Urb. El Paraíso, Trujillo','com_id_tor'=>1,'com_status'=>true,'com_id_usu_created'=>1],
        ]);

        // ── PROFESORES ADICIONALES ───────────────────────────────────
        $profesores = [
            ['10000004','María','Alejandra','Rodríguez','Torres','maria.rodriguez@uptp.edu.ve'],
            ['10000005','José','Luis','Martínez','Díaz','jose.martinez@uptp.edu.ve'],
            ['10000006','Ana','Beatriz','González','Flores','ana.gonzalez@uptp.edu.ve'],
            ['10000007','Luis','Alberto','Hernández','Castro','luis.hernandez@uptp.edu.ve'],
            ['10000008','Carmen','Elena','López','Vargas','carmen.lopez@uptp.edu.ve'],
            ['10000009','Pedro','Antonio','Ramírez','Medina','pedro.ramirez@uptp.edu.ve'],
            ['10000010','Luisa','Patricia','Morales','Suárez','luisa.morales@uptp.edu.ve'],
            ['10000011','Ramón','Eduardo','Torres','Jiménez','ramon.torres@uptp.edu.ve'],
            ['10000012','Isabel','Cristina','Vásquez','Blanco','isabel.vasquez@uptp.edu.ve'],
        ];

        foreach ($profesores as $p) {
            $id = DB::table('usuario')->insertGetId([
                'usu_cedula'          => $p[0],
                'usu_primer_nombre'   => $p[1],
                'usu_segundo_nombre'  => $p[2],
                'usu_primer_apellido' => $p[3],
                'usu_segundo_apellido'=> $p[4],
                'usu_email'           => $p[5],
                'usu_username'        => $p[0],
                'usu_password'        => Hash::make('password123'),
                'usu_tiene_acceso'    => true,
                'usu_status'          => true,
            ]);
            DB::table('usuario_rol')->insert([
                'uro_id_usu'=>$id,'uro_id_rol'=>2,
                'uro_fecha_inicio'=>$hoy,'uro_status'=>true,'uro_id_usu_created'=>1,
            ]);
        }

        // ── ESTUDIANTES ──────────────────────────────────────────────
        // sec_id: 331=1, 332=2, 221=3, 441=4
        $estudiantes = [
            ['20000001','Valentina','Isabel','García','Mora',1],
            ['20000002','Diego','Alejandro','Sánchez','Núñez',1],
            ['20000003','Andrea','Paola','Fernández','Salazar',1],
            ['20000004','Miguel','Ángel','Orozco','Pinto',1],
            ['20000005','Gabriela','María','Mendoza','Leal',1],
            ['20000006','Fernando','José','Castro','Rivas',1],
            ['20000007','Stephanie','Lorena','Navarro','Colón',1],
            ['20000008','Marco','Antonio','Arpaia','Blanco',1],
            ['20000009','Juan','Carlos','Cordero','Delgado',1],
            ['20000010','Greinmer','José','Valderrama','Fuentes',1],
            ['20000011','Ángel','David','Aguilar','Herrera',1],
            ['20000012','Paola','Cristina','Romero','Ibáñez',1],
            ['20000013','Rafael','Eduardo','Nieves','Jaramillo',2],
            ['20000014','Laura','Sofía','Quintero','Keller',2],
            ['20000015','Andrés','Felipe','Bravo','Luna',2],
            ['20000016','Natalia','Beatriz','Campos','Mora',2],
            ['20000017','Héctor','Ramón','Delgado','Nava',2],
            ['20000018','Patricia','Alejandra','Espinoza','Ortiz',2],
            ['20000019','Roberto','Carlos','Figueroa','Peña',2],
            ['20000020','Daniela','María','Guerrero','Quiroz',2],
            ['20000021','Jesús','Manuel','Herrera','Reyes',2],
            ['20000022','Mariela','Luisa','Infante','Silva',2],
            ['20000023','Alexander','José','Jiménez','Torres',2],
            ['20000024','Karina','Beatriz','León','Urdaneta',2],
            ['20000025','Oscar','David','Medina','Vera',3],
            ['20000026','Vanessa','Carolina','Noriega','Wence',3],
            ['20000027','Ricardo','Alfredo','Ochoa','Yáñez',3],
            ['20000028','Alejandra','Patricia','Palacios','Zerpa',3],
            ['20000029','Wilmer','Antonio','Ramos','Albornoz',3],
            ['20000030','Génesis','María','Santos','Bermúdez',3],
            ['20000031','Jonathan','Enrique','Toro','Colmenares',3],
            ['20000032','Mariana','Sofía','Urbina','Dugarte',3],
            ['20000033','Freddy','Alberto','Villalobos','Escalante',3],
            ['20000034','Yolanda','Elena','Zambrano','Flores',3],
            ['20000035','Carlos','Miguel','Acosta','García',3],
            ['20000036','Melissa','Andrea','Barrios','Hernández',3],
            ['20000037','Nelson','Ramón','Carrasquel','Izquierdo',4],
            ['20000038','Adriana','Luisa','Domínguez','Jiménez',4],
            ['20000039','Esteban','José','Escalante','Lara',4],
            ['20000040','Camila','Beatriz','Flores','Mendoza',4],
            ['20000041','Jesús','Alberto','García','Peña',4],
            ['20000042','Diana','Carolina','Herrera','Ramos',4],
            ['20000043','Luis','Eduardo','Infante','Soto',4],
            ['20000044','Rosa','María','Jiménez','Torres',4],
            ['20000045','Andrés','Rafael','Leal','Ureña',4],
            ['20000046','Carmen','Sofía','Mendoza','Vargas',4],
            ['20000047','Pedro','Antonio','Nieves','Zerpa',4],
            ['20000048','Luisa','Beatriz','Ochoa','Alcalá',4],
            // ── Sección 111 (T1 Mañana) ─────────────────────────────────
            ['20000049','Carlos','Eduardo','Alvarado','Paz',5],
            ['20000050','Mariana','Sofía','Benítez','Ríos',5],
            ['20000051','José','Luis','Contreras','Salas',5],
            ['20000052','Valeria','Isabel','Duarte','Mora',5],
            ['20000053','Andrés','Felipe','Escalona','Vega',5],
            ['20000054','Gabriela','María','Flores','Peña',5],
            ['20000055','Ricardo','Antonio','Gómez','Torres',5],
            ['20000056','Daniela','Cristina','Herrera','Luna',5],
            ['20000057','Miguel','Alejandro','Infante','Cruz',5],
            ['20000058','Paola','Beatriz','Jiménez','Díaz',5],
            ['20000059','Fernando','José','Leal','Ortiz',5],
            ['20000060','Stephanie','Lorena','Medina','Blanco',5],
            // ── Sección 112 (T1 Tarde) ──────────────────────────────────
            ['20000061','Nelson','Ramón','Noriega','Castillo',6],
            ['20000062','Adriana','Luisa','Ochoa','Fuentes',6],
            ['20000063','Esteban','José','Palacios','García',6],
            ['20000064','Camila','Beatriz','Quiroz','Herrera',6],
            ['20000065','Jesús','Alberto','Ramos','Jiménez',6],
            ['20000066','Diana','Carolina','Santos','López',6],
            ['20000067','Luis','Eduardo','Torres','Martínez',6],
            ['20000068','Rosa','María','Urbina','Núñez',6],
            ['20000069','Andrés','Rafael','Vargas','Ortega',6],
            ['20000070','Carmen','Sofía','Wences','Pérez',6],
            ['20000071','Pedro','Antonio','Zerpa','Rodríguez',6],
            ['20000072','Luisa','Beatriz','Alcalá','Sánchez',6],
            // ── Sección 222 (T2 Tarde) ──────────────────────────────────
            ['20000073','Wilmer','Antonio','Bermúdez','Torres',7],
            ['20000074','Génesis','María','Colmenares','Vega',7],
            ['20000075','Jonathan','Enrique','Dugarte','Ríos',7],
            ['20000076','Mariana','Sofía','Escalante','Mora',7],
            ['20000077','Freddy','Alberto','Flores','Salas',7],
            ['20000078','Yolanda','Elena','García','Peña',7],
            ['20000079','Carlos','Miguel','Herrera','Luna',7],
            ['20000080','Melissa','Andrea','Infante','Cruz',7],
            ['20000081','Oscar','David','Jiménez','Díaz',7],
            ['20000082','Vanessa','Carolina','Leal','Ortiz',7],
            ['20000083','Ricardo','Alfredo','Medina','Blanco',7],
            ['20000084','Alejandra','Patricia','Noriega','Castillo',7],
        ];

        foreach ($estudiantes as $e) {
            $id = DB::table('usuario')->insertGetId([
                'usu_cedula'          => $e[0],
                'usu_primer_nombre'   => $e[1],
                'usu_segundo_nombre'  => $e[2],
                'usu_primer_apellido' => $e[3],
                'usu_segundo_apellido'=> $e[4],
                'usu_email'           => strtolower($e[1].'.'.$e[3]).'@estudiante.uptp.edu.ve',
                'usu_username'        => $e[0],
                'usu_password'        => Hash::make($e[0]),
                'usu_tiene_acceso'    => false,
                'usu_status'          => true,
            ]);
            DB::table('estudiante_seccion')->insert([
                'ese_id_usu'           => $id,
                'ese_id_sec'           => $e[5],
                'ese_fecha_asignacion' => $hoy,
                'ese_status'           => true,
                'ese_id_usu_created'   => 1,
            ]);
        }

        // Líder original (id=3) también va en sección 331
        DB::table('estudiante_seccion')->insert([
            'ese_id_usu'           => 3,
            'ese_id_sec'           => 1,
            'ese_fecha_asignacion' => $hoy,
            'ese_status'           => true,
            'ese_id_usu_created'   => 1,
        ]);

        // ── PROFESOR — SECCIÓN ───────────────────────────────────────
        // profesor original (id=2) → secciones 331 y 332 (tiene dos)
        // José Martínez (id=5)     → sección 221
        // Luis Hernández (id=7)    → sección 441
        // Pedro Ramírez (id=9)     → sección 111
        // María Rodríguez (id=4)   → sección 112
        // Ana González (id=6)      → sección 222
        // Carmen López (id=8) y Luisa Morales (id=10) → sin sección aún
        DB::table('profesor_seccion')->insert([
            ['pse_id_usu'=>2, 'pse_id_sec'=>1,'pse_fecha_asignacion'=>$hoy,'pse_status'=>true,'pse_id_usu_created'=>1],
            ['pse_id_usu'=>2, 'pse_id_sec'=>2,'pse_fecha_asignacion'=>$hoy,'pse_status'=>true,'pse_id_usu_created'=>1],
            ['pse_id_usu'=>5, 'pse_id_sec'=>3,'pse_fecha_asignacion'=>$hoy,'pse_status'=>true,'pse_id_usu_created'=>1],
            ['pse_id_usu'=>7, 'pse_id_sec'=>4,'pse_fecha_asignacion'=>$hoy,'pse_status'=>true,'pse_id_usu_created'=>1],
            ['pse_id_usu'=>9, 'pse_id_sec'=>5,'pse_fecha_asignacion'=>$hoy,'pse_status'=>true,'pse_id_usu_created'=>1],
            ['pse_id_usu'=>4, 'pse_id_sec'=>6,'pse_fecha_asignacion'=>$hoy,'pse_status'=>true,'pse_id_usu_created'=>1],
            ['pse_id_usu'=>6, 'pse_id_sec'=>7,'pse_fecha_asignacion'=>$hoy,'pse_status'=>true,'pse_id_usu_created'=>1],
        ]);

        $this->command->info('');
        $this->command->info('✅ Seeder completado.');
        $this->command->info('');
        $this->command->info('  coordinador@uptp.edu.ve   |  password123');
        $this->command->info('  profesor@uptp.edu.ve      |  password123  → Sección 331');
        $this->command->info('  lider@uptp.edu.ve         |  password123  → Sección 331');
        $this->command->info('  maria.rodriguez@uptp.edu.ve | password123 → Sección 331');
        $this->command->info('  jose.martinez@uptp.edu.ve   | password123 → Sección 332');
        $this->command->info('  ana.gonzalez@uptp.edu.ve    | password123 → Sección 332');
        $this->command->info('  luis.hernandez@uptp.edu.ve  | password123 → Sección 221');
        $this->command->info('  carmen.lopez@uptp.edu.ve    | password123 → Sección 221');
        $this->command->info('  pedro.ramirez@uptp.edu.ve   | password123 → Sección 441');
        $this->command->info('  luisa.morales@uptp.edu.ve   | password123 → Sección 441');
        $this->command->info('');
        $this->command->info('  48 estudiantes distribuidos en 4 secciones.');
        $this->command->info('  Entra como profesor y crea los equipos desde la interfaz.');
    }
}