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
        $sessionId = session()->getId();

        $runNewTerminal = true;
        $checkPort = shell_exec('netstat -tuln | grep 8449');
        if (!empty($checkPort)) {
            if (Str::contains($checkPort, 'LISTEN')) {
                $runNewTerminal = false;
            }
        }
        if ($runNewTerminal) {
            $exec = shell_exec('node /usr/local/phyre/web/Modules/Terminal/nodejs/terminal/server.js >> /usr/local/phyre/web/storage/logs/terminal/server-terminal.log &');
        }

        return [
            'title' => 'Terminal',
            'sessionId' => $sessionId,
        ];
    }
}
