<?php

namespace Modules\Microweber\Listeners;

use App\Events\DomainIsCreated;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\Domain;
use App\Models\HostingPlan;
use App\Models\HostingSubscription;
use App\Services\HostingSubscriptionService;
use Illuminate\Support\Str;
use MicroweberPackages\SharedServerScripts\MicroweberWhitelabelSettingsUpdater;
use MicroweberPackages\SharedServerScripts\MicroweberWhitelabelWebsiteApply;
use Modules\Microweber\App\Models\MicroweberInstallation;

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

        if (!in_array('microweber', $findHostingPlan->additional_services)) {
            return;
        }

        if ($findHostingPlan->default_server_application_type != 'apache_php') {
            return;
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

        if($shouldCreateMysqlDatabase) {
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

    //    dd(setting('microweber'));

        $install = new \MicroweberPackages\SharedServerScripts\MicroweberInstaller();
        $install->setChownUser($findDomain->domain_username);
        $install->enableChownAfterInstall();

        $install->setPath($findDomain->domain_public);
        $install->setSourcePath(config('microweber.sharedPaths.app'));

        $install->setLanguage($installationLanguage);

        //$install->setStandaloneInstallation();
        if($installationType == 'symlink') {
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

        $install->setAdminEmail(Str::random(8) . '@'.$emailDomain);
        $install->setAdminUsername(Str::random(8));
        $install->setAdminPassword(Str::random(8));

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
                $whitelabelApply->setWebPath($findDomain->domain_public);
                $whitelabelApply->setSharedPath($sharedAppPath);
                $whitelabelApply->apply();
            } catch (\Exception $e) {
                //   \Log::error('Error applying whitelabel to website: ' . $mwInstallation->installation_path);
            }

            $findInstallation = MicroweberInstallation::where('installation_path', $findDomain->domain_public)
                ->where('domain_id', $findDomain->id)
                ->first();

            if (!$findInstallation) {
                $findInstallation = new MicroweberInstallation();
                $findInstallation->domain_id = $findDomain->id;
                $findInstallation->installation_path = $findDomain->domain_public;
            }

            $findInstallation->app_version = 'latest';
            //$findInstallation->template = $installationTemplate;

            if ($installationType == 'symlink') {
                $findInstallation->installation_type = 'symlink';
            } else {
                $findInstallation->installation_type = 'standalone';
            }

            $findInstallation->save();

        }

    }
}
