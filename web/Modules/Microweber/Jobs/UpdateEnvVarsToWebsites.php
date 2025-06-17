<?php

namespace Modules\Microweber\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MicroweberPackages\SharedServerScripts\MicroweberEnvFileWebsiteApply;
use Modules\Microweber\App\Models\MicroweberInstallation;

class UpdateEnvVarsToWebsites implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public static $displayName = 'Update Environment Variables To Websites';
    public static $displayDescription = 'Apply environment variables settings to all websites';


    public $websiteInstallationId = null;


    public function handle(): void
    {
        set_time_limit(0);


        if($this->websiteInstallationId){
            $mwInstallations = MicroweberInstallation::where('id', $this->websiteInstallationId)->get();
            if ($mwInstallations->isEmpty()) {
                return;
            }
        } else {
            $mwInstallations = MicroweberInstallation::all();
        }

        $mwInstallations = MicroweberInstallation::all();
        if ($mwInstallations->isEmpty()) {
            return;
        }

        $envSettings = setting('microweber.env_vars');
        if (empty($envSettings)) {
            return;
        }

        // Convert settings to env vars format
        $envVars = $this->prepareEnvVars($envSettings);

        foreach ($mwInstallations as $mwInstallation) {
            try {
                $envApply = new MicroweberEnvFileWebsiteApply();
                $envApply->setWebPath($mwInstallation->installation_path);
                $envApply->applyEnvVars($envVars);
            } catch (\Exception $e) {
                // Log error but continue with other installations
                \Log::error('Error applying env vars to website: ' . $mwInstallation->installation_path . ' - ' . $e->getMessage());
            }
        }
    }

    public function prepareEnvVars(array $settings): array
    {
        $envVars = [];



        // Process custom environment variables
        if (!empty($settings['custom_env'])) {
            $customVars = explode("\n", $settings['custom_env']);
            foreach ($customVars as $line) {
                $line = trim($line);
                if (empty($line) || strpos($line, '=') === false) {
                    continue;
                }
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                if (!empty($key)) {
                    $envVars[$key] = $value;
                }
            }
        }

        return $envVars;
    }


    public function setInstallationId($websiteInstallationId): void
    {
        $this->websiteInstallationId = $websiteInstallationId;
    }
}
