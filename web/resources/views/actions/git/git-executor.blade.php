mkdir -p /home/{{$systemUsername}}/.ssh
ssh-keyscan {{$gitProvider}} >> /home/{{$systemUsername}}/.ssh/known_hosts
chmod 0600 /home/{{$systemUsername}}/.ssh/known_hosts
chown {{$systemUsername}}:{{$systemUsername}} /home/{{$systemUsername}}/.ssh/known_hosts


chmod +x {{$shellFile}}
chown {{$systemUsername}}:{{$systemUsername}} {{$shellFile}}

su -m {{$systemUsername}} -c "bash {{$shellFile}} > {{$shellLog}}"

@if ($afterCommand)

{{$afterCommand}}

@endif
