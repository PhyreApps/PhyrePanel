<?php

namespace Modules\Microweber\Listeners;

use App\Events\DomainIsCreated;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Domain;
use App\Models\HostingPlan;
use App\Models\HostingSubscription;
use App\Services\HostingSubscriptionService;
use App\SupportedApplicationTypes;
use Illuminate\Support\Str;
use MicroweberPackages\SharedServerScripts\MicroweberWhitelabelSettingsUpdater;
use MicroweberPackages\SharedServerScripts\MicroweberWhitelabelWebsiteApply;
use Modules\Microweber\App\Models\MicroweberInstallation;
use Modules\Microweber\Jobs\UpdateEnvVarsToWebsites;

class DomainIsCreatedListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DomainIsCreated $event): void
    {
        $findDomain = Domain::where('id', $event->model->id)->first();
        if (!$findDomain) {
            return;
        }
        $findHostingSubscription = HostingSubscription::where('id', $findDomain->hosting_subscription_id)->first();
        if (!$findHostingSubscription) {
            return;
        }
        $findHostingPlan = HostingPlan::where('id', $findHostingSubscription->hosting_plan_id)->first();
        if (!$findHostingPlan) {
            return;
        }

        $skip = true;
        if (in_array('microweber', $findHostingPlan->additional_services)) {
            $skip = false;
        }
        if (in_array('microweber_custom', $findHostingPlan->additional_services)) {
            $skip = false;
        }

        if ($skip) {
            return;
        }

        if ($findHostingPlan->default_server_application_type != 'apache_php') {
            return;
        }


        $installPath = $findDomain->domain_root . '/microweber';
        $installPathPublicHtml = $findDomain->domain_public;
        $installPathPublicFOrSymlink = $findDomain->domain_public . '/microweber/public';

        if (!is_dir($installPath)) {
            mkdir($installPath, 0755, true);
        }


        if (isset($findHostingPlan->default_server_application_settings['php_version'])) {
            $phpVersion = $findHostingPlan->default_server_application_settings['php_version'];
            $phpSbin = 'php' . $phpVersion;
        } else {
            $supportedPhpVersions = SupportedApplicationTypes::getPHPVersions();
            $phpVersion = end($supportedPhpVersions);
            $phpVersion = str_replace('PHP ', '', $phpVersion);
            $phpSbin = 'php' . $phpVersion;
        }

        $databasesAreCreated = false;
        $createdDatabaseUsername = null;
        $createdDatabaseUserPassword = null;
        $createdDatabaseName = null;
        $createdDatabaseHost = null;
        $createdDatabasePort = null;

        $databseType = 'sqlite';
        $databasesAreCreated = false;
        $shouldCreateMysqlDatabase = false;


        $microweberSettingsFromPanel = setting('microweber');

        if (isset($microweberSettingsFromPanel['database_driver']) && $microweberSettingsFromPanel['database_driver'] == 'mysql') {
            $shouldCreateMysqlDatabase = true;
        }

        if ($shouldCreateMysqlDatabase) {
            try {

                $databaseUserPassword = Str::password(24);
                $databaseName = $databaseUsername = 'mw' . time();

                $hss = new HostingSubscriptionService($findDomain->hosting_subscription_id);
                $createDatabase = $hss->createDatabase($databaseName);
                if (isset($createDatabase['data']['database_name'])) {
                    $createdDatabaseName = $createDatabase['data']['database_name'];
                }
                $createDatabaseUser = $hss->createDatabaseUser($createDatabase['data']['database_id'], $databaseUsername, $databaseUserPassword);
                if (isset($createDatabaseUser['data']['database_user'])) {
                    $createdDatabaseUsername = $createDatabaseUser['data']['database_user'];
                    $createdDatabaseUserPassword = $createDatabaseUser['data']['database_password'];
                    $createdDatabaseHost = $createDatabase['data']['database_host'];
                    $createdDatabasePort = $createDatabase['data']['database_port'];
                }

                $databasesAreCreated = true;

            } catch (\Exception $e) {
                $databasesAreCreated = false;
            }
        }
        $installationType = 'symlink';
        $installationLanguage = 'en';
        $website_manager_url = 'https://microweber.com';

        //$installationTemplate = 'default';


        if (isset($microweberSettingsFromPanel['default_installation_type']) && $microweberSettingsFromPanel['default_installation_type'] == 'standalone') {
            $installationType = 'standalone';
        }
        if (isset($microweberSettingsFromPanel['website_manager_url']) && $microweberSettingsFromPanel['website_manager_url']) {
            $website_manager_url = $microweberSettingsFromPanel['website_manager_url'];
        }


        $install = new \MicroweberPackages\SharedServerScripts\MicroweberInstaller();
        $install->setChownUser($findDomain->domain_username);
        $install->enableChownAfterInstall();

        // $install->setPath($findDomain->domain_public);
        $install->setPath($installPath);
        $install->setSourcePath(config('microweber.sharedPaths.app'));

        $install->setLanguage($installationLanguage);

        $domainForInstall = $findDomain->domain;
        $install->setAppUrl($domainForInstall);


        //$install->setStandaloneInstallation();
        if ($installationType == 'symlink') {
            $install->setSymlinkInstallation();
        } else {
            $install->setStandaloneInstallation();
        }


        if ($databasesAreCreated) {
            $install->setDatabaseDriver('mysql');
            $install->setDatabaseHost($createdDatabaseHost . ':' . $createdDatabasePort);
            $install->setDatabaseName($createdDatabaseName);
            $install->setDatabaseUsername($createdDatabaseUsername);
            $install->setDatabasePassword($createdDatabaseUserPassword);
        } else {
            $install->setDatabaseDriver('sqlite');
        }

        $emailDomain = 'microweber.com';
        $wildcardDomain = setting('general.wildcard_domain');
        if (!empty($wildcardDomain)) {
            $emailDomain = $wildcardDomain;
        }

        $username = Str::random(8);


        $install->setPhpSbin($phpSbin);

        if (in_array('microweber_custom', $findHostingPlan->additional_services)) {
            $install->setAdminEmail(null);
            $install->setAdminUsername(null);
            $install->setAdminPassword(null);
        } else {
            $install->setAdminEmail($username . '@' . $emailDomain);
            $install->setAdminUsername($username);
            $install->setAdminPassword(Str::random(8));
        }

        $status = $install->run();


        if (isset($status['success']) && $status['success']) {

            $sharedAppPath = config('microweber.sharedPaths.app');
//            $whitelabelSettings = setting('microweber.whitelabel');
//            $whitelabelSettings['website_manager_url'] = setting('microweber.website_manager_url');
//
//            $whitelabel = new MicroweberWhitelabelSettingsUpdater();
//            $whitelabel->setPath($sharedAppPath);
//            $whitelabel->apply($whitelabelSettings);

            try {
                $whitelabelApply = new MicroweberWhitelabelWebsiteApply();
                //  $whitelabelApply->setWebPath($findDomain->domain_public);
                $whitelabelApply->setWebPath($installPath);
                $whitelabelApply->setSharedPath($sharedAppPath);
                $whitelabelApply->apply();
            } catch (\Exception $e) {
                //   \Log::error('Error applying whitelabel to website: ' . $mwInstallation->installation_path);
            }

            $findInstallation = MicroweberInstallation::where('installation_path', $installPath)
                ->where('domain_id', $findDomain->id)
                ->first();

            if (!$findInstallation) {
                $findInstallation = new MicroweberInstallation();
                $findInstallation->domain_id = $findDomain->id;
                $findInstallation->installation_path = $installPath;
            }

            $findInstallation->app_version = 'latest';
            //$findInstallation->template = $installationTemplate;

            if ($installationType == 'symlink') {
                $findInstallation->installation_type = 'symlink';
            } else {
                $findInstallation->installation_type = 'standalone';
            }


            //symlink public folder
            if (is_dir($installPathPublicHtml)) {
                //rename the public folder to public_old
            rename($installPathPublicHtml, $installPathPublicHtml . '_old');
            }

            if (!is_link($installPathPublicHtml)) {

                //copy cgi-bin

              symlink($installPath . '/public', $installPathPublicHtml);


                //copy cgi-bin folder
                if (!is_dir($installPath . '/public/cgi-bin')) {
                    // copy($installPathPublicHtml . '_old' . '/cgi-bin', $installPath . '/cgi-bin');
                   // rename($installPathPublicHtml . '_old' . '/cgi-bin', $installPath . '/cgi-bin');

                    if (is_dir($installPathPublicHtml . '_old' . '/cgi-bin')) {
                        rename($installPathPublicHtml . '_old' . '/cgi-bin', $installPath . '/public/cgi-bin');
                    }

                }




            }


            $findInstallation->save();


            $envJob = new UpdateEnvVarsToWebsites();
            $envJob->setInstallationId($findInstallation->id);
            $envJob->handle();


        }

    }
}

