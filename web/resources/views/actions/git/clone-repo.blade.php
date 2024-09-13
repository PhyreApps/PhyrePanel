echo "Cloning started at {{ date('Y-m-d H:i:s') }}"

su -m {{$systemUsername}} -c "export HOME=/home/{{$systemUsername}}"

@if($privateKeyFile)

ssh-keyscan {{$gitProvider}} >> /home/{{$systemUsername}}/.ssh/known_hosts
chmod 0600 /home/{{$systemUsername}}/.ssh/known_hosts
chown {{$systemUsername}}:{{$systemUsername}} /home/{{$systemUsername}}/.ssh/known_hosts

su -m {{$systemUsername}} -c 'git -c core.sshCommand="ssh -i {{$privateKeyFile}}" clone {{$cloneUrl}} {{$projectDir}}'

@else

su -m {{$systemUsername}} -c 'git clone {{$cloneUrl}} {{$projectDir}}'

@endif

phyre-php /usr/local/phyre/web/artisan git-repository:mark-as-cloned {{$gitRepositoryId}}

rm -rf /tmp/git-clone-{{$gitRepositoryId}}.sh
