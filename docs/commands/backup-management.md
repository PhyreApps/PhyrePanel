# Backup Management

## Available Commands

### `phyre:create-daily-full-backup`
- Description: Creates a full system backup
- Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:create-daily-full-backup
```

### `phyre:create-daily-full-hosting-subscriptions-backup`
- Description: Creates backups for all hosting subscriptions
- Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:create-daily-full-hosting-subscriptions-backup
```

### `phyre:run-backup-checks`
- Description: Checks and manages backup status
- Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:run-backup-checks
```

### `phyre:run-upload-backups-to-remote-servers`
- Description: Uploads backups to configured remote servers
- Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:run-upload-backups-to-remote-servers
```
