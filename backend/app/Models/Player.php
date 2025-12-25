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
    ];

    protected $casts = [
        'condition' => 'integer',
        'power' => 'integer',
        'meet' => 'integer',
        'run' => 'integer',
        'defense' => 'integer',
        'pitching_era' => 'decimal:2',
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

    public function getTotalParameterAttribute()
    {
        if ($this->player_type === 0) {
            return $this->power + $this->meet + $this->run + $this->defense;
        } else {
            return $this->fastball + $this->changeup + $this->control + $this->defense;
        }
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

