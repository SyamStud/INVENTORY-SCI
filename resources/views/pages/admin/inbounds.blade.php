<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Bukti Penerimaan Barang
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <div class="mt-8 pb-10">
            <table id="exam" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr class="text-white">
                        <th class="w-10">No</th>
                        <th>Nomor PO</th>
                        <th>Nomor BPG</th>
                        <th>No/Tgl Surat Pesanan</th>
                        <th class="w-20">Tanggal</th>
                        <th class="w-20">Detail Aset</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="detail-inbound" :show="false">
        <div class="p-5" x-data="{
            item: null,
            dataTable: null,
            setItem(data) {
                this.item = data;
                this.initializeDataTable(data.id);
            },
            initializeDataTable(inboundId) {
                if (this.dataTable) {
                    this.dataTable.destroy();
                }
        
                this.dataTable = $('#detail-inbound').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    paging: false,
                    ajax: {
                        url: '{{ route('inbounds.items.data') }}',
                        data: function(d) {
                            d.inbound_id = inboundId;
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
                            data: 'cost',
                            name: 'cost',
                            orderable: false,
                        },
                        {
                            data: 'total_cost',
                            name: 'total_cost',
                            orderable: false,
                        },
                    ],
                    columnDefs: [{
                            targets: [0, 2, 3, 4],
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-center');
                            }
                        },
                    ],
                });
            }
        }" @set-item.window="setItem($event.detail)"
            @hidden.window="this.dataTable.clear().draw();">
            <h5 class="font-semibold text-md">Detail pemasukan Barang</h5>
            <table id="detail-inbound" class="table table-striped nowrap" style="width:100%">
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
                    ajax: "{{ route('inbounds.data') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'po_number',
                            name: 'po_number',
                            orderable: false
                        },
                        {
                            data: 'bpg_number',
                            name: 'bpg_number',
                            orderable: false
                        },
                        {
                            data: 'order_note_number',
                            name: 'order_note_number',
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
                    ],
                    columnDefs: [{
                            targets: 0,
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-center');
                            }
                        },
                        {
                            targets: 5,
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
