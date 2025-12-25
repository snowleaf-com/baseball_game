<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('player_number')->comment('背番号');
            $table->integer('batting_order')->comment('打順');
            $table->string('position', 10)->nullable()->comment('守備位置');
            $table->string('name', 50)->comment('選手名');
            $table->integer('condition')->default(5)->comment('コンディション');
            $table->integer('power')->comment('パワー');
            $table->integer('meet')->comment('ミート');
            $table->integer('run')->comment('走力');
            $table->integer('defense')->comment('守備');
            $table->integer('at_bats')->default(0)->comment('打席');
            $table->integer('hits')->default(0)->comment('安打');
            $table->integer('runs')->default(0)->comment('得点');
            $table->integer('home_runs')->default(0)->comment('本塁打');
            $table->integer('stolen_bases')->default(0)->comment('盗塁');
            $table->integer('errors')->default(0)->comment('失策');
            $table->integer('four_balls')->default(0)->comment('四球');
            $table->integer('strikeouts')->default(0)->comment('三振');
            $table->integer('ground_into_double_play')->default(0)->comment('併殺打');
            $table->integer('player_type')->default(0)->comment('0:野手, 1:投手');
            $table->string('pitch_type')->nullable()->comment('投手タイプ');
            $table->integer('fastball')->nullable()->comment('速球');
            $table->integer('changeup')->nullable()->comment('チェンジアップ');
            $table->integer('control')->nullable()->comment('制球');
            $table->integer('pitching_wins')->default(0)->comment('投手勝利数');
            $table->integer('pitching_losses')->default(0)->comment('投手敗北数');
            $table->integer('innings_pitched')->default(0)->comment('投球回');
            $table->decimal('pitching_era', 5, 2)->default(0)->comment('防御率');
            $table->integer('strikeouts_pitched')->default(0)->comment('奪三振');
            $table->integer('walks_allowed')->default(0)->comment('与四球');
            $table->integer('home_runs_allowed')->default(0)->comment('被本塁打');
            $table->timestamps();
            $table->index(['user_id', 'batting_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};

