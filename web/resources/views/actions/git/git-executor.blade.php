chmod +x {{$shellFile}}
chown {{$systemUsername}}:{{$systemUsername}} {{$shellFile}}

su -m {{$systemUsername}} -c "bash {{$shellFile}} > {{$shellLog}}"

@if ($afterCommand)

{{$afterCommand}}

@endif
