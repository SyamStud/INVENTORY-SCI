<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Pengisian Bahan Bakar
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <button
            class="flex w-full justify-center md:w-max md:justify-normal items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-filling')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Pengisian Bahan Bakar
        </button>

        <x-modal name="add-filling" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Pengisian Bahan Bakar</h5>

                <form class="mt-5" id="addFillingForm" onsubmit="handleAddFilling(event)">
                    @csrf

                    <div class="mb-4 w-full">
                        <x-input-label for="name" :value="__('Nomor Polisi')" />
                        <select class="w-full select2" name="vehicle_id">
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->nopol }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <x-input-label for="km_fillings" :value="__('Kilometer Pengisian')" />
                        <x-text-input id="km_fillings" class="block mt-1 w-full" type="number" name="km_fillings"
                            required autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="quantity" :value="__('Nominal')" />
                        <x-text-input id="quantity" class="block mt-1 w-full" type="number" name="quantity" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="driver" :value="__('Nama Yang Mengisi')" />
                        <x-text-input id="driver" class="block mt-1 w-full" type="text" name="driver" required
                            autofocus />
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Tambah Pengisian Bahan Bakar
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
                        <th>Plat Nomor</th>
                        <th>Merk / Tipe</th>
                        <th>KM Pengisian</th>
                        <th>Nominal</th>
                        <th>Nama Yang Mengisi</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-filling" :show="false">
        <div class="p-5" x-data="{
            filling: null,
            setFilling(data) {
                this.filling = data;
            }
        }" @set-filling.window="setFilling($event.detail)">
            <h5 class="font-semibold text-md">Edit Pengisian Bahan Bakar</h5>

            <form class="mt-5" id="editFillingForm" onsubmit="handleEditFilling(event)">
                @method('PUT')
                @csrf

                <input type="hidden" name="filling_id" x-bind:value="filling?.id">

                <div class="mb-4 w-full">
                    <x-input-label for="edit_name" :value="__('Nomor Polisi')" />
                    <select class="w-full select2" name="vehicle_id" x-bind:value="filling?.vehicle_id">
                        @foreach ($vehicles as $vehicle)
                            <option value="{{ $vehicle->id }}">{{ $vehicle->nopol }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_km_fillings" :value="__('Kilometer Pengisian')" />
                    <x-text-input id="edit_km_fillings" class="block mt-1 w-full" type="number" name="km_fillings"
                        required x-bind:value="filling?.km_fillings" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_quantity" :value="__('Nominal')" />
                    <x-text-input id="edit_quantity" class="block mt-1 w-full" type="number" name="quantity" required
                        x-bind:value="filling?.quantity" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_driver" :value="__('Nama Yang Mengisi')" />
                    <x-text-input id="edit_driver" class="block mt-1 w-full" type="text" name="driver" required
                        x-bind:value="filling?.driver" autofocus />
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

    <x-modal name="delete-filling" :show="false">
        <div class="p-5" x-data="{
            filling: null,
            setFilling(data) {
                this.filling = data;
            }
        }" @set-filling.window="setFilling($event.detail)">
            <h5 class="font-semibold text-md">Hapus Pengisian Bahan Bakar</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus pengisian Bahan Bakar <span
                    class="font-bold text-red-600" x-text="filling?.name"></span>?</p>

            <form class="mt-5" id="deleteFillingForm" onsubmit="handleDeleteFilling(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="filling_id" x-bind:value="filling?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                        Hapus Pengisian Bahan Bakar
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
                    ajax: "{{ route('fuels.data') }}",
                    dom: '<"top"Blf>rt<"bottom"ip>',
                    buttons: [{
                        extend: 'excelHtml5',
                        text: '<span class="flex gap-2 items-center"><img src="https://img.icons8.com/?size=100&id=11594&format=png&color=FFFFFF" alt="Excel" style="height:20px; margin-right:5px;"> Export ke Excel</span>',
                        title: 'Data Pengisian Bahan Bakar',
                        exportOptions: {
                            columns: ':not(:last-child):not(:nth-child(n+20):nth-child(-n+22))'
                        }
                    }],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'nopol',
                            name: 'nopol'
                        },
                        {
                            data: 'brand',
                            name: 'brand'
                        },
                        {
                            data: 'km_fillings',
                            name: 'km_fillings'
                        },
                        {
                            data: 'quantity',
                            name: 'quantity'
                        },
                        {
                            data: 'driver',
                            name: 'driver'
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
                            targets: 6,
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

        function handleAddFilling(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Tambah Pengisian Bahan Bakar', 'add');

            $.ajax({
                url: "{{ route('fuels.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'add-filling'
                    }));

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Pengisian berhasil ditambahkan'
                        });
                    }
                },
                error: function(xhr) {
                    Toast.fire({
                        icon: 'error',
                        title: 'Terjadi kesalahan saat menambahkan data'
                    });
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Tambah Pengisian Bahan Bakar', 'add');
                    }, 500);
                }
            });
        }

        function handleEditFilling(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const fillingId = formData.get('filling_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/admin/fuels/${fillingId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'edit-filling'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Pengisian berhasil diubah'
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat mengubah data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Simpan Perubahan', 'edit');
                    }, 500);
                }
            });
        }

        function handleDeleteFilling(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const fillingId = formData.get('filling_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Pengisian Bahan Bakar', 'delete');

            $.ajax({
                url: `/admin/fuels/${fillingId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-filling'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Pengisian berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menghapus data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Pengisian Bahan Bakar', 'delete');
                    }, 500);
                }
            });
        }
    </script>

    <script>
        $(document).ready(function() {
            document.querySelectorAll('.select2').forEach(function(select) {
                $(select).select2();
            });
        });
    </script>

    <style>
        .dt-button.buttons-excel.buttons-html5 {
            margin-right: 15px;
        }
    </style>

</x-app-layout>
