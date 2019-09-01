FROM php:7.3-cli

WORKDIR /var/www/app

# Install composer
RUN cd /usr/bin \
    && php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
    && php -r "if (hash_file('sha384', 'composer-setup.php') === 'a5c698ffe4b8e849a443b120cd5ba38043260d5c4023dbf93e1558871f1f07f58274fc6f4c93bcfd858c6bd0775cd8d1') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
    && php composer-setup.php \
    && php -r "unlink('composer-setup.php');"

# Install packages
RUN apt-get update && apt-get install -y \
    libyaml-dev \
    libmemcached-dev \
    zlib1g-dev \
    zip unzip \
    git

# Install PHP extensions
RUN pecl install yaml \
    && docker-php-ext-enable yaml

RUN pecl install memcached \
    && docker-php-ext-enable memcached

# Expose port 8000
EXPOSE 8000

# Run app
CMD [ "sh","-c", "php /usr/bin/composer.phar install && php -S 0.0.0.0:8000 -t public index.php" ]
