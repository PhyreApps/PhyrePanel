<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Jelix\IniFile\IniModifier;

class KeyGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:key-generate';

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
        $randomAppKey = 'base64:'.base64_encode(random_bytes(32));

        $ini = new IniModifier('phyre-config.ini');
        $ini->setValue('APP_KEY', $randomAppKey, 'phyre');
        $ini->save();

        $this->info('Application key set successfully.');

    }
}
