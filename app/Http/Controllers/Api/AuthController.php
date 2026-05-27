<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Socio;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json(['message' => 'Credenciales incorrectas.'], 401);
        }

        $user = Auth::user();

        if (!$user->esSocio() || !$user->socio_id) {
            Auth::logout();
            return response()->json(['message' => 'Esta cuenta no corresponde a un socio.'], 403);
        }

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json(['token' => $token]);
    }

    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'identificador' => 'required|string',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|string|min:8|confirmed',
        ]);

        $id = trim($request->identificador);

        $socio = Socio::where('estado', 'activo')
            ->where(function ($q) use ($id) {
                $q->where('numero_socio', $id)
                  ->orWhere('numero_documento', $id);
            })
            ->first();

        if (!$socio) {
            return response()->json(['message' => 'No se encontró un socio activo con ese número o DNI.'], 422);
        }

        if (User::where('socio_id', $socio->id)->exists()) {
            return response()->json(['message' => 'Ya existe una cuenta para este socio. Iniciá sesión.'], 422);
        }

        $user = User::create([
            'name'     => $socio->nombreCompleto(),
            'email'    => trim($request->email),
            'password' => Hash::make($request->password),
            'rol'      => 'socio',
            'socio_id' => $socio->id,
        ]);

        $token = $user->createToken('mobile')->plainTextToken;

        return response()->json(['token' => $token], 201);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada.']);
    }
}
