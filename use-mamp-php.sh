#!/bin/bash
# MAMPのPHP 8.3.28をPATHに追加するスクリプト
# 使用方法: source use-mamp-php.sh

export PATH="/Applications/MAMP/bin/php/php8.3.28/bin:$PATH"
export SSL_CERT_FILE="/Applications/MAMP/Library/OpenSSL/certs/cacert.pem"
export REQUESTS_CA_BUNDLE="/Applications/MAMP/Library/OpenSSL/certs/cacert.pem"

echo "✅ MAMP PHP 8.3.28がPATHに追加されました"
php -v

