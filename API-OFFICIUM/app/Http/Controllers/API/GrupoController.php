<?php

namespace App\Http\Controllers\API;

use App\Models\Grupo;
use App\Models\User;
use App\Models\Publicacion;
use App\Events\UsuarioSeUnioAGrupo;
use App\Events\UsuarioSeUnioAGrupoPrivado;
use App\Events\UsuarioAceptoSolicitud;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

class GrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        $grupos = Grupo::paginate(10); // Carga los grupos paginados, 10 por página

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Grupos listados correctamente.',
            'Data' => $grupos
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

        // Verifica si el ID del usuario autenticado coincide con el IDUsuario de la empresa
        // if ($request->IDUsuario != $userId) {
        //     return response()->json([
        //         "StatusCode" => 403,
        //         "ReasonPhrase" => "Acceso no autorizado.",
        //         "Message" => "No tienes permiso para modificar esta empresa. "."UsuarioID Token :".$userId." UsuarioID Fomr :".$request->IDUsuario
        //     ], 403); // 403 (Forbidden) si no coincide
        // }

        $validator = Validator::make($request->all(), [
            //'IDUsuario' => 'required|exists:users,IDUsuario',
            'Nombre' => 'required|max:255|unique:grupos,Nombre',
            'Descripcion' => 'nullable|string', // Define los tipos permitidos
            'Privacidad' => 'required|string|in:Publico,Privado', // Ajusta el tamaño máximo según necesites (en KB)
            'Foto' => 'required|file|image'
        ]);

        if ($validator->fails()) {
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "validation errors.",
                "Message" => $validator->errors()->all()
            ], 422);
        }

        try {

            //Foto
            // Determina la carpeta base según el tipo de perfil
            $user = User::findOrFail($userId);
            $carpetaBase = '';
            if ($user->rol === 'empresa') {
                $carpetaBase = 'Empresa';
            } elseif ($user->rol === 'usuario') {
                $carpetaBase = 'Desempleado';
            } else {
                return response()->json([
                    "StatusCode" => 400,
                    "ReasonPhrase" => "Error en la petición.",
                    "Message" => "El rol del usuario no es válido para la subida de documentos."
                ], 400);
            }

            // Manejo de la foto
            $foto = $request->file('Foto');
            $nombreFotoUnico = Str::uuid() . '.' . $foto->getClientOriginalExtension();

            // Define la ruta de almacenamiento
            $rutaAlmacenamiento = "{$carpetaBase}/{$userId}/Grupos";

            // Guarda el archivo
            $rutaFoto = $foto->storeAs($rutaAlmacenamiento, $nombreFotoUnico, 'public');


            if (!$rutaFoto) {
                return response()->json([
                    "StatusCode" => 500,
                    "ReasonPhrase" => "Error al guardar el archivo.",
                    "Message" => "No se pudo guardar el archivo en el sistema."
                ], 500);
            }

            // Crea el nuevo grupo
            $grupo = new Grupo();
            $grupo->Nombre = $request->input('Nombre');
            $grupo->Descripcion = $request->input('Descripcion');
            $grupo->Privacidad = $request->input('Privacidad');
            $grupo->Propietario = $userId;
            $grupo->Foto = Storage::url($rutaFoto);
            $grupo->save();

            // Asocia al usuario autenticado como miembro del grupo
            //$user = User::findOrFail($userId);
            //$grupo->users()->attach($user);
              $grupo->users()->attach($user->IDUsuario, ['EstadoMiembro' => 'Unido']);

            return response()->json([
                'StatusCode' => 201,
                'ReasonPhrase' => 'Grupo creado correctamente.',
                'Message' => 'El grupo ha sido creado y el usuario se ha unido como miembro.',
                'data' => $grupo->fresh()->load('users') // Recarga el grupo con los usuarios asociados
            ], 201); // 201 Created

        } catch (QueryException $e) {


            // Manejar otros errores de base de datos
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al registrar el usuario.?"."\n".$e->getMessage(),
                'SQL error: ' . $e->getMessage(),
                'SQL query: ' . $e->getSql(),
                'Bindings: ', $e->getBindings()

            ], 500); // 500 (Internal Server Error) para otros errores
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Grupo $grupo)
    {
        //
        //ID usuario autenticado.
        $userId = auth()->id();


        // Carga el grupo y sus relaciones necesarias.
        // 'users' para los miembros del grupo.
        // 'publicaciones' para los posts del grupo.
        // Dentro de 'publicaciones', anidamos las relaciones:
        //    - 'user': el usuario que hizo la publicación.
        //    - 'user.desempleado': si el usuario es desempleado.
        //    - 'user.empresa': si el usuario es una empresa.
        //    - 'comentarios': los comentarios de cada publicación.
        //    - 'comentarios.user': el usuario que hizo cada comentario.
        //    - 'comentarios.user.desempleado': si el usuario del comentario es desempleado.
        //    - 'comentarios.user.empresa': si el usuario del comentario es una empresa.

         $grupo->load([
            'users' => function ($query) {
                // Carga el perfil específico de cada usuario miembro del grupo
                $query->wherePivot('EstadoMiembro', 'Unido')
                ->with(['desempleado' => function ($desempleadoQuery) {
                    $desempleadoQuery->whereHas('user', function ($q) {
                        $q->where('rol', 'usuario'); // Solo cargar si el rol es 'usuario'
                    });
                }, 'empresa' => function ($empresaQuery) {
                    $empresaQuery->whereHas('user', function ($q) {
                        $q->where('rol', 'empresa'); // Solo cargar si el rol es 'empresa'
                    });
                }]);
            },// Para verificar la membresía
            'propietario',
            'publicaciones' => function ($query)  use ($grupo) {
                $query
                ->where('IDGrupo',$grupo->IDGrupo)//Donde la publicacion sea del grupo en cuestion
                ->orderBy('created_at', 'desc') // Opcional: ordenar publicaciones por fecha
                ->withCount('documentos', 'likes') // Añade el contador de comentarios
                ->withCount('comentarios')
                ->with(['documentos','likes']); // Carga la relación 'likes' para la comprobación de `has_liked`
            },
            'publicaciones.user.desempleado', // Carga el perfil de desempleado del usuario que publicó
            'publicaciones.user.empresa',     // Carga el perfil de empresa del usuario que publicó
            'publicaciones.comentarios' => function ($query) {
                $query->orderBy('FechaComentario', 'desc'); // Opcional: ordenar comentarios por fecha
            },
            'publicaciones.comentarios.user.desempleado', // Carga el perfil de desempleado del usuario del comentario
            'publicaciones.comentarios.user.empresa',     // Carga el perfil de empresa del usuario del comentario
            // También puedes agregar contadores si los usas en el frontend (ej. likes_count, comentarios_count)
            //'publicaciones.likesCount', // Assuming you have a `likesCount` relationship or attribute
            //'publicaciones.comentariosCount' // Assuming you have a `comentariosCount` relationship or attribute
        ]);

        //  Determina el estado de la membresía del USUARIO ACTUAL en el grupo
        $currentUserMembershipStatus = 'NoUnido'; // Valor por defecto

        if ($userId) {
            // Consulta directamente la tabla pivote para obtener el estado del usuario actual,
            // sin importar si está 'Unido', 'Pendiente', o 'Rechazado'.
            $membership = $grupo->users()
                                ->where('users.IDUsuario', $userId)
                                ->withPivot('EstadoMiembro') // Necesitamos la columna del estado
                                ->first();

            if ($membership) {
                $currentUserMembershipStatus = $membership->pivot->EstadoMiembro;
            }
        }

        // Puedes añadir un atributo dinámico al grupo para indicar si el usuario actual lo posee.
        // Esto se usa para el botón "Editar Grupo".
        $grupo->is_owner = ($grupo->Propietario === $userId);

        // Puedes añadir un atributo dinámico a cada publicación para indicar si el usuario actual es el autor.
        // Esto se usa para los botones "Editar/Eliminar" de cada post.
        $grupo->publicaciones->each(function ($publicacion) use ($userId) {
            $publicacion->is_author = ($publicacion->IDUsuario === $userId);
            $publicacion->likes_count = $publicacion->likes->count();
            // También puedes añadir si el usuario actual le ha dado like a la publicación
            $publicacion->likedByCurrentUser = $publicacion->likes->contains('IDUsuario', $userId);
            // Si necesitas cargar los likes específicos para la comprobación
            // $publicacion->load('likes');
        });

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Grupo encontrado correctamente',
            'Message' => 'La información del grupo ha sido encontrada con éxito.',
            'Data' => $grupo,
            'Member' => $currentUserMembershipStatus

        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Grupo $grupo)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Grupo $grupo)
    {
        //
        $userId = auth()->id(); // Obtén el ID del usuario autenticado

        // Verifica si el usuario autenticado es el propietario del grupo
        if ($grupo->Propietario !== $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para modificar este grupo."
            ], 403); // 403 (Forbidden)
        }

        $rules = [
            'Nombre' => 'nullable|string|max:255|unique:grupos,Nombre,' . $grupo->IDGrupo . ',IDGrupo',
            'Descripcion' => 'nullable|string',
            'Privacidad' => 'nullable|string|in:Publico,Privado',
            'Foto' => 'nullable|file|image|max:4048', // Validación para la foto (opcional)
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "validation errors.",
                "Message" => $validator->errors()->all()
            ], 422);
        }

        try {
            // Actualiza los campos del grupo si están presentes en la petición
            if ($request->filled('Nombre')) {
                $grupo->Nombre = $request->input('Nombre');
            }
            if ($request->filled('Descripcion')) {
                $grupo->Descripcion = $request->input('Descripcion');
            }
            if ($request->filled('Privacidad')) {
                $grupo->Privacidad = $request->input('Privacidad');
            }

            // Manejo de la foto
            if ($request->hasFile('Foto')) {

                $user = User::findOrFail($userId);
                // Determina la carpeta base según el tipo de perfil
                $carpetaBase = '';
                if ($user->rol === 'empresa') {
                    $carpetaBase = 'Empresa';
                } elseif ($user->rol === 'usuario') {
                    $carpetaBase = 'Desempleado';
                }
                $foto = $request->file('Foto');
                $nombreFotoUnico = Str::uuid() . '.' . $foto->getClientOriginalExtension();
                $rutaAlmacenamiento = "{$carpetaBase}/{$userId}/Grupos"; // Misma carpeta que en el store
                $rutaFotoNueva = $foto->storeAs($rutaAlmacenamiento, $nombreFotoUnico, 'public');

                if (!$rutaFotoNueva) {
                    return response()->json([
                        "StatusCode" => 500,
                        "ReasonPhrase" => "Error al guardar el nuevo archivo de foto.",
                        "Message" => "No se pudo guardar la nueva foto del grupo en el sistema."
                    ], 500);
                }

                // Elimina la foto anterior si existe
                // Si la carga de la nueva foto fue exitosa, intentamos eliminar la anterior
                if ($grupo->Foto) {
                    $rutaFotoAnterior = str_replace(Storage::url(''), '', $grupo->Foto);
                    if (Storage::disk('public')->exists($rutaFotoAnterior)) {
                        Storage::disk('public')->delete($rutaFotoAnterior);
                    }
                }
                $grupo->Foto = Storage::url($rutaFotoNueva);
            }

            $grupo->save();

            return response()->json([
                'StatusCode' => 200,
                'ReasonPhrase' => 'Grupo actualizado correctamente.',
                'Message' => 'La información del grupo ha sido actualizada con éxito.',
                'Data' => $grupo->fresh()->load('users') // Carga los miembros actualizados del grupo
            ], 200);

        }catch (QueryException $e) {


            // Manejar otros errores de base de datos
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al registrar el usuario.?"."\n".$e->getMessage(),
                'SQL error: ' . $e->getMessage(),
                'SQL query: ' . $e->getSql(),
                'Bindings: ', $e->getBindings()

            ], 500); // 500 (Internal Server Error) para otros errores
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Grupo $grupo)
    {
        //
        $userId = auth()->id(); // Obtén el ID del usuario autenticado

        // Verifica si el usuario autenticado es el propietario del grupo
        if ($grupo->Propietario !== $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para eliminar este grupo."
            ], 403); // 403 (Forbidden)
        }

        try {
            // Elimina la foto del grupo si existe
            if ($grupo->Foto) {
                $rutaFoto = str_replace(Storage::url(''), '', $grupo->Foto);
                if (Storage::disk('public')->exists($rutaFoto)) {
                    Storage::disk('public')->delete($rutaFoto);
                }
            }

            // Elimina el grupo de la base de datos
            $grupo->delete();

            return response()->json([
                "StatusCode" => 200,
                "ReasonPhrase" => "Grupo eliminado correctamente.",
                "Message" => "El grupo ha sido eliminado con éxito."
            ], 200); // 200 (OK)

        } catch (\Exception $e) {
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al intentar eliminar el grupo."
            ], 500); // 500 (Internal Server Error)
        }
    }

    public function join(Request $request, $idGrupo)
    {
        $userId = auth()->id(); // Obtén el ID del usuario autenticado

        try {
            $grupo = Grupo::findOrFail($idGrupo);
            $user = User::findOrFail($userId);

            // 1. Verifica si ya hay un registro (unido, pendiente, rechazado)
            $existingMembership = $grupo->users()->where('users.IDUsuario', $userId)
             ->withPivot('EstadoMiembro')
            ->first();

            if ($existingMembership) {
                Log::info("Existe");
                $currentState = $existingMembership->pivot->EstadoMiembro;
                Log::info($currentState);
                if ($currentState == 'Unido') {
                                    Log::info("Unido");
                    return response()->json([
                        "StatusCode" => 409,
                        "ReasonPhrase" => "Conflicto.",
                        "Message" => "El usuario ya es miembro de este grupo."
                    ], 409);
                } elseif ($currentState == 'Pendiente') {
                                    Log::info("Pendiente");
                    return response()->json([
                        "StatusCode" => 409,
                        "ReasonPhrase" => "Conflicto.",
                        "Message" => "El usuario ya tiene una solicitud pendiente para este grupo."
                    ], 409);
                } elseif ($currentState == 'Rechazado') {

                    // Si el grupo es público, lo uniremos directamente.
                    if ($grupo->Privacidad == 'Privado') {
                        $grupo->users()->updateExistingPivot($user->IDUsuario, ['EstadoMiembro' => 'Pendiente']);
                         event(new UsuarioSeUnioAGrupoPrivado($grupo, $user)); // Disparar evento para unido
                        return response()->json([
                            'StatusCode' => 200,
                            'ReasonPhrase' => 'Solicitud enviada.',
                            'Message' => 'Su solicitud para unirse al grupo privado ha sido enviada nuevamente.',
                            'data' => [
                                'grupo' => $grupo->fresh()->load('users'),
                                'estado_miembro' => 'pendiente'
                            ]
                        ], 200);
                    } else {

                        // Grupo público, actualiza a unido
                        $grupo->users()->updateExistingPivot($user->IDUsuario, ['EstadoMiembro' => 'Unido']);
                        event(new UsuarioSeUnioAGrupo($grupo, $user)); // Disparar evento para unido
                        return response()->json([
                            'StatusCode' => 200,
                            'ReasonPhrase' => 'Usuario unido al grupo correctamente.',
                            'Message' => 'El usuario se ha unido al grupo exitosamente.',
                            'data' => [
                                'grupo' => $grupo->fresh()->load('users'),
                                'estado_miembro' => 'unido'
                            ]
                        ], 200);
                    }
                }
            }

            // 2. Lógica para unirse (o solicitar unirse) a un grupo
            if ($grupo->Privacidad === 'Publico') {
                // Si el grupo es público, el usuario se une directamente
                $grupo->users()->attach($user->IDUsuario, ['EstadoMiembro' => 'Unido']);
                event(new UsuarioSeUnioAGrupo($grupo, $user));

                return response()->json([
                    'StatusCode' => 200,
                    'ReasonPhrase' => 'Usuario unido al grupo correctamente.',
                    'Message' => 'El usuario se ha unido al grupo exitosamente.',
                    'data' => [
                        'grupo' => $grupo->fresh()->load('users'),
                        'estado_miembro' => 'Unido'
                    ]
                ], 200);

            } elseif ($grupo->Privacidad === 'Privado') {
                                Log::info("Se la sudo");
                // Si el grupo es privado, el usuario envía una solicitud (estado 'pendiente')
                $grupo->users()->attach($user->IDUsuario, ['EstadoMiembro' => 'Pendiente']);

                // Enviar una notificación al creador/administradores del grupo
                event(new UsuarioSeUnioAGrupoPrivado($grupo, $user)); // Disparar evento para unido



                return response()->json([
                    'StatusCode' => 200,
                    'ReasonPhrase' => 'Solicitud enviada.',
                    'Message' => 'Su solicitud para unirse al grupo privado ha sido enviada. Esperando aprobación.',
                    'data' => [
                        'grupo' => $grupo->fresh()->load('users'),
                        'estado_miembro' => 'pendiente'
                    ]
                ], 200);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "StatusCode" => 404,
                "ReasonPhrase" => "No encontrado.",
                "Message" => "El grupo con el ID proporcionado no existe."
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al intentar unir al usuario al grupo.".$e->getMessage()
            ], 500);
        }
    }

    public function leave(Request $request, $idGrupo)
    {
        $userId = auth()->id(); // Obtén el ID del usuario autenticado

        try {
            $grupo = Grupo::findOrFail($idGrupo);

            // Verifica si el usuario es miembro del grupo
            if (!$grupo->users()->where('users.IDUsuario', $userId)->exists()) {
                return response()->json([
                    "StatusCode" => 404,
                    "ReasonPhrase" => "No encontrado.",
                    "Message" => "El usuario no es miembro de este grupo."
                ], 404);
            }
            // 2. ELIMINA LAS PUBLICACIONES del usuario en este grupo
            // Primero, obtenemos las publicaciones que el usuario hizo en este grupo
            $publicacionesAEliminar = Publicacion::where('IDUsuario', $userId)
                                                ->where('IDGrupo', $idGrupo)
                                                ->get();

            foreach ($publicacionesAEliminar as $publicacion) {
               // Eliminar el archivo del sistema de archivos
                if ($publicacion->Archivo) {
                    $rutaArchivo = str_replace(Storage::url(''), '', $publicacion->Archivo);
                    if (Storage::disk('public')->exists($rutaArchivo)) {
                        Storage::disk('public')->delete($rutaArchivo);
                    }
                }

                // Luego eliminamos la publicación de la base de datos
                $publicacion->delete();
            }

            // Remueve al usuario del grupo
            $grupo->users()->detach($userId);

            return response()->json([
                'StatusCode' => 200,
                'ReasonPhrase' => 'Usuario abandonó el grupo correctamente.',
                'Message' => 'El usuario ha sido removido del grupo exitosamente.',
                'data' => $grupo->fresh()->load('users') // Carga los miembros actualizados del grupo
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "StatusCode" => 404,
                "ReasonPhrase" => "No encontrado.",
                "Message" => "El grupo con el ID proporcionado no existe."
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al intentar remover al usuario del grupo."
            ], 500);
        }
    }

    public function posts(Grupo $grupo)
    {
        // Carga las publicaciones del grupo con sus relaciones necesarias
        $publicaciones = $grupo->publicaciones()->with(['user', 'documentos', 'comentarios.user'])
        ->paginate(10);

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Publicaciones del grupo listadas correctamente.',
            'data' => $publicaciones
        ], 200);
    }

    public function gruposByIDUsuario()
    {
        try {

            // Obtén el ID del usuario autenticado por Sanctum
            $userId = auth()->id();
            // Encuentra al usuario por su ID
            $user = User::find($userId);

            // Si el usuario no existe, devuelve un error 404
            if (!$user) {
                return response()->json([
                    "StatusCode" => 404,
                    "ReasonPhrase" => "Usuario no encontrado.",
                    "Message" => "El usuario con ID {$userId} no existe."
                ], 404);
            }

            // Obtén los grupos a los que pertenece el usuario
            // Carga también las relaciones de los grupos si es necesario (propietario, publicaciones, etc.)
            $grupos = $user->grupos()->with(['propietario', 'publicaciones', 'users'])
             ->orderBy('created_at', 'desc')
            ->get();

            return response()->json([
                'StatusCode' => 200,
                'ReasonPhrase' => 'Grupos obtenidos correctamente.',
                'Message' => "Listado de grupos a los que pertenece el usuario con ID {$userId}.",
                'Data' => $grupos
            ], 200);

        } catch (\Exception $e) {
            // Manejar cualquier excepción inesperada
            Log::error("WTF" . $e->getMessage());
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al obtener los grupos: " . $e->getMessage()
            ], 500);
        }
    }

    // --- Nuevas funciones para grupos privados ---

    /**
     * Obtener solicitudes pendientes para un grupo específico (solo administradores/creador del grupo).
     */
    public function getPendingRequests($idGrupo)
    {
        $userId = auth()->id();
        $grupo = Grupo::findOrFail($idGrupo);

        Log::info("IDUsuario " . $grupo->Propietario);
        Log::info("IDUsuario Grupo ". $userId);
        // Verifica si el usuario actual es el creador del grupo (o tiene rol de admin)
        if ($grupo->Propietario !== $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Prohibido.",
                "Message" => "No tiene permiso para ver las solicitudes de este grupo."
            ], 403);
        }

        $pendingUsers = $grupo
        ->users()
        ->wherePivot('EstadoMiembro', 'Pendiente')
        ->withPivot('EstadoMiembro', 'created_at')
        ->with(['desempleado', 'empresa']) // Carga perfiles para mostrar nombre y foto
        ->get();

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'OK.',
            'Message' => 'Solicitudes pendientes obtenidas correctamente.',
            'Data' => $pendingUsers->map(function ($user) {
                return [
                    'IDUsuario' => $user->IDUsuario,
                    'email' => $user->email,
                    'nombre' => $user->desempleado->Nombre ?? $user->empresa->NombreEmpresa ?? 'Usuario',
                    'foto' => $user->desempleado->Foto ?? $user->empresa->Foto ?? 'assets/default.png',
                    'estado_miembro' => $user->pivot->EstadoMiembro,
                    'solicitado_en' => $user->pivot->created_at,
                ];
            }),
        ], 200);
    }

    /**
     * Aceptar o rechazar una solicitud de unión a un grupo privado.
     * @param int $idGrupo
     * @param int $solicitudUserId
     * @param Request $request con 'accion' ('aceptar' o 'rechazar')
     */
    public function handleJoinRequest(Request $request, $idGrupo, $solicitudUserId)
    {
        $adminId = auth()->id();
        $grupo = Grupo::findOrFail($idGrupo);

        // Verifica si el usuario actual es el creador del grupo (o tiene rol de admin)
        if ($grupo->Propietario !== $adminId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Prohibido.",
                "Message" => "No tiene permiso para gestionar las solicitudes de este grupo."
            ], 403);
        }

        $accion = $request->input('accion'); // 'aceptar' o 'rechazar'

        if (!in_array($accion, ['aceptar', 'rechazar'])) {
            return response()->json([
                "StatusCode" => 400,
                "ReasonPhrase" => "Solicitud incorrecta.",
                "Message" => "La acción debe ser 'aceptar' o 'rechazar'."
            ], 400);
        }

        // Asegúrate de que el user model se cargue correctamente.
        $solicitanteUser = User::find($solicitudUserId);

        $membership = $grupo->users()
                            ->where('users.IDUsuario', $solicitudUserId)
                            ->wherePivot('EstadoMiembro', 'Pendiente')
                            ->first();

        if (!$membership || $membership->pivot->EstadoMiembro !== 'Pendiente') {
            return response()->json([
                "StatusCode" => 404,
                "ReasonPhrase" => "No encontrado.",
                "Message" => "Solicitud pendiente no encontrada para este usuario en este grupo."
            ], 404);
        }

        if ($accion === 'aceptar') {
            $grupo->users()->updateExistingPivot($solicitudUserId, ['EstadoMiembro' => 'Unido']);
            //Notificar al usuario que su solicitud fue aceptada
            if ($solicitanteUser) {
                event(new UsuarioAceptoSolicitud($grupo, $solicitanteUser));
            }

            $message = 'Solicitud de unión aceptada. El usuario es ahora miembro.';
            $newStatus = 'unido';
        } else { // 'rechazar'
            $grupo->users()->updateExistingPivot($solicitudUserId, ['EstadoMiembro' => 'Rechazado']); // O podrías eliminar el registro
            // $grupo->users()->detach($solicitudUserId); // Eliminar el registro al rechazar
            // Notificar al usuario que su solicitud fue rechazada

            $message = 'Solicitud de unión rechazada.';
            $newStatus = 'rechazado';
        }

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'OK.',
            'Message' => $message,
            'Data' => [
                'IDUsuario' => $solicitudUserId,
                'IDGrupo' => $idGrupo,
                'EstadoMiembro' => $newStatus
            ]
        ], 200);
    }




}
