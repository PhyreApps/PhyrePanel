#=========================================================================#
# PHYRE HOSTING PANEL - Default Web Domain Template                       #
# DO NOT MODIFY THIS FILE! CHANGES WILL BE LOST WHEN REBUILDING DOMAINS   #
# https://phyrepanel.com/docs/server-administration/web-templates.html    #
#=========================================================================#

DefaultRuntimeDir ${APACHE_RUN_DIR}
PidFile ${APACHE_PID_FILE}
Timeout 300
KeepAlive On
MaxKeepAliveRequests 100
KeepAliveTimeout 5

User ${APACHE_RUN_USER}
Group ${APACHE_RUN_GROUP}

HostnameLookups Off
ErrorLog ${APACHE_LOG_DIR}/error.log
LogLevel warn

IncludeOptional mods-enabled/*.load
IncludeOptional mods-enabled/*.conf

Listen 80

<IfModule ssl_module>
    Listen 443
</IfModule>

<IfModule mod_gnutls.c>
    Listen 443
</IfModule>

<Directory />
Options FollowSymLinks
AllowOverride None
Require all denied
</Directory>

<Directory /usr/share>
AllowOverride None
Require all granted
</Directory>

<Directory /var/www/>
Options Indexes FollowSymLinks
AllowOverride None
Require all granted
</Directory>

AccessFileName .htaccess

<FilesMatch "^\.ht">
Require all denied
</FilesMatch>

LogFormat "%v:%p %h %l %u %t \"%r\" %>s %O \"%{Referer}i\" \"%{User-Agent}i\"" vhost_combined
LogFormat "%h %l %u %t \"%r\" %>s %O \"%{Referer}i\" \"%{User-Agent}i\"" combined
LogFormat "%h %l %u %t \"%r\" %>s %O" common
LogFormat "%{Referer}i -> %U" referer
LogFormat "%{User-agent}i" agent

IncludeOptional conf-enabled/*.conf

@foreach($virtualHosts as $virtualHost)

<VirtualHost *:{{$virtualHost['port']}}>

    @if(!empty($virtualHost['serverAdmin']))

        ServerAdmin {{$virtualHost['serverAdmin']}}

    @endif

    ServerName {{$virtualHost['domain']}}

    @if(!empty($virtualHost['domainAlias']))

        ServerAlias {{$virtualHost['domainAlias']}}

    @endif

    DocumentRoot {{$virtualHost['domainPublic']}}
    SetEnv APP_DOMAIN {{$virtualHost['domain']}}

    @if(isset($virtualHost['enableRuid2']) && $virtualHost['enableRuid2'] && !empty($virtualHost['user']) && !empty($virtualHost['group']))

        #RDocumentChRoot {{$virtualHost['domainPublic']}}
        #SuexecUserGroup {{$virtualHost['user']}} {{$virtualHost['group']}}
        #RUidGid {{$virtualHost['user']}} {{$virtualHost['group']}}

    @endif

    @if($virtualHost['enableLogs'])

        LogFormat "%h %l %u %t \"%r\" %>s %b" common

        CustomLog {{$virtualHost['domainRoot']}}/logs/apache2/bytes.log bytes
        CustomLog {{$virtualHost['domainRoot']}}/logs/apache2/access.log common
        ErrorLog {{$virtualHost['domainRoot']}}/logs/apache2/error.log

    @endif

    @if($virtualHost['appType'] == 'php')

        ScriptAlias /cgi-bin/ {{$virtualHost['domainPublic']}}/cgi-bin/

    @endif

    @if (!empty($virtualHost['proxyPass']))

        ProxyPreserveHost On
        ProxyRequests Off
        ProxyVia On
        ProxyPass / {{$virtualHost['proxyPass']}}
        ProxyPassReverse / {{$virtualHost['proxyPass']}}

    @endif

    <Directory {{$virtualHost['domainPublic']}}>

        Options Indexes FollowSymLinks MultiViews @if($virtualHost['appType'] == 'php') Includes ExecCGI @endif

        AllowOverride All
        Require all granted

        @if(isset($virtualHost['enableRuid2']) && $virtualHost['enableRuid2'] && !empty($virtualHost['user']) && !empty($virtualHost['group']))

            RMode config
            RUidGid {{$virtualHost['user']}} {{$virtualHost['group']}}

        @endif

        @if($virtualHost['passengerAppRoot'] !== null)

            PassengerAppRoot {{$virtualHost['passengerAppRoot']}}

            PassengerAppType {{$virtualHost['passengerAppType']}}

            @if($virtualHost['passengerStartupFile'] !== null)
                PassengerStartupFile {{$virtualHost['passengerStartupFile']}}
            @endif

        @endif

        @if($virtualHost['appType'] == 'php')

            Action phpcgi-script /cgi-bin/php
            <Files *.php>
                SetHandler phpcgi-script
            </Files>

            @php
                $appendOpenBaseDirs = $virtualHost['homeRoot'];
                if (isset($virtualHost['phpAdminValueOpenBaseDirs'])
                        && is_array($virtualHost['phpAdminValueOpenBaseDirs'])
                        && !empty($virtualHost['phpAdminValueOpenBaseDirs'])) {
                    $appendOpenBaseDirs .= ':' . implode(':', $virtualHost['phpAdminValueOpenBaseDirs']);
                }
            @endphp

            php_admin_value open_basedir {{$appendOpenBaseDirs}}

            php_admin_value upload_tmp_dir {{$virtualHost['homeRoot']}}/tmp
            php_admin_value session.save_path {{$virtualHost['homeRoot']}}/tmp
            php_admin_value sys_temp_dir {{$virtualHost['homeRoot']}}/tmp

        @endif

    </Directory>

    @if(!empty($virtualHost['sslCertificateFile']) and !empty($virtualHost['sslCertificateKeyFile']))

        SSLEngine on
        SSLCertificateFile {{$virtualHost['sslCertificateFile']}}
        SSLCertificateKeyFile {{$virtualHost['sslCertificateKeyFile']}}

        @if (!empty($virtualHost['sslCertificateChainFile']))

            SSLCertificateChainFile {{$virtualHost['sslCertificateChainFile']}}

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


@endforeach

