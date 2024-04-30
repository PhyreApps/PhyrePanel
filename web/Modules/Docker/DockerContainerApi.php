<?php

namespace Modules\Docker;

use function AlibabaCloud\Client\json;

class DockerContainerApi
{
    public $name = '';
    public $image = '';
    public $environmentVariables = [];
    public $volumeMapping = [];

    public $port = '';
    public $externalPort = '';

    public $dockerCompose = '';

    public function setName($name)
    {
        $name = trim($name);
        $this->name = $name;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function setEnvironmentVariables($environmentVariables)
    {
        $this->environmentVariables = $environmentVariables;
    }

    public function setVolumeMapping($volumeMapping)
    {
        $this->volumeMapping = $volumeMapping;
    }

    public function setPort($port)
    {
        $port = trim($port);
        $this->port = $port;
    }

    public function setExternalPort($externalPort)
    {
        $externalPort = trim($externalPort);
        $this->externalPort = $externalPort;
    }

    public function setDockerCompose($dockerCompose)
    {
        $this->dockerCompose = $dockerCompose;
    }

    public function recreate($containerId)
    {
        shell_exec('docker stop ' . $containerId);
        shell_exec('docker rm ' . $containerId);

        return $this->run();
    }

    public function run()
    {
        $commandId = rand(10000, 99999);

        if (!empty($this->dockerCompose)) {
            $dockerComposeFileContent = $this->dockerCompose;
        } else {
            $dockerComposeFileContent = view('docker::actions.docker-compose-yml', [
                'name' => $this->name,
                'image' => $this->image,
                'port' => $this->port,
                'externalPort' => $this->externalPort,
                'environmentVariables' => $this->environmentVariables,
                'volumeMapping' => $this->volumeMapping,
                'version' => '3'
            ])->render();
        }

        $dockerContaienrPath = storage_path('docker/'.$this->name);
        if (!is_dir($dockerContaienrPath)) {
            shell_exec('mkdir -p ' . $dockerContaienrPath);
        }


        $dockerComposeFile = $dockerContaienrPath . '/docker-compose.yml';
        file_put_contents($dockerComposeFile, $dockerComposeFileContent);

        $output = shell_exec("cd $dockerContaienrPath && docker-compose up -d");

        // Get docker container id from output
        $output = shell_exec('docker ps --format json --filter name='.$this->name);
        $output = json_decode($output, true);

        return $output;
    }
}
