<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('budget')->default(2000000000)->after('camp_count')->comment('球団資金（初期値20億円）');
            $table->integer('year')->default(1)->after('budget')->comment('現在の年数');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['budget', 'year']);
        });
    }
};

