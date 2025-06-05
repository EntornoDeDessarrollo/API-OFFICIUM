<?php

namespace App\Listeners;

use App\Events\EstadoAplicacion;
use App\Models\Notificacion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class NotificarDesempleadoEstadoAplicacion
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
    public function handle(EstadoAplicacion $event): void
    {
        //
        $aplicacion = $event->aplicacion;
        $ofertaEmpleo = $aplicacion->oferta; // Asumiendo la relación 'oferta' en Aplicacion
        $desempleado = $aplicacion->desempleado; // Asumiendo la relación 'desempleado' en Aplicacion

        if ($desempleado && $ofertaEmpleo) {
            Notificacion::create([
                'IDUsuario' => $desempleado->IDUsuario,
                'Titulo' => 'Estado de Aplicación Actualizado',
                'Mensaje' => "El estado de tu aplicación a la oferta '{$ofertaEmpleo->Titulo}' ha cambiado a '{$aplicacion->Estado}'.",
                'Leido' => false,
                'FechaNotificacion' => now(),
                'Ruta' => "/aplicacion/{$aplicacion->IDAplicacion}"  , // Ruta a los detalles de la aplicación del desempleado
            ]);
        }
    }
}
