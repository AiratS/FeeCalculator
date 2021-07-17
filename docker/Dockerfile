FROM php:7.4.21-cli-buster
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"
RUN docker-php-ext-install bcmath
RUN apt update && apt install -y git
WORKDIR /app
COPY . .
CMD ["php"]
