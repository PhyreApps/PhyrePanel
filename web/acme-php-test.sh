PHYRE_PHP=/usr/local/phyre/php/bin/php

$PHYRE_PHP -r "copy('https://github.com/acmephp/acmephp/releases/download/1.0.1/acmephp.phar', 'acmephp.phar');"
$PHYRE_PHP -r "copy('https://github.com/acmephp/acmephp/releases/download/1.0.1/acmephp.phar.pubkey', 'acmephp.phar.pubkey');"
$PHYRE_PHP acmephp.phar --version
