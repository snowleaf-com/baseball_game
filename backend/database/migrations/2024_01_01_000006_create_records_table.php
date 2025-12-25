<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id();
            $table->string('record_type', 50)->comment('記録タイプ');
            $table->string('category', 50)->comment('カテゴリ');
            $table->decimal('value', 10, 2)->comment('記録値');
            $table->string('team_name', 50)->nullable()->comment('チーム名');
            $table->string('player_name', 50)->nullable()->comment('選手名');
            $table->string('saku', 50)->nullable()->comment('作成者名');
            $table->integer('league_number')->comment('リーグ回数');
            $table->timestamp('achieved_at')->comment('達成日時');
            $table->timestamps();
            $table->index(['record_type', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};

