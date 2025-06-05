<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SectorController;
use App\Http\Controllers\API\EmpresaController;
use App\Http\Controllers\API\DesempleadoController;
use App\Http\Controllers\API\DocumentoController;
use App\Http\Controllers\API\GrupoController;
use App\Http\Controllers\API\PublicacionController;
use App\Http\Controllers\API\ComentarioController;
use App\Http\Controllers\API\OfertaEmpleoController;
use App\Http\Controllers\API\AplicacionController;
use App\Http\Controllers\API\CategoriaController;
use App\Http\Controllers\API\SuscripcionsController;
use App\Http\Controllers\API\NotificacionController;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post("register",[AuthController::class,"register"]);
Route::post("login", [AuthController::class, "login"]);
Route::post("recover", [AuthController::class, "recover"]);

Route::middleware('auth:sanctum')->group(function(){

    Route::get("testAuth",[AuthController::class, "testAuth"]);
    Route::post("logout", [AuthController::class, "logout"]);
    Route::post("verifyCode", [AuthController::class, "verifyCode"]);
    Route::get('usuarios/grupos', [AuthController::class, 'listGroupUser']);
    Route::get('usuarios/{idUsuario}', [AuthController::class, 'userID']);
    Route::get('rolUsuario', [AuthController::class, 'userRol']);

    Route::apiResource("empresa", EmpresaController::class)->middleware(['rol:admin|empresa'])->only(['show', 'update', 'destroy']); //Si se utliza comas para separar los roles laravel lo identifica como middlewares y no como parametros
    Route::apiResource("empresa", EmpresaController::class)->only(['store']);
    Route::get("test", [EmpresaController::class,"test"]);

    Route::apiResource("desempleado", DesempleadoController::class)->middleware(['rol:admin|usuario'])->only(['show', 'update', 'destroy']);
    Route::apiResource("desempleado", DesempleadoController::class)->only(['store']);

    Route::apiResource("documento", DocumentoController::class);
    Route::get('documentos/byIDUsuario', [DocumentoController::class, 'documentoByIDUsuario']);
    Route::get('documentos/byIDUsuario/{userId}', [DocumentoController::class, 'getDocumentoByIDUsuario']);
    Route::get('documentos/fotosByIDUsuario', [DocumentoController::class, 'fotosByUsuario']);
    Route::get('documentos/fotosByIDUsuario/{userId}', [DocumentoController::class, 'getFotosByUsuario']);
    Route::get('documentos/pdfsByIDUsuario', [DocumentoController::class, 'pdfsByUsuario']);
    Route::get('documentos/pdfsByIDUsuario/{userId}', [DocumentoController::class, 'getPdfsByUsuario']);
    Route::get('documentos/videosByIDUsuario', [DocumentoController::class, 'videosByUsuario']);
    Route::get('documentos/videosByIDUsuario/{userId}', [DocumentoController::class, 'getVideosByUsuario']);

    Route::apiResource("grupo", GrupoController::class)->except(['create', 'edit']);
    Route::get('grupos/{idGrupo}/unirse', [GrupoController::class, 'join']);
    Route::get('grupos/{idGrupo}/abandonar', [GrupoController::class, 'leave']);
    Route::get('grupos/publicaciones/{grupo}', [GrupoController::class, 'posts']);
    Route::get('grupos/byIDUsuario', [GrupoController::class, 'gruposByIDUsuario']);
    Route::get('grupos/{idGrupo}/pendientes', [GrupoController::class, 'getPendingRequests']);
    Route::post('grupos/{idGrupo}/{solicitudUserId}/estado', [GrupoController::class, 'handleJoinRequest']);
    //Route::get('grupo/byIDUsuario', [GrupoController::class, 'listGroupUser']);

    Route::apiResource("publicacion", PublicacionController::class)->except(['create', 'edit']);
    Route::get('publicacion/{publicacion}/like', [PublicacionController::class, 'like']);
    Route::delete('publicacion/{publicacion}/unlike', [PublicacionController::class, 'unlike']);
    Route::get('publicacion/{publicacion}/liked', [PublicacionController::class, 'liked']);
    Route::get('publicaciones/postsByUsuario', [PublicacionController::class, 'postsByUsuario']);
    Route::get('publicaciones/postsByUsuario/{userId}', [PublicacionController::class, 'getPostsByUsuario']);

    Route::apiResource("comentario", ComentarioController::class)->except(['create', 'edit']);

    Route::get('ofertaEmpleo/buscar', [OfertaEmpleoController::class, 'buscar']);
    Route::apiResource("ofertaEmpleo", OfertaEmpleoController::class)->except(['create', 'edit']);
    Route::get('ofertasEmpleos', [OfertaEmpleoController::class, 'getUserOffers']);

    Route::apiResource("aplicacion", AplicacionController::class)->except(['index', 'create', 'edit']);
    Route::get('misAplicaciones', [AplicacionController::class, 'myApplys']);
    Route::get('aplicacion/{oferta}/aplicaciones', [AplicacionController::class, 'applys']);

    Route::resource('sector', SectorController::class)->except(['create', 'edit']);

    Route::apiResource("categoria", CategoriaController::class)->except(['create', 'edit']);
    Route::get('categoriasUsuario', [CategoriaController::class, 'categoryUser']);

    Route::get('misSuscripciones', [SuscripcionsController::class, 'mySuscriptions']);
    Route::post('suscripcion/add', [SuscripcionsController::class, 'store']);
    Route::post('suscripcion/eliminar', [SuscripcionsController::class, 'destroy']);

    Route::apiResource("notificacion", NotificacionController::class)->only(['index','destroy']);
    Route::get('notificaciones/{read}', [NotificacionController::class, 'markAsRead']);
});
