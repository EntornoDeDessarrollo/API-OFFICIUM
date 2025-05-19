<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Aplicacion;
use App\Models\User;
use App\Models\Desempleado;
use App\Models\OfertaEmpleo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;

use App\Events\EstadoAplicacion;
use App\Events\OfertaAplicada;


class AplicacionController extends Controller
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
        // Obtén el ID del usuario autenticado
        $userId = auth()->id();

        // Busca el desempleado asociado al usuario autenticado
        $desempleado = Desempleado::where('IDUsuario', $userId)->first();

        if (!$desempleado) {
            return response()->json([
                "StatusCode" => 404,
                "ReasonPhrase" => "No encontrado.",
                "Message" => "No se encontró información de desempleado para el usuario autenticado.".$userId
            ], 404);
        }

        // Valida los datos de la petición
        $validator = Validator::make($request->all(), [
            'IDOferta' => 'required|exists:oferta_empleos,IDOferta',
            'Estado' => 'nullable|string|in:Abierta,Pendiente,Rechazada',

        ]);

        if ($validator->fails()) {
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "Errores de validación.",
                "Message" => $validator->errors()->all()
            ], 422);
        }

        try {
            // Verifica si el desempleado ya ha aplicado a esta oferta
            $existingApplication = Aplicacion::where('IDDesempleado', $desempleado->IDDesempleado)
                ->where('IDOferta', $request->input('IDOferta'))
                ->first();

            if ($existingApplication) {
                return response()->json([
                    "StatusCode" => 409, // Conflict
                    "ReasonPhrase" => "Conflicto.",
                    "Message" => "Ya has aplicado a esta oferta de empleo."
                ], 409);
            }

            // Crea la nueva aplicación
            $aplicacion = new Aplicacion();
            $aplicacion->IDDesempleado = $desempleado->IDDesempleado;
            $aplicacion->IDOferta = $request->input('IDOferta');
            $aplicacion->Estado = 'Pendiente'; // Establece 'Pendiente' por defecto
            $aplicacion->FechaAplicacion = now();
            $aplicacion->save();

            // Carga las relaciones para la respuesta
            $aplicacion->load('desempleado', 'oferta');

            // Disparar el evento OfertaAplicada
            event(new OfertaAplicada($aplicacion));

            return response()->json([
                "StatusCode" => 201, // Created
                "ReasonPhrase" => "Creado.",
                "Message" => "Tu aplicación se ha registrado correctamente.",
                "data" => $aplicacion
            ], 201);

        } catch (Exception $e) {
            Log::error("Error al registrar la aplicación: " . $e->getMessage());
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al intentar registrar tu aplicación."
            ], 500);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(Aplicacion $aplicacion)
    {
        //
        // Carga las relaciones necesarias para mostrar los detalles de la aplicación
        //Se puede acceder directamente a las funciones de los dos modelos correspondientes
        $loggedInUserId = auth()->id();
        $desempleadoId = $aplicacion->IDDesempleado;
        $empresaIdDeOferta = $aplicacion->oferta->IDEmpresa;

        // Verifica si el usuario autenticado es el desempleado que aplicó
        $esDesempleadoAplicante = ($loggedInUserId === $aplicacion->desempleado->IDUsuario);

        // Verifica si el usuario autenticado pertenece a la empresa que creó la oferta
        $esEmpresaOferente = ($aplicacion->oferta->empresa->IDUsuario === $loggedInUserId);

        if (!$esDesempleadoAplicante && !$esEmpresaOferente) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para ver los detalles de esta aplicación."
            ], 403);
        }

        // Carga las relaciones necesarias para mostrar los detalles de la aplicación
        $aplicacion->load('desempleado.user', 'oferta.empresa.user', 'oferta.categoria');

        return response()->json([
            "StatusCode" => 200,
            "ReasonPhrase" => "OK.",
            "Message" => "Detalles de la aplicación obtenidos correctamente.",
            "data" => $aplicacion
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Aplicacion $aplicacion)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Aplicacion $aplicacion)
    {
        //
        // Obtén el ID del usuario autenticado
        $userId = auth()->id();

        // Busca la oferta de empleo asociada a esta aplicación
        $ofertaEmpleo = OfertaEmpleo::findOrFail($aplicacion->IDOferta);

        // Verifica si el usuario autenticado es el propietario de la oferta de empleo
        $empresa = $ofertaEmpleo->empresa()->where('IDUsuario', $userId)->first();

        if (!$empresa) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para modificar el estado de esta aplicación."
            ], 403);
        }

        // Valida los datos de la petición (solo permitimos actualizar el estado por ahora)
        $validator = Validator::make($request->all(), [
            'Estado' => 'required|string|in:Abierta,Pendiente,Rechazada',
        ]);

        if ($validator->fails()) {
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "Errores de validación.",
                "Message" => $validator->errors()->all()
            ], 422);
        }

        try {
            $aplicacion->Estado = $request->input('Estado');
            $aplicacion->save();

            $aplicacion->load('desempleado.user', 'oferta.empresa', 'oferta.categoria');

            // Disparar el evento AplicacionEstadoCambiado
            event(new EstadoAplicacion($aplicacion));

            return response()->json([
                "StatusCode" => 200,
                "ReasonPhrase" => "OK.",
                "Message" => "El estado de la aplicación se ha actualizado correctamente.",
                "data" => $aplicacion
            ], 200);

        } catch (\Exception $e) {
            \Log::error("Error al actualizar el estado de la aplicación: " . $e->getMessage());
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al intentar actualizar el estado de la aplicación."
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Aplicacion $aplicacion)
    {
        //
        $userId = auth()->id();

        // Verifica si el usuario autenticado es el desempleado que aplicó
        if ($aplicacion->desempleado->IDUsuario !== $userId) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para eliminar esta aplicación."
            ], 403); // 403 (Forbidden)
        }

        try {
            $aplicacion->delete();

            return response()->json([
                "StatusCode" => 200,
                "ReasonPhrase" => "OK.",
                "Message" => "La aplicación se ha eliminado correctamente."
            ], 200); // 200 (OK)

        } catch (\Exception $e) {
            \Log::error("Error al eliminar la aplicación: " . $e->getMessage());
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al intentar eliminar la aplicación."
            ], 500); // 500 (Internal Server Error)
        }
    }

    public function myApplys()
    {
        try{
            $userId = auth()->id();
            $desempleado = Desempleado::where('IDUsuario', $userId)
            ->with('ofertasAplicadas')->paginate(10);

            if (!$desempleado) {
                return response()->json([
                    "StatusCode" => 403,
                    "ReasonPhrase" => "Acceso no autorizado.",
                    "Message" => "No tienes permiso para ver estas aplicaciones."
                ], 403);
            }

            return response()->json([
                'StatusCode' => 200,
                'ReasonPhrase' => 'Mis aplicaciones listadas correctamente.',
                'data' => $desempleado
            ], 200);
        } catch (\Exception $e) {
            \Log::error("Error al actualizar el estado de la aplicación: " . $e->getMessage());
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al intentar actualizar el estado de la aplicación."
            ], 500);
        }
    }

    public function applys(OfertaEmpleo $oferta)
    {
        // Verifica si el usuario autenticado es el propietario de la oferta (autorización)
        if ($oferta->empresa->IDUsuario !== auth()->id()) {
            return response()->json([
                "StatusCode" => 403,
                "ReasonPhrase" => "Acceso no autorizado.",
                "Message" => "No tienes permiso para ver estas aplicaciones."
            ], 403);
        }

        $aplicaciones = Aplicacion::where('IDOferta', $oferta->IDOferta)
        ->with('desempleado.user')->paginate(10);

        return response()->json([
            'StatusCode' => 200,
            'ReasonPhrase' => 'Aplicantes a la oferta listados correctamente.',
            'data' => $aplicaciones
        ], 200);
    }



}
