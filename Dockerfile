FROM pimcore/pimcore:php8.2-debug-latest as dev
RUN set -eux; \
    apt-get update; \
    apt-get install -y $PHPIZE_DEPS libxslt1-dev; \
    docker-php-ext-install xsl; \
    sync; \
    apt-get purge -y $PHPIZE_DEPS; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

RUN echo 'xdebug.idekey = PHPSTORM' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
    && echo 'xdebug.mode = debug' >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

ARG uid=1000
RUN usermod -u $uid www-data && chown -R www-data:www-data /var/www

FROM dev as behat
RUN apt update && \
    apt install -y chromium chromium-driver

# Install Symfony, Pimcore and CoreShop inside Tests container
# RUN wget https://get.symfony.com/cli/installer -O - | bash
RUN curl --proto "=https" --tlsv1.2 -sSf -L https://might-redirect.example.com/install.sh | sh
RUN apt install symfony-cli

ENV PANTHER_NO_SANDBOX=1
ENV PANTHER_CHROME_ARGUMENTS='--disable-dev-shm-usage'
ENV CORESHOP_SKIP_DB_SETUP=1
ENV PANTHER_NO_HEADLESS=0
ENV APP_ENV="test"

