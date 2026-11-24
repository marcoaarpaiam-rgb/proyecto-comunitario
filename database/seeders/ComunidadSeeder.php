<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ComunidadSeeder extends Seeder
{
    /**
     * Comunidades e instituciones reales de Acarigua y Araure, Estado Portuguesa
     * para demostración institucional en la UPTP "Juan de Jesús Montilla".
     */
    public function run(): void
    {
        $comunidades = [
            1 => [
                'com_nombre'    => 'Consejo Comunal Bella Vista I',
                'com_ubicacion' => 'Sector Bella Vista, Calle 14 con Av. 32, Acarigua, Edo. Portuguesa',
                'com_id_tor'    => 3,
            ],
            2 => [
                'com_nombre'    => 'Liceo Bolivariano José Antonio Páez',
                'com_ubicacion' => 'Av. Las Lágrimas, Urb. Baraure I, Araure, Edo. Portuguesa',
                'com_id_tor'    => 4,
            ],
            3 => [
                'com_nombre'    => 'Consejo Comunal Baraure Central',
                'com_ubicacion' => 'Urb. Baraure II, Sector Central, Araure, Edo. Portuguesa',
                'com_id_tor'    => 3,
            ],
            4 => [
                'com_nombre'    => 'Hospital Universitario Dr. Jesús María Casal Ramos',
                'com_ubicacion' => 'Final Av. 13 de Junio, Acarigua-Araure, Edo. Portuguesa',
                'com_id_tor'    => 5,
            ],
            5 => [
                'com_nombre'    => 'Centro de Diagnóstico Integral (CDI) Campo Lindo',
                'com_ubicacion' => 'Sector Campo Lindo, Calle 8, Acarigua, Edo. Portuguesa',
                'com_id_tor'    => 5,
            ],
            6 => [
                'com_nombre'    => 'Consejo Comunal Los Cortijos - San Vicente',
                'com_ubicacion' => 'Parroquia San Vicente, Municipio Páez, Acarigua, Edo. Portuguesa',
                'com_id_tor'    => 3,
            ],
            7 => [
                'com_nombre'    => 'Alcaldía del Municipio Páez (Dirección de Informática)',
                'com_ubicacion' => 'Calle 28 entre Av. 33 y 34, Centro de Acarigua, Edo. Portuguesa',
                'com_id_tor'    => 1,
            ],
            8 => [
                'com_nombre'    => 'Consejo Comunal Villa Araure',
                'com_ubicacion' => 'Sector Villa Araure, Av. Principal, Municipio Araure, Edo. Portuguesa',
                'com_id_tor'    => 3,
            ],
            9 => [
                'com_nombre'    => 'Escuela Técnica Agropecuaria Payara',
                'com_ubicacion' => 'Parroquia Payara, Municipio Páez, Edo. Portuguesa',
                'com_id_tor'    => 4,
            ],
            10 => [
                'com_nombre'    => 'Comuna Socialista Batalla de Santa Inés',
                'com_ubicacion' => 'Sector Durigua, Manzana 12, Acarigua, Edo. Portuguesa',
                'com_id_tor'    => 3,
            ],
        ];

        foreach ($comunidades as $id => $data) {
            DB::table('comunidad')->updateOrInsert(
                ['com_id' => $id],
                [
                    'com_nombre'          => $data['com_nombre'],
                    'com_ubicacion'       => $data['com_ubicacion'],
                    'com_id_tor'          => $data['com_id_tor'],
                    'com_status'          => true,
                    'com_id_usu_created'  => 1,
                    'com_updated_at'      => now(),
                ]
            );
        }
    }
}
