FROM php:8.3-cli

RUN apt-get update && apt-get install -y git unzip libzip-dev libonig-dev libicu-dev zlib1g-dev \
    && docker-php-ext-install pdo pdo_mysql intl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
# Only create the Laravel project if the directory is empty (prevents build failures
# when overlay files are present in the build context).
RUN if [ ! -f /var/www/artisan ]; then \
            composer create-project laravel/laravel /tmp/laravel_src && \
            cp -a /tmp/laravel_src/. /var/www/ ; \
        else \
            echo "artisan exists, skipping create-project"; \
        fi
RUN composer require laravel/sanctum guzzlehttp/guzzle || true
# If artisan exists, publish Sanctum provider assets; otherwise skip.
RUN if [ -f artisan ]; then \
            php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider" --force || true; \
        else \
            echo "artisan not found, skipping vendor:publish"; \
        fi

COPY overlay/ /var/www/
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache || true

EXPOSE 8000
CMD php -S 0.0.0.0:8000 -t public
