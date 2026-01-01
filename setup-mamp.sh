#!/bin/bash

set -e  # エラーが発生したら停止

echo "=========================================="
echo "Baseball Game MAMP用セットアップを開始します"
echo "=========================================="
echo ""

# 必要なツールの確認
echo "📋 必要なツールを確認しています..."

# PHPの確認（MAMPのPHPを優先）- Composerより先に確認
PHP_BIN=""
# MAMPのPHPを検索（PHP 8.3を優先、なければ8.2、8.1の順）
if [ -d "/Applications/MAMP/bin/php" ]; then
    # PHP 8.3を優先的に探す
    if [ -f "/Applications/MAMP/bin/php/php8.3.0/bin/php" ]; then
        PHP_BIN="/Applications/MAMP/bin/php/php8.3.0/bin/php"
        echo "✅ MAMP PHP 8.3.0 が見つかりました: $PHP_BIN"
    else
        # 8.3.xの他のバージョンを探す
        MAMP_PHP_83=$(find /Applications/MAMP/bin/php -name "php" -type f -path "*/php8.3.*/bin/php" 2>/dev/null | sort -V | head -1)
        if [ -n "$MAMP_PHP_83" ] && [ -f "$MAMP_PHP_83" ]; then
            PHP_BIN="$MAMP_PHP_83"
            PHP_VERSION_NAME=$(echo "$MAMP_PHP_83" | sed -E 's|.*/php([0-9.]+)/bin/php|\1|')
            echo "✅ MAMP PHP $PHP_VERSION_NAME が見つかりました: $PHP_BIN"
        else
            # 8.2を探す
            MAMP_PHP_82=$(find /Applications/MAMP/bin/php -name "php" -type f -path "*/php8.2.*/bin/php" 2>/dev/null | sort -V | head -1)
            if [ -n "$MAMP_PHP_82" ] && [ -f "$MAMP_PHP_82" ]; then
                PHP_BIN="$MAMP_PHP_82"
                PHP_VERSION_NAME=$(echo "$MAMP_PHP_82" | sed -E 's|.*/php([0-9.]+)/bin/php|\1|')
                echo "✅ MAMP PHP $PHP_VERSION_NAME が見つかりました: $PHP_BIN"
                echo "   ⚠️  PHP 8.3以上が推奨されています"
            else
                # 8.1を探す
                MAMP_PHP_81=$(find /Applications/MAMP/bin/php -name "php" -type f -path "*/php8.1.*/bin/php" 2>/dev/null | sort -V | head -1)
                if [ -n "$MAMP_PHP_81" ] && [ -f "$MAMP_PHP_81" ]; then
                    PHP_BIN="$MAMP_PHP_81"
                    PHP_VERSION_NAME=$(echo "$MAMP_PHP_81" | sed -E 's|.*/php([0-9.]+)/bin/php|\1|')
                    echo "✅ MAMP PHP $PHP_VERSION_NAME が見つかりました: $PHP_BIN"
                    echo "   ⚠️  PHP 8.3以上が推奨されています"
                fi
            fi
        fi
    fi
fi

# MAMPのPHPが見つからない場合、システムのPHPを確認
if [ -z "$PHP_BIN" ]; then
    if command -v php &> /dev/null; then
        PHP_BIN="php"
        echo "✅ システムのPHPを使用します"
    else
        echo "⚠️  PHPが見つかりませんでした"
        echo "   MAMPをインストールして、PHP 8.3を選択してください"
        echo "   または、システムにPHPをインストールしてください"
        PHP_BIN=""  # 空のままにしておく
    fi
fi

# Composerの確認（PHPが見つかってから実行）
if ! command -v composer &> /dev/null; then
    echo "❌ Composerがインストールされていません"
    echo "   https://getcomposer.org/ からインストールしてください"
    exit 1
fi

