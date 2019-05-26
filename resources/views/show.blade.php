@extends('layout')

@section('content')
    <div>

        {{--@php
            var_dump($channels["id"]);
        @endphp--}}

        @if(count($channels) > 0)
            @if($embed["embedEnabled"] === true)
                <div>
                    <iframe src="{{ $embed["embedURL"] }}" muted=true style="width:100%;height:40em;border:0px;overflow:hidden;padding-top:15px"></iframe>
                </div>
            @endif
            <h2>Enabled Channels</h2>
            @foreach ($channels as $channel)
                @if($channel["enabled"] === true)
                    <br/>
                    <div class="card card-body bg-light">
                        <div class="row">
                            <div class="col-md-1 col-sm-4">
                                <img style="width:50px;height:50px;" src="{{$channel["platformImage"]}}">
                            </div>
                            <div class="col-md-10 col-sm-8">
                                <h3><a href="{{$channel["url"]}}" target="_blank">{{$channel["platformId"]}}</a></h3>
                                <small>{{$channel["displayName"]}}</small>
                            </div>
                            <div>
                                @if($channel["url"] !== "" && substr($channel["url"], -3) !== "/me" && $channel["enabled"] === true)
                                    <a class="btn btn-primary" href="{{$channel["url"]}}" role="button" target="_blank">></a>
                                @endif
                            </div>
                        </div>
                </div>
                @endif
            @endforeach
            {{--{{$posts->links()}}--}}
        @else
            <p>No channels added</p>
        @endif
    <div>
@endsection