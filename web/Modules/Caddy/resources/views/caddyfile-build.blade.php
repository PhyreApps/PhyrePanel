{
    email {{ $caddyEmail }}
    admin off

    @if(isset($zeroSSlApiToken) && $zeroSSlApiToken)
    cert_issuer zerossl {{ $zeroSSlApiToken }}
    @endif

    cert_issuer acme
    auto_https disable_redirects

    # Global options
    servers {
        protocols h1 h2 h3
    }

    servers :443 {
        protocols h1
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
@if(setting('caddy.enable_static_files', false) && isset($subdomain['document_root']))
        root * {{ $subdomain['document_root'] }}

@php
    $trimmedStaticPaths = isset($staticPaths) ? trim($staticPaths) : '';
    $staticPathsArray = !empty($trimmedStaticPaths) ? explode("\n", $trimmedStaticPaths) : [];
@endphp
@if(count($staticPathsArray) > 0)
        @staticfiles_{!! $matcherName !!} {
@foreach($staticPathsArray as $path)
@php $trimmedPath = trim($path); @endphp
@if(!empty($trimmedPath))
            path {{ $trimmedPath }}
@endif
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
@endif

        # Proxy to Apache
        reverse_proxy {{ $subdomain['proxy_to'] }} {
            header_up Host {host}
            header_up X-Real-IP {remote_host}
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
@if(setting('caddy.enable_static_files', false) && isset($block['document_root']))
    # Static file paths for domain
    root * {{ $block['document_root'] ?? '/var/www/html' }}

@php
    $trimmedStaticPaths = isset($staticPaths) ? trim($staticPaths) : '';
    $staticPathsArray = !empty($trimmedStaticPaths) ? explode("\n", $trimmedStaticPaths) : [];
@endphp
@if(count($staticPathsArray) > 0)
    @staticfiles {
@foreach($staticPathsArray as $path)
@php $trimmedPath = trim($path); @endphp
@if(!empty($trimmedPath))
        path {{ $trimmedPath }}
@endif
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
@endif

@if(isset($block['use_wildcard']) && $block['use_wildcard'] && isset($block['tls_cloudflare']) && $block['tls_cloudflare'])
    tls {
        dns cloudflare {{ $cloudflareApiToken }}
    }
@else
    # Force ACME issuer for non-wildcard domains (uses HTTP challenge by default)
    tls {
        issuer acme
    }
@endif

    # Proxy remaining requests to Apache
    handle {
        reverse_proxy {{ $block['proxy_to'] }} {
            header_up Host {host}
            header_up X-Real-IP {remote_host}
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
}

@if(isset($block['enable_www']) && $block['enable_www'])
# www.{{ $block['domain'] }} - serve same content as main domain
www.{{ $block['domain'] }} {
    tls {
        issuer acme
    }
    
    # Proxy to Apache (same as main domain)
    handle {
        reverse_proxy {{ $block['proxy_to'] }} {
            header_up Host {host}
            header_up X-Real-IP {remote_host}
        }
    }

    # Enable compression
    encode zstd gzip

    # Security headers
    header {
        -Server
        -X-Powered-By
    }
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
