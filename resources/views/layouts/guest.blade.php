<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
        <style> *{font-family: "Poppins", sans-serif;}</style>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">

        <!-- MEU CSS -->
        <link rel="stylesheet" href="./css/styles.css">

    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="mt-6">
                <img class="mt-6" src="{{ asset('images/logo-crcn-ne-vertical-principal.png') }}" alt="Logo" width="150" height="75">
            </div>
            <br> <br><br>
            <div class=" flex flex-col items-center sm:justify-center" style="width: 500px">
                <h1 class="text-center" style="font-family: 'Roboto Condensed', sans-serif; font-size: 4rem; color: #f3523b; font-weight: 1000">
                    SIAPRA
                </h1>
                
                <h1 class="text-center" style="font-family: 'Roboto Condensed', sans-serif; font-size: 1.5rem; color: #f3523b; font-weight: 350;">
                    Sistema de Apoio à Produção <br> de Radiofármacos
                </h1>
                
                <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                    {{ $slot }}
                </div>
            </div>
        </div>
    </body>
</html>
