<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'saku',
        'password',
        'home_url',
        'team_name',
        'icon',
        'ip_address',
        'last_rank',
        'wins',
        'win_streak',
        'max_win_streak',
        'losses',
        'innings_offense',
        'innings_defense',
        'runs_scored',
        'runs_allowed',
        'era',
        'total_at_bats',
        'total_hits',
        'total_home_runs',
        'total_stolen_bases',
        'total_errors',
        'boss_type',
        'camp_count',
        'budget',
        'year',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        // boss_typeは文字列型（'offensive' | 'defensive' | 'balanced' | 'running'）なのでキャスト不要
        'password' => 'hashed',
        'budget' => 'integer',
        'year' => 'integer',
    ];

    public function players()
    {
        return $this->hasMany(Player::class);
    }

    public function homeGames()
    {
        return $this->hasMany(Game::class, 'home_team_id');
    }

    public function awayGames()
    {
        return $this->hasMany(Game::class, 'away_team_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function getGamesAttribute()
    {
        return $this->wins + $this->losses;
    }

    public function getWinRateAttribute()
    {
        $games = $this->games;
        return $games > 0 ? ($this->wins / $games) * 1000 : 0;
    }

    public function getBattingAverageAttribute()
    {
        return $this->total_at_bats > 0 
            ? ($this->total_hits / $this->total_at_bats) * 1000 
            : 0;
    }

    public function getRunsPerGameAttribute()
    {
        return $this->innings_offense > 0 
            ? ($this->runs_scored / $this->innings_offense) * 27 
            : 0;
    }
}

