<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>{{ config('app.name', 'SIMPerpus') }} - @yield('title', 'Selamat Datang')</title>
    <meta name="description" content="Sistem Informasi Perpustakaan SMK" />
    <meta name="author" content="SMKN 1 Sanggau Ledo" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link href="{{ asset('assets/admin/vendor/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/user/css/custom-user.css') }}" rel="stylesheet" />

    @yield('css')

</head>

<body class="d-flex flex-column min-vh-100">
    @include('user.components.header')

    <main class="container mt-4 mb-5 flex-grow-1">
        @hasSection('page-title')
            <h1 class="h3 mb-4 text-gray-800">@yield('page-title')</h1>
        @endif

        @yield('content')
    </main>

    @include('user.components.footer')

    <script src="{{ asset('assets/admin/vendor/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/bootstrap.bundle.min.js') }}"></script>

    <script type="module" src="https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js"></script>
    <script type="module" src="https://www.gstatic.com/firebasejs/11.6.1/firebase-messaging.js"></script>

    <script>
        window.fcmTokenStoreUrl = "{{ route('user.fcm-token.store') }}";
    </script>
    <script type="module" src="{{ asset('assets/user/js/custom-user.js') }}"></script>

    @yield('script')

</body>

</html>
