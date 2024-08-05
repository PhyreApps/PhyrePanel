sudo certbot certonly \
    --non-interactive \
    --agree-tos \
    --manual \
    --preferred-challenges=http \
    -d {{$domain}} \
    --email {{$email}} \
    --manual-auth-hook /usr/local/phyre/web/Modules/LetsEncrypt/shell/hooks/pre/http-authenticator.sh \
    --force-renewal
