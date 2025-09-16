FROM php:8.4-fpm

ARG USER
ARG UID
ARG GROUP
ARG GID

# Instalar dependências e extensões PHP em uma única camada
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libicu-dev \
    libxml2-dev \
    libxslt1-dev \
    zip \
    ca-certificates \
    curl \
    unzip \
    mariadb-client \
    libzip-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd intl opcache pdo pdo_mysql xml xsl zip \
    && pecl install redis-stable \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Criar usuário e grupo
RUN addgroup --gid $GID $GROUP \
    && adduser --disabled-password --gecos '' --uid $UID --gid $GID $USER

# Copiar configuração PHP
COPY ./docker/php-fpm/php.ini /usr/local/etc/php/

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Configurar diretório de trabalho e permissões
WORKDIR /var/www/html
RUN chown -R $USER:$GROUP /var/www/html

USER $USER

