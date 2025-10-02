@extends('layouts.admin')

@section('title', 'Without Menu Layouts - Sneat Admin')

@section('body')
    <div class="layout-wrapper layout-content-navbar layout-without-menu">
        <div class="layout-container">
            <!-- Layout container -->
            <div class="layout-page">
                @include('admin.partials.header')

                <!-- Content wrapper -->
                <div class="content-wrapper">
                    <!-- Content -->

                    <div class="container-xxl flex-grow-1 container-p-y">
                        <!-- Layout Demo -->
                        <div class="layout-demo-wrapper">
                            <div class="layout-demo-placeholder">
                                <img src="{{ asset('admin/assets/img/layouts/layout-without-menu-light.png') }}" class="img-fluid"
                                    alt="Layout without menu" data-app-light-img="layouts/layout-without-menu-light.png"
                                    data-app-dark-img="layouts/layout-without-menu-dark.png" />
                            </div>
                            <div class="layout-demo-info">
                                <h4>Layout without Menu (Navigation)</h4>
                                <button class="btn btn-primary" type="button" onclick="history.back()">Go Back</button>
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
    </div>
@endsection
