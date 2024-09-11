<?php

namespace App;

class GitClient
{

    public static function parseGitUrl($url)
    {
        $name = '';
        $owner = '';
        $provider = '';

        // first type is: git@github.com:microweber-dev/panel.git
        // second type is: https://github.com/microweber-dev/panel.git
        // third type is: https://github.com/microweber-dev/panel
        // fourth type is: http://gitlab.com/microweber-dev/panel.git

        if (str_contains($url, ':') && str_contains($url, 'git@')) {
            $urlExploded = explode(':', $url);

            $provider = $urlExploded[0];
            $provider = str_replace('git@', '', $provider);

            $urlExploded = explode('/', $urlExploded[1]);
            $owner = $urlExploded[0];
            $name = str_replace('.git', '', $urlExploded[1]);

        } else if (str_contains($url, '.git')) {

            $parsedUrl = parse_url($url);

            $provider = $parsedUrl['host'];

            $urlExploded = explode('/', $parsedUrl['path']);

            $owner = $urlExploded[1];
            $name = str_replace('.git', '', $urlExploded[2]);

        } else {
            $parsedUrl = parse_url($url);

            $provider = $parsedUrl['host'];

            $urlExploded = explode('/', $parsedUrl['path']);

            $owner = $urlExploded[1];
            $name = $urlExploded[2];
        }

        return [
            'name' => $name,
            'owner' => $owner,
            'provider' => $provider
        ];

    }

    public static function getRepoDetailsByUrl($url)
    {
        $url = trim($url);

        if (empty($url)) {
            return [];
        }

        shell_exec('GIT_TERMINAL_PROMPT=0');
        $outputBranches = shell_exec('git ls-remote --heads '.$url);
        $outputTags = shell_exec('git ls-remote --tags '.$url);

        $branches = [];
        $tags = [];

        $gitUrl = self::parseGitUrl($url);

        foreach (explode("\n", $outputBranches) as $line) {
            if (empty($line)) {
                continue;
            }
            $parts = explode("\t", $line);
            $branch = str_replace('refs/heads/', '', $parts[1]);
            $branches[$branch] = $branch;
        }

        foreach (explode("\n", $outputTags) as $line) {
            if (empty($line)) {
                continue;
            }
            $parts = explode("\t", $line);
            $tag = str_replace('refs/tags/', '', $parts[1]);
            $tags[$tag] = $tag;
        }

        $output = [
            'branches' => $branches,
            'tags' => $tags,
            'name' => $gitUrl['name'],
            'owner' => $gitUrl['owner'],
            'provider' => $gitUrl['provider']
        ];

        return $output;
    }

}
