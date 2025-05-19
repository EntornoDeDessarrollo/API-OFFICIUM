<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Database\QueryException;
use App\Http\Middleware\RolCheck;


return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
        $middleware->alias([
            'rol' => RolCheck::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
        $exceptions->render(function (Throwable $e) {
            // Para solicitudes a la API (rutas que comienzan con 'api/')
           if (request()->is('api/*')) {
                if ($e instanceof ValidationException) {
                    return response()->json([
                        "StatusCode" => 422,
                        "ReasonPhrase" => "Errores de validación.",
                        "Message" => $e->validator->errors()->all(),
                        "Exception" => $e->getMessage()
                    ], 422);
                }
                if ($e instanceof AuthenticationException) {
                    return response()->json([
                        "StatusCode" => 401,
                        "ReasonPhrase" => "No autenticado.",
                        "Message" => "Debes iniciar sesión para acceder a este recurso.",
                        "Exception" => $e->getMessage()
                    ], 401);
                }
                if ($e instanceof ModelNotFoundException) {
                    return response()->json([
                        "StatusCode" => 404,
                        "ReasonPhrase" => "Recurso no encontrado.",
                        "Message" => "El recurso solicitado no existe o fue eliminado.",
                        "Exception" => $e->getMessage()
                    ], 404);
                }
                if ($e instanceof NotFoundHttpException) {
                    return response()->json([
                        "StatusCode" => 404,
                        "ReasonPhrase" => "Ruta no encontrada.",
                        "Message" => "La ruta solicitada no existe en esta API.",
                        "Exception" => $e->getMessage()
                    ], 404);
                }
                if ($e instanceof QueryException) {
                    return response()->json([
                        "StatusCode" => 500,
                        "ReasonPhrase" => "Error en la base de datos.",
                        "Message" => "Ha ocurrido un problema al realizar la consulta en la base de datos.". $e->getMessage()."\n".$e->getSql(),
                        "Exception" => $e->getMessage(),
                        "Sql" => $e->getSql(),
                        "Bindings" => $e->getBindings(),
                        "File" => $e->getFile(),
                        "Line" => $e->getLine()
                    ], 500);
                }
                // Registra otras excepciones de rutas de API para depuración
                Log::error($e);
                return response()->json([
                    "StatusCode" => 500,
                    "ReasonPhrase" => "Error interno del servidor.",
                    "Message" => "Ha ocurrido un error inesperado. Intenta más tarde.",
                    "Exception" => $e->getMessage(),
                    "File" => $e->getFile(),
                    "Line" => $e->getLine()

                ], 500);
            }
                // Para solicitudes web se devuelve null.
                // Esto permite que Laravel continúe con el manejo de excepciones predeterminado,
                // que generalmente implica renderizar una vista de error HTML.
            return null;
           });

    })
    ->withEvents(discover: [
        __DIR__.'/../app/Listeners',
    ])
   ->create();
