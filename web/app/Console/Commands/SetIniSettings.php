<?php

namespace App\Console\Commands;

use Dotenv\Dotenv;
use Illuminate\Console\Command;
use Jelix\IniFile\IniModifier;

class SetIniSettings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:set-ini-settings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
//        $ini = new IniModifier('phyre-config.ini');
//        $ini->setValue('', '', 'phyre');
//        $ini->save();

    }
}
