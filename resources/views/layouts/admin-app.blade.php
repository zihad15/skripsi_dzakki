<!DOCTYPE html>
<html lang="en">
    @include('partials.head')
    <body class="sb-nav-fixed">
       @include('partials.header')
        <div id="layoutSidenav">
            @include('partials.navbar')
            <div id="layoutSidenav_content">
                <main>
                    @yield('content')
                </main>
                @include('partials.footer')
            </div>
        </div>
        @include('partials.foot')
    </body>
</html>
