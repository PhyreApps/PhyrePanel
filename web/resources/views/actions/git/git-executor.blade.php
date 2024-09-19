chmod +x {{$shellFile}}
chown {{$systemUsername}}:{{$systemUsername}} {{$shellFile}}

sudo -m {{$systemUsername}} -c "bash {{$shellFile}}"

@if ($afterCommand)

{{$afterCommand}}

@endif
