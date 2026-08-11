# === 1) Étape de build (builder) ===
FROM php:8.3-fpm-alpine AS builder

# 1.1 Dépendances système pour PHP et Composer
RUN apk add --no-cache \
      git unzip curl libzip-dev oniguruma-dev libxml2-dev \
      libpng-dev jpeg-dev freetype-dev icu-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql zip bcmath gd intl \
 && apk add --no-cache --virtual .build-deps-pecl $PHPIZE_DEPS \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && apk del .build-deps-pecl

WORKDIR /var/www

# 1.2 Installer Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 1.3 Copier les fichiers de dépendances et installer les dépendances
COPY database/ database/
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --no-scripts

# 1.4 Copier le reste du code de l'application
COPY . .

# === 2) Étape de production ===
FROM php:8.3-fpm-alpine

# 2.1 Extensions d’exécution
RUN apk add --no-cache \
      libzip-dev icu-dev libxml2-dev oniguruma-dev \
      libpng-dev jpeg-dev freetype-dev icu-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install pdo_mysql zip bcmath gd intl opcache \
 && apk add --no-cache --virtual .build-deps-pecl $PHPIZE_DEPS \
 && pecl install redis \
 && docker-php-ext-enable redis \
 && apk del .build-deps-pecl \
 && mv "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

WORKDIR /var/www

# 2.2 Copier le code et les dépendances depuis le builder
COPY --from=builder /usr/bin/composer /usr/bin/composer
COPY --from=builder /var/www /var/www
COPY --from=builder /usr/local/lib/php/extensions/ /usr/local/lib/php/extensions/
COPY --from=builder /usr/local/etc/php/conf.d/ /usr/local/etc/php/conf.d/

# 2.3 Copier le script d'entrée et le rendre exécutable
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# 2.4 Permissions et utilisateur
RUN chown -R www-data:www-data /var/www
USER www-data

EXPOSE 9000
ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
