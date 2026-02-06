<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')
    <title>@yield('title')::Flexicell Mobile</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/logo.png') }}" />
    <!-- Font Awesome -->
    <link href="{{ asset('assets/fontawesome/css/all.min.css') }}" rel="stylesheet" />
    <!-- Datatable -->
    <link rel="stylesheet" href="{{ asset('assets/css/dataTables.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/buttons.bootstrap5.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/buttons.dataTables.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.dataTables.min.css') }}">
    <!-- Sweet Alert 2 -->
    <link rel="stylesheet" href="{{ asset('assets/css/sweetalert2.css') }}">
    {{-- theme css --}}
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css?' . date('YmdHis')) }}" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/2.0.0-alpha.2/cropper.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    @yield('style')
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed" style="background-color: rgb(255, 255, 255);">
        <!-- Sidebar Start -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            @include('layouts.partials.aside')
            <!-- End Sidebar scroll-->
        </aside>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            @include('layouts.partials.header')
            <!--  Header End -->
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
        <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
        <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
        <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
        <script src="{{ asset('assets/libs/simplebar/dist/simplebar.js') }}"></script>
        <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
        {{-- Sweet Alert 2 --}}
        <script src="{{ asset('assets/js/sweetalert2.min.js') }}"></script>
        {{-- js validation --}}
        <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>
        <script type="text/javascript" src="{{ asset('assets/js/twitter-bootstrap/bootstrap.min.js') }}"></script>
        {{-- Datatable --}}
        <script src="{{ asset('assets/js/datatable/datatable.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/datatables.mark.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/rowReorder.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/dataTables.buttons.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/buttons.bootstrap5.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/jszip.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/pdfmake.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/vfs_fonts.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/buttons.html5.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/buttons.print.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/mark.min.js') }}"></script>
        <script src="{{ asset('assets/js/datatable/datatable_responsive.js') }}"></script>
        {{-- template js --}}
        <script src="{{ asset('assets/js/app.min.js') }}"></script>
        <script src="{{ asset('assets/js/custom.js?' . date('YmdHis')) }}"></script>
        <script src="{{ asset('js/iconify-icon.min.js') }}"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/2.0.0-alpha.2/cropper.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

        @yield('script')
</body>

</html>
