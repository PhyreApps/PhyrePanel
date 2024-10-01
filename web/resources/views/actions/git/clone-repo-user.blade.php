echo "Cloning started at {{ date('Y-m-d H:i:s') }}"

@if($privateKeyFile)

ssh-keyscan {{$gitProvider}} >> /home/{{$systemUsername}}/.ssh/known_hosts
chmod 0600 /home/{{$systemUsername}}/.ssh/known_hosts
chown {{$systemUsername}}:{{$systemUsername}} /home/{{$systemUsername}}/.ssh/known_hosts

git -c core.sshCommand="ssh -i {{$privateKeyFile}}" clone {{$cloneUrl}} {{$projectDir}}

@else

git clone {{$cloneUrl}} {{$projectDir}}

@endif

@if($deploymentScript)
{!! $deploymentScript !!}
@endif

rm -rf {{$selfFile}}
