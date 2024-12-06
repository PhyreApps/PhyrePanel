<?php

namespace Modules\Email\App\Filament\Pages;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\BadgeColumn;
use Modules\Email\App\Services\EmailService;
use Filament\Tables\Columns\TextColumn;
use Modules\Email\App\Enums\ServiceStatus;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Modules\Email\App\Models\EmailHealthStatus;

class EmailHealthStatusPage extends Page implements HasForms, HasTable
{

    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-heart';
    protected static string $view = 'email::filament.pages.email-health-status';
    protected static ?string $navigationGroup = 'Email';
    protected static ?string $navigationLabel = 'Email Health Status';

    protected static ?int $navigationSort = 5;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('service')
                    ->label('Service')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
            ])
            ->query(fn () => EmailHealthStatus::query())
            ->actions([
                Action::make('restart')
                    ->label('Restart')
                    ->action(function (EmailHealthStatus $record) {
                        $emailService = new EmailService();
                        $serviceName = strtolower($record->service);
                        $emailService->restartService($serviceName);
                    })
                    ->requiresConfirmation()
                    ->color('warning')
                    ->visible(fn (EmailHealthStatus $record) => $record->status === ServiceStatus::RUNNING),
                Action::make('start')
                    ->label('Start')
                    ->action(function (EmailHealthStatus $record) {
                        $emailService = new EmailService();
                        $serviceName = strtolower($record->service);
                        $emailService->startService($serviceName);
                    })
                    ->requiresConfirmation()
                    ->color('success')
                    ->visible(fn (EmailHealthStatus $record) => $record->status === ServiceStatus::NOT_RUNNING),
                Action::make('stop')
                    ->label('Stop')
                    ->action(function (EmailHealthStatus $record) {
                        $emailService = new EmailService();
                        $serviceName = strtolower($record->service);
                        $emailService->stopService($serviceName);
                    })
                    ->requiresConfirmation()
                    ->color('danger')
                    ->visible(fn (EmailHealthStatus $record) => $record->status === ServiceStatus::RUNNING),

                    ActionGroup::make([
                        Action::make('viewLogs')
                        ->label('View Logs')
                        ->modalContent(function (EmailHealthStatus $record){
                            $serviceName = strtolower($record->service);
                            $emailService = new EmailService();
                            $logContents = $emailService->getLog($serviceName);
                            return view('email::filament.pages.view-log-modal',[
                                'logContents' => $logContents
                            ]);
                        })
                        ->color('info'),
                    Action::make('truncateLogs')
                        ->label('Truncate Logs')
                        ->action(function (EmailHealthStatus $record) {
                            $serviceName = strtolower($record->service);
                            $emailService = new EmailService();
                            $emailService->truncateLog($serviceName);
                        })
                        ->requiresConfirmation()
                        ->color('danger')
                    ])->label('Logs')

            ]);
    }
}
