<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('players', function (Blueprint $table) {
            // 野手パラメータ（パワプロ準拠）
            $table->integer('trajectory')->nullable()->after('defense')->comment('弾道（1-4）');
            $table->integer('shoulder')->nullable()->after('trajectory')->comment('肩力（1-255）');
            $table->integer('catch')->nullable()->after('shoulder')->comment('捕球（1-255）');
            
            // 投手パラメータ（パワプロ準拠）
            $table->integer('stamina')->nullable()->after('control')->comment('スタミナ（1-255）');
            $table->integer('velocity')->nullable()->after('stamina')->comment('球速（1-255）');
            $table->string('breaking_ball', 10)->nullable()->after('velocity')->comment('変化球（G, F, C, Sなど）');
            
            // 成長・年数関連
            $table->integer('age')->nullable()->after('name')->comment('年齢');
            $table->integer('years_pro')->default(0)->after('age')->comment('プロ年数');
            $table->string('growth_type', 20)->nullable()->after('years_pro')->comment('成長型（early, normal, late）');
            
            // その他
            $table->integer('acquisition_cost')->nullable()->after('growth_type')->comment('獲得時の費用');
            $table->boolean('is_default')->default(false)->after('acquisition_cost')->comment('デフォルト選手フラグ');
            
            // 既存のrunをspeedにリネーム（後方互換性のため両方保持）
            // 既存のmeet, power, defenseはそのまま保持（後方互換性）
        });
    }

    public function down(): void
    {
        Schema::table('players', function (Blueprint $table) {
            $table->dropColumn([
                'trajectory',
                'shoulder',
                'catch',
                'stamina',
                'velocity',
                'breaking_ball',
                'age',
                'years_pro',
                'growth_type',
                'acquisition_cost',
                'is_default',
            ]);
        });
    }
};


