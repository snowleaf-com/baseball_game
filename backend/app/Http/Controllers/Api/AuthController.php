<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\PlayerMasterService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    protected $playerMasterService;

    public function __construct(PlayerMasterService $playerMasterService)
    {
        $this->playerMasterService = $playerMasterService;
    }

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
        // playersパラメータが提供されている場合は従来の方式、省略された場合はデフォルト選手を自動生成
        $hasPlayers = $request->has('players') && is_array($request->players) && count($request->players) > 0;
        
        $rules = [
            'saku' => 'required|string|max:50|unique:users',
            'password' => 'required|string|min:4|max:8',
            'team_name' => 'required|string|max:50|unique:users',
            'home_url' => 'nullable|url',
            'icon' => 'nullable|string',
            // boss_typeはチーム方針（文字列型）
            'boss_type' => 'nullable|string|in:offensive,defensive,balanced,running',
        ];

        if ($hasPlayers) {
            // 従来の方式：手動で選手パラメータを指定
            $rules['players'] = 'required|array|size:10';
            $rules['players.*.name'] = 'required|string|max:50';
            $rules['players.*.position'] = 'required|string';
            $rules['players.*.power'] = 'required|integer|min:1|max:10';
            $rules['players.*.meet'] = 'required|integer|min:1|max:10';
            $rules['players.*.run'] = 'required|integer|min:1|max:10';
            $rules['players.*.defense'] = 'required|integer|min:1|max:10';
        }

        $request->validate($rules);

        // チーム方針のデフォルト値
        $teamStrategy = $request->boss_type ?? 'balanced';

        $user = User::create([
            'saku' => $request->saku,
            'password' => Hash::make($request->password),
            'team_name' => $request->team_name,
            'home_url' => $request->home_url,
            'icon' => $request->icon,
            'boss_type' => $teamStrategy,
            'ip_address' => $request->ip(),
            'budget' => 2000000000, // 初期資金20億円
            'year' => 1, // 初期年数
        ]);

        if ($hasPlayers) {
            // 従来の方式：手動で選手を作成
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
                    'player_type' => $index < 9 ? 0 : 1,
                    'pitch_type' => $index >= 9 ? ($playerData['pitch_type'] ?? '') : null,
                    'fastball' => $index >= 9 ? $playerData['power'] : null,
                    'changeup' => $index >= 9 ? $playerData['meet'] : null,
                    'control' => $index >= 9 ? $playerData['run'] : null,
                ]);
            }
        } else {
            // 新しい方式：デフォルト選手を自動生成
            $this->playerMasterService->createDefaultPlayersForUser($user);
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

