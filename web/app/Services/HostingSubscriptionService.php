<?php

namespace App\Services;

use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\HostingPlan;
use App\Models\HostingSubscription;
use App\Models\RemoteDatabaseServer;
use Illuminate\Support\Str;

class HostingSubscriptionService
{
    public $hostingSubscriptionId;

    public function __construct($hostingSubscriptionId)
    {
        $this->hostingSubscriptionId = $hostingSubscriptionId;
    }

    public function createDatabase($databaseName)
    {
        $findHostingSubscription = HostingSubscription::where('id', $this->hostingSubscriptionId)->first();
        if (!$findHostingSubscription) {
            return;
        }
        $findHostingPlan = HostingPlan::where('id', $findHostingSubscription->hosting_plan_id)->first();
        if (!$findHostingPlan) {
            return;
        }

        $databaseHost = 'localhost';
        $databasePort = 3306;

        $createDatabase = new Database();
        $createDatabase->hosting_subscription_id = $this->hostingSubscriptionId;

        if ($findHostingPlan->default_database_server_type == 'remote') {
            $createDatabase->remote_database_server_id = $findHostingPlan->default_remote_database_server_id;
            $createDatabase->is_remote_database_server = 1;

            $findRemoteDatabaseServer = RemoteDatabaseServer::where('id', $findHostingPlan->default_remote_database_server_id)->first();
            if ($findRemoteDatabaseServer) {
                $databaseHost = $findRemoteDatabaseServer->host;
                $databasePort = $findRemoteDatabaseServer->port;
            }
        }
        $createDatabase->database_name = $databaseName;
        $createDatabase->save();

        return [
            'success' => true,
            'data' => [
                'database_id' => $createDatabase->id,
                'database_name' => $createDatabase->database_name_prefix . $createDatabase->database_name,
                'database_host' => $databaseHost,
                'database_port' => $databasePort
            ]
        ];
    }

    public function createDatabaseUser($databaseId, $databaseUser, $databasePassword)
    {
        $createDatabaseUser = new DatabaseUser();
        $createDatabaseUser->database_id = $databaseId;
        $createDatabaseUser->username = $databaseUser;
        $createDatabaseUser->password = $databasePassword;
        $createDatabaseUser->save();

        return [
            'success' => true,
            'data' => [
                'database_id' => $createDatabaseUser->database_id,
                'database_user' => $createDatabaseUser->username_prefix . $createDatabaseUser->username,
                'database_password' => $createDatabaseUser->password
            ]
        ];
    }
}
