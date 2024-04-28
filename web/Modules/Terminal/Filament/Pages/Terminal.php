<?php

namespace Modules\Terminal\Filament\Pages;

use Filament\Pages\Page;

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

        shell_exec('node /usr/local/phyre/web/nodejs/terminal/server.js >> /usr/local/phyre/web/storage/logs/terminal/server-terminal.log &');

        return [
            'title' => 'Terminal',
            'sessionId' => $sessionId,
        ];
    }
}
