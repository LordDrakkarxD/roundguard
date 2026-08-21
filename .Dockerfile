FROM php:8.4-cli

# Extensões necessárias
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libzip-dev nodejs npm \
    && docker-php-ext-install pdo pdo_pgsql zip \
    && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Copia os arquivos
COPY . .

# Instala dependências PHP
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instala e builda o frontend
RUN npm install && npm run build

# Permissões
RUN chmod -R 775 storage bootstrap/cache

# Porta do Railway
ENV PORT=8080
EXPOSE 8080

# Sobe o app
CMD php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=$PORT