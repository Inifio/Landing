@extends('show')

@section('embed')

    <div>
        <iframe src="{{ $embed["embedURL"] }}" muted=true style="width:100%;height:40em;border:0px;overflow:hidden;padding-top:15px"></iframe>
    </div>

@endsection