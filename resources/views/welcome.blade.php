@extends('layout')

@section('content')
    <div>
        @if (session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif
        <h1>Welcome to Restream Landing</h1>
        <p>Let your viewers decide where to watch and enjoy your content!</p>
        <a class="btn btn-primary" href="/login" role="button">Login with Restream</a>
    <div>
@endsection