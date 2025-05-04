FROM php:8.2-fpm

# 1) Instalamos dependencias de sistema y limpiamos cache
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        build-essential \
        libpng-dev \
        libjpeg62-turbo-dev \
        libfreetype6-dev \
        libonig-dev \
        libzip-dev \
        zip \
        unzip \
        git \
        curl && \
    rm -rf /var/lib/apt/lists/*

# 2) Configuramos y compilamos extensiones PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip

# 3) Copiamos Composer desde la imagen oficial
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# 4) Creamos usuario no-root y ajustamos permisos
RUN addgroup --system app \
 && adduser --system --ingroup app app \
 && chown -R app:app /var/www

WORKDIR /var/www

# 5) Cache de Composer: copiamos primero composer.json y composer.lock
COPY composer.json composer.lock ./

# 6) Instalamos dependencias de Composer
RUN composer install --no-dev --optimize-autoloader --no-progress --no-interaction

# 7) Copiamos el resto de la aplicación
COPY . .

# 8) Exponemos el puerto 8000
EXPOSE 8000

# 9) Cambiamos al usuario no-root y arrancamos el servidor embebido
USER app
CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]