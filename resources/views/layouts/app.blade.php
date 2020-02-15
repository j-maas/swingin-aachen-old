<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Swingin&rsquo; Aachen</title>

    <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
</head>
<body>
@include('header')
<main>
    @yield('content')
</main>
</body>
</html>
