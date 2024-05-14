<?php

namespace Tests\Unit;

use App\Filament\Enums\BackupStatus;
use App\Helpers;
use App\Jobs\ProcessHostingSubscriptionBackup;
use App\Models\Backup;
use App\Models\Customer;
use App\Models\Database;
use App\Models\DatabaseUser;
use App\Models\HostingPlan;
use App\Models\HostingSubscription;
use App\Models\HostingSubscriptionBackup;
use Faker\Factory;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Tests\Feature\Api\ActionTestCase;
use Illuminate\Support\Facades\Queue;

class HSBackupTest extends ActionTestCase
{
    public function testFullBackup()
    {

        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

     //   Queue::fake();

        $chs = $this->_createHostingSubscription();

        $newBackup = new HostingSubscriptionBackup();
        $newBackup->backup_type = 'full';
        $newBackup->hosting_subscription_id = $chs['hostingSubscriptionId'];
        $newBackup->save();


        $phsb = new ProcessHostingSubscriptionBackup($newBackup->id);
        $phsb->handle();

        $findLastBackup = HostingSubscriptionBackup::where('hosting_subscription_id', $chs['hostingSubscriptionId'])
            ->first();

        $this->assertNotEmpty($findLastBackup);
        $this->assertNotEmpty($findLastBackup->id);
        $this->assertNotEmpty($findLastBackup->created_at);
        $this->assertSame($findLastBackup->backup_type, 'full');

        $backupFinished = false;
        for ($i = 0; $i < 100; $i++) {

            $findLastBackup = HostingSubscriptionBackup::where('id', $findLastBackup->id)->first();
            if ($findLastBackup->status == BackupStatus::Completed) {
                $backupFinished = true;
                break;
            }

            sleep(1);
        }

        $this->assertTrue($backupFinished);
        $this->assertSame($findLastBackup->status, BackupStatus::Completed);
        $this->assertNotEmpty($findLastBackup->file_path);
        $this->assertTrue(file_exists($findLastBackup->file_path));

        $backup = new HostingSubscriptionBackup();
        $checkCronJob = $backup->checkCronJob();
        $this->assertTrue($checkCronJob);

        $chs = $this->_createHostingSubscription();

        $backup = new HostingSubscriptionBackup();
        $backup->backup_type = 'full';
        $backup->hosting_subscription_id = $chs['hostingSubscriptionId'];
        $backup->save();

        $phsb = new ProcessHostingSubscriptionBackup($backup->id);
        $phsb->handle();

        $backupId = $backup->id;

        $findBackup = false;
        $backupCompleted = false;
        for ($i = 0; $i < 100; $i++) {

            $findBackup = HostingSubscriptionBackup::where('id', $backupId)->first();
            if ($findBackup) {
                if ($findBackup->status == BackupStatus::Completed) {
                    $backupCompleted = true;
                    break;
                }
            }
            sleep(1);
        }

        $this->assertTrue($backupCompleted);
        $this->assertNotEmpty($findBackup->file_path);
        $this->assertTrue(file_exists($findBackup->file_path));

        $getFilesize = filesize($findBackup->file_path);
        $this->assertGreaterThan(0, $getFilesize);
        $this->assertSame($getFilesize, intval($findBackup->size));

        Helpers::extractZip($findBackup->file_path, $findBackup->path . '/unit-test');
//
        $findDatabase = Database::where('id', $chs['databaseId'])->first();

        $extractedDatabase = $findBackup->path . '/unit-test/databases/' . $findDatabase->database_name_prefix . $findDatabase->database_name . '.sql';

        $this->assertTrue(file_exists($extractedDatabase));
        $extractedDatabaseContent = file_get_contents($extractedDatabase);
        $this->assertNotEmpty($extractedDatabaseContent);

        foreach ($chs['databaseTableData'] as $tableName => $tableData) {
            $this->assertStringContainsString('CREATE TABLE `' . $tableName . '`', $extractedDatabaseContent);
            foreach ($tableData as $data) {
                $this->assertStringContainsString('INSERT INTO `' . $tableName . '`', $extractedDatabaseContent);
                $this->assertStringContainsString($data['name'], $extractedDatabaseContent);
                $this->assertStringContainsString($data['email'], $extractedDatabaseContent);
                $this->assertStringContainsString($data['phone'], $extractedDatabaseContent);
            }
        }

    }

