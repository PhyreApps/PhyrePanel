@extends('letsencrypt::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('letsencrypt.name') !!}</p>
@endsection
