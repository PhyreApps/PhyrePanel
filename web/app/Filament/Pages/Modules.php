<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Modules extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static string $view = 'filament.pages.modules';

    protected static ?string $navigationGroup = 'Server Management';

    protected static ?string $navigationLabel = 'Extensions';

    protected static ?int $navigationSort = 1;

    protected function getViewData(): array
    {

        $scanModules = scandir(base_path('Modules'));
        $scanModules = array_diff($scanModules, ['.', '..']);

        $modules = [];
        foreach ($scanModules as $key => $module) {
            if (!is_dir(base_path('Modules/' . $module))) {
                unset($modules[$key]);
            }
            $moduleJson = file_get_contents(base_path('Modules/' . $module . '/module.json'));
            $moduleJson = json_decode($moduleJson, true);
            if (isset($moduleJson['hidden']) && $moduleJson['hidden'] == true) {
                continue;
            }
            $category = 'All';
            $logoIcon = 'heroicon-o-puzzle-piece';
            if (isset($moduleJson['logoIcon'])) {
                $logoIcon = $moduleJson['logoIcon'];
            }
            if (isset($moduleJson['category'])) {
                $category = $moduleJson['category'];
            }
            $modules[$category][] = [
                'name' => $module,
                'description' => 'A drag and drop website builder and a powerful next-generation CMS.',
                'url' => url('admin/' . $module),
                'iconUrl' => url('images/modules/' . $module . '.png'),
                'logoIcon' => $logoIcon,
                'category' => 'Content Management',
            ];
        }

        return [
            'categories' => $modules,
        ];


        return [
            'categories' => [
                'Security' => [
                    [
                        'name' => 'Lets Encrypt',
                        'description' => 'Automatically secure your website with a free SSL certificate from Lets Encrypt.',
                        'url' => url('admin/letsencrypt'),
                        'iconUrl' => url('images/modules/letsencrypt.png'),
                        'category' => 'Security',
                    ],
                ],
                'Content Management' => [
                    [
                        'name' => 'Microweber',
                        'description' => 'A drag and drop website builder and a powerful next-generation CMS.',
                        'url' => url('admin/microweber'),
                        'iconUrl' => url('images/modules/microweber.png'),
                        'category' => 'Content Management',
                    ],
                    [
                        'name' => 'WordPress',
                        'description' => 'WordPress is a free and open-source content management system written in PHP and paired with a MySQL or MariaDB database.',
                        'url' => url('admin/wordpress'),
                        'iconUrl' => url('images/modules/wordpress.svg'),
                        'category' => 'Content Management',
                    ],
                ],
                'E-Commerce' => [
                    [
                        'name' => 'OpenCart',
                        'description' => 'A free shopping cart system. OpenCart is an open source PHP-based online e-commerce solution.',
                        'url' => url('admin/opencart'),
                        'iconUrl' => url('images/modules/opencart.png'),
                        'category' => 'E-Commerce',
                    ],
                ],
            ],
        ];
    }
}
