/usr/local/phyre/web/Modules/SSLManager/shell/acme.sh --issue -d {{$domain}} -d www.{{$domain}} --webroot {{$domainPublic}} --server letsencrypt
