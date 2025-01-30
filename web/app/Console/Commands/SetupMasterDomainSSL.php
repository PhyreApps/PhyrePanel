<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SetupMasterDomainSSL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'phyre:setup-master-domain-ssl';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install SSL certificate for the master domain';

    /**
     * The master domain name.
     *
     * @var string
     */
    protected $masterDomain;

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {

        do {
            $this->askForMasterDomain();
            $changeMasterDomain = $this->confirm('Are you sure you want to proceed with this master domain?', true);
        } while (!$changeMasterDomain);

        setting([
            'general.master_domain' => $this->masterDomain,
        ]);

        $this->info('Setting up SSL certificate for the master domain...');
        $this->info('This may take a few minutes. Please wait...');

    }

    public function askForMasterDomain()
    {
        do {
            $this->masterDomain = $this->ask('Enter valid Master Domain name (e.g. myhost.com):');

            if (!$this->isValidDomain($this->masterDomain)) {
                $this->error('Invalid domain name entered. Please try again.');
            }
        } while (!$this->isValidDomain($this->masterDomain));

        $this->info("Master domain: $this->masterDomain");
        $this->info('Your PhyrePanel will be visitable at https://' . $this->masterDomain.':8443');


        
    }

    /**
     * Validate the given domain name.
     *
     * @param string $domain
     * @return bool
     */
    private function isValidDomain($domain)
    {
        return (bool) preg_match('/^(?!-)([a-zA-Z0-9-]{1,63}(?<!-)\.)+[a-zA-Z]{2,}$/', $domain);
    }
}
