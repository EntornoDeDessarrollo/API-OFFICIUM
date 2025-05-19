<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Documento;
use Illuminate\Support\Str;
use App\Models\Publicacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use App\Events\PublicacionLiked;


class PublicacionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // Carga las publicaciones paginadas y sus relaciones:
        // - user: El propietario de la publicación (asumiendo una relación 'user()' en Publicacion)
        // - documentos: Los documentos asociados a la publicación
        // - comentarios: Los comentarios asociados a la publicación, y dentro de cada comentario, su usuario ('comentarios.user')
        $publicaciones = Publicacion::with(['user', 'documentos', 'comentarios.user', 'likes'])
            ->whereNull('IDGrupo')
            ->paginate(10);

        // Añade un contador de likes a cada publicación en la colección
        $publicaciones->each(function ($publicacion) {
            $publicacion->likes_count = $publicacion->likes->count();
            //unset($publicacion->likes); // Opcional: elimina la colección de likes para reducir el tamaño de la respuesta
        });

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Publicaciones listadas correctamente.',
            'data' => $publicaciones
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

        $validator = Validator::make($request->all(), [
            'Contenido' => 'required|string',
            'TipoArchivo' => 'nullable|string|in:Foto,Video,PDF',
            'Archivo' => 'nullable|file',
            'IDGrupo' => 'nullable|exists:grupos,IDGrupo', // IDGrupo es opcional y debe existir en la tabla 'grupos'
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
            $publicacion = new Publicacion();
            $publicacion->Contenido = $request->input('Contenido');
            $publicacion->IDUsuario = $userId;
            $publicacion->TipoArchivo = $request->input('TipoArchivo');
            $publicacion->FechaPublicacion = now();

            // Asigna el IDGrupo si se proporciona en la petición
            if ($request->filled('IDGrupo')) {
                $publicacion->IDGrupo = $request->input('IDGrupo');
            }

            $publicacion->save();

            // Manejo de la foto
            if ($request->hasFile('Archivo')) {

                $user = User::findOrFail($userId);
                // Determina la carpeta base según el tipo de perfil
                $carpetaBase = '';
                if ($user->rol === 'empresa') {
                    $carpetaBase = 'Empresa';
                } elseif ($user->rol === 'usuario') {
                    $carpetaBase = 'Desempleado';
                }
                $Archivo = $request->file('Archivo');
                $nombreArchivoUnico = Str::uuid() . '.' . $Archivo->getClientOriginalExtension();
                $rutaAlmacenamiento = "{$carpetaBase}/{$userId}/Publicacion"; // Misma carpeta que en el store
                $rutaArchivoNuevo = $Archivo->storeAs($rutaAlmacenamiento, $nombreArchivoUnico, 'public');

                if (!$rutaArchivoNuevo) {
                    return response()->json([
                        "StatusCode" => 500,
                        "ReasonPhrase" => "Error al guardar el nuevo archivo.",
                        "Message" => "No se pudo guardar el archivo en el sistema."
                    ], 500);
                }

                $publicacion->Archivo = Storage::url($rutaArchivoNuevo);
                $publicacion->save();

                // Crea un nuevo registro en la tabla documentos
                $documento = new Documento();
                $documento->IDUsuario = $userId;
                $documento->IDPublicacion = $publicacion->IDPublicacion; // Se asignará después de guardar la publicación
                $documento->NombreArchivo = $nombreArchivoUnico;
                $documento->URL = Storage::url($rutaArchivoNuevo);
                $documento->Tipo ="Publicacion";
                $documento->FechaSubida = now();
                $documento->save();

                // // Asigna el ID de la publicación al documento (esto debe hacerse después de guardar la publicación para tener su ID)
                // $documento->IDPublicacion = $publicacion->IDPublicacion;
                // $documento->save();
            }

            //$publicacion->save();

            return response()->json([
                'StatusCode' => 201,
                'ReasonPhrase' => 'Publicacion subida correctamente.',
                'Message' => 'Publicacion subida y guardada con éxito.',
                'data' => $publicacion->load('documentos', 'grupo') // Carga la relación documentos si existe
            ], 201); // 201 Created

        } catch (QueryException $e) {

            // Manejar otros errores de base de datos
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al registrar la publicacion?"."\n".$e->getMessage(),
                'SQL error: ' . $e->getMessage(),
                'SQL query: ' . $e->getSql(),
                'Bindings: ', $e->getBindings()

            ], 500); // 500 (Internal Server Error) para otros errores
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Publicacion $publicacion)
    {
        // Carga la publicación y sus relaciones:
        // - user: El propietario de la publicación
        // - documentos: Los documentos asociados
        // - comentarios: Los comentarios asociados, con su respectivo usuario
        $publicacion->load(['user', 'documentos', 'comentarios.user','likes']);

        // Añade un contador de likes al objeto de la publicación
        $publicacion->likes_count = $publicacion->likes->count();

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Publicacion encontrada correctamente',
            'Message' => 'La información de la publicacion ha sido encontrada con éxito.',
            'Data' => $publicacion,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Publicacion $publicacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Publicacion $publicacion)
    {
        //
        $userId = auth()->id();

        // Verifica si el usuario autenticado es el propietario de la publicación
        if ($publicacion->IDUsuario !== $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para modificar esta publicación."
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'Contenido' => 'nullable|string',
            'Archivo' => 'nullable|file|image'
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
            if ($request->filled('Contenido')) {
                $publicacion->Contenido = $request->input('Contenido');
            }

            // Manejo del nuevo archivo si se proporciona
            if ($request->hasFile('Archivo')) {
                $user = User::findOrFail($userId);
                $carpetaBase = '';
                if ($user->rol === 'empresa') {
                    $carpetaBase = 'Empresa';
                } elseif ($user->rol === 'usuario') {
                    $carpetaBase = 'Desempleado';
                }
                $archivo = $request->file('Archivo');
                $nombreArchivoUnico = Str::uuid() . '.' . $archivo->getClientOriginalExtension();
                $rutaAlmacenamiento = "{$carpetaBase}/{$userId}/Publicacion";
                $rutaArchivoNuevo = $archivo->storeAs($rutaAlmacenamiento, $nombreArchivoUnico, 'public');

                if (!$rutaArchivoNuevo) {
                    return response()->json([
                        "StatusCode" => 500,
                        "ReasonPhrase" => "Error al guardar el nuevo archivo.",
                        "Message" => "No se pudo guardar el nuevo archivo en el sistema."
                    ], 500);
                }

                // Eliminar el archivo anterior del sistema de archivos (si existe)
                if ($publicacion->Archivo) {
                    $rutaArchivoAnterior = str_replace(Storage::url(''), '', $publicacion->Archivo);
                    if (Storage::disk('public')->exists($rutaArchivoAnterior)) {
                        Storage::disk('public')->delete($rutaArchivoAnterior);
                    }
                }

                // Actualizar la ruta del archivo en la publicación
                $publicacion->Archivo = Storage::url($rutaArchivoNuevo);
                $publicacion->save(); // Guardar la publicación aquí para que el cambio en Archivo se refleje al buscar el documento

                // Actualizar o crear el registro del documento
                $documento = $publicacion->documentos()->firstOrNew();
                $documento->IDUsuario = $userId;
                $documento->IDPublicacion = $publicacion->IDPublicacion;
                $documento->NombreArchivo = $nombreArchivoUnico;
                $documento->URL = Storage::url($rutaArchivoNuevo);
                $documento->Tipo = "Publicacion";
                $documento->FechaSubida = now();
                $documento->save();
            } else {
                $publicacion->save(); // Guardar la publicación incluso si solo se modificó el contenido
            }

            return response()->json([
                'StatusCode' => 200,
                'ReasonPhrase' => 'Publicacion actualizada correctamente.',
                'Message' => 'La publicación ha sido actualizada con éxito.',
                'data' => $publicacion->load('documentos')
            ], 200);

        } catch (QueryException $e) {
            Log::error("Error al actualizar la publicacion: " . $e->getMessage());
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al intentar actualizar la publicación."
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Publicacion $publicacion)
    {
        //
        $userId = auth()->id();

        // Verifica si el usuario autenticado es el propietario del documento
        if ($publicacion->IDUsuario !== $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para eliminar esta publicacion."
            ], 403); // 403 (Forbidden)
        }

        try {
            // Eliminar el archivo del sistema de archivos
            if ($publicacion->Archivo) {
                $rutaArchivo = str_replace(Storage::url(''), '', $publicacion->Archivo);
                if (Storage::disk('public')->exists($rutaArchivo)) {
                    Storage::disk('public')->delete($rutaArchivo);
                }
            }

            // Eliminar el registro de la base de datos
            $publicacion->delete();

            return response()->json([
                "StatusCode" => 200,
                "ReasonPhrase" => "Publicacion eliminada correctamente.",
                "Message" => "La publicacion ha sido eliminada con éxito."
            ], 200); // 200 (OK)

        } catch (\Exception $e) {
            Log::error("Error al eliminar la publicacion: " . $e->getMessage());
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al intentar eliminar la publicacion."
            ], 500); // 500 (Internal Server Error)
        }
    }


    public function like(Request $request, Publicacion $publicacion)
    {
        $user = auth()->id();

        // Verifica si el usuario ya dio like
        if ($publicacion->likes()->where('likes.IDUsuario', $user)->exists()) {
            return response()->json([
                'StatusCode' => 409, // Conflict
                'ReasonPhrase' => 'El usuario ya ha dado like a esta publicación.',
            ], 409);
        }

        $publicacion->likes()->attach($user);

        // Disparar el evento PublicacionLiked
        event(new PublicacionLiked($publicacion, Auth::user()));

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Like añadido correctamente.',
            'likes_count' => $publicacion->likes()->count(),
        ], 200);
    }

    public function unlike(Request $request, Publicacion $publicacion)
    {
        $user = auth()->id();

        // Verifica si el usuario dio like previamente
        if (!$publicacion->likes()->where('likes.IDUsuario', $user)->exists()) {
            return response()->json([
                'StatusCode' => 404, // Not Found
                'ReasonPhrase' => 'El usuario no ha dado like a esta publicación.',
            ], 404);
        }

        $publicacion->likes()->detach($user);

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Like eliminado correctamente.',
            'likes_count' => $publicacion->likes()->count(),
        ], 200);
    }

    public function liked(Publicacion $publicacion)
    {
        $user = auth()->id();
        $liked = $publicacion->likes()->where('likes.IDUsuario', $user)->exists();

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Estado del like obtenido correctamente.',
            'liked' => $liked,
        ], 200);
    }

    public function postsByUsuario()
    {

        $userId = auth()->id();
        $user = User::findOrFail($userId);

        // Obtener publicaciones donde IDUsuario coincida y IDGrupo sea NULL
        // Inicia la consulta de publicaciones
        $publicacionesQuery = Publicacion::where('IDUsuario', $userId)
                                         ->whereNull('IDGrupo')
                                         ->with(['documentos', 'likes'])
                                         ->withCount('comentarios');

        // 1. Carga el perfil del PROPIETARIO de la publicación
        if ($user->rol === 'empresa') {
            $publicacionesQuery->with('user.empresa');


        } elseif ($user->rol === 'usuario') {
            $publicacionesQuery->with('user.desempleado');
        }

        // 2. Carga los comentarios y, dentro de cada comentario, carga el perfil específico del USUARIO del comentario
        $publicacionesQuery->with(['comentarios' => function ($query) {
            $query->with(['user' => function ($userQuery) {
                // Dentro de la relación 'user' del comentario
                // Aquí usamos una carga selectiva basada en el 'rol' del usuario del comentario
                $userQuery->with(['empresa' => function ($empresaQuery) {
                    // Solo cargar 'empresa' si el rol es 'empresa'
                    $empresaQuery->whereHas('user', function ($q) {
                        $q->where('rol', 'empresa');
                    });
                }, 'desempleado' => function ($desempleadoQuery) {
                    // Solo cargar 'desempleado' si el rol es 'usuario'
                    $desempleadoQuery->whereHas('user', function ($q) {
                        $q->where('rol', 'usuario');
                    });
                }]);
            }]);
        }]);

        // Carga el usuario propietario de la publicación base (si no se hizo antes con el condicional)
        $publicacionesQuery->with('user');


        $publicaciones = $publicacionesQuery->get();

        // Opcional: Añadir el contador de likes para cada publicación
        $publicaciones->each(function ($publicacion) use ($userId) {
            $publicacion->likes_count = $publicacion->likes->count();
             // Comprueba si el usuario autenticado ha dado like a esta publicación
            $publicacion->likedByCurrentUser = $publicacion->likes->contains('IDUsuario', $userId);
            // Si quieres ocultar la colección 'likes' después de usarla, puedes hacerlo
            //unset($publicacion->likes); // Elimina la relación 'likes' de la respuesta JSON si no la necesitas en el frontend
        });

        if ($publicaciones->isEmpty()) {
            return response()->json([
                'StatusCode' => 404,
                'ReasonPhrase' => 'No Content',
                'Message' => 'No se encontraron publicaciones para el usuario especificado sin asignación de grupo.',
                'Data' => []
            ], 404);
        }

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'OK',
            'Message' => 'Publicaciones obtenidas correctamente.',
            'Data' => $publicaciones,
        ], 200);


    }

}
