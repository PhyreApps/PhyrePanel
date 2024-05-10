[program:phyre-worker]
process_name=%(program_name)s_%(process_num)02d
command=phyre-php /usr/local/phyre/web/artisan queue:work --sleep=3 --tries=3 --timeout=0
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=root
numprocs={{ $workersCount }}
redirect_stderr=true
stdout_logfile=/usr/local/phyre/web/storage/logs/worker.log
stopwaitsecs=3600
