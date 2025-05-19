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
            ->orderByDesc('FechaNotificacion')
            ->get();

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'OK.',
            'Message' => 'Notificaciones del usuario obtenidas correctamente.',
            'data' => $notificaciones,
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
    }
}
