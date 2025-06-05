<?php

namespace App\Listeners;

use App\Events\OfertaAplicada;
use App\Models\Notificacion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificarEmpresaNuevaAplicacion
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
    public function handle(OfertaAplicada $event): void
    {
        //
        $aplicacion = $event->aplicacion;
        $ofertaEmpleo = $aplicacion->oferta; // Asumiendo que tienes una relación 'oferta' en el modelo Aplicacion

        if ($ofertaEmpleo && $ofertaEmpleo->empresa) {
            Notificacion::create([
                'IDUsuario' => $ofertaEmpleo->empresa->IDUsuario,
                'Titulo' => 'Nuevo Aplicante',
                'Mensaje' => "Un nuevo desempleado ha aplicado a tu oferta: '{$ofertaEmpleo->Titulo}'.",
                'Leido' => false,
                'FechaNotificacion' => now(),
                'Ruta' => "/ofertaEmpleo/{$ofertaEmpleo->IDOferta}", // Ruta a la lista de aplicantes o a la aplicación específica
                //'Ruta' => 'ofertas-empleo/' . $ofertaEmpleo->IDOferta . '/aplicaciones/' . $aplicacion->IDAplicacion, // Quiza podria
            ]);
        }

    }
}
