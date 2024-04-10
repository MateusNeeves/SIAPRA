<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>SIAPRA</title>

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet" />
        <style> *{font-family: "Poppins", sans-serif;}</style>
        
        <!-- ICONES BOOTSTRAP -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" >

        <!-- JQUERY -->
        <script defer type="text/javascript" src="https://code.jquery.com/jquery-3.7.1.js"></script>
        <script src="//ajax.aspnetcdn.com/ajax/jQuery/jquery-2.1.1.js" type="text/javascript"></script>
    
        <!-- DATATABLES -->
        <link rel="stylesheet" href="https://cdn.datatables.net/2.0.3/css/dataTables.dataTables.min.css">
        <script defer type="text/javascript" src="https://cdn.datatables.net/2.0.3/js/dataTables.min.js"></script>

        <!-- BOOTSTRAP 5 -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
        <script defer type="text/javascript" src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
        
        <!-- DATATABLES BUTTONS -->
        <link rel="stylesheet" href="https://cdn.datatables.net/buttons/3.0.1/css/buttons.dataTables.min.css">
        <script defer type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.1/js/dataTables.buttons.min.js"></script>

            <!-- HTML BUTTON -->
            <script defer type="text/javascript" src="https://cdn.datatables.net/buttons/3.0.1/js/buttons.html5.min.js"></script>
        
        <!-- DATATABLES SELECT -->
        <script defer type="text/javascript" src="https://cdn.datatables.net/select/2.0.0/js/dataTables.select.min.js"></script>
        <link rel="stylesheet" href="https://cdn.datatables.net/select/2.0.0/css/select.dataTables.min.css">
        
        <!-- PDF MAKER -->
        <script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
        <script defer type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
        <!-- PRINT DIV TO PDF -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.5/jspdf.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.4.1/html2canvas.min.js"></script>

        
        <!-- MEU CSS -->
        <link rel="stylesheet" href="./css/styles.css">
        
        <!-- MEUS JS -->
        <script defer type="text/javascript" src="./js/delete.js"></script>
        <script defer type="text/javascript" src="./js/table.js"></script>
        <script defer type="text/javascript" src="./js/paramTable.js"></script>
        <script defer type="text/javascript" src="./js/printPdf.js"></script>

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
