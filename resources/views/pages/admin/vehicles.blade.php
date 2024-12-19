<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Kendaraan
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <button
            class="flex w-full justify-center md:w-max md:justify-normal items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-vehicle')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Kendaraan
        </button>

        <x-modal name="add-vehicle" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Kendaraan</h5>

                <form class="mt-5" id="addVehicleForm" onsubmit="handleAddVehicle(event)">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="nopol" :value="__('Plat Nomor')" />
                        <x-text-input id="nopol" class="block mt-1 w-full" type="text" name="nopol" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="brand" :value="__('Merek / Tipe')" />
                        <x-text-input id="brand" class="block mt-1 w-full" type="text" name="brand" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="stnk" :value="__('Tanggal STNK')" />
                        <x-text-input id="stnk" class="block mt-1 w-full" type="date" name="stnk" required
                            autofocus />
                    </div>

                    <div class="mb-4">
                        <x-input-label for="kir" :value="__('Tanggal KIR')" />
                        <x-text-input id="kir" class="block mt-1 w-full" type="date" name="kir" required
                            autofocus />
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Tambah Kendaraan
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
                        <th>Tanggal STNK</th>
                        <th>Tanggal KIR</th>
                        <th class="w-40">Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-vehicle" :show="false">
        <div class="p-5" x-data="{
            vehicle: null,
            setVehicle(data) {
                this.vehicle = data;
            }
        }" @set-vehicle.window="setVehicle($event.detail)">
            <h5 class="font-semibold text-md">Edit Kendaraan</h5>

            <form class="mt-5" id="editVehicleForm" onsubmit="handleEditVehicle(event)">
                @method('PUT')
                @csrf

                <input type="hidden" name="vehicle_id" x-bind:value="vehicle?.id">

                <div class="mb-4">
                    <x-input-label for="edit_nopol" :value="__('Plat Nomor')" />
                    <x-text-input id="edit_nopol" class="block mt-1 w-full" type="text" name="nopol" required
                        x-bind:value="vehicle?.nopol" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_brand" :value="__('Merek / Tipe')" />
                    <x-text-input id="edit_brand" class="block mt-1 w-full" type="text" name="brand" required
                        x-bind:value="vehicle?.brand" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_stnk" :value="__('Tanggal STNK')" />
                    <x-text-input id="edit_stnk" class="block mt-1 w-full" type="date" name="stnk" required
                        x-bind:value="vehicle?.stnk" autofocus />
                </div>

                <div class="mb-4">
                    <x-input-label for="edit_kir" :value="__('Tanggal KIR')" />
                    <x-text-input id="edit_kir" class="block mt-1 w-full" type="date" name="kir" required
                        x-bind:value="vehicle?.kir" autofocus />
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

    <x-modal name="delete-vehicle" :show="false">
        <div class="p-5" x-data="{
            vehicle: null,
            setVehicle(data) {
                this.vehicle = data;
            }
        }" @set-vehicle.window="setVehicle($event.detail)">
            <h5 class="font-semibold text-md">Hapus Kendaraan</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus kendaraan <span class="font-bold text-red-600"
                    x-text="vehicle?.name"></span>?</p>

            <form class="mt-5" id="deleteVehicleForm" onsubmit="handleDeleteVehicle(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="vehicle_id" x-bind:value="vehicle?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                        Hapus Kendaraan
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
                    ajax: "{{ route('vehicles.data') }}",
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
                            data: 'stnk',
                            name: 'stnk'
                        },
                        {
                            data: 'kir',
                            name: 'kir'
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

        function handleAddVehicle(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Tambah Kendaraan', 'add');

            $.ajax({
                url: "{{ route('vehicles.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'add-vehicle'
                    }));

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Kendaraan berhasil ditambahkan'
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
                        setLoading(submitButton, false, 'Tambah Kendaraan', 'add');
                    }, 500);
                }
            });
        }

        function handleEditVehicle(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const kendaraanId = formData.get('vehicle_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/admin/vehicles/${kendaraanId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'edit-vehicle'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Kendaraan kendaraan berhasil diubah'
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

        function handleDeleteVehicle(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const kendaraanId = formData.get('vehicle_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Kendaraan', 'delete');

            $.ajax({
                url: `/admin/vehicles/${kendaraanId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-vehicle'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Kendaraan kendaraan berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menghapus data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Kendaraan', 'delete');
                    }, 500);
                }
            });
        }
    </script>

</x-app-layout>
