<?php

namespace App\Listeners;

use App\Events\UsuarioSeUnioAGrupo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\Notificacion;

class NotificarPropietarioDeGrupo
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
    public function handle(UsuarioSeUnioAGrupo $event): void
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
                'Titulo' => 'Nuevo Miembro en tu Grupo',
                'Mensaje' => "{$nombreUsuarioUnido} se ha unido a tu grupo '{$grupo->Nombre}'.",
                'Leido' => false,
                'FechaNotificacion' => now(),
                'Ruta' => 'grupo/' . $grupo->IDGrupo, // Ruta al grupo
            ]);
        }
    }
}
