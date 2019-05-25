@extends('layout')

@section('content')
    <div>
        <h1>Welcome to Restream Landing</h1>
        <p>Let your viewers decide where to watch and enjoy your content!</p>
        <a class="btn btn-primary" href="https://api.restream.io/login?response_type=code&client_id=3b5d20d7-2860-49da-bf54-e4f8c6dad488&redirect_uri=http://127.0.0.1:8000/oauth/redirect&state=test" role="button">Link</a>
    <div>
@endsection