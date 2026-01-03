<?php

namespace App\Services;

use App\Models\Game;
use App\Models\LeagueSetting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class GameSimulationService
{
    public function simulateGame(User $homeTeam, User $awayTeam, array $homeBattingOrder, string $teamStrategy)
    {
        return DB::transaction(function () use ($homeTeam, $awayTeam, $homeBattingOrder, $teamStrategy) {
            // $teamStrategyは将来の拡張用（現在は使用しない）
            // 試合シミュレーションロジック（既存のPerlコードを移植）
            $homeScore = 0;
            $awayScore = 0;
            $gameLog = [];

            // 簡易版の試合シミュレーション
            for ($inning = 1; $inning <= 9; $inning++) {
                $homeInningScore = $this->simulateInning($homeTeam, $awayTeam, $homeBattingOrder, true);
                $awayInningScore = $this->simulateInning($awayTeam, $homeTeam, range(1, 10), false);
                
                $homeScore += $homeInningScore;
                $awayScore += $awayInningScore;

                $gameLog[] = [
                    'inning' => $inning,
                    'home_score' => $homeInningScore,
                    'away_score' => $awayInningScore,
                ];
            }

            // リーグ設定を取得
            $leagueSetting = LeagueSetting::getInstance();

            // 試合結果を保存
            $game = Game::create([
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'home_score' => $homeScore,
                'away_score' => $awayScore,
                'game_log' => $gameLog,
                'played_at' => now(),
                'league_day' => $leagueSetting->league_day,
                'league_number' => $leagueSetting->league_number,
            ]);

            // チーム成績を更新
            $this->updateTeamStats($homeTeam, $awayTeam, $homeScore, $awayScore);

            return $game->load(['homeTeam', 'awayTeam']);
        });
    }

    private function simulateInning(User $offenseTeam, User $defenseTeam, array $battingOrder, bool $isHome)
    {
        $runs = 0;
        $outs = 0;
        $bases = [0, 0, 0]; // 1塁, 2塁, 3塁

        $batterIndex = 0;
        while ($outs < 3) {
            $batter = $offenseTeam->players()
                ->where('batting_order', $battingOrder[$batterIndex % count($battingOrder)])
                ->first();

            if (!$batter) {
                $batterIndex++;
                continue;
            }

            $result = $this->calculateAtBat($batter, $defenseTeam);

            if ($result['out']) {
                $outs++;
            } else {
                $runs += $result['runs'];
                // ランナー処理
                $this->advanceRunners($bases, $result);
            }

            $batterIndex++;
        }

        return $runs;
    }

    private function calculateAtBat($batter, $defenseTeam)
    {
        // 簡易版の打撃計算
        $power = $batter->power;
        $meet = $batter->meet;
        $run = $batter->run;

        $roll = rand(1, 100);
        
        if ($roll < $power * 5) {
            // ホームラン
            return ['out' => false, 'runs' => 1, 'hit_type' => 'home_run'];
        } elseif ($roll < ($power + $meet) * 5) {
            // ヒット
            return ['out' => false, 'runs' => 0, 'hit_type' => 'hit'];
        } else {
            // アウト
            return ['out' => true, 'runs' => 0, 'hit_type' => 'out'];
        }
    }

    private function advanceRunners(array &$bases, array $result)
    {
        // ランナー進塁処理（簡易版）
        if ($result['hit_type'] === 'home_run') {
            $bases = [0, 0, 0];
        } elseif ($result['hit_type'] === 'hit') {
            array_unshift($bases, 1);
            array_pop($bases);
        }
    }

    private function updateTeamStats(User $homeTeam, User $awayTeam, int $homeScore, int $awayScore)
    {
        if ($homeScore > $awayScore) {
            $homeTeam->increment('wins');
            $awayTeam->increment('losses');
        } else {
            $awayTeam->increment('wins');
            $homeTeam->increment('losses');
        }

        $homeTeam->increment('runs_scored', $homeScore);
        $homeTeam->increment('runs_allowed', $awayScore);
        $awayTeam->increment('runs_scored', $awayScore);
        $awayTeam->increment('runs_allowed', $homeScore);

        $homeTeam->save();
        $awayTeam->save();
    }
}

