FROM php:8.0-fpm-alpine3.13

# Arguments defined in docker-compose.yml
ARG user
ARG uid

# Install system dependencies
RUN apk update && \
  apk upgrade && \
  apk add --no-cache \
  libjpeg-turbo-dev \
  freetype-dev \
  libwebp-dev \
  libpng-dev \
  oniguruma-dev \
  libzip-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
  && docker-php-ext-install -j$(nproc) gd pdo_mysql mbstring exif pcntl bcmath zip

# Get latest Composer 
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Create user to run Composer and Artisan Commands
RUN adduser -G www-data -u $uid -h /home/$user -D $user
RUN mkdir -p /home/$user/.composer && \
  chown -R $user:www-data /home/$user

# Set working directory
WORKDIR /var/www

USER $user