<?php

namespace App\Http\Controllers\API;

use App\Models\Desempleado;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class DesempleadoController extends Controller
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
        $imagePath = null; // Variable imagen por si falla.
        //
        $validator = Validator::make($request->all(), [
            'IDUsuario' => 'required',
            'Nombre' => 'required|string',
            'Apellido' => 'required|string',
            'DNI' => 'required|string',
            'Porfolios' => 'required|string',
            'Disponibilidad' => 'required|string',
            'Foto' => 'nullable|file|image' // validación de imagen
        ]);


        if($validator->fails()){
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "validation errors.",
                "Message" => $validator->errors()->all()
            ],422);// 422 (Unprocessable Entity) para errores de validación
        }

        try {
            $desempleado = new Desempleado();
            $desempleado->IDUsuario = $request->input('IDUsuario');
            $desempleado->Nombre = $request->input('Nombre');
            $desempleado->Apellido = $request->input('Apellido');
            $desempleado->DNI = $request->input('DNI');
            $desempleado->Porfolios = $request->input('Porfolios');
            $desempleado->Disponibilidad = $request->input('Disponibilidad');

            // Guardar imagen


            if ($request->hasFile('Foto')) {
                $userId = $request->input('IDUsuario');
                $image = $request->file('Foto');

                $folder = "Desempleado/{$userId}/FotoPerfil";
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();

                // Guarda en: storage/app/public/Empresa/{id}/FotoPerfil/xxx.jpg
                $path = $image->storeAs($folder, $filename, 'public');

                // Guarda la ruta pública
                $desempleado->Foto = 'storage/' . $path;

                // Guarda la ruta real en caso de error luego
                $imagePath = storage_path("app/public/{$path}");
            }

            $desempleado->save();

            return response()->json([
                'StatusCode' => 200,
                'ReasonPhrase' => 'Desempleado creado correctamente',
                'Message' => 'El desempleado ha sido registrado con éxito.',
                'Data' => $desempleado
            ]);
        } catch (QueryException $e) {

            // Eliminar imagen si fue subida pero ocurre error
            if ($imagePath && file_exists($imagePath)) {
                unlink($imagePath);
            }


            // if ($e->getCode() === '23000') { // Código de error para duplicado en MySQL
            //     return response()->json([
            //         "StatusCode" => 409,
            //         "ReasonPhrase" => "Desempleado ya registrado.",
            //         "Message" => "Desempleado duplicado."
            //     ], 409);// 409 (Conflict) para duplicado
            // }

            // Manejar otros errores de base de datos si es necesario
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
    public function show(Desempleado $desempleado)
    {
        //
        $IDUsuario = auth()->id();

        // Verifica si el ID del usuario autenticado coincide con el IDUsuario de la empresa
        if ($desempleado->IDUsuario != $IDUsuario) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para acceder este desempleado. "."UsuarioID Token :".$IDUsuario." ID : "." Desempleado :".$desempleado
            ], 403); // 403 (Forbidden) si no coincide
        }


        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Desempleado encontrado correctamente',
            'Message' => 'La información del desempleado ha sido encontrada con éxito.',
            'Data' => $desempleado,
            'Usuario Token' => $IDUsuario,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Desempleado $desempleado)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Desempleado $desempleado)
    {
        //
        Log::info(" Desempleado : ".$desempleado);
        $imagePath = null; // Variable para la ruta de la imagen por si falla la actualización.

        $validator = Validator::make($request->all(), [
            'IDUsuario' => 'required',
            'Nombre' => 'nullable|string',
            'Apellido' => 'nullable|string',
            'DNI' => 'nullable|string',
            'Portafolios' => 'nullable|string',
            'Disponibilidad' => 'nullable|string',
            'Foto' => 'nullable|file|image' // Validación de imagen
        ]);

        if ($validator->fails()) {
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "validation errors.",
                "Message" => $validator->errors()->all()
            ], 422); // 422 (Unprocessable Entity) para errores de validación
        }

        try {
            // Obtén el ID del usuario autenticado por Sanctum
            $userId = auth()->id();
            //$empresa = Empresa::find($id);



            // Verifica si el ID del usuario autenticado coincide con el IDUsuario de la empresa
            if ($desempleado->IDUsuario !== $userId) {
                return response()->json([
                    "StatusCode" => 403,
                    "ReasonPhrase" => "Acceso no autorizado.",
                    "Message" => "No tienes permiso para modificar esta empresa. "."UsuarioID Token :".$userId." UsuarioID Fomr :".$request->IDUsuario." ID : ".$id." Empresa :".$empresa
                ], 403); // 403 (Forbidden) si no coincide
            }

            // Actualiza los campos proporcionados en la petición
            $desempleado->Nombre = $request->input('Nombre', $desempleado->Nombre);
            $desempleado->Apellido = $request->input('Apellido', $desempleado->Apellido);
            $desempleado->DNI = $request->input('DNI', $desempleado->DNI);
            $desempleado->Porfolios = $request->input('Portafolios', $desempleado->Portafolios);
            $desempleado->Disponibilidad = $request->input('Disponibilidad', $desempleado->Disponibilidad);
            $desempleado->Foto = $request->input('Foto', $desempleado->Foto);
            //$desempleado->IDUsuario = $request->input('IDUsuario'); // Asegúrate de permitir la actualización si es necesario

            // Actualizar la imagen si se proporciona una nueva
            if ($request->hasFile('Foto')) {
                $image = $request->file('Foto');
                $folder = "Desempleado/{$userId}/FotoPerfil"; // Usa el ID del usuario autenticado en la ruta
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs($folder, $filename, 'public');
                $newImagePath = 'storage/' . $path;
                $imagePath = storage_path("app/public/{$path}"); // Ruta real de la nueva imagen

                // Eliminar la imagen anterior si existe
                if ($desempleado->Foto && Storage::disk('public')->exists(str_replace('storage/', '', $desempleado->Foto))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $desempleado->Foto));
                }

                // Guarda la nueva ruta de la imagen
                $desempleado->Foto = $newImagePath;
            }

            $desempleado->save();

            return response()->json([
                'StatusCode' => 200,
                'ReasonPhrase' => 'Empresa actualizada correctamente',
                'Message' => 'La información de la empresa ha sido actualizada con éxito.',
                'data' => $desempleado
            ]);

        } catch (QueryException $e) {
            // Eliminar la nueva imagen si fue subida pero ocurre un error en la base de datos
            if ($imagePath && file_exists($imagePath)) {
                unlink($imagePath);
            }
            Log::error("message\n".$e);

            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error en la base de datos.",
                "Message" => "Ha ocurrido un problema al realizar la consulta en la base de datos.",
                "Exception" => $e->getMessage(),
                "Sql" => $e->getSql(),
                "Bindings" => $e->getBindings(),
                "File" => $e->getFile(),
                "Line" => $e->getLine()
            ], 500);

        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Desempleado $desempleado)
    {
        //
        $userId = auth()->id();

        // Verifica si el usuario autenticado es el propietario de la empresa
        if ($desempleado->IDUsuario !== $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para eliminar este desempleado.". " Desempleado".$desempleado->IDUsuario." IDToken: ".$userId
            ], 403); // 403 (Forbidden)
        }

        try {
            $carpetaUsuario = "Desempleado/{$desempleado->IDUsuario}";
            // Eliminar la foto de perfil si existe
            if ($desempleado->Foto && Storage::disk('public')->exists($carpetaUsuario)) {
                //Storage::disk('public')->delete(str_replace('storage/', '', $desempleado->Foto));
                Storage::disk('public')->deleteDirectory($carpetaUsuario);
            }

            $desempleado->delete();

            return response()->json([
                "StatusCode" => 200,
                "ReasonPhrase" => "Desempleado eliminado correctamente.",
                "Message" => "EL desempleado ha sido eliminado con éxito."
            ], 200); // 200 (OK)

        } catch (Exception $e) {
            Log::error("Error al eliminar el desempleado: " . $e->getMessage());
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al intentar eliminar el desempleado."
            ], 500); // 500 (Internal Server Error)
        }
    }
}
