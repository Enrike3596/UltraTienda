<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $validated['email'])->first();

        try {
            $valid = $user instanceof User && Hash::check($validated['password'], $user->password);
        } catch (\RuntimeException $e) {
            // Hash inválido en BD (ej. $2a$ en vez de $2y$): no debe ser un 500.
            Log::warning('API login con hash inválido: '.$e->getMessage(), ['email' => $validated['email']]);

            $valid = false;
        }

        if (! $valid || ! $user instanceof User) {
            throw ValidationException::withMessages([
                'email' => 'El correo o la contraseña son incorrectos.',
            ]);
        }

        $token = $user->createToken('postman')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user->load('role'),
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Sesión cerrada.']);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json($request->user()->load('role'));
    }
}
