<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- SEO Meta Tags -->
        <meta name="description" content="Aplikasi Sistem Informasi Manajemen Mitra Pangan (ASIMPENAS) - Perum BULOG Kantor Cabang Surakarta untuk pengelolaan data mitra gabah dan beras dalam negeri">
        <meta name="keywords" content="ASIMPENAS, Bulog, Mitra Pangan, Gabah, Beras, Surakarta, Sistem Informasi, Manajemen Mitra">
        <meta name="author" content="Perum BULOG Kantor Cabang Surakarta">
        <meta name="robots" content="index, follow">
        
        <!-- Open Graph Meta Tags -->
        <meta property="og:title" content="ASIMPENAS - Sistem Informasi Manajemen Mitra Pangan">
        <meta property="og:description" content="Aplikasi Sistem Informasi Manajemen Mitra Pangan (ASIMPENAS) - Perum BULOG Kantor Cabang Surakarta">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="ASIMPENAS">
        
        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="ASIMPENAS - Sistem Informasi Manajemen Mitra Pangan">
        <meta name="twitter:description" content="Aplikasi Sistem Informasi Manajemen Mitra Pangan (ASIMPENAS) - Perum BULOG Kantor Cabang Surakarta">

        <title inertia>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        
        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
