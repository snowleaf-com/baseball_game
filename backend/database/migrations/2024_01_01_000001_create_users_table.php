<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('saku', 50)->comment('作成者名');
            $table->string('password')->comment('パスワード');
            $table->string('home_url')->nullable()->comment('ホームページURL');
            $table->string('team_name', 50)->comment('チーム名');
            $table->string('icon')->nullable()->comment('アイコン');
            $table->string('ip_address', 45)->nullable()->comment('IPアドレス');
            $table->integer('last_rank')->default(0)->comment('最終順位');
            $table->integer('wins')->default(0)->comment('勝利数');
            $table->integer('win_streak')->default(0)->comment('連勝数');
            $table->integer('max_win_streak')->default(0)->comment('最大連勝数');
            $table->integer('losses')->default(0)->comment('敗北数');
            $table->integer('innings_offense')->default(0)->comment('攻撃回数');
            $table->integer('innings_defense')->default(0)->comment('守備回数');
            $table->integer('runs_scored')->default(0)->comment('得点');
            $table->integer('runs_allowed')->default(0)->comment('失点');
            $table->decimal('era', 5, 2)->default(0)->comment('防御率');
            $table->integer('total_at_bats')->default(0)->comment('総打席');
            $table->integer('total_hits')->default(0)->comment('総安打');
            $table->integer('total_home_runs')->default(0)->comment('総本塁打');
            $table->integer('total_stolen_bases')->default(0)->comment('総盗塁');
            $table->integer('total_errors')->default(0)->comment('総失策');
            $table->string('boss_type')->nullable()->comment('ボスタイプ（JSON形式）');
            $table->integer('camp_count')->default(0)->comment('キャンプ使用回数');
            $table->timestamps();
            $table->index(['wins', 'losses']);
            $table->index('team_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

