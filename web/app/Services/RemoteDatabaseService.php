<?php

namespace App\Services;

use App\Models\RemoteDatabaseServer;
use App\UniversalDatabaseExecutor;
use Illuminate\Support\Str;

class RemoteDatabaseService
{
    public $remoteDatabaseServerId;

    public function __construct($remoteDatabaseServerId) {
        $this->remoteDatabaseServerId = $remoteDatabaseServerId;
    }

    public function createDatabase($databaseName)
    {

        $remoteDatabaseServer = RemoteDatabaseServer::find($this->remoteDatabaseServerId);
        if (!$remoteDatabaseServer) {
            return false;
        }

        $databaseManager = new UniversalDatabaseExecutor(
            $remoteDatabaseServer->host,
            $remoteDatabaseServer->port,
            $remoteDatabaseServer->username,
            $remoteDatabaseServer->password,
            null
        );

        $createDatabase = $databaseManager->createDatabase($databaseName);

        return $createDatabase;
    }

    public function deleteDatabase($databaseName)
    {
        $remoteDatabaseServer = RemoteDatabaseServer::where('id', $this->remoteDatabaseServerId)->first();
        if (!$remoteDatabaseServer) {
            return false;
        }

        $databaseManager = new UniversalDatabaseExecutor(
            $remoteDatabaseServer->host,
            $remoteDatabaseServer->port,
            $remoteDatabaseServer->username,
            $remoteDatabaseServer->password
        );

        $deleteDatabase = $databaseManager->deleteDatabase($databaseName);
        if ($deleteDatabase) {
            return true;
        } else {
            return false;
        }
    }
}
