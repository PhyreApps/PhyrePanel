@extends('minecraft::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('minecraft.name') !!}</p>
@endsection
