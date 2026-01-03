<?php

namespace App\Services;

use App\Models\Player;
use App\Models\PlayerMaster;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PlayerMasterService
{
    /**
     * デフォルト選手を生成してユーザーに登録
     */
    public function createDefaultPlayersForUser(User $user): void
    {
        DB::transaction(function () use ($user) {
            // 野手9名を生成
            $batterNames = $this->generateRandomNames(9, 'batter');
            $playerNumber = 1;
            
            foreach ($batterNames as $index => $name) {
                $this->createDefaultBatter($user, $name, $playerNumber, $index + 1);
                $playerNumber++;
            }
            
            // 投手1名を生成
            $pitcherName = $this->generateRandomNames(1, 'pitcher')[0];
            $this->createDefaultPitcher($user, $pitcherName, $playerNumber);
        });
    }

    /**
     * デフォルト野手を作成
     */
    private function createDefaultBatter(User $user, string $name, int $playerNumber, int $battingOrder): Player
    {
        // 基本的な能力値（10-30の範囲でランダム - 弱めの初期選手）
        $basePower = rand(10, 30);
        $baseMeet = rand(10, 30);
        $baseSpeed = rand(10, 30);
        $baseDefense = rand(10, 30);
        $baseShoulder = rand(10, 30);
        $baseCatch = rand(10, 30);
        $trajectory = rand(1, 2); // 弾道1-2（弱め）
        
        // 既存のパラメータとの互換性のため、既存フィールドにも設定
        $power = $basePower;
        $meet = $baseMeet;
        $run = $baseSpeed; // runはspeedのエイリアスとして使用
        $defense = $baseDefense;
        
        return Player::create([
            'user_id' => $user->id,
            'player_number' => $playerNumber,
            'batting_order' => $battingOrder,
            'position' => $this->getDefaultPosition($battingOrder),
            'name' => $name,
            'condition' => 5,
            // 既存パラメータ（後方互換性）
            'power' => $power,
            'meet' => $meet,
            'run' => $run,
            'defense' => $defense,
            // 新パラメータ（パワプロ準拠）
            'trajectory' => $trajectory,
            'shoulder' => $baseShoulder,
            'catch' => $baseCatch,
            // 成長関連
            'age' => rand(18, 22),
            'years_pro' => 0,
            'growth_type' => $this->getRandomGrowthType(),
            // その他
            'player_type' => 0, // 野手
            'is_default' => true,
            'acquisition_cost' => 0,
        ]);
    }

    /**
     * デフォルト投手を作成
     */
    private function createDefaultPitcher(User $user, string $name, int $playerNumber): Player
    {
        // 基本的な能力値（10-30の範囲でランダム - 弱めの初期選手）
        $baseControl = rand(10, 30);
        $baseStamina = rand(10, 30);
        $baseVelocity = rand(10, 30);
        $baseDefense = rand(10, 30);
        $breakingBall = $this->getRandomBreakingBall();
        
        // 既存のパラメータとの互換性のため、既存フィールドにも設定
        $control = $baseControl;
        $fastball = $baseVelocity; // fastballはvelocityのエイリアスとして使用
        $changeup = rand(10, 25); // 簡易的な変化球能力（弱め）
        
        return Player::create([
            'user_id' => $user->id,
            'player_number' => $playerNumber,
            'batting_order' => 0, // 投手は打順なし
            'position' => 'P',
            'name' => $name,
            'condition' => 5,
            // 既存パラメータ（後方互換性）
            'power' => 0,
            'meet' => 0,
            'run' => 0,
            'defense' => $baseDefense,
            'control' => $control,
            'fastball' => $fastball,
            'changeup' => $changeup,
            // 新パラメータ（パワプロ準拠）
            'stamina' => $baseStamina,
            'velocity' => $baseVelocity,
            'breaking_ball' => $breakingBall,
            // 成長関連
            'age' => rand(18, 22),
            'years_pro' => 0,
            'growth_type' => $this->getRandomGrowthType(),
            // その他
            'player_type' => 1, // 投手
            'is_default' => true,
            'acquisition_cost' => 0,
        ]);
    }

    /**
     * ランダムな名前を生成
     */
    private function generateRandomNames(int $count, string $type): array
    {
        $names = [];
        $firstNames = ['太郎', '次郎', '三郎', '四郎', '五郎', '一郎', '二郎', '健', '太', '翔', '大輔', '翔太', '健太', '大介', '直樹'];
        $lastNames = ['山田', '佐藤', '鈴木', '高橋', '田中', '伊藤', '渡辺', '中村', '小林', '加藤', '吉田', '山本', '松本', '井上', '木村'];
        
        for ($i = 0; $i < $count; $i++) {
            $firstName = $firstNames[array_rand($firstNames)];
            $lastName = $lastNames[array_rand($lastNames)];
            $names[] = $lastName . $firstName;
        }
        
        return $names;
    }

    /**
     * デフォルト守備位置を取得
     */
    private function getDefaultPosition(int $battingOrder): string
    {
        $positions = [
            1 => 'CF', // 中堅手
            2 => '2B', // 二塁手
            3 => '3B', // 三塁手
            4 => '1B', // 一塁手
            5 => 'LF', // 左翼手
            6 => 'RF', // 右翼手
            7 => 'SS', // 遊撃手
            8 => 'C',  // 捕手
            9 => 'DH', // 指名打者
        ];
        
        return $positions[$battingOrder] ?? 'OF';
    }

    /**
     * ランダムな成長型を取得
     */
    private function getRandomGrowthType(): string
    {
        $types = ['early', 'normal', 'late'];
        return $types[array_rand($types)];
    }

    /**
     * ランダムな変化球を取得
     */
    private function getRandomBreakingBall(): string
    {
        $balls = ['G', 'F', 'C', 'S'];
        return $balls[array_rand($balls)];
    }
}