# Composerのバージョン表示（PHPが見つかっている場合のみ）
if [ -n "$PHP_BIN" ]; then
    COMPOSER_VERSION=$($PHP_BIN $(which composer) --version 2>/dev/null | head -n 1 || composer --version 2>/dev/null | head -n 1 || echo "インストール済み")
    echo "✅ Composer: $COMPOSER_VERSION"
else
    echo "✅ Composer: インストール済み（PHPが見つからないためバージョン確認をスキップ）"
fi

# PHPバージョンの確認
if [ -n "$PHP_BIN" ]; then
    # フルパスの場合とコマンド名の場合の両方に対応
    if [ -f "$PHP_BIN" ] || command -v "$PHP_BIN" &> /dev/null; then
        PHP_VERSION=$($PHP_BIN -r 'echo PHP_VERSION;' 2>/dev/null || echo "unknown")
        if [ "$PHP_VERSION" != "unknown" ]; then
            echo "   PHPバージョン: $PHP_VERSION"
            PHP_MAJOR=$(echo $PHP_VERSION | cut -d. -f1)
            PHP_MINOR=$(echo $PHP_VERSION | cut -d. -f2)
            if [ "$PHP_MAJOR" -lt 8 ] || ([ "$PHP_MAJOR" -eq 8 ] && [ "$PHP_MINOR" -lt 3 ]); then
                echo "   ⚠️  警告: PHP 8.3以上が推奨されています（現在: $PHP_VERSION）"
            fi
        fi
    fi
fi

if ! command -v pnpm &> /dev/null; then
    echo "📦 pnpmをインストールしています..."
    if command -v npm &> /dev/null; then
        npm install -g pnpm
    else
        echo "❌ npmがインストールされていません"
        echo "   Node.jsをインストールしてください: https://nodejs.org/"
        exit 1
    fi
fi
echo "✅ pnpm: $(pnpm --version)"

echo ""

# Laravelプロジェクトのセットアップ
if [ ! -d "backend/vendor" ]; then
    echo "📦 Laravelプロジェクトをセットアップしています..."
    cd backend
    
    # MAMPのPHPを使う場合、composerもMAMPのPHPを使う
    if [ -n "$PHP_BIN" ] && [ "$PHP_BIN" != "php" ]; then
        # composerのパスを取得（複数の可能性をチェック）
        COMPOSER_BIN=""
        if command -v composer &> /dev/null; then
            COMPOSER_BIN=$(which composer)
        elif [ -f "/usr/local/bin/composer" ]; then
            COMPOSER_BIN="/usr/local/bin/composer"
        elif [ -f "$HOME/.composer/vendor/bin/composer" ]; then
            COMPOSER_BIN="$HOME/.composer/vendor/bin/composer"
        elif [ -f "$HOME/.config/composer/vendor/bin/composer" ]; then
            COMPOSER_BIN="$HOME/.config/composer/vendor/bin/composer"
        fi
        
        if [ -n "$COMPOSER_BIN" ] && [ -f "$COMPOSER_BIN" ]; then
            echo "   MAMPのPHP ($PHP_BIN) を使用してcomposerを実行します"
            # Deprecation Noticeを抑制してcomposerを実行（エラーは表示）
            if $PHP_BIN "$COMPOSER_BIN" install 2>&1 | grep -v "^Deprecation Notice:" | grep -v "^Deprecated:"; then
                COMPOSER_EXIT=0
            else
                COMPOSER_EXIT=${PIPESTATUS[0]}
            fi
            if [ $COMPOSER_EXIT -ne 0 ]; then
                echo ""
                echo "❌ composer installが失敗しました（終了コード: $COMPOSER_EXIT）"
                echo "   依存関係の競合が発生している可能性があります"
                echo "   composer.jsonのバージョンを確認してください"
                cd ..
                exit $COMPOSER_EXIT
            fi
        else
            echo "   ⚠️  composerのパスが見つかりません。通常のcomposerコマンドを試します"
            # 環境変数でPHPを指定してcomposerを実行
            export PHP_BIN="$PHP_BIN"
            if composer install 2>&1 | grep -v "^Deprecation Notice:" | grep -v "^Deprecated:"; then
                COMPOSER_EXIT=0
            else
                COMPOSER_EXIT=${PIPESTATUS[0]}
            fi
            if [ $COMPOSER_EXIT -ne 0 ]; then
                echo ""
                echo "❌ composer installが失敗しました（終了コード: $COMPOSER_EXIT）"
                echo "   依存関係の競合が発生している可能性があります"
                echo "   composer.jsonのバージョンを確認してください"
                cd ..
                exit $COMPOSER_EXIT
            fi
        fi
    else
        composer install
    fi
    cd ..
