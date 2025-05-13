<?php

namespace Modules\SSLManager\App\Filament\Pages;

use App\Settings;
use App\ApiClient;
use App\MasterDomain;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Jobs\ApacheBuild;
use Illuminate\Support\Str;
use Filament\Actions\Action;
use App\Models\DomainSslCertificate;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Wizard;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;

class WildcardIssuer extends Page
{
    protected static ?string $navigationGroup = 'SSL Manager';

    protected static ?string $navigationIcon = null;

    protected static ?int $navigationSort = 2;

    public $poolingInstallLog = true;
    public $installLog = '';
    public $installLogFilePath =  '/var/www/acme-wildcard-install.log';
    public $installInstructions = [];

    public ?string $wildcardDomain = '';
    public ?string $masterEmail = '';

    protected static string $view = 'sslmanager::filament.pages.wildcard-issuer';


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

    public function requestCertificates() {

        $wildcardDomain = $this->wildcardDomain;
        $wildcardDomain = str_replace('*.', '', $wildcardDomain);

        if (file_exists($this->installLogFilePath)) {
            unlink($this->installLogFilePath);
        }

        $acmeCommand = "bash /usr/local/phyre/web/Modules/LetsEncrypt/shell/acme.sh --register-account -m $this->masterEmail ";
        $acmeCommand = shell_exec($acmeCommand);

        $acmeCommand = "bash /usr/local/phyre/web/Modules/LetsEncrypt/shell/acme.sh --force --issue -d '*.$wildcardDomain' --dns --yes-I-know-dns-manual-mode-enough-go-ahead-please";
    //    dump($acmeCommand);

        $acmeCommand = shell_exec($acmeCommand . " >> $this->installLogFilePath &");
        return [
            'success' => 'SSL certificate request sent.',
            'commandOutput' => $acmeCommand
        ];
    }

    public function installCertificates()
    {

        $wildcardDomain = $this->wildcardDomain;
        $wildcardDomain = str_replace('*.', '', $wildcardDomain);

        $acmeCommand = "bash /usr/local/phyre/web/Modules/LetsEncrypt/shell/acme.sh --renew -d '*.$wildcardDomain' --dns --yes-I-know-dns-manual-mode-enough-go-ahead-please";


//dd($acmeCommand);
        $acmeCommand = shell_exec($acmeCommand);

        $isOkStrings = [
           'And the full-chain cert is in',
           'Skipping. Next renewal time is',
           'Domains not changed',

        ];

        $isOk = false;
        foreach ($isOkStrings as $isOkString) {
            if (str_contains($acmeCommand, $isOkString)) {
                $isOk = true;
                break;
            }
        }

        if ($isOk) {

            $checkCertificateFilesExist  = $this->checkCertificateFilesExist($wildcardDomain);

            if (isset($checkCertificateFilesExist['sslFiles']['certificateContent'])) {

                $findWildcardSsl = DomainSslCertificate::where('domain', '*.'.$wildcardDomain)->first();
                if (!$findWildcardSsl) {
                    $findWildcardSsl = new DomainSslCertificate();
                    $findWildcardSsl->domain = '*.'.$wildcardDomain;
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

    public function form(Form $form) : Form
    {
        return $form
            ->schema($this->schema());
    }

    public function schema(): array
    {
        if (request()->get('step', null) === 'verification') {
            $this->getInstallLog();
        }

        return [
            Wizard::make([
                Wizard\Step::make('WildcardIssuer Install')
                    ->description('Issue new Wildcard SSL certificate for domain')
                    ->schema([

                        TextInput::make('wildcardDomain')
                            ->live()
                            ->helperText('Issue new Wildcard SSL certificate for domain. Example: *.mysite.com')
                            ->placeholder('*.mysite.com'),

                        TextInput::make('masterEmail')
                            ->live()
                            ->default(setting('general.master_email'))
                            ->helperText('Email address for notifications')
                            ->placeholder('master@example.com'),


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
                    ->schema([

                        TextInput::make('installInstructions')
                            ->view('sslmanager::filament.wildcard_install_instructions')
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
                            ->view('sslmanager::filament.wildcard_install_finish')
                            ->label('Installation Finished')
                    ]),
            ])
            //    ->persistStepInQueryString(),
        ];
    }
}
