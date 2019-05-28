@extends('layout')

@section('content')
    <div>
        @if(count($channels) > 0 || Auth::user()->username === $user)
            @if($embed["embedEnabled"] === true)
                <br/>
                <div>
                    @switch($embed["platform"])
                        @case("Mixer")
                            <h2>Mixer Stream</h2>
                            <iframe src="{{ $embed["embedURL"] }}" muted=true style="width:100%;height:40em;border:0px;overflow:hidden;padding-top:15px"></iframe>
                        @break
                        @case("Twitch")
                            <h2>Twitch Stream</h2>
                            <iframe
                                src="https://player.twitch.tv/?channel={{$embed["displayName"]}}"
                                style="width:100%;height:40em;border:0px;overflow:hidden;padding-top:15px"
                                height="300px"
                                frameborder="0"
                                allowfullscreen="true">
                        </iframe>
                        @break
                    @endswitch
                </div>
            @endif
            <br/>
            <h2>Enabled Channels</h2>
            @foreach ($channels as $channel)
                    <br/>
                    <div class="card card-body bg-light">
                        <div class="row">
                            <div class="col-md-1 col-sm-4">
                                <img style="width:50px;height:50px;" src="{{$channel["platformImage"]}}">
                            </div>
                            <div class="col-md-9 col-sm-8">
                                <h3><a href="{{$channel["url"]}}" target="_blank">{{$channel["platformName"]}}</a></h3>
                                <small>{{$channel["displayName"]}}</small>
                            </div>
                            <div>
                                @auth
                                    @if(Auth::user()->username === $user)
                                        <a class="btn btn-primary" href="/disable/{{$user}}/{{$channel["platformId"]}}" role="button">Disable</a>
                                    @endif
                                @endauth
                                @if($channel["url"] !== "" && substr($channel["url"], -3) !== "/me" && $channel["enabled"] === true)
                                    <a class="btn btn-primary" href="{{$channel["url"]}}" role="button" target="_blank">View</a>
                                @endif
                            </div>
                        </div>
                </div>
            @endforeach
            @auth
                @if(Auth::user()->username === $user)
                    <br/>
                    <h2>Disabled Channels</h2>
                    @foreach($disabledChannels as $disabledChannel)
                            <br/>
                            <div class="card card-body bg-light">
                                <div class="row">
                                    <div class="col-md-1 col-sm-4">
                                        <img style="width:50px;height:50px;" src="{{$disabledChannel["platformImage"]}}">
                                    </div>
                                    <div class="col-md-9 col-sm-8">
                                        <h3><a href="{{$disabledChannel["url"]}}" target="_blank">{{$disabledChannel["platformName"]}}</a></h3>
                                        <small>{{$disabledChannel["displayName"]}}</small>
                                    </div>
                                    <div>
                                        @if(Auth::user()->username === $user)
                                            <a class="btn btn-primary" href="/enable/{{$user}}/{{$disabledChannel["platformId"]}}" role="button">Enable</a>
                                        @endif
                                        @if($disabledChannel["url"] !== "" && substr($disabledChannel["url"], -3) !== "/me" && $disabledChannel["enabled"] === true)
                                            <a class="btn btn-primary" href="{{$disabledChannel["url"]}}" role="button" target="_blank">></a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                    @endforeach
                @endif
            @endauth
        @else
            <div class="row align-items-center justify-content-center">
                <div class="col" style="padding-top: 25em;">
                    <h2 class="text-center text-lg-center">No channels enabled :(</h2>
                    @guest
                        <div class="col text-center">
                            <a class="btn btn-primary text-center" href="/login" role="button">Login to edit channels</a>
                        </div>
                    @endguest
                </div>
            </div>
        @endif
    </div>
@endsection