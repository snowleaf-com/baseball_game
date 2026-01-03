<?php

namespace App\Console\Commands;

use App\Models\Game;
use App\Models\LeagueSetting;
use App\Models\User;
use App\Services\GameSimulationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoGameCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'game:auto-play';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自動試合を実行（試合間隔をチェックして実行）';

    protected $gameService;

    public function __construct(GameSimulationService $gameService)
    {
        parent::__construct();
        $this->gameService = $gameService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $leagueSetting = LeagueSetting::getInstance();
        $intervalMinutes = $leagueSetting->game_interval_minutes;

        $this->info("自動試合を開始します（試合間隔: {$intervalMinutes}分）");

        // 全ユーザーを取得
        $users = User::with('players')->get();

        if ($users->count() < 2) {
            $this->warn('試合を実行するには最低2チーム必要です。');
            return 0;
        }

        $playedCount = 0;
        $skippedCount = 0;

        // 各チームについて、試合間隔をチェックして試合を実行
        foreach ($users as $user) {
            // 最後の試合を取得
            $lastGame = Game::where(function ($query) use ($user) {
                $query->where('home_team_id', $user->id)
                      ->orWhere('away_team_id', $user->id);
            })->latest('played_at')->first();

            // 試合間隔チェック
            if ($lastGame) {
                $minutesSinceLastGame = now()->diffInMinutes($lastGame->played_at);
                if ($minutesSinceLastGame < $intervalMinutes) {
                    $this->line("チーム「{$user->team_name}」は試合間隔が短すぎます（{$minutesSinceLastGame}分前）。スキップします。");
                    $skippedCount++;
                    continue;
                }
            }

            // 対戦相手をランダムに選択（自分以外）
            $opponents = $users->where('id', '!=', $user->id);
            if ($opponents->isEmpty()) {
                continue;
            }

            $opponent = $opponents->random();

            // 打順を自動生成（野手9名の打順）
            $batters = $user->players()
                ->where('player_type', 0)
                ->orderBy('batting_order')
                ->limit(9)
                ->get();

            if ($batters->count() < 9) {
                $this->warn("チーム「{$user->team_name}」の野手が不足しています（必要: 9名、現在: {$batters->count()}名）。スキップします。");
                $skippedCount++;
                continue;
            }

            $battingOrder = $batters->pluck('batting_order')->toArray();

            // チーム方針を取得（デフォルト値は'balanced'）
            $teamStrategy = $user->boss_type ?? 'balanced';

            try {
                // 試合を実行
                $game = $this->gameService->simulateGame(
                    $user,
                    $opponent,
                    $battingOrder,
                    $teamStrategy
                );

                $this->info("試合完了: {$user->team_name} vs {$opponent->team_name} ({$game->home_score}-{$game->away_score})");
                $playedCount++;

                Log::info("自動試合実行", [
                    'home_team' => $user->team_name,
                    'away_team' => $opponent->team_name,
                    'score' => "{$game->home_score}-{$game->away_score}",
                ]);

            } catch (\Exception $e) {
                $this->error("試合実行エラー: {$e->getMessage()}");
                Log::error("自動試合エラー", [
                    'user_id' => $user->id,
                    'opponent_id' => $opponent->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        $this->info("自動試合完了: 実行 {$playedCount}試合、スキップ {$skippedCount}チーム");
        return 0;
    }
}


