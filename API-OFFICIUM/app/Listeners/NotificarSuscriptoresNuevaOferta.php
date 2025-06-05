<?php

namespace App\Listeners;

use App\Events\OfertaCreada;
use App\Models\Notificacion;
use App\Models\Categoria;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificarSuscriptoresNuevaOferta
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OfertaCreada $event)
    {
        //
        $ofertaEmpleo = $event->ofertaEmpleo;
        $categoriaId = $ofertaEmpleo->IDCategoria;

        // Obtener todos los desempleados suscritos a esta categoría
        $suscriptores = Categoria::find($categoriaId)->suscriptores;

        foreach ($suscriptores as $desempleado) {
            Notificacion::create([
                'IDUsuario' => $desempleado->IDUsuario,
                'Titulo' => 'Nueva Oferta de Empleo',
                'Mensaje' => "Se ha publicado una nueva oferta de empleo en la categoría a la que estás suscrito: '{$ofertaEmpleo->Titulo}'. ¡Échale un vistazo!",
                'Leido' => false,
                'FechaNotificacion' => now(),
                'Ruta' => "/ofertaEmpleo/{$ofertaEmpleo->IDOferta}",
            ]);
        }

    }
}
