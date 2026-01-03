<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeagueSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'league_number',
        'league_day',
        'league_start_date',
        'league_limit_days',
        'league_max_games',
        'league_update_hour',
        'game_interval_minutes',
    ];

    protected $casts = [
        'league_number' => 'integer',
        'league_day' => 'integer',
        'league_start_date' => 'datetime',
        'league_limit_days' => 'integer',
        'league_max_games' => 'integer',
        'league_update_hour' => 'integer',
        'game_interval_minutes' => 'integer',
    ];

    /**
     * シングルトンインスタンスを取得
     */
    public static function getInstance()
    {
        $setting = self::first();
        if (!$setting) {
            $setting = self::create([
                'league_number' => 1,
                'league_day' => 1,
                'league_limit_days' => 20,
                'league_max_games' => 160,
                'league_update_hour' => 12,
                'game_interval_minutes' => 30,
            ]);
        }
        return $setting;
    }
}


