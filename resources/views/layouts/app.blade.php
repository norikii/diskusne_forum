<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet" type="text/css">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        body { padding-bottom: 100px }
        .level { display: flex; align-items: center;}
        .flex { flex: 1 }
        [v-cloak] { display: none; }
    </style>

    <!-- Scripts -->
    <script charset="utf-8">
        window.App = {!! json_encode([
            'signedIn' => Auth::check(),
            'user' => Auth::user()
        ]) !!};
    </script>
</head>
<body>
<div id="app">
    @include('layouts.nav')

    <flash message="{{ session('flash') }}"></flash>
</div>
<script src="{{ asset('js/app.js') }}" type="module"></script>
</body>
</html>
