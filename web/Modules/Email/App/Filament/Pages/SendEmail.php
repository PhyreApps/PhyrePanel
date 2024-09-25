<?php

namespace Modules\Email\App\Filament\Pages;

use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use PHPMailer\PHPMailer\PHPMailer;

class SendEmail extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';

    protected static string $view = 'email::filament.pages.send-email';

    protected static ?string $navigationGroup = 'Email';

    protected static ?string $navigationLabel = 'Send Email';

    protected static ?int $navigationSort = 2;

    public $from = '';
    public $to = '';
    public $body = 'Hi,
Welcome to your new account.
    ';
    public $subject = 'Welcome';

    public function getEmailDomain()
    {
        return setting('email.domain');
    }

    public function form(Form $form): Form
    {
        $this->from = 'admin@'.$this->getEmailDomain();

        return $form
            ->schema([
                TextInput::make('from')
                    ->disabled()
                    ->columnSpanFull(),
                TextInput::make('to')
                    ->label('To')
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('subject')
                    ->label('Subject')
                    ->required()
                    ->columnSpanFull(),
                Textarea::make('body')
                    ->rows(10)
                    ->label('Body')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public function send()
    {

        $emailDomain = $this->getEmailDomain();

        $phpMailer = new PHPMailer();
        $phpMailer->isMail();
        $phpMailer->setFrom('admin@' . $emailDomain, 'admin@'.$emailDomain);
        $phpMailer->addAddress($this->to);
        $phpMailer->Subject = $this->subject;
        $phpMailer->Body = $this->body;

        $sendMail = $phpMailer->send();

        if ($sendMail) {
            // Trigger a success notification
            Notification::make()
                ->title('Email Sent')
                ->body('The email has been sent successfully.')
                ->send();
            $this->to = '';
        } else {
            // Trigger an error notification
            Notification::make()
                ->title('Email Not Sent')
                ->body('The email could not be sent.')
                ->send();
        }
    }
}
