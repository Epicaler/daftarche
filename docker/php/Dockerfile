# Runtime only: PHP + extensions + Composer. The application code is bind-mounted, never copied in.
FROM php:8.4-fpm-alpine

ARG UID=1000
ARG GID=1000

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

RUN install-php-extensions pdo_mysql bcmath intl opcache zip pcntl \
    && apk add --no-cache mariadb-client tzdata su-exec shadow git unzip \
    # Run PHP as the host user so files created in storage/ stay owned by you
    && groupmod -o -g ${GID} www-data \
    && usermod -o -u ${UID} -g ${GID} www-data

ENV TZ=Asia/Tehran \
    COMPOSER_HOME=/tmp/composer
COPY php.ini /usr/local/etc/php/conf.d/zz-app.ini
COPY entrypoint.sh /usr/local/bin/entrypoint
RUN chmod +x /usr/local/bin/entrypoint

WORKDIR /var/www/html
ENTRYPOINT ["entrypoint"]
CMD ["php-fpm"]
