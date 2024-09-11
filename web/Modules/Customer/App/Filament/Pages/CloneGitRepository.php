<?php

namespace Modules\Customer\App\Filament\Pages;

use App\GitClient;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Set;
use Filament\Pages\Page;

class CloneGitRepository extends Page
{

    protected static ?string $navigationLabel = 'Clone Git Repository';

    protected static string $view = 'customer::filament.pages.clone-git-repository';

    protected static ?string $slug = 'git-repositories/clone';

    protected static bool $shouldRegisterNavigation = false;

    public function getTitle(): string
    {
        return 'Clone Git Repository';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([

                Wizard::make([
                    Wizard\Step::make('Repository')
                        ->schema([
                            TextInput::make('url')
                                ->label('URL')
                                ->required()
                                ->columnSpanFull()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $repoDetails = GitClient::getRepoDetailsByUrl($state);
                                    if (isset($repoDetails['name'])) {
                                        $set('name', $repoDetails['owner'] .'/'. $repoDetails['name']);
                                    }
                                })
                                ->placeholder('Enter the URL of the repository'),

                              Select::make('git_ssh_key_id')
                                  ->label('SSH Key')
                                  ->options(\App\Models\GitSshKey::all()->pluck('name', 'id'))
                                  ->columnSpanFull()
                                  ->live()
                                  ->required(),


                        ]),
                    Wizard\Step::make('Delivery')
                        ->schema([
                            // ...
                        ]),
                    Wizard\Step::make('Billing')
                        ->schema([
                            // ...
                        ]),
                ])->columnSpanFull()

            ]);
    }
}
