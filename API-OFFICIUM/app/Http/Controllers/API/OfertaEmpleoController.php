<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OfertaEmpleo;
use App\Models\User;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

use App\Events\OfertaCreada;


class OfertaEmpleoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // Carga las publicaciones paginadas y sus relaciones:
        // - user: El propietario de la oferta de empleo (asumiendo una relación 'user()' en oferta empleo)
        // - aplicaciones: las aplicaciones asociados a la oferta de empleo
        $ofertasEmpleos = OfertaEmpleo::with([
            'empresa:IDEmpresa,NombreEmpresa,Foto',
            'aplicaciones',
            'categoria:IDCategoria,Nombre',
            'desempleadosAplicados'])
            ->paginate(10);

        // Añade un contador de likes a cada publicación en la colección
        $ofertasEmpleos->each(function ($ofertaEmpleo) {
            $ofertaEmpleo->Nombre_Categoria = $ofertaEmpleo->categoria->Nombre;
        });

        // $ofertasEmpleos = OfertaEmpleo::with(['empresa' => function ($query) {
        //     $query->select('IDEmpresa', 'Nombre', 'Ubicacion');
        // }, 'categoria' => function ($query) {
        //     $query->select('IDCategoria', 'Nombre');
        // }])
        // ->paginate(10);

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Ofertas de empleo listadas correctamente.',
            'data' => $ofertasEmpleos
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
        // Obtén el ID del usuario autenticado por Sanctum
        $userId = auth()->id();
        $empresa = Empresa::where('IDUsuario', $userId)->first();

        // Verifica si el usuario autenticado
        if ($empresa->IDUsuario !== $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso crear esta oferta de empleo."
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'IDCategoria' => 'required|exists:categorias,IDCategoria',
            'Titulo' => 'required|string',
            'Descripcion' => 'required|string',
            'Ubicacion' => 'required|string',
            'Estado' => 'required|string|in:Abierta,Cerrada,En Proceso',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "validation errors.",
                "Message" => $validator->errors()->all()
            ], 422);
        }



        try {
            // Crea el nueva publicacion
            $ofertaEmpleo = new OfertaEmpleo();
            $ofertaEmpleo->IDEmpresa = $empresa->IDUsuario;
            $ofertaEmpleo->IDCategoria = $request->input('IDCategoria');
            $ofertaEmpleo->Titulo = $request->input('Titulo');
            $ofertaEmpleo->Descripcion = $request->input('Descripcion');
            $ofertaEmpleo->Ubicacion = $request->input('Ubicacion');
            $ofertaEmpleo->Estado = $request->input('Estado');
            $ofertaEmpleo->FechaPublicacion = now();


            $ofertaEmpleo->save();

            $ofertaEmpleo->NombreCategoria = $ofertaEmpleo->categoria->Nombre;

            // Disparar el evento OfertaCreada
            event(new OfertaCreada($ofertaEmpleo));


            return response()->json([
                'StatusCode' => 201,
                'ReasonPhrase' => 'Oferta de Empleo subida correctamente.',
                'Message' => 'Oferta de Empleo subida y guardada con éxito.',
                'data' => $ofertaEmpleo->load('categoria', 'empresa','aplicaciones') // Carga la relación documentos si existe
            ], 201); // 201 Created

        } catch (QueryException $e) {

            // Manejar otros errores de base de datos
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al registrar la oferta de empleo"."\n".$e->getMessage(),
                'SQL error: ' . $e->getMessage(),
                'SQL query: ' . $e->getSql(),
                'Bindings: ', $e->getBindings()

            ], 500); // 500 (Internal Server Error) para otros errores
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(OfertaEmpleo $ofertaEmpleo)
    {
        //
        // Carga la publicación y sus relaciones:
        // - empresa: El propietario de la empresa
        // - aplicaciones: Las aplicaciones asociadas
        $ofertaEmpleo->load(['empresa:IDEmpresa,NombreEmpresa,Foto', 'aplicaciones','categoria:IDCategoria,Nombre']);

        // Añade el nombre de la categoria
        $ofertaEmpleo->CategoriaNombre = $ofertaEmpleo->categoria->Nombre;

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Publicacion encontrada correctamente',
            'Message' => 'La información de la publicacion ha sido encontrada con éxito.',
            'Data' => $ofertaEmpleo,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OfertaEmpleo $ofertaEmpleo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, OfertaEmpleo $ofertaEmpleo)
    {

        //
        $userId = auth()->id();
        $empresa = Empresa::where('IDUsuario', $userId)->first();


        if (!$empresa) {
            return response()->json([
                "StatusCode" => 404,
                "ReasonPhrase" => "Error de credenciales",
                'Message' => 'El usuario de empresa no existe.',
                "Data" => null
            ]);
        }

        // Verifica si el usuario autenticado es el propietario de la publicación
        if ($ofertaEmpleo->IDEmpresa !== $empresa->IDEmpresa) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para modificar esta publicación."
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'IDCategoria' => 'nullable|exists:categorias,IDCategoria',
            'Titulo' => 'nullable|string',
            'Descripcion' => 'nullable|string',
            'Estado' => 'nullable|string|in:Abierta,Cerrada,En Proceso'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "validation errors.",
                "Message" => $validator->errors()->all()
            ], 422);
        }

        try {
            // Actualiza el contenido si se proporciona
            if ($request->filled('IDCategoria')) {
                $ofertaEmpleo->IDCategoria = $request->input('IDCategoria');
               // $ofertaEmpleo->NuevaCategoria = $ofertaEmpleo->categoria->Nombre;
            }
            if ($request->filled('Titulo')) {
                $ofertaEmpleo->Titulo = $request->input('Titulo');
            }
            if ($request->filled('Descripcion')) {
                $ofertaEmpleo->Descripcion = $request->input('Descripcion');
            }
            if ($request->filled('Estado')) {
                $ofertaEmpleo->Estado = $request->input('Estado');
            }

            $ofertaEmpleo->save(); // Guardar la publicación incluso si solo se modificó el contenido

            $ofertaEmpleo->load('empresa','aplicaciones');

            if ($request->filled('IDCategoria')) {
                $ofertaEmpleo->Categoria_Nueva = $ofertaEmpleo->categoria->Nombre;
            }

            return response()->json([
                'StatusCode' => 200,
                'ReasonPhrase' => 'Oferta Empleo actualizada correctamente.',
                'Message' => 'La oferta de empleo ha sido actualizada con éxito.',
                'data' => $ofertaEmpleo->load('empresa','aplicaciones')
            ], 200);

        } catch (QueryException $e) {
            Log::error("Error al actualizar la oferta de empleo: " . $e->getMessage());
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al intentar actualizar la oferta de empleo."
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OfertaEmpleo $ofertaEmpleo)
    {
        //
        $userId = auth()->id();
        $empresa = Empresa::where('IDUsuario', $userId)->first();

        // Verifica si el usuario autenticado es el propietario del documento
        if ($ofertaEmpleo->IDEmpresa !== $empresa->IDEmpresa) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para eliminar esta oferta de empleo."
            ], 403); // 403 (Forbidden)
        }

        try {

            // Eliminar el registro de la base de datos
            $ofertaEmpleo->delete();

            return response()->json([
                "StatusCode" => 200,
                "ReasonPhrase" => "Oferta de empleo eliminada correctamente.",
                "Message" => "La oferta de empleo ha sido eliminada con éxito."
            ], 200); // 200 (OK)

        } catch (\Exception $e) {
            Log::error("Error al eliminar la oferta de emepleo: " . $e->getMessage());
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al intentar eliminar la oferta de empleo."
            ], 500); // 500 (Internal Server Error)
        }
    }


    public function buscar(Request $request)
    {
        $query = OfertaEmpleo::query()
            ->with(['empresa', 'categoria']); // Carga las relaciones necesarias

        // Búsqueda por título (si el parámetro 'titulo' está presente)
        if ($request->has('titulo')) {
            $query->where('Titulo', 'like', '%' . $request->input('titulo') . '%');
        }

        // Búsqueda por ubicación (si el parámetro 'ubicacion' está presente)
        if ($request->has('ubicacion')) {
            $query->where('Ubicacion', 'like', '%' . $request->input('ubicacion') . '%');
        }

        // Búsqueda por categoría (si el parámetro 'categoria' está presente)
        if ($request->has('categoria')) {
            $query->whereHas('categoria', function ($q) use ($request) {
                $q->where('Nombre', 'like', '%' . $request->input('categoria') . '%');
            });
        }

        // Búsqueda por estado (si el parámetro 'estado' está presente)
        if ($request->has('estado')) {
            $query->where('Estado', $request->input('estado'));
        }

        // Puedes añadir más condiciones 'if' para otros parámetros de búsqueda (rango de fechas, etc.)

        $resultados = $query->paginate(10); // Pagina los resultados

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Ofertas de empleo encontradas.',
            'data' => $resultados
        ], 200);
    }
}
