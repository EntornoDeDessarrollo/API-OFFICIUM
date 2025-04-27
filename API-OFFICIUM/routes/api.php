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


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post("register",[AuthController::class,"register"]);
Route::post("login", [AuthController::class, "login"]);
Route::post("recover", [AuthController::class, "recover"]);
Route::resource('sector', SectorController::class);

Route::middleware('auth:sanctum')->group(function(){

    Route::get("testAuth",[AuthController::class, "testAuth"]);
    Route::post("logout", [AuthController::class, "logout"]);
    Route::post("verifyCode", [AuthController::class, "verifyCode"]);
    Route::get('usuarios/grupos', [AuthController::class, 'listGroupUser']);

    Route::apiResource("empresa", EmpresaController::class)->middleware(['rol:admin|empresa'])->only(['show', 'update', 'destroy']); //Si se utliza comas para separar los roles laravel lo identifica como middlewares y no como parametros
    Route::apiResource("empresa", EmpresaController::class)->only(['store']);
    Route::get("test", [EmpresaController::class,"test"]);

    Route::apiResource("desempleado", DesempleadoController::class)->middleware(['rol:admin|usuario'])->only(['show', 'update', 'destroy']);
    Route::apiResource("desempleado", DesempleadoController::class)->only(['store']);

    Route::apiResource("documento", DocumentoController::class);
    Route::get('documentos/byIDUsuario', [DocumentoController::class, 'documentoByIDUsuario']);

    Route::apiResource("grupo", GrupoController::class)->except(['create', 'edit']);
    Route::get('grupos/{idGrupo}/unirse', [GrupoController::class, 'join']);
    Route::get('grupos/{idGrupo}/abandonar', [GrupoController::class, 'leave']);
    Route::get('grupos/publicaciones/{grupo}', [GrupoController::class, 'posts']);
    //Route::get('grupo/byIDUsuario', [GrupoController::class, 'listGroupUser']);

    Route::apiResource("publicacion", PublicacionController::class)->except(['create', 'edit']);
    Route::get('publicacion/{publicacion}/like', [PublicacionController::class, 'like']);
    Route::delete('publicacion/{publicacion}/unlike', [PublicacionController::class, 'unlike']);
    Route::get('publicacion/{publicacion}/liked', [PublicacionController::class, 'liked']);

    Route::apiResource("comentario", ComentarioController::class)->except(['create', 'edit']);

});
