<?php

namespace Database\Seeders;

use App\Models\StudyType;
use Illuminate\Database\Seeder;

class StudyTypeSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            [
              'key'=>'geo_obra_nueva',
              'label'=>'Estudio Geotécnico Obra nueva',
              'description'=>"Estudio Geotécnico, incluye:\n- Tres (3) perforaciones entre 0–6 m\n- Granulometría\n- Límites\n- Humedades",
              'default_unit'=>'UND','default_qty'=>1,'default_unit_price'=>1400000
            ],
            [
              'key'=>'geo_reconocimiento',
              'label'=>'Estudio Geotécnico Para reconocimiento de una Obra existente',
              'description'=>"Estudio Geotécnico (reconocimiento obra existente), incluye:\n- Calicatas / verificación estratigrafía\n- Ensayos básicos de laboratorio",
              'default_unit'=>'UND','default_qty'=>1,'default_unit_price'=>1200000
            ],
            [
              'key'=>'geo_reconocimiento_adicion',
              'label'=>'Estudio Geotecnico para Reconocimiento y Adicion',
              'description'=>"Estudio Geotécnico para Reconocimiento y Adición",
              'default_unit'=>'UND','default_qty'=>1,'default_unit_price'=>1500000
            ],
            [
              'key'=>'estruc_obra_nueva',
              'label'=>'Calculo estructural obra nueva',
              'description'=>"Cálculo Estructural área aprox 600 m², incluye:\n- Memoria de cálculo\n- Planos estructurales",
              'default_unit'=>'UND','default_qty'=>1,'default_unit_price'=>1800000
            ],
            [
              'key'=>'estruc_reconocimiento',
              'label'=>'Calculo estructual para reconocimiento obra existente',
              'description'=>"Cálculo estructural (reconocimiento obra existente)",
              'default_unit'=>'UND','default_qty'=>1,'default_unit_price'=>1600000
            ],
            [
              'key'=>'geo_especiales',
              'label'=>'Estudio Geotécnico para estructuras especiales ( box culvert, canales, tanques, muros contención )',
              'description'=>"Estudio Geotécnico para estructuras especiales (box culvert, canales, tanques, muros de contención)",
              'default_unit'=>'UND','default_qty'=>1,'default_unit_price'=>2200000
            ],
            [
              'key'=>'estruc_especiales',
              'label'=>'Calculo Estructural para estructuras especiales ( box culvert, canales, tanques, muros de contención )',
              'description'=>"Cálculo Estructural para estructuras especiales (box culvert, canales, tanques, muros de contención)",
              'default_unit'=>'UND','default_qty'=>1,'default_unit_price'=>2500000
            ],
            [
              'key'=>'vulnerabilidad',
              'label'=>'Vulnerabilidad',
              'description'=>"Estudio de Vulnerabilidad estructural",
              'default_unit'=>'UND','default_qty'=>1,'default_unit_price'=>1300000
            ],
        ];

        foreach ($data as $row) StudyType::updateOrCreate(['key'=>$row['key']], $row);
    }
}

