<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HabilidadesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $habilidades = [
            // Habilidades blandas
            ['Tipo' => 'Blanda', 'Habilidad' => 'Comunicación'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Trabajo en equipo'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Pensamiento crítico'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Resolución de conflictos'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Gestión del tiempo'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Liderazgo'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Adaptabilidad'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Empatía'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Creatividad'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Toma de decisiones'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Inteligencia emocional'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Capacidad de escucha'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Pensamiento estratégico'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Negociación'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Proactividad'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Manejo del estrés'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Orientación al cliente'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Ética laboral'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Autoconfianza'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Capacidad de aprendizaje'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Responsabilidad'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Motivación'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Manejo de críticas'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Organización'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Colaboración'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Pensamiento analítico'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Persuasión'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Autogestión'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Disciplina'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Capacidad de síntesis'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Sociabilidad'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Atención al detalle'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Actitud positiva'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Curiosidad'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Integridad'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Compromiso'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Claridad al hablar'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Tolerancia a la frustración'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Orientación a resultados'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Servicio al cliente'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Flexibilidad'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Capacidad de observación'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Capacidad de mediación'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Sentido del humor'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Autoconocimiento'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Innovación'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Escucha activa'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Capacidad de análisis'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Espíritu emprendedor'],
            ['Tipo' => 'Blanda', 'Habilidad' => 'Compasión'],

            // Habilidades duras
            ['Tipo' => 'Dura', 'Habilidad' => 'Programación en Java'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Programación en Python'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Programación en C++'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Programación en JavaScript'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Desarrollo Web Frontend'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Desarrollo Web Backend'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Desarrollo en Android'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Desarrollo en iOS'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Uso de frameworks Laravel'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Uso de frameworks Angular'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Diseño UX/UI'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Gestión de bases de datos'],
            ['Tipo' => 'Dura', 'Habilidad' => 'SQL avanzado'],
            ['Tipo' => 'Dura', 'Habilidad' => 'MongoDB'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Ciberseguridad'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Redacción técnica'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Diseño gráfico'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Edición de video'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Fotografía digital'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Gestión de redes sociales'],
            ['Tipo' => 'Dura', 'Habilidad' => 'SEO (Optimización de motores de búsqueda)'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Marketing digital'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Google Analytics'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Administración de sistemas Linux'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Administración de servidores Windows'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Machine Learning'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Análisis de datos'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Big Data'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Inteligencia Artificial'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Automatización de procesos'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Testing de software'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Control de versiones (Git)'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Docker'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Kubernetes'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Cloud Computing (AWS, Azure, GCP)'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Contabilidad'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Análisis financiero'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Gestión de proyectos'],
            ['Tipo' => 'Dura', 'Habilidad' => 'SCRUM'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Diseño de productos'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Ingeniería de software'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Uso de AutoCAD'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Diseño de circuitos electrónicos'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Simulación 3D'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Gestión de inventarios'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Logística y cadena de suministro'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Control de calidad'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Conducción de montacargas'],
            ['Tipo' => 'Dura', 'Habilidad' => 'Instalación eléctrica'],
        ];

        DB::table('habilidads')->insert($habilidades);

    }
}
