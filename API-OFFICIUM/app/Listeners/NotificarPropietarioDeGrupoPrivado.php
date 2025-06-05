<?php

namespace App\Listeners;

use App\Events\UsuarioSeUnioAGrupoPrivado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

use App\Models\Notificacion;

class NotificarPropietarioDeGrupoPrivado
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
    public function handle(UsuarioSeUnioAGrupoPrivado $event): void
    {
        //
        $grupo = $event->grupo;
        $usuarioUnido = $event->usuario;
        $propietario = $grupo->propietario; // Asumiendo relación 'propietario' en Grupo

        if ($propietario && $propietario->IDUsuario !== $usuarioUnido->IDUsuario) {
            $nombreUsuarioUnido = '';
            if ($usuarioUnido->desempleado) {
                $nombreUsuarioUnido = $usuarioUnido->desempleado->Nombre;
            } elseif ($usuarioUnido->empresa) {
                $nombreUsuarioUnido = $usuarioUnido->empresa->NombreEmpresa;
            } else {
                $nombreUsuarioUnido = 'Un usuario';
            }

            Notificacion::create([
                'IDUsuario' => $propietario->IDUsuario,
                'Titulo' => 'Solicitud en tu Grupo Privado',
                'Mensaje' => "{$nombreUsuarioUnido} se quiere unirse a tu grupo '{$grupo->Nombre}'.",
                'Leido' => false,
                'FechaNotificacion' => now(),
                'Ruta' => "/grupos/{$grupo->IDGrupo}" // Ruta a la página del grupo
            ]);
        }
    }
}
