<?php

return [
    'name' => 'Caddy',
    
    // Default settings
    'defaults' => [
        'enabled' => false,
        'email' => env('CADDY_EMAIL', 'admin@localhost'),
        'http_port' => env('CADDY_HTTP_PORT', 80),
        'https_port' => env('CADDY_HTTPS_PORT', 443),
        'apache_proxy_port' => env('CADDY_APACHE_PROXY_PORT', 8080),
        'auto_configure_apache' => true,
        'auto_rebuild_on_domain_changes' => true,
        'enable_hsts' => true,
        'enable_security_headers' => true,
        'enable_gzip' => true,
        'disable_apache_ssl' => true,
    ],
    
    // Paths
    'paths' => [
        'caddyfile' => env('CADDYFILE_PATH', '/etc/caddy/Caddyfile'),
        'log_dir' => env('CADDY_LOG_DIR', '/var/log/caddy'),
        'data_dir' => env('CADDY_DATA_DIR', '/var/lib/caddy'),
    ],
    
    // Security settings
    'security' => [
        'rate_limit' => [
            'enabled' => true,
            'requests_per_minute' => 100,
        ],
        'cors' => [
            'enabled' => true,
            'allow_origins' => ['*'],
            'allow_methods' => ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'],
            'allow_headers' => ['Content-Type', 'Authorization', 'X-Requested-With'],
        ],
    ],
    
    // Logging
    'logging' => [
        'enabled' => true,
        'format' => 'json',
        'level' => 'info',
        'max_size' => '10mb',
        'max_keep' => 5,
    ],
];
