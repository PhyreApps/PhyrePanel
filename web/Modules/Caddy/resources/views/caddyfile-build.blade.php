{
    email {{ $caddyEmail }}
    admin off
    auto_https on
    
    # Global options
    servers {
        protocol {
            experimental_http3
        }
    }
}

@foreach($caddyBlocks as $block)
# {{ $block['domain'] }} configuration
{{ $block['domain'] }} {
    reverse_proxy {{ $block['proxy_to'] }} {
        header_up Host {host}
        header_up X-Real-IP {remote_host}
        header_up X-Forwarded-For {remote_host}
        header_up X-Forwarded-Proto {scheme}
        header_up X-Forwarded-Port {server_port}
        
        # Health check
        health_uri /
        health_interval 30s
        health_timeout 5s
        
        # Load balancing (if multiple backends in future)
        lb_policy round_robin
    }
    
    # Enable gzip compression
    encode gzip zstd
    
    # Security headers
    header {
        # Security
        Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
        X-Content-Type-Options "nosniff"
        X-Frame-Options "SAMEORIGIN"
        X-XSS-Protection "1; mode=block"
        Referrer-Policy "strict-origin-when-cross-origin"
        
        # Remove server info
        -Server
        -X-Powered-By
        
        # CORS (basic)
        Access-Control-Allow-Origin "*"
        Access-Control-Allow-Methods "GET, POST, PUT, DELETE, OPTIONS"
        Access-Control-Allow-Headers "Content-Type, Authorization, X-Requested-With"
    }
    
    # Handle OPTIONS requests for CORS
    @options method OPTIONS
    respond @options 204
    
    # Rate limiting
    rate_limit {
        zone dynamic {
            key {remote_host}
            events 100
            window 1m
        }
    }
    
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

# Catch-all for undefined domains on port 80
:80 {
    respond "Domain not configured" 404
}

# Catch-all for undefined domains on port 443
:443 {
    respond "Domain not configured" 404
    
    # TLS configuration for catch-all
    tls {
        on_demand
    }
}
