FROM php:7.4.21-cli-buster
ARG USER_ID
ARG GROUP_ID
ENV UID=${USER_ID}
ENV GID=${GROUP_ID}
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
RUN php composer-setup.php --install-dir=/bin --filename=composer
RUN php -r "unlink('composer-setup.php');"
RUN docker-php-ext-install bcmath
RUN apt update && apt install -y git
RUN groupadd --gid $USER_ID appuser \
  && useradd --uid $GROUP_ID --gid appuser --shell /bin/bash --create-home appuser
WORKDIR /app
RUN chown -R appuser:appuser ./
USER appuser
COPY . .
RUN composer install
CMD ["php"]
