FROM php:8.4-apache

# Install required packages and enable PHP extensions
RUN apt-get update && apt-get install -y --no-install-recommends mariadb-client \
        libxml2-dev \
        libxslt-dev \
        libzip-dev \
        dos2unix \
    && docker-php-ext-install \
        mysqli \
        pdo_mysql \
        xsl \
        zip \
    && rm -rf /var/lib/apt/lists/*

# --- NUR FÜR TAGIFY: Node.js/npm installieren (1 Änderung) ---
RUN apt-get update && apt-get install -y curl ca-certificates gnupg \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && rm -rf /var/lib/apt/lists/*

# Set Apache document root and enable rewrite module
ENV APACHE_DOCUMENT_ROOT=/var/www/html
RUN sed -i 's|/var/www/html|${APACHE_DOCUMENT_ROOT}|g' /etc/apache2/sites-available/000-default.conf \
    && a2enmod rewrite

# Copy application files
COPY . /var/www/html/

# --- NUR FÜR TAGIFY: npm install ausführen (2. Änderung) ---
WORKDIR /var/www/html
RUN if [ -f package.json ]; then npm install --force; fi

# Install database schema and set entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN dos2unix /usr/local/bin/docker-entrypoint.sh \
    && chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]
CMD ["apache2-foreground"]