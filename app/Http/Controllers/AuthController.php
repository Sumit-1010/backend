<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Sign up API for creating a new user
    public function signup(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,base_commander,logistics_officer',
            'base_id' => 'required|exists:bases,id',
        ]);

        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            // 'password' => bcrypt($validatedData['password']),
            'password' => Hash::make($validatedData['password']),
            'role' => $validatedData['role'],
            'base_id' => $validatedData['base_id'],
        ]);

        // Generate token
        $token = $user->createToken('authToken',[$user->role, $user->base_id])->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'role' => $user->role,
            'base_id' => $user->base_id,
        ], 201);
    }

    // Login API for authenticating a user
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        if (!auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = auth()->user();

        // Generate token
        $token = $user->createToken('authToken',[$user->role, $user->base_id])->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    // Logout API for revoking a user's token
    public function logout(Request $request)
    {
        $user = auth()->user();

        // Revoke all tokens
        $user->tokens()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }


}
