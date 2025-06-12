<?php

namespace App\Listeners;

use App\Events\UsuarioAceptoSolicitud;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\Notificacion;

class NotificarUsuarioDeAceptacionGrupoPrivado
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
    public function handle(UsuarioAceptoSolicitud $event): void
    {
        //
        $grupo = $event->grupo;
        $usuarioAceptado = $event->usuario  ;

        // Obtener el nombre del usuario aceptado para el mensaje
        $nombreUsuarioAceptado = $usuarioAceptado->desempleado->Nombre ??
         $usuarioAceptado->empresa->NombreEmpresa ??
         $usuarioAceptado->email;

        // Crear la notificación en la base de datos
        Notificacion::create([
            'IDUsuario' => $usuarioAceptado->IDUsuario, // El ID del usuario que va a recibir la notificación
            'Titulo' => '¡Solicitud de Grupo Aceptada!',
            'Mensaje' => "Tu solicitud para unirte al grupo '{$grupo->Nombre}' ha sido aceptada. ¡Bienvenido!",
            'Leido' => false,
            'FechaNotificacion' => now(),
            'Ruta' => "/grupos/{$grupo->IDGrupo}" // Ruta a la página del grupo
        ]);
    }
}
