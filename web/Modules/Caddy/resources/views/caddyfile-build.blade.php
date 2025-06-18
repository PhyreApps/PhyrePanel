{
    email {{ $caddyEmail }}
    admin off

    # Global options
    servers {
        protocols h1 h2 h3
    }
}

@foreach($caddyBlocks as $block)
# {{ $block['domain'] }} configuration
{{ $block['domain'] }} {

    @if(setting('caddy.enable_static_files', false) && !empty($staticPaths) and isset($block['document_root']))
    # Static file paths for domain
    root * {{ $block['document_root'] ?? '/var/www/html' }}

    @static_files {
        @foreach(explode("\n", trim($staticPaths)) as $path)
        path {{ trim($path) }}
        @endforeach
    }

    # Handle static files first
    handle @static_files {
        file_server
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
@endforeach

# Catch-all for undefined domains
:80 {
    respond "Domain not configured" 404
}

:443 {
    respond "Domain not configured" 404
}
