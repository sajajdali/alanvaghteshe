@extends('reminder::layouts.master')

@section('content')
    <h1>Hello World</h1>

    <p>Module: {!! config('reminder.name') !!}</p>
@endsection
