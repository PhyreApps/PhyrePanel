<?php

namespace Modules\LetsEncrypt\Filament\Clusters\LetsEncrypt\Pages;

use App\ApiClient;
use App\Jobs\ApacheBuild;
use App\MasterDomain;
use App\Models\DomainSslCertificate;
use App\Settings;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Str;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncryptCluster;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class WildcardDomain extends BaseSettings
{
    protected static ?string $navigationGroup = 'Let\'s Encrypt';

    protected static ?string $cluster = LetsEncryptCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?int $navigationSort = 2;

    public $poolingInstallLog = true;
    public $installLog = '';
    public $installLogFilePath =  '/var/www/acme-wildcard-install.log';
    public $installInstructions = [];

    public static function getNavigationLabel() : string
    {
        return 'Wildcard Domain';
    }
    public function getFormActions() : array
    {
        return [

        ];
    }

    public function checkCertificateFilesExist($domain)
    {

        $sslCertificateFilePath = '/root/.acmephp/master/certs/*.'.$domain.'/public/cert.pem';
        $sslCertificateKeyFilePath = '/root/.acmephp/master/certs/*.'.$domain.'/private/key.private.pem';
        $sslCertificateChainFilePath = '/root/.acmephp/master/certs/*.'.$domain.'/public/fullchain.pem';


        if (file_exists($sslCertificateFilePath)
            && file_exists($sslCertificateKeyFilePath)
            && file_exists($sslCertificateChainFilePath)) {

            $sslCertificateFileContent = file_get_contents($sslCertificateFilePath);
            $sslCertificateKeyFileContent = file_get_contents($sslCertificateKeyFilePath);
            $sslCertificateChainFileContent = file_get_contents($sslCertificateChainFilePath);


            return [
                'sslFiles' => [
                    'certificate' => $sslCertificateFilePath,
                    'certificateContent' => $sslCertificateFileContent,
                    'privateKey' => $sslCertificateKeyFilePath,
                    'privateKeyContent' => $sslCertificateKeyFileContent,
                    'certificateChain' => $sslCertificateChainFilePath,
                    'certificateChainContent' => $sslCertificateChainFileContent
                ]
            ];
        }

        return false;

    }

    public function installCertificates()
    {
        $masterDomain = new MasterDomain();
        $masterDomain->domain = setting('general.wildcard_domain');

        $checkCertificateFilesExist = $this->checkCertificateFilesExist($masterDomain->domain);
        if (isset($checkCertificateFilesExist['sslFiles']['certificateContent'])) {

            $findWildcardSsl = DomainSslCertificate::where('domain', '*.'.$masterDomain->domain)->first();
            if (!$findWildcardSsl) {
                $findWildcardSsl = new DomainSslCertificate();
                $findWildcardSsl->domain = '*.'.$masterDomain->domain;
                $findWildcardSsl->customer_id = 0;
                $findWildcardSsl->is_active = 1;
                $findWildcardSsl->is_wildcard = 1;
                $findWildcardSsl->is_auto_renew = 1;
                $findWildcardSsl->provider = 'letsencrypt';
            }

            $findWildcardSsl->certificate = $checkCertificateFilesExist['sslFiles']['certificateContent'];
            $findWildcardSsl->private_key = $checkCertificateFilesExist['sslFiles']['privateKeyContent'];
            $findWildcardSsl->certificate_chain = $checkCertificateFilesExist['sslFiles']['certificateChainContent'];
            $findWildcardSsl->save();


            $mds = new MasterDomain();
            $mds->configureVirtualHost();

            ApacheBuild::dispatchSync();

            return [
                'success' => 'Domain SSL certificate updated.'
            ];

        }

        if (file_exists($this->installLogFilePath)) {
            unlink($this->installLogFilePath);
        }

        $acmeConfigYaml = view('letsencrypt::actions.acme-config-wildcard-yaml', [
            'domain' => $masterDomain->domain,
            'domainRoot' => $masterDomain->domainRoot,
            'domainPublic' => $masterDomain->domainPublic,
            'email' => $masterDomain->email,
            'country' => $masterDomain->country,
            'locality' => $masterDomain->locality,
            'organization' => $masterDomain->organization
        ])->render();

        $acmeConfigYaml = preg_replace('~(*ANY)\A\s*\R|\s*(?!\r\n)\s$~mu', '', $acmeConfigYaml);

        file_put_contents($masterDomain->domainRoot.'/acme-wildcard-config.yaml', $acmeConfigYaml);

        $amePHPPharFile = base_path().'/Modules/LetsEncrypt/Actions/acmephp.phar';

        if (!is_dir(dirname($this->installLogFilePath))) {
            shell_exec('mkdir -p ' . dirname($this->installLogFilePath));
        }

        $phyrePHP = 'phyre-php';
        $command = $phyrePHP.' '.$amePHPPharFile.' run '.$masterDomain->domainRoot.'/acme-wildcard-config.yaml >> ' . $this->installLogFilePath . ' &';
        shell_exec($command);

        return [
            'success' => 'SSL certificate request sent.'
        ];
    }

    public function getInstallLog()
    {
        $installLog = '';
        if (file_exists($this->installLogFilePath)) {
            $installLog = file_get_contents($this->installLogFilePath);
        }

        if (Str::contains($installLog, 'Add the following TXT record')) {
            $acmeChallangeDomain = '';
            $acmeChallangeTxtValue = '';
            foreach (explode("\n", $installLog) as $line) {
                if (Str::contains($line, 'Domain:')) {
                    $acmeChallangeDomain = trim($line);
                }
                if (Str::contains($line, 'TXT value:')) {
                    $acmeChallangeTxtValue = trim($line);
                }
            }
            $acmeChallangeDomain = str_replace('Domain: ', '', $acmeChallangeDomain);
            $acmeChallangeTxtValue = str_replace('TXT value: ', '', $acmeChallangeTxtValue);
            $this->installInstructions = [
                'acmeChallangeDomain' => $acmeChallangeDomain,
                'acmeChallangeTxtValue' => $acmeChallangeTxtValue,
            ];
            $this->poolingInstallLog = false;

        } else {
            $installLog = str_replace("\n", "<br />", $installLog);
            $this->installLog = $installLog;
            $this->poolingInstallLog = true;
        }

    }

    public function schema(): array
    {
        if (request()->get('step', null) === 'verification') {
            $this->getInstallLog();
        }

        return [

            Wizard::make([
                Wizard\Step::make('Install')
                    //->description('Install a wildcard SSL certificate for the master domain')
                    ->schema([
                        TextInput::make('wildcard_domain')
                            ->helperText('Install a wildcard SSL certificate for the master domain')
                            ->placeholder(setting('general.wildcard_domain'))
                            ->disabled(),
                    ])->afterValidation(function () {
                        if (file_exists($this->installLogFilePath)) {
                            unlink($this->installLogFilePath);
                        }
                        $this->poolingInstallLog = true;
                        $this->installCertificates();
                    }),
                Wizard\Step::make('Verification')
                   // ->description('Adding TXT record in DNS zone to verify domain ownership')
                    ->schema([

                       TextInput::make('installInstructions')
                           ->view('letsencrypt::filament.wildcard_install_instructions')
                           ->label('Installation Instructions')


                    ])->afterValidation(function () {

                        $log = $this->installCertificates();

                        if (isset($log['success'])) {
                            Notification::make()
                                ->title('SSL certificate installed successfully.')
                                ->body('You can now use SSL certificate for your domain.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Failed to verify domain ownership.')
                                ->body('Please, add TXT record in DNS zone and try again.')
                                ->danger()
                                ->send();

                            if (isset($this->installInstructions['acmeChallangeDomain'])) {
                                Notification::make()
                                    ->title('Check TXT record in DNS zone')
                                    ->body(shell_exec('host -t TXT ' . $this->installInstructions['acmeChallangeDomain']))
                                    ->warning()
                                    ->send();
                            }

                            throw new Halt();
                        }

                    }),
                Wizard\Step::make('Finish')
                    ->schema([
                        TextInput::make('installFinished')
                            ->view('letsencrypt::filament.wildcard_install_finish')
                            ->label('Installation Finished')
                    ]),
            ])
                ->persistStepInQueryString(),
        ];
    }
}
