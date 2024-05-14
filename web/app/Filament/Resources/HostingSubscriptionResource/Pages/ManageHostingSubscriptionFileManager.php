<?php

namespace App\Filament\Resources\HostingSubscriptionResource\Pages;

use App\Filament\Resources\HostingSubscriptionResource;
use App\Models\Domain;
use App\Models\FileItem;
use App\Models\HostingSubscription;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Resources\Pages\ViewRecord;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\HeaderActionsPosition;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Number;
use Livewire\Attributes\Url;
use Livewire\Component;

class ManageHostingSubscriptionFileManager extends ViewRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = HostingSubscriptionResource::class;

    protected static string $relationship = 'fileManager';

    protected static ?string $navigationIcon = 'heroicon-o-folder-open';

    protected static string $view = 'filament.pages.file-manager';

    public string $disk = 'local';

    #[Url(except: '')]
    public string $path = '';

    protected $listeners = ['updatePath' => '$refresh'];

    public function getTitle(): string|Htmlable
    {
        $recordTitle = $this->getRecordTitle();

        $recordTitle = $recordTitle instanceof Htmlable ? $recordTitle->toHtml() : $recordTitle;

        return "File Manager {$recordTitle} ";
    }

    public function getBreadcrumb(): string
    {
        return 'File Manager';
    }

    public static function getNavigationLabel(): string
    {
        return 'File Manager';
    }

    public function table(Table $table): Table
    {
        $findDomain = Domain::select(['home_root', 'hosting_subscription_id', 'is_main'])
            ->where('hosting_subscription_id', $this->record->id)
            ->where('is_main',1)->first();

        $this->disk = $findDomain->home_root;

        $storage = Storage::build([
            'driver' => 'local',
            'throw' => false,
            'root' => $this->disk,
        ]);

        return $table
         //   ->deferLoading()
            ->heading($this->disk .'/'. $this->path ?: 'Root')
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
                    ->formatStateUsing(fn ($state) => $state ? Number::fileSize($state) : '0.0KB'),
                TextColumn::make('type'),
            ])
            ->actions([

                ActionGroup::make([
//                    ViewAction::make('open')
//                        ->label('Open')
//                        ->hidden(fn (FileItem $record): bool => ! $record->canOpen())
//                        ->url(fn (FileItem $record): string => $storage->url($record->path))
//                        ->openUrlInNewTab(),
                    Action::make('download')
                        ->label('Download')
                        ->icon('heroicon-o-document-arrow-down')
                        ->hidden(fn (FileItem $record): bool => $record->isFolder())
                        ->action(fn (FileItem $record) => $storage->download($record->path)),
                    DeleteAction::make('delete')
                        ->successNotificationTitle('File deleted')
                        ->hidden(fn (FileItem $record): bool => $record->isPreviousPath())
                        ->action(function (FileItem $record, Action $action) {
                            if ($record->delete()) {
                                $action->sendSuccessNotification();
                            }

                        }),
                ]),
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
            ->headerActionsPosition(HeaderActionsPosition::Bottom)
            ->headerActions([

//                Action::make('home')
//                    ->label('Home')
//                    ->action(fn () => $this->path = '')
//                    ->icon('heroicon-o-home'),
//
//                Action::make('back')
//                    ->label('Back')
//                    ->action(fn () => $this->path = dirname($this->path))
//                    ->icon('heroicon-o-arrow-left'),

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
                    ->action(function (array $data, Component $livewire, Action $action) use($storage) : void {
                        $storage->makeDirectory($livewire->path.'/'.$data['name']);

                        $this->resetTable();
                        $action->sendSuccessNotification();
                    }),

                Action::make('create_file')
                    ->label('Create File')
                    ->icon('heroicon-o-document-plus')
                    ->form([
                        TextInput::make('file_name')
                            ->label('File name')
                            ->placeholder('File name')
                            ->required(),
                        Textarea::make('file_content')
                            ->label('Content')
                            ->required(),
                    ])
                    ->successNotificationTitle('File created')
                    ->action(function (array $data, Component $livewire, Action $action) use($storage) : void {

                        $storage->put($livewire->path.'/'.$data['file_name'], $data['file_content']);

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
