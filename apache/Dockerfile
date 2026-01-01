FROM httpd:2.4-alpine

# mod_rewriteとmod_proxy_fcgiを有効化
RUN sed -i \
    -e 's/^#\(LoadModule rewrite_module\)/\1/' \
    -e 's/^#\(LoadModule proxy_module\)/\1/' \
    -e 's/^#\(LoadModule proxy_fcgi_module\)/\1/' \
    /usr/local/apache2/conf/httpd.conf

# カスタム設定ファイルをコピー
COPY 000-default.conf /usr/local/apache2/conf/extra/000-default.conf

# カスタム設定を有効化
RUN echo "Include conf/extra/000-default.conf" >> /usr/local/apache2/conf/httpd.conf

# ポート80を公開
EXPOSE 80

CMD ["httpd", "-D", "FOREGROUND"]

