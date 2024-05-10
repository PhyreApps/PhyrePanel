<?php
use App\PhyreConfig;

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => PhyreConfig::get('MAILGUN_DOMAIN'),
        'secret' => PhyreConfig::get('MAILGUN_SECRET'),
        'endpoint' => PhyreConfig::get('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => PhyreConfig::get('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => PhyreConfig::get('AWS_ACCESS_KEY_ID'),
        'secret' => PhyreConfig::get('AWS_SECRET_ACCESS_KEY'),
        'region' => PhyreConfig::get('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

];
