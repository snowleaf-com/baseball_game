<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\User;
use App\Services\GameSimulationService;
use Illuminate\Http\Request;

class GameController extends Controller
{
    protected $gameService;

    public function __construct(GameSimulationService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function index(Request $request)
    {
        $games = Game::with(['homeTeam', 'awayTeam'])
            ->orderBy('played_at', 'desc')
            ->limit(5)
            ->get();

        return response()->json($games);
    }

    public function play(Request $request)
    {
        $request->validate([
            'opponent_team_id' => 'required|exists:users,id',
            'batting_order' => 'required|array|size:10',
            // boss_typeはオプショナル（チーム方針、省略時はユーザーの保存値またはデフォルト値を使用）
            'boss_type' => 'nullable|string|in:offensive,defensive,balanced,running',
        ]);

        $user = $request->user();
        $opponent = User::findOrFail($request->opponent_team_id);

        // 試合間隔チェック
        $lastGame = Game::where(function ($query) use ($user) {
            $query->where('home_team_id', $user->id)
                  ->orWhere('away_team_id', $user->id);
        })->latest('played_at')->first();

        if ($lastGame && $user->games > 0) {
            $minutesSinceLastGame = now()->diffInMinutes($lastGame->played_at);
            if ($minutesSinceLastGame < 30) {
                return response()->json([
                    'message' => '試合間隔が短すぎます。30分以上空けてください。',
                ], 400);
            }
        }

        // チーム方針はユーザーの保存値を使用（試合進行には影響しないが、将来の拡張性のため）
        $teamStrategy = $request->boss_type ?? $user->boss_type ?? 'balanced';

        // 試合実行（teamStrategyは将来の拡張用、現在は使用しない）
        $result = $this->gameService->simulateGame(
            $user,
            $opponent,
            $request->batting_order,
            $teamStrategy
        );

        return response()->json($result, 201);
    }

    public function show(Game $game)
    {
        return response()->json($game->load(['homeTeam.players', 'awayTeam.players']));
    }
}

