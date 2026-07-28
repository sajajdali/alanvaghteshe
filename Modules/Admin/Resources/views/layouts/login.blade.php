<!doctype html>
<html lang="fa_IR" dir="rtl">
<!-- This "custom-app.blade.php" master page is used only for "custom" page content present in "views/livewire" Ex: login, 404 -->
<head>

    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="سیستم مدیریت">
    <meta name="author" content="شمیرات وب">
    <meta name="keywords" content="مدیرت">

    <!-- TITLE -->
    <title>{{ $title ?? 'ورود امن مدیریت' }}</title>

    @include('admin::layouts.components.styles')
    @livewireStyles

</head>

<body class="rtl login-img">

<!-- GLOBAL-LOADER -->
<div id="global-loader">
    <img src="{{admin_asset('images/loader.svg')}}" class="loader-img" alt="Loader">
</div>

@yield('content')
{{ $slot }}
@include('admin::layouts.components.scripts')
@livewireScripts
@stack('scripts')
</body>

</html>
