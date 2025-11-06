<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    // GET /api/users – peržiūra visų vartotojų
    public function index()
    {
        return response()->json(User::all());
    }

    // GET /api/users/{id} – vieno vartotojo peržiūra
    public function show($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        return response()->json($user);
    }

    // POST /api/users – naujo vartotojo kūrimas
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'password' => 'required|min:6',
                'address' => 'nullable|string|max:500',
                'role' => 'in:user,worker,admin',
                'shelter_id' => [
                    Rule::requiredIf(fn() => $request->input('role') === 'worker'), // required only for workers
                    'nullable',
                    'exists:shelters,id', // must exist in shelters table
                ],
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Invalid input data',
                'details' => $e->errors()
            ], 422);
        }
        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'message' => 'User created successfully',
            'data' => $user
        ], 201);
    }

    // PUT /api/users/{id} – vartotojo atnaujinimas
    public function update(Request $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'password' => 'sometimes|min:6',
            'address' => 'sometimes|string|max:500',
            'role' => 'sometimes|in:user,worker,admin',
            'shelter_id' => [
                Rule::requiredIf(fn() => $request->input('role') === 'worker'), // required only for workers
                'exists:shelters,id', // must exist in shelters table
            ],
        ]);

        if (isset($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        }

        $user->update($validated);
        return response()->json([
            'message' => 'User updated successfully',
            'data' => $user
        ]);
    }

    // DELETE /api/users/{id} – vartotojo pašalinimas
    public function destroy($id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }

        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully'
        ], 204);
    }
}
