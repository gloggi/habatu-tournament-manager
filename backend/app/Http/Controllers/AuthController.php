<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
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
        if (! $user || ! Hash::check($validated['password'], $user->password)) {
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

    public function redirectToProvider(Request $request)
    {
        $url = Socialite::driver('midata')->stateless()->redirect()->getTargetUrl();

        return response()->json(['url' => $url]);
    }

    public function handleProviderCallback(Request $request)
    {
        $tokenResponse = Socialite::driver('midata')->getAccessTokenResponse($request->code);
        $accessToken = $tokenResponse['access_token'];
        $midataUser = Socialite::driver('midata')->userFromToken($accessToken);

        $user = User::where('midata_id', $midataUser->id)->first();

        if (! $user) {
            $user = User::create([
                'nickname' => $midataUser->attributes['nickname'],
                'midata_id' => $midataUser->attributes['id'],
            ]);

            $user->save();
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'token' => $token,
            'user' => $user,
        ], 201);

    }

    public function me(Request $request)
    {
        $user = $request->user('api');
        if ($user) {
            return response()->json($user);
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

    }
}
