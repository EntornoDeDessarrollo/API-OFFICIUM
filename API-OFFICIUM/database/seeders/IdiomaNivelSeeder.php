<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IdiomaNivelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $idiomas = [
            'Español', 'Inglés', 'Francés', 'Alemán', 'Italiano',
            'Portugués', 'Chino Mandarín', 'Japonés', 'Coreano', 'Ruso',
            'Árabe', 'Hindi', 'Turco', 'Holandés', 'Sueco',
            'Noruego', 'Danés', 'Polaco', 'Checo', 'Finlandés',
            'Rumano', 'Griego', 'Hebreo', 'Tailandés', 'Vietnamita',
            'Búlgaro', 'Ucraniano', 'Serbio', 'Croata', 'Esloveno',
            'Indonesio', 'Malayo', 'Filipino (Tagalo)', 'Swahili', 'Afrikáans',
            'Persa (Farsi)', 'Pashto', 'Urdu', 'Tamil', 'Bengalí',
            'Punjabi', 'Gujarati', 'Nepalí', 'Kazajo', 'Mongol',
            'Georgiano', 'Armenio', 'Letón', 'Lituano', 'Estonio',
        ];

        $niveles = [
            'A1 - Principiante',
            'A2 - Básico',
            'B1 - Intermedio',
            'B2 - Intermedio Alto',
            'C1 - Avanzado',
            'C2 - Nativo/Bilingüe',
        ];

        foreach ($idiomas as $idiomaNombre) {
            // Insertar idioma y obtener ID
            $idiomaId = DB::table('idiomas')->insertGetId([
                'Idioma' => $idiomaNombre,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Insertar todos los niveles para este idioma
            foreach ($niveles as $nivel) {
                DB::table('idioma_nivels')->insert([
                    'IDIdioma' => $idiomaId,
                    'Nivel' => $nivel,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
