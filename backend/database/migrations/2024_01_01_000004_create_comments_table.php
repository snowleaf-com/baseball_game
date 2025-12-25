<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_system')->default(false)->comment('システムコメントか');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('saku', 50)->nullable()->comment('作成者名');
            $table->string('home_url')->nullable()->comment('ホームページURL');
            $table->text('comment')->comment('コメント内容');
            $table->string('game_result')->nullable()->comment('試合結果');
            $table->timestamps();
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};

