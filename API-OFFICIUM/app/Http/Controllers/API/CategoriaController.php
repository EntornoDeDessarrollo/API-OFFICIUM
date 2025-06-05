<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Categoria;
use App\Models\Desempleado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class CategoriaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        //
        $categoria = Categoria::get();

        return response()->json([
            "StatusCode" => 200,
            "ReasonPhrase" => "Todos las categorias",
            'Message' => 'Sectores resividos correctamente',
            "Data" => $categoria
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
    public function show(Categoria $categoria)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        //
    }

    public function categoryUser()
    {
        //

        $userId = auth()->id();

        $desempleado = Desempleado::where('IDUsuario', $userId)->first();

        if (!$desempleado) {
            return response()->json([
                "StatusCode" => 404,
                "ReasonPhrase" => "Not Found",
                'Message' => 'No se encontró información de desempleado para el usuario autenticado.',
                "Data" => []
            ], 404);
        }

        $desempleadoId = $desempleado->IDDesempleado;

        // Obtener las categorías a las que el usuario NO está suscrito
        $categoriasNoSuscritas = Categoria::whereDoesntHave('suscriptores', function ($query) use ($desempleadoId) {
            $query->where('suscripcion.IDDesempleado', $desempleadoId);
        })->get();

        return response()->json([
            "StatusCode" => 200,
            "ReasonPhrase" => "Todos las categorias",
            'Message' => 'Sectores resividos correctamente',
            "Data" => $categoriasNoSuscritas
        ], 200);
    }
}
