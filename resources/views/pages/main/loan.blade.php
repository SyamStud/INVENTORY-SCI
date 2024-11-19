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

            <x-text-input id="loan_number" class="block mt-1 w-full" type="hidden" name="loan_number" required
                autofocus />

            <div class="md:flex gap-5">
                <div class="mb-4 w-full">
                    <x-input-label for="customer_name" :value="__('Nama Pelanggan')" />
                    <x-text-input id="customer_name" value="{{ $loan->customer_name ?? '' }}" class="block mt-1 w-full"
                        type="text" name="customer_name" required autofocus />
                </div>

                <div id="loan-action" class="md:flex gap-2 md:w-max mt-4">
                    <button type="button" x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'cancel-loan')"
                        class="w-full md:w-max h-max bg-red-700 px-4 py-2 rounded-md font-semibold text-white mt-2">Batalkan
                        Peminjaman</button>

                    <button type="button" x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-loan')"
                        class="w-full md:w-max h-max bg-green-700 px-4 py-2 rounded-md font-semibold text-white mt-2">Konfirmasi
                        Peminjaman</button>
                </div>
            </div>

            <hr class="my-5">

            <div class="w-full md:flex items-center gap-4">
                <div id="input_asset" class="mb-4 w-full">
                    <x-input-label for="asset_id" :value="__('Nama Aset')" />
                    <select class="w-full select2" name="asset_id" x-data x-init="$nextTick(() => {
                        $('.select2').select2();
                    })">
                        @foreach ($assets as $asset)
                            <option value="{{ $asset->id }}">
                                {{ $asset->name . " || " . $asset->serial_number }}
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
                    <x-input-label for="duration" :value="__('Durasi (Hari)')" />
                    <x-text-input id="duration" class="block mt-1 w-full" type="number" name="duration" required
                        autofocus />
                </div>

                <div class="mb-4 w-full md:w-96">
                    <x-input-label for="notes" :value="__('Keterangan')" />
                    <x-text-input id="notes" class="block mt-1 w-full" type="text" name="notes" required
                        autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="loan_check" :value="__('Kondisi')" />
                    <div class="flex items-center space-x-4 mt-1">
                        <!-- Checkbox Baik -->
                        <label class="inline-flex items-center">
                            <input type="radio" name="loan_check" value="baik" class="form-radio text-indigo-600"
                                x-bind:checked="item?.loan_check === 'baik'" />
                            <span class="ml-2">{{ __('Baik') }}</span>
                        </label>

                        <!-- Checkbox Rusak -->
                        <label class="inline-flex items-center">
                            <input type="radio" name="loan_check" value="rusak" class="form-radio text-indigo-600"
                                x-bind:checked="item?.loan_check === 'rusak'" />
                            <span class="ml-2">{{ __('Rusak') }}</span>
                        </label>
                    </div>
                </div>


                <div class="md:flex gap-2 md:w-max">
                    <button type="submit"
                        class="w-full md:w-max bg-blue-600 px-4 py-2 rounded-md font-semibold text-white mt-2">Tambah
                        Aset</button>
                </div>
            </div>
        </form>

        <div class="mt-8 pb-10">
            <table id="exam" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr class="text-white">
                        <th class="w-10">No</th>
                        <th>Tag Number</th>
                        <th>Nama Aset</th>
                        <th>Merek</th>
                        <th>Serial Number</th>
                        <th>Kuantitas</th>
                        <th>Durasi</th>
                        <th>Kondisi</th>
                        <th>Catatan</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>

        <x-modal name="edit-asset" :show="false">
            <div class="p-5" x-data="{
                asset: null,
                setAsset(data) {
                    this.asset = data;
                }
            }" @set-asset.window="setAsset($event.detail)">
                <h5 class="font-semibold text-md">Ubah Data Aset</h5>

                <form class="mt-5" id="editAssetForm" onsubmit="handleEditAsset(event)">
                    @method('PUT')
                    @csrf

                    <input type="hidden" name="loan_asset_id" x-bind:value="asset?.id">
                    <div class="mb-4">
                        <x-input-label for="edit_asset_name" :value="__('Nama Aset')" />
                        <x-text-input id="edit_asset_name" class="block mt-1 w-full bg-gray-200 text-gray-500"
                            type="text" name="asset_name" disabled x-bind:value="asset?.asset.name" autofocus />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="edit_quantity" :value="__('Kuantitas')" />
                        <x-text-input id="edit_quantity" class="block mt-1 w-full" type="number" name="quantity"
                            required x-bind:value="asset?.quantity" autofocus />
                    </div>
                    <div class="mb-4">
                        <x-input-label for="edit_duration" :value="__('Durasi')" />
                        <x-text-input id="edit_duration" class="block mt-1 w-full" type="number" name="duration"
                            required autofocus x-bind:value="asset?.duration" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="notes" :value="__('Keterangan')" />
                        <x-text-input id="notes" class="block mt-1 w-full" type="text" name="notes"
                            required autofocus x-bind:value="asset?.notes" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="loan_check" :value="__('Kondisi')" />
                        <div class="flex items-center space-x-4 mt-1">
                            <!-- Checkbox Baik -->
                            <label class="inline-flex items-center">
                                <input type="radio" name="loan_check" value="baik"
                                    class="form-radio text-indigo-600"
                                    x-bind:checked="asset?.loan_check === 'baik'" />
                                <span class="ml-2">{{ __('Baik') }}</span>
                            </label>

                            <!-- Checkbox Rusak -->
                            <label class="inline-flex items-center">
                                <input type="radio" name="loan_check" value="rusak"
                                    class="form-radio text-indigo-600"
                                    x-bind:checked="asset?.loan_check === 'rusak'" />
                                <span class="ml-2">{{ __('Rusak') }}</span>
                            </label>
                        </div>
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex assets-center gap-1 px-4 py-2 bg-[#C07F00] rounded-md text-white font-medium text-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <x-modal name="delete-asset" :show="false">
            <div class="p-5" x-data="{
                asset: null,
                setAsset(data) {
                    this.asset = data;
                }
            }" @set-asset.window="setAsset($event.detail)">
                <h5 class="font-semibold text-md">Hapus Aset</h5>

                <p class="mt-5">Apakah Anda yakin ingin menghapus aset <span class="font-bold text-red-600"
                        x-text="asset?.asset.name"></span>?</p>

                <form class="mt-5" id="deleteAssetForm" onsubmit="handleDeleteAsset(event)">
                    @method('DELETE')
                    @csrf
                    <input type="hidden" name="loan_asset_id" x-bind:value="asset?.id">

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex assets-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                            Batalkan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <x-modal name="cancel-loan" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Batalkan Peminjaman</h5>

                <p class="mt-5">Apakah Anda yakin ingin <span class="text-red-700">membatalkan</span> peminjaman?
                </p>

                <form class="mt-5" id="cancelLoanForm" onsubmit="handleCancelLoan(event)">
                    @method('POST')
                    @csrf

                    <input id="in_number" type="hidden" name="loan_number">

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                            Batalkan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <x-modal name="confirm-loan" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Konfirmasi Peminjaman</h5>

                <form class="mt-5" id="confirmLoanForm" onsubmit="handleConfirmLoan(event)">
                    @method('POST')
                    @csrf

                    <input id="in_number" type="hidden" name="loan_number">

                    <div class="mb-4 w-full">
                        <x-input-label for="operation_head" :value="__('Kepala Bidang Operasi')" />
                        <select class="w-full select2" name="operation_head" x-data x-init="$nextTick(() => {
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
                        <x-input-label for="loan_officer" :value="__('Petugas Peminjam')" />
                        <select class="w-full select2" name="loan_officer" x-data x-init="$nextTick(() => {
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
                        <x-input-label for="general_division" :value="__('Fungsi Umum')" />
                        <select class="w-full select2" name="general_division" x-data x-init="$nextTick(() => {
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
                            Konfirmasi Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <x-modal name="receipt-letter" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Konfirmasi Cetak Bukti Peminjaman</h5>

                <p class="mt-5">Apakah anda ingin mencetak bukti peminjaman ?</p>
                <p class="mt-5 italic text-sm text-gray-500">* Bukti peminjaman masih dapat dilihat lain waktu melalui
                    dashboard</p>

                <form class="mt-5" id="receiptForm">
                    <div class="w-full flex justify-end gap-2">
                        <button type="button" x-on:click.prevent="$dispatch('close-modal', 'receipt-letter')"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-transparent border-2 border-gray-300 shadow-sm rounded-md font-medium text-sm">
                            Tutup
                        </button>

                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Preview Dokumen
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
        checkLoan();

        let oldCode;

        function checkLoan() {
            $.ajax({
                url: '/loans/check',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.exist === false) {
                        $('#loan-action').css('display', 'none');
                        $('.select2').select2();
                    } else {
                        if ($(window).width() < 768) {
                            $('#loan-action').css('display', 'block');
                        } else {
                            $('#loan-action').css('display', 'flex');
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
                url: '/loans/generate-code',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    console.log(response);
                    $('#code').text("Nomor : " + response.code);
                    $('#loan_number').val(response.code);
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
                    ajax: "{{ route('loans.temp.data') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'tag_number',
                            name: 'tag_number'
                        },
                        {
                            data: 'asset',
                            name: 'asset'
                        },
                        {
                            data: 'brand',
                            name: 'brand'
                        },
                        {
                            data: 'serial_number',
                            name: 'serial_number'
                        },
                        {
                            data: 'quantity',
                            name: 'quantity'
                        },
                        {
                            data: 'duration',
                            name: 'duration'
                        },
                        {
                            data: 'loan_check',
                            name: 'loan_check'
                        },
                        {
                            data: 'notes',
                            name: 'notes'
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
                            targets: 9,
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

            setLoading(submitButton, true, 'Tambah Aset');

            $.ajax({
                url: "{{ route('loans.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if ($(window).width() < 768) {
                        $('#loan-action').css('display', 'block');
                    } else {
                        $('#loan-action').css('display', 'flex');
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
                            title: 'Aset berhasil ditambahkan'
                        });

                        dataTable.ajax.reload();
                        form.reset();

                        console.log(response.loan);

                        $('#customer_name').val(response.loan.customer_name);
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
                    setLoading(submitButton, false, 'Tambah Aset');
                }
            });
        }

        function handleEditAsset(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const assetId = formData.get('loan_asset_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/loans/${assetId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'edit-asset'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Data aset berhasil diubah'
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

        function handleDeleteAsset(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const assetId = formData.get('loan_asset_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Aset', 'delete');

            $.ajax({
                url: `/loans/${assetId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-asset'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Asset berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Aset', 'delete');
                    }, 500);
                }
            });
        }

        function handleCancelLoan(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Batalkan Peminjaman', 'delete');

            $.ajax({
                url: `/loans/cancel`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#loan-action').css('display', 'none');
                    $('.select2').select2();


                    dataTable.ajax.reload();
                    generateCode();

                    $('#customer_name').val('');
                    $('#order_note_number').val('');
                    $('#contract_note_number').val('');
                    $('#delivery_note_number').val('');

                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'cancel-loan'
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
                        setLoading(submitButton, false, 'Batalkan Peminjaman', 'delete');
                    }, 500);
                }
            });
        }

        function handleConfirmLoan(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Konfirmasi Peminjaman');

            $.ajax({
                url: `/loans/confirm`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    generateCode();
                    checkLoan();

                    // Clear form fields
                    $('#customer_name').val('');

                    // Close confirm-loan modal
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'confirm-loan'
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
                        console.log(response.document);
                        window.location.href = `/storage/${response.document}`;
                    });
                },
                error: function(xhr) {
                    console.error(xhr);
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Konfirmasi Peminjaman');
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
    </script>


</x-app-layout>
