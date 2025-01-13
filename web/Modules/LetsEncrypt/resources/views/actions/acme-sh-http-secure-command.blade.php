/usr/local/phyre/web/Modules/LetsEncrypt/shell/acme.sh --issue -d {{$domain}} -d www.{{$domain}} --webroot {{$domainPublic}}
