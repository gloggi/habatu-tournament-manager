<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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

    public function store(Request $request)
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
        $availableRoles = [['id' => 'user', 'name' => 'User'], ['id' => 'referee', 'name' => 'Schiri'], ['id' => 'admin', 'name' => 'Admin']];

        return response()->json($availableRoles);
    }
}
