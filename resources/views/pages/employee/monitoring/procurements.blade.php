<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Monitoring Pengadaan
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <form>
                <div class="flex flex-wrap items-center gap-4">
                    <label for="branch_id" class="font-medium text-gray-700">
                        Unit Kerja:
                    </label>
                    <div class="flex-1 max-w-xs">
                        <select
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            name="branch_id" id="branch_id">
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button id="filter-btn" type="button"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors shadow-sm">
                        <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M21 21L15 15M17 10C17 13.866 13.866 17 10 17C6.13401 17 3 13.866 3 10C3 6.13401 6.13401 3 10 3C13.866 3 17 6.13401 17 10Z"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Filter
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-8 pb-10">
            <table id="exam" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr class="text-white">
                        <th class="w-10">No</th>
                        <th>Tanggal Masuk</th>
                        <th>Nama Barang / Jasa</th>
                        <th>Nama User</th>
                        <th>Status Pengadaaan</th>
                        <th>Tanggal Proses</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>


    <script>
        let dataTable;

        $('#filter-btn').on('click', function() {
            const branchId = $('#branch_id').val();
            dataTable.ajax.url("{{ route('monitoring.procurements.data') }}?branch_id=" + branchId).load();
        });

        $(function() {
            dataTable = $('#exam').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ route('monitoring.procurements.data') }}",
                        data: function(d) {
                            d.branch_id = $('#branch_id').val();
                        }
                    },
                    dom: '<"top"Blf>rt<"bottom"ip>',
                    buttons: [{
                        extend: 'excelHtml5',
                        text: '<span class="flex gap-2 items-center"><img src="https://img.icons8.com/?size=100&id=11594&format=png&color=FFFFFF" alt="Excel" style="height:20px; margin-right:5px;"> Export ke Excel</span>',
                        title: 'Data Pengadaan',
                        exportOptions: {
                            columns: ':visible'
                        }
                    }],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'entry_date',
                            name: 'entry_date'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'user_name',
                            name: 'user_name',
                        },
                        {
                            data: 'status',
                            name: 'status',
                        },
                        {
                            data: 'process_date',
                            name: 'process_date',
                        },
                    ],
                    columnDefs: [{
                        targets: [0, 1, 4, 5, ],
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).addClass('text-center');
                        }
                    }, ]
                }).columns.adjust()
                .responsive.recalc();
        });
    </script>

    <style>
        .dt-button.buttons-excel.buttons-html5 {
            margin-right: 15px;
        }
    </style>

</x-app-layout>
