@extends('layouts.admin')

@section('title', 'Without navbar - Sneat Admin')

@section('body')
    <div class="layout-wrapper layout-content-navbar">
        <div class="layout-container">
            @include('admin.partials.sidebar', ['currentRoute' => basename($_SERVER['PATH_INFO'])])

            <!-- Layout container -->
            <div class="layout-page">
                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Layout Demo -->
                        <div class="layout-demo-wrapper">
                            <div class="layout-demo-placeholder">
                                <img src="{{ asset('admin/assets/img/layouts/layout-without-navbar-light.png') }}" class="img-fluid"
                                    alt="Layout without navbar"
                                    data-app-light-img="layouts/layout-without-navbar-light.png"
                                    data-app-dark-img="layouts/layout-without-navbar-dark.png" />
                            </div>
                            <div class="layout-demo-info">
                                <h4>Layout without Navbar</h4>
                                <p>Layout does not contain Navbar component.</p>
                            </div>
                        </div>
                        <!--/ Layout Demo -->
                    </div>
                    <!-- / Content -->

                    @include('admin.partials.footer')

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- Content wrapper -->
            </div>
            <!-- / Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>
@endsection
