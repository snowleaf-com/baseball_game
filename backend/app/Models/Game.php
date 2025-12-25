<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'home_team_id',
        'away_team_id',
        'home_score',
        'away_score',
        'game_log',
        'played_at',
        'league_day',
        'league_number',
    ];

    protected $casts = [
        'game_log' => 'array',
        'played_at' => 'datetime',
        'league_day' => 'integer',
        'league_number' => 'integer',
    ];

    public function homeTeam()
    {
        return $this->belongsTo(User::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(User::class, 'away_team_id');
    }

    public function getWinnerAttribute()
    {
        if ($this->home_score > $this->away_score) {
            return $this->homeTeam;
        } elseif ($this->away_score > $this->home_score) {
            return $this->awayTeam;
        }
        return null;
    }

    public function getLoserAttribute()
    {
        if ($this->home_score < $this->away_score) {
            return $this->homeTeam;
        } elseif ($this->away_score < $this->home_score) {
            return $this->awayTeam;
        }
        return null;
    }
}

