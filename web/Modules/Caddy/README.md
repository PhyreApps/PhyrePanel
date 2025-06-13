# Caddy Module for Phyre Panel

## Overview

The Caddy module provides comprehensive reverse proxy functionality for Phyre Panel, enabling SSL termination and automatic HTTPS for all managed domains. Caddy handles SSL certificates automatically via Let's Encrypt and proxies HTTPS requests to Apache running on non-standard ports.

## Features

- **Automatic SSL Termination**: Caddy automatically obtains and renews SSL certificates via Let's Encrypt
- **Reverse Proxy to Apache**: Routes HTTPS traffic to Apache running on non-SSL ports (default: 8080)
- **Apache Integration**: Automatically configures Apache to work with Caddy
- **Management Interface**: Comprehensive Filament pages for configuration and monitoring
- **CLI Commands**: Console commands for rebuilding and checking status
- **Event-Driven Updates**: Automatic configuration rebuilds when domains change
- **Health Monitoring**: Built-in health checks and status monitoring
- **Security Headers**: Automatic security headers and HSTS enforcement
- **Logging**: Comprehensive logging with automatic log rotation

## Installation

### 1. Install Caddy

Run the installation script:
```bash
sudo bash /usr/local/phyre/web/Modules/Caddy/shell-scripts/install-caddy.sh
```

This script will:
- Add the official Caddy repository
- Install Caddy web server
- Create necessary directories
- Set up systemd service
- Configure basic permissions

### 2. Enable the Module

1. Navigate to **Settings > General** in Phyre Panel
2. Go to the **Caddy** tab
3. Enable Caddy integration
4. Configure your email for Let's Encrypt
5. Set Apache proxy port (default: 8080)
6. Save settings

### 3. Configure Apache Integration

The module can automatically configure Apache:
- Changes Apache HTTP port to 8080 (or custom port)
- Disables Apache SSL (Caddy handles SSL)
- Rebuilds Apache configuration

## Configuration

### General Settings

Access via **Caddy > Settings** in the admin panel:

#### General Tab
- **Enable Caddy**: Toggle Caddy functionality
- **Email**: Email for Let's Encrypt certificate registration
- **HTTP/HTTPS Ports**: Caddy listening ports (default: 80/443)

#### Apache Integration Tab
- **Auto-configure Apache**: Automatically adjust Apache settings
- **Apache Proxy Port**: Port where Apache serves HTTP (default: 8080)
- **Disable Apache SSL**: Let Caddy handle all SSL (recommended)

#### Security Tab
- **Enable HSTS**: HTTP Strict Transport Security
- **Security Headers**: X-Frame-Options, X-Content-Type-Options, etc.
- **Enable Compression**: GZIP compression for responses

#### Management Tab
- **Auto-rebuild**: Automatically rebuild configuration on domain changes
- **Backup Settings**: Configuration backup options

### Advanced Configuration

Configuration file: `/usr/local/phyre/web/Modules/Caddy/config/config.php`

```php
return [
    'enabled' => true,
    'email' => 'admin@yourdomain.com',
    'config_path' => '/etc/caddy/Caddyfile',
    'log_path' => '/var/log/caddy',
    'binary_path' => '/usr/bin/caddy',
    'max_backups' => 10,
    // ... more options
];
```

## Management

### Dashboard

Access via **Caddy > Dashboard**:
- Service status monitoring
- Version information
- Configuration validation
- Quick actions (start/stop/restart/reload)
- Health checks
- Recent activity

### Logs

Access via **Caddy > Logs**:
- System logs (systemd journal)
- Access logs (per domain)
- Error logs
- Real-time log viewing

### Management Interface

Access via **Caddy > Management**:
- Service control buttons
- Configuration rebuild
- Manual domain management
- Backup/restore operations

## CLI Commands

### Rebuild Configuration
```bash
php artisan caddy:rebuild
```

### Check Status
```bash
php artisan caddy:status
```

### Force Rebuild with Permissions Fix
```bash
php artisan caddy:rebuild --fix-permissions
```

## Domain Management

### Automatic Configuration

