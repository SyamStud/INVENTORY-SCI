<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Aset Cabang Lain
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <div class="flex gap-2 w-full">
            <a href="{{ route('assets.index') }}"
                class="flex items-center gap-1 px-4 py-2 bg-[#31363F] rounded-md text-white font-medium text-sm">
                <img class="w-4" src="https://img.icons8.com/?size=100&id=39800&format=png&color=FFFFFF" alt="">
                Kembali
            </a>

            <form>
                <div class="flex items-center gap-2">
                    <label for="branch_id" class="ms-5 font-semibold">Pilih Kantor Cabang :</label>
                    <select class="border-gray-300 rounded-md" name="branch_id" id="branch_id">
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}"
                                {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>

                    <button id="filter-btn" type="button" class="bg-[#31363F] p-2 rounded-md">
                        <img width="25" height="25"
                            src="https://img.icons8.com/?size=100&id=83220&format=png&color=FFFFFF" alt="filter--v1" />
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-8 pb-10">
            <table id="exam" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr class="text-white">
                        <th class="w-10">No</th>
                        <th>Nomor Inventaris</th>
                        <th>Tag Number</th>
                        <th>Nama Aset</th>
                        <th>Merek</th>
                        <th>Nomor Seri</th>
                        <th>Warna</th>
                        <th>Ukuran</th>
                        <th>Kondisi</th>
                        <th>Status</th>
                        <th>No. Sertifikasi</th>
                        <th>Interval Kalibrasi</th>
                        <th>Tanggal Kalibrasi</th>
                        <th>Tanggal Kalibrasi Berakhir</th>
                        <th>Lembaga Kalibrasi</th>
                        <th>Jenis Kalibrasi</th>
                        <th>Range / Kapasitas</th>
                        <th>Faktor Koreksi</th>
                        <th>Signifikasi</th>
                        <th>Kalibrasi</th>
                        <th>Pengadaan</th>
                        <th>Foto</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>


    <script>
        let dataTable;

        $('#filter-btn').on('click', function() {
            const branchId = $('#branch_id').val();
            dataTable.ajax.url("{{ route('assets.other.data') }}?branch_id=" + branchId).load();
        });

        $(function() {
            dataTable = $('#exam').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ route('assets.other.data') }}",
                        data: function(d) {
                            d.branch_id = $('#branch_id').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'inventory_number',
                            name: 'inventory_number',
                            orderable: false
                        },
                        {
                            data: 'tag_number',
                            name: 'tag_number',
                            orderable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'brand',
                            name: 'brand'
                        },
                        {
                            data: 'serial_number',
                            name: 'serial_number',
                            orderable: false
                        },
                        {
                            data: 'color',
                            name: 'color'
                        },
                        {
                            data: 'size',
                            name: 'size'
                        },
                        {
                            data: 'condition',
                            name: 'condition'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'calibration_number',
                            name: 'calibration_number'
                        },
                        {
                            data: 'calibration_interval',
                            name: 'calibration_interval'
                        },
                        {
                            data: 'calibration_start_date',
                            name: 'calibration_start_date'
                        },
                        {
                            data: 'calibration_due_date',
                            name: 'calibration_due_date',
                        },
                        {
                            data: 'calibration_institution',
                            name: 'calibration_institution'
                        },
                        {
                            data: 'calibration_type',
                            name: 'calibration_type'
                        },
                        {
                            data: 'range',
                            name: 'range'
                        },
                        {
                            data: 'correction_factor',
                            name: 'correction_factor'
                        },
                        {
                            data: 'significance',
                            name: 'significance'
                        },
                        {
                            data: 'calibration',
                            name: 'calibration',
                            orderable: false
                        },
                        {
                            data: 'procurement',
                            name: 'procurement',
                            orderable: false
                        },
                        {
                            data: 'photo',
                            name: 'photo',
                            orderable: false
                        },
                    ],
                    columnDefs: [{
                        targets: 0,
                        createdCell: function(td, cellData, rowData, row, col) {
                            $(td).addClass('text-center');
                        }
                    }, ]
                }).columns.adjust()
                .responsive.recalc();
        });
    </script>

</x-app-layout>
