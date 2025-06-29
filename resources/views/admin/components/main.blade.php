<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin SIMPUS - @yield('title', 'Dashboard')</title>
    <meta name="description" content="Admin Sistem Informasi Perpustakaan" />
    <meta name="author" content="SMKN 1 Sanggau Ledo" />
    <link rel="icon" type="image/png" href="{{ asset('assets/images/logo.png') }}" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" />
    <link href="{{ asset('assets/admin/vendor/fa.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/vendor/bootstrap.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/vendor/datatables.min.css') }}" rel="stylesheet" />
    <link href="{{ asset('assets/admin/css/custom-admin.css') }}" rel="stylesheet" />

    @yield('css')
</head>

<body>

    @include('admin.components.header')

    <div class="container-fluid">
        <div class="row">

            <nav class="col-lg-2 d-none d-lg-block sidebar-desktop">
                <div class="sidebar-sticky">
                    @include('admin.components.sidebar-menu')
                </div>
            </nav>

            <main class="col-12 col-lg-10 main-content-desktop">

                <div class="page-header mt-4">
                    <h1 class="h2">@yield('page-title', 'Dashboard')</h1>
                </div>

                <div class="page-content">
                    @yield('content')
                </div>

            </main>
        </div>
    </div>

    @include('admin.components.sidebar-offcanvas')

    <script src="{{ asset('assets/admin/vendor/jquery-3.7.0.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendor/datatables.min.js') }}"></script>

    @yield('script')
</body>

</html>
