@if(isset($version))
version: '{{$version}}'
@endif

services:

  {{$name}}:
    image: "{{$image}}"
    restart: always
    ports:
      - {{$externalPort}}:{{ $port }}

    @if(isset($environmentVariables)
                && !empty($environmentVariables)
                && is_array($environmentVariables))

    environment:

    @foreach($environmentVariables as $key => $value)

      {{$key}}: "{{$value}}"

    @endforeach
    @endif
