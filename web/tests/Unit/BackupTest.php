<?php

namespace tests\Unit;

use App\Filament\Enums\BackupStatus;
use App\Helpers;
use App\Models\Backup;
use App\Models\Customer;
use App\Models\HostingPlan;
use App\Models\HostingSubscription;
use Faker\Factory;
use Illuminate\Support\Facades\Artisan;
use Tests\Feature\Api\ActionTestCase;

class BackupTest extends ActionTestCase
{
    public function testFullBackup()
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 0);

        Artisan::call('phyre:run-backup');

        $findLastBackup = Backup::orderBy('id', 'asc')->first();
        $this->assertNotEmpty($findLastBackup);
        $this->assertNotEmpty($findLastBackup->id);
        $this->assertNotEmpty($findLastBackup->created_at);
        $this->assertSame($findLastBackup->backup_type, 'full');

        $backupFinished = false;
        for ($i = 0; $i < 100; $i++) {
            $findLastBackup = Backup::orderBy('id', 'desc')->first();
            $findLastBackup->checkBackup();
            if ($findLastBackup->status == BackupStatus::Completed) {
                $backupFinished = true;
                break;
            }
            sleep(1);
        }
        $this->assertTrue($backupFinished);
        $this->assertSame($findLastBackup->status, BackupStatus::Completed);
        $this->assertNotEmpty($findLastBackup->filepath);
        $this->assertTrue(file_exists($findLastBackup->filepath));

        $backup = new Backup();
        $checkCronJob = $backup->checkCronJob();
        $this->assertTrue($checkCronJob);

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

        $backup = new Backup();
        $backup->backup_type = 'full';
        $backup->save();

        $backupId = $backup->id;

        $findBackup = false;
        $backupCompleted = false;
        for ($i = 0; $i < 100; $i++) {
            $findBackup = Backup::where('id', $backupId)->first();
            $findBackup->checkBackup();
            if ($findBackup->status == BackupStatus::Completed) {
                $backupCompleted = true;
                break;
            }
            sleep(1);
        }

        $this->assertTrue($backupCompleted);
        $this->assertNotEmpty($findBackup->filepath);
        $this->assertTrue(file_exists($findBackup->filepath));

        $getFilesize = filesize($findBackup->filepath);
        $this->assertGreaterThan(0, $getFilesize);
        $this->assertSame(Helpers::checkPathSize($findBackup->path), $findBackup->size);

        Helpers::extractTar($findBackup->filepath, $findBackup->path . '/unit-test');


    }

}
