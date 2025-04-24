<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Desempleado;
use Illuminate\Support\Facades\Hash;
use Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Log;

use Mail;
use App\Mail\VerificationEmail;
use App\Mail\RecoverEmail;


class AuthController extends Controller
{
    //

    public function testAuth (){
        return response()->json(['message' => 'Testiado'], 200);
    }


    public function register(Request $request)
    {
        Log::error('Verifica si guarda errores de registro de usuario en logs ');
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required",
        ]);

        if($validator->fails()){
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "validation errors.",
                "Message" => $validator->errors()->all()
            ],422);// 422 (Unprocessable Entity) para errores de validación
        }

        try {

            $verificationCode = rand(100000, 999999);

            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'verificationCode' => $verificationCode,
                'created_at' => now()
            ]);

            $response = [];
            $response["token"] = $user->createToken($user->email)->plainTextToken;
            $response["email"] = $user->email;


            Mail::to($user->email)->send(new VerificationEmail($user, $verificationCode));

            return response()->json([
                "StatusCode" => 201,
                "ReasonPhrase" => "Usuario Registrado",
                'Message' => 'Usuario registrado correctamente',
                "Data" => $response
            ], 201); // 201 (Created) para registro exitoso

        } catch (QueryException $e) {

            if ($e->getCode() === '23000') { // Código de error para duplicado en MySQL
                return response()->json([
                    "StatusCode" => 409,
                    "ReasonPhrase" => "El email ya está registrado.",
                    "Message" => "Email duplicado."
                ], 409);// 409 (Conflict) para duplicado
            }

            // Manejar otros errores de base de datos si es necesario
            return response()->json([
                "StatusCode" => 500,
                "ReasonPhrase" => "Error interno del servidor.",
                "Message" => "Ocurrió un error al registrar el usuario."
            ], 500); // 500 (Internal Server Error) para otros errores
        }

    }

    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "password" => "required",
        ]);

        if($validator->fails()){
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "validation errors.",
                "Message" => $validator->errors()->all()
            ],422);// 422 (Unprocessable Entity) para errores de validación
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();


            $logged = Empresa::where('IDUsuario', $user->IDUsuario)->first();
            if (!$logged) {
                $logged = Desempleado::where('IDUsuario', $user->IDUsuario)->first();
            }



            $response = [];
            $response["token"] = $user->createToken($user->email)->plainTextToken;
            $response["profile"] = $logged;

            return response()->json([
                "StatusCode" => 200,
                "ReasonPhrase" => "Usuario Logiado",
                'Message' => 'Usuario logeado correctamente',
                "Data" => $response
            ]);
        }

        // Verificar si el usuario existe
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json([
                "StatusCode" => 404,
                "ReasonPhrase" => "Error de credenciales",
                'Message' => 'El correo electrónico no existe.',
                "Data" => null
            ]);
        }

        // Verificar si la contraseña es incorrecta
        if (!Hash::check($request->password, $user->password)) {
            return response()->json([
                "StatusCode" => 401,
                "ReasonPhrase" => "Error de credenciales",
                'Message' => 'Contraseña incorrecta.',
                "Data" => null
            ]);
        }

        return response()->json([
            "StatusCode" => 404,
            "ReasonPhrase" => "Error",
            'Message' => 'Las credenciales no coinciden.',
            "Data" => null
        ]);

    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();
        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
            "code" => "required",
        ]);

        if($validator->fails()){
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "Faltan campos",
                "Message" => $validator->errors()->all()
            ],422);// 422 (Unprocessable Entity) para errores de validación
        }

         // Buscar usuario por email
        $user = User::where("email", $request->email)->first();

        if (!$user) {
            return response()->json([
                "StatusCode" => 404,
                "ReasonPhrase" => "User Not Found",
                "Message" => "No se encontró un usuario con ese email."
            ], 404);
        }

        // Verificar si el código coincide
        if ($user->verificationCode !== $request->code) {
            return response()->json([
                "StatusCode" => 400,
                "ReasonPhrase" => "Invalid Code",
                "Message" => "El código ingresado es incorrecto."
            ], 400);
        }

        // Marcar al usuario como verificado
        $user->email_verified_at = now();
        $user->verificationCode = null; // Eliminar el código después de usarlo
        $user->save();

        return response()->json([
            "StatusCode" => 200,
            "ReasonPhrase" => "Success",
            "Message" => "Código verificado correctamente. Email confirmado."
        ], 200);

    }

    public function recover(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => "required|email",
        ]);

        if($validator->fails()){
            return response()->json([
                "StatusCode" => 422,
                "ReasonPhrase" => "Se requiere un email valido",
                "Message" => $validator->errors()->all()
            ],422);// 422 (Unprocessable Entity) para errores de validación
        }

         // Buscar usuario por email
        $user = User::where("email", $request->email)->first();

        if (!$user) {
            return response()->json([
                "StatusCode" => 404,
                "ReasonPhrase" => "User Not Found",
                "Message" => "No se encontró un usuario con ese email."
            ], 404);
        }

        // Generar nueva contraseña
        $newPassword = $this->generateRandomPassword();

        // Actualizar contraseña del usuario en la base de datos
        $user->password = Hash::make($newPassword);
        $user->save();

        Mail::to($user->email)->send(new RecoverEmail($user, $newPassword));

        return response()->json([
            "StatusCode" => 200,
            "ReasonPhrase" => "Success",
            "Message" => "Código verificado correctamente. Email confirmado."
        ], 200);

    }

    function generateRandomPassword($length = 6) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $password = '';
        $hasLowercase = false;
        $hasUppercase = false;
        $hasNumber = false;

        while (strlen($password) < $length || !$hasLowercase || !$hasUppercase || !$hasNumber) {
            $password = '';
            $hasLowercase = false;
            $hasUppercase = false;
            $hasNumber = false;

            for ($i = 0; $i < $length; $i++) {
                $char = $characters[rand(0, strlen($characters) - 1)];
                $password .= $char;

                if (ctype_lower($char)) $hasLowercase = true;
                if (ctype_upper($char)) $hasUppercase = true;
                if (is_numeric($char)) $hasNumber = true;
            }
        }

        return $password;
    }



}
