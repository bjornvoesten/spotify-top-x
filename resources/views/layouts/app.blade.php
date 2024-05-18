<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    @vite('resources/css/app.css')
</head>
<body>
<div>
<div>
    @auth()
        <a href="{{ route('spotify.logout') }}" class="btn">Logout</a>
    @elseguest()
        <a href="{{ route('spotify.redirect') }}" class="btn">Login</a>
    @endauth
</div>

<div>
    @yield('content')
</div>
</div>
</body>
</html>
