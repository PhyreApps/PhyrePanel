@extends('docker::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('docker.name') !!}</p>
@endsection
