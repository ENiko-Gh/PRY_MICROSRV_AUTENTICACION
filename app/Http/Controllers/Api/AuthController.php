<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * [POST] /api/register - Registra un nuevo usuario y genera un token.
     */
    public function register(Request $request)
    {
        // 1. Validación de datos de entrada
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'perfil' => ['nullable', 'in:administrador,editor,usuario común']
        ]);

        // 2. Crear el usuario (DocBlock para soporte de IDE, elimina advertencias)
        /** @var \App\Models\User $user */
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'perfil' => $request->perfil ?? 'usuario común',
        ]);

        // 3. Generar el token con el perfil como capacidad (scope)
        $token = $user->createToken('auth_token', [$user->perfil])->plainTextToken;

        // 4. Respuesta de éxito (201 Created)
        return response()->json([
            'message' => 'Registro exitoso. Token generado.',
            'user' => $user->only(['id', 'name', 'email', 'perfil']),
            'token' => $token
        ], 201);
    }

    /**
     * [POST] /api/login - Valida credenciales y genera un nuevo token.
     */
    public function login(Request $request)
    {
        // 1. Validación de credenciales
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'token_name' => 'nullable|string'
        ]);

        // 2. Intentar autenticar al usuario
        if (!Auth::attempt($request->only('email', 'password'))) {
            // Si falla, lanzar una excepción de validación
            throw ValidationException::withMessages([
                'email' => ['Las credenciales proporcionadas son incorrectas.'],
            ]);
        }

        // 3. Obtenemos el usuario autenticado (DocBlock para soporte de IDE)
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 4. Revocar tokens anteriores para asegurar que solo uno esté activo
        // Esto es una buena práctica para evitar tokens obsoletos
        $user->tokens()->delete();

        // 5. Crear el nuevo token con el perfil como capacidad (scope)
        $token = $user->createToken($request->token_name ?? 'api-token', [$user->perfil])->plainTextToken;

        // 6. Respuesta de éxito
        return response()->json([
            'message' => 'Inicio de sesión exitoso. Token generado.',
            'user' => $user->only(['id', 'name', 'email', 'perfil']),
            'token' => $token
        ]);
    }

    /**
     * [POST] /api/logout - Cierra la sesión eliminando todos los tokens activos.
     * Requiere el middleware 'auth:sanctum'.
     */
    public function logout(Request $request)
    {
        // Revoca todos los tokens del usuario autenticado
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Cierre de sesión exitoso. Todos los tokens han sido revocados.']);
    }

    /**
     * [GET] /api/user - Devuelve el usuario autenticado (Ruta Protegida).
     */
    public function show(Request $request)
    {
        // Devuelve solo los datos esenciales del usuario autenticado
        return response()->json($request->user()->only(['id', 'name', 'email', 'perfil']));
    }

    /**
     * [GET] /api/admin-route - Ruta de ejemplo protegida con validación de perfil.
     * La verificación de perfil ahora la realiza el middleware 'role:administrador'.
     */
    public function checkAdmin(Request $request)
    {
        // Si la solicitud llega aquí, el usuario YA ha sido verificado por el middleware 'role:administrador'.
        // Solo retornamos un mensaje de éxito.
        return response()->json([
            'message' => 'Acceso Autorizado. Perfil de Administrador verificado por Middleware.',
            'user_id' => $request->user()->id
        ], 200);
    }
}
