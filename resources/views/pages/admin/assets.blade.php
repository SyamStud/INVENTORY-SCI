<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Aset
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <button class="flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-asset')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Aset
        </button>

        <x-modal name="add-asset" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Aset</h5>

                <form class="mt-5" id="addAssetForm" onsubmit="handleAddAsset(event)" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="inventory_number" :value="__('Nomor Inventaris')" />
                        <x-text-input id="inventory_number" class="block mt-1 w-full" type="text"
                            name="inventory_number" required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Aset')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="serial_number" :value="__('Nomor Seri')" />
                        <x-text-input id="serial_number" class="block mt-1 w-full" type="text" name="serial_number"
                            required autofocus />
                    </div>

                    <div class="mb-4 w-full">
                        <x-input-label for="name" :value="__('Merek')" />
                        <select class="w-full select2" name="brand_id">
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="calibration" :value="__('Unggah Dokumen Kalibrasi')" />
                        <label for="calibration" id="calibrationLabel"
                            class="flex justify-center items-center gap-2 mt-1 w-full border border-gray-300 rounded-md p-2 text-center cursor-pointer shadow-sm">
                            <img class="w-5"
                                src="https://img.icons8.com/?size=100&id=pEujrB5ongzP&format=png&color=000000"
                                alt="">
                            <span id="text-document">Pilih Dokumen</span>
                        </label>
                        <input type="file" id="calibration" name="calibration" accept=".pdf" class="hidden"
                            onchange="showFileName(event, 'calibrationLabel')" />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="photo" :value="__('Unggah Foto')" />
                        <label for="photo" id="photoLabel"
                            class="flex justify-center items-center gap-2 mt-1 w-full border border-gray-300 rounded-md p-2 text-center cursor-pointer shadow-sm">
                            <img class="w-6"
                                src="https://img.icons8.com/?size=100&id=ubpIBvM5kGB0&format=png&color=000000"
                                alt="">
                            <span id="text-photo">Pilih Foto</span>
                        </label>
                        <input type="file" id="photo" name="photo" accept="image/*" class="hidden"
                            onchange="showFileName(event, 'photoLabel')" />
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Tambah Aset
                        </button>
                    </div>
                </form>
            </div>
        </x-modal>

        <div class="mt-8 pb-10">
            <table id="exam" class="table table-striped nowrap" style="width:100%">
                <thead>
                    <tr class="text-white">
                        <th class="w-10">No</th>
                        <th>Nomor Inventaris</th>
                        <th>Nama Aset</th>
                        <th>Nomor Seri</th>
                        <th>Merek</th>
                        <th class="w-32">Kalibrasi</th>
                        <th class="w-28">Foto</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-asset" :show="false">
        <div class="p-5" x-data="{
            asset: null,
            setAsset(data) {
                this.asset = data;
            }
        }" @set-asset.window="setAsset($event.detail)">
            <h5 class="font-semibold text-md">Edit Aset</h5>

            <form class="mt-5" id="editAssetForm" onsubmit="handleEditAsset(event)">
                @method('PUT')
                @csrf

                <input type="hidden" name="asset_id" x-bind:value="asset?.id">
                <div class="mb-4">
                    <x-input-label for="edit_inventory_number" :value="__('Nomor Inventaris')" />
                    <x-text-input id="edit_inventory_number" class="block mt-1 w-full" type="text"
                        name="inventory_number" required x-bind:value="asset?.inventory_number" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_name" :value="__('Nama Aset')" />
                    <x-text-input id="edit_name" class="block mt-1 w-full" type="text" name="name" required
                        x-bind:value="asset?.name" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_serial_number" :value="__('Nomor Seri')" />
                    <x-text-input id="edit_serial_number" class="block mt-1 w-full" type="text"
                        name="serial_number" required x-bind:value="asset?.serial_number" autofocus />
                </div>

                <div class="mb-4 w-full">
                    <x-input-label for="name" :value="__('Nama Aset')" />
                    <select class="w-full select2" name="brand_id" x-data x-init="$nextTick(() => {
                        $('.select2').select2(); // Inisialisasi Select2
                        if (asset) {
                            $('.select2').val(asset.brand_id).trigger('change');
                        }
                    })"
                        @set-asset.window="$nextTick(() => {
                                $('.select2').val($event.detail.brand_id).trigger('change');
                            })">
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}"
                                x-bind:selected="asset?.brand_id == {{ $brand->id }}">
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="calibration" :value="__('Unggah Dokumen Kalibrasi')" />
                    <label for="calibration"
                        class="flex justify-center items-center gap-2 mt-1 w-full border border-gray-300 rounded-md p-2 text-center cursor-pointer shadow-sm">
                        <img class="w-5"
                            src="https://img.icons8.com/?size=100&id=pEujrB5ongzP&format=png&color=000000"
                            alt="">
                        Pilih Dokumen
                    </label>
                    <input type="file" id="calibration" name="calibration" accept=".pdf" class="hidden"
                        onchange="showFileName(event, 'calibrationFilename')" />
                    <p id="calibrationFilename" class="mt-2 text-sm text-gray-600"></p>
                </div>

                <div class="mb-4">
                    <x-input-label for="photo" :value="__('Unggah Foto')" />
                    <label for="photo"
                        class="flex justify-center items-center gap-2 mt-1 w-full border border-gray-300 rounded-md p-2 text-center cursor-pointer shadow-sm">
                        <img class="w-6"
                            src="https://img.icons8.com/?size=100&id=ubpIBvM5kGB0&format=png&color=000000"
                            alt="">
                        Pilih Foto
                    </label>
                    <input type="file" id="photo" name="photo" accept="image/*" class="hidden"
                        onchange="showFileName(event, 'photoFilename')" />
                    <p id="photoFilename" class="mt-2 text-sm text-gray-600"></p>
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

    <x-modal name="delete-asset" :show="false">
        <div class="p-5" x-data="{
            asset: null,
            setAsset(data) {
                this.asset = data;
            }
        }" @set-asset.window="setAsset($event.detail)">
            <h5 class="font-semibold text-md">Hapus Aset</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus aset <span class="font-bold text-red-600"
                    x-text="asset?.name"></span>?</p>

            <form class="mt-5" id="deleteAssetForm" onsubmit="handleDeleteAsset(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="asset_id" x-bind:value="asset?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                        Hapus Aset
                    </button>
                </div>
            </form>
        </div>
    </x-modal>

    <script>
        let dataTable;

        $(function() {
            dataTable = $('#exam').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: "{{ route('assets.data') }}",
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
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'serial_number',
                            name: 'serial_number',
                            orderable: false
                        },
                        {
                            data: 'brand',
                            name: 'brand'
                        },
                        {
                            data: 'calibration',
                            name: 'calibration',
                            orderable: false
                        },
                        {
                            data: 'photo',
                            name: 'photo',
                            orderable: false
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
                            targets: 7,
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('flex justify-center gap-2 w-max');
                            }
                        }
                    ]
                }).columns.adjust()
                .responsive.recalc();
        });

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
                    button.css('background-color', '#EF4444');
                }
                button.html(text);
            }
        }

        function handleAddAsset(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Tambah Aset', 'add');

            $.ajax({
                url: "{{ route('assets.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'add-asset'
                    }));

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
                    }

                    const textDocument = document.getElementById('calibrationLabel').querySelector('span');
                    textDocument.textContent = 'Pilih Dokumen';

                    const textPhoto = document.getElementById('photoLabel').querySelector('span');
                    textPhoto.textContent = 'Pilih Foto';
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat menambahkan data'
                    });

                    console.error(xhr);
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Tambah Aset', 'add');
                    }, 500);
                }
            });
        }

        function handleEditAsset(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const asetId = formData.get('asset_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/admin/assets/${asetId}`,
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
                        title: 'Data berhasil diubah'
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
            const asetId = formData.get('asset_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Aset', 'delete');

            $.ajax({
                url: `/admin/assets/${asetId}`,
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
                        title: 'Aset berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menghapus data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Aset', 'delete');
                    }, 500);
                }
            });
        }

        function showFileName(event, elementId) {
            const input = event.target;
            const label = document.getElementById(elementId);
            const span = label.querySelector('span');

            if (input.files.length > 0) {
                span.textContent = input.files[0].name;
            } else {
                span.textContent = elementId === 'calibrationLabel' ? 'Pilih Dokumen' : 'Pilih Foto';
            }
        }
    </script>

    <script>
        $(document).ready(function() {
            document.querySelectorAll('.select2').forEach(function(select) {
                $(select).select2();
            });
        });
    </script>

</x-app-layout>
