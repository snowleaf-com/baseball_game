<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'player_number',
        'batting_order',
        'position',
        'name',
        'condition',
        'power',
        'meet',
        'run',
        'defense',
        'at_bats',
        'hits',
        'runs',
        'home_runs',
        'stolen_bases',
        'errors',
        'four_balls',
        'strikeouts',
        'ground_into_double_play',
        'player_type',
        'pitch_type',
        'fastball',
        'changeup',
        'control',
        'pitching_wins',
        'pitching_losses',
        'innings_pitched',
        'pitching_era',
        'strikeouts_pitched',
        'walks_allowed',
        'home_runs_allowed',
        // 新パラメータ（パワプロ準拠）
        'trajectory',
        'shoulder',
        'catch',
        'stamina',
        'velocity',
        'breaking_ball',
        // 成長・年数関連
        'age',
        'years_pro',
        'growth_type',
        // その他
        'acquisition_cost',
        'is_default',
    ];

    protected $casts = [
        'condition' => 'integer',
        'power' => 'integer',
        'meet' => 'integer',
        'run' => 'integer',
        'defense' => 'integer',
        'pitching_era' => 'decimal:2',
        // 新パラメータ
        'trajectory' => 'integer',
        'shoulder' => 'integer',
        'catch' => 'integer',
        'stamina' => 'integer',
        'velocity' => 'integer',
        'age' => 'integer',
        'years_pro' => 'integer',
        'acquisition_cost' => 'integer',
        'is_default' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getBattingAverageAttribute()
    {
        return $this->at_bats > 0 
            ? ($this->hits / $this->at_bats) * 1000 
            : 0;
    }

    /**
     * 走力（speed）のアクセサ
     * 既存のrunフィールドとの互換性を保つ
     */
    public function getSpeedAttribute()
    {
        // 新しいspeedパラメータがあればそれを使用、なければ既存のrunを使用
        return $this->attributes['speed'] ?? $this->run ?? 0;
    }

    /**
     * 総合パラメータ（既存方式）
     */
    public function getTotalParameterAttribute()
    {
        if ($this->player_type === 0) {
            return $this->power + $this->meet + $this->run + $this->defense;
        } else {
            return ($this->fastball ?? 0) + ($this->changeup ?? 0) + ($this->control ?? 0) + $this->defense;
        }
    }

    /**
     * 総合パラメータ（パワプロ準拠方式 - 野手）
     */
    public function getTotalBatterParameterAttribute()
    {
        if (!$this->isBatter()) {
            return 0;
        }
        
        return ($this->trajectory ?? 0) * 10 + // 弾道は1-4なので10倍
               ($this->meet ?? 0) +
               ($this->power ?? 0) +
               ($this->speed ?? 0) +
               ($this->shoulder ?? 0) +
               ($this->defense ?? 0) +
               ($this->catch ?? 0);
    }

    /**
     * 総合パラメータ（パワプロ準拠方式 - 投手）
     */
    public function getTotalPitcherParameterAttribute()
    {
        if (!$this->isPitcher()) {
            return 0;
        }
        
        return ($this->control ?? 0) +
               ($this->stamina ?? 0) +
               ($this->velocity ?? 0) +
               ($this->defense ?? 0);
    }

    public function isBatter()
    {
        return $this->player_type === 0;
    }

    public function isPitcher()
    {
        return $this->player_type === 1;
    }
}

