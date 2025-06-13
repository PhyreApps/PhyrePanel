<?php

namespace App\Filament\Pages;

use App\Jobs\ApacheBuild;
use App\Models\Domain;
use App\Models\HostingSubscription;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;

class CreateHostingSubscription extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.create-hosting-subscription';

    protected static ?string $navigationGroup = 'Hosting Services';

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $slug = 'hosting-subscriptions/create';

    protected static ?string $title = 'Create Hosting Account';

    public $state = [];

    public function verifyDomain($domain)
    {

        $verifyPHPContent = <<<EOT
<?php
if (isset(\$_GET['verified'])) {
    echo 'Domain verified';
}
?>
EOT;
;
        $file = fopen("/var/www/html/verify.php", "w") or die("Unable to open file!");
        fwrite($file, $verifyPHPContent);
        fclose($file);

        $errors = [];


        // Get Apache port settings
        $httpPort = setting('general.apache_http_port') ?? '80';
        $httpsPort = setting('general.apache_https_port') ?? '443';
        $sslDisabled = setting('general.apache_ssl_disabled') ?? false;

        // Check if domain is pointing to the server
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://$domain:$httpPort/verify.php?verified=1");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($curl);

        curl_close($curl);

        if ($result !== 'Domain verified') {
            $errors[] = 'Domain not pointing to the server';
        }

//        // Check if www. domain is pointing to the server
//        $curl = curl_init();
//        curl_setopt($curl, CURLOPT_URL, "http://www.$domain/verify.php?verified=1");
//        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 5);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
//        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
//        $result = curl_exec($curl);
//        curl_close($curl);
//
//        if ($result !== 'Domain verified') {
//            $errors[] = 'www. domain not pointing to the server';
//        }

        return [
            'errors' => $errors,
            'domain' => $domain
        ];

    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('state')
            ->schema([
            Wizard::make([
                Wizard\Step::make('Validation')
                    ->schema([
                        TextInput::make('domain')
                            ->required()
                            ->regex('/^([a-z0-9]+(-[a-z0-9]+)*\.)+[a-z]{2,}$/i')
                            ->disabled(function ($record) {
                                if (isset($record->exists)) {
                                    return $record->exists;
                                } else {
                                    return false;
                                }
                            })
                            ->suffixIcon('heroicon-m-globe-alt')
                            ->columnSpanFull(),
                    ])->afterValidation(function () {

                        $domain = $this->state['domain'];
                        // validate domain
                        if (!filter_var($domain, FILTER_VALIDATE_DOMAIN)) {
                            $this->addError('domain', 'Invalid domain name');
                            return;
                        }

                        // Verify domain if pointing to the server
                        $verify = $this->verifyDomain($domain);
                        if (isset($verify['errors']) && count($verify['errors']) > 0) {
                            $this->replaceMountedAction('firstVerifyDomain', ['domain' => $domain]);
                            throw new Halt();
                        }

                    }),
                Wizard\Step::make('Customer Information')
                    ->schema([
                        Select::make('customer_id')
                            ->label('Customer')
                            ->options(
                                \App\Models\Customer::all()->pluck('name', 'id')
                            )
                            ->required()->columnSpanFull(),
                    ]),
                Wizard\Step::make('Building Hosting Account')
                    ->schema([

                        Select::make('hosting_plan_id')
                            ->label('Hosting Plan')
                            ->options(
                                \App\Models\HostingPlan::all()->pluck('name', 'id')
                            )
                            ->required()->columnSpanFull(),

                        Checkbox::make('advanced')
                            ->live()
                            ->columnSpanFull(),

                        TextInput::make('system_username')
                            ->hidden(fn(Get $get): bool => !$get('advanced'))
                            ->disabled(function ($record) {
                                if (isset($record->exists)) {
                                    return $record->exists;
                                } else {
                                    return false;
                                }
                            })
                            ->suffixIcon('heroicon-m-user'),

                        TextInput::make('system_password')
                            ->hidden(fn(Get $get): bool => !$get('advanced'))
                            ->disabled(function ($record) {
                                if (isset($record->exists)) {
                                    return $record->exists;
                                } else {
                                    return false;
                                }
                            })
                            ->suffixIcon('heroicon-m-lock-closed'),
                    ]),
            ])
                ->submitAction(new HtmlString(Blade::render(<<<BLADE
                    <x-filament::button
                        wire:loading.attr="disabled"
                        wire:click="createHostingAccount"
                    >
                        Create Hosting Account
                    </x-filament::button>
                BLADE)))
                ->columnSpanFull(),

        ]);
    }

    public function createHostingAccount()
    {
        $domain = $this->state['domain'];
        $findDomain = Domain::where('domain', $domain)->first();
        if ($findDomain) {
            $this->replaceMountedAction('errorModal', ['message' => 'Domain already exists']);
           return;
        }

        if (!empty($this->state['system_username'])) {
            $findHostingSubscription = HostingSubscription::where('system_username', $this->state['system_username'])->first();
            if ($findHostingSubscription) {
                $this->replaceMountedAction('errorModal', ['message' => 'System username already exists']);
                return;
            }
        }

        $hostingSubscription = new HostingSubscription();
        $hostingSubscription->customer_id = $this->state['customer_id'];
        $hostingSubscription->hosting_plan_id = $this->state['hosting_plan_id'];
        $hostingSubscription->domain = $domain;

        if (isset($this->state['system_username'])) {
            $hostingSubscription->system_username = $this->state['system_username'];
        }

        if (isset($this->state['system_password'])) {
            $hostingSubscription->system_password = $this->state['system_password'];
        }

        $hostingSubscription->setup_date = Carbon::now();
        $hostingSubscription->save();

        ApacheBuild::dispatchSync();

        return redirect(route('filament.admin.resources.hosting-subscriptions.index'));

    }

    public function errorModalAction(): Action
    {

        return Action::make('error')
            ->modalContent(view('filament.pages.create-hosting-subscription.error-modal', [
                'domain' => $this->state['domain']
            ]))
            ->modalSubmitActionLabel('Ok');
    }

    public function firstVerifyDomainAction(): Action
    {

        // Get current server IP
        $serverIp = shell_exec("hostname -I | cut -d' ' -f1");
        $serverIp = trim($serverIp);


        return Action::make('Verifying Domain')
            ->modalContent(view('filament.pages.create-hosting-subscription.verify-domain-modal', [
                'domain' => $this->state['domain'],
                'serverIp' =>$serverIp
            ]))
            ->modalSubmitActionLabel('Continue');
    }

}
