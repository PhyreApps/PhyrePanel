<?php

namespace Tests\Unit;

use App\Installers\Server\Applications\PHPInstaller;
use App\SupportedApplicationTypes;
use Illuminate\Support\Str;
use Tests\TestCase;

class AutoInstallTest extends TestCase
{
    public function testInstall()
    {

        $this->assertTrue(Str::contains(php_uname(),'Ubuntu'));
//
        // Make Apache+PHP Application Server with all supported php versions and modules
        $installLogFilePath = storage_path('install-apache-php-log-unit-test.txt');
        //unlink($installLogFilePath);

        $phpInstaller = new PHPInstaller();
        $phpInstaller->setPHPVersions([
            '8.2'
        ]);
        $phpInstaller->setPHPModules(array_keys(SupportedApplicationTypes::getPHPModules()));
        $phpInstaller->setLogFilePath($installLogFilePath);
        $phpInstaller->install();

        $installationSuccess = false;
        for ($i = 1; $i <= 200; $i++) {
            $logContent = file_get_contents($installLogFilePath);
            if (str_contains($logContent, 'All packages installed successfully!')) {
                $installationSuccess = true;
                break;
            }
            sleep(3);

        }

        if (!$installationSuccess) {
            $logContent = file_get_contents($installLogFilePath);
            $this->fail('Apache+PHP installation failed. Log: '.$logContent);
        }

        $this->assertTrue($installationSuccess, 'Apache+PHP installation failed');

    }
}
