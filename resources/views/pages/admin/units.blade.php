<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Satuan Unit
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <button class="flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-unit')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Unit
        </button>

        <x-modal name="add-unit" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Satuan Unit</h5>

                <form class="mt-5" id="addUnitForm" onsubmit="handleAddUnit(event)">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Satuan')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                            autofocus />
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Tambah Satuan
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
                        <th>Nama Satuan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-unit" :show="false">
        <div class="p-5" x-data="{
            unit: null,
            setUnit(data) {
                this.unit = data;
            }
        }" @set-unit.window="setUnit($event.detail)">
            <h5 class="font-semibold text-md">Edit Satuan Unit</h5>

            <form class="mt-5" id="editUnitForm" onsubmit="handleEditUnit(event)">
                @method('PUT')
                @csrf

                <input type="hidden" name="unit_id" x-bind:value="unit?.id">
                <div class="mb-4">
                    <x-input-label for="edit_name" :value="__('Nama Satuan')" />
                    <x-text-input id="edit_name" class="block mt-1 w-full" type="text" name="name" required
                        x-bind:value="unit?.name" autofocus />
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

    <x-modal name="delete-unit" :show="false">
        <div class="p-5" x-data="{
            unit: null,
            setUnit(data) {
                this.unit = data;
            }
        }" @set-unit.window="setUnit($event.detail)">
            <h5 class="font-semibold text-md">Hapus Satuan Unit</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus satuan unit <span class="font-bold text-red-600"
                    x-text="unit?.name"></span>?</p>

            <form class="mt-5" id="deleteUnitForm" onsubmit="handleDeleteUnit(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="unit_id" x-bind:value="unit?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                        Hapus Satuan
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
                    ajax: "{{ route('units.data') }}",
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name'
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
                            targets: 1,
                            createdCell: function(td, cellData, rowData, row, col) {
                                $(td).addClass('w-full');
                            }
                        },
                        {
                            targets: 2,
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

        function handleAddUnit(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Tambah Satuan', 'add');

            $.ajax({
                url: "{{ route('units.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'add-unit'
                    }));

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Satuan berhasil ditambahkan'
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
                        setLoading(submitButton, false, 'Tambah Satuan', 'add');
                    }, 500);
                }
            });
        }

        function handleEditUnit(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const unitId = formData.get('unit_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/admin/units/${unitId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'edit-unit'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Satuan unit berhasil diubah'
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

        function handleDeleteUnit(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const unitId = formData.get('unit_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Satuan', 'delete');

            $.ajax({
                url: `/admin/units/${unitId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-unit'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Satuan unit berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menghapus data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Satuan', 'delete');
                    }, 500);
                }
            });
        }
    </script>

</x-app-layout>
