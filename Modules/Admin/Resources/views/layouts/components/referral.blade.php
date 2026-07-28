<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="theme-color" content="#ffffff"/>
    <title>{{ $title ?? 'Default Title' }}</title>
    <!-- فایل CSS Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- فایل JS Bootstrap (باید بعد از jQuery لود شود) -->

    <link rel="stylesheet" href="{{ asset('assets/landing/css/main.css') }}"/>
    @livewireStyles
    @stack('styles')
    <style>
        @media (max-width: 1023px) {
            .mobile-fixed-btn-wrapper {
                position: fixed;
                bottom: 8px;
                left: 8px;
                right: 8px;
                z-index: 1000;
            }

            .mobile-fixed-btn {
                width: 100%;
                background-color: #9333ea;
                color: white;
                padding: 1rem 0;
                border: none;
                border-radius: 0;
                font-size: 1rem;
                text-align: center;
            }
        }
    </style>
</head>

<body class="container bg-gradient-to-l from-light-orange to-lilac">
{{ $slot ?? '' }}


<script src="{{ asset('assets/landing/js/main.js') }}"></script>
<script src="{{ asset('assets/landing/js/spritemap.js') }}"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@livewireScripts
@stack('scripts')
</body>

</html>
