#=========================================================================#
# PHYRE HOSTING PANEL - Default Web Domain Template                       #
# DO NOT MODIFY THIS FILE! CHANGES WILL BE LOST WHEN REBUILDING DOMAINS   #
# https://phyrepanel.com/docs/server-administration/web-templates.html    #
#=========================================================================#

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

    @if($appType == 'php')

    ScriptAlias /cgi-bin/ {{$domainPublic}}/cgi-bin/

    @endif

    @if (!empty($proxyPass))

    ProxyPass / {{$proxyPass}}

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

    Include /etc/apache2/phyre/options-ssl-apache.conf

    @endif

</VirtualHost>

