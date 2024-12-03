<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Bukti Peminjaman
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
                        <th>Kepala Bidang Operasi</th>
                        <th>Petugas Peminjaman</th>
                        <th>Fungsi Umum</th>
                        <th>Tanggal Peminjaman</th>
                        <th class="w-20">Detail Aset</th>
                        <th class="w-20">Status</th>
                        <th class="w-20">Dokumen</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="detail-loan" :show="false">
        <div class="p-5" x-data="{
            asset: null,
            dataTable: null,
            setAsset(data) {
                this.asset = data;
                this.initializeDataTable(data.id);
            },
            initializeDataTable(loanId) {
                if (this.dataTable) {
                    this.dataTable.destroy();
                }
        
                this.dataTable = $('#detail-loan').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    paging: false,
                    ajax: {
                        url: '{{ route('loans.assets.data') }}',
                        data: function(d) {
                            d.loan_id = loanId;
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nama_asset',
                            name: 'nama_asset',
                            orderable: false
                        },
                        {
                            data: 'duration',
                            name: 'duration',
                            orderable: false
                        },
                        {
                            data: 'notes',
                            name: 'notes',
                            orderable: false
                        }
                    ],
                });
            }
        }" @set-asset.window="setAsset($event.detail)"
            @hidden.window="this.dataTable.clear().draw();">
            <h5 class="font-semibold text-md">Detail Peminjaman</h5>
            <table id="detail-loan" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr class="text-white">
                        <th class="w-10">No</th>
                        <th>Nama Asset</th>
                        {{-- <th>Jumlah</th> --}}
                        <th>Lama Peminjaman</th>
                        <th>Catatan</th>
                    </tr>
                </thead>
            </table>
        </div>
    </x-modal>

    <script>
        let dataTable;

        $(function() {
            dataTable = $('#exam').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('loans.data') }}",
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
                            data: 'operation_head',
                            name: 'operation_head',
                            orderable: false
                        },
                        {
                            data: 'loan_officer',
                            name: 'loan_officer',
                            orderable: false
                        },
                        {
                            data: 'general_division',
                            name: 'general_division',
                            orderable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at',
                            orderable: false
                        },
                        {
                            data: 'detail',
                            name: 'detail',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'status',
                            name: 'status',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'document',
                            name: 'document',
                            orderable: false,
                            searchable: false
                        }
                    ],
                    columnDefs: [{
                            targets: [0, 6],
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-center');
                            }
                        },
                        {
                            targets: 7,
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('flex justify-center w-30');
                            }
                        }
                    ],
                }).columns.adjust()
                .responsive.recalc();
        });
    </script>

    <script>
        $(document).ready(function() {
            document.querySelectorAll('.select2').forEach(function(select) {
                $(select).select2();
            });
        });
    </script>

</x-app-layout>
