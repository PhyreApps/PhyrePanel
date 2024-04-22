<?php

namespace Modules\Docker;

use function AlibabaCloud\Client\json;

class DockerContainerApi
{
    public $image = '';
    public $environmentVariables = [];
    public $volumeMapping = [];

    public $port = '';
    public $externalPort = '';

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
        $this->port = $port;
    }

    public function setExternalPort($externalPort)
    {
        $this->externalPort = $externalPort;
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
        $commands = [];
        $commands[] = 'docker run -d ' . $this->image;

        if (!empty($this->port)) {
            $commands[] = '-p ' . $this->port . ':' . $this->externalPort;
        }

        if (!empty($this->environmentVariables)) {
            foreach ($this->environmentVariables as $key => $value) {
                $commands[] = '-e ' . $key . '=' . $value;
            }
        }

        if (!empty($this->volumeMapping)) {
            foreach ($this->volumeMapping as $key => $value) {
                $commands[] = '-v ' . $key . ':' . $value;
            }
        }

        $shellFileContent = '';

        foreach ($commands as $command) {
            $shellFileContent .= $command . PHP_EOL;
        }

        $shellFileContent .= 'rm -f /tmp/docker-run-container-'.$commandId.'.sh';

        file_put_contents('/tmp/docker-run-container-'.$commandId.'.sh', $shellFileContent);
        $output = shell_exec('bash /tmp/docker-run-container-'.$commandId.'.sh');

        // Get docker container id from output
        $dockerContainerId = trim($output);
        $output = shell_exec('docker ps --format json --filter id='.$dockerContainerId);
        $output = json_decode($output, true);

        return $output;
    }
}
