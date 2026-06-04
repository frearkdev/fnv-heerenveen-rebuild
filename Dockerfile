FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends curl \
    && docker-php-ext-install pdo_mysql \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html

HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=5 \
  CMD curl -fsS http://localhost/health.php || exit 1

EXPOSE 80
