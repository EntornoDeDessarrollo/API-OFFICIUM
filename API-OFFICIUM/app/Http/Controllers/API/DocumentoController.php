<?php

namespace App\Http\Controllers\API;

use App\Models\Documento;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;


class DocumentoController extends Controller
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

        // Verifica si el ID del usuario autenticado coincide con el IDUsuario de la empresa
        if ($request->IDUsuario != $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para modificar esta empresa. "."UsuarioID Token :".$userId." UsuarioID Fomr :".$request->IDUsuario
            ], 403); // 403 (Forbidden) si no coincide
        }

        $validator = Validator::make($request->all(), [
            'IDUsuario' => 'required|exists:users,IDUsuario',
            'IDPublicacion' => 'nullable|exists:publicaciones,IDPublicacion', // Opcional, dependiendo de tu lógica
            'Tipo' => 'required|string|in:Foto,Video,PDF,otro', // Define los tipos permitidos
            'Archivo' => 'required|file|max:20480', // Ajusta el tamaño máximo según necesites (en KB)
        ]);

        if ($validator->fails()) {
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "validation errors.",
                "Message" => $validator->errors()->all()
            ], 422);
        }

        try {



            $user = User::findOrFail($request->input('IDUsuario'));
            $tipoDocumento = $request->input('Tipo');
            $archivo = $request->file('Archivo');
            $nombreArchivoOriginal = $archivo->getClientOriginalName();
            $extension = $archivo->getClientOriginalExtension();
            $nombreArchivoUnico = Str::uuid() . '.' . $extension;
            $fechaSubida = now();

            // Determina la carpeta base según el tipo de perfil
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

            // Define la ruta de almacenamiento
            $rutaAlmacenamiento = "{$carpetaBase}/{$userId}/{$tipoDocumento}";

            // Guarda el archivo
            $rutaArchivo = $archivo->storeAs($rutaAlmacenamiento, $nombreArchivoUnico, 'public');

            if (!$rutaArchivo) {
                return response()->json([
                    "StatusCode" => 500,
                    "ReasonPhrase" => "Error al guardar el archivo.",
                    "Message" => "No se pudo guardar el archivo en el sistema."
                ], 500);
            }

            // Crea el registro en la base de datos
            $documento = new Documento();
            $documento->IDUsuario = $user->IDUsuario;
            $documento->IDPublicacion = $request->input('IDPublicacion');
            $documento->Tipo = $tipoDocumento;
            $documento->NombreArchivo = $nombreArchivoOriginal;
            $documento->URL = Storage::url($rutaArchivo); // Genera la URL pública del archivo
            $documento->FechaSubida = $fechaSubida;
            $documento->save();

            return response()->json([
                'StatusCode' => 201,
                'ReasonPhrase' => 'Documento subido correctamente.',
                'Message' => 'El documento se ha subido y guardado con éxito.',
                'data' => $documento
            ], 201); // 201 Created
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                "StatusCode" => 404,
                "ReasonPhrase" => "No encontrado.",
                "Message" => "El usuario con el ID proporcionado no existe."
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al intentar subir y guardar el documento." . "\n" . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Documento $documento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Documento $documento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Documento $documento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Documento $documento)
    {
        //
        $userId = auth()->id();

    // Verifica si el usuario autenticado es el propietario del documento
    if ($documento->IDUsuario !== $userId) {
        return response()->json([
            "StatusCode" => 403,
            "ReasonPhrase" => "Acceso no autorizado.",
            "Message" => "No tienes permiso para eliminar este documento."
        ], 403); // 403 (Forbidden)
    }

    try {
        // Eliminar el archivo del sistema de archivos
        if ($documento->URL) {
            $rutaArchivo = str_replace(Storage::url(''), '', $documento->URL);
            if (Storage::disk('public')->exists($rutaArchivo)) {
                Storage::disk('public')->delete($rutaArchivo);
            }
        }

        // Eliminar el registro de la base de datos
        $documento->delete();

        return response()->json([
            "StatusCode" => 200,
            "ReasonPhrase" => "Documento eliminado correctamente.",
            "Message" => "El documento ha sido eliminado con éxito."
        ], 200); // 200 (OK)

    } catch (\Exception $e) {
        Log::error("Error al eliminar el documento: " . $e->getMessage());
        return response()->json([
            "StatusCode" => 500,
            "ReasonPhrase" => "Error interno del servidor.",
            "Message" => "Ocurrió un error al intentar eliminar el documento."
        ], 500); // 500 (Internal Server Error)
    }
    }
}
