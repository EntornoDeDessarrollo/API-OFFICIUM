<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Desempleado;
use App\Models\Categoria;
use Illuminate\Support\Facades\Validator;

class SuscripcionsController extends Controller
{
    //
    public function store(Request $request)
    {

        $userId = auth()->id();
        $desempleado = Desempleado::where('IDUsuario', $userId)->first();

        // Verifica si el usuario autenticado
        if ($desempleado->IDUsuario !== $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para suscribirte."
            ], 403);
        }

        $validator = Validator::make($request->all(), [

            'IDCategoria' => 'required|exists:categorias,IDCategoria',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "validation errors.",
                "Message" => $validator->errors()->all()
            ], 422);
        }

        $categoria = Categoria::findOrFail($request->input('IDCategoria'));


        // Verifica si ya existe la suscripción para evitar duplicados
        if (!$desempleado->suscripciones()->where('suscripcion.IDCategoria', $categoria->IDCategoria)->exists()) {
            $desempleado->suscripciones()->attach($categoria->IDCategoria);

            return response()->json([
                'StatusCode' => 201,
                'ReasonPhrase' => 'Suscripto.',
                'Message' => 'Suscripción añadida correctamente'
            ], 201);
        } else {
            return response()->json([
                'StatusCode' => 409,
                'ReasonPhrase' => 'Conflict.',
                'Message' => 'Ya estás suscrito a esta categoría'
            ], 409);
        }

    }

    public function destroy(Request $request)
    {
        $userId = auth()->id();
        $desempleado = Desempleado::where('IDUsuario', $userId)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'IDCategoria' => 'required|exists:categorias,IDCategoria',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "validation errors.",
                "Message" => $validator->errors()->all()
            ], 422);
        }

        $categoriaId = $request->input('IDCategoria');

        // Verifica si existe la suscripción antes de intentar eliminarla
        if ($desempleado->suscripciones()->where('suscripcion.IDCategoria', $categoriaId)->exists()) {
            $desempleado->suscripciones()->detach($categoriaId);

            return response()->json([
                "StatusCode" => 200,
                "ReasonPhrase" => "OK.",
                "Message" => "Suscripción eliminada correctamente."
            ], 200);
        } else {
            return response()->json([
                "StatusCode" => 404,
                "ReasonPhrase" => "Not Found.",
                "Message" => "No estás suscrito a esta categoría."
            ], 404);
        }
    }

    public function mySuscriptions()
    {
        $userId = auth()->id();
        $desempleado = Desempleado::where('IDUsuario', $userId)->with('suscripciones')->firstOrFail();

        return response()->json([
            "StatusCode" => 200,
            "ReasonPhrase" => "OK.",
            "Message" => "Suscripciones del usuario listadas correctamente.",
            "data" => $desempleado->suscripciones
        ], 200);
    }
}
