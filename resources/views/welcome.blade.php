<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Laravel</title>
</head>
<body>
@auth()
	<p>{{ $user->name }}</p>

	<a href="{{ route('spotify.logout') }}">Logout</a>
@elseguest()
	<a href="{{ route('spotify.redirect') }}">Login</a>
@endauth
</body>
</html>