else
    echo "✅ Laravel依存関係は既にインストール済みです"
fi

# .envファイルの設定
if [ ! -f "backend/.env" ]; then
    echo "📝 .envファイルを作成しています..."
    if [ -f ".env.mamp" ]; then
        cp .env.mamp backend/.env
        echo "✅ .env.mampからbackend/.envを作成しました"
    elif [ -f ".env.mamp.example" ]; then
        cp .env.mamp.example backend/.env
        echo "✅ .env.mamp.exampleからbackend/.envを作成しました"
    elif [ -f "env-mamp.txt" ]; then
        cp env-mamp.txt backend/.env
        echo "✅ env-mamp.txtからbackend/.envを作成しました"
    elif [ -f ".env.example" ]; then
        cp .env.example backend/.env
        echo "✅ .env.exampleからbackend/.envを作成しました"
        echo "   ⚠️  データベース接続情報をMAMP用に編集してください"
    elif [ -f "backend/.env.example" ]; then
        cp backend/.env.example backend/.env
        echo "✅ backend/.env.exampleからbackend/.envを作成しました"
        echo "   ⚠️  データベース接続情報をMAMP用に編集してください"
    else
        # .env.mampファイルが存在しない場合は、デフォルトの内容を作成
        cat > backend/.env << 'ENVEOF'
APP_NAME="Baseball Game"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8888

LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# データベース設定（MAMP用）
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=8889
DB_DATABASE=baseball_game
DB_USERNAME=root
DB_PASSWORD=root

# Redis設定（MAMP用 - オプション）
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# キャッシュ・セッション設定（Redisを使わない場合はfileに変更）
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
QUEUE_CONNECTION=sync

# メール設定
MAIL_MAILER=smtp
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@baseball-game.local"
MAIL_FROM_NAME="${APP_NAME}"

# フロントエンド設定
REACT_APP_API_URL=http://localhost:8888/api
ENVEOF
        echo "✅ デフォルトのbackend/.envを作成しました"
    fi
    
    # アプリケーションキーの生成
    if [ -f "backend/.env" ]; then
        echo "🔑 アプリケーションキーを生成しています..."
        if [ -f "backend/artisan" ]; then
            cd backend
            if [ -n "$PHP_BIN" ] && [ "$PHP_BIN" != "php" ]; then
                $PHP_BIN artisan key:generate
            else
                php artisan key:generate
            fi
            cd ..
        else
            echo "   ⚠️  artisanファイルが見つかりません"
            echo "   composer installが正常に完了していない可能性があります"
        fi
    fi
else
    echo "✅ backend/.envは既に存在します"
fi

# Reactプロジェクトのセットアップ
if [ ! -d "frontend/node_modules" ]; then
    echo "📦 Reactプロジェクトをセットアップしています..."
    cd frontend
    pnpm install
    cd ..
else
    echo "✅ React依存関係は既にインストール済みです"
fi

# Redisの確認（オプション）
if ! command -v redis-server &> /dev/null; then
    echo ""
    echo "⚠️  Redisがインストールされていません（オプション）"
    echo "   Redisを使う場合は、以下でインストールしてください:"
    echo "   brew install redis"
    echo "   brew services start redis"
    echo ""
    echo "   Redisを使わない場合は、backend/.envで以下を設定してください:"
    echo "   CACHE_DRIVER=file"
    echo "   SESSION_DRIVER=file"
