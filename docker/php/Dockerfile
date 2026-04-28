FROM php:8.4-fpm

# Instalar dependências do sistema
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Instalar extensões PHP
RUN docker-php-ext-install \
    pdo \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    xml \
    opcache

# Instalar extensão Redis
RUN pecl install redis \
    && docker-php-ext-enable redis

# Configurar opcache
RUN echo "opcache.enable=1" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.memory_consumption=256" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.max_accelerated_files=20000" >> /usr/local/etc/php/conf.d/opcache.ini \
    && echo "opcache.revalidate_freq=0" >> /usr/local/etc/php/conf.d/opcache.ini

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Criar usuário para rodar a aplicação
RUN groupadd -g 1000 www \
    && useradd -u 1000 -ms /bin/bash -g www www

# Definir diretório de trabalho
WORKDIR /var/www

# Pool FPM como usuário www (UID 1000) — mesmo dono típico dos arquivos em WSL/Linux ao montar ./src
COPY zzz-www-pool.conf /usr/local/etc/php-fpm.d/zzz-www-pool.conf

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint-laravel.sh
RUN chmod +x /usr/local/bin/docker-entrypoint-laravel.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/docker-entrypoint-laravel.sh"]
CMD ["php-fpm"]
