@extends('microweber::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('microweber.name') !!}</p>
@endsection
