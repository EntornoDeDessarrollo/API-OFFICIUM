<?php

namespace App\Listeners;

use App\Events\PublicacionLiked;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\Notificacion;

class NotificarAutorDeNuevoLike
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
    public function handle(PublicacionLiked $event): void
    {
        //
        $publicacion = $event->publicacion;
        $usuarioLike = $event->usuarioLike;
        $autor = $publicacion->user;

        if ($autor && $autor->IDUsuario != $usuarioLike->IDUsuario) {
            $nombreLiker = '';
            if ($usuarioLike->desempleado) {
                $nombreLiker = $usuarioLike->desempleado->Nombre;
            } elseif ($usuarioLike->empresa) {
                $nombreLiker = $usuarioLike->empresa->NombreEmpresa;
            } else {
                $nombreLiker = 'Un usuario';
            }

            Notificacion::create([
                'IDUsuario' => $autor->IDUsuario,
                'Titulo' => 'Nuevo Like en tu Publicación',
                'Mensaje' => "{$nombreLiker} le ha dado like a tu publicación '{$this->limitarTexto($publicacion->Contenido)}'.", // Usamos un contenido limitado o el título si lo tienes
                'Leido' => false,
                'FechaNotificacion' => now(),
                'Ruta' => 'publicacion/' . $publicacion->IDPublicacion,
            ]);
        }
    }

        /**
     * Limita la longitud del texto para el mensaje de la notificación.
     *
     * @param  string  $texto
     * @param  int  $limite
     * @return string
     */
    protected function limitarTexto(string $texto, int $limite = 50): string
    {
        return \Illuminate\Support\Str::limit($texto, $limite, '...');
    }
}
