<?php

namespace App\Listeners;

use App\Events\PublicacionComentada;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use Illuminate\Support\Facades\Log;

use App\Models\Notificacion;


class NotificarAutorDeNuevoComentario
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
    public function handle(PublicacionComentada $event): void
    {
        //
        $comentario = $event->comentario;
        $publicacion = $comentario->publicacion; // Asumiendo relación 'publicacion' en Comentario
        $autor = $publicacion->user;
        $comentador = $comentario->user;
        $nombreComentador = '';

        if ($comentador->desempleado) {
            $nombreComentador = $comentador->desempleado->Nombre;
        } elseif ($comentador->empresa) {
            $nombreComentador = $comentador->empresa->NombreEmpresa;
        } else {
            $nombreComentador = 'Un usuario'; // Caso por si acaso no tiene ni desempleado ni empresa (raro)
        }

        Log::info("Nombre del comentador: ".$nombreComentador);
        Log::info("Autor: ".$autor);
        Log::info("ID Usuario: ".$autor->IDUsuario);
        Log::info("ID Comentario: ".$comentario->IDUsuario);

        // No notificar al propio autor del comentario
        if ($autor  && $autor->IDUsuario != $comentario->IDUsuario) {
            Notificacion::create([
                'IDUsuario' => $autor->IDUsuario,
                'Titulo' => 'Nuevo Comentario en tu Publicación',
                'Mensaje' => "{$nombreComentador} ha comentado en tu publicación '{$publicacion->Titulo}': '{$this->limitarTexto($comentario->Contenido)}'.",
                'Leido' => false,
                'FechaNotificacion' => now(),
                'Ruta' => "/post/{$publicacion->IDPublicacion}", // Ruta a la publicación con el comentario anclado
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
