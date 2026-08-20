FROM php:8.2-cli

# Dépendances système nécessaires pour Laravel + extensions PHP courantes
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copier uniquement les fichiers de dépendances d'abord (meilleur cache Docker)
COPY composer.json composer.lock ./
RUN composer install --optimize-autoloader --no-dev --no-scripts --no-interaction

# Copier le reste du code
COPY . .

# Finaliser l'installation Composer (scripts, autoload)
RUN composer dump-autoload --optimize

# Render fournit le port via la variable $PORT — Laravel doit écouter dessus
EXPOSE 10000

CMD php artisan config:cache && php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}