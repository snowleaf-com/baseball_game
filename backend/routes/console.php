<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

// 自動試合スケジューラー
// 試合間隔に応じて実行頻度を設定（デフォルトは30分ごと）
Schedule::command('game:auto-play')
    ->everyMinute() // 毎分チェック（実際の実行はコマンド内で間隔チェック）
    ->withoutOverlapping() // 重複実行を防止
    ->runInBackground(); // バックグラウンドで実行

