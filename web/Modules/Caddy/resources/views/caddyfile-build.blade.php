{
    email {{ $caddyEmail }}
    admin off

    @if(isset($cloudflareApiToken) && $cloudflareApiToken)
    # Set the ACME DNS challenge provider to use Cloudflare for all sites
    acme_dns cloudflare {{ $cloudflareApiToken }}
    @endif

    @if(isset($zeroSSlApiToken) && $zeroSSlApiToken)
    cert_issuer zerossl {{ $zeroSSlApiToken }}
    @endif

    cert_issuer acme
    auto_https prefer_wildcard

    # Global options
    servers {
        protocols h1 h2 h3
    }
}

@foreach($caddyBlocks as $block)
@if(isset($block['is_wildcard_group']) && $block['is_wildcard_group'])
# Wildcard domain configuration for *.{{ $block['parent_domain'] }}
*.{{ $block['parent_domain'] }} {
    tls {
        dns cloudflare {{ $cloudflareApiToken }}
    }

@foreach($block['subdomains'] as $subdomain)
@php
    $hostname = $subdomain['domain'];
    $matcherName = str_replace('.', '_', $hostname);
@endphp
    @matchername_{!! $matcherName !!} host {{ $hostname }}
    handle @matchername_{!! $matcherName !!} {
@if(setting('caddy.enable_static_files', false) && !empty($staticPaths) && isset($subdomain['document_root']))
        root * {{ $subdomain['document_root'] }}


        @staticfiles_{!! $matcherName !!} {
@foreach(explode("\n", trim($staticPaths)) as $path)
            path {{ trim($path) }}
@endforeach
        }

        # Handle static files first
        handle @staticfiles_{!! $matcherName !!} {
            file_server
        }
        header @staticfiles_{!! $matcherName !!} {
            Cache-Control max-age=5184000
            ETag
        }
@endif

        # Proxy to Apache
        reverse_proxy {{ $subdomain['proxy_to'] }} {
            header_up Host {host}
            header_up X-Real-IP {remote_host}
            header_up X-Forwarded-Proto {scheme}
        }

        # Enable compression
        encode zstd gzip

        # Security headers
        header {
            -Server
            -X-Powered-By
        }


    }
@endforeach

    # Fallback for otherwise unhandled domains
    handle {
        respond "Subdomain not configured" 404
    }
}
@else
# {{ $block['domain'] }} configuration
{{ $block['domain'] }} {
@if(setting('caddy.enable_static_files', false) && !empty($staticPaths) && isset($block['document_root']))
    # Static file paths for domain
    root * {{ $block['document_root'] ?? '/var/www/html' }}

    @staticfiles {
@foreach(explode("\n", trim($staticPaths)) as $path)
        path {{ trim($path) }}
@endforeach
    }

    # Handle static files first
    handle @staticfiles {
        file_server
    }
    header @staticfiles {
        Cache-Control max-age=5184000
        ETag
    }
@endif

@if(isset($block['use_wildcard']) && $block['use_wildcard'] && isset($block['tls_cloudflare']) && $block['tls_cloudflare'])
    tls {
        dns cloudflare
    }
@endif

    # Proxy remaining requests to Apache
    handle {
        reverse_proxy {{ $block['proxy_to'] }} {
            header_up Host {host}
            header_up X-Real-IP {remote_host}
            header_up X-Forwarded-Proto {scheme}
        }
    }

    # Enable compression
    encode zstd gzip

    # Security headers
    header {
        -Server
        -X-Powered-By
    }

    # Handle OPTIONS requests for CORS
    @options method OPTIONS
    respond @options 204

    # Logging
    log {
        output file /var/log/caddy/{{ $block['domain'] }}.log {
            roll_size 10mb
            roll_keep 5
        }
    }
}

@if(isset($block['enable_www']) && $block['enable_www'])
# www.{{ $block['domain'] }} redirect
www.{{ $block['domain'] }} {
    redir https://{{ $block['domain'] }}{uri} permanent
}
@endif
@endif
@endforeach

# Catch-all for undefined domains
:80 {
    respond "Domain not configured" 404
}

:443 {
    respond "Domain not configured" 404
}
