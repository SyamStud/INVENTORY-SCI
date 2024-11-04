<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.tailwindcss.css" />
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet">

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])


    <!-- jQuery -->
    <style>
        .table-striped tbody tr:nth-child(odd) {
            background-color: #f9f9f9;
        }

        .table-striped tbody tr:nth-child(even) {
            background-color: #ffffff;
        }

        .table th,
        .table td {
            padding: 8px 12px;
            border: 1px solid #ddd;
        }

        .table th {
            background-color: #31363F;
            text-align: center;
        }

        .table th:first-child {
            background-color: #31363F;
            text-align: center;
            border-top-left-radius: 8px;
        }

        .table th:last-child {
            background-color: #31363F;
            text-align: center;
            border-top-right-radius: 8px;
        }

        .table tr:last-child td:first-child {
            border-bottom-left-radius: 8px;
        }

        .table tr:last-child td:last-child {
            border-bottom-right-radius: 8px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button.current {
            color: #ffffff !important;
            font-weight: 500;
            border-radius: .25rem;
            background: #31363F !important;
            border: 1px solid transparent;
            font-size: 14px;
        }

        .dataTables_wrapper select {
            width: 80px;
        }

        .dataTables_wrapper select,
        .dataTables_wrapper .dataTables_filter input {
            color: #575757;
            padding-left: 1rem;
            padding-right: 1rem;
            padding-top: 5px;
            padding-bottom: 5px;
            line-height: 1.25;
            border-width: 2px;
            border-radius: .25rem;
            border-color: #edf2f7;
            background-color: rgb(255, 255, 255);
            margin-bottom: 20px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            font-weight: 500;
            border-radius: .25rem;
            border: 1px solid transparent;
        }

        .paginate_button,
        .dataTables_filter,
        .dataTables_length,
        .dataTables_info,
        .dtr-title,
        .dtr-data {
            font-size: 14px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            color: #ffffff !important;
            font-weight: 500;
            border-radius: .25rem;
            background: #31363F !important;
            border: 1px solid transparent;
        }

        table.dataTable.no-footer {
            border-bottom: none;
            /*border-b-1 border-gray-300*/
            margin-top: 0.75em;
            margin-bottom: 0.75em;
        }

        table.dataTable.dtr-inline.collapsed>tbody>tr>td:first-child:before,
        table.dataTable.dtr-inline.collapsed>tbody>tr>th:first-child:before {
            background-color: #31363F !important;
        }

        .dtr-data {
            /* display: flex; */
            gap: 5px;
            width: 100%;
        }

        .dtr-details {
            width: 100%;
        }

        .modal-enter {
            transition: opacity 0.3s ease-out, transform 0.3s ease-out;
            opacity: 0;
            transform: translate(0, 1rem) scale(0.95);
        }

        .modal-enter-active {
            opacity: 1;
            transform: translate(0, 0) scale(1);
        }

        .modal-leave {
            transition: opacity 0.2s ease-in, transform 0.2s ease-in;
            opacity: 1;
            transform: translate(0, 0) scale(1);
        }

        .modal-leave-active {
            opacity: 0;
            transform: translate(0, 1rem) scale(0.95);
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @include('layouts.navigation')

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="mx-auto py-6 px-10 sm:px-6 lg:px-10">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    <!-- DataTable JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.4.1/js/responsive.bootstrap5.min.js"></script>

    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jsPDF/2.4.0/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.13/jspdf.plugin.autotable.min.js"></script>
</body>

</html>
