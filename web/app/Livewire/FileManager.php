<?php

namespace App\Livewire;

use App\Models\FileItem;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Livewire\Attributes\Url;
use Livewire\Component;

class FileManager extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static string $view = 'filament.pages.file-manager';

    protected string $disk = 'local';

    #[Url(except: '')]
    public string $path = '';

    protected $listeners = ['updatePath' => '$refresh'];

    public function table(Table $table): Table
    {
        return $table
            ->heading($this->path ?: 'Root')
            ->query(
                FileItem::queryForDiskAndPath($this->disk, $this->path)
            )
            ->paginated(false)
            ->columns([
                TextColumn::make('name')
                    ->icon(fn ($record): string => match ($record->type) {
                        'Folder' => 'heroicon-o-folder',
                        default => 'heroicon-o-document'
                    })
                    ->iconColor(fn ($record): string => match ($record->type) {
                        'Folder' => 'warning',
                        default => 'gray',
                    })
                    ->action(function (FileItem $record) {
                        if ($record->isFolder()) {
                            $this->path = $record->path;

                            $this->dispatch('updatePath');
                        }
                    }),
                TextColumn::make('dateModified')
                    ->dateTime(),
                TextColumn::make('size')
                    ->formatStateUsing(fn ($state) => $state ? Number::fileSize($state) : ''),
                TextColumn::make('type'),
            ])
            ->actions([
                ViewAction::make('open')
                    ->label('Open')
                    ->hidden(fn (FileItem $record): bool => ! $record->canOpen())
                    ->url(fn (FileItem $record): string => Storage::disk($this->disk)->url($record->path))
                    ->openUrlInNewTab(),
                Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-document-arrow-down')
                    ->hidden(fn (FileItem $record): bool => $record->isFolder())
                    ->action(fn (FileItem $record) => Storage::disk($this->disk)->download($record->path)),
                DeleteAction::make('delete')
                    ->successNotificationTitle('File deleted')
                    ->hidden(fn (FileItem $record): bool => $record->isPreviousPath())
                    ->action(function (FileItem $record, Action $action) {
                        if ($record->delete()) {
                            $action->sendSuccessNotification();
                        }

                    }),
            ])
            ->bulkActions([
                BulkAction::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->successNotificationTitle('Files deleted')
                    ->deselectRecordsAfterCompletion()
                    ->action(function (Collection $records, BulkAction $action) {
                        $records->each(fn (FileItem $record) => $record->delete());
                        $action->sendSuccessNotification();
                    }),
            ])
            ->checkIfRecordIsSelectableUsing(fn (FileItem $record): bool => ! $record->isPreviousPath())
            ->headerActions([
                Action::make('create_folder')
                    ->label('Create Folder')
                    ->icon('heroicon-o-folder-plus')
                    ->form([
                        TextInput::make('name')
                            ->label('Folder name')
                            ->placeholder('Folder name')
                            ->required(),
                    ])
                    ->successNotificationTitle('Folder created')
                    ->action(function (array $data, Component $livewire, Action $action): void {
                        Storage::disk($livewire->disk)
                            ->makeDirectory($livewire->path.'/'.$data['name']);

                        $this->resetTable();
                        $action->sendSuccessNotification();
                    }),

                Action::make('upload_file')
                    ->label('Upload files')
                    ->icon('heroicon-o-document-arrow-up')
                    ->color('info')
                    ->form([
                        FileUpload::make('files')
                            ->required()
                            ->multiple()
                            ->previewable(false)
                            ->preserveFilenames()
                            ->disk($this->disk)
                            ->directory($this->path),
                    ]),
            ]);
    }
}
