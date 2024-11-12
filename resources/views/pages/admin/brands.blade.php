<x-app-layout>
    <x-slot name="nav">admin</x-slot>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Daftar Merek
        </h2>
    </x-slot>

    <main class="px-10 mt-10">
        <button class="flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm"
            x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-brand')">
            <img class="w-6" src="https://img.icons8.com/?size=100&id=oqWjYJSQSZAj&format=png&color=FFFFFF"
                alt="">
            Tambah Merek
        </button>

        <x-modal name="add-brand" :show="false">
            <div class="p-5">
                <h5 class="font-semibold text-md">Tambah Merek</h5>

                <form class="mt-5" id="addBrandForm" onsubmit="handleAddBrand(event)">
                    @csrf

                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Merek')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" required
                            autofocus />
                    </div>

                    <div class="w-full flex justify-end">
                        <button type="submit"
                            class="justify-end flex items-center gap-1 px-4 py-2 bg-green-700 rounded-md text-white font-medium text-sm">
                            Tambah Merek
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
                        <th>Nama Merek</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
            </table>
        </div>
    </main>

    <x-modal name="edit-brand" :show="false">
        <div class="p-5" x-data="{
            brand: null,
            setBrand(data) {
                this.brand = data;
            }
        }" @set-brand.window="setBrand($event.detail)">
            <h5 class="font-semibold text-md">Edit Merek</h5>

            <form class="mt-5" id="editBrandForm" onsubmit="handleEditBrand(event)">
                @method('PUT')
                @csrf

                <input type="hidden" name="brand_id" x-bind:value="brand?.id">
                <div class="mb-4">
                    <x-input-label for="edit_name" :value="__('Nama Merek')" />
                    <x-text-input id="edit_name" class="block mt-1 w-full" type="text" name="name" required
                        x-bind:value="brand?.name" autofocus />
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

    <x-modal name="delete-brand" :show="false">
        <div class="p-5" x-data="{
            brand: null,
            setBrand(data) {
                this.brand = data;
            }
        }" @set-brand.window="setBrand($event.detail)">
            <h5 class="font-semibold text-md">Hapus Merek</h5>

            <p class="mt-5">Apakah Anda yakin ingin menghapus merek <span class="font-bold text-red-600"
                    x-text="brand?.name"></span>?</p>

            <form class="mt-5" id="deleteBrandForm" onsubmit="handleDeleteBrand(event)">
                @method('DELETE')
                @csrf
                <input type="hidden" name="brand_id" x-bind:value="brand?.id">

                <div class="w-full flex justify-end">
                    <button type="submit"
                        class="justify-end flex items-center gap-1 px-4 py-2 bg-red-700 rounded-md text-white font-medium text-sm">
                        Hapus Merek
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
                    ajax: "{{ route('brands.data') }}",
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

        function handleAddBrand(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Tambah Merek', 'add');

            $.ajax({
                url: "{{ route('brands.store') }}",
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    form.reset();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'add-brand'
                    }));

                    if (response.status == 'error') {
                        Toast.fire({
                            icon: 'error',
                            title: response.message
                        });
                    } else {
                        Toast.fire({
                            icon: 'success',
                            title: 'Merek berhasil ditambahkan'
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
                        setLoading(submitButton, false, 'Tambah Merek', 'add');
                    }, 500);
                }
            });
        }

        function handleEditBrand(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const merekId = formData.get('brand_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Simpan Perubahan', 'edit');

            $.ajax({
                url: `/admin/brands/${merekId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'edit-brand'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Merek merek berhasil diubah'
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

        function handleDeleteBrand(event) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            const merekId = formData.get('brand_id');
            const submitButton = $(form).find('button[type="submit"]');

            setLoading(submitButton, true, 'Hapus Merek', 'delete');

            $.ajax({
                url: `/admin/brands/${merekId}`,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    dataTable.ajax.reload();
                    dispatchEvent(new CustomEvent('close-modal', {
                        detail: 'delete-brand'
                    }));

                    Toast.fire({
                        icon: 'success',
                        title: 'Merek merek berhasil dihapus'
                    });
                },
                error: function(xhr) {
                    alert('Terjadi kesalahan saat menghapus data');
                },
                complete: function() {
                    setTimeout(() => {
                        setLoading(submitButton, false, 'Hapus Merek', 'delete');
                    }, 500);
                }
            });
        }
    </script>

</x-app-layout>
