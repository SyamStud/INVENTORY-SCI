<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Bukti Pengeluaran Barang
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <div class="mt-8 pb-10">
            <table id="exam" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr class="text-white">
                        <th class="w-10">No</th>
                        <th>Nomor Dokumen</th>
                        <th>Tujuan</th>
                        <th>Keperluan</th>
                        <th>No/Tgl Surat Permintaan</th>
                        <th>No/Tgl Surat Pengantar</th>
                        <th>Pengesah</th>
                        <th>Penganggung Jawab</th>
                        <th>Penerima</th>
                        <th class="w-20">Tanggal</th>
                        <th class="w-20">Detail Aset</th>
                        <th class="w-20">Status</th>
                        <th class="w-20">Dokumen</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="detail-outbound" :show="false">
        <div class="p-5" x-data="{
            item: null,
            dataTable: null,
            setItem(data) {
                this.item = data;
                this.initializeDataTable(data.id);
            },
            initializeDataTable(outboundId) {
                if (this.dataTable) {
                    this.dataTable.destroy();
                }
        
                this.dataTable = $('#detail-outbound').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    paging: false,
                    ajax: {
                        url: '{{ route('outbounds.items.data') }}',
                        data: function(d) {
                            d.outbound_id = outboundId;
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name',
                            orderable: false
                        },
                        {
                            data: 'quantity',
                            name: 'quantity',
                            orderable: false,
                        },
                        {
                            data: 'price',
                            name: 'price',
                            orderable: false,
                        },
                        {
                            data: 'total_price',
                            name: 'total_price',
                            orderable: false,
                        },
                    ],
                });
            }
        }" @set-item.window="setItem($event.detail)"
            @hidden.window="this.dataTable.clear().draw();">
            <h5 class="font-semibold text-md">Detail pengeluaran Barang</h5>
            <table id="detail-outbound" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr class="text-white">
                        <th class="w-10">No</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Harga</th>
                        <th>Total Harga</th>
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
                    ajax: "{{ route('outbounds.data') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'outbound_number',
                            name: 'outbound_number',
                            orderable: false
                        },
                        {
                            data: 'release_to',
                            name: 'release_to',
                            orderable: false
                        },
                        {
                            data: 'release_reason',
                            name: 'release_reason',
                            orderable: false
                        },
                        {
                            data: 'request_note_number',
                            name: 'request_note_number',
                            orderable: false
                        },
                        {
                            data: 'delivery_note_number',
                            name: 'delivery_note_number',
                            orderable: false
                        },
                        {
                            data: 'approved_by',
                            name: 'approved_by',
                            orderable: false
                        },
                        {
                            data: 'released_by',
                            name: 'released_by',
                            orderable: false
                        },
                        {
                            data: 'received_by',
                            name: 'received_by',
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
