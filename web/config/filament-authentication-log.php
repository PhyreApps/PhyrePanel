<?php
use App\PhyreConfig;

return [

    'notifications' => [

        //        'new-device' => [
        //
        //            // Send the NewDevice notification
        //
        //            'enabled' => PhyreConfig::get('NEW_DEVICE_NOTIFICATION', true),
        //
        //
        //
        //            // Use torann/geoip to attempt to get a location
        //
        //            'location' => true,
        //
        //
        //
        //            // The Notification class to send
        //
        //            'template' => \Rappasoft\LaravelAuthenticationLog\Notifications\NewDevice::class,
        //
        //        ],
        //
        //        'failed-login' => [
        //
        //            // Send the FailedLogin notification
        //
        //            'enabled' => env('FAILED_LOGIN_NOTIFICATION', false),
        //
        //
        //
        //            // Use torann/geoip to attempt to get a location
        //
        //            'location' => true,
        //
        //
        //
        //            // The Notification class to send
        //
        //            'template' => \Rappasoft\LaravelAuthenticationLog\Notifications\FailedLogin::class,
        //
        //        ],

    ],

    'resources' => [
        'AutenticationLogResource' => \Tapp\FilamentAuthenticationLog\Resources\AuthenticationLogResource::class,
    ],

    'authenticable-resources' => [
        \App\Models\User::class,
    ],

    'navigation' => [
        'group' => 'System',
        'authentication-log' => [
            'register' => true,
            'sort' => 5,
            'icon' => 'heroicon-o-shield-check',
        ],
    ],

    'sort' => [
        'column' => 'login_at',
        'direction' => 'desc',
    ],
];
