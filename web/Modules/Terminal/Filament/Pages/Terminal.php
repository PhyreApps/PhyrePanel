<?php

namespace Modules\Terminal\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Str;

class Terminal extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-command-line';

    protected static string $view = 'terminal::filament.pages.terminal';

    protected static ?string $navigationGroup = 'Server Management';

    protected static ?string $navigationLabel = 'Terminal';

    protected static ?int $navigationSort = 1;

    protected function getViewData(): array
    {
        // Get server ip
        $serverIp = shell_exec("hostname -I | awk '{print $1}'");
        $serverIp = trim($serverIp);

        $sessionId = md5(session()->getId());

        $appTerminalConfigFile = storage_path('app/terminal/config.json');
        if (!is_dir($appTerminalConfigFile)) {
            shell_exec('mkdir -p ' . dirname($appTerminalConfigFile));
        }

        file_put_contents($appTerminalConfigFile, json_encode([
            'serverIp' => $serverIp,
        ], JSON_PRETTY_PRINT));

        $appTerminalSessionsPath = storage_path('app/terminal/sessions');
        if (!is_dir($appTerminalSessionsPath)) {
            shell_exec('mkdir -p ' . $appTerminalSessionsPath);
        }
        if (is_dir($appTerminalSessionsPath)) {
            shell_exec('rm -rf ' . $appTerminalSessionsPath.'/*');
        }

        $sessionStorageFile = $appTerminalSessionsPath . '/' . $sessionId;
        if (!is_file($sessionStorageFile)) {
            file_put_contents($sessionStorageFile, json_encode([
                'sessionId' => $sessionId,
                'commands' => [],
                'user' => 'root',
            ], JSON_PRETTY_PRINT));
        }

        $runNewTerminal = true;
        $checkPort = shell_exec('netstat -tuln | grep 8449');
        if (!empty($checkPort)) {
            if (Str::contains($checkPort, 'LISTEN')) {
                $runNewTerminal = false;
            }
        }
        if ($runNewTerminal) {
            if (!is_dir('/usr/local/phyre/web/Modules/Terminal/nodejs/terminal/node_modules')) {
                $exec = shell_exec('cd /usr/local/phyre/web/Modules/Terminal/nodejs/terminal && npm install');
            }
            $exec = shell_exec('node /usr/local/phyre/web/Modules/Terminal/nodejs/terminal/server.js >> /usr/local/phyre/web/storage/logs/terminal/server-terminal.log &');
        }

        return [
            'title' => 'Terminal',
            'sessionId' => $sessionId,
        ];
    }
}
