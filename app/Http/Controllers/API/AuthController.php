<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validar los datos de entrada
        $fields = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        // Verificar el correo electrónico
        $user = User::with('role')->where('email', $fields['email'])->first();

        // Verificar la contraseña
        if (!$user || !Hash::check($fields['password'], $user->password)) {
            return response([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        // Crear el Token de Sanctum
        $token = $user->createToken('iglesiatoken')->plainTextToken;

        // Responder con los datos del usuario, su rol y el token
        $response = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role->name // Útil para saber qué vistas mostrar en Ionic
            ],
            'token' => $token
        ];

        return response($response, 200);
    }

    public function logout(Request $request)
    {
        // Revocar (eliminar) el token que se usó para autenticarse
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'Sesión cerrada correctamente'
        ], 200);
    }
}
