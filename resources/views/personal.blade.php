@extends('layouts.app')

@section('content')
    <p>
        Hey {{ $user->name }}!
    </p>
    <p>
        Your most listened tracks and artists have been gathered and are listed below.
    </p>

    <form action="{{ route('personal.notify') }}" method="post">
        @csrf
        @method('PATCH')

        <div style="margin-bottom: 1em;">
            <input type="checkbox" name="notify" @checked($user->notify) id="notify">
            <label for="notify">Notify me when new data is available</label>
        </div>

        @session('success')
        <p>Preferences updated!</p>
        @endsession

        <button type="submit" class="btn">Update</button>
    </form>

    <b>Tracks</b>

    @if($tracks->isNotEmpty())
        <ol>
            @foreach($tracks as $track)
                <li>
                    <a href="{{ $track->uri }}">{{ $track->name }}</a>
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
                    <a href="{{ $artist->uri }}">{{ $artist->name }}</a>
                </li>
            @endforeach
        </ol>
    @else
        <p>No data available</p>
    @endif

    <div style="margin-bottom: 2em;">
        <a href="{{ route('home') }}" class="btn">Back To Home</a>
    </div>
@endsection
