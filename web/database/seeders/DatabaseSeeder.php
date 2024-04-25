<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Backup;
use App\Models\Customer;
use App\Models\HostingPlan;
use App\Models\HostingSubscriptionBackup;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $backup = new Backup();
        $backup->checkCronJob();

        $backup = new HostingSubscriptionBackup();
        $backup->checkCronJob();

        $findCustomer = Customer::where('email', 'jhondoe@gmail.com')->first();
        if (! $findCustomer) {
            Customer::create([
                'name' => 'Jhon Doe',
                'username' => 'jhondoe',
                'email' => 'jhondoe@gmail.com',
                'password' => bcrypt('password'),
                'phone' => '1234567890',
            ]);
        }

        $findHostingPlan = HostingPlan::where('name', 'Hosting Free')->first();
        if (! $findHostingPlan) {
            HostingPlan::create([
                'name' => 'Hosting Free',
               // 'slug' => 'free',
                'description' => 'Free hosting plan',
                'disk_space' => 1000,
                'bandwidth' => 10000,
                'databases' => 1,
                'ftp_accounts' => 1,
                'email_accounts' => 1,
                'subdomains' => 1,
                'parked_domains' => 1,
                'addon_domains' => 1,
                'ssl_certificates' => 0,
                'daily_backups' => 0,
                'free_domain' => 0,
                'additional_services' => [],
                'features' => [],
                'limitations' => [],
                'default_server_application_type' => 'apache_php',
                'default_server_application_settings'=>[
                    'php_version' => '8.3',
                ]
            ]);
        }

        $findHostingPlan = HostingPlan::where('name', 'Hosting Basic')->first();
        if (! $findHostingPlan) {
            HostingPlan::create([
                'name' => 'Hosting Basic',
            //    'slug' => 'basic',
                'description' => 'Basic hosting plan',
                'disk_space' => 5000,
                'bandwidth' => 50000,
                'databases' => 5,
                'ftp_accounts' => 5,
                'email_accounts' => 5,
                'subdomains' => 5,
                'parked_domains' => 5,
                'addon_domains' => 5,
                'ssl_certificates' => 0,
                'daily_backups' => 0,
                'free_domain' => 0,
                'additional_services' => [],
                'features' => [],
                'limitations' => [],
                'default_server_application_type' => 'apache_php',
                'default_server_application_settings'=>[
                    'php_version' => '8.3',
                ]
            ]);
        }

        $findHostingPlan = HostingPlan::where('name', 'Hosting Pro')->first();
        if (! $findHostingPlan) {
            HostingPlan::create([
                'name' => 'Hosting Pro',
              //  'slug' => 'pro',
                'description' => 'Pro hosting plan',
                'disk_space' => 10000,
                'bandwidth' => 100000,
                'databases' => 10,
                'ftp_accounts' => 10,
                'email_accounts' => 10,
                'subdomains' => 10,
                'parked_domains' => 10,
                'addon_domains' => 10,
                'ssl_certificates' => 1,
                'daily_backups' => 1,
                'free_domain' => 0,
                'additional_services' => [],
                'features' => [],
                'limitations' => [],
                'default_server_application_type' => 'apache_php',
                'default_server_application_settings'=>[
                    'php_version' => '8.3',
                ]
            ]);
        }

        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
