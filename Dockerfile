FROM pimcore/pimcore:php8.1-latest as dev
RUN set -eux; \
    apt-get update; \
    apt-get install -y $PHPIZE_DEPS libxslt1-dev; \
    docker-php-ext-install xsl; \
    sync; \
    apt-get purge -y $PHPIZE_DEPS; \
    rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*
ARG uid=1000
RUN usermod -u $uid www-data && chown -R www-data:www-data /var/www

FROM dev as cromium
RUN apt update && \
    apt install -y chromium chromium-driver
ENV PANTHER_NO_SANDBOX=1
ENV PANTHER_CHROME_ARGUMENTS='--disable-dev-shm-usage'
ENV CORESHOP_SKIP_DB_SETUP=1
ENV PANTHER_NO_HEADLESS=0
