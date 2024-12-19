<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Aset
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <div class="flex gap-2">
            <button class="flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
                x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-asset')">
                <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                    alt="">
                Tambah Aset
            </button>

            <a href="{{ route('assets.other') }}"
                class="flex items-center gap-1 px-4 py-2 bg-[#31363F] rounded-md text-white font-medium text-sm">
                <img class="w-6" src="https://img.icons8.com/?size=100&id=9k0o3TIEUkNT&format=png&color=FFFFFF"
                    alt="">
                Aset Cabang Lain
            </a>
        </div>

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
                        <x-input-label for="tag_number" :value="__('Tag Number')" />
                        <x-text-input id="tag_number" class="block mt-1 w-full" type="text" name="tag_number"
                            required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Aset')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                            autofocus />
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
                        <x-input-label for="serial_number" :value="__('Nomor Seri')" />
                        <x-text-input id="serial_number" class="block mt-1 w-full" type="text" name="serial_number"
                            required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="color" :value="__('Warna')" />
                        <x-text-input id="color" class="block mt-1 w-full" type="text" name="color" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="size" :value="__('Ukuran')" />
                        <x-text-input id="size" class="block mt-1 w-full" type="text" name="size" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="condition" :value="__('Kondisi Barang')" />
                        <select name="condition"
                            class="w-full block mt-1 border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm">
                            <option value="baik">Baik</option>
                            <option value="rusak">Rusak</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="status" :value="__('Status Barang')" />
                        <select name="status"
                            class="w-full block mt-1 border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm">
                            <option value="terpakai">Terpakai</option>
                            <option value="tidak terpakai">Tidak Terpakai</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="calibration_number" :value="__('No. Sertifikasi Kalibrasi / Perizinan')" />
                        <x-text-input id="calibration_number" class="block mt-1 w-full" type="text"
                            name="calibration_number" required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="calibration_interval" :value="__('Interval Kalibrasi (Tahun)')" />
                        <x-text-input id="calibration_interval" class="block mt-1 w-full" type="number"
                            name="calibration_interval" required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="calibration_start_date" :value="__('Tanggal Kalibrasi')" />
                        <x-text-input id="calibration_start_date" class="block mt-1 w-full" type="date"
                            name="calibration_start_date" required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="calibration_institution" :value="__('Lembaga Kalibrasi')" />
                        <x-text-input id="calibration_institution" class="block mt-1 w-full" type="text"
                            name="calibration_institution" required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="calibration_type" :value="__('Jenis Kalibrasi')" />
                        <select name="calibration_type"
                            class="w-full block mt-1 border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm">
                            <option value="internal">Internal</option>
                            <option value="eksternal">Eksternal</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="range" :value="__('Range / Kapasitas')" />
                        <x-text-input id="range" class="block mt-1 w-full" type="text" name="range"
                            required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="correction_factor" :value="__('Faktor Koreksi')" />
                        <x-text-input id="correction_factor" class="block mt-1 w-full" type="text"
                            name="correction_factor" required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="significance" :value="__('Signifikan')" />
                        <select name="significance"
                            class="w-full block mt-1 border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm">
                            <option value="ya">Ya</option>
                            <option value="tidak">Tidak</option>
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
                        <input type="file" id="calibration" name="calibration[]" accept=".pdf" multiple
                            class="hidden" onchange="showMultipleFiles(event, 'calibrationFilename')" />
                        <div id="calibrationFilename" class="mt-2 text-sm text-gray-600"></div>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="procurement" :value="__('Unggah Dokumen Pengadaan')" />
                        <label for="procurement" id="procurementLabel"
                            class="flex justify-center items-center gap-2 mt-1 w-full border border-gray-300 rounded-md p-2 text-center cursor-pointer shadow-sm">
                            <img class="w-5"
                                src="https://img.icons8.com/?size=100&id=pEujrB5ongzP&format=png&color=000000"
                                alt="">
                            <span id="text-document">Pilih Dokumen</span>
                        </label>
                        <input type="file" id="procurement" name="procurement" accept=".pdf" class="hidden"
                            onchange="showFileName(event, 'procurementLabel')" />
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
                        <th>Aksi</th>
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
                    <x-input-label for="edit_tag_number" :value="__('Tag Number')" />
                    <x-text-input id="edit_tag_number" class="block mt-1 w-full" type="text" name="tag_number"
                        x-bind:value="asset?.tag_number" required autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_name" :value="__('Nama Aset')" />
                    <x-text-input id="edit_name" class="block mt-1 w-full" type="text" name="name" required
                        x-bind:value="asset?.name" autofocus />
                </div>

                <div class="mb-4 w-full">
                    <x-input-label for="name" :value="__('Merek')" />
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
                    <x-input-label for="edit_serial_number" :value="__('Nomor Seri')" />
                    <x-text-input id="edit_serial_number" class="block mt-1 w-full" type="text"
                        name="serial_number" required x-bind:value="asset?.serial_number" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_color" :value="__('Warna')" />
                    <x-text-input id="edit_color" class="block mt-1 w-full" type="text" name="color" required
                        x-bind:value="asset?.color" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_size" :value="__('Ukuran')" />
                    <x-text-input id="edit_size" class="block mt-1 w-full" type="text" name="size" required
                        x-bind:value="asset?.size" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_condition" :value="__('Kondisi Barang')" />
                    <select name="condition"
                        class="w-full block mt-1 border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm"
                        x-bind:value="asset?.condition">
                        <option value="baik">Baik</option>
                        <option value="rusak">Rusak</option>
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_status" :value="__('Status Barang')" />
                    <select name="status"
                        class="w-full block mt-1 border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm"
                        x-bind:value="asset?.status">
                        <option value="terpakai">Terpakai</option>
                        <option value="tidak terpakai">Tidak Terpakai</option>
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_calibration_number" :value="__('No. Sertifikasi Kalibrasi / Perizinan')" />
                    <x-text-input id="edit_calibration_number" class="block mt-1 w-full" type="text"
                        name="calibration_number" required x-bind:value="asset?.calibration_number" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_calibration_interval" :value="__('Interval Kalibrasi (Tahun)')" />
                    <x-text-input id="edit_calibration_interval" class="block mt-1 w-full" type="number"
                        name="calibration_interval" required x-bind:value="asset?.calibration_interval" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_calibration_start_date" :value="__('Tanggal Kalibrasi')" />
                    <x-text-input id="edit_calibration_start_date" class="block mt-1 w-full" type="date"
                        name="calibration_start_date" required x-bind:value="asset?.calibration_start_date" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_calibration_institution" :value="__('Lembaga Kalibrasi')" />
                    <x-text-input id="edit_calibration_institution" class="block mt-1 w-full" type="text"
                        name="calibration_institution" required x-bind:value="asset?.calibration_institution" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_calibration_type" :value="__('Jenis Kalibrasi')" />
                    <select name="calibration_type"
                        class="w-full block mt-1 border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm"
                        x-bind:value="asset?.calibration_type">
                        <option value="internal">Internal</option>
                        <option value="eksternal">Eksternal</option>
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_range" :value="__('Range / Kapasitas')" />
                    <x-text-input id="edit_range" class="block mt-1 w-full" type="text" name="range" required
                        x-bind:value="asset?.range" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_correction_factor" :value="__('Faktor Koreksi')" />
                    <x-text-input id="edit_correction_factor" class="block mt-1 w-full" type="text"
                        name="correction_factor" required x-bind:value="asset?.correction_factor" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_significance" :value="__('Signifikan')" />
                    <select name="significance"
                        class="w-full block mt-1 border-gray-300 focus:border-blue-600 focus:ring-blue-600 rounded-md shadow-sm"
                        x-bind:value="asset?.significance">
                        <option value="ya">Ya</option>
                        <option value="tidak">Tidak</option>
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_calibration" :value="__('Unggah Dokumen Kalibrasi')" />
                    <label for="edit_calibration"
                        class="flex justify-center items-center gap-2 mt-1 w-full border border-gray-300 rounded-md p-2 text-center cursor-pointer shadow-sm">
                        <img class="w-5"
                            src="https://img.icons8.com/?size=100&id=pEujrB5ongzP&format=png&color=000000"
                            alt="">
                        Pilih Dokumen
                    </label>
                    <input type="file" id="edit_calibration" name="calibration[]" accept=".pdf" multiple
                        class="hidden" onchange="showMultipleFiles(event, 'edit_calibrationFilename')" />
                    <div id="edit_calibrationFilename" class="mt-2 text-sm text-gray-600"></div>
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_procurement" :value="__('Unggah Dokumen Pengadaan')" />
                    <label for="edit_procurement" id="edit_procurementLabel"
                        class="flex justify-center items-center gap-2 mt-1 w-full border border-gray-300 rounded-md p-2 text-center cursor-pointer shadow-sm">
                        <img class="w-5"
                            src="https://img.icons8.com/?size=100&id=pEujrB5ongzP&format=png&color=000000"
                            alt="">
                        <span id="text-document">Pilih Dokumen</span>
                    </label>
                    <input type="file" id="edit_procurement" name="procurement" accept=".pdf" class="hidden"
                        onchange="showFileName(event, 'edit_procurementLabel')" />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_photo" :value="__('Unggah Foto')" />
                    <label for="edit_photo" id="edit_photoLabel"
                        class="flex justify-center items-center gap-2 mt-1 w-full border border-gray-300 rounded-md p-2 text-center cursor-pointer shadow-sm">
                        <img class="w-6"
                            src="https://img.icons8.com/?size=100&id=ubpIBvM5kGB0&format=png&color=000000"
                            alt="">
                        <span id="text-photo">Pilih Foto</span>
                    </label>
                    <input type="file" id="edit_photo" name="photo" accept="image/*" class="hidden"
                        onchange="showFileName(event, 'edit_photoLabel')" />
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
                            targets: -1,
                            className: 'dt-body-right'
                        }
                    ]
                }).columns.adjust()
                .responsive.recalc();

            dataTable.on('draw', function() {
                $('#exam thead th').css('border-top-right-radius', '0');

                var visibleColumns = dataTable.columns(':visible').indexes().toArray();
                var lastVisibleColumnIndex = visibleColumns[visibleColumns.length - 1];

                if (lastVisibleColumnIndex !== undefined) {
                    $('#exam thead th').eq(lastVisibleColumnIndex).css('border-top-right-radius', '8px');
                    $('#exam tbody tr td').eq(lastVisibleColumnIndex).css('border-bottom-right-radius',
                        '8px');
                }
            });

            // Mengatur border ketika kolom diperluas atau disembunyikan
            dataTable.on('responsive-resize responsive-display', function() {
                updateBorders();
            });

            // Fungsi untuk memperbarui border
            function updateBorders() {
                // Dapatkan indeks kolom terakhir yang terlihat
                var visibleColumns = dataTable.columns(':visible').indexes().toArray();
                var lastVisibleColumnIndex = visibleColumns[visibleColumns.length - 1];

                console.log('Last visible column index:', lastVisibleColumnIndex);

                if (lastVisibleColumnIndex != undefined) {
                    $('#exam tbody tr').each(function() {
                        $(this).find('td').eq(lastVisibleColumnIndex).css('border-bottom-right-radius',
                            '0px');
                    });
                }

                console.log('Borders updated');
            }

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

            for (let [key, value] of formData.entries()) {
                console.log(`${key}: ${value}`);
            }

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

            console.log(elementId);

            if (input.files.length > 0) {
                span.textContent = input.files[0].name;
            } else {
                span.textContent = elementId === 'calibrationLabel' ? 'Pilih Dokumen' : 'Pilih Foto';
            }
        }

        function showMultipleFiles(event, elementId) {
            const input = event.target;
            const filesContainer = document.getElementById(elementId);
            filesContainer.innerHTML = '';

            if (input.files.length > 0) {
                const fileList = document.createElement('ul');
                fileList.className = 'list-disc pl-5';

                Array.from(input.files).forEach(file => {
                    const li = document.createElement('li');
                    li.textContent = file.name;
                    fileList.appendChild(li);
                });

                filesContainer.appendChild(fileList);
            } else {
                filesContainer.textContent = 'Tidak ada file yang dipilih';
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
