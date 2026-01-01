#!/bin/bash

echo "Baseball Game セットアップを開始します..."

# Laravelプロジェクトのセットアップ
if [ ! -d "backend/vendor" ]; then
    echo "Laravelプロジェクトをセットアップしています..."
    cd backend
    composer install
    cd ..
fi

# .envファイルの設定
if [ ! -f "backend/.env" ]; then
    echo ".envファイルを作成しています..."
    if [ -f ".env.docker" ]; then
        cp .env.docker backend/.env
        echo ".env.dockerからbackend/.envを作成しました"
    elif [ -f "env-docker.txt" ]; then
        cp env-docker.txt backend/.env
        echo "env-docker.txtからbackend/.envを作成しました"
    elif [ -f ".env.example" ]; then
        cp .env.example backend/.env
        echo ".env.exampleからbackend/.envを作成しました"
    elif [ -f "backend/.env.example" ]; then
        cp backend/.env.example backend/.env
        echo "backend/.env.exampleからbackend/.envを作成しました"
    fi
    
    if [ -f "backend/.env" ]; then
        cd backend
        php artisan key:generate
        cd ..
    fi
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

