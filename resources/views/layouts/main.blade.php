<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>
        {{ config('app.name') }} | @yield('title')
    </title>
    
    <link rel="shortcut icon" href="{{ asset('assets/admin/images/favicon.ico') }}" />
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    @yield('css')
</head>

<body>  

    @yield('content')

    <script src="{{ asset('assets/admin/js/all.min.js') }}"></script>

    @yield('js')


</body>


</html>
