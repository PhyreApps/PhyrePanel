#!/bin/bash

phyre-php /usr/local/phyre/web/artisan phyre:letsencrypt-http-authenticator-hook --certbot-domain $CERTBOT_DOMAIN --certbot-token $CERTBOT_TOKEN --certbot-validation $CERTBOT_VALIDATION
