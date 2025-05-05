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
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Str;
use Modules\LetsEncrypt\Filament\Clusters\LetsEncryptCluster;
use Outerweb\FilamentSettings\Filament\Pages\Settings as BaseSettings;

class IssueWildcardCertificate extends BaseSettings
{
    protected static ?string $navigationGroup = 'Let\'s Encrypt';

    protected static ?string $cluster = LetsEncryptCluster::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

    protected static ?int $navigationSort = 2;

    public $poolingInstallLog = true;
    public $installLog = '';
    public $installLogFilePath = '/var/www/acme-wildcard-install.log';
    public $installInstructions = [];
    public ?array $data = [];

    public static function getNavigationLabel(): string
    {
        return 'Issue Wildcard Certificate';
    }

    public function getFormActions(): array
    {
        return [

        ];
    }

    public function checkCertificateFilesExist($domain)
    {

        //check file
        $sslCertificateFilePath = '/root/.acme.sh/*.' . $domain . '_ecc/*.' . $domain . '.cer';
        $sslCertificateKeyFilePath = '/root/.acme.sh/*.' . $domain . '_ecc/*.' . $domain . '.key';
        $sslCertificateChainFilePath = '/root/.acme.sh/*.' . $domain . '_ecc/fullchain.cer';


        if (file_exists($sslCertificateFilePath)
            && file_exists($sslCertificateKeyFilePath)
            && file_exists($sslCertificateChainFilePath)
        ) {

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

    public function requestCertificates()
    {

        $masterDomain = new MasterDomain();
        $masterDomain->domain = setting('general.wildcard_domain');

        if (file_exists($this->installLogFilePath)) {
            unlink($this->installLogFilePath);
        }

        $acmeCommand = "bash /usr/local/phyre/web/Modules/LetsEncrypt/shell/acme.sh --register-account -m $masterDomain->email ";
        $acmeCommand = shell_exec($acmeCommand);

        $acmeCommand = "bash /usr/local/phyre/web/Modules/LetsEncrypt/shell/acme.sh --issue -d '*.$masterDomain->domain' --dns --yes-I-know-dns-manual-mode-enough-go-ahead-please";
        $acmeCommand = shell_exec($acmeCommand . " >> $this->installLogFilePath &");

        return [
            'success' => 'SSL certificate request sent.',
            'commandOutput' => $acmeCommand
        ];

    }

    public function installCertificates()
    {
        $masterDomain = new MasterDomain();
        $masterDomain->domain = setting('general.wildcard_domain');

        $acmeCommand = "bash /usr/local/phyre/web/Modules/LetsEncrypt/shell/acme.sh --renew -d '*.$masterDomain->domain' --dns --yes-I-know-dns-manual-mode-enough-go-ahead-please";
        $acmeCommand = shell_exec($acmeCommand);


        $done = ['And the full-chain cert is in', 'seems to already have an'];

        $isDone = false;

        foreach ($done as $item) {
            if (str_contains($acmeCommand, $item)) {
                $isDone = true;
              //  break;
            }
        }


        if ($isDone) {

            $checkCertificateFilesExist = $this->checkCertificateFilesExist($masterDomain->domain);

            if (isset($checkCertificateFilesExist['sslFiles']['certificateContent'])) {

                $findWildcardSsl = DomainSslCertificate::where('domain', '*.' . $masterDomain->domain)->first();
                if (!$findWildcardSsl) {
                    $findWildcardSsl = new DomainSslCertificate();
                    $findWildcardSsl->domain = '*.' . $masterDomain->domain;
                    $findWildcardSsl->customer_id = 0;
                    $findWildcardSsl->is_active = 1;
                    $findWildcardSsl->is_wildcard = 1;
                    $findWildcardSsl->is_auto_renew = 1;
                    $findWildcardSsl->provider = 'AUTO_SSL';
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
        }

        return [
            'error' => 'SSL certificate not found.'
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

    public function form(Form $form): Form
    {
        if (request()->get('step', null) === 'verification') {
            $this->getInstallLog();
        }


        $form->schema([
            Wizard::make([
                Wizard\Step::make('IssueWildcardCertificate Install')
                    ->description('Issue new wildcard SSL certificate for domain')
                    ->schema([
                        TextInput::make('wildcard_domain')
                            ->helperText('Issue new wildcard SSL certificate for domain. Example: *.mysite.com')
                            ->placeholder('*.mysite.com'),
                    ])->afterValidation(function () {
                        if (file_exists($this->installLogFilePath)) {
                            unlink($this->installLogFilePath);
                        }

                        $this->poolingInstallLog = true;
                        $log = $this->requestCertificates();

                        if (!isset($log['success'])) {
                            Notification::make()
                                ->title('Failed to request SSL certificate.')
                                ->body('Please, try again.')
                                ->danger()
                                ->send();
                            throw new Halt();
                        }
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
        ]);

        $form->statePath('data');
        return $form;
    }
}
