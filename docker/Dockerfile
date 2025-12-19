FROM webdevops/php-apache:7.4

# This image already includes: pdo, pdo_mysql, intl, zip, etc.
# mod_rewrite is also pre-enabled

# Install Composer (if not present)
RUN if ! command -v composer &> /dev/null; then \
      curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; \
    fi

ENV WEB_DOCUMENT_ROOT=/app/webroot
WORKDIR /app
