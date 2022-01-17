FROM php:7.4.3-cli

# Install redis driver
RUN pecl install redis && docker-php-ext-enable redis

# Copy rootdir
COPY . /etc/php

# Run startup script
CMD ["php", "/etc/php/main.php"]
