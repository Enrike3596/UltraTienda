<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(
            User::with('role')->orderByDesc('created_at')->orderByDesc('id')->paginate(10)
        );
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
        ]);

        return response()->json(User::create($validated)->load('role'), 201);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json($user->load('role'));
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
        ]);

        if (($validated['password'] ?? null) === null) {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json($user->fresh()->load('role'));
    }

    public function destroy(User $user): JsonResponse
    {
        $user->delete();

        return response()->json(null, 204);
    }
}
