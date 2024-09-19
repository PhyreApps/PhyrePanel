echo "Pull started at {{ date('Y-m-d H:i:s') }}"

cd {{$projectDir}}

@if($privateKeyFile)

ssh-keyscan {{$gitProvider}} >> /home/{{$systemUsername}}/.ssh/known_hosts
chmod 0600 /home/{{$systemUsername}}/.ssh/known_hosts
chown {{$systemUsername}}:{{$systemUsername}} /home/{{$systemUsername}}/.ssh/known_hosts

git -c core.sshCommand="ssh -i {{$privateKeyFile}}" pull

@else

git pull

@endif

rm -rf {{$selfFile}}
