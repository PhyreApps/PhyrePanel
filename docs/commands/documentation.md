# Phyre Panel Terminal Commands

## Available Commands

### System Commands

`phyre:health-check`
  - Description: Checks system health including supervisor and Apache2 status
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:health-check
```

`phyre:run-repair`
  - Description: Performs system repairs including database users, phpMyAdmin, and Apache configurations
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:run-repair
```

`phyre:run-domain-repair`
  - Description: Repairs domain configurations
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:run-domain-repair
```

### Installation Commands

`phyre:install-apache`
  - Description: Installs Apache web server with latest PHP version
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:install-apache
```

`phyre:install-module {module}`
  - Description: Installs a specific Phyre module
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:install-module module_name
```

### SSL & Domain Management

`phyre:setup-master-domain-ssl`
  - Description: Sets up SSL certificate for the master domain
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:setup-master-domain-ssl
```

`phyre:apache-ping-websites-with-curl`
  - Description: Tests HTTP response for all websites
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:apache-ping-websites-with-curl
```

### Backup Management

`phyre:create-daily-full-backup`
  - Description: Creates a full system backup
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:create-daily-full-backup
```

`phyre:create-daily-full-hosting-subscriptions-backup`
  - Description: Creates backups for all hosting subscriptions
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:create-daily-full-hosting-subscriptions-backup
```

`phyre:run-backup-checks`
  - Description: Checks and manages backup status
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:run-backup-checks
```

`phyre:run-upload-backups-to-remote-servers`
  - Description: Uploads backups to configured remote servers
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:run-upload-backups-to-remote-servers
```

### User Management

`phyre:create-admin-account`
  - Description: Creates a new admin account
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:create-admin-account
```

`phyre:reset-admin-account-password`
  - Description: Resets password for an existing admin account
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:reset-admin-account-password
```

### System Configuration

`phyre:key-generate`
  - Description: Generates application key in phyre-config.ini
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:key-generate
```

`phyre:set-ini-settings {key} {value}`
  - Description: Sets configuration values in phyre-config.ini
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:set-ini-settings key value
```

### System Update

`phyre:update`
  - Description: Updates Phyre to the latest version
  - Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:update
```

## Usage Examples

```bash
# Create a new admin account
phyre-php /usr/local/phyre/web/artisan phyre:create-admin-account

# Check system health
phyre-php /usr/local/phyre/web/artisan phyre:health-check

# Create daily backup
phyre-php /usr/local/phyre/web/artisan phyre:create-daily-full-backup

# Install a module
phyre-php /usr/local/phyre/web/artisan phyre:install-module blog
```

## Notes

- All commands should be run from the project root directory
- Some commands may require sudo/root privileges
- Backup commands run automatically via cron but can be executed manually
- Always ensure proper permissions before running system-level commands

