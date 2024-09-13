echo "Pull started at {{ date('Y-m-d H:i:s') }}"

su -m {{$systemUsername}} -c "export HOME=/home/{{$systemUsername}}"

@if($privateKeyFile)

ssh-keyscan {{$gitProvider}} >> /home/{{$systemUsername}}/.ssh/known_hosts
chmod 0600 /home/{{$systemUsername}}/.ssh/known_hosts
chown {{$systemUsername}}:{{$systemUsername}} /home/{{$systemUsername}}/.ssh/known_hosts

su -m {{$systemUsername}} -c 'cd {{$projectDir}} && git -c core.sshCommand="ssh -i {{$privateKeyFile}}" pull'

@else

su -m {{$systemUsername}} -c 'cd {{$projectDir}} && git pull'

@endif

phyre-php /usr/local/phyre/web/artisan git-repository:mark-as-pulled {{$gitRepositoryId}}


rm -rf /tmp/git-pull-{{$gitRepositoryId}}.sh
