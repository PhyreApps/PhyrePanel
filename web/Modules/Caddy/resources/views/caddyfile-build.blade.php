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
	}

	# Enable compression
	encode gzip

	# Security headers
	header {
		Strict-Transport-Security "max-age=31536000; includeSubDomains; preload"
		X-Content-Type-Options "nosniff"
		X-Frame-Options "SAMEORIGIN"
		X-XSS-Protection "1; mode=block"
		Referrer-Policy "strict-origin-when-cross-origin"
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
