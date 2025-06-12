<?php

namespace App\Http\Controllers\API;

use App\Models\Empresa;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class EmpresaController extends Controller
{

    // public function __construct()
    // {
    //     //$this->middleware('auth')->except(['index', 'show']);
    //     $this->middleware('rol:empresa')->only(['show', 'update', 'destroy']);
    // }
    public function test(){
        return response()->json([
            "StatusCode" => 200,
            "ReasonPhrase" => "Success",
            "Message" => "Test confirmado."
        ], 200);
    }

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
        $imagePath = null; // Variable imagen por si falla.
        //
        $validator = Validator::make($request->all(), [
            'IDUsuario' => 'required',
            'NombreEmpresa' => 'required|string',
            'CIF' => 'required|string',
            'IDSector' => 'required|integer',
            'Ubicacion' => 'required|string',
            'SitioWeb' => 'nullable|string',
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
            $empresa = new Empresa();
             // Asocia el usuario directamente con el modelo
            $empresa->user()->associate(User::findOrFail($request->input('IDUsuario')));
            $empresa->NombreEmpresa = $request->input('NombreEmpresa');
            $empresa->CIF = $request->input('CIF');
            $empresa->IDSector = $request->input('IDSector');
            $empresa->Ubicacion = $request->input('Ubicacion');
            $empresa->SitioWeb = $request->input('SitioWeb');

            // Guardar imagen

            if ($request->hasFile('Foto')) {
                $userId = $request->input('IDUsuario');
                $image = $request->file('Foto');

                $folder = "Empresa/{$userId}/FotoPerfil";
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();

                // Guarda en: storage/app/public/Empresa/{id}/FotoPerfil/xxx.jpg
                $path = $image->storeAs($folder, $filename, 'public');

                // Guarda la ruta pública
                $empresa->Foto = 'storage/' . $path;

                // Guarda la ruta real en caso de error luego
                $imagePath = storage_path("app/public/{$path}");
            }

            //$emresa->create();
            $empresa->save();
            // Buscar el usuario relacionado
            $usuario = User::find($empresa->IDUsuario);

            //$empresa->user->rol = 'empresa';
            $usuario->rol = 'empresa';
            $usuario->save();

            return response()->json([
                'StatusCode' => 200,
                'ReasonPhrase' => 'Empresa creada correctamente',
                'Message' => 'La empresa ha sido registrada con éxito.',
                'data' => $empresa
            ]);
        } catch (QueryException $e) {

            // Eliminar imagen si fue subida pero ocurre error
            if ($imagePath && file_exists($imagePath)) {
                unlink($imagePath);
            }

            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error en la base de datos.",
                "Message" => "Ha ocurrido un problema al realizar la consulta en la base de datos."."\n".
                $e->getMessage()."\n".$e->getSql()."\n".$e->getFile()."\n".$e->getLine(),
                "Exception" => $e->getMessage(),
                "Sql" => $e->getSql(),
                "Bindings" => $e->getBindings(),
                "File" => $e->getFile(),
                "Line" => $e->getLine()
            ], 500);

        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Empresa $empresa)
    {
        //

        $IDUsuario = auth()->id();

        // Verifica si el ID del usuario autenticado coincide con el IDUsuario de la empresa
        if ($empresa->IDUsuario != $IDUsuario) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para acceder a esta empresa. "."UsuarioID Token :".$IDUsuario." Empresa :".$empresa
            ], 403); // 403 (Forbidden) si no coincide
        }


        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Empresa encontrada correctamente',
            'Message' => 'La información de la empresa ha sido encontrada con éxito.',
            'Data' => $empresa,
            'Usuario Token' => $IDUsuario,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Empresa $empresa)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empresa $empresa)
    {
        //
        $userId = auth()->id();

        // Verifica si el usuario autenticado
        if ($empresa->IDUsuario !== $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para modificar empresa."
            ], 403); // 403 (Forbidden)
        }

        $validator = Validator::make($request->all(), [
            'NombreEmpresa' => 'nullable|string',
            'CIF' => 'nullable|string',
            'IDSector' => 'nullable|integer',
            'Ubicacion' => 'nullable|string',
            'SitioWeb' => 'nullable|string',
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
            if ($empresa->IDUsuario !== $userId) {
                return response()->json([
                    "StatusCode" => 403,
                    "ReasonPhrase" => "Acceso no autorizado.",
                    "Message" => "No tienes permiso para modificar esta empresa. "."UsuarioID Token :".
                    $userId." UsuarioID Fomr :".$request->IDUsuario." ID : ".$id." Empresa :".$empresa
                ], 403); // 403 (Forbidden) si no coincide
            }

            // Actualiza los campos proporcionados en la petición
            if ($request->filled('NombreEmpresa')) {
                $empresa->NombreEmpresa = $request->input('NombreEmpresa', $empresa->NombreEmpresa);
            }
            if ($request->filled('CIF')) {
                $empresa->CIF = $request->input('CIF', $empresa->CIF);
            }
            if ($request->filled('IDSector')) {
                $empresa->IDSector = $request->input('IDSector', $empresa->IDSector);
            }
            if ($request->filled('Ubicacion')) {
                $empresa->Ubicacion = $request->input('Ubicacion', $empresa->Ubicacion);
            }
            if ($request->filled('SitioWeb')) {
                $empresa->SitioWeb = $request->input('SitioWeb', $empresa->SitioWeb);
            }
            //$empresa->IDUsuario = $request->input('IDUsuario'); // Asegúrate de permitir la actualización si es necesario

            // Actualizar la imagen si se proporciona una nueva
            if ($request->hasFile('Foto')) {
                Log::info("Se va a modificar imagen");
                $image = $request->file('Foto');
                $folder = "Empresa/{$userId}/FotoPerfil"; // Usa el ID del usuario autenticado en la ruta
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                $path = $image->storeAs($folder, $filename, 'public');
                $newImagePath = 'storage/' . $path;
                $imagePath = storage_path("app/public/{$path}"); // Ruta real de la nueva imagen

                // Eliminar la imagen anterior si existe
                if ($empresa->Foto && Storage::disk('public')->exists(str_replace('storage/', '', $empresa->Foto))) {
                    Storage::disk('public')->delete(str_replace('storage/', '', $empresa->Foto));
                }

                // Guarda la nueva ruta de la imagen
                $empresa->Foto = $newImagePath;
            }

            $empresa->save();

            $response["profile"] = $empresa;

            return response()->json([
                'StatusCode' => 200,
                'ReasonPhrase' => 'Empresa actualizada correctamente',
                'Message' => 'La información de la empresa ha sido actualizada con éxito.',
                'Data' => $response
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
    public function destroy(Empresa $empresa)
    {
        //
        $userId = auth()->id();

        // Verifica si el usuario autenticado es el propietario de la empresa
        if ($empresa->IDUsuario !== $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para eliminar esta empresa."
            ], 403); // 403 (Forbidden)
        }

        try {

            $carpetaUsuario = "Empresa/{$empresa->IDUsuario}";
            // Eliminar la foto de perfil si existe
            if ($empresa->Foto && Storage::disk('public')->exists($carpetaUsuario)) {
                //Storage::disk('public')->delete(str_replace('storage/', '', $empresa->Foto));
                Storage::disk('public')->deleteDirectory($carpetaUsuario);
            }

            $empresa->delete();

            return response()->json([
                "StatusCode" => 200,
                "ReasonPhrase" => "Empresa eliminada correctamente.",
                "Message" => "La empresa ha sido eliminada con éxito."
            ], 200); // 200 (OK)

        } catch (Exception $e) {
            Log::error("Error al eliminar la empresa: " . $e->getMessage());
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al intentar eliminar la empresa."
            ], 500); // 500 (Internal Server Error)
        }
    }
}
