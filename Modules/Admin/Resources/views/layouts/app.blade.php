<!doctype html>
<html lang="fa_IR" dir="rtl"
      style="--primary01:rgba(0, 112, 187, 0.1); --primary02:rgba(0, 112, 187, 0.2); --primary03:rgba(0, 112, 187, 0.3); --primary06:rgba(0, 112, 187, 0.6); --primary09:rgba(0, 112, 187, 0.9); --primary-bg-color:#0070bb; --primary-bg-hover:#0070bb95; --primary-bg-border:#0070bb; --dark-null:rgba(0, 112, 187, 0.5); --transparent-null:#0070bb; --primary-transparentcolor:#0070bb20; --darkprimary-null:#0070bb20; --transparentprimary-null:#0070bb20;">
<head>

    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="سیستم مدیریت">
    <meta name="author" content="شمیران وب">
    <meta name="keywords" content="مدیریت الان وقتشه">
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="76x76" href="{{ admin_default_asset('favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ admin_default_asset('favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ admin_default_asset('favicon/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ admin_default_asset('favicon/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ admin_default_asset('favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
    <link rel="mask-icon" href="{{ admin_default_asset('favicon/safari-pinned-tab.svg') }}" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <!-- TITLE -->
    <title>{{ $title ?? 'مدیریت' }}</title>
    @include('admin::layouts.components.styles')
    @stack('styles')
    @livewireStyles
</head>

<body class="rtl app sidebar-mini">


<!-- PAGE -->
<div class="page">
    <div class="page-main">
        @include('admin::layouts.components.app-header')
        @include('admin::layouts.components.app-sidebar')
        <!--app-content open-->
        <div class="app-content main-content mt-0">
            <div class="side-app">
                <!-- CONTAINER -->
                <div class="main-container container-fluid">
                    @yield('content')
                    {{ $slot ?? '' }}
                </div>
            </div>
        </div>
        <!-- CONTAINER CLOSED -->
    </div>

    @include('admin::layouts.components.modal')

    @yield('modal')

    @include('admin::layouts.components.footer')

</div>
<!-- page -->

@include('admin::layouts.components.scripts')
@livewireScripts
@stack('scripts')
</body>

</html>
