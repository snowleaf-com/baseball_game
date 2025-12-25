<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('games', function (Blueprint $table) {
            $table->id();
            $table->foreignId('home_team_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('away_team_id')->constrained('users')->onDelete('cascade');
            $table->integer('home_score')->default(0)->comment('ホーム得点');
            $table->integer('away_score')->default(0)->comment('アウェイ得点');
            $table->text('game_log')->nullable()->comment('試合ログ（JSON形式）');
            $table->timestamp('played_at')->nullable()->comment('試合日時');
            $table->integer('league_day')->comment('リーグ日数');
            $table->integer('league_number')->comment('リーグ回数');
            $table->timestamps();
            $table->index(['home_team_id', 'away_team_id']);
            $table->index('played_at');
            $table->index('league_day');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('games');
    }
};

