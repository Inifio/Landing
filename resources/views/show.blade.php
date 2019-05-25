@extends('layout')

@section('content')
    <div>
        @if ($permission)
            <p>Given code: {{ $code }} </p>
            <p>Access Token: {{ $access_token }} </p>
            <p>Refresh Code: {{ $refresh_code }} </p>
        @else
            <p>No permission given</p>
        @endif
    <div>
@endsection