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
        if (! $findDomain) {
            return;
        }
        $findHostingSubscription = HostingSubscription::where('id', $findDomain->hosting_subscription_id)->first();
        if (! $findHostingSubscription) {
            return;
        }
        $findHostingPlan = HostingPlan::where('id', $findHostingSubscription->hosting_plan_id)->first();
        if (! $findHostingPlan) {
            return;
        }

        if (! in_array('microweber', $findHostingPlan->additional_services)) {
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

        try {

            $databaseUserPassword = Str::password(24);
            $databaseName = $databaseUsername = 'mw'.time();

            $hss = new HostingSubscriptionService($findDomain->hosting_subscription_id);
            $createDatabase = $hss->createDatabase($databaseName);
            if (isset($createDatabase['data']['database_name'])) {
                $createdDatabaseName = $createDatabase['data']['database_name'];
            }
            $createDatabaseUser = $hss->createDatabaseUser($createDatabase['data']['database_id'], $databaseUsername,$databaseUserPassword);
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

        $installationType = 'symlink';
        $installationLanguage = 'bg';

        $install = new \MicroweberPackages\SharedServerScripts\MicroweberInstaller();
        $install->setChownUser($findDomain->domain_username);
        $install->enableChownAfterInstall();

        $install->setPath($findDomain->domain_public);
        $install->setSourcePath(config('microweber.sharedPaths.app'));

        $install->setLanguage($installationLanguage);

        //$install->setStandaloneInstallation();
        $install->setSymlinkInstallation();

        if ($databasesAreCreated) {
            $install->setDatabaseDriver('mysql');
            $install->setDatabaseHost($createdDatabaseHost . ':' . $createdDatabasePort);
            $install->setDatabaseName($createdDatabaseName);
            $install->setDatabaseUsername($createdDatabaseUsername);
            $install->setDatabasePassword($createdDatabaseUserPassword);
        } else {
            $install->setDatabaseDriver('sqlite');
        }

        $install->setAdminEmail(Str::random(8) . '@microweber.com');
        $install->setAdminUsername(Str::random(8));
        $install->setAdminPassword(Str::random(8));

        $status = $install->run();

        if (isset($status['success']) && $status['success']) {

            $whiteLabelSettings = [];
            $whiteLabelSettings['website_manager_url'] = 'https://microweber.com';

            $whitelabel = new MicroweberWhitelabelSettingsUpdater();
            $whitelabel->setPath($findDomain->domain_public);
            $whitelabel->apply($whiteLabelSettings);

            $findInstallation = MicroweberInstallation::where('installation_path', $findDomain->domain_public)
                ->where('domain_id', $findDomain->id)
                ->first();

            if (! $findInstallation) {
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