else
    echo "✅ Redis: $(redis-server --version | head -n 1)"
    if ! pgrep -x "redis-server" > /dev/null; then
        echo "⚠️  Redisサーバーが起動していません"
        echo "   起動するには: brew services start redis"
    else
        echo "✅ Redisサーバーは起動中です"
    fi
fi

# .htaccessファイルの確認
if [ ! -f "backend/public/.htaccess" ]; then
    echo "📝 .htaccessファイルを作成しています..."
    cat > backend/public/.htaccess << 'EOF'
<IfModule mod_rewrite.c>
    <IfModule mod_negotiation.c>
        Options -MultiViews -Indexes
    </IfModule>

    RewriteEngine On

    # Handle Authorization Header
    RewriteCond %{HTTP:Authorization} .
    RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]

    # Redirect Trailing Slashes If Not A Folder...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_URI} (.+)/$
    RewriteRule ^ %1 [L,R=301]

    # Send Requests To Front Controller...
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>
EOF
    echo "✅ .htaccessファイルを作成しました"
else
    echo "✅ .htaccessファイルは既に存在します"
fi

echo ""
echo "=========================================="
echo "✅ セットアップが完了しました！"
echo "=========================================="
echo ""
echo "📋 次のステップ:"
echo ""
echo "1. MAMPを起動して、PHP 8.3を選択してください"
echo ""
echo "2. MAMPのドキュメントルートを設定:"
echo ""
echo "   方法A: プロジェクトをMAMPのhtdocsに配置（推奨）"
echo "   - プロジェクトを /Applications/MAMP/htdocs/baseball_game に移動またはコピー:"
echo "     cp -r $(pwd) /Applications/MAMP/htdocs/baseball_game"
echo "   - または、シンボリックリンクを作成:"
echo "     ln -s $(pwd) /Applications/MAMP/htdocs/baseball_game"
echo "   - ブラウザで以下にアクセス:"
echo "     http://localhost:8888/baseball_game/backend/public/api"
echo ""
echo "   方法B: backend/publicのみシンボリックリンクで配置"
echo "   - ターミナルで以下のコマンドを実行:"
echo "     ln -s $(pwd)/backend/public /Applications/MAMP/htdocs/baseball-game"
echo "   - その後、ブラウザで http://localhost:8888/baseball-game/api にアクセス"
echo ""
echo "3. MySQLデータベースを作成:"
echo "   - phpMyAdminにアクセス: http://localhost/phpMyAdmin"
echo "   - データベース名: baseball_game を作成"
echo ""
echo "4. backend/.envファイルを確認・編集:"
echo "   - DB_DATABASE, DB_USERNAME, DB_PASSWORD を確認"
echo "   - Redisを使わない場合は CACHE_DRIVER=file, SESSION_DRIVER=file に変更"
echo ""
echo "5. データベースマイグレーションを実行:"
echo "   cd backend"
if [ -n "$PHP_BIN" ] && [ "$PHP_BIN" != "php" ]; then
    echo "   $PHP_BIN artisan migrate"
    echo "   または、MAMPのPHPパスを直接指定:"
    echo "   /Applications/MAMP/bin/php/php8.3.0/bin/php artisan migrate"
else
    echo "   php artisan migrate"
fi
echo ""
echo "6. フロントエンドを起動:"
echo "   cd frontend"
echo "   pnpm start"
echo ""
echo "🌐 アクセスURL:"
echo "   バックエンドAPI:"
echo "     - htdocsにプロジェクト全体を配置: http://localhost:8888/baseball_game/backend/public/api"
echo "     - backend/publicのみシンボリックリンク: http://localhost:8888/baseball-game/api"
echo "   フロントエンド: http://localhost:3000"
echo ""

