<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use App\Models\Team;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with(['section', 'team'])->get();
        return response()->json($users);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|unique:users',
            'password' => 'required|string|min:6',
        ]);

        // Hash the password before storing it
        $validated['password'] = Hash::make($validated['password']);

        // Create the user
        $user = User::create($validated);

        // Create a personal access token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return the token and user information
        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'nickname' => 'required|string',
            'password' => 'required|string',
            'section_id' => 'nullable|integer|exists:sections,id',
            'team_id' => 'nullable|integer|exists:teams,id',
        ]);

        // Find the user by their nickname
        $user = User::where('nickname', $validated['nickname'])->first();

        // Check if the user exists and if the password is correct
        if (!$user || !Hash::check($validated['password'], $user->password)) {
            return response()->json([
                'message' => 'Invalid login credentials.',
            ], 401);
        }

        // Create a new token for the user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Return the token and user information
        return response()->json([
            'token' => $token,
            'user' => $user,
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::with(['section', 'team'])->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nickname' => 'sometimes|string|max:255',
            'email' => 'sometimes|email',
            'password' => 'sometimes|string|min:8',
            'section_id' => 'nullable|integer|exists:sections,id',
            'team_id' => 'nullable|integer|exists:teams,id',
            'role' => 'nullable|string|in:admin,user,referee',
        ]);
        $user = User::findOrFail($id);
        if (isset($validated['team_id'])) {
            $validated['section_id'] = Team::find($validated['team_id'])->section_id;
        }
        $user->update($validated);
        return response()->json($user);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(null, 204);
    }

    public function  store(Request $request)
    {
        $validated = $request->validate([
            'nickname' => 'required|string|unique:users',
            'section_id' => 'nullable|integer|exists:sections,id',
            'team_id' => 'nullable|integer|exists:teams,id',
            'role' => 'nullable|string|in:admin,user,referee',
        ]);

        $validated['password'] = Hash::make(Str::random(20));

        $user = User::create($validated);

        return response()->json($user, 201);
    }

    public function getRoles()
    {
        $availableRoles = [ ['id' => 'user', 'name' => 'User'], ['id' => 'referee', 'name' => 'Schiri'], ['id' => 'admin', 'name' => 'Admin'],];
        return response()->json($availableRoles);
    }
}
