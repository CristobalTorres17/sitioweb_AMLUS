<?php

namespace Database\Seeders;

use App\Models\District;
use Illuminate\Database\Seeder;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        District::truncate();

        $districts = [
            [
                'name'         => 'La Unión',
                'slug'         => 'la-union',
                'svg_path_id'  => 'path23',
                'short_summary' => 'Ciudad principal de La Unión Sur, sede del Puerto de Cutuco.',
                'description'  => 'La Unión es la ciudad cabecera del departamento, conocida por su puerto y riqueza histórica.',
                'color'        => '#1a56a0',
                'position'     => 1,
            ],
            [
                'name'         => 'Conchagua',
                'slug'         => 'conchagua',
                'svg_path_id'  => 'path25',
                'short_summary' => 'Municipio de gran riqueza natural con el volcán Conchagua.',
                'description'  => 'Conchagua se destaca por su volcán y sus comunidades costeras en el Golfo de Fonseca.',
                'color'        => '#2e8b57',
                'position'     => 2,
            ],
            [
                'name'         => 'San Alejo',
                'slug'         => 'san-alejo',
                'svg_path_id'  => 'path22',
                'short_summary' => 'Municipio agrícola y ganadero del noreste del departamento.',
                'description'  => 'San Alejo es reconocido por su actividad agropecuaria y sus tradiciones culturales.',
                'color'        => '#b8860b',
                'position'     => 3,
            ],
            [
                'name'         => 'El Carmen',
                'slug'         => 'el-carmen',
                'svg_path_id'  => 'path21',
                'short_summary' => 'Municipio costero con acceso al Golfo de Fonseca.',
                'description'  => 'El Carmen destaca por su actividad pesquera y sus comunidades rurales.',
                'color'        => '#8b0000',
                'position'     => 4,
            ],
            [
                'name'         => 'Yayantique',
                'slug'         => 'yayantique',
                'svg_path_id'  => 'path20',
                'short_summary' => 'Tranquilo municipio al sur del departamento.',
                'description'  => 'Yayantique es conocido por su vida comunitaria y su actividad local.',
                'color'        => '#4b0082',
                'position'     => 5,
            ],
            [
                'name'         => 'Intipucá',
                'slug'         => 'intipuca',
                'svg_path_id'  => 'path24',
                'short_summary' => 'Municipio con una fuerte comunidad de salvadoreños en el exterior.',
                'description'  => 'Intipucá es famoso por sus lazos con la diáspora salvadoreña y sus remesas.',
                'color'        => '#ff6600',
                'position'     => 6,
            ],
            [
                'name'         => 'Yucuaiquín',
                'slug'         => 'yucuaiquin',
                'svg_path_id'  => 'path19',
                'short_summary' => 'Pequeño municipio al norte del departamento.',
                'description'  => 'Yucuaiquín es un municipio tranquilo con tradiciones y paisajes rurales.',
                'color'        => '#008080',
                'position'     => 7,
            ],
            [
                'name'         => 'Meanguera del Golfo',
                'slug'         => 'meanguera-del-golfo',
                'svg_path_id'  => 'path27',
                'short_summary' => 'Isla ubicada en el Golfo de Fonseca.',
                'description'  => 'Meanguera del Golfo es la única isla del departamento, con gran potencial turístico.',
                'color'        => '#00509e',
                'position'     => 8,
            ],
        ];

        foreach ($districts as $data) {
            District::create(array_merge($data, ['is_active' => true]));
        }
    }
}
