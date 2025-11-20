<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        
        <!-- SEO Meta Tags -->
        <meta name="description" content="ASIMPENAS - Platform digital seleksi mitra pangan dan penawaran komoditas gabah beras Perum BULOG Surakarta. Daftar sebagai mitra pangan sekarang!">
        <meta name="keywords" content="ASIMPENAS, Bulog, Mitra Pangan, Gabah, Beras, Surakarta, Sistem Informasi, Manajemen Mitra, Seleksi Mitra, Komoditas">
        <meta name="author" content="Perum BULOG Kantor Cabang Surakarta">
        <meta name="robots" content="index, follow">
        
        <!-- Open Graph Meta Tags -->
        <meta property="og:title" content="ASIMPENAS - Aplikasi Seleksi Mitra dan Penawaran Komoditas Perum BULOG">
        <meta property="og:description" content="Platform digital seleksi mitra pangan dan penawaran komoditas gabah beras Perum BULOG Surakarta. Daftar sebagai mitra pangan sekarang!">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:site_name" content="ASIMPENAS - Perum BULOG Surakarta">
        
        <!-- Twitter Card Meta Tags -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="ASIMPENAS - Aplikasi Seleksi Mitra dan Penawaran Komoditas">
        <meta name="twitter:description" content="Platform digital seleksi mitra pangan dan penawaran komoditas gabah beras Perum BULOG Surakarta">

        <title inertia>{{ config('app.name', 'ASIMPENAS') }}</title>
        <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
        
        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Structured Data (JSON-LD) for SEO -->
        <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebApplication",
            "name": "ASIMPENAS",
            "alternateName": "Aplikasi Seleksi Mitra dan Penawaran Komoditas",
            "url": "{{ url('/') }}",
            "description": "Platform digital seleksi mitra pangan dan penawaran komoditas gabah beras Perum BULOG Surakarta",
            "applicationCategory": "BusinessApplication",
            "operatingSystem": "Web",
            "offers": {
                "@type": "Offer",
                "price": "0",
                "priceCurrency": "IDR"
            },
            "provider": {
                "@type": "Organization",
                "name": "Perum BULOG Kantor Cabang Surakarta",
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "Jl. L.U. Adi Sucipto No. 17, Blulukan, Colomadu",
                    "addressLocality": "Karanganyar",
                    "addressRegion": "Jawa Tengah",
                    "postalCode": "57174",
                    "addressCountry": "ID"
                },
                "telephone": "+62-271-716498",
                "email": "bulog.bumn.ska@gmail.com",
                "url": "https://www.bulog.co.id"
            }
        }
        </script>

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
