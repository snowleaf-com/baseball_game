<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('league_settings', function (Blueprint $table) {
            $table->id();
            $table->integer('league_number')->default(1)->comment('リーグ回数');
            $table->integer('league_day')->default(1)->comment('リーグ日数');
            $table->timestamp('league_start_date')->nullable()->comment('リーグ開始日');
            $table->integer('league_limit_days')->default(20)->comment('リーグ期間（日）');
            $table->integer('league_max_games')->default(160)->comment('リーグ最大試合数');
            $table->integer('league_update_hour')->default(12)->comment('リーグ更新時刻（時）');
            $table->integer('game_interval_minutes')->default(30)->comment('試合間隔（分）');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('league_settings');
    }
};

