<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Persetujuan Dokumen Peminjaman Aset
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <div class="mt-8 pb-10">
            <table id="exam" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr class="text-white">
                        <th class="w-10">No</th>
                        <th>Nomor Dokumen</th>
                        <th>Nama Pelanggan</th>
                        <th class="w-20">Aksi</th>
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
                    ajax: "{{ route('documents.loans.pending') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'loan_number',
                            name: 'loan_number'
                        },
                        {
                            data: 'customer_name',
                            name: 'customer_name'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    columnDefs: [{
                            targets: 0,
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-center');
                            }
                        },
                        {
                            targets: 3,
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('flex justify-center gap-2 w-max');
                            }
                        }
                    ]
                }).columns.adjust()
                .responsive.recalc();
        });
    </script>

</x-app-layout>
