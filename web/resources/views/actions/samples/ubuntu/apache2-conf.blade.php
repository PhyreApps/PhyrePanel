<VirtualHost *:{{$port}}>

    @if(!empty($serverAdmin))

    ServerAdmin {{$serverAdmin}}

    @endif

    ServerName {{$domain}}

    @if(!empty($domainAlias))

    ServerAlias {{$domainAlias}}

    @endif

    DocumentRoot {{$domainPublic}}
    SetEnv APP_DOMAIN {{$domain}}

    @if(isset($enableRuid2) && $enableRuid2 && !empty($user) && !empty($group))

    #RDocumentChRoot {{$domainPublic}}
    #SuexecUserGroup {{$user}} {{$group}}
    #RUidGid {{$user}} {{$group}}

    @endif

    @if($enableLogs)

    LogFormat "%h %l %u %t \"%r\" %>s %b" common

    CustomLog {{$domainRoot}}/logs/apache2/bytes.log bytes
    CustomLog {{$domainRoot}}/logs/apache2/access.log common
    ErrorLog {{$domainRoot}}/logs/apache2/error.log

    @endif

    @if($appType == 'php')

    ScriptAlias /cgi-bin/ {{$domainPublic}}/cgi-bin/

    @endif

    @if (!empty($proxyPass))

    ProxyPreserveHost On
    ProxyRequests Off
    ProxyVia On
    ProxyPass / {{$proxyPass}}
    ProxyPassReverse / {{$proxyPass}}

    @endif

    <Directory {{$domainPublic}}>

        Options Indexes FollowSymLinks MultiViews @if($appType == 'php') Includes ExecCGI @endif

        AllowOverride All
        Require all granted

        @if(isset($enableRuid2) && $enableRuid2 && !empty($user) && !empty($group))

        RMode config
        RUidGid {{$user}} {{$group}}

        @endif

        @if($passengerAppRoot !== null)

        PassengerAppRoot {{$passengerAppRoot}}

        PassengerAppType {{$passengerAppType}}

        @if($passengerStartupFile !== null)
        PassengerStartupFile {{$passengerStartupFile}}
        @endif

        @endif

        @if($appType == 'php')

        Action phpcgi-script /cgi-bin/php
        <Files *.php>
            SetHandler phpcgi-script
        </Files>

        @php
        $appendOpenBaseDirs = $homeRoot;
        if (isset($phpAdminValueOpenBaseDirs)
                && is_array($phpAdminValueOpenBaseDirs)
                && !empty($phpAdminValueOpenBaseDirs)) {
            $appendOpenBaseDirs .= ':' . implode(':', $phpAdminValueOpenBaseDirs);
        }
        @endphp

        php_admin_value open_basedir {{$appendOpenBaseDirs}}

        php_admin_value upload_tmp_dir {{$homeRoot}}/tmp
        php_admin_value session.save_path {{$homeRoot}}/tmp
        php_admin_value sys_temp_dir {{$homeRoot}}/tmp

        @endif

    </Directory>

    @if(!empty($sslCertificateFile) and !empty($sslCertificateKeyFile))

    SSLEngine on
    SSLCertificateFile {{$sslCertificateFile}}
    SSLCertificateKeyFile {{$sslCertificateKeyFile}}

    @if (!empty($sslCertificateChainFile))

    SSLCertificateChainFile {{$sslCertificateChainFile}}

    @endif


    SSLEngine on

    # Intermediate configuration, tweak to your needs
    SSLProtocol             all -SSLv2 -SSLv3 -TLSv1 -TLSv1.1
    SSLCipherSuite          ECDHE-ECDSA-AES128-GCM-SHA256:ECDHE-RSA-AES128-GCM-SHA256:ECDHE-ECDSA-AES256-GCM-SHA384:ECDHE-RSA-AES256-GCM-SHA384:ECDHE-ECDSA-CHACHA20-POLY1305:ECDHE-RSA-CHACHA20-POLY1305:DHE-RSA-AES128-GCM-SHA256:DHE-RSA-AES256-GCM-SHA384
    SSLHonorCipherOrder     off
    SSLSessionTickets       off

    SSLOptions +StrictRequire

    # Add vhost name to log entries:
    LogFormat "%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-agent}i\"" vhost_combined
    LogFormat "%v %h %l %u %t \"%r\" %>s %b" vhost_common


    @endif

</VirtualHost>
