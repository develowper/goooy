<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <!-- Google tag (gtag.js) -->
{{--    <script async src="https://www.googletagmanager.com/gtag/js?id=G-7YJZL3GXR9"></script>--}}
    {{--    <script>--}}
    {{--        window.dataLayer = window.dataLayer || [];--}}

    {{--        function gtag() {--}}
    {{--            dataLayer.push(arguments);--}}
    {{--        }--}}

    {{--        gtag('js', new Date());--}}

    {{--        gtag('config', 'G-7YJZL3GXR9');--}}
    {{--    </script>--}}
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>
    <link rel="stylesheet" href="https://static.neshan.org/sdk/leaflet/v1.9.4/neshan-sdk/v1.0.8/index.css"/>
    <script src="https://static.neshan.org/sdk/leaflet/v1.9.4/neshan-sdk/v1.0.8/index.js"></script>

    <!-- Scripts -->
    @routes
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
    @inertiaHead
</head>
<body class="font-sans antialiased">
@inertia

</body>
</html>
