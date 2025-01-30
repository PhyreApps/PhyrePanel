# System Commands

## Available Commands

### `phyre:health-check`
- Description: Checks system health including supervisor and Apache2 status
- Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:health-check
```

### `phyre:run-repair`
- Description: Performs system repairs including database users, phpMyAdmin, and Apache configurations  
- Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:run-repair
```

### `phyre:run-domain-repair`
- Description: Repairs domain configurations
- Usage: 
```
phyre-php /usr/local/phyre/web/artisan phyre:run-domain-repair
```
