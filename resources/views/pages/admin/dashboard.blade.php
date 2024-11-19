<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <div class="mt-8 pb-10">
            <table id="exam" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr class="text-white">
                        <th class="w-10">No</th>
                        <th>Nomor Peminjaman</th>
                        <th>Nama Peminjam</th>
                        <th>Tag Number</th>
                        <th>Nama Aset</th>
                        <th>Merek</th>
                        <th>Kuantitas</th>
                        <th>Durasi</th>
                        <th>Tanggal Peminjaman</th>
                        <th>Estimasi Pengembalian</th>
                        {{-- <th class="w-20">Status</th> --}}
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <script>
        let dataTable;

        $(function() {

            dataTable = $('#exam').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ route('dashboard.getLoanAssets') }}",
                    },
                    dom: '<"top"Blf>rt<"bottom"ip>',
                    buttons: [{
                        extend: 'colvis',
                        text: 'Sembunyikan Kolom',
                        columns: ':not(:first-child)',
                    }],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'loan_number',
                            name: 'loan_number',
                            orderable: false
                        },
                        {
                            data: 'customer_name',
                            name: 'customer_name',
                            orderable: false
                        },
                        {
                            data: 'tag_number',
                            name: 'tag_number',
                            orderable: false
                        },
                        {
                            data: 'asset',
                            name: 'asset',
                            orderable: false
                        },
                        {
                            data: 'brand',
                            name: 'brand',
                            orderable: false
                        },
                        {
                            data: 'quantity',
                            name: 'quantity',
                            orderable: false
                        },
                        {
                            data: 'duration',
                            name: 'duration',
                            orderable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'return_estimation',
                            name: 'return_estimation',
                            orderable: false,
                            searchable: false
                        },
                    ],
                    columnDefs: [{
                        targets: [0, 4, 5, 6, 7, 8],
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).addClass('text-center');
                        }
                    }, ],
                }).columns.adjust()
                .responsive.recalc();
        });
    </script>
</x-app-layout>
