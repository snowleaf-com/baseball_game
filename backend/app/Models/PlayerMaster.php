<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerMaster extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'player_type',
        'base_trajectory',
        'base_meet',
        'base_power',
        'base_speed',
        'base_shoulder',
        'base_defense',
        'base_catch',
        'base_control',
        'base_stamina',
        'base_velocity',
        'base_breaking_ball',
        'age',
        'growth_type',
        'default_cost',
        'is_default',
    ];

    protected $casts = [
        'player_type' => 'integer',
        'base_trajectory' => 'integer',
        'base_meet' => 'integer',
        'base_power' => 'integer',
        'base_speed' => 'integer',
        'base_shoulder' => 'integer',
        'base_defense' => 'integer',
        'base_catch' => 'integer',
        'base_control' => 'integer',
        'base_stamina' => 'integer',
        'base_velocity' => 'integer',
        'age' => 'integer',
        'default_cost' => 'integer',
        'is_default' => 'boolean',
    ];

    public function isBatter()
    {
        return $this->player_type === 0;
    }

    public function isPitcher()
    {
        return $this->player_type === 1;
    }
}


