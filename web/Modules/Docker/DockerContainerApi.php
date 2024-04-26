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

    public function recreate($containerId)
    {
        shell_exec('docker stop ' . $containerId);
        shell_exec('docker rm ' . $containerId);

        return $this->run();
    }

    public function run()
    {
        $commandId = rand(10000, 99999);

        $shellFileContent = 'docker run -it --network=host --name ' . $this->name . ' ';

        if (!empty($this->port)) {
            $shellFileContent .= ' -p ' . $this->externalPort . ':' . $this->port . ' ';
        }
        $shellFileContent .= '-d ' . $this->image . ' ';

//        if (!empty($this->environmentVariables)) {
//            foreach ($this->environmentVariables as $key => $value) {
//                $commands[] = '-e ' . $key . '=' . $value;
//            }
//        }
//
//        if (!empty($this->volumeMapping)) {
//            foreach ($this->volumeMapping as $key => $value) {
//                $commands[] = '-v ' . $key . ':' . $value;
//            }
//        }

        $shellFileContent .= PHP_EOL . 'rm -f /tmp/docker-run-container-'.$commandId.'.sh';

       // dd($shellFileContent);

        file_put_contents('/tmp/docker-run-container-'.$commandId.'.sh', $shellFileContent);
        $output = shell_exec('bash /tmp/docker-run-container-'.$commandId.'.sh');

        // Get docker container id from output
        $dockerContainerId = trim($output);
        $output = shell_exec('docker ps --format json --filter id='.$dockerContainerId);
        $output = json_decode($output, true);

        return $output;
    }
}
