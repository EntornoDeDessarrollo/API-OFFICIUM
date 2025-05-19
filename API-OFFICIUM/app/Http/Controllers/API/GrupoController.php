<?php

namespace App\Http\Controllers\API;

use App\Models\Grupo;
use App\Models\User;
use App\Events\UsuarioSeUnioAGrupo;
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
            'data' => $grupos
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
            $grupo->users()->attach($user);

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
        // Carga la relación 'users' (los miembros del grupo)
        $grupo->load('users');

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Grupo encontrado correctamente',
            'Message' => 'La información del grupo ha sido encontrada con éxito.',
            'Data' => $grupo,

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
            'Foto' => 'nullable|file|image|max:2048', // Validación para la foto (opcional)
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

            // Verifica si el usuario ya es miembro del grupo
            if ($grupo->users()->where('users.IDUsuario', $userId)->exists()) {
                return response()->json([
                    "StatusCode" => 409,
                    "ReasonPhrase" => "Conflicto.",
                    "Message" => "El usuario ya es miembro de este grupo."
                ], 409);
            }

            // Añade al usuario al grupo
            $grupo->users()->attach($user);

            // Disparar el evento UsuarioSeUnioAGrupo
            event(new UsuarioSeUnioAGrupo($grupo, $user));

            return response()->json([
                'StatusCode' => 200,
                'ReasonPhrase' => 'Usuario unido al grupo correctamente.',
                'Message' => 'El usuario se ha unido al grupo exitosamente.',
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




}
