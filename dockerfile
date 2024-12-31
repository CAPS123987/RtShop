FROM php:8-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod ssl && a2enmod rewrite
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini"

COPY ./apache/000-default.conf /etc/apache2/sites-available/000-default.conf

RUN echo "ServerRoot '/workspaces/project_name'" >> /etc/apache2/httpd.conf

EXPOSE 80