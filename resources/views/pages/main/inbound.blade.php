<x-app-layout>
    <x-slot name="nav">main</x-slot>

    <x-slot name="header">
        <div class="w-full flex justify-center md:justify-start">
            <h2 id="code" class="font-semibold text-xl text-gray-800 leading-tight">
            </h2>
        </div>
    </x-slot>

    <main class="px-10 mt-10">
        <form onsubmit="handleAddItem(event)">
            @csrf

            <x-text-input id="inbound_number" class="block mt-1 w-full" type="hidden" name="inbound_number" required
                autofocus />

            <div class="md:flex gap-5">
                <div class="mb-4 w-full">
                    <x-input-label for="received_from" :value="__('Diterima Dari')" />
                    <x-text-input id="received_from" value="{{ $inbound->received_from ?? '' }}"
                        class="block mt-1 w-full" type="text" name="received_from" required autofocus />
                </div>
                <div class="mb-4 w-full">
                    <x-input-label for="order_note_number" :value="__('No/Tgl. Surat Pesanan')" />
                    <x-text-input id="order_note_number" class="block mt-1 w-full" type="text"
                        value="{{ $inbound->order_note_number ?? '' }}" name="order_note_number" required autofocus />
                </div>
                <div class="mb-4 w-full">
                    <x-input-label for="contract_note_number" :value="__('No/Tgl. Kontrak')" />
                    <x-text-input id="contract_note_number" class="block mt-1 w-full" type="text"
                        value="{{ $inbound->contract_note_number ?? '' }}" name="contract_note_number" required
                        autofocus />
                </div>
                <div class="mb-4 w-full">
                    <x-input-label for="delivery_note_number" :value="__('No/Tgl. Surat Pengantar')" />
                    <x-text-input id="delivery_note_number" class="block mt-1 w-full" type="text"
                        value="{{ $inbound->delivery_note_number ?? '' }}" name="delivery_note_number" required
                        autofocus />
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
                    <x-text-input id="quantity" class="block mt-1 w-full" type="number" name="quantity" required
                        autofocus />
                </div>

                <div class="mb-4 w-full md:w-96">
                    <x-input-label for="cost" :value="__('Harga Satuan')" />
                    <x-text-input id="cost" class="block mt-1 w-full" type="number" name="cost" required
                        autofocus />
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
                        <x-text-input id="edit_quantity" class="block mt-1 w-full" type="text" name="quantity"
                            required x-bind:value="item?.quantity" autofocus />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="edit_cost" :value="__('Harga Satuan')" />
                        <x-text-input id="edit_cost" class="block mt-1 w-full" type="text" name="cost"
                            required x-bind:value="item?.cost" autofocus />
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
                            Batalkan Penerimaan
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <x-modal name="cancel-inbound" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Batalkan Penerimaan</h5>

                <p class="mt-5">Apakah Anda yakin ingin <span class="text-red-700">membatalkan</span> penerimaan?
                </p>

                <form class="mt-5" id="cancelInboundForm" onsubmit="handleCancelInbound(event)">
                    @method('POST')
                    @csrf

                    <input id="in_number" type="hidden" name="inbound_number">

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

        {{-- <x-modal name="receipt-letter" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Konfirmasi Cetak Bukti Penerimaan</h5>

                <p class="mt-5">Apakah anda ingin mencetak bukti penerimaan ?</p>
                <p class="mt-5 italic text-sm text-gray-500">* Bukti penerimaan masih dapat dicetak lain waktu melalui
                    dashboard</p>

                <form class="mt-5" id="receiptForm">
                    <div class="w-full flex justify-end gap-2">
                        <button type="button"
                            onclick="dispatchEvent(new CustomEvent('close-modal', {detail: 'receipt-letter'}))"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-transparent border-2 border-gray-300 shadow-sm rounded-md font-medium text-sm">
                            Tutup
                        </button>

                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Cetak Bukti Penerimaan
                        </button>
                    </div>
                </form>
            </div>
        </x-modal> --}}
    </main>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });

        generateCode();
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

        // setInterval(() => {
        //     $.ajax({
        //         url: '/fetch-date',
        //         method: 'GET',
        //         dataType: 'json',
        //         success: function(response) {
        //             $('#date').text(response.date);
        //         },
        //         error: function(xhr, status, error) {
        //             console.error('Terjadi kesalahan:', error);
        //         }
        //     });
        // }, 1000);


        function generateCode() {
            $.ajax({
                url: '/inbounds/generate-code',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    $('#code').text("Nomor : " + response.code);
                    $('#inbound_number').val(response.code);
                    $('#in_number').val(response.code);
                },
                error: function(xhr, status, error) {
                    console.error('Terjadi kesalahan:', xhr);
                }
            });
        }

        let dataTable;

        $(function() {
            dataTable = $('#exam').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('inbounds.temp.data') }}",
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

                        console.log(response.inbound);

                        $('#received_from').val(response.inbound.received_from);
                        $('#order_note_number').val(response.inbound.order_note_number);
                        $('#contract_note_number').val(response.inbound.contract_note_number);
                        $('#delivery_note_number').val(response.inbound.delivery_note_number);

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

            setLoading(submitButton, true, 'Batalkan Penerimaan', 'delete');

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
                    generateCode();

                    $('#received_from').val('');
                    $('#order_note_number').val('');
                    $('#contract_note_number').val('');
                    $('#delivery_note_number').val('');

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
                    generateCode();
                    checkInbound();

                    // Clear form fields
                    $('#received_from').val('');
                    $('#order_note_number').val('');
                    $('#contract_note_number').val('');
                    $('#delivery_note_number').val('');

                    // Close confirm-inbound modal
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'confirm-inbound'
                    }));

                    // Show success toast
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });

                    // // Open receipt-letter modal
                    // dispatchEvent(new CustomEvent('open-modal', {
                    //     detail: 'receipt-letter'
                    // }));

                    // const receiptForm = $('#receiptForm');

                    // receiptForm.off('submit').on('submit', function(e) {
                    //     e.preventDefault();
                    //     console.log(response.inbound_id);
                    //     window.location.href = `/inbounds/receipt/${response.inbound_id}`;
                    // });
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
