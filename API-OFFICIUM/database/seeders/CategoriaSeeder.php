<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        DB::table('categorias')->insert([
            ['nombre' => 'Desarrollo Web'],
            ['nombre' => 'Ciberseguridad'],
            ['nombre' => 'Soporte Técnico'],
            ['nombre' => 'Inteligencia Artificial'],
            ['nombre' => 'Gestión de Bases de Datos'],

            ['nombre' => 'Medicina General'],
            ['nombre' => 'Fisioterapia'],
            ['nombre' => 'Psicología'],
            ['nombre' => 'Nutrición'],
            ['nombre' => 'Enfermería'],

            ['nombre' => 'Formación Online'],
            ['nombre' => 'Educación Infantil'],
            ['nombre' => 'Capacitación Empresarial'],
            ['nombre' => 'Idiomas'],
            ['nombre' => 'Investigación Académica'],

            ['nombre' => 'Contabilidad'],
            ['nombre' => 'Análisis Financiero'],
            ['nombre' => 'Banca de Inversión'],
            ['nombre' => 'Gestión de Riesgos'],
            ['nombre' => 'Asesoría Financiera'],

            ['nombre' => 'Producción Industrial'],
            ['nombre' => 'Control de Calidad'],
            ['nombre' => 'Ingeniería de Procesos'],
            ['nombre' => 'Mantenimiento Industrial'],
            ['nombre' => 'Diseño de Producto'],

            ['nombre' => 'Gestión de Inventarios'],
            ['nombre' => 'Atención al Cliente'],
            ['nombre' => 'Ventas Minoristas'],
            ['nombre' => 'Compras y Abastecimiento'],
            ['nombre' => 'Visual Merchandising'],

            ['nombre' => 'Gestión Hotelera'],
            ['nombre' => 'Organización de Eventos'],
            ['nombre' => 'Gastronomía'],
            ['nombre' => 'Turismo Cultural'],
            ['nombre' => 'Servicios de Catering'],

            ['nombre' => 'Arquitectura'],
            ['nombre' => 'Obra Civil'],
            ['nombre' => 'Venta de Inmuebles'],
            ['nombre' => 'Gestión de Proyectos de Construcción'],
            ['nombre' => 'Reformas y Remodelaciones'],

            ['nombre' => 'Energías Renovables'],
            ['nombre' => 'Minería'],
            ['nombre' => 'Ingeniería Energética'],
            ['nombre' => 'Gestión de Recursos Hídricos'],
            ['nombre' => 'Exploración Petrolera'],

            ['nombre' => 'Agricultura Orgánica'],
            ['nombre' => 'Producción Animal'],
            ['nombre' => 'Acuicultura'],
            ['nombre' => 'Tecnología Agrícola'],
            ['nombre' => 'Gestión de Explotaciones Agropecuarias'],

            ['nombre' => 'Transporte de Mercancías'],
            ['nombre' => 'Logística Internacional'],
            ['nombre' => 'Gestión de Almacenes'],
            ['nombre' => 'Cadena de Suministro'],
            ['nombre' => 'Transporte de Pasajeros'],

            ['nombre' => 'Marketing Digital'],
            ['nombre' => 'Branding'],
            ['nombre' => 'Comunicación Corporativa'],
            ['nombre' => 'Publicidad en Medios'],
            ['nombre' => 'Investigación de Mercados'],

            ['nombre' => 'Reclutamiento y Selección'],
            ['nombre' => 'Formación y Desarrollo'],
            ['nombre' => 'Compensaciones y Beneficios'],
            ['nombre' => 'Gestión del Talento'],
            ['nombre' => 'Relaciones Laborales'],

            ['nombre' => 'Derecho Laboral'],
            ['nombre' => 'Derecho Penal'],
            ['nombre' => 'Asesoría Corporativa'],
            ['nombre' => 'Propiedad Intelectual'],
            ['nombre' => 'Derecho Civil'],

            ['nombre' => 'Investigación Biomédica'],
            ['nombre' => 'Desarrollo de Medicamentos'],
            ['nombre' => 'Ensayos Clínicos'],
            ['nombre' => 'Ingeniería Genética'],
            ['nombre' => 'Producción Farmacéutica'],

            ['nombre' => 'Producción Audiovisual'],
            ['nombre' => 'Artes Escénicas'],
            ['nombre' => 'Gestión Cultural'],
            ['nombre' => 'Artes Plásticas'],
            ['nombre' => 'Industria Musical'],

            ['nombre' => 'Administración Pública'],
            ['nombre' => 'Políticas Públicas'],
            ['nombre' => 'Servicios Sociales'],
            ['nombre' => 'Seguridad Ciudadana'],
            ['nombre' => 'Gestión Municipal'],

            ['nombre' => 'Cooperación Internacional'],
            ['nombre' => 'Gestión de Proyectos Sociales'],
            ['nombre' => 'Recaudación de Fondos'],
            ['nombre' => 'Voluntariado'],
            ['nombre' => 'Educación Comunitaria'],

            ['nombre' => 'Periodismo'],
            ['nombre' => 'Producción Televisiva'],
            ['nombre' => 'Edición de Contenidos'],
            ['nombre' => 'Fotografía'],
            ['nombre' => 'Radio y Podcast'],

            ['nombre' => 'Redes de Comunicaciones'],
            ['nombre' => 'Servicios Móviles'],
            ['nombre' => 'Ingeniería en Telecomunicaciones'],
            ['nombre' => 'Proveedores de Internet'],
            ['nombre' => 'Cableado de Redes'],

            ['nombre' => 'Fabricación de Automóviles'],
            ['nombre' => 'Ingeniería Automotriz'],
            ['nombre' => 'Reparación de Vehículos'],
            ['nombre' => 'Diseño de Vehículos'],
            ['nombre' => 'Logística de Automoción'],

            ['nombre' => 'Ingeniería Aeroespacial'],
            ['nombre' => 'Diseño de Aeronaves'],
            ['nombre' => 'Fabricación de Componentes Aeronáuticos'],
            ['nombre' => 'Investigación Espacial'],
            ['nombre' => 'Mantenimiento Aeronáutico'],

            ['nombre' => 'Consultoría Estratégica'],
            ['nombre' => 'Consultoría Tecnológica'],
            ['nombre' => 'Consultoría de Recursos Humanos'],
            ['nombre' => 'Consultoría Financiera'],
            ['nombre' => 'Consultoría en Transformación Digital'],

            ['nombre' => 'Seguros de Vida'],
            ['nombre' => 'Seguros de Salud'],
            ['nombre' => 'Seguros de Automóviles'],
            ['nombre' => 'Seguros Empresariales'],
            ['nombre' => 'Gestión de Siniestros'],

            ['nombre' => 'Desarrollo Inmobiliario'],
            ['nombre' => 'Administración de Propiedades'],
            ['nombre' => 'Asesoría Inmobiliaria'],
            ['nombre' => 'Venta y Alquiler de Propiedades'],
            ['nombre' => 'Gestión de Proyectos Inmobiliarios'],

            ['nombre' => 'Diseño Gráfico'],
            ['nombre' => 'Diseño de Interiores'],
            ['nombre' => 'Diseño de Producto'],
            ['nombre' => 'Diseño Web'],
            ['nombre' => 'Diseño de Moda'],

            ['nombre' => 'I+D Tecnológico'],
            ['nombre' => 'Innovación Empresarial'],
            ['nombre' => 'Desarrollo de Nuevos Productos'],
            ['nombre' => 'Investigación Médica'],
            ['nombre' => 'Investigación de Materiales'],

            ['nombre' => 'Servicios Contables'],
            ['nombre' => 'Asesoría Legal'],
            ['nombre' => 'Servicios de Consultoría'],
            ['nombre' => 'Servicios de Traducción'],
            ['nombre' => 'Servicios de Arquitectura'],

            ['nombre' => 'Producción de Videojuegos'],
            ['nombre' => 'Creación de Contenido Digital'],
            ['nombre' => 'Edición de Video'],
            ['nombre' => 'Animación 3D'],
            ['nombre' => 'Streaming y Multimedia'],

            ['nombre' => 'Producción Alimentaria'],
            ['nombre' => 'Productos de Cuidado Personal'],
            ['nombre' => 'Moda y Textil'],
            ['nombre' => 'Electrónica de Consumo'],
            ['nombre' => 'Bebidas y Alimentos Empaquetados'],

            ['nombre' => 'Gestión de Flotas'],
            ['nombre' => 'Control de Inventarios'],
            ['nombre' => 'Planificación de la Demanda'],
            ['nombre' => 'Transporte Internacional'],
            ['nombre' => 'Logística de Última Milla'],

            ['nombre' => 'Clínica Veterinaria'],
            ['nombre' => 'Investigación Veterinaria'],
            ['nombre' => 'Nutrición Animal'],
            ['nombre' => 'Cirugía Veterinaria'],
            ['nombre' => 'Rehabilitación Animal'],

            ['nombre' => 'Gestión de Residuos'],
            ['nombre' => 'Energías Renovables'],
            ['nombre' => 'Consultoría Ambiental'],
            ['nombre' => 'Protección de la Biodiversidad'],
            ['nombre' => 'Educación Ambiental'],

            ['nombre' => 'Joyería Artesanal'],
            ['nombre' => 'Cerámica'],
            ['nombre' => 'Carpintería'],
            ['nombre' => 'Tejidos y Bordados'],
            ['nombre' => 'Producción de Alimentos Artesanales'],
        ]);
    }
}
