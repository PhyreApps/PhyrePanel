<?php

namespace App\Console\Commands;

use App\Installers\Server\Applications\PHPInstaller;
use App\SupportedApplicationTypes;
use Illuminate\Console\Command;

class InstallApache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:install-apache';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Apache web server';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phpVersions = array_keys(SupportedApplicationTypes::getPHPVersions());
        $phpModules = array_keys(SupportedApplicationTypes::getPHPModules());

        // Get the latest four versions of PHP
        $phpVersions = array_filter($phpVersions, function ($version) {
            return version_compare($version, '8.0', '>=');
        });
        $phpVersions = array_filter($phpVersions, function ($version) {
            return version_compare($version, '8.2', '<=');
        });

        $phpInstaller = new PHPInstaller();
        $phpInstaller->setPHPVersions($phpVersions);
        $phpInstaller->setPHPModules($phpModules);
        $getCommands = $phpInstaller->commands();

        foreach ($getCommands as $command) {
            try {
                $this->info(shell_exec($command));
            } catch (\Exception $e) {
                $this->error($e->getMessage());
            }
        }

    }
}
