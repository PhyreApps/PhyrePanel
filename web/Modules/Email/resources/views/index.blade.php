@extends('email::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('email.name') !!}</p>
@endsection
