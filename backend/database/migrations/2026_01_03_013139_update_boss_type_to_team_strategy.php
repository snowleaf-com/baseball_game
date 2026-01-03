<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // コメントを更新（データ型はstringのまま）
        });

        // 既存のJSON形式のboss_typeを文字列形式に変換
        DB::table('users')->whereNotNull('boss_type')->get()->each(function ($user) {
            $bossType = json_decode($user->boss_type, true);
            if (is_array($bossType)) {
                // 既存データは'balanced'に変換
                DB::table('users')
                    ->where('id', $user->id)
                    ->update(['boss_type' => 'balanced']);
            }
        });

        // NULLの場合は'balanced'に設定
        DB::table('users')
            ->whereNull('boss_type')
            ->update(['boss_type' => 'balanced']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // ダウンマイグレーション時は元のJSON形式に戻す（簡易版）
        DB::table('users')->update([
            'boss_type' => json_encode([
                'b_act' => 5,
                'b_bnt' => 5,
                'b_ste' => 5,
                'b_mnd' => 5,
            ])
        ]);
    }
};
