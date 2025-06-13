<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('page_title') - I Mum Mum</title>

    <link rel="stylesheet" type="text/css" href="{{ mix('css/app.css') }}">
</head>

<body>
    @include('admin/layout/side-bar')

    <div class="main">
        @include('admin/layout/top-bar')

        @yield('content')
    </div>

    <script type="text/javascript" src="{{ mix('js/app.js') }}"></script>

    @yield('script')
</body>

</html>

<script>
    function toggleSidebar() {
        if ($("#sidebar").hasClass("sidebar-open")) {
            $("#sidebar").removeClass("sidebar-open");
            $("body").css("overflow-y", "auto");
        } else {
            $("#sidebar").addClass("sidebar-open");
            $("body").css("overflow-y", "hidden");
        }
    }

    function toggleSidebarSectionGroup(e) {
        var sectionGroup = $(e).closest('.sidebar-section-group');

        if (sectionGroup.hasClass('active')) {
            sectionGroup.removeClass('active');
        } else {
            sectionGroup.addClass('active');
        }
    }
</script>
