<?php

namespace App\Filament\Enums;

use Filament\Support\Contracts\HasLabel;
use JaOcero\RadioDeck\Contracts\HasDescriptions;
use JaOcero\RadioDeck\Contracts\HasIcons;

enum ServerApplicationType: string implements HasLabel, HasDescriptions, HasIcons
{
    case APACHE_PHP = 'apache_php';
    //   case CADDY_APACHE_PHP = 'caddy_apache_php';
//    case APACHE_NODEJS = 'apache_nodejs';
//    case APACHE_PYTHON = 'apache_python';
//    case APACHE_RUBY = 'apache_ruby';
//    case APACHE_DOCKER = 'apache_docker';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::APACHE_PHP => 'Apache + PHP (CGI)',
//           self::CADDY_APACHE_PHP => 'Caddy + Apache + PHP (CGI)',
//            self::APACHE_NODEJS => 'Apache + Node.js (Passenger)',
//            self::APACHE_PYTHON => 'Apache + Python (Passenger)',
//            self::APACHE_RUBY => 'Apache + Ruby (Passenger)',
//            self::APACHE_DOCKER => 'Docker',
        };
    }

    public function getDescriptions(): ?string
    {
        return match ($this) {
            self::APACHE_PHP => 'Install applications like Microweber and more.',
//          self::CADDY_APACHE_PHP => 'Install applications like WordPress, Joomla, Drupal and more with Caddy as the web server.',
//            self::APACHE_NODEJS => 'Install applications like Ghost, KeystoneJS, and more.',
//            self::APACHE_PYTHON => 'Install applications like Django, Flask, and more.',
//            self::APACHE_RUBY => 'Install applications like Ruby on Rails, Sinatra, and more.',
//            self::APACHE_DOCKER => 'Run your own Docker containers.',
        };
    }

    public function getIcons(): ?string
    {
        return match ($this) {
            self::APACHE_PHP => 'phyre-php',
//            self::APACHE_NODEJS => 'phyre-nodejs',
//            self::APACHE_PYTHON => 'phyre-python',
//            self::APACHE_RUBY => 'phyre-ruby',
//            self::APACHE_DOCKER => 'docker-logo',
        };
    }
}
