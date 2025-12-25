<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'is_system',
        'user_id',
        'saku',
        'home_url',
        'comment',
        'game_result',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

