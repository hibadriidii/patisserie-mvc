
FROM php:8.2-apache

# Installer les extensions nécessaires pour MySQL
RUN docker-php-ext-install pdo_mysql mysqli

# Activer mod_rewrite
RUN a2enmod rewrite

# Copier le projet
COPY . /var/www/html/

# Installer la configuration Apache pour Render
COPY render-apache.conf /etc/apache2/sites-available/000-default.conf

# Configurer Apache pour écouter sur le port 10000
RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf

# Donner les droits nécessaires aux images uploadées
RUN mkdir -p /var/www/html/public/images \
    && chown -R www-data:www-data /var/www/html/public/images

EXPOSE 10000
