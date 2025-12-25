#!/bin/bash

echo "Baseball Game セットアップを開始します..."

# Laravelプロジェクトのセットアップ
if [ ! -d "backend/vendor" ]; then
    echo "Laravelプロジェクトをセットアップしています..."
    cd backend
    composer install
    if [ ! -f ".env" ]; then
        cp .env.example .env
        php artisan key:generate
    fi
    cd ..
fi

# Reactプロジェクトのセットアップ
if [ ! -d "frontend/node_modules" ]; then
    echo "Reactプロジェクトをセットアップしています..."
    cd frontend
    # pnpmがインストールされていない場合はインストール
    if ! command -v pnpm &> /dev/null; then
        echo "pnpmをインストールしています..."
        npm install -g pnpm
    fi
    pnpm install
    cd ..
fi

# Dockerコンテナの起動
echo "Dockerコンテナを起動しています..."
docker-compose up -d --build

# データベースのマイグレーション
echo "データベースをマイグレーションしています..."
sleep 10  # データベースの起動を待つ
docker-compose exec -T app php artisan migrate --force

echo "セットアップが完了しました！"
echo "フロントエンド: http://localhost:3000"
echo "バックエンドAPI: http://localhost:8080/api"