    private function _createHostingSubscription()
    {

        $customer = new Customer();
        $customer->name = 'UnitBackupTest' . time();
        $customer->email = 'UnitBackupTest' . time() . '@unit-test.com';
        $customer->save();

        $hostingPlan = new HostingPlan();
        $hostingPlan->name = 'UnitBackupTest' . time();
        $hostingPlan->description = 'Unit Backup Test';
        $hostingPlan->disk_space = 1000;
        $hostingPlan->bandwidth = 1000;
        $hostingPlan->databases = 1;
        $hostingPlan->additional_services = ['daily_backups'];
        $hostingPlan->features = [];
        $hostingPlan->default_server_application_type = 'apache_php';
        $hostingPlan->default_server_application_settings = [
            'php_version' => '8.3',
        ];
        $hostingPlan->save();

        $hostingSubscription = new HostingSubscription();
        $hostingSubscription->customer_id = $customer->id;
        $hostingSubscription->hosting_plan_id = $hostingPlan->id;
        $hostingSubscription->domain = 'unit-backup-test' . time() . '.com';
        $hostingSubscription->save();


        /**
         *  Create Database and Database User
         *  with random data for testing the backup
         */
        $database = new Database();
        $database->hosting_subscription_id = $hostingSubscription->id;
        $database->database_name = 'ubt' . time();
        $database->save();

        $this->assertNotEmpty($database->id);

        $databaseUser = new DatabaseUser();
        $databaseUser->database_id = $database->id;
        $databaseUser->username = 'ubt' . time();
        $databaseUser->password = Str::password(24);
        $databaseUser->save();

        $this->assertNotEmpty($databaseUser->id);

        $unitTestDbConnection = 'db-unit-' . $database->id;
        config(['database.connections.' . $unitTestDbConnection => [
            'driver' => 'mysql',
            'host' => '127.0.0.1',
            'port' => '3306',
            'database' => $database->database_name_prefix . $database->database_name,
            'username' => $databaseUser->username_prefix . $databaseUser->username,
            'password' => $databaseUser->password,
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix' => '',
            'strict' => false,
            'engine' => null,
        ]]);

        Schema::connection($unitTestDbConnection)
            ->create('random_table', function ($table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::connection($unitTestDbConnection)
            ->create('second_random_table', function ($table) {
                $table->increments('id');
                $table->string('name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->timestamps();
            });

        $this->assertTrue(Schema::connection($unitTestDbConnection)
            ->hasTable('random_table'));

        $this->assertTrue(Schema::connection($unitTestDbConnection)
            ->hasTable('second_random_table'));

        $databaseTableData = [];
        for ($i = 0; $i < 200; $i++) {
            $databaseData = [
                'name' => 'UnitBackupTest' . time() . $i,
                'email' => 'UnitBackupTest' . time() . $i . '@unit-test.com',
                'phone' => time(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $databaseTableData['random_table'][] = $databaseData;
            $databaseTableData['second_random_table'][] = $databaseData;

            DB::connection($unitTestDbConnection)
                ->table('random_table')
                ->insert($databaseData);

            DB::connection($unitTestDbConnection)
                ->table('second_random_table')
                ->insert($databaseData);
        }

        return [
            'customerId' => $customer->id,
            'hostingPlanId' => $hostingPlan->id,
            'hostingSubscriptionId' => $hostingSubscription->id,
            'databaseId' => $database->id,
            'databaseUserId' => $databaseUser->id,
            'databaseConnection' => $unitTestDbConnection,
            'databaseTableData' => $databaseTableData
        ];
    }
}
