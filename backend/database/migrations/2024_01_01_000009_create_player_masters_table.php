<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('player_masters', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('選手名');
            $table->integer('player_type')->default(0)->comment('0:野手, 1:投手');
            
            // 野手パラメータ
            $table->integer('base_trajectory')->nullable()->comment('弾道（1-4）');
            $table->integer('base_meet')->nullable()->comment('ミート（1-255）');
            $table->integer('base_power')->nullable()->comment('パワー（1-255）');
            $table->integer('base_speed')->nullable()->comment('走力（1-255）');
            $table->integer('base_shoulder')->nullable()->comment('肩力（1-255）');
            $table->integer('base_defense')->nullable()->comment('守備力（1-255）');
            $table->integer('base_catch')->nullable()->comment('捕球（1-255）');
            
            // 投手パラメータ
            $table->integer('base_control')->nullable()->comment('コントロール（1-255）');
            $table->integer('base_stamina')->nullable()->comment('スタミナ（1-255）');
            $table->integer('base_velocity')->nullable()->comment('球速（1-255）');
            $table->string('base_breaking_ball', 10)->nullable()->comment('変化球（G, F, C, Sなど）');
            
            // 成長関連
            $table->integer('age')->comment('年齢');
            $table->string('growth_type', 20)->default('normal')->comment('成長型（early, normal, late）');
            
            // コスト
            $table->integer('default_cost')->default(0)->comment('デフォルト選手の場合は0、獲得選手の場合は獲得費用');
            $table->boolean('is_default')->default(false)->comment('デフォルト選手フラグ');
            
            $table->timestamps();
            $table->index(['player_type', 'is_default']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('player_masters');
    }
};


