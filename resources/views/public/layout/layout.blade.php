<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('page_title') - I Mum Mum</title>

    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('img/i_mum_mum_white_background_logo.png') }}">
    <link rel="apple-touch-icon" sizes="128x128" href="{{ asset('img/i_mum_mum_white_background_logo.png') }}">
    <link rel="shortcut icon" href="{{ asset('img/i_mum_mum_white_background_logo.png') }}">

    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
</head>

<body class="custom-body">
    <div class="custom-container">
        <img src="{{ asset('img/i_mum_mum_logo.png') }}" alt="I Mum Mum Logo" class="logo">

        @yield('content')
    </div>

    <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>

    @yield('script')
</body>

</html>
