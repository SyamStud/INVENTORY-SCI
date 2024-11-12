<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<x-header></x-header>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @if ($type == 'admin')
            @include('layouts.nav-admin')
        @else
            @include('layouts.nav')
        @endif

        <!-- Page Heading -->
        @isset($header)
            <header class="bg-white shadow">
                <div class="mx-auto py-6 px-10 sm:px-6 lg:px-10">
                    {{ $header }}
                </div>
            </header>
        @endisset

        <x-alert position="top-end"></x-alert>

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
