<x-app-layout>
    <x-slot name="nav">main</x-slot>

    <x-slot name="header">
        <div class="w-full flex justify-center md:justify-start">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Pengembalian Aset
            </h2>
        </div>
    </x-slot>

    <main class="px-10 mt-10 relative">

        <form onsubmit="handleSearchReturn(event)">
            @csrf

            <div class="md:flex gap-5">
                <div class="mb-4 w-full">
                    <x-input-label for="loan_number" :value="__('Nomor Peminjaman')" />
                    <x-text-input id="loan_number" class="block mt-1 w-full" type="text" name="loan_number" required
                        autofocus />
                </div>

                <div class="md:flex gap-2 md:w-max mt-4">
                    <button type="submit"
                        class="w-full md:w-max h-max bg-blue-600 px-4 py-2 rounded-md font-semibold text-white mt-2">Cari
                        Data Peminjaman</button>
                </div>
            </div>

            <hr class="my-5">
        </form>

        <form onsubmit="handleConfirmReturn(event)">
            @method('POST')
            @csrf

            <div class="hidden" id="return-action">
                <div class="flex w-full justify-between items-center">
                    <h5 id="customer_name" class="text-lg font-semibold">Nama Pelanggan : </h5>
                    <button type="submit"
                        class="w-full md:w-max h-max bg-green-700 px-4 py-2 rounded-md font-semibold text-white mt-2">Konfirmasi
                        Pengembalian</button>
                </div>
            </div>
            <div class="mt-8 pb-10">
                <table id="exam" class="table table-striped nowrap" style="width:100%">
                    <thead>
                        <tr class="text-white">
                            <th class="w-10">No</th>
                            <th>Tag Number</th>
                            <th>Nama Aset</th>
                            <th>Merek</th>
                            <th>Serial Number</th>
                            <th class="w-40">Kondisi Peminjaman</th>
                            <th class="w-40">Kondisi Pengembalian</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </form>

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

                    <input type="hidden" name="return_asset_id" x-bind:value="asset?.id">
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
                        <x-text-input id="notes" class="block mt-1 w-full" type="text" name="notes" required
                            autofocus x-bind:value="asset?.notes" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="return_check" :value="__('Kondisi')" />
                        <div class="flex items-center space-x-4 mt-1">
                            <!-- Checkbox Baik -->
                            <label class="inline-flex items-center">
                                <input type="radio" name="return_check" value="baik"
                                    class="form-radio text-indigo-600"
                                    x-bind:checked="asset?.return_check === 'baik'" />
                                <span class="ml-2">{{ __('Baik') }}</span>
                            </label>

                            <!-- Checkbox Rusak -->
                            <label class="inline-flex items-center">
                                <input type="radio" name="return_check" value="rusak"
                                    class="form-radio text-indigo-600"
                                    x-bind:checked="asset?.return_check === 'rusak'" />
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
                    <input type="hidden" name="return_asset_id" x-bind:value="asset?.id">

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex assets-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                            Batalkan Peminjaman
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <x-modal name="cancel-return" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Batalkan Peminjaman</h5>

                <p class="mt-5">Apakah Anda yakin ingin <span class="text-red-700">membatalkan</span> peminjaman?
                </p>

                <form class="mt-5" id="cancelReturnForm" onsubmit="handleCancelReturn(event)">
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

        <x-modal name="confirm-return" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Konfirmasi Peminjaman</h5>

                <form class="mt-5" id="confirmReturnForm" onsubmit="handleConfirmReturn(event)">
                    @method('POST')
                    @csrf

                    <input id="in_number" type="hidden" name="loan_number">

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
                <p class="mt-5 italic text-sm text-gray-500">* Bukti peminjaman masih dapat dicetak lain waktu melalui
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
                            Cetak Bukti Peminjaman
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

        let dataTable;

        // Store checkbox states
        let checkboxStates = {};

        $(function() {
            dataTable = $('#exam').DataTable({
                    processing: true,
                    responsive: true,
                    data: [],
                    columns: [{
                            data: null,
                            render: function(data, type, row, meta) {
                                return meta.row + 1;
                            },
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'tag_number',
                            name: 'tag_number',
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
                            data: 'loan_check',
                            name: 'loan_check'
                        },
                        {
                            data: null,
                            render: function(data, type, row, meta) {
                                // Generate unique ID using row data
                                const uniqueId = `checkbox_${row.asset}_${meta.row}`;

                                // Get saved state or default to empty
                                const savedState = checkboxStates[uniqueId] || '';

                                return `<div class="mb-6">
                                            <div class="flex items-center space-x-4 mt-6">
                                                <label class="inline-flex items-center">
                                                    <input type="radio" 
                                                        name="return_check_${meta.row}" 
                                                        value="baik" 
                                                        class="form-radio text-indigo-600 return-check"
                                                        data-id="${uniqueId}"
                                                        ${savedState === 'baik' ? 'checked' : ''}
                                                     required />
                                                    <span class="ml-2">Baik</span>
                                                </label>

                                                <label class="inline-flex items-center">
                                                    <input type="radio" 
                                                        name="return_check_${meta.row}" 
                                                        value="rusak" 
                                                        class="form-radio text-indigo-600 return-check"
                                                        data-id="${uniqueId}"
                                                        ${savedState === 'rusak' ? 'checked' : ''}
                                                     required />
                                                    <span class="ml-2">Rusak</span>
                                                </label>
                                            </div>
                                        </div>`;
                            }
                        },
                        {
                            data: null,
                            render: function(data, type, row, meta) {
                                return `<textarea name="notes_${meta.row}" class="form-textarea mt-1 block w-full resize-none rounded-md border-gray-300 shadow-sm">${row.notes || ''}</textarea>`;
                            }
                        }
                    ],
                    columnDefs: [{
                            targets: [0],
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('text-center');
                            }
                        },
                        {
                            targets: [3, 5],
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('w-32 text-center');
                            }
                        },
                        {
                            targets: 6,
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('flex w-42 justify-center');
                            }
                        },
                        {
                            targets: 7,
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('w-56');
                            }
                        }
                    ],
                    drawCallback: function() {
                        // Restore checkbox states after redraw
                        $('.return-check').each(function() {
                            const id = $(this).data('id');
                            const value = $(this).val();
                            if (checkboxStates[id] === value) {
                                $(this).prop('checked', true);
                            }
                        });
                    }
                }).columns.adjust()
                .responsive.recalc();

            // Save checkbox state when changed
            $(document).on('change', '.return-check', function() {
                const id = $(this).data('id');
                const value = $(this).val();
                checkboxStates[id] = value;
            });
        });

        function handleSearchReturn(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Cari Data');

            $.ajax({
                url: "{{ route('loans.search') }}",
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
                            $('#return-action').css('display', 'block');
                        } else {
                            $('#return-action').css('display', 'flex');
                        }
                        $('.select2').select2();

                        const loanData = Array.isArray(response.loan) ? response.loan : [response.loan];

                        // Reset checkbox states when loading new data
                        checkboxStates = {};

                        const formattedData = loanData[0].assets.map(asset => ({
                            loan_asset_id: asset.id,
                            tag_number: asset.asset.tag_number,
                            asset: asset.asset.name,
                            brand: asset.asset.brand.name,
                            serial_number: asset.asset.serial_number,
                            quantity: asset.quantity,
                            duration: asset.duration,
                            loan_check: asset.loan_check,
                            return_check: asset.return_check,
                            notes: asset.notes,
                        }));

                        dataTable.clear().rows.add(formattedData).draw();

                        Toast.fire({
                            icon: 'success',
                            title: 'Data ditemukan'
                        });

                        form.reset();
                        $('#loan_number').val(response.loan.loan_number);
                        $('#customer_name').text("Nama Pelanggan : " + response.loan.customer_name);
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
                    setLoading(submitButton, false, 'Cari Data');
                }
            });
        }

        function handleEditAsset(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const assetId = formData.get('return_asset_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/returns/${assetId}`,
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
            const assetId = formData.get('return_asset_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Aset', 'delete');

            $.ajax({
                url: `/returns/${assetId}`,
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

        function handleCancelReturn(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Batalkan Peminjaman', 'delete');

            $.ajax({
                url: `/returns/cancel`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#return-action').css('display', 'none');
                    $('.select2').select2();


                    dataTable.ajax.reload();

                    $('#customer_name').val('');
                    $('#order_note_number').val('');
                    $('#contract_note_number').val('');
                    $('#delivery_note_number').val('');

                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'cancel-return'
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

        function handleConfirmReturn(event) {
            event.preventDefault();


            const form = event.target;
            const submitButton = $(form).find('button[type="submit"]');

            // Get all data from DataTable, not just visible page
            const allData = dataTable.data().toArray();

            // Create FormData object
            const formData = new FormData(form);

            // Add all checkbox states to formData
            allData.forEach((row, index) => {
                const uniqueId = `checkbox_${row.asset}_${index}`;
                const checkState = checkboxStates[uniqueId] || '';

                const notes = $(`textarea[name="notes_${index}"]`).val();

                if (checkState) {
                    formData.append(`assets[${index}][loan_asset_id]`, row.loan_asset_id);
                    formData.append(`assets[${index}][return_check]`, checkState);
                    formData.append(`assets[${index}][notes]`, notes);
                }
            });

            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

            setLoading(submitButton, true, 'Konfirmasi Pengembalian');

            $.ajax({
                url: `/returns/confirm`,
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
                            $('#return-action').css('display', 'block');
                        } else {
                            $('#return-action').css('display', 'flex');
                        }
                        $('.select2').select2();
                        dataTable.clear().draw();

                        // Clear form fields
                        $('#loan_number').val('');

                        // Close confirm-return modal
                        dispatchEvent(new CustomEvent('close-modal', {
                            detail: 'confirm-return'
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
                            window.location.href =
                                `/documents/returns/download/${response.loan_id}/true/${response.loan_number}`;
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr);
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Konfirmasi Pengembalian');
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
