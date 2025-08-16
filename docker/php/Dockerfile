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

# Composerのインストール
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
  && php -r "if (hash_file('sha384', 'composer-setup.php') === 'dac665fdc30fdd8ec78b38b9800061b4150413ff2e3b6f88543c636f7cd84f6db9189d43a81e5503cda447da73c7e5b6') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
  && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
  && php -r "unlink('composer-setup.php');" 

RUN curl -fsSL https://deb.nodesource.com/setup_22.x | bash - \
  && apt-get install -y nodejs

# ユーザーを作成する
RUN useradd -m -u 1000 -g www-data app

USER app
RUN composer global require laravel/installer

# Laravel installerのPATHを通す
ENV PATH="$PATH:/home/app/.composer/vendor/bin:/home/app/.config/composer/vendor/bin"

WORKDIR /var/www/html