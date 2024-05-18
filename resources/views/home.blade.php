@extends('layouts.app')

@section('content')
    <br/>

    <b>Tracks</b>

    @if($tracks->isNotEmpty())
        <ol>
            @foreach($tracks as $track)
                <li>
                    <a href="{{ $track->uri }}">{{ $track->name }} ({{ $track->popularity }})</a>
                </li>
            @endforeach
        </ol>
    @else
        <p>No data available</p>
    @endif

    <b>Artists</b>

    @if($tracks->isNotEmpty())
        <ol>
            @foreach($artists as $artist)
                <li>
                    <a href="{{ $artist->uri }}">{{ $artist->name }} ({{ $artist->popularity }})</a>
                </li>
            @endforeach
        </ol>
    @else
        <p>No data available</p>
    @endif
@endsection
