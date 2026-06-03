FROM dunglas/frankenphp:1-php8.3

RUN install-php-extensions \
    pdo_mysql \
    mysqli \
    intl \
    gd \
    zip \
    opcache \
    pcntl \
    bcmath \
    exif

WORKDIR /app

COPY docker/octane/entrypoint.sh /usr/local/bin/octane-entrypoint
RUN chmod +x /usr/local/bin/octane-entrypoint

ENTRYPOINT ["octane-entrypoint"]
