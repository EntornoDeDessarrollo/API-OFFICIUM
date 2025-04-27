<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Comentario;
use App\Models\Publicacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class ComentarioController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        // Obtén el ID del usuario autenticado por Sanctum
        $userId = auth()->id();

        $validator = Validator::make($request->all(), [
            'IDPublicacion' => 'required|exists:publicacions,IDPublicacion',
            'Contenido' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "validation errors.",
                "Message" => $validator->errors()->all()
            ], 422);
        }

        try {
            // Crea el nuevo comentario
            $comentario = new Comentario();
            $comentario->IDUsuario = $userId;
            $comentario->IDPublicacion = $request->input('IDPublicacion');
            $comentario->Contenido = $request->input('Contenido');
            $comentario->FechaComentario = now();
            $comentario->save();

            // Carga la relación con el usuario para incluirlo en la respuesta
            $comentario->load('user');

            return response()->json([
                'StatusCode' => 201,
                'ReasonPhrase' => 'Comentario creado correctamente.',
                'Message' => 'El comentario ha sido añadido con éxito.',
                'data' => $comentario
            ], 201); // 201 Created

        } catch (\Exception $e) {
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al registrar el comentario." . "\n" . $e->getMessage()
            ], 500); // 500 (Internal Server Error)
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Comentario $comentario)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comentario $comentario)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comentario $comentario)
    {
        //
        // Obtén el ID del usuario autenticado
        $userId = auth()->id();

        // Verifica si el usuario autenticado es el propietario del comentario
        if ($comentario->IDUsuario !== $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para modificar este comentario."
            ], 403); // 403 (Forbidden)
        }

        $validator = Validator::make($request->all(), [
            'Contenido' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "validation errors.",
                "Message" => $validator->errors()->all()
            ], 422);
        }

        try {
            // Actualiza el contenido del comentario
            $comentario->Contenido = $request->input('Contenido');
            $comentario->save();

            // Carga la relación con el usuario para incluirlo en la respuesta
            $comentario->load('user');

            return response()->json([
                'StatusCode' => 200,
                'ReasonPhrase' => 'Comentario actualizado correctamente.',
                'Message' => 'El comentario ha sido modificado con éxito.',
                'data' => $comentario
            ], 200); // 200 OK

        } catch (\Exception $e) {
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al actualizar el comentario." . "\n" . $e->getMessage()
            ], 500); // 500 (Internal Server Error)
        }


    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comentario $comentario)
    {
        //
        // Obtén el ID del usuario autenticado
        $userId = auth()->id();

        // Verifica si el usuario autenticado es el propietario del comentario
        if ($comentario->IDUsuario !== $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para eliminar este comentario."
            ], 403); // 403 (Forbidden)
        }

        try {
            // Elimina el comentario
            $comentario->delete();

            return response()->json([
                'StatusCode' => 200,
                'ReasonPhrase' => 'Comentario eliminado correctamente.',
                'Message' => 'El comentario ha sido borrado con éxito.'
            ], 200); // 200 OK

        } catch (\Exception $e) {
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al eliminar el comentario." . "\n" . $e->getMessage()
            ], 500); // 500 (Internal Server Error)
        }
    }
}
