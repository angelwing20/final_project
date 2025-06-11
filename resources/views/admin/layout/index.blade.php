<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('page_title') - I Mum Mum</title>

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>

<body>
    @include('admin/layout/side-bar')

    <div class="main">
        @include('admin/layout/top-bar')

        @yield('content')
    </div>

    @yield('script')
</body>

</html>
