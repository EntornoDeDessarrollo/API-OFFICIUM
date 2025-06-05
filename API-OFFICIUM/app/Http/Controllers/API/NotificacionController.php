<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use Illuminate\Http\Request;

class NotificacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $userId = auth()->id();

        if (!$userId) {
            return response()->json([
                'StatusCode' => 401,
                'ReasonPhrase' => 'No autenticado.',
                'Message' => 'Usuario no autenticado.',
            ], 401);
        }

        $notificaciones = Notificacion::where('IDUsuario', $userId)
            //->orderByDesc('FechaNotificacion')
            ->orderBy('FechaNotificacion', 'desc')
            ->get();

        $unreadCount = Notificacion::where('IDUsuario', $userId)
            ->where('Leido', false) // Asumiendo que 'Leido' es un booleano (0 para no leído, 1 para leído)
            ->count();

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'OK.',
            'Message' => 'Notificaciones del usuario obtenidas correctamente.',
            'Data' => $notificaciones,
            'UnreadCount' => $unreadCount,
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Notificacion $notificacion)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Notificacion $notificacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Notificacion $notificacion)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Notificacion $notificacion)
    {
        //
         $userId = auth()->id();

        // Verifica si el usuario autenticado es el propietario del documento
        if ($notificacion->IDUsuario !== $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para eliminar esta notificación."
            ], 403); // 403 (Forbidden)
        }

        // Eliminar el registro de la base de datos
        $notificacion->delete();

        return response()->json([
            "StatusCode" => 200,
            "ReasonPhrase" => "Notificación eliminada correctamente.",
            "Message" => "La Notificación ha sido eliminada con éxito."
        ], 200); // 200 (OK)

    }

    public function markAsRead($notificacionId) // Puedes pasar el ID directamente en la ruta
    {
        $userId = auth()->id(); // Obtén el ID del usuario autenticado

        try {
            // Busca la notificación por su ID y asegúrate de que pertenezca al usuario autenticado.
            // firstOrFail() lanzará ModelNotFoundException si no la encuentra.
            $notificacion = Notificacion::where('IDNotificacion', $notificacionId)
                                        ->where('IDUsuario', $userId)
                                        ->firstOrFail();

            // Si ya está leída, puedes devolver una respuesta que lo indique (opcional)
            if ($notificacion->Leido) {
                return response()->json([
                    'StatusCode' => 200, // O 204 No Content si prefieres
                    'ReasonPhrase' => 'OK.',
                    'Message' => 'La notificación ya estaba marcada como leída.',
                ], 200);
            }

            // Marca la notificación como leída
            $notificacion->Leido = true;
            $notificacion->save();

            return response()->json([
                'StatusCode' => 200,
                'ReasonPhrase' => 'OK.',
                'Message' => 'Notificación marcada como leída correctamente.',
            ], 200);

        } catch (ModelNotFoundException $e) {
            // Se ejecuta si la notificación no existe o no pertenece al usuario
            return response()->json([
                'StatusCode' => 404,
                'ReasonPhrase' => 'No encontrada.',
                'Message' => 'Notificación no encontrada o no pertenece al usuario autenticado.',
            ], 404);
        } catch (\Exception $e) {
            // Captura cualquier otro error inesperado
            Log::error("Error al marcar notificación {$notificacionId} como leída para el usuario {$userId}: " . $e->getMessage());
            return response()->json([
                'StatusCode' => 500,
                'ReasonPhrase' => 'Error interno del servidor.',
                'Message' => 'Ocurrió un error inesperado al marcar la notificación como leída.',
            ], 500);
        }
    }


}
