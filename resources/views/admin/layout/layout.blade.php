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

<body class="body">
    @include('admin/layout/side-bar')

    <div class="main">
        @include('admin/layout/top-bar')

        <div class="content">
            @yield('content')
        </div>
    </div>

    <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>

    @yield('script')
    @yield('scripts')

    <script>
        @if (session()->has('success'))
            notifier.show('Success!', '{!! session('success') !!}', 'success', '', 4000);
        @elseif (session('error'))
            notifier.show('Failed!', '{!! session('error') !!}', 'danger', '', 4000);
        @endif

        function toggleSidebar() {
            if ($("#sidebar").hasClass("sidebar-open")) {
                $("#sidebar").removeClass("sidebar-open");
                $("body").css("overflow-y", "auto");
            } else {
                $("#sidebar").addClass("sidebar-open");
                $("body").css("overflow-y", "hidden");
            }
        }

        function deleteConfirmation(e) {
            e.preventDefault();

            const form = e.target.closest('form');

            Swal.fire({
                title: 'Are you sure you want to delete?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Confirm',
                cancelButtonText: 'Cancel',
                confirmButtonColor: '#fca7af',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>

</body>

</html>
