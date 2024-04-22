<?php

namespace Modules\Docker;

class DockerApi
{
    public $logFile = '';

    public function setLogFile($logFile)
    {
        $this->logFile = $logFile;
    }

    public function pullImage($name)
    {
        $commandId = rand(10000, 99999);
        $commands = [];
        $commands[] = 'docker pull ' . $name;

        $shellFileContent = '';

        foreach ($commands as $command) {
            $shellFileContent .= $command . PHP_EOL;
        }

        $shellFileContent .= 'echo "DONE!"' . PHP_EOL;
        $shellFileContent .= 'rm -f /tmp/docker-pull-image-'.$commandId.'.sh';

        file_put_contents('/tmp/docker-pull-image-'.$commandId.'.sh', $shellFileContent);
        shell_exec('bash /tmp/docker-pull-image-'.$commandId.'.sh >> ' . $this->logFile . ' &');

    }

    public function searchImages($keyword, $filters = [])
    {
        $filtersString = ''; // --filter is-official=true

        $output = shell_exec('docker search --format "{{json .}}" --no-trunc '.$filtersString.' ' . $keyword);
        $output = trim($output);
        $output = str_replace("\n", ',', $output);
        $output = '[' . $output . ']';
        $dockerSearch = json_decode($output, true);

        if ($dockerSearch === null) {
            return [];
        }

        return $dockerSearch;
    }

    public function getContainers()
    {
        $output = shell_exec('docker ps -a --format "{{json .}}"');
        $output = trim($output);
        $output = str_replace("\n", ',', $output);
        $output = '[' . $output . ']';
        $dockerContainers = json_decode($output, true);

        if ($dockerContainers === null) {
            return [];
        }

        return $dockerContainers;
    }

    public function removeContainerById($id)
    {
        shell_exec('docker rm -f ' . $id);
    }

    public function restartContainer($id)
    {
        shell_exec('docker restart ' . $id);
    }

    public function stopContainer($id)
    {
        shell_exec('docker stop ' . $id);
    }

    public function startContainer($id)
    {
        shell_exec('docker start ' . $id);
    }

    public function getContainerLogs($id)
    {
        $output = shell_exec('docker logs ' . $id .' > /tmp/docker-logs-'.$id.'.log  2>&1');
        $logContent = '';
        if (file_exists('/tmp/docker-logs-'.$id.'.log')) {
            $logContent = file_get_contents('/tmp/docker-logs-'.$id.'.log');
        }
        return $logContent;
    }

    public function getContainerStats($id)
    {
        $output = shell_exec('docker stats --format json --no-stream ' . $id);
        $output = json_decode($output, true);

        return $output;
    }

    public function getContainerProcesses($id)
    {
        $output = shell_exec('docker top ' . $id);
        return $output;
    }

    public function getContainerById($id)
    {
        $output = shell_exec('docker ps -f id='.$id.' -a --format "{{json .}}"');

        $output = trim($output);
        $output = str_replace("\n", ',', $output);
        $output = '[' . $output . ']';
        $dockerContainer = json_decode($output, true);

        if (!isset($dockerContainer[0])) {
            return [];
        }

        return $dockerContainer[0];

    }

    public function getContainerInspect($id)
    {
        $output = shell_exec('docker inspect ' . $id);
        return $output;
    }

    public function getDockerImageInspect($name)
    {
        $output = shell_exec('docker image inspect ' . $name);
        $output = json_decode($output, true);
        if (isset($output[0])) {
            return $output[0];
        }

        return [];
    }

}
