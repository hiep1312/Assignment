@php $currentPage = basename($_SERVER['PATH_INFO']); @endphp
<!DOCTYPE html>
<html lang="en" class="light-style @if(in_array($currentPage, ['auth-forgot-password-basic', 'auth-login-basic', 'auth-register-basic'], true)) customizer-hide @else layout-menu-fixed @endif" dir="ltr">
<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>@yield('title', "Sneat Admin")</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset("admin/assets/img/favicon/favicon.ico") }}" />

    <!-- Fonts Google -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="{{ asset("admin/assets/fonts/boxicons.css") }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset("admin/assets/css/core.css") }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset("admin/assets/css/theme-default.css") }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset("admin/assets/css/style-theme.css") }}" />
    @vite('resources/css/app.css')

    <!-- Library CSS -->
    <link rel="stylesheet" href="{{ asset("admin/assets/libs/perfect-scrollbar/perfect-scrollbar.css") }}" />

    <!-- Page CSS -->
    @stack('styles')

    <!-- Helpers -->
    <script src="{{ asset("admin/assets/js/helpers.js") }}"></script>

    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset("admin/assets/js/config.js") }}"></script>
</head>
<body>
    @section('body')
        <div class="layout-wrapper layout-content-navbar">
            <div class="layout-container">
                @include('admin.partials.sidebar', ['currentRoute' => $currentPage])
                <div class="layout-page">
                    @include('admin.partials.header')
                    <div class="content-wrapper">
                        @yield('content')
                        @include('admin.partials.footer')

                        <div class="content-backdrop fade"></div>
                    </div>
                </div>
            </div>

            <!-- Overlay -->
            <div class="layout-overlay layout-menu-toggle"></div>
        </div>
    @show

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="{{ asset("admin/assets/libs/jquery/jquery.js") }}"></script>
    <script src="{{ asset("admin/assets/libs/popper/popper.js") }}"></script>
    <script src="{{ asset("admin/assets/js/bootstrap.js") }}"></script>
    <script src="{{ asset("admin/assets/libs/perfect-scrollbar/perfect-scrollbar.js") }}"></script>
    <script src="{{ asset("admin/assets/js/menu.js") }}"></script>
    @vite('resources/js/app.js')
    <!-- endbuild -->

    <!-- Main JS -->
    <script src="{{ asset("admin/assets/js/main.js") }}"></script>

    <!-- Page JS -->
    @stack('scripts')

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
</body>
</html>
