<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('sectors')->insert([
            ['nombre' => 'Tecnología de la Información y Servicios'],
            ['nombre' => 'Salud y Bienestar'],
            ['nombre' => 'Educación y Formación'],
            ['nombre' => 'Finanzas y Banca'],
            ['nombre' => 'Industria Manufacturera'],
            ['nombre' => 'Comercio Minorista y Mayorista'],
            ['nombre' => 'Hostelería, Turismo y Restauración'],
            ['nombre' => 'Construcción e Inmobiliaria'],
            ['nombre' => 'Energía y Recursos Naturales'],
            ['nombre' => 'Agricultura, Ganadería y Pesca'],
            ['nombre' => 'Transporte y Logística'],
            ['nombre' => 'Marketing, Publicidad y Comunicación'],
            ['nombre' => 'Recursos Humanos y Selección de Personal'],
            ['nombre' => 'Legal y Servicios Jurídicos'],
            ['nombre' => 'Biotecnología y Farmacéutica'],
            ['nombre' => 'Entretenimiento, Arte y Cultura'],
            ['nombre' => 'Servicios Públicos y Administración'],
            ['nombre' => 'Organizaciones sin Ánimo de Lucro'],
            ['nombre' => 'Medios de Comunicación'],
            ['nombre' => 'Telecomunicaciones'],
            ['nombre' => 'Automoción'],
            ['nombre' => 'Aeroespacial'],
            ['nombre' => 'Consultoría Empresarial'],
            ['nombre' => 'Seguros'],
            ['nombre' => 'Real Estate'],
            ['nombre' => 'Diseño'],
            ['nombre' => 'Investigación y Desarrollo'],
            ['nombre' => 'Servicios Profesionales '],
            ['nombre' => 'Medios de Comunicación y Entretenimiento Digital'],
            ['nombre' => 'Bienes de Consumo '],
            ['nombre' => 'Logística y Cadena de Suministro'],
            ['nombre' => 'Salud Animal y Veterinaria'],
            ['nombre' => 'Servicios Ambientales y Sostenibilidad'],
            ['nombre' => 'Artesanía y Producción a Pequeña Escala'],
        ]);
    }
}
