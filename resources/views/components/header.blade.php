<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="stylesheet" href="https://cdn.datatables.net/2.1.3/css/dataTables.tailwindcss.css" />
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.dataTables.min.css" rel="stylesheet">


    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- SweetAlert2 CSS and JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.12.4/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Select2 CSS and JS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js" defer></script>

    <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.5/dist/signature_pad.umd.min.js"></script>
    <style>
        #signatureCanvas {
            border: 2px dashed #ccc;
            border-radius: 4px;
            touch-action: none;
        }
    </style>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Custom Styles for Select2 -->
    <style>
        .dropdown {
            display: none;
            position: absolute;
            top: 60px;
            right: 0;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            z-index: 1000;
        }

        .dropdown a {
            display: block;
            padding: 10px 15px;
            color: black;
            text-decoration: none;
        }

        .dropdown a:hover {
            background-color: #74B2E9;
            color: white;
        }

        @media (max-width: 768px) {
            .foto {
                width: 40px;
                height: 40px;
            }
        }

        @media (max-width: 480px) {
            .foto {
                width: 35px;
                height: 35px;
            }

            .dropdown {
                top: 45px;
            }
        }
    </style>

    <style>
        .select2.select2-container {
            display: flex;
            width: 100%;
        }

        .selection {
            display: flex;
            width: 100%;
        }

        .select2-selection.select2-selection--single {
            height: 43px;
            border: 1px solid #d1d3e2;
            --tw-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --tw-shadow-colored: 0 1px 2px 0 var(--tw-shadow-color);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
            margin-top: 5px;
            width: 100%;
            padding-top: 5px;
        }

        .select2-select2-container {
            height: 40px;
            padding-top: 5px;
        }

        .select2-selection__arrow {
            margin-top: 12px;
        }

        .select2-container span {
            border: none;
        }
    </style>


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

        .dt-button.buttons-collection.buttons-colvis {
            margin-right: 15px !important;
            background-color: #31363F !important;
            border-radius: 0.375rem !important;
            color: #ffffff !important;
            font-weight: 500 !important;
            border: 1px solid transparent !important;
            padding: 0.5rem 1rem !important;
            box-sizing: border-box !important;
            background: none;
            transition: background-color 0.2s ease, color 0.2s ease !important;
        }

        .dt-button.buttons-collection.buttons-colvis:hover {
            background-color: #31363F !important;
            color: #ffffff !important;
            border: 1px solid transparent !important;
            transform: none !important;
            outline: none !important;
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

        /* Membuat wrapper tabel dapat menggulir horizontal, tetapi tidak untuk filter, pencarian, dan pagination */
        #exam_wrapper {
            width: 100%;
            overflow: hidden;
            /* Menghindari scroll di wrapper */
        }

        /* Hanya tabel yang dapat di-scroll horizontal */
        #exam {
            width: 100%;
            overflow-x: auto;
            /* Scroll horizontal hanya untuk tabel */
            display: block;
            /* Pastikan tabel bisa memiliki scroll horizontal */
            table-layout: fixed;
            /* Menghindari kolom meluber */
        }

        /* Mengatur posisi filter dan pagination agar tetap di tempat */
        .dataTables_filter,
        .dataTables_length,
        .dataTables_paginate {
            position: relative;
            z-index: 10;
            /* Agar tetap di atas tabel */
        }

        /* Pastikan elemen-elemen kontrol tidak tergulung */
        /* #exam_wrapper .dataTables_filter,
        #exam_wrapper .dataTables_length,
        #exam_wrapper .dataTables_paginate {
            position: sticky;
            top: 0;
            z-index: 0;
        } */

        /* Menambahkan padding atau margin pada tabel untuk memberi ruang pada elemen kontrol */
        #exam {
            margin-top: 20px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
