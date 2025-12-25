<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'saku' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('saku', $request->saku)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'saku' => ['認証情報が正しくありません。'],
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->load('players'),
            'token' => $token,
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'saku' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:4|max:8',
            'team_name' => 'required|string|max:50|unique:users',
            'home_url' => 'nullable|url',
            'icon' => 'nullable|string',
            'players' => 'required|array|size:10',
            'players.*.name' => 'required|string|max:50',
            'players.*.position' => 'required|string',
            'players.*.power' => 'required|integer|min:1|max:10',
            'players.*.meet' => 'required|integer|min:1|max:10',
            'players.*.run' => 'required|integer|min:1|max:10',
            'players.*.defense' => 'required|integer|min:1|max:10',
            'boss_type' => 'required|array',
        ]);

        $user = User::create([
            'saku' => $request->saku,
            'password' => Hash::make($request->password),
            'team_name' => $request->team_name,
            'home_url' => $request->home_url,
            'icon' => $request->icon,
            'boss_type' => $request->boss_type,
            'ip_address' => $request->ip(),
        ]);

        foreach ($request->players as $index => $playerData) {
            $user->players()->create([
                'player_number' => $index + 1,
                'batting_order' => $index + 1,
                'position' => $playerData['position'] ?? null,
                'name' => $playerData['name'],
                'power' => $playerData['power'],
                'meet' => $playerData['meet'],
                'run' => $playerData['run'],
                'defense' => $playerData['defense'],
                'player_type' => $index < 8 ? 0 : 1,
                'pitch_type' => $index >= 8 ? ($playerData['pitch_type'] ?? '') : null,
                'fastball' => $index >= 8 ? $playerData['power'] : null,
                'changeup' => $index >= 8 ? $playerData['meet'] : null,
                'control' => $index >= 8 ? $playerData['run'] : null,
            ]);
        }

        $token = $user->createToken('auth-token')->plainTextToken;

        return response()->json([
            'user' => $user->load('players'),
            'token' => $token,
        ], 201);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'ログアウトしました。']);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('players'));
    }
}

