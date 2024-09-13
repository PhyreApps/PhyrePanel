<?php

namespace App;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Query;

class UniversalDatabaseExecutor
{
    public $host;
    public $port;

    public $username;
    public $password;

    public $database =  null;

    public function __construct($host, $port, $username, $password = null, $database = null)
    {
        $this->host = $host;
        $this->port = $port;

        $this->username = $username;
        $this->password = $password;

        $this->database = $database;
    }

    private function _getDatabaseConnection()
    {
        $connectionParams = [
            'user' => $this->username,
            'password'=> $this->password,
            'host' => $this->host,
            'port' => $this->port,
            'driver' => 'pdo_mysql',
        ];

        return DriverManager::getConnection($connectionParams);

    }

    public function fixPasswordPolicy()
    {
        $connection = $this->_getDatabaseConnection();
        $connection->executeQuery('SET GLOBAL validate_password.policy = 0');
    }

    public function createDatabase($databaseName)
    {

        try {
            $connection = $this->_getDatabaseConnection();
            $resultSet = $connection->executeQuery('CREATE DATABASE ' . $databaseName);

            return [
                'success' => true,
                'message' => 'Database created successfully'
            ];
        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

    }

    public function deleteDatabase($databaseName)
    {
        try {
            $connection = $this->_getDatabaseConnection();
            $resultSet = $connection->executeQuery('DROP DATABASE ' . $databaseName);

            return $resultSet;
        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'database doesn\'t exist') !== false) {
                return [
                    'error' => true,
                    'message' => 'Database does not exist'
                ];
            }
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }

    }

    public function getUserByUsername($username)
    {

        $connection = $this->_getDatabaseConnection();

        $resultSet = $connection->executeQuery('SELECT * FROM mysql.user WHERE User = ?', [
            $username
        ]);

        return $resultSet->fetchAssociative();

    }

    public function userGrantPrivilegesToDatabase($username, $databases = [])
    {

        $connection = $this->_getDatabaseConnection();

        if (!empty($databases)) {
            foreach ($databases as $database) {
                $connection->executeStatement('GRANT ALL PRIVILEGES ON '.$database.'.* TO ?', [
                    $username
                ]);
            }
        }

        $connection->executeQuery('FLUSH PRIVILEGES');

    }
    public function createUser($username, $password)
    {
        try {
            $connection = $this->_getDatabaseConnection();

            $resultSet = $connection->executeStatement('CREATE USER ? IDENTIFIED BY ?', [
                $username,
                $password
            ]);

            return [
                'success' => true,
                'message' => 'User created successfully'
            ];

        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }
    public function deleteUser($username)
    {
        try {
            $connection = $this->_getDatabaseConnection();

            $resultSet = $connection->executeStatement('DROP USER ?', [
                $username
            ]);

            return [
                'success' => true,
                'message' => 'User deleted successfully'
            ];

        } catch (\Exception $e) {
            return [
                'error' => true,
                'message' => $e->getMessage()
            ];
        }
    }

}
