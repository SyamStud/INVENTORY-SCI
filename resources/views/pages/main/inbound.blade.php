<x-app-layout>
    <x-slot name="nav">main</x-slot>

    <x-slot name="header">
        <div class="w-full flex justify-center md:justify-start">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Penerimaan Barang
            </h2>
        </div>
    </x-slot>

    <main class="px-10 mt-10 relative">
        <x-spinner></x-spinner>

        <div id="main-content">
            <form onsubmit="handleAddItem(event)">
                @csrf

                <input id="inbound_temp_id" type="hidden" name="inbound_temp_id" value="{{ $inbound->id ?? '' }}">

                <div class="md:flex gap-5">
                    <div class="mb-4 w-full">
                        <x-input-label for="po_number" :value="__('Nomor PO')" />
                        <x-text-input id="po_number" value="{{ $inbound->po_number ?? '' }}" class="block mt-1 w-full"
                            type="text" name="po_number" required autofocus />
                    </div>
                    <div class="mb-4 w-full">
                        <x-input-label for="bpg_number" :value="__('Nomor BPG')" />
                        <x-text-input id="bpg_number" class="block mt-1 w-full" type="text"
                            value="{{ $inbound->bpg_number ?? '' }}" name="bpg_number" required autofocus />
                    </div>
                    <div class="mb-4 w-full">
                        <x-input-label for="order_note_number" :value="__('No/Tgl. Surat Pesanan')" />
                        <x-text-input id="order_note_number" class="block mt-1 w-full" type="text"
                            value="{{ $inbound->order_note_number ?? '' }}" name="order_note_number" required
                            autofocus />
                    </div>
                    <div class="mb-4 w-full">
                        <x-input-label for="date" :value="__('Tanggal')" />
                        <x-text-input id="date" class="block mt-1 w-full" type="date"
                            value="{{ $inbound->date ?? '' }}" name="date" required />
                    </div>
                </div>

                <hr class="my-5">

                <div class="w-full md:flex items-center gap-4">
                    <div id="input_item" class="mb-4 w-full">
                        <x-input-label for="item_id" :value="__('Nama Barang')" />
                        <select class="w-full select2" name="item_id" x-data x-init="$nextTick(() => {
                            $('.select2').select2(); // Inisialisasi Select2
                        })">
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}">
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4 w-full md:w-52">
                        <x-input-label for="quantity" :value="__('Kuantitas')" />
                        <x-text-input id="quantity" class="block mt-1 w-full" type="number" name="quantity"
                            min="1" required autofocus />
                    </div>

                    <div class="mb-4 w-full md:w-96">
                        <x-input-label for="cost" :value="__('Harga Satuan')" />
                        <x-text-input id="cost" class="block mt-1 w-full" type="number" name="cost"
                            min="1" required autofocus />
                    </div>

                    <div class="md:flex gap-2 md:w-max">
                        <button type="submit"
                            class="w-full md:w-max bg-blue-600 px-4 py-2 rounded-md font-semibold text-white mt-2">Tambah
                            Barang</button>

                        <div id="inbound-action" class="md:flex gap-2 md:w-max">
                            <button type="button" x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'cancel-inbound')"
                                class="w-full md:w-max bg-red-700 px-4 py-2 rounded-md font-semibold text-white mt-2">Batalkan
                                Penerimaan</button>

                            <button type="button" x-data=""
                                x-on:click.prevent="$dispatch('open-modal', 'confirm-inbound')"
                                class="w-full md:w-max bg-green-700 px-4 py-2 rounded-md font-semibold text-white mt-2">Konfirmasi
                                Penerimaan</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="mt-8 pb-10">
                <table id="exam" class="table table-striped nowrap" style="width:100%">
                    <thead>
                        <tr class="text-white">
                            <th class="w-10">No</th>
                            <th>Nama Barang</th>
                            <th>Kuantitas</th>
                            <th>Harga Satuan</th>
                            <th>Subtotal</th>
                            <th class="w-40">Aksi</th>
                        </tr>
                    </thead>
                </table>
            </div>

            <x-modal name="edit-item" :show="false">
                <div class="p-5" x-data="{
                    item: null,
                    setItem(data) {
                        this.item = data;
                    }
                }" @set-item.window="setItem($event.detail)">
                    <h5 class="font-semibold text-md">Ubah Data Barang</h5>

                    <form class="mt-5" id="editItemForm" onsubmit="handleEditItem(event)">
                        @method('PUT')
                        @csrf

                        <input type="hidden" name="inbound_item_id" x-bind:value="item?.id">
                        <div class="mb-4">
                            <x-input-label for="edit_item_name" :value="__('Nama Barang')" />
                            <x-text-input id="edit_item_name" class="block mt-1 w-full bg-gray-200 text-gray-500"
                                type="text" name="item_name" disabled x-bind:value="item?.item.name" autofocus />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="edit_quantity" :value="__('Kuantitas')" />
                            <x-text-input id="edit_quantity" class="block mt-1 w-full" type="text"
                                name="quantity" min="1" required x-bind:value="item?.quantity" autofocus />
                        </div>
                        <div class="mb-4">
                            <x-input-label for="edit_cost" :value="__('Harga Satuan')" />
                            <x-text-input id="edit_cost" class="block mt-1 w-full" type="text" name="cost"
                                min="1" required x-bind:value="item?.cost" autofocus />
                        </div>

                        <div class="w-full flex justify-end">
                            <button type="submit"
                                class="justify-end flex items-center gap-1 px-4 py-2 bg-[#C07F00] rounded-md text-white font-medium text-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <x-modal name="delete-item" :show="false">
                <div class="p-5" x-data="{
                    item: null,
                    setItem(data) {
                        this.item = data;
                    }
                }" @set-item.window="setItem($event.detail)">
                    <h5 class="font-semibold text-md">Hapus Barang</h5>

                    <p class="mt-5">Apakah Anda yakin ingin menghapus barang <span class="font-bold text-red-600"
                            x-text="item?.item.name"></span>?</p>

                    <form class="mt-5" id="deleteItemForm" onsubmit="handleDeleteItem(event)">
                        @method('DELETE')
                        @csrf
                        <input type="hidden" name="inbound_item_id" x-bind:value="item?.id">

                        <div class="w-full flex justify-end">
                            <button type="submit"
                                class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                                Hapus Barang
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <x-modal name="cancel-inbound" :show="false">
                <div class="p-5">
                    <h5 class="font-semibold text-md">Batalkan Penerimaan</h5>

                    <p class="mt-5">Apakah Anda yakin ingin <span class="text-red-700">membatalkan</span>
                        penerimaan?
                    </p>

                    <form class="mt-5" id="cancelInboundForm" onsubmit="handleCancelInbound(event)">
                        @method('POST')
                        @csrf

                        <div class="w-full flex justify-end">
                            <button type="submit"
                                class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                                Batalkan Penerimaan
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>

            <x-modal name="confirm-inbound" :show="false">
                <div class="p-5">
                    <h5 class="font-semibold text-md">Konfirmasi Penerimaan</h5>

                    <p class="mt-5">Apakah Anda yakin ingin meyimpan penerimaan?</p>

                    <form class="mt-5" id="confirmInboundForm" onsubmit="handleConfirmInbound(event)">
                        @method('POST')
                        @csrf

                        <input id="in_number" type="hidden" name="inbound_number">

                        <div class="w-full flex justify-end">
                            <button type="submit"
                                class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                                Konfirmasi Penerimaan
                            </button>
                        </div>
                    </form>
                </div>
            </x-modal>
        </div>
    </main>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });

        checkInbound();

        let oldCode;

        function checkInbound() {
            $.ajax({
                url: '/inbounds/check',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.exist === false) {
                        $('#inbound-action').css('display', 'none');
                        $('.select2').select2();
                    } else {
                        if ($(window).width() < 768) {
                            $('#inbound-action').css('display', 'block');
                        } else {
                            $('#inbound-action').css('display', 'flex');
                        }
                    }

                    $('.select2').select2();
                },
                error: function(xhr, status, error) {
                    console.error('Terjadi kesalahan:', error);
                }
            });
        }

        let dataTable;

        $(function() {
            $('#loading-spinner').show();

            let isInitialLoad = true;

            dataTable = $('#exam').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ route('inbounds.temp.data') }}",
                        beforeSend: function() {
                            // Tampilkan spinner hanya saat initial load
                            if (isInitialLoad) {
                                $('#loading-spinner').show();
                            }
                        },
                        complete: function() {
                            // Sembunyikan spinner
                            $('#loading-spinner').hide();
                            // Set isInitialLoad menjadi false setelah load pertama
                            isInitialLoad = false;
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'item',
                            name: 'item'
                        },
                        {
                            data: 'quantity',
                            name: 'quantity'
                        },
                        {
                            data: 'cost',
                            name: 'cost'
                        },
                        {
                            data: 'subtotal',
                            name: 'subtotal'
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
                            targets: 5,
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('flex justify-center gap-2 w-max');
                            }
                        }
                    ]
                }).columns.adjust()
                .responsive.recalc();
        });

        function handleAddItem(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');
            console.log(formData);

            setLoading(submitButton, true, 'Tambah Barang');

            $.ajax({
                url: "{{ route('inbounds.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if ($(window).width() < 768) {
                        $('#inbound-action').css('display', 'block');
                    } else {
                        $('#inbound-action').css('display', 'flex');
                    }
                    $('.select2').select2();

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Barang berhasil ditambahkan'
                        });

                        dataTable.ajax.reload();
                        form.reset();

                        $('#inbound_temp_id').val(response.inbound.id);
                        $('#po_number').val(response.inbound.po_number);
                        $('#bpg_number').val(response.inbound.bpg_number);
                        $('#order_note_number').val(response.inbound.order_note_number);
                        $('#date').val(response.inbound.date);
                        $('#quantity').val('');
                        $('#cost').val('');
                    }
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan'
                    });

                    console.error(xhr);
                },
                complete: function() {
                    setLoading(submitButton, false, 'Tambah Barang');
                }
            });
        }

        function handleEditItem(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const itemId = formData.get('inbound_item_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/inbounds/${itemId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'edit-item'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Data barang berhasil diubah'
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Simpan Perubahan', 'edit');
                    }, 500);
                }
            });
        }

        function handleDeleteItem(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const itemId = formData.get('inbound_item_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Barang', 'delete');

            $.ajax({
                url: `/inbounds/${itemId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-item'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Satuan item berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Barang', 'delete');
                    }, 500);
                }
            });
        }

        function handleCancelInbound(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');
            const inboundId = $('#inbound_temp_id').val();

            setLoading(submitButton, true, 'Batalkan Penerimaan', 'delete');

            formData.append('id', inboundId);

            $.ajax({
                url: `/inbounds/cancel`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#inbound-action').css('display', 'none');
                    $('.select2').select2();

                    dataTable.ajax.reload();
                    $('#inbound_temp_id').val('');
                    $('#po_number').val('');
                    $('#bpg_number').val('');
                    $('#order_note_number').val('');
                    $('#date').val('');
                    $('#quantity').val('');
                    $('#cost').val('');

                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'cancel-inbound'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Batalkan Penerimaan', 'delete');
                    }, 500);
                }
            });
        }

        function handleConfirmInbound(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Konfirmasi Penerimaan');

            $.ajax({
                url: `/inbounds/confirm`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    checkInbound();

                    // Clear form fields
                    $('#inbound_temp_id').val('');
                    $('#po_number').val('');
                    $('#bpg_number').val('');
                    $('#order_note_number').val('');
                    $('#date').val('');
                    $('#quantity').val('');
                    $('#cost').val('');

                    // Close confirm-inbound modal
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'confirm-inbound'
                    }));

                    // Show success toast
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Konfirmasi Penerimaan');
                    }, 500);
                }
            });
        }

        function setLoading(button, isLoading, text, type = 'add') {
            if (isLoading) {
                button.prop('disabled', true);
                button.css('background-color', '#9CA3AF');
                button.html('Mohon Tunggu...');
            } else {
                button.prop('disabled', false);
                button.css('background-color', '#2563EB');
                button.html(text);
            }
        }
    </script>
</x-app-layout>
 