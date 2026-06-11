<!DOCTYPE html>
@if (\Request::is('rtl'))
  <html dir="rtl" lang="ar">
@else
  <html lang="en">
@endif

<head>
    <x-head></x-head>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="g-sidenav-show bg-gray-100 {{ (\Request::is('rtl') ? 'rtl' : (Request::is('virtual-reality') ? 'virtual-reality' : '')) }}">

@if (
    Request::is('login') ||
    Request::is('register') ||
    Request::is('login/forgot-password') ||
    Request::is('reset-password/*')
)
    @include('layouts.navbars.guest.nav')
    @yield('content')
    @include('layouts.footers.guest.footer')

@else
    @include('layouts.navbars.auth.sidebar')

    <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
        @include('layouts.navbars.auth.nav')

        <div class="container-fluid py-4">

            @yield('content')

            @include('layouts.footers.auth.footer')
        </div>
    </main>
@endif

<x-script></x-script>

</body>
</html>
