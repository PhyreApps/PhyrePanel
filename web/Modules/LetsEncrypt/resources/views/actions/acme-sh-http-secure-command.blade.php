/usr/local/phyre/web/Modules/LetsEncrypt/shell/acme.sh --issue -d {{$domain}},www.{{$domain}} --webroot {{$domainPublic}}
