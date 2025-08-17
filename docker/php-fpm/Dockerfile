FROM php:8.4-fpm

ARG USER
ARG UID
ARG GROUP
ARG GID

RUN addgroup --gid $GID $GROUP
RUN adduser --disabled-password --gecos '' --uid $UID --gid $GID $USER

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
    && docker-php-ext-enable redis

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

COPY ./docker/php-fpm/php.ini /usr/local/etc/php/

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

RUN chown -R www-data:www-data /var/www/html

USER $USER

