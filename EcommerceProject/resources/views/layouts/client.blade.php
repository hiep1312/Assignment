<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">

    <title>
        @hasSection('title')
            @yield('title', "Bookio")
        @else
            {{ $title ?? "Bookio" }}
        @endif
    </title>

    <!-- Favicon -->
    <link href="{{ asset("storage/logo-bookio.ico") }}" rel="icon">

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Heebo:wght@400;500;600&family=Nunito:wght@600;700;800&family=Pacifico&display=swap" rel="stylesheet">

    <!-- Icon Font Stylesheet -->
    <link href="https://site-assets.fontawesome.com/releases/v7.1.0/css/brands.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="{{ asset('client/assets/lib/animate/animate.min.css') }}" rel="stylesheet">
    <link href="{{ asset('client/assets/lib/owlcarousel/assets/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('client/assets/lib/tempusdominus/css/tempusdominus-bootstrap-4.min.css') }}" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="{{ asset('client/assets/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- Website Stylesheet -->
    <link href="{{ asset('client/assets/css/style.css') }}" rel="stylesheet">
    @vite('resources/css/app.css')

    <!-- Page CSS -->
    @stack('styles')
</head>
<body class="bg-white p-0">
    {{-- @include('client.partials.spinner') --}}

    @section('body')
        <div class="container-fluid position-relative p-0">
            @hasSection('hero')
                @include('client.partials.header', ['hasBreadcrumb' => true])

                @yield('hero', '')
            @else
                @include('client.partials.header', ['hasBreadcrumb' => false])
            @endif
        </div>

        <main class="content-wrapper">
            @livewire('client.partials.error')

            @hasSection('content')
                @yield('content')
            @else
                {{ $slot ?? '' }}
            @endif
        </main>

        @include('client.partials.footer')
    @show

    <!-- Back to Top -->
    <a href="javascript:void(0);" class="btn btn-lg btn-primary btn-lg-square back-to-top"><i class="bi bi-arrow-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="{{ asset('client/assets/lib/wow/wow.min.js') }}"></script>
    <script src="{{ asset('client/assets/lib/easing/easing.min.js') }}"></script>
    <script src="{{ asset('client/assets/lib/waypoints/waypoints.min.js') }}"></script>
    <script src="{{ asset('client/assets/lib/counterup/counterup.min.js') }}"></script>
    <script src="{{ asset('client/assets/lib/owlcarousel/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('client/assets/lib/tempusdominus/js/moment.min.js') }}"></script>
    <script src="{{ asset('client/assets/lib/tempusdominus/js/moment-timezone.min.js') }}"></script>
    <script src="{{ asset('client/assets/lib/tempusdominus/js/tempusdominus-bootstrap-4.min.js') }}"></script>

    <!-- Website Javascript -->
    <script src="{{ asset('client/assets/js/main.js') }}"></script>
    @vite('resources/js/app.js')

    <!-- Page JS -->
    @stack('scripts')
</body>
</html>
