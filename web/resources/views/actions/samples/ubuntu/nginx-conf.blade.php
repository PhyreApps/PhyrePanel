server {

    server_name {{$domain}} www.{{$domain}};

    root        {{$domainRoot}};
    charset     utf-8;

    location / {

    }
}
