# Image test untuk branch 6.4. Dipakai oleh docker/test.sh (matrix PHP x Symfony 6.4).
ARG PHP_VERSION=8.1
FROM php:${PHP_VERSION}-cli

RUN apt-get -o Acquire::Retries=5 update \
    && apt-get -o Acquire::Retries=5 install -y --no-install-recommends git unzip libpng-dev libjpeg62-turbo-dev libfreetype6-dev libsqlite3-dev libzip-dev libicu-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd pdo_sqlite zip intl bcmath \
    && rm -rf /var/lib/apt/lists/* \
    && git config --global --add safe.directory '*'

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# symfony/flex global agar SYMFONY_REQUIRE membatasi versi symfony/* saat composer update
RUN composer global config --no-plugins allow-plugins.symfony/flex true \
    && composer global require --no-interaction --no-progress 'symfony/flex:^2'

WORKDIR /app
