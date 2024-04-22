<?php

namespace App\Filament\Resources\CustomerResource\Pages;

use App\Filament\Resources\CustomerResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditCustomer extends EditRecord
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function ($action) {
                    if (! $this->record->websites->isEmpty()) {
                        Notification::make()
                            ->danger()
                            ->title('Customer cannot be deleted')
                            ->body('This customer has websites associated with it. Please delete websites first.')
                            //  ->persistent()
                            ->send();
                        $action->cancel();
                    }
                }),
        ];
    }
}
