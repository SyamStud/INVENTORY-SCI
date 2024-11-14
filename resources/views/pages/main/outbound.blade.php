<x-app-layout>
    <x-slot name="nav">main</x-slot>

    <x-slot name="header">
        <div class="w-full flex justify-center md:justify-start">
            <h2 id="code" class="font-semibold text-xl text-gray-800 leading-tight">
            </h2>
            {{-- <h2 id="date" class="font-semibold text-xl text-gray-800 leading-tight">
            </h2> --}}
        </div>
    </x-slot>

    <main class="px-10 mt-10">
        <form onsubmit="handleAddItem(event)">
            @csrf

            <x-text-input id="outbound_number" class="block mt-1 w-full" type="hidden" name="outbound_number" required
                autofocus />

            <div class="md:flex gap-5">
                <div class="mb-4 w-full">
                    <x-input-label for="release_to" :value="__('Dikeluarkan Barang Kepada')" />
                    <x-text-input id="release_to" value="{{ $outbound->release_to ?? '' }}" class="block mt-1 w-full"
                        type="text" name="release_to" required autofocus />
                </div>
                <div class="mb-4 w-full">
                    <x-input-label for="release_reason" :value="__('Untuk Keperluan')" />
                    <x-text-input id="release_reason" class="block mt-1 w-full" type="text"
                        value="{{ $outbound->release_reason ?? '' }}" name="release_reason" required autofocus />
                </div>
                <div class="mb-4 w-full">
                    <x-input-label for="request_note_number" :value="__('No/Tgl. Surat Permintaan')" />
                    <x-text-input id="request_note_number" class="block mt-1 w-full" type="text"
                        value="{{ $outbound->request_note_number ?? '' }}" name="request_note_number" required
                        autofocus />
                </div>
                <div class="mb-4 w-full">
                    <x-input-label for="delivery_note_number" :value="__('No/Tgl. Surat Pengantar')" />
                    <x-text-input id="delivery_note_number" class="block mt-1 w-full" type="text"
                        value="{{ $outbound->delivery_note_number ?? '' }}" name="delivery_note_number" required
                        autofocus />
                </div>
                <div class="mb-4 w-full">
                    <x-input-label for="received_by" :value="__('Nama Penerima')" />
                    <x-text-input id="received_by" class="block mt-1 w-full" type="text"
                        value="{{ $outbound->received_by ?? '' }}" name="received_by" required autofocus />
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
                    <x-text-input id="quantity" class="block mt-1 w-full" type="number" name="quantity" required min="1"
                        autofocus />
                </div>

                <div class="md:flex gap-2 md:w-max">
                    <button type="submit"
                        class="w-full md:w-max bg-blue-600 px-4 py-2 rounded-md font-semibold text-white mt-2">Tambah
                        Barang</button>

                    <div id="outbound-action" class="md:flex gap-2 md:w-max">
                        <button type="button" x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'cancel-outbound')"
                            class="w-full md:w-max bg-red-700 px-4 py-2 rounded-md font-semibold text-white mt-2">Batalkan
                            Pengeluaran</button>

                        <button type="button" x-data=""
                            x-on:click.prevent="$dispatch('open-modal', 'confirm-outbound')"
                            class="w-full md:w-max bg-green-700 px-4 py-2 rounded-md font-semibold text-white mt-2">Konfirmasi
                            Pengeluaran</button>
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

                    <input type="hidden" name="outbound_item_id" x-bind:value="item?.id">
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
                    <input type="hidden" name="outbound_item_id" x-bind:value="item?.id">

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                            Hapus Barang
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <x-modal name="cancel-outbound" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Batalkan Pengeluaran</h5>

                <p class="mt-5">Apakah Anda yakin ingin <span class="text-red-700">membatalkan</span> pengeluaran?
                </p>

                <form class="mt-5" id="cancelOutboundForm" onsubmit="handleCancelOutbound(event)">
                    @method('POST')
                    @csrf

                    <input id="in_number" type="hidden" name="outbound_number">

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                            Batalkan Pengeluaran
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <x-modal name="confirm-outbound" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Konfirmasi Pengeluaran</h5>

                <form class="mt-5" id="confirmOutboundForm" onsubmit="handleConfirmOutbound(event)">
                    @method('POST')
                    @csrf

                    <input id="in_number" type="hidden" name="outbound_number">

                    <div class="mb-4 w-full">
                        <x-input-label for="approved_by" :value="__('Disetujui Oleh')" />
                        <select class="w-full select2" name="approved_by" x-data x-init="$nextTick(() => {
                            $('.select2').select2(); // Inisialisasi Select2
                        })">
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4 w-full">
                        <x-input-label for="released_by" :value="__('Penanggung Jawab')" />
                        <select class="w-full select2" name="released_by" x-data x-init="$nextTick(() => {
                            $('.select2').select2(); // Inisialisasi Select2
                        })">
                            @foreach ($employees as $employee)
                                <option value="{{ $employee->id }}">
                                    {{ $employee->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4 w-full">
                        <x-input-label class="mb-2" for="photos" :value="__('Unggah Foto')" />

                        <!-- Custom Input File -->
                        <label class="block w-full cursor-pointer">
                            <div id="input-photo"
                                class="flex items-center justify-between p-3 border border-dashed rounded-lg bg-gray-50 hover:bg-gray-100">
                                <span id="file-label" class="text-sm text-gray-600">
                                    Klik untuk memilih foto (maksimal 2 foto)
                                </span>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-400">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M3 16.5V7.5A2.5 2.5 0 015.5 5h13a2.5 2.5 0 012.5 2.5v9a2.5 2.5 0 01-2.5 2.5h-13A2.5 2.5 0 013 16.5zM10 9.5h4M8 13.5h8m-5 4v.5m0-4.5h-.5" />
                                </svg>
                            </div>
                            <input id="photos" type="file" name="photos[]" class="hidden" multiple
                                onchange="updateFileList()" />
                        </label>

                        <!-- File List -->
                        <div id="file-list" class="mt-2 text-sm text-gray-700 space-y-1">
                            <!-- Nama file akan ditampilkan di sini -->
                        </div>
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Konfirmasi Pengeluaran
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <x-modal name="receipt-letter" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Konfirmasi Cetak Bukti Pengeluaran</h5>

                <p class="mt-5">Apakah anda ingin mencetak bukti pengeluaran ?</p>
                <p class="mt-5 italic text-sm text-gray-500">* Bukti pengeluaran masih dapat dicetak lain waktu melalui
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
                            Cetak Bukti Pengeluaran
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>
    </main>

    <script>
        $(document).ready(function() {
            $('.select2').select2();
        });

        generateCode();
        checkOutbound();

        let oldCode;

        function checkOutbound() {
            $.ajax({
                url: '/outbounds/check',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.exist === false) {
                        $('#outbound-action').css('display', 'none');
                        $('.select2').select2();
                    } else {
                        if ($(window).width() < 768) {
                            $('#outbound-action').css('display', 'block');
                        } else {
                            $('#outbound-action').css('display', 'flex');
                        }
                    }

                    $('.select2').select2();
                },
                error: function(xhr, status, error) {
                    console.error('Terjadi kesalahan:', error);
                }
            });
        }

        function generateCode() {
            $.ajax({
                url: '/outbounds/generate-code',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    $('#code').text("Nomor : " + response.code);
                    $('#outbound_number').val(response.code);
                    $('#in_number').val(response.code);
                },
                error: function(xhr, status, error) {
                    console.error('Terjadi kesalahan:', xhr);
                }
            });
        }

        function updateFileList() {
            const input = document.getElementById('photos');
            const fileList = document.getElementById('file-list');
            const label = document.getElementById('file-label');
            const inputCustom = document.getElementById('input-photo');

            // Bersihkan daftar file sebelumnya
            fileList.innerHTML = '';

            // Jika file yang dipilih lebih dari 2
            if (input.files.length > 2) {
                label.textContent = 'Maksimal 2 foto';
                label.style.color = 'red';
                input.value = '';
                inputCustom.style.border = '1px solid red';
                inputCustom.style.backgroundColor = '#fef2f2';
                return;
            } else {
                label.style.color = '#374151';
                inputCustom.style.border = '1px solid #D1D5DB';
                inputCustom.style.backgroundColor = '#F3F4F6';
            }

            // Jika tidak ada file yang dipilih
            if (input.files.length === 0) {
                label.textContent = 'Klik untuk memilih gambar (maksimal 2 foto)';
                return;
            }

            // Update label dengan jumlah file
            label.textContent = `${input.files.length} file dipilih`;

            // Tambahkan nama file ke daftar
            Array.from(input.files).forEach(file => {
                const listItem = document.createElement('div');
                listItem.textContent = `📄 ${file.name}`;
                fileList.appendChild(listItem);
            });
        }


        let dataTable;

        $(function() {
            dataTable = $('#exam').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('outbounds.temp.data') }}",
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
                            data: 'price',
                            name: 'price'
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
                            targets: [0, 2, 3, 4],
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

            setLoading(submitButton, true, 'Tambah Barang', 'add-item');

            $.ajax({
                url: "{{ route('outbounds.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {


                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        if ($(window).width() < 768) {
                            $('#outbound-action').css('display', 'block');
                        } else {
                            $('#outbound-action').css('display', 'flex');
                        }

                        $('.select2').select2();

                        Toast.fire({
                            icon: 'success',
                            title: 'Barang berhasil ditambahkan'
                        });

                        dataTable.ajax.reload();
                        form.reset();

                        console.log(response.outbound);

                        $('#release_to').val(response.outbound.release_to);
                        $('#release_reason').val(response.outbound.release_reason);
                        $('#request_note_number').val(response.outbound.request_note_number);
                        $('#delivery_note_number').val(response.outbound.delivery_note_number);
                        $('#received_by').val(response.outbound.received_by);

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
                    setLoading(submitButton, false, 'Tambah Barang', 'add-item');
                }
            });
        }

        function handleEditItem(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const itemId = formData.get('outbound_item_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/outbounds/${itemId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        dataTable.ajax.reload();

                        Toast.fire({
                            icon: 'success',
                            title: 'Data barang berhasil diubah'
                        });
                    }

                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'edit-item'
                    }));
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
            const itemId = formData.get('outbound_item_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Barang', 'delete');

            $.ajax({
                url: `/outbounds/${itemId}`,
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

        function handleCancelOutbound(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Batalkan Pengeluaran', 'delete');

            $.ajax({
                url: `/outbounds/cancel`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#outbound-action').css('display', 'none');
                    $('.select2').select2();


                    dataTable.ajax.reload();
                    generateCode();

                    $('#release_to').val('');
                    $('#release_reason').val('');
                    $('#request_note_number').val('');
                    $('#delivery_note_number').val('');
                    $('#received_by').val('');
                    $('#quantity').val('');

                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'cancel-outbound'
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
                        setLoading(submitButton, false, 'Batalkan Pengeluaran', 'delete');
                    }, 500);
                }
            });
        }

        function handleConfirmOutbound(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Konfirmasi Pengeluaran');

            $.ajax({
                url: `/outbounds/confirm`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    generateCode();
                    checkOutbound();

                    // Clear form fields
                    $('#release_to').val('');
                    $('#release_reason').val('');
                    $('#request_note_number').val('');
                    $('#delivery_note_number').val('');
                    $('#received_by').val('');

                    // Close confirm-outbound modal
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'confirm-outbound'
                    }));

                    // Show success toast
                    Toast.fire({
                        icon: 'success',
                        title: response.message
                    });

                    // Open receipt-letter modal
                    dispatchEvent(new CustomEvent('open-modal', {
                        detail: 'receipt-letter'
                    }));

                    const receiptForm = $('#receiptForm');

                    receiptForm.off('submit').on('submit', function(e) {
                        e.preventDefault();
                        console.log(response.outbound_id);
                        window.location.href = `/outbounds/receipt/${response.outbound_id}`;
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Konfirmasi Pengeluaran');
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
                if (type === 'add') {
                    button.css('background-color', '#15803D');
                } else if (type === 'edit') {
                    button.css('background-color', '#C07F00');
                } else if (type === 'delete') {
                    button.css('background-color', '#b91C1C');
                } else {
                    button.css('background-color', '#2563EB');
                }
                button.html(text);
            }
        }
    </script>


</x-app-layout>
