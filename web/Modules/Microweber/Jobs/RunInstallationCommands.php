<?php

namespace Modules\Microweber\Jobs;

use App\Models\HostingSubscription;
use App\ShellApi;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Microweber\App\Models\MicroweberInstallation;

class RunInstallationCommands implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public static $displayName = 'Run Installation Commands';
    public static $displayDescription = 'Execute commands on Microweber installations';

    protected $command;
    protected $installationId;

    public function __construct(string $command, ?int $installationId = null)
    {
        $this->command = $command;
        $this->installationId = $installationId;
    }

    public function handle(): void
    {
        set_time_limit(0);

        if ($this->installationId) {
            $installations = MicroweberInstallation::where('id', $this->installationId)->get();
        } else {
            $installations = MicroweberInstallation::all();
        }

        if ($installations->isEmpty()) {
            \Log::info('No installations found to run command: ' . $this->command);
            return;
        }

        foreach ($installations as $installation) {

            $this->runCommandForInstallation($installation, $this->command);


//            try {
//            } catch (\Exception $e) {
//                \Log::error('Error running command "' . $this->command . '" on installation: ' . $installation->installation_path . ' - ' . $e->getMessage());
//            }
        }
    }

    private function runCommandForInstallation(MicroweberInstallation $installation, string $command): void
    {
        $domain = $installation->domain;
        if (!$domain) {
            \Log::warning('Domain not found for installation: ' . $installation->id);
            return;
        }

        $hostingSubscription = HostingSubscription::where('id', $domain->hosting_subscription_id)->first();
        if (!$hostingSubscription) {
            \Log::warning('Hosting subscription not found for domain: ' . $domain->id);
            return;
        }

        $installationPath = $installation->installation_path;
        if (!is_dir($installationPath)) {
            \Log::warning('Installation path does not exist: ' . $installationPath);
            return;
        }

        $username = $hostingSubscription->system_username;

        switch ($command) {
            case 'cache:clear':
                $this->runArtisanCommand($installationPath, 'cache:clear', $username);
                break;

            case 'microweber:vendor-assets-symlink':
                $this->runArtisanCommand($installationPath, 'microweber:vendor-assets-symlink', $username);
                break;
            case 'microweber:reload-database':
                $this->runArtisanCommand($installationPath, 'microweber:reload-database', $username);
                break;

            case 'composer:dump':
                $this->runComposerCommand($installationPath, 'dump-autoload', $username);
                break;

            case 'composer:publish-assets':
                $this->runComposerCommand($installationPath, 'publish-assets', $username);
                break;

            default:
                // For custom artisan commands
                $this->runArtisanCommand($installationPath, $command, $username);
                break;
        }

        \Log::info("Command '{$command}' executed successfully for installation: {$installationPath}");
    }

    private function runArtisanCommand(string $installationPath, string $command, string $username): void
    {
        $artisanPath = $installationPath . '/artisan';

        if (!file_exists($artisanPath)) {
            throw new \Exception('Artisan file not found at: ' . $artisanPath);
        }

        $fullCommand = "cd {$installationPath} && sudo -u {$username} php {$artisanPath} {$command}";
        \Log::info("Executing artisan command: {$fullCommand}");

        $result = ShellApi::exec($fullCommand);

        if (is_array($result) and $result['exit_code'] !== 0) {
            throw new \Exception('Artisan command failed: ' . $result['output']);
        }
    }

    private function runComposerCommand(string $installationPath, string $command, string $username): void
    {
        $composerPath = $installationPath . '/composer.json';

        if (!file_exists($composerPath)) {
            throw new \Exception('Composer.json file not found at: ' . $composerPath);
        }

        $fullCommand = "cd {$installationPath} && sudo -u {$username} composer {$command}";


        \Log::info("Executing composer command: {$fullCommand}");

        $result = ShellApi::exec($fullCommand);

//        if(!isset( $result['exit_code'])){
//            dd($result);
//        }
//
//        if ($result['exit_code'] !== 0) {
//            throw new \Exception('Composer command failed: ' . $result['output']);
//        }
    }
}