When you create a domain in Phyre Panel:
1. The domain is automatically added to Caddyfile
2. Caddy obtains SSL certificate via Let's Encrypt
3. HTTP traffic is redirected to HTTPS
4. HTTPS traffic is proxied to Apache on port 8080

### Manual Configuration

You can manually trigger rebuilds:
- Through the management interface
- Via CLI commands
- By saving settings in the admin panel

## Generated Caddyfile Structure

```caddy
{
    email admin@yourdomain.com
    admin off
}

# example.com configuration
example.com {
    reverse_proxy localhost:8080 {
        header_up Host {host}
        header_up X-Real-IP {remote_host}
        header_up X-Forwarded-Port {server_port}
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
        output file /var/log/caddy/example.com.log {
            roll_size 10mb
            roll_keep 5
        }
    }
}

# www.example.com redirect
www.example.com {
    redir https://example.com{uri} permanent
}
```

## Troubleshooting

### Common Issues

#### 1. Caddy Service Not Starting
```bash
# Check status
systemctl status caddy

# Check configuration
caddy validate --config /etc/caddy/Caddyfile

# Check logs
journalctl -u caddy -f
```

#### 2. SSL Certificate Issues
```bash
# Check certificate storage
ls -la /var/lib/caddy/.local/share/caddy/certificates/

# Force certificate renewal
systemctl restart caddy
```

#### 3. Port Conflicts
- Ensure Apache is not listening on ports 80/443
- Check that Apache is configured for port 8080
- Verify no other services are using ports 80/443

#### 4. Permission Issues
```bash
# Fix Caddy directories
sudo chown -R caddy:caddy /etc/caddy
sudo chown -R caddy:caddy /var/log/caddy
sudo chown -R caddy:caddy /var/lib/caddy

# Fix permissions (777 for log directory to allow multi-user write access)
sudo chmod 755 /etc/caddy
sudo chmod 644 /etc/caddy/Caddyfile
sudo chmod 777 /var/log/caddy
```

### Health Checks

The module includes built-in health checks:
- Service status
- Configuration validity
- Log directory permissions
- Binary availability

Access health checks via the dashboard or CLI.

### Backup and Recovery

Configuration backups are automatically created:
- Before each rebuild
- Stored with timestamps
- Automatic cleanup of old backups
- Restore functionality in case of errors

## Security Considerations

### SSL/TLS Security
- Automatic HTTPS redirects
- HSTS headers enforced
- Modern TLS cipher suites
- Automatic certificate renewal

### Headers
- X-Frame-Options: SAMEORIGIN
- X-Content-Type-Options: nosniff
- X-XSS-Protection: 1; mode=block
- Referrer-Policy: strict-origin-when-cross-origin
- Strict-Transport-Security with preload

### Access Control
- Rate limiting capabilities
- CORS configuration
- Request filtering

## Performance

### Optimizations
- GZIP compression enabled
- HTTP/2 support
- Efficient reverse proxy
- Connection pooling

### Monitoring
- Response time monitoring
- Error rate tracking
- Certificate expiration alerts
- Health check status

## Support

### Logs Location
- System logs: `/var/log/caddy/`
- Per-domain logs: `/var/log/caddy/{domain}.log`
- Systemd journal: `journalctl -u caddy`

### Configuration Files
- Main config: `/etc/caddy/Caddyfile`
- Module config: `/usr/local/phyre/web/Modules/Caddy/config/config.php`
- Backups: `/etc/caddy/Caddyfile.backup.*`

### Getting Help
1. Check the dashboard for health status
2. Review logs for specific errors
3. Validate configuration syntax
4. Check systemd service status
5. Verify Apache integration settings

## Version Compatibility

- **Caddy**: 2.6.0 or later
- **Apache**: 2.4.x
- **PHP**: 8.1 or later
- **Laravel**: 10.x or later
- **Phyre Panel**: Latest version

## Changelog

### Version 1.0.0
- Initial release
- Basic reverse proxy functionality
- SSL termination
- Apache integration
- Management interface
- Health monitoring
- Automatic configuration builds
