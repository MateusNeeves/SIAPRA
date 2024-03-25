<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'SIAPRA') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
        <style> *{font-family: "Poppins", sans-serif;}</style>
        
        <!-- JQUERY -->
        <script defer type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.js"></script>
    
        <!-- DATATABLES BOOTSTRAP 5 -->
        <link rel="stylesheet" href="//cdn.datatables.net/2.0.3/css/dataTables.dataTables.min.css">
        <script defer type="text/javascript" src="//cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
        <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>

        <style>
            div.dt-container select.dt-input{
                padding: 2px 20px;
                background-position: right;
            }
        </style>

        <!-- MEUS JS -->
        <script defer type="text/javascript" src="./js/table.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')
            
            <!-- Page Content -->
            <main style="padding-top: 65px">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
