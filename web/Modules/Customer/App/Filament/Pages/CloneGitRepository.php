<?php

namespace Modules\Customer\App\Filament\Pages;

use App\GitClient;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Wizard;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Pages\Page;

class CloneGitRepository extends Page
{

    protected static ?string $navigationLabel = 'Clone Git Repository';

    protected static string $view = 'customer::filament.pages.clone-git-repository';

    protected static ?string $slug = 'git-repositories/clone';

    protected static bool $shouldRegisterNavigation = false;

    public $state = [
        'url' => '',
        'name' => '',
        'git_ssh_key_id' => '',
    ];

    public $repositoryDetails = [];
    public function getTitle(): string
    {
        return 'Clone Git Repository';
    }

    public function form(Form $form): Form
    {
        return $form
            ->statePath('state')
            ->schema([

                Wizard::make([
                    Wizard\Step::make('Repository')
                        ->schema([

                            Select::make('git_ssh_key_id')
                                ->label('SSH Key')
                                ->options(\App\Models\GitSshKey::all()->pluck('name', 'id'))
                                ->columnSpanFull()
                                ->live()
                                ->required(),

                            TextInput::make('url')
                                ->label('URL')
                                ->required()
                                ->columnSpanFull()
                                ->live()
                                ->afterStateUpdated(function ($state, Set $set) {
                                    $repoDetails = GitClient::getRepoDetailsByUrl($state);
                                    if (isset($repoDetails['name'])) {
                                        $set('name', $repoDetails['owner'] .'/'. $repoDetails['name']);
                                        $this->repositoryDetails = $repoDetails;
                                    }
                                })
                                ->placeholder('Enter the URL of the repository'),


                        ]),
                    Wizard\Step::make('Details')
                        ->schema([

                            TextInput::make('name')
                                ->label('Name')
                                ->required()
                                ->columnSpanFull()
                                ->placeholder('Enter the name of the repository'),

                            Radio::make('clone_from')
                                ->label('Clone from')
                                ->options([
                                    'branch' => 'Branch',
                                    'tag' => 'Tag',
                                ])
                                ->live()
                                ->columnSpanFull(),

                            Select::make('branch')
                                ->label('Branch')
                                ->required()
                                ->hidden(function (Get $get) {
                                    return $get('clone_from') !== 'branch';
                                })
                                ->options(function (Get $get) {
                                    $url = $get('url');
                                    $repoDetails = GitClient::getRepoDetailsByUrl($url);
                                    if (isset($repoDetails['name'])) {
                                        return $repoDetails['branches'];
                                    }
                                })
                                ->live()
                                ->columnSpanFull()
                                ->placeholder('Enter the branch of the repository'),

                            Select::make('tag')
                                ->label('Tag')
                                ->live()
                                ->hidden(function (Get $get) {
                                    return $get('clone_from') !== 'tag';
                                })
                                ->options(function (Get $get) {
                                    $url = $get('url');
                                    $repoDetails = GitClient::getRepoDetailsByUrl($url);
                                    if (isset($repoDetails['name'])) {
                                        return $repoDetails['tags'];
                                    }
                                })
                                ->columnSpanFull()
                                ->placeholder('Enter the tag of the repository'),

                        ]),
                    Wizard\Step::make('Clone to')
                        ->schema([

                            TextInput::make('dir')
                                ->label('Directory')
                                ->columnSpanFull()
                                ->required()
                                ->placeholder('Enter the directory of the repository'),

                        ]),
                ])->columnSpanFull()

            ]);
    }
}
