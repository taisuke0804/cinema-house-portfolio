FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    vim \
  && apt-get clean \
  && docker-php-ext-install pdo_mysql mbstring zip gd

# Composerのインストール（公式イメージからコピー）
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
  && apt-get install -y nodejs

# ユーザーを作成する
RUN useradd -m -u 1000 -g www-data app

USER app
RUN composer global require laravel/installer

# Laravel installerのPATHを通す
ENV PATH="$PATH:/home/app/.composer/vendor/bin:/home/app/.config/composer/vendor/bin"

WORKDIR /var/www/html