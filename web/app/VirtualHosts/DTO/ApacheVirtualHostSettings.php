<?php

namespace App\VirtualHosts\DTO;

class ApacheVirtualHostSettings
{
    public $port = 80;

    public $domain;

    public $domainAlias;

    public $domainPublic;

    public $domainRoot;
    public $homeRoot;

    public $user;
    public $userGroup;
    public $additionalServices = [];

    public $sslCertificateFile = null;
    public $sslCertificateKeyFile = null;
    public $sslCertificateChainFile = null;

    public $appType = null;

    public $appVersion = null;

    public $passengerAppRoot = null;
    public $passengerAppType = null;
    public $passengerStartupFile = null;

    public $serverAdmin = null;

    public $proxyPass = null;

    public $enableLogs = false;

    public function setPort($port)
    {
        $this->port = $port;
    }
    public function setDomain($domain)
    {
        $this->domain = $domain;
    }

    public function setDomainAlias($domainAlias)
    {
        $this->domainAlias = $domainAlias;
    }

    public function setDomainPublic($domainPublic)
    {
        $this->domainPublic = $domainPublic;
    }

    public function setDomainRoot($domainRoot)
    {
        $this->domainRoot = $domainRoot;
    }

    public function setHomeRoot($homeRoot)
    {
        $this->homeRoot = $homeRoot;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    public function setUserGroup($userGroup)
    {
        $this->userGroup = $userGroup;
    }

    public function setAdditionalServices($additionalServices)
    {
        $this->additionalServices = $additionalServices;
    }

    public function setSSLCertificateFile($sslCertificateFile)
    {
        $this->sslCertificateFile = $sslCertificateFile;
    }

    public function setSSLCertificateKeyFile($sslCertificateKeyFile)
    {
        $this->sslCertificateKeyFile = $sslCertificateKeyFile;
    }

    public function setSSLCertificateChainFile($sslCertificateChainFile)
    {
        $this->sslCertificateChainFile = $sslCertificateChainFile;
    }

    public function setAppType($appType)
    {
        $this->appType = $appType;
    }

    public function setAppVersion($appVersion)
    {
        $this->appVersion = $appVersion;
    }

    public function setPassengerAppRoot($passengerAppRoot)
    {
        $this->passengerAppRoot = $passengerAppRoot;
    }

    public function setPassengerAppType($passengerAppType)
    {
        $this->passengerAppType = $passengerAppType;
    }

    public function setPassengerStartupFile($passengerStartupFile)
    {
        $this->passengerStartupFile = $passengerStartupFile;
    }

    public function setServerAdmin($email)
    {
        $this->serverAdmin = $email;
    }

    public function setProxyPass($proxyPass)
    {
        $this->proxyPass = $proxyPass;
    }

    public function setEnableLogs($enableLogs)
    {
        $this->enableLogs = $enableLogs;
    }

    public function getSettings()
    {
        $settings = [
            'port' => $this->port,
            'domain' => $this->domain,
            'domainAlias' => $this->domainAlias,
            'domainPublic' => $this->domainPublic,
            'domainRoot' => $this->domainRoot,
            'homeRoot' => $this->homeRoot,
            'serverAdmin' => $this->serverAdmin,
            'user' => $this->user,
            'group' => $this->userGroup,
            'enableRuid2' => true,
            'sslCertificateFile' => $this->sslCertificateFile,
            'sslCertificateKeyFile' => $this->sslCertificateKeyFile,
            'sslCertificateChainFile' => $this->sslCertificateChainFile,
            'appType' => $this->appType,
            'appVersion' => $this->appVersion,
            'passengerAppRoot' => $this->passengerAppRoot,
            'passengerAppType' => $this->passengerAppType,
            'passengerStartupFile' => $this->passengerStartupFile,
            'proxyPass' => $this->proxyPass,
            'enableLogs' => $this->enableLogs,
        ];

        $apacheVirtualHostConfigs = app()->virtualHostManager->getConfigs($this->additionalServices);

        $settings = array_merge($settings, $apacheVirtualHostConfigs);

        return $settings;
    }
}
